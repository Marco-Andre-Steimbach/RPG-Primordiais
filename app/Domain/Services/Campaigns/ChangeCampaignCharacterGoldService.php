<?php

namespace App\Domain\Services\Campaigns;

use App\Application\DTOs\Campaigns\ChangeCharacterGoldAmountDTO;
use App\Core\Exceptions\ValidationException;
use App\Infrastructure\Repositories\CampaignCharacterRepository;
use App\Infrastructure\Repositories\CampaignRepository;
use App\Infrastructure\Repositories\CampaignCharacterGoldRepository;

class ChangeCampaignCharacterGoldService
{
    private CampaignCharacterRepository $campaignCharacters;
    private CampaignRepository $campaigns;
    private CampaignCharacterGoldRepository $gold;

    public function __construct()
    {
        $this->campaignCharacters = new CampaignCharacterRepository();
        $this->campaigns          = new CampaignRepository();
        $this->gold               = new CampaignCharacterGoldRepository();
    }

    public function execute(
        int $campaignCharacterId,
        ChangeCharacterGoldAmountDTO $dto,
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

        $goldData = $this->gold->findByCampaignCharacterId($campaignCharacterId);

        if (!$goldData) {
            $this->gold->create([
                'campaign_character_id' => $campaignCharacterId,
                'gold' => 0,
            ]);

            $currentGold = 0;
        } else {
            $currentGold = (int) $goldData['gold'];
        }

        if ($dto->operation === 'add') {
            $currentGold += $dto->amount;
        } else {
            $currentGold -= $dto->amount;

            if ($currentGold < 0) {
                $currentGold = 0;
            }
        }

        $this->gold->updateByCampaignCharacterId(
            $campaignCharacterId,
            $currentGold
        );

        return [
            'gold' => $currentGold,
        ];
    }
}
