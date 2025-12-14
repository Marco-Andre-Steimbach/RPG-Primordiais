<?php

namespace App\Domain\Services\Users;

use App\Application\DTOs\Users\UserUpdatePasswordDTO;
use App\Infrastructure\Repositories\UserRepository;
use App\Core\Exceptions\UnauthorizedException;
use App\Core\Exceptions\ValidationException;

class UpdateUserPasswordService
{
    private UserRepository $users;

    public function __construct()
    {
        $this->users = new UserRepository();
    }

    public function execute(int $userId, UserUpdatePasswordDTO $dto): void
    {
        $user = $this->users->findById($userId);

        if (!$user || !password_verify($dto->current_password, $user->password)) {
            throw new UnauthorizedException('Senha atual inválida.');
        }

        if (strlen($dto->new_password) < 6) {
            throw new ValidationException('Nova senha deve ter no mínimo 6 caracteres.');
        }

        $hashed = password_hash($dto->new_password, PASSWORD_BCRYPT);

        $this->users->updatePassword($userId, $hashed);
    }
}
