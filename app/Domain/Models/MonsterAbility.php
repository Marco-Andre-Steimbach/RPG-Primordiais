<?php

namespace App\Domain\Models;

class MonsterAbility
{
    public function __construct(
        public int $id,
        public string $title,
        public string $description,
        public ?string $dice_formula = null,
        public int $base_damage = 0,
        public int $bonus_damage = 0,
        public int $bonus_speed = 0,
        public array $element_types = [],
        public ?string $created_at = null
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,

            'dice_formula' => $this->dice_formula,
            'base_damage' => $this->base_damage,
            'bonus_damage' => $this->bonus_damage,
            'bonus_speed' => $this->bonus_speed,

            'element_types' => $this->element_types,

            'created_at' => $this->created_at,
        ];
    }
}
