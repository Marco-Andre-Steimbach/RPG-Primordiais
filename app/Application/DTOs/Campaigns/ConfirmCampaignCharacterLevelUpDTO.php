<?php

namespace App\Application\DTOs\Campaigns;

class ConfirmCampaignCharacterLevelUpDTO
{
    public int $campaign_character_id;
    public string $attribute;

    public function __construct(array $data)
    {
        $this->campaign_character_id = (int) $data['campaign_character_id'];
        $this->attribute = (string) $data['attribute'];
    }
}
