<?php

namespace App\Domain\Models;

class Ability
{
    public function __construct(
        public int $id,

        public ?int $character_id,

        public string $title,
        public string $description,

        public ?string $arcane_title = null,
        public ?string $arcane_description = null,

        public int $mana_cost = 0,
        public ?int $arcane_mana_cost = null,

        public array $normal_element_types = [],
        public array $arcane_element_types = [],

        public ?int $required_race_id = null,
        public ?int $required_order_id = null,

        public string $status = 'draft',
        public ?int $converted_ability_id = null,

        public ?string $created_at = null,
        public ?string $updated_at = null
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,

            'character_id' => $this->character_id,

            'title' => $this->title,
            'description' => $this->description,

            'arcane_title' => $this->arcane_title,
            'arcane_description' => $this->arcane_description,

            'mana_cost' => $this->mana_cost,
            'arcane_mana_cost' => $this->arcane_mana_cost,

            'normal_element_types' =>
                $this->normal_element_types,

            'arcane_element_types' =>
                $this->arcane_element_types,

            'required_race_id' =>
                $this->required_race_id,

            'required_order_id' =>
                $this->required_order_id,

            'status' => $this->status,

            'converted_ability_id' =>
                $this->converted_ability_id,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
