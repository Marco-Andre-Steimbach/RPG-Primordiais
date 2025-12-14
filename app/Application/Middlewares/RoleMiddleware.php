<?php

namespace App\Application\Middlewares;

use App\Core\Http\Request;
use App\Core\Exceptions\ForbiddenException;
use App\Infrastructure\Repositories\UserRoleRepository;

class RoleMiddleware
{
    public function handle(Request $request, array $allowedRoles): void
    {
        $user = $request->user();

        if (!$user) {
            throw new ForbiddenException('Usuário não autenticado.');
        }

        $repo = new UserRoleRepository();
        $userRoles = $repo->getRoleNamesByUser($user->id);

        foreach ($allowedRoles as $role) {
            if (in_array($role, $userRoles, true)) {
                return;
            }
        }

        throw new ForbiddenException('Você não tem permissão para acessar este recurso.');
    }
}
