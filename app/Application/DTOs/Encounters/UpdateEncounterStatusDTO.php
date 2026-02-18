<?php

namespace App\Application\DTOs\Encounters;

use App\Core\Exceptions\ValidationException;

class UpdateEncounterStatusDTO
{
    public int $encounter_id;
    public string $status;

    private const ALLOWED_STATUS = [
        'pending',
        'active',
        'finished'
    ];

    public function __construct(array $data)
    {
        $this->encounter_id = (int) ($data['encounter_id'] ?? 0);
        $this->status = trim((string) ($data['status'] ?? ''));

        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if ($this->encounter_id <= 0) {
            $errors['encounter_id'][] = 'encounter_id inválido.';
        }

        if (!in_array($this->status, self::ALLOWED_STATUS, true)) {
            $errors['status'][] = 'Status inválido. Valores permitidos: pending, active, finished.';
        }

        if ($errors) {
            throw new ValidationException('Dados inválidos.', $errors);
        }
    }
}
