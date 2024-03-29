<?php

use App\Modules\System\Autoloader\Entity;

return [
	new Entity('App\Modules\System\\', 'app/modules/system/lib/'),
	new Entity('App\Modules\System\Validator\\', 'app/modules/system/lib/validator'),
	new Entity('App\Modules\System\Validator\Rules\\', 'app/modules/system/lib/validator/rules'),
	new Entity('App\Controllers\\', 'app/controllers/'),
	new Entity('App\Modules\System\\', 'app/modules/system/lib'),
	new Entity('App\Modules\System\Controller\\', 'app/modules/system/lib/controller'),
	new Entity('App\Modules\System\Configuration\\', 'app/modules/system/lib/configuration'),
	new Entity('App\Modules\System\Container\\', 'app/modules/system/lib/container'),
	new Entity('App\Modules\System\DataBase\\', 'app/modules/system/lib/database'),
	new Entity('App\Modules\System\DataBase\Queries\\', 'app/modules/system/lib/database/queries'),
	new Entity('App\Modules\System\Router\\', 'app/modules/system/lib/router'),
	new Entity('App\Modules\System\Session\\', 'app/modules/system/lib/session'),
	new Entity('App\Modules\System\Response\\', 'app/modules/system/lib/response'),
	new Entity('App\Modules\System\Exceptions\Interfaces\\', 'app/modules/system/lib/exceptions/interfaces'),
	new Entity('App\Modules\System\Exceptions\\', 'app/modules/system/lib/exceptions'),
	new Entity('App\Modules\System\Options\\', 'app/modules/system/lib/options'),
	new Entity('App\Modules\System\Request\\', 'app/modules/system/lib/request'),
	new Entity('App\Modules\System\Logger\\', 'app/modules/system/lib/logger'),
	new Entity('App\Modules\System\User\\', 'app/modules/system/lib/user'),
	new Entity('App\Modules\System\User\UserConfirmation\Interfaces\\', 'app/modules/system/lib/user/userconfirmation/interfaces'),
	new Entity('App\Modules\System\User\UserConfirmation\\', 'app/modules/system/lib/user/userconfirmation'),
	new Entity('App\Modules\System\Tools\\', 'app/modules/system/lib/tools'),
	new Entity('App\Modules\System\File\\', 'app/modules/system/lib/file'),
];