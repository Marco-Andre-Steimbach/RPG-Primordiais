<?php

use App\Core\Http\Router;
use App\Application\Controllers\Users\UserController;

$router = new Router();

$router->add('POST', '/users/register', [UserController::class, 'register']);
$router->add('POST', '/users/login', [UserController::class, 'login']);

return $router;
