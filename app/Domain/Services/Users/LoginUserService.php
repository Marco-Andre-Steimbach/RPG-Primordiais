<?php

namespace App\Domain\Services\Users;

use App\Application\DTOs\Users\UserLoginDTO;
use App\Infrastructure\Repositories\UserRepository;
use App\Core\Exceptions\ValidationException;
use App\Domain\Models\User;

class LoginUserService
{
    private UserRepository $users;

    public function __construct()
    {
        $this->users = new UserRepository();
    }

    public function execute(UserLoginDTO $dto): User
    {
        $user = $this->users->findByLogin($dto->login);

        if (!$user) {
            throw new ValidationException("Credenciais inválidas.");
        }

        if (!password_verify($dto->password, $user->password)) {
            throw new ValidationException("Credenciais inválidas.");
        }

        return $user;
    }
}
