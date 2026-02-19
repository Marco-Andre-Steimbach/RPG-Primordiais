<?php

namespace App\Domain\Services\Campaigns;

use App\Application\DTOs\Campaigns\LevelUpCampaignCharacterDTO;
use App\Core\Exceptions\ValidationException;
use App\Infrastructure\Repositories\CampaignCharacterRepository;
use App\Infrastructure\Repositories\CampaignCharacterAttributesRepository;

class LevelUpCampaignCharacterService
{
    private CampaignCharacterRepository $campaignCharacters;
    private CampaignCharacterAttributesRepository $attributes;

    public function __construct()
    {
        $this->campaignCharacters = new CampaignCharacterRepository();
        $this->attributes = new CampaignCharacterAttributesRepository();
    }

    public function execute(
        LevelUpCampaignCharacterDTO $dto,
        int $userId
    ): void {
        $campaignCharacter = $this->campaignCharacters->findById(
            $dto->campaign_character_id
        );

        if (!$campaignCharacter) {
            throw new ValidationException(
                'Personagem inválido.',
                ['campaign_character_id' => ['Personagem não encontrado.']]
            );
        }

        if ((int) $campaignCharacter['user_id'] !== $userId) {
            throw new ValidationException(
                'Ação não permitida.',
                ['campaign_character_id' => ['Você não controla este personagem.']]
            );
        }

        $pending = (int) ($campaignCharacter['pending_level_ups'] ?? 0);

        if ($pending <= 0) {
            throw new ValidationException(
                'Nada para evoluir.',
                ['pending_level_ups' => ['Este personagem não possui pontos pendentes.']]
            );
        }

        $currentAttributes = $this->attributes->findByCampaignCharacterId(
            $dto->campaign_character_id
        );

        if (!$currentAttributes) {
            throw new ValidationException(
                'Erro interno.',
                ['attributes' => ['Atributos não encontrados.']]
            );
        }

        $this->attributes->updateByCampaignCharacterId(
            $dto->campaign_character_id,
            [
                $dto->attribute => (int) $currentAttributes[$dto->attribute] + 1
            ]
        );

        $this->campaignCharacters->decrementPendingLevelUps(
            $dto->campaign_character_id,
            1
        );
    }
}
