<?php

namespace App\Application\DTOs\Encounters;

use App\Core\Exceptions\ValidationException;

class UpdateEncounterResourcesDTO
{
    public string $type;

    public ?int $encounter_player_id;
    public ?int $encounter_monster_id;

    public ?int $current_hp;
    public ?int $current_mana;
    public ?int $current_sanity;

    public function __construct(array $data)
    {
        $this->type = (string) ($data['type'] ?? '');

        $this->encounter_player_id = isset($data['encounter_player_id'])
            ? (int) $data['encounter_player_id']
            : null;

        $this->encounter_monster_id = isset($data['encounter_monster_id'])
            ? (int) $data['encounter_monster_id']
            : null;

        $this->current_hp = array_key_exists('current_hp', $data)
            ? (int) $data['current_hp']
            : null;

        $this->current_mana = array_key_exists('current_mana', $data)
            ? (int) $data['current_mana']
            : null;

        $this->current_sanity = array_key_exists('current_sanity', $data)
            ? (int) $data['current_sanity']
            : null;

        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if (!in_array($this->type, ['player', 'monster'], true)) {
            $errors['type'][] = 'type deve ser player ou monster.';
        }

        if ($this->type === 'player') {
            if (
                $this->encounter_player_id === null ||
                $this->encounter_player_id <= 0
            ) {
                $errors['encounter_player_id'][] =
                    'encounter_player_id inválido.';
            }

            if ($this->encounter_monster_id !== null) {
                $errors['encounter_monster_id'][] =
                    'encounter_monster_id não deve ser informado para player.';
            }

            if (
                $this->current_hp === null &&
                $this->current_mana === null &&
                $this->current_sanity === null
            ) {
                $errors['resources'][] =
                    'Informe pelo menos um recurso para atualizar.';
            }
        }

        if ($this->type === 'monster') {
            if (
                $this->encounter_monster_id === null ||
                $this->encounter_monster_id <= 0
            ) {
                $errors['encounter_monster_id'][] =
                    'encounter_monster_id inválido.';
            }

            if ($this->encounter_player_id !== null) {
                $errors['encounter_player_id'][] =
                    'encounter_player_id não deve ser informado para monster.';
            }

            if ($this->current_hp === null) {
                $errors['current_hp'][] =
                    'current_hp é obrigatório para monster.';
            }

            if (
                $this->current_mana !== null ||
                $this->current_sanity !== null
            ) {
                $errors['resources'][] =
                    'Monstros só podem atualizar current_hp.';
            }
        }

        if ($this->current_hp !== null && $this->current_hp < 0) {
            $errors['current_hp'][] =
                'current_hp não pode ser negativo.';
        }

        if ($this->current_mana !== null && $this->current_mana < 0) {
            $errors['current_mana'][] =
                'current_mana não pode ser negativo.';
        }

        if (
            $this->current_sanity !== null &&
            $this->current_sanity < 0
        ) {
            $errors['current_sanity'][] =
                'current_sanity não pode ser negativo.';
        }

        if ($errors) {
            throw new ValidationException(
                'Dados inválidos.',
                $errors
            );
        }
    }
}
