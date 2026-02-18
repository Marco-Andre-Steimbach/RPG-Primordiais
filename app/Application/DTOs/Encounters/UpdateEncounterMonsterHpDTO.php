<?php

namespace App\Application\DTOs\Encounters;

use App\Core\Exceptions\ValidationException;

class UpdateEncounterMonsterHpDTO
{
    public int $encounter_monster_id;
    public int $current_hp;

    public function __construct(array $data)
    {
        $this->encounter_monster_id = (int) ($data['encounter_monster_id'] ?? 0);
        $this->current_hp = (int) ($data['current_hp'] ?? 0);

        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if ($this->encounter_monster_id <= 0) {
            $errors['encounter_monster_id'][] = 'encounter_monster_id inválido.';
        }

        if ($this->current_hp < 0) {
            $errors['current_hp'][] = 'current_hp não pode ser negativo.';
        }

        if ($errors) {
            throw new ValidationException('Dados inválidos.', $errors);
        }
    }
}
