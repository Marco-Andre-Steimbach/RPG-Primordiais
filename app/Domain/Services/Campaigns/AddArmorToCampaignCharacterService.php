<?php

namespace App\Domain\Services\Campaigns;

use App\Application\DTOs\Campaigns\AddArmorToCampaignCharacterDTO;
use App\Core\Exceptions\ValidationException;
use App\Core\Exceptions\ConflictException;
use App\Infrastructure\Repositories\CampaignCharacterRepository;
use App\Infrastructure\Repositories\ArmorRepository;
use App\Infrastructure\Repositories\WeaponRepository;
use App\Infrastructure\Repositories\CampaignCharacterArmorRepository;
use App\Infrastructure\Repositories\ArmorSlotRepository;

class AddArmorToCampaignCharacterService
{
    private CampaignCharacterRepository $campaignCharacters;
    private ArmorRepository $armors;
    private WeaponRepository $weapons;
    private ArmorSlotRepository $slots;
    private CampaignCharacterArmorRepository $campaignArmors;

    public function __construct()
    {
        $this->campaignCharacters = new CampaignCharacterRepository();
        $this->armors             = new ArmorRepository();
        $this->weapons            = new WeaponRepository();
        $this->slots              = new ArmorSlotRepository();
        $this->campaignArmors     = new CampaignCharacterArmorRepository();
    }

    public function execute(
        int $campaignCharacterId,
        AddArmorToCampaignCharacterDTO $dto
    ): void {
        $campaignCharacter = $this->campaignCharacters->findById($campaignCharacterId);

        if (!$campaignCharacter) {
            throw new ValidationException(
                'Personagem inválido.',
                ['campaign_character_id' => ['Não encontrado.']]
            );
        }

        if ($this->weapons->existsByItemId($dto->armor_item_id)) {
            throw new ValidationException(
                'Item inválido.',
                ['armor_item_id' => ['Este item é uma arma.']]
            );
        }

        $armor = $this->armors->findByItemId($dto->armor_item_id);

        if (!$armor) {
            throw new ValidationException(
                'Item inválido.',
                ['armor_item_id' => ['Este item não é uma armadura.']]
            );
        }

        if ($this->campaignArmors->exists($campaignCharacterId, $armor->id)) {
            throw new ConflictException('Esta armadura já foi adicionada.');
        }

        $slot = $this->slots->findById($armor->armor_slot_id);

        if (!$slot) {
            throw new ValidationException(
                'Slot inválido.',
                ['armor_slot_id' => ['Slot não encontrado.']]
            );
        }

        if ((bool) $slot['is_exclusive']) {
            $this->campaignArmors
                ->deactivateBySlot(
                    $campaignCharacterId,
                    $armor->armor_slot_id
                );
        }

        $equip = $dto->equip === true;

        if ($equip) {
            if ((bool) $slot['is_exclusive']) {
                $this->campaignArmors
                    ->unequipBySlot(
                        $campaignCharacterId,
                        $armor->armor_slot_id
                    );
            }
        }

        $this->campaignArmors->create([
            'campaign_character_id' => $campaignCharacterId,
            'armor_id'              => $armor->id,
            'is_active'             => true,
            'is_equipped'           => $equip,
        ]);
    }
}
