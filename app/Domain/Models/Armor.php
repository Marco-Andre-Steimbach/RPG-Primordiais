<?php

namespace App\Domain\Models;

class Armor
{
    public function __construct(
        public int $id,
        public int $item_id,
        public int $armor_slot_id,
        public int $armor_class_bonus,
        public int $min_strength_required,
        public int $speed_penalty,
        public array $element_types = [],
        public array $armor_abilities = [],
        public ?string $created_at = null
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'item_id' => $this->item_id,
            'armor_slot_id' => $this->armor_slot_id,

            'armor_class_bonus' => $this->armor_class_bonus,
            'min_strength_required' => $this->min_strength_required,
            'speed_penalty' => $this->speed_penalty,

            'element_types' => $this->element_types,
            'armor_abilities' => $this->armor_abilities,

            'created_at' => $this->created_at,
        ];
    }
}
