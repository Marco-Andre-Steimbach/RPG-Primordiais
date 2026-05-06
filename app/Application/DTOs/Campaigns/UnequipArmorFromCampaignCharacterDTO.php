<?php

namespace App\Application\DTOs\Campaigns;

class UnequipArmorFromCampaignCharacterDTO
{
    public int $armor_slot_id;

    public function __construct(array $data)
    {
        $this->armor_slot_id = (int) ($data['armor_slot_id'] ?? 0);
    }
}
