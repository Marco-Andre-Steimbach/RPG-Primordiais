<?php

namespace App\Domain\Models;

class ArmorAbility
{
    public function __construct(
        public int $id,
        public string $title,
        public string $description,
        public ?string $dice_formula = null,
        public int $base_damage = 0,
        public int $armor_class_bonus = 0,
        public int $bonus_speed = 0,
        public ?string $created_at = null,
        public ?string $updated_at = null
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'dice_formula' => $this->dice_formula,
            'base_damage' => $this->base_damage,
            'armor_class_bonus' => $this->armor_class_bonus,
            'bonus_speed' => $this->bonus_speed,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
