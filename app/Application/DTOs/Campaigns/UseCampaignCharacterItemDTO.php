<?php

namespace App\Application\DTOs\Campaigns;

use App\Core\Exceptions\ValidationException;

class UseCampaignCharacterItemDTO
{
    public int $campaign_character_id;
    public int $item_id;

    public function __construct(array $data)
    {
        $this->campaign_character_id =
            (int) ($data['campaign_character_id'] ?? 0);

        $this->item_id =
            (int) ($data['item_id'] ?? 0);

        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if ($this->campaign_character_id <= 0) {
            $errors['campaign_character_id'][] =
                'campaign_character_id inválido.';
        }

        if ($this->item_id <= 0) {
            $errors['item_id'][] =
                'item_id inválido.';
        }

        if ($errors) {
            throw new ValidationException('Dados inválidos.', $errors);
        }
    }
}
