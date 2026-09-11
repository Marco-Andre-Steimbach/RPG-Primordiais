<?php

namespace App\Domain\Models;

class Weapon
{
    public function __construct(
        public int $id,
        public int $item_id,
        public int $weapon_damage_type_id,
        public string $dice_formula,
        public int $base_damage,
        public int $bonus_accuracy,
        public int $bonus_speed,
        public int $range,
        public string $required_modifier,
        public int $required_modifier_value,
        public ?int $ammo_item_id = null,
        public int $ammo_per_use = 1,
        public array $element_types = [],
        public ?string $created_at = null
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'item_id' => $this->item_id,
            'weapon_damage_type_id' => $this->weapon_damage_type_id,

            'dice_formula' => $this->dice_formula,
            'base_damage' => $this->base_damage,
            'bonus_accuracy' => $this->bonus_accuracy,
            'bonus_speed' => $this->bonus_speed,
            'range' => $this->range,

            'required_modifier' => $this->required_modifier,
            'required_modifier_value' => $this->required_modifier_value,

            'ammo_item_id' => $this->ammo_item_id,
            'ammo_per_use' => $this->ammo_per_use,

            'element_types' => $this->element_types,

            'created_at' => $this->created_at,
        ];
    }
}
