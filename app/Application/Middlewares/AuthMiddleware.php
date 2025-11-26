<?php

namespace App\Application\Middlewares;

use App\Core\Http\Response;
use App\Core\Http\Request;
use App\Domain\Services\AuthService;

class AuthMiddleware
{
    public function handle(Request $request)
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (!str_starts_with($authHeader, 'Bearer ')) {
            Response::json(['error' => 'Token não fornecido'], 401);
        }

        $token = substr($authHeader, 7);

        $service = new AuthService();
        $user = $service->validateToken($token);

        if (!$user) {
            Response::json(['error' => 'Token inválido'], 401);
        }

        $request->setUser($user);
    }
}
