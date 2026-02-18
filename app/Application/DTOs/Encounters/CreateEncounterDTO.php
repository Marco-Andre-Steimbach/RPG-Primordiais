<?php

namespace App\Application\DTOs\Encounters;

use App\Core\Exceptions\ValidationException;

class CreateEncounterDTO
{
    public int $campaign_id;
    public string $name;
    public string $description;

    public function __construct(array $data)
    {
        $this->campaign_id = (int) ($data['campaign_id'] ?? 0);
        $this->name = trim((string) ($data['name'] ?? ''));
        $this->description = trim((string) ($data['description'] ?? ''));

        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if ($this->campaign_id <= 0) {
            $errors['campaign_id'][] = 'campaign_id é obrigatório e deve ser válido.';
        }

        if ($this->name === '') {
            $errors['name'][] = 'Nome do encontro é obrigatório.';
        }

        if ($this->description === '') {
            $errors['description'][] = 'Descrição do encontro é obrigatória.';
        }

        if ($errors) {
            throw new ValidationException('Dados inválidos.', $errors);
        }
    }
}
