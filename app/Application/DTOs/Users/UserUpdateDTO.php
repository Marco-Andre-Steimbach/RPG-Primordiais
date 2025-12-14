<?php

namespace App\Application\DTOs\Users;

use App\Core\Exceptions\ValidationException;

class UserUpdateDTO
{
    public string $first_name;
    public string $last_name;
    public ?string $nickname;
    public string $email;

    public function __construct(array $data)
    {
        $this->first_name = trim($data['first_name'] ?? '');
        $this->last_name  = trim($data['last_name'] ?? '');
        $this->nickname   = isset($data['nickname']) ? trim($data['nickname']) : null;
        $this->email      = strtolower(trim($data['email'] ?? ''));

        $this->validate();
    }

    private function validate(): void
    {
        if (strlen($this->first_name) < 2) {
            throw new ValidationException("Nome muito curto.");
        }

        if (strlen($this->last_name) < 2) {
            throw new ValidationException("Sobrenome muito curto.");
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException("Email inválido.");
        }

        if ($this->nickname !== null && strlen($this->nickname) < 3) {
            throw new ValidationException("Nickname muito curto.");
        }
    }

    public function toArray(): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'nickname'   => $this->nickname,
            'email'      => $this->email,
        ];
    }
}
