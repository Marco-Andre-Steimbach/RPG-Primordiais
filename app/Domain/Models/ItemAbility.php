<?php

namespace App\Domain\Models;

class ItemAbility
{
    public function __construct(
        public int $id,
        public string $title,
        public string $description,
        public ?string $dice_formula = null,
        public int $base_damage = 0,
        public int $bonus_damage = 0,
        public int $bonus_accuracy = 0,
        public int $bonus_speed = 0,
        public int $range = 1, 
        public bool $is_consumable = false,
        public ?int $max_uses = null,
        public ?int $override_element_type_id = null,
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
            'bonus_damage' => $this->bonus_damage,
            'bonus_accuracy' => $this->bonus_accuracy,
            'bonus_speed' => $this->bonus_speed,
            'range' => $this->range,
            'is_consumable' => $this->is_consumable,
            'max_uses' => $this->max_uses,
            'override_element_type_id' => $this->override_element_type_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
