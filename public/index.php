<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Exceptions\ExceptionHandler;

set_exception_handler([ExceptionHandler::class, 'handle']);

use App\Core\Config\Env;
use App\Core\Http\Request;

Env::load(__DIR__ . '/../.env');

$router = require __DIR__ . '/../app/routes/api.php';

$request = new Request();
$router->dispatch($request);
