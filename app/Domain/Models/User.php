<?php

namespace App\Domain\Models;

class User
{
    public function __construct(
        public int $id,
        public string $first_name,
        public string $last_name,
        public string $nickname,
        public string $email,
        public string $password,       
        public ?string $created_at = null,
        public ?string $updated_at = null
    ) {}

    public function fullName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function toArray(): array
    {
        return [
            "id"         => $this->id,
            "first_name" => $this->first_name,
            "last_name"  => $this->last_name,
            "nickname"   => $this->nickname,
            "email"      => $this->email,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at
        ];
    }
}
