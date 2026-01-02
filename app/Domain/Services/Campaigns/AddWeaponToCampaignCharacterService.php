<?php

namespace App\Domain\Services\Campaigns;

use App\Application\DTOs\Campaigns\AddWeaponToCampaignCharacterDTO;
use App\Core\Exceptions\ValidationException;
use App\Core\Exceptions\ConflictException;
use App\Infrastructure\Repositories\CampaignCharacterRepository;
use App\Infrastructure\Repositories\WeaponRepository;
use App\Infrastructure\Repositories\ArmorRepository;
use App\Infrastructure\Repositories\CampaignCharacterWeaponRepository;

class AddWeaponToCampaignCharacterService
{
    private CampaignCharacterRepository $campaignCharacters;
    private WeaponRepository $weapons;
    private ArmorRepository $armors;
    private CampaignCharacterWeaponRepository $campaignWeapons;

    public function __construct()
    {
        $this->campaignCharacters = new CampaignCharacterRepository();
        $this->weapons            = new WeaponRepository();
        $this->armors             = new ArmorRepository();
        $this->campaignWeapons    = new CampaignCharacterWeaponRepository();
    }

    public function execute(
        int $campaignCharacterId,
        AddWeaponToCampaignCharacterDTO $dto
    ): void {
        $campaignCharacter = $this->campaignCharacters->findById($campaignCharacterId);

        if (!$campaignCharacter) {
            throw new ValidationException(
                'Personagem inválido.',
                ['campaign_character_id' => ['Não encontrado.']]
            );
        }

        $weapon = $this->weapons->findByItemId($dto->weapon_id);

        if (!$weapon) {
            throw new ValidationException(
                'Item inválido.',
                ['weapon_id' => ['Este item não é uma arma.']]
            );
        }

        if ($this->armors->existsByItemId($dto->weapon_id)) {
            throw new ValidationException(
                'Item inválido.',
                ['weapon_id' => ['Este item é uma armadura, não uma arma.']]
            );
        }

        if ($this->campaignWeapons->exists($campaignCharacterId, $weapon->id)) {
            throw new ConflictException('Esta arma já foi adicionada ao personagem.');
        }

        $activeWeapons = $this->campaignWeapons
            ->findActiveByCampaignCharacter($campaignCharacterId);

        if (count($activeWeapons) >= 2) {
            if (!$dto->deactivate_weapon_id) {
                throw new ValidationException(
                    'Limite atingido, deactivate_weapon_id Informe qual arma será desativada.'
                );
            }

            $weaponToDeactivate = $this->campaignWeapons
                ->findById($dto->deactivate_weapon_id);

            if (
                !$weaponToDeactivate
                || (int) $weaponToDeactivate['campaign_character_id'] !== $campaignCharacterId
                || !$weaponToDeactivate['is_active']
            ) {
                throw new ValidationException(
                    'Arma inválida.',
                    ['deactivate_weapon_id' => ['Arma inválida para desativação.']]
                );
            }

            $this->campaignWeapons->deactivate($weaponToDeactivate['id']);
        }

        $equip = $dto->equip === true;

        if ($equip) {
            $this->campaignWeapons->unequipAll($campaignCharacterId);
        }

        $this->campaignWeapons->create([
            'campaign_character_id' => $campaignCharacterId,
            'weapon_id'             => $weapon->id,
            'is_active'             => true,
            'is_equipped'           => $equip,
        ]);
    }
}
