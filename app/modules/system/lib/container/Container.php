<?php

namespace App\Modules\System\Container;

use App\Modules\System\Configuration\Configuration;
use App\Modules\System\DataBase\MySqlDb;
use App\Modules\System\Logger\Logger;
use App\Modules\System\Request\Request;
use App\Modules\System\Response\HtmlResponse;

class Container
{
	protected array $services;
	protected array $cachedServices;
	static protected Container $instance;

	private function __construct()
	{
		$this->services = [
			Logger::class => fn() => new Logger(),
			Configuration::class => fn() => new Configuration(),
			MySqlDb::class => fn() => new MySqlDb(self::get(Configuration::class)),
			Request::class => fn() => new Request(),
			HtmlResponse::class => fn() => new HtmlResponse()
		];
	}

	static public function getInstance() : Container
	{
		if(!isset(self::$instance))
		{
			self::$instance = new Container();
		}
		return self::$instance;
	}

	public function get(string $id)
	{
		if(isset($this->cachedServices[$id]))
		{
			return $this->cachedServices[$id];
		}
		$this->cachedServices[$id] = $this->services[$id]();
		return $this->cachedServices[$id];
	}
}