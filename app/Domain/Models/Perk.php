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
        public ?int $race_id = null,
        public ?int $order_id = null,
        public int $required_level = 1,
        public array $element_types = [],
        public array $flags = [],
        public array $attributes = [],
        public array $ability = [],
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

            'race_id' => $this->race_id,
            'order_id' => $this->order_id,
            'required_level' => $this->required_level,

            'element_types' => $this->element_types,
            'flags' => $this->flags,
            'attributes' => $this->attributes,
            'ability' => $this->ability,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
