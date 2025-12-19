<?php

namespace App\Domain\Models;

class Monster
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description = null,
        public int $base_hp = 1,
        public int $base_ac = 10,
        public int $base_speed = 6,
        public int $actions_per_turn = 3,
        public int $base_str = 1,
        public int $base_dex = 1,
        public int $base_con = 1,
        public int $base_wis = 1,
        public int $base_int = 1,
        public array $element_types = [],
        public ?string $created_at = null,
        public ?string $updated_at = null
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,

            'base_hp' => $this->base_hp,
            'base_ac' => $this->base_ac,
            'base_speed' => $this->base_speed,

            'actions_per_turn' => $this->actions_per_turn,

            'base_str' => $this->base_str,
            'base_dex' => $this->base_dex,
            'base_con' => $this->base_con,
            'base_wis' => $this->base_wis,
            'base_int' => $this->base_int,

            'element_types' => $this->element_types,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
