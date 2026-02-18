<?php

namespace App\Application\DTOs\Encounters;

use App\Core\Exceptions\ValidationException;

class UpdateEncounterInitiativeDTO
{
    public int $initiative_id;
    public int $initiative_value;

    public function __construct(array $data)
    {
        $this->initiative_id = (int) ($data['initiative_id'] ?? 0);
        $this->initiative_value = (int) ($data['initiative_value'] ?? 0);

        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if ($this->initiative_id <= 0) {
            $errors['initiative_id'][] = 'initiative_id inválido.';
        }

        if ($this->initiative_value <= 0) {
            $errors['initiative_value'][] = 'initiative_value deve ser maior que 0.';
        }

        if ($errors) {
            throw new ValidationException('Dados inválidos.', $errors);
        }
    }
}
