<?php

namespace App\Domain\Models;

class Race
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description = null,
        public ?string $created_at = null,
        public ?string $updated_at = null,
        public array $attributes = []
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'attributes' => $this->attributes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
