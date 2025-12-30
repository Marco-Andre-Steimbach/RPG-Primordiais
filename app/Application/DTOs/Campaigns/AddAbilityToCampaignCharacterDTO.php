<?php

namespace App\Application\DTOs\Campaigns;

use App\Core\Exceptions\ValidationException;

class AddAbilityToCampaignCharacterDTO
{
    public int $ability_id;

    public function __construct(array $data)
    {
        $this->ability_id = (int) ($data['ability_id'] ?? 0);
        $this->validate();
    }

    private function validate(): void
    {
        if ($this->ability_id <= 0) {
            throw new ValidationException(
                'Dados inválidos.',
                ['ability_id' => ['ability_id inválido.']]
            );
        }
    }
}
