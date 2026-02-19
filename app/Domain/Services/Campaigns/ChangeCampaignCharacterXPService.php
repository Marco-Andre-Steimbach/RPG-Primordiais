<?php

namespace App\Domain\Services\Campaigns;

use App\Application\DTOs\Campaigns\ChangeCharacterXPAmountDTO;
use App\Core\Exceptions\ValidationException;
use App\Infrastructure\Repositories\CampaignCharacterRepository;
use App\Infrastructure\Repositories\CampaignRepository;
use App\Infrastructure\Repositories\CampaignCharacterXPRepository;

class ChangeCampaignCharacterXPService
{
    private CampaignCharacterRepository $campaignCharacters;
    private CampaignRepository $campaigns;
    private CampaignCharacterXPRepository $xp;

    public function __construct()
    {
        $this->campaignCharacters = new CampaignCharacterRepository();
        $this->campaigns          = new CampaignRepository();
        $this->xp                 = new CampaignCharacterXPRepository();
    }

    public function execute(
        int $campaignCharacterId,
        ChangeCharacterXPAmountDTO $dto,
        int $userId
    ): array {

        $campaignCharacter = $this->campaignCharacters->findById($campaignCharacterId);

        if (!$campaignCharacter) {
            throw new ValidationException(
                'Personagem inválido.',
                ['campaign_character_id' => ['Não encontrado.']]
            );
        }

        $campaign = $this->campaigns->findById(
            (int) $campaignCharacter['campaign_id']
        );

        if (!$campaign) {
            throw new ValidationException(
                'Campanha inválida.',
                ['campaign' => ['Campanha não encontrada.']]
            );
        }

        if ((int) $campaign->created_by !== $userId) {
            throw new ValidationException(
                'Ação não permitida.',
                ['user' => ['Apenas o mestre pode alterar o XP.']]
            );
        }

        $xpData = $this->xp->findByCampaignCharacterId($campaignCharacterId);

        if (!$xpData) {
            $this->xp->create([
                'campaign_character_id' => $campaignCharacterId,
                'current_xp'            => 0,
                'total_xp'              => 0,
            ]);

            $currentXP = 0;
            $totalXP   = 0;
        } else {
            $currentXP = (int) $xpData['current_xp'];
            $totalXP   = (int) $xpData['total_xp'];
        }

        $level = (int) $campaignCharacter['level'];
        $levelsGained = 0;

        if ($dto->operation === 'add') {

            $currentXP += $dto->amount;
            $totalXP   += $dto->amount;

            while (true) {

                $xpToLevel = $level * 1000;

                if ($currentXP >= $xpToLevel) {
                    $currentXP -= $xpToLevel;
                    $level++;
                    $levelsGained++;
                } else {
                    break;
                }
            }

            if ($levelsGained > 0) {
                $this->campaignCharacters->updateLevel(
                    $campaignCharacterId,
                    $level
                );
            }

        } else {

            $currentXP -= $dto->amount;

            if ($currentXP < 0) {
                $currentXP = 0;
            }

            if ($totalXP - $dto->amount >= 0) {
                $totalXP -= $dto->amount;
            } else {
                $totalXP = 0;
            }
        }

        $this->xp->updateByCampaignCharacterId(
            $campaignCharacterId,
            $currentXP,
            $totalXP
        );

        return [
            'level'           => $level,
            'current_xp'      => $currentXP,
            'total_xp'        => $totalXP,
            'levels_gained'   => $levelsGained,
            'next_level_xp'   => $level * 1000
        ];
    }
}
