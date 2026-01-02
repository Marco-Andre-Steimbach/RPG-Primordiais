<?php

namespace App\Domain\Models;

class Ability
{
    public function __construct(
        public int $id,
        public string $title,
        public string $description,
        public ?string $arcane_title = null,
        public ?string $arcane_description = null,
        public int $mana_cost = 0,
        public ?int $arcane_mana_cost = null,
        public ?string $dice_formula = null,
        public int $base_damage = 0,
        public int $bonus_speed = 0,
        public array $element_types = [],
        public ?int $required_race_id = null,
        public ?int $required_order_id = null,
        public ?string $created_at = null,
        public ?string $updated_at = null
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,

            'title' => $this->title,
            'description' => $this->description,

            'arcane_title' => $this->arcane_title,
            'arcane_description' => $this->arcane_description,

            'mana_cost' => $this->mana_cost,
            'arcane_mana_cost' => $this->arcane_mana_cost,

            'dice_formula' => $this->dice_formula,
            'base_damage' => $this->base_damage,
            'bonus_speed' => $this->bonus_speed,

            'element_types' => $this->element_types,

            'required_race_id' => $this->required_race_id,
            'required_order_id' => $this->required_order_id,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
