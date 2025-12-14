<?php

namespace App\Application\Middlewares;

use App\Core\Http\Response;
use App\Core\Http\Request;
use App\Core\Security\TokenService;
use App\Infrastructure\Repositories\UserRepository;

class AuthMiddleware
{
    public function handle(Request $request)
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? ($headers['authorization'] ?? '');

        if (!str_starts_with($authHeader, 'Bearer ')) {
            Response::json(['error' => 'Token não fornecido'], 401);
            exit;
        }

        $token = substr($authHeader, 7);
        $decoded = TokenService::validate($token);

        if (!$decoded || empty($decoded['user_id'])) {
            Response::json(['error' => 'Token inválido'], 401);
            exit;
        }

        $repo = new UserRepository();
        $user = $repo->findById($decoded['user_id']);

        if (!$user) {
            Response::json(['error' => 'Usuário não encontrado'], 404);
            exit;
        }

        if (TokenService::needsRefresh($decoded)) {
            $newToken = 'Bearer ' . TokenService::generate([
                'user_id' => $user->id,
                'email'   => $user->email,
            ]);

            header('X-Refresh-Token: ' . $newToken);
            header('Access-Control-Expose-Headers: X-Refresh-Token');
        }

        $request->setUser($user);
    }
}
