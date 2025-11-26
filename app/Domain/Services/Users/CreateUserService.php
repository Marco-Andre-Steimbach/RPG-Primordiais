<?php

namespace App\Domain\Services\Users;

use App\Application\DTOs\Users\UserRegisterDTO;
use App\Infrastructure\Repositories\UserRepository;
use App\Infrastructure\Repositories\UserRoleRepository;
use App\Core\Exceptions\ValidationException;
use App\Domain\Models\User;

class CreateUserService
{
    private UserRepository $users;
    private UserRoleRepository $userRoles;

    public function __construct()
    {
        $this->users = new UserRepository();
        $this->userRoles = new UserRoleRepository();
    }

    public function execute(UserRegisterDTO $dto): User
    {
        if ($this->users->existsByEmail($dto->email)) {
            throw new ValidationException("Email já está em uso.");
        }

        if (!empty($dto->nickname) && $this->users->existsByNickname($dto->nickname)) {
            throw new ValidationException("Nickname já está em uso.");
        }

        $hashedPassword = password_hash($dto->password, PASSWORD_BCRYPT);

        $userId = $this->users->create([
            'first_name' => $dto->first_name,
            'last_name'  => $dto->last_name,
            'nickname'   => $dto->nickname,
            'email'      => $dto->email,
            'password'   => $hashedPassword,
        ]);

        if (!$userId) {
            throw new ValidationException("Falha ao criar usuário.");
        }

        $rolePlayerId = $this->userRoles->getRoleIdByName('player');

        if (!$rolePlayerId) {
            throw new ValidationException("Role 'player' não encontrada.");
        }

        $this->userRoles->attachRole($userId, $rolePlayerId);

        $user = $this->users->findById($userId);

        if (!$user) {
            throw new ValidationException("Erro ao carregar o usuário recém-criado.");
        }

        return $user;
    }
}
