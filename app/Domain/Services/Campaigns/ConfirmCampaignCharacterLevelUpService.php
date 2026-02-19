<?php

namespace App\Domain\Services\Campaigns;

use App\Application\DTOs\Campaigns\ConfirmCampaignCharacterLevelUpDTO;
use App\Core\Exceptions\ValidationException;
use App\Infrastructure\Repositories\CampaignCharacterRepository;
use App\Infrastructure\Repositories\CampaignCharacterAttributesRepository;

class ConfirmCampaignCharacterLevelUpService
{
    private CampaignCharacterRepository $characters;
    private CampaignCharacterAttributesRepository $attributes;

    public function __construct()
    {
        $this->characters = new CampaignCharacterRepository();
        $this->attributes = new CampaignCharacterAttributesRepository();
    }

    public function execute(
        ConfirmCampaignCharacterLevelUpDTO $dto,
        int $userId
    ): void {

        $character = $this->characters->findById(
            $dto->campaign_character_id
        );

        if (!$character) {
            throw new ValidationException(
                'Personagem inválido.',
                ['campaign_character_id' => ['Não encontrado.']]
            );
        }

        if ((int)$character['user_id'] !== $userId) {
            throw new ValidationException(
                'Ação não permitida.',
                ['user' => ['Você não pode evoluir este personagem.']]
            );
        }

        if ((int)$character['pending_level_ups'] <= 0) {
            throw new ValidationException(
                'Nenhum level-up pendente.',
                ['pending_level_ups' => ['Não há pontos para distribuir.']]
            );
        }

        $allowed = ['str', 'dex', 'con', 'intt', 'wis', 'cha'];

        if (!in_array($dto->attribute, $allowed, true)) {
            throw new ValidationException(
                'Atributo inválido.',
                ['attribute' => ['Atributo não permitido.']]
            );
        }

        $this->attributes->incrementAttribute(
            $dto->campaign_character_id,
            $dto->attribute
        );

        $this->characters->decrementPendingLevelUps(
            $dto->campaign_character_id
        );
    }
}
