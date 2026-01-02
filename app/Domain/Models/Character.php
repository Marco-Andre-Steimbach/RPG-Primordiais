<?php

namespace App\Domain\Models;

class Character
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description = null,
        public ?int $race_id = null,
        public ?int $order_id = null,
        public string $mana_modifier,
        public ?int $created_by = null,
        public ?string $created_at = null,
        public ?string $updated_at = null
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,

            'race_id' => $this->race_id,
            'order_id' => $this->order_id,

            'mana_modifier' => $this->mana_modifier,

            'created_by' => $this->created_by,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
