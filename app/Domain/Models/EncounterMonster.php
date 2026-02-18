<?php

namespace App\Domain\Models;

class EncounterMonster
{
    public function __construct(
        public int $id,
        public int $encounter_id,
        public int $monster_id,
        public int $monster_level,
        public int $current_hp,
        public ?string $created_at = null
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'encounter_id' => $this->encounter_id,
            'monster_id' => $this->monster_id,
            'monster_level' => $this->monster_level,
            'current_hp' => $this->current_hp,
            'created_at' => $this->created_at,
        ];
    }
}
