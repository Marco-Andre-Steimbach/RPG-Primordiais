<?php

namespace App\Application\DTOs\Campaigns;

use App\Core\Exceptions\ValidationException;

class LevelUpCampaignCharacterDTO
{
    public int $campaign_character_id;
    public string $attribute;

    public function __construct(array $data)
    {
        $this->campaign_character_id = (int) ($data['campaign_character_id'] ?? 0);
        $this->attribute = $data['attribute'] ?? '';

        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if ($this->campaign_character_id <= 0) {
            $errors['campaign_character_id'][] = 'campaign_character_id inválido.';
        }

        $allowedAttributes = ['str', 'dex', 'con', 'intt', 'wis', 'cha'];

        if (!in_array($this->attribute, $allowedAttributes, true)) {
            $errors['attribute'][] = 'Atributo inválido para level-up.';
        }

        if ($errors) {
            throw new ValidationException('Dados inválidos.', $errors);
        }
    }
}
