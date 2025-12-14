<?php

namespace App\Domain\Services\Users;

use App\Infrastructure\Repositories\UserRepository;
use App\Infrastructure\Repositories\UserRoleRepository;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\ConflictException;
use App\Core\Exceptions\ValidationException;

class GiveUserRoleService
{
    private UserRepository $users;
    private UserRoleRepository $roles;

    public function __construct()
    {
        $this->users = new UserRepository();
        $this->roles = new UserRoleRepository();
    }

    public function execute(int $userId, string $roleName): void
    {
        $user = $this->users->findById($userId);

        if (!$user) {
            throw new NotFoundException('Usuário não encontrado.');
        }

        $roleId = $this->roles->getRoleIdByName($roleName);

        if (!$roleId) {
            throw new NotFoundException('Role não encontrada.');
        }

        $currentRoles = $this->roles->getRolesByUser($userId);

        if (in_array($roleId, $currentRoles, true)) {
            throw new ConflictException('Usuário já possui essa role.');
        }

        if (!$this->roles->attachRole($userId, $roleId)) {
            throw new ValidationException('Erro ao atribuir a role.');
        }
    }
}
