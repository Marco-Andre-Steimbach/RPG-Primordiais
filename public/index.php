<?php

header("Access-Control-Allow-Origin: http://localhost:5173");
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

$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    Env::load($envPath);
}

$router = require __DIR__ . '/../app/routes/api.php';

$request = new Request();
$router->dispatch($request);
