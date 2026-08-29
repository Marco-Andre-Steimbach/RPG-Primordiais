<?php

namespace App\Application\DTOs\Encounters;

use App\Core\Exceptions\ValidationException;

class UpdateEncounterResourcesDTO
{
    public string $type;

    public ?int $encounter_player_id;
    public ?int $encounter_monster_id;

    public string $resource;
    public string $mode;
    public int $value;

    public function __construct(array $data)
    {
        $this->type = (string) ($data['type'] ?? '');

        $this->encounter_player_id = isset($data['encounter_player_id'])
            ? (int) $data['encounter_player_id']
            : null;

        $this->encounter_monster_id = isset($data['encounter_monster_id'])
            ? (int) $data['encounter_monster_id']
            : null;

        $this->resource = (string) ($data['resource'] ?? '');
        $this->mode = (string) ($data['mode'] ?? '');

        $this->value = array_key_exists('value', $data)
            ? (int) $data['value']
            : 0;

        $this->validate($data);
    }

    private function validate(array $data): void
    {
        $errors = [];

        if (!in_array($this->type, ['player', 'monster'], true)) {
            $errors['type'][] =
                'type deve ser player ou monster.';
        }

        if (!in_array($this->mode, ['delta', 'set'], true)) {
            $errors['mode'][] =
                'mode deve ser delta ou set.';
        }

        if (!array_key_exists('value', $data)) {
            $errors['value'][] =
                'value é obrigatório.';
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
                !in_array(
                    $this->resource,
                    ['hp', 'mana', 'sanity'],
                    true
                )
            ) {
                $errors['resource'][] =
                    'resource deve ser hp, mana ou sanity.';
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

            if ($this->resource !== 'hp') {
                $errors['resource'][] =
                    'Monstros só podem atualizar hp.';
            }
        }

        if (
            $this->mode === 'set' &&
            $this->value < 0
        ) {
            $errors['value'][] =
                'value não pode ser negativo quando mode for set.';
        }

        if ($errors) {
            throw new ValidationException(
                'Dados inválidos.',
                $errors
            );
        }
    }
}
