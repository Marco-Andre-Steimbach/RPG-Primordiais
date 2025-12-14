<?php

namespace App\Domain\Services\Users;

use App\Infrastructure\Repositories\UserRepository;
use App\Infrastructure\Repositories\UserRoleRepository;
use App\Core\Exceptions\NotFoundException;

class GetAuthenticatedUserService
{
    public function execute(int $userId): array
    {
        $userRepo = new UserRepository();
        $roleRepo = new UserRoleRepository();

        $user = $userRepo->findById($userId);

        if (!$user) {
            throw new NotFoundException('Usuário não encontrado.');
        }

        $roles = $roleRepo->getRoleNamesByUser($userId);

        return [
            'user' => $user->toArray(),
            'roles' => $roles,
        ];
    }
}
