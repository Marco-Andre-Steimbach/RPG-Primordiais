<?php

namespace App\Domain\Services\Users;

use App\Application\DTOs\Users\UserUpdateDTO;
use App\Core\Exceptions\ConflictException;
use App\Infrastructure\Repositories\UserRepository;

class UpdateUserService
{
    private UserRepository $users;

    public function __construct()
    {
        $this->users = new UserRepository();
    }

    public function execute(int $userId, UserUpdateDTO $dto)
    {
        if ($this->users->emailInUse($dto->email, $userId)) {
            throw new ConflictException("Email já está sendo usado.");
        }

        if ($this->users->nicknameInUse($dto->nickname, $userId)) {
            throw new ConflictException("Nickname já está sendo usado.");
        }

        $this->users->updateUser($userId, $dto->toArray());

        return $this->users->findById($userId);
    }
}
