<?php

namespace App\Domain\Models;

class MonsterAttack
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description,
        public string $dice_formula,
        public int $base_damage,
        public int $bonus_accuracy,

        public int $attack_range = 1,
        public ?int $weapon_damage_type_id = null,

        public array $element_types = [],

        public ?string $created_at = null
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,

            'dice_formula' => $this->dice_formula,
            'base_damage' => $this->base_damage,
            'bonus_accuracy' => $this->bonus_accuracy,

            'attack_range' => $this->attack_range,
            'weapon_damage_type_id' => $this->weapon_damage_type_id,

            'element_types' => $this->element_types,

            'created_at' => $this->created_at,
        ];
    }
}
