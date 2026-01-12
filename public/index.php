<?php

$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';

header("Access-Control-Allow-Origin: $origin");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Exceptions\ExceptionHandler;
use App\Core\Config\Env;
use App\Core\Http\Request;

set_exception_handler([ExceptionHandler::class, 'handle']);

Env::load(__DIR__ . '/../.env');

$router = require __DIR__ . '/../app/routes/api.php';

$request = new Request();
$router->dispatch($request);
