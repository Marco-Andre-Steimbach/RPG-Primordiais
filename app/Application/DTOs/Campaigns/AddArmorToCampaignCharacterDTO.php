<?php

namespace App\Application\DTOs\Campaigns;

use App\Core\Exceptions\ValidationException;

class AddArmorToCampaignCharacterDTO
{
    public int $armor_item_id;
    public bool $equip;

    public function __construct(array $data)
    {
        $this->armor_item_id = (int) ($data['armor_item_id'] ?? 0);
        $this->equip         = (bool) ($data['equip'] ?? false);

        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if ($this->armor_item_id <= 0) {
            $errors['armor_item_id'][] = 'Item de armadura inválido.';
        }

        if ($errors) {
            throw new ValidationException(
                'Dados inválidos.',
                $errors
            );
        }
    }
}
