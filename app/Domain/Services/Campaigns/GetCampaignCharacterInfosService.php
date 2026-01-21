<?php

namespace App\Domain\Services\Campaigns;

use App\Core\Exceptions\ValidationException;
use App\Infrastructure\Repositories\CampaignCharacterRepository;
use App\Infrastructure\Repositories\CampaignCharacterPerkRepository;
use App\Infrastructure\Repositories\CampaignCharacterAbilityRepository;
use App\Infrastructure\Repositories\CampaignCharacterWeaponRepository;
use App\Infrastructure\Repositories\CampaignCharacterArmorRepository;
use App\Infrastructure\Repositories\CampaignCharacterGoldRepository;

class GetCampaignCharacterInfosService
{
    public function execute(int $campaignId, int $characterId): array
    {
        $campaignCharacterRepo = new CampaignCharacterRepository();

        $perkRepo = new CampaignCharacterPerkRepository();
        $abilityRepo = new CampaignCharacterAbilityRepository();
        $weaponRepo = new CampaignCharacterWeaponRepository();
        $armorRepo = new CampaignCharacterArmorRepository();
        $goldRepo = new CampaignCharacterGoldRepository();

        $campaignCharacter = $campaignCharacterRepo
            ->findByCampaignAndCharacter($campaignId, $characterId);

        if (!$campaignCharacter) {
            throw new ValidationException(
                'Personagem inválido.',
                ['character_id' => ['Este personagem não pertence à campanha.']]
            );
        }

        $campaignCharacterId = (int) $campaignCharacter['id'];
        $goldData = $goldRepo->findByCampaignCharacterId($campaignCharacterId);

        $currentGold = $goldData
            ? (int) $goldData['current_gold']
            : 0;


        return [
            'campaign_character_id' => (int) $campaignCharacter['id'],
            'level' => (int) $campaignCharacter['level'],
            'gold' => $currentGold,
            'perks' => count(
                $perkRepo->findByCampaignCharacter($campaignCharacterId)
            ),
            'abilities' => count(
                $abilityRepo->findByCampaignCharacter($campaignCharacterId)
            ),
            'weapons' => count(
                $weaponRepo->findActiveByCampaignCharacter($campaignCharacterId)
            ),
            'armors' => count(
                $armorRepo->findActiveByCampaignCharacter($campaignCharacterId)
            ),
        ];
    }
}
