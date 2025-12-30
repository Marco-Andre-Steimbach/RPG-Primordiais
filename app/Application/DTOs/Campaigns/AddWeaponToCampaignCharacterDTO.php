<?php

namespace App\Application\DTOs\Campaigns;

use App\Core\Exceptions\ValidationException;

class AddWeaponToCampaignCharacterDTO
{
    public int $weapon_id;
    public ?int $deactivate_weapon_id;
    public bool $equip;

    public function __construct(array $data)
    {
        $this->weapon_id = (int) ($data['weapon_id'] ?? 0);
        $this->deactivate_weapon_id = isset($data['deactivate_weapon_id'])
            ? (int) $data['deactivate_weapon_id']
            : null;

        $this->equip = (bool) ($data['equip'] ?? false);

        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if ($this->weapon_id <= 0) {
            $errors['weapon_id'][] = 'weapon_id inválido.';
        }

        if ($this->deactivate_weapon_id !== null && $this->deactivate_weapon_id <= 0) {
            $errors['deactivate_weapon_id'][] = 'deactivate_weapon_id inválido.';
        }

        if ($errors) {
            throw new ValidationException('Dados inválidos.', $errors);
        }
    }
}
