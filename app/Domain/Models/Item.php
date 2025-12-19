<?php

namespace App\Domain\Models;

class Item
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description = null,
        public int $value = 0,
        public array $element_types = [],
        public array $item_abilities = [],
        public ?string $created_at = null,
        public ?string $updated_at = null
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'value' => $this->value,
            'element_types' => $this->element_types,
            'item_abilities' => $this->item_abilities,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
