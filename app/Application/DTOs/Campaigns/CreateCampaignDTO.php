<?php

namespace App\Application\DTOs\Campaigns;

use App\Core\Exceptions\ValidationException;

class CreateCampaignDTO
{
    public string $name;
    public ?string $description;

    public function __construct(array $data)
    {
        $this->name = trim((string) ($data['name'] ?? ''));
        $this->description = isset($data['description'])
            ? trim((string) $data['description'])
            : null;

        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if ($this->name === '') {
            $errors['name'][] = 'Nome da campanha é obrigatório.';
        }

        if (strlen($this->name) < 3) {
            $errors['name'][] = 'Nome da campanha deve ter ao menos 3 caracteres.';
        }

        if ($errors) {
            throw new ValidationException('Dados inválidos.', $errors);
        }
    }
}
