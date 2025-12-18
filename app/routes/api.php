<?php

use App\Core\Http\Router;
use App\Application\Controllers\Users\UserController;
use App\Application\Controllers\Races\RaceController;
use App\Application\Controllers\Perks\PerkController;
use App\Application\Controllers\Orders\OrderController;

$router = new Router();

$router->add('POST', '/users/register', [UserController::class, 'register']);
$router->add('POST', '/users/login', [UserController::class, 'login']);
$router->middleware('auth')->add('GET', '/users/me', [UserController::class, 'me']);
$router->middleware('auth')->add('PUT', '/users/update', [UserController::class, 'update']);
$router->middleware('auth')->add('PUT', '/users/update-password', [UserController::class, 'updatePassword']);
$router->middleware('auth')->middleware('role:admin')->add('POST', '/users/give-role', [UserController::class, 'giveRole']);

$router->middleware('auth')->middleware('role:admin,dungeon_master')->add('POST', '/races', [RaceController::class, 'store']);
$router->middleware('auth')->middleware('role:admin,dungeon_master')->add('PUT', '/races/update', [RaceController::class, 'update']);
$router->middleware('auth')->add('GET', '/races', [RaceController::class, 'index']);
$router->middleware('auth')->add('GET', '/races/:id', [RaceController::class, 'show']);

$router->middleware('auth')->middleware('role:admin,dungeon_master')->add('POST', '/orders', [OrderController::class, 'store']);
$router->middleware('auth')->add('GET', '/orders', [OrderController::class, 'index']);
$router->middleware('auth')->add('GET', '/orders/:id', [OrderController::class, 'show']);

$router->middleware('auth')->middleware('role:admin,dungeon_master')->add('POST', '/perks', [PerkController::class, 'store']);
$router->middleware('auth')->add('GET', '/races/:id/perks', [PerkController::class, 'byRace']);
$router->middleware('auth')->add('GET', '/orders/:id/perks', [PerkController::class, 'byOrder']);
$router->middleware('auth')->add('GET', '/perks/:id', [PerkController::class, 'show']);


return $router;
