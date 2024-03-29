<?php

namespace App\Modules\System\DataBase\Queries;

class SelectQuery extends Query
{
	private array $select = [];
	private array $joins = [];
	private string $groupBy = '';
	private array $limit = [];
	private array $order = [];

	public function setSelect(array $selectStatement): self
	{
		$this->select = $selectStatement;
		return $this;
	}

	public function setJoin(array $join): self
	{
		$this->joins[] = $join;
		return $this;
	}

	public function setGroupBy(string $groupBy): self
	{
		$this->groupBy = $groupBy;
		return $this;
	}

	public function setLimit(array $limit): self
	{
		$this->limit = $limit;
		return $this;
	}

	public function setOrderBy(array $order): self
	{
		$this->order = $order;
		return $this;
	}

	protected function generateSql(): self
	{
		$this->sql = "SELECT {SELECT} FROM {TABLE} {JOIN} {WHERE} {GROUP BY} {ORDER BY} {LIMIT};";
		try {
			$this->replaceSelect();
			$this->replaceTableName();
			$this->replaceJoin();
			$this->replaceWhere();
			$this->replaceGroupBy();
			$this->replaceLimit();
			$this->replaceOrderBy();
		}catch (\Exception $exception)
		{
			echo $exception->getMessage();
			die();
		}
		return $this;
	}

	private function replaceSelect(): void
	{
		if(!$this->select)
		{
			throw new \Exception('Add select statements to query');
		}
		$selectPlaceholder = '{SELECT}';
		for ($i = 0; $i < count($this->select); $i++)
		{
			$fieldDelimiter = '';
			if($i >= 0 && $i < count($this->select) - 1)
			{
				$fieldDelimiter = ', ';
			}
			$this->sql = str_replace($selectPlaceholder, "{$this->select[$i]}{$fieldDelimiter}{$selectPlaceholder}", $this->sql);
		}
		$this->deletePlaceholder($selectPlaceholder);
	}

	private function replaceJoin(): void
	{
		$joinPlaceholder = '{JOIN}';
		if($this->joins)
		{
			for ($i = 0; $i < count($this->joins); $i++)
			{
				$joinType = isset($this->joins[$i]['type']) ? strtoupper($this->joins[$i]['type']) : '';
				$referenceTable = $this->joins[$i]['ref_table'];
				$joinCondition = $this->joins[$i]['on'];
				$joinCondition = str_replace('this', $this->tableName, $joinCondition);
				$joinCondition = str_replace('ref', $referenceTable, $joinCondition);
				$this->sql = str_replace($joinPlaceholder, "{$joinType} JOIN `{$referenceTable}` ON {$joinCondition} {$joinPlaceholder}", $this->sql);
			}
		}
		$this->deletePlaceholder($joinPlaceholder);
	}

	private function replaceGroupBy(): void
	{
		$groupByPlaceholder = '{GROUP BY}';
		if($this->groupBy)
		{
			$this->groupBy = str_replace('this', $this->tableName, $this->groupBy);
			$this->sql = str_replace($groupByPlaceholder, "GROUP BY `{$this->groupBy}`", $this->sql);
		}
		$this->deletePlaceholder($groupByPlaceholder);
	}

	private function replaceLimit(): void
	{
		$limitPlaceholder = '{LIMIT}';
		if($this->limit)
		{
			if(count($this->limit) == 1)
			{
				$this->sql = str_replace($limitPlaceholder, "LIMIT {$this->limit[0]}", $this->sql);
			}else
			{
				$this->sql = str_replace($limitPlaceholder, "LIMIT {$this->limit[0]}, {$this->limit[1]}", $this->sql);
			}
		}
		$this->deletePlaceholder($limitPlaceholder);
	}

	private function replaceOrderBy(): void
	{
		$orderByPlaceholder = '{ORDER BY}';
		if(isset($this->order['fields']))
		{
			for($i = 0; $i < count($this->order['fields']); $i++)
			{
				if($i == 0)
				{
					$this->sql = str_replace($orderByPlaceholder, "ORDER BY `{$this->order['fields'][$i]}` {ORDER BY}", $this->sql);
				}else
				{
					$this->sql = str_replace($orderByPlaceholder, ", `{$this->order['fields'][$i]}` {ORDER BY}", $this->sql);
				}
			}
			$order = 'ASC';
			if($this->order['order'])
			{
				$order = $this->order['order'];
			}
			$this->sql = str_replace($orderByPlaceholder, " $order ", $this->sql);
		}
		$this->deletePlaceholder($orderByPlaceholder);
	}
}