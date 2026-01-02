<?php

namespace App\Domain\Services\Campaigns;

use App\Core\Exceptions\ValidationException;
use App\Infrastructure\Repositories\CampaignRepository;
use App\Infrastructure\Repositories\CampaignCharacterRepository;
use App\Infrastructure\Repositories\CharacterRepository;
use App\Infrastructure\Repositories\UserRepository;

class GetCampaignByIdService
{
    private CampaignRepository $campaigns;
    private CampaignCharacterRepository $campaignCharacters;
    private CharacterRepository $characters;
    private UserRepository $users;

    public function __construct()
    {
        $this->campaigns = new CampaignRepository();
        $this->campaignCharacters = new CampaignCharacterRepository();
        $this->characters = new CharacterRepository();
        $this->users = new UserRepository();
    }

    public function execute(int $campaignId): array
    {
        $campaign = $this->campaigns->findById($campaignId);

        if (!$campaign) {
            throw new ValidationException(
                'Campanha não encontrada.',
                ['campaign_id' => ['Campanha inválida.']]
            );
        }

        $master = $this->users->findById($campaign->created_by);

        $campaignCharacters
            = $this->campaignCharacters->findByCampaignId($campaignId);

        $characters = [];

        foreach ($campaignCharacters as $cc) {
            $character = $this->characters->findById($cc['character_id']);
            $player = $this->users->findById($cc['user_id']);

            $characters[] = [
                'campaign_character_id' => $cc['id'],
                'character_id' => $cc['character_id'],
                'name' => $character?->name,
                'race_id' => $character?->race_id,
                'order_id' => $character?->order_id,
                'level' => $cc['level'],
                'controlled_by' => $player?->nickname,
            ];
        }

        return [
            'id' => $campaign->id,
            'name' => $campaign->name,
            'description' => $campaign->description,
            'created_at' => $campaign->created_at,
            'master' => $master?->nickname,
            'characters' => $characters,
        ];
    }
}
