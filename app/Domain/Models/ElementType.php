<?php

namespace App\Domain\Models;

class ElementType
{
    public function __construct(
        public int $id,
        public string $name,
        public string $description,
        public ?string $created_at = null
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'created_at' => $this->created_at,
        ];
    }
}
