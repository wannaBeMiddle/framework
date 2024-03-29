<?php

namespace App\Modules\System\Options;

use App\Modules\System\DataBase\Queries\SelectQuery;
use App\Modules\System\DataBase\Queries\UpdateQuery;

class Options
{
	static protected array $cache;

	static public function getOption(string $optionName): array
	{
		if(isset(static::$cache[$optionName]))
		{
			return static::$cache[$optionName];
		}
		$option = (new SelectQuery())
			->setTableName('options')
			->setSelect(['*'])
			->setWhere([
				'condition' => 'code = :code',
			])
			->setParams([
				'code' => $optionName
			])
			->execution()
			->getResult();
		if($option)
		{
			$optionId = $option['id'];
			$optionValue = (new SelectQuery())
				->setTableName('option_values')
				->setSelect(['value'])
				->setWhere([
					'condition' => '`option` = ' . $optionId
				])
				->execution()
				->getResult();
			if($optionValue)
			{
				$optionResult = array_merge($option, $optionValue);
				static::$cache[$optionName] = $optionResult;
				return $optionResult;
			}
			$optionValue = (new SelectQuery())
				->setTableName('option_enums')
				->setSelect(['enum_value', 'selected', 'code'])
				->setWhere([
					'condition' => '`option` = ' . $optionId
				])
				->execution()
				->getResult();
			if($optionValue)
			{
				$option['values'] = $optionValue;
				static::$cache[$optionName] = $option;
				return $option;
			}
		}
		return $option;
	}

	static public function getOptionsByModule(string $module): array
	{
		if(isset(static::$cache[$module]))
		{
			return static::$cache[$module];
		}
		$options = (new SelectQuery())
			->setTableName('options')
			->setSelect(['options.*', 'option_sections.code as "section_code"', 'option_sections.name as "section_name"'])
			->setWhere([
				'condition' => 'module = :module',
			])
			->setJoin([
				'type' => 'inner',
				'ref_table' => 'option_sections',
				'on' => 'this.section = ref.id'
			])
			->setParams([
				'module' => $module
			])
			->execution();
		if($options->isSuccess())
		{
			$options = $options->getResult();
			$enumOptions = [];
			$textOptions = [];
			foreach ($options as $option)
			{
				if($option['type'] == 'enum')
				{
					$enumOptions[] = $option;
					continue;
				}
				$textOptions[] = $option;
			}
			$enumOptions = static::getEnumOptionsByOptionIds(array_column($enumOptions, 'id'));
			$textOptions = static::getOptionsByOptionIds(array_column($textOptions, 'id'));
			foreach ($enumOptions as $enumOption)
			{
				foreach ($options as &$option)
				{
					if($option['id'] == $enumOption['option'])
					{
						$option['values'][] = $enumOption;
					}
				}
			}
			unset($option);
			foreach ($textOptions as $textOption)
			{
				foreach ($options as &$option)
				{
					if($option['id'] == $textOption['option'])
					{
						$option['value'] = $textOption['value'];
					}
				}
			}
			unset($option);
			static::$cache[$module] = $options;
			return $options;
		}
		return [];
	}

	static public function getEnumOptionsByOptionIds(array $ids): array
	{
		$options = (new SelectQuery())
			->setSelect(['*'])
			->setTableName('option_enums');
		$fields = [];
		foreach ($ids as $id)
		{
			$options->setWhere([
				'condition' => '`option` = :option' . $id,
				'logic' => 'OR'
			]);
			$fields['option' . $id] = $id;
		}
		$result = $options->setParams($fields)
			->execution();
		if($result->isSuccess())
		{
			return $result->getResult();
		}
		return [];
	}

	static public function getOptionsByOptionIds(array $ids): array
	{
		$options = (new SelectQuery())
			->setSelect(['*'])
			->setTableName('option_values');
		$fields = [];
		foreach ($ids as $id)
		{
			$options->setWhere([
				'condition' => '`option` = :option' . $id,
				'logic' => 'OR'
			]);
			$fields['option' . $id] = $id;
		}
		$result = $options->setParams($fields)
			->execution();
		if($result->isSuccess())
		{
			return $result->getResult();
		}
		return [];
	}

	/**
	 * @param int $optionId
	 * @param string|int|array $value
	 * @return bool
	 */
	static public function setOptionById(int $optionId, $value): bool
	{
		if(is_array($value))
		{
			return static::setEnumOptionById($optionId, $value);
		}

		$update = (new UpdateQuery())
			->setTableName('option_values')
			->setFields(['value'])
			->setParams([
				'value' => $value,
				'option' => $optionId
			])
			->setWhere([
				'condition' => '`option` = :option'
			])
			->execution();

		return $update->isSuccess();
	}

	/**
	 * @param int $optionId
	 * @param array $values
	 * @return bool
	 */
	static protected function setEnumOptionById(int $optionId, array $values): bool
	{
		$clear = (new UpdateQuery())
			->setTableName('option_enums')
			->setFields(['selected'])
			->setWhere([
				'condition' => '`option` = :option'
			])
			->setParams([
				'selected' => 0,
				'option' => $optionId
			])
			->execution();

		$update = (new UpdateQuery())
			->setTableName('option_enums')
			->setFields(['selected']);

		$fields = [];
		foreach ($values as $value)
		{
			$update->setWhere([
				'condition' => '`id` = :id' . $value,
				'logic' => 'OR'
			]);
			$fields['id'.$value] = $value;
		}
		$fields['selected'] = 1;
		$result = $update->setParams($fields)
		->execution();
		return $result->isSuccess();
	}
}