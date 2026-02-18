<?php

namespace App\Application\DTOs\Encounters;

use App\Core\Exceptions\ValidationException;

class AddMonsterToEncounterDTO
{
    public int $encounter_id;
    public int $monster_id;
    public int $quantity;
    public int $monster_level;

    public function __construct(array $data)
    {
        $this->encounter_id = (int) ($data['encounter_id'] ?? 0);
        $this->monster_id   = (int) ($data['monster_id'] ?? 0);
        $this->quantity     = (int) ($data['quantity'] ?? 0);
        $this->monster_level = (int) ($data['monster_level'] ?? 1);

        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if ($this->encounter_id <= 0) {
            $errors['encounter_id'][] = 'encounter_id inválido.';
        }

        if ($this->monster_id <= 0) {
            $errors['monster_id'][] = 'monster_id inválido.';
        }

        if ($this->quantity <= 0) {
            $errors['quantity'][] = 'quantity deve ser maior que 0.';
        }

        if ($this->quantity > 100) {
            $errors['quantity'][] = 'quantity não pode ser maior que 100.';
        }

        if ($this->monster_level <= 0) {
            $errors['monster_level'][] = 'monster_level deve ser maior que 0.';
        }

        if ($errors) {
            throw new ValidationException('Dados inválidos.', $errors);
        }
    }
}
