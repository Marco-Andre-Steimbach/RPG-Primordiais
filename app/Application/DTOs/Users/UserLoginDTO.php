<?php

namespace App\Application\DTOs\Users;

class UserLoginDTO
{
    public string $login;
    public string $password;

    public function __construct(array $data)
    {
        $this->login = trim($data['login'] ?? '');
        $this->password = $data['password'] ?? '';
    }
}
