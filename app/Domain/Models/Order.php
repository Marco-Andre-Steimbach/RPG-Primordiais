<?php

namespace App\Domain\Models;

class Order
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description = null,
        public ?string $created_at = null,
        public ?string $updated_at = null,
        public ?int $required_race_id = null,
        public array $attributes = []
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'required_race_id' => $this->required_race_id,
            'attributes' => $this->attributes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
