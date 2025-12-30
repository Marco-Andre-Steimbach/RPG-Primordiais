<?php

namespace App\Application\DTOs\Campaigns;

use App\Core\Exceptions\ValidationException;

class AddPerkToCampaignCharacterDTO
{
    public int $perk_id;

    public function __construct(array $data)
    {
        $this->perk_id = (int) ($data['perk_id'] ?? 0);

        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if ($this->perk_id <= 0) {
            $errors['perk_id'][] = 'perk_id inválido.';
        }

        if ($errors) {
            throw new ValidationException('Dados inválidos.', $errors);
        }
    }
}
