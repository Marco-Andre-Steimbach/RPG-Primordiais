<?php

namespace App\Application\DTOs\Encounters;

use App\Core\Exceptions\ValidationException;

class AddPlayerToEncounterDTO
{
    public int $encounter_id;
    public int $campaign_character_id;

    public function __construct(array $data)
    {
        $this->encounter_id = (int) ($data['encounter_id'] ?? 0);
        $this->campaign_character_id = (int) ($data['campaign_character_id'] ?? 0);

        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if ($this->encounter_id <= 0) {
            $errors['encounter_id'][] = 'encounter_id inválido.';
        }

        if ($this->campaign_character_id <= 0) {
            $errors['campaign_character_id'][] = 'campaign_character_id inválido.';
        }

        if ($errors) {
            throw new ValidationException('Dados inválidos.', $errors);
        }
    }
}
