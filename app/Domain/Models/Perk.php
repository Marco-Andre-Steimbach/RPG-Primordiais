<?php

namespace App\Domain\Models;

class Perk
{
    public function __construct(
        public int $id,
        public string $name,
        public string $description,
        public string $type,
        public int $mana_cost,
        public ?string $created_at = null,
        public ?string $updated_at = null
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'mana_cost' => $this->mana_cost,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
