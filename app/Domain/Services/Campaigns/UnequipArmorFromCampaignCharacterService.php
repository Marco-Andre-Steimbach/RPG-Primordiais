<?php

namespace App\Domain\Services\Campaigns;

use App\Application\DTOs\Campaigns\UnequipArmorFromCampaignCharacterDTO;
use App\Core\Exceptions\ValidationException;
use App\Infrastructure\Repositories\CampaignCharacterRepository;
use App\Infrastructure\Repositories\CampaignCharacterArmorRepository;
use App\Infrastructure\Repositories\ArmorSlotRepository;

class UnequipArmorFromCampaignCharacterService
{
    private CampaignCharacterRepository $campaignCharacters;
    private CampaignCharacterArmorRepository $campaignArmors;
    private ArmorSlotRepository $slots;

    public function __construct()
    {
        $this->campaignCharacters = new CampaignCharacterRepository();
        $this->campaignArmors     = new CampaignCharacterArmorRepository();
        $this->slots              = new ArmorSlotRepository();
    }

    public function execute(
        int $campaignCharacterId,
        UnequipArmorFromCampaignCharacterDTO $dto
    ): void {
        $campaignCharacter = $this->campaignCharacters->findById($campaignCharacterId);

        if (!$campaignCharacter) {
            throw new ValidationException(
                'Personagem inválido.',
                ['campaign_character_id' => ['Não encontrado.']]
            );
        }

        $slot = $this->slots->findById($dto->armor_slot_id);

        if (!$slot) {
            throw new ValidationException(
                'Slot inválido.',
                ['armor_slot_id' => ['Slot não encontrado.']]
            );
        }

        $this->campaignArmors
            ->unequipBySlot(
                $campaignCharacterId,
                $dto->armor_slot_id
            );

        $this->campaignArmors
            ->deactivateBySlot(
                $campaignCharacterId,
                $dto->armor_slot_id
            );
    }
}
