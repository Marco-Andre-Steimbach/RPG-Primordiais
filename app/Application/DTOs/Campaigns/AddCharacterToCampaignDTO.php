<?php

namespace App\Application\DTOs\Campaigns;

use App\Core\Exceptions\ValidationException;

class AddCharacterToCampaignDTO
{
    public int $character_id;
    public array $attributes;

    public function __construct(array $data)
    {
        $this->character_id = (int) ($data['character_id'] ?? 0);
        $this->attributes = $data['attributes'] ?? [];

        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if ($this->character_id <= 0) {
            $errors['character_id'][] = 'character_id inválido.';
        }

        if (!is_array($this->attributes) || empty($this->attributes)) {
            $errors['attributes'][] = 'Attributes são obrigatórios.';
        }

        $allowedAttributes = ['str', 'dex', 'con', 'intt', 'wis', 'cha', 'sanity'];

        foreach ($this->attributes as $attr => $value) {
            if (!in_array($attr, $allowedAttributes, true)) {
                $errors['attributes'][] = "Atributo inválido: {$attr}.";
                continue;
            }

            if (!is_int($value) || $value < 0) {
                $errors['attributes'][] = "Valor inválido para {$attr}.";
            }
        }

        if ($errors) {
            throw new ValidationException('Dados inválidos.', $errors);
        }
    }
}
