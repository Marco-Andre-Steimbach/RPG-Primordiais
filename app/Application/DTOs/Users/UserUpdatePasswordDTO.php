<?php

namespace App\Application\DTOs\Users;

class UserUpdatePasswordDTO
{
    public string $current_password;
    public string $new_password;

    public function __construct(array $data)
    {
        $this->current_password = $data['current_password'];
        $this->new_password = $data['new_password'];
    }
}
