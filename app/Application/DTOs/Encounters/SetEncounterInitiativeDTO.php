<?php

namespace App\Application\DTOs\Encounters;

use App\Core\Exceptions\ValidationException;

class SetEncounterInitiativeDTO
{
    public int $encounter_id;
    public ?int $encounter_monster_id;
    public ?int $encounter_player_id;
    public int $initiative_value;

    public function __construct(array $data)
    {
        $this->encounter_id = (int) ($data['encounter_id'] ?? 0);

        $this->encounter_monster_id = isset($data['encounter_monster_id'])
            ? (int) $data['encounter_monster_id']
            : null;

        $this->encounter_player_id = isset($data['encounter_player_id'])
            ? (int) $data['encounter_player_id']
            : null;

        $this->initiative_value = (int) ($data['initiative_value'] ?? 0);

        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if ($this->encounter_id <= 0) {
            $errors['encounter_id'][] = 'encounter_id inválido.';
        }

        if ($this->initiative_value <= 0) {
            $errors['initiative_value'][] = 'initiative_value deve ser maior que 0.';
        }

        $hasMonster = $this->encounter_monster_id !== null && $this->encounter_monster_id > 0;
        $hasPlayer  = $this->encounter_player_id !== null && $this->encounter_player_id > 0;

        if (!$hasMonster && !$hasPlayer) {
            $errors['target'][] = 'É necessário informar encounter_monster_id ou encounter_player_id.';
        }

        if ($hasMonster && $hasPlayer) {
            $errors['target'][] = 'Informe apenas encounter_monster_id OU encounter_player_id, não ambos.';
        }

        if ($errors) {
            throw new ValidationException('Dados inválidos.', $errors);
        }
    }
}
