<?php

use App\Core\Http\Router;
use App\Application\Controllers\Users\UserController;
use App\Application\Controllers\Races\RaceController;

$router = new Router();

$router->add('POST', '/users/register', [UserController::class, 'register']);
$router->add('POST', '/users/login', [UserController::class, 'login']);
$router->middleware('auth')->add('GET', '/users/me', [UserController::class, 'me']);
$router->middleware('auth')->add('PUT', '/users/update', [UserController::class, 'update']);
$router->middleware('auth')->add('PUT', '/users/update-password', [UserController::class, 'updatePassword']);
$router->middleware('auth')->middleware('role:admin')->add('POST', '/users/give-role', [UserController::class, 'giveRole']);

$router->middleware('auth')->middleware('role:admin,dungeon_master')->add('POST', '/races', [RaceController::class, 'store']);

return $router;
