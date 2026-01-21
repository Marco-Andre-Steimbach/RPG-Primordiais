<?php

namespace App\Domain\Services\Campaigns;

use App\Core\Exceptions\ValidationException;
use App\Infrastructure\Repositories\CampaignRepository;
use App\Infrastructure\Repositories\CampaignCharacterRepository;
use App\Infrastructure\Repositories\CampaignCharacterArmorRepository;
use App\Infrastructure\Repositories\CampaignCharacterItemRepository;
use App\Infrastructure\Repositories\CampaignCharacterWeaponRepository;

use App\Infrastructure\Repositories\ArmorRepository;
use App\Infrastructure\Repositories\ArmorElementTypeRepository;
use App\Infrastructure\Repositories\ArmorArmorAbilityRepository;
use App\Infrastructure\Repositories\ArmorAbilityRepository;

use App\Infrastructure\Repositories\WeaponRepository;
use App\Infrastructure\Repositories\WeaponElementTypeRepository;
use App\Infrastructure\Repositories\WeaponAbilityRepository;
use App\Infrastructure\Repositories\WeaponAbilityElementTypeRepository;

use App\Infrastructure\Repositories\ItemRepository;
use App\Infrastructure\Repositories\ItemElementTypeRepository;
use App\Infrastructure\Repositories\ItemAbilityRepository;

class GetLupidaService
{
    public function execute(int $campaignId): array
    {
        $campaignRepo = new CampaignRepository();
        $campaignCharacterRepo = new CampaignCharacterRepository();

        $campaignArmorRepo = new CampaignCharacterArmorRepository();
        $campaignItemRepo = new CampaignCharacterItemRepository();
        $campaignWeaponRepo = new CampaignCharacterWeaponRepository();

        $armorRepo = new ArmorRepository();
        $armorElementRepo = new ArmorElementTypeRepository();
        $armorArmorAbilityRepo = new ArmorArmorAbilityRepository();
        $armorAbilityRepo = new ArmorAbilityRepository();

        $weaponRepo = new WeaponRepository();
        $weaponElementRepo = new WeaponElementTypeRepository();
        $weaponAbilityRepo = new WeaponAbilityRepository();
        $weaponAbilityElementRepo = new WeaponAbilityElementTypeRepository();

        $itemRepo = new ItemRepository();
        $itemElementRepo = new ItemElementTypeRepository();
        $itemAbilityRepo = new ItemAbilityRepository();

        $campaign = $campaignRepo->findById($campaignId);

        if (!$campaign) {
            throw new ValidationException(
                'Campanha inválida.',
                ['campaign_id' => ['Campanha não encontrada.']]
            );
        }

        $characters = $campaignCharacterRepo->findByCampaignId($campaignId);

        $ownedArmorIds = [];
        $ownedWeaponIds = [];
        $ownedItemIds = [];

        foreach ($characters as $character) {
            $cid = (int) $character['id'];

            foreach ($campaignArmorRepo->findActiveByCampaignCharacter($cid) as $row) {
                $ownedArmorIds[(int) $row['armor_id']] = true;
            }

            foreach ($campaignWeaponRepo->findActiveByCampaignCharacter($cid) as $row) {
                $ownedWeaponIds[(int) $row['weapon_id']] = true;
            }

            foreach ($campaignItemRepo->findActiveByCampaignCharacter($cid) as $row) {
                $ownedItemIds[(int) $row['item_id']] = true;
            }
        }

        $armors = array_values(array_filter(
            $armorRepo->findAllWithItemAndSlot(),
            fn ($a) => !isset($ownedArmorIds[(int) $a['armor_id']])
        ));

        $weapons = array_values(array_filter(
            $weaponRepo->findAllWithItemAndDamageType(),
            fn ($w) => !isset($ownedWeaponIds[(int) $w['id']])
        ));

        $items = array_values(array_filter(
            $itemRepo->findAllNonEquipable(),
            fn ($i) => !isset($ownedItemIds[(int) $i['id']])
        ));

        shuffle($armors);
        shuffle($weapons);
        shuffle($items);

        $armors = array_slice($armors, 0, 5);
        $weapons = array_slice($weapons, 0, 5);
        $items = array_slice($items, 0, 5);

        foreach ($armors as &$armor) {
            $armorId = (int) $armor['armor_id'];
            $itemId = (int) $armor['item_id'];

            $armor['value'] = $itemRepo->getValueById($itemId);
            $armor['elements'] = $armorElementRepo->getByArmorId($armorId);

            if ((int) $armor['armor_slot_id'] === 2) {
                $armor['weak_damage_type_id'] = $armor['weak_damage_type_id']
                    ? (int) $armor['weak_damage_type_id']
                    : null;
            }

            $abilityIds = $armorArmorAbilityRepo->getByArmorId($armorId);
            $armor['abilities'] = array_map(
                fn ($id) => $armorAbilityRepo->findById($id)->toArray(),
                $abilityIds
            );
        }

        foreach ($weapons as &$weapon) {
            $weaponId = (int) $weapon['id'];
            $itemId = (int) $weapon['item_id'];

            $weapon['value'] = $itemRepo->getValueById($itemId);
            $weapon['elements'] = $weaponElementRepo->getByWeaponId($weaponId);

            $abilities = $weaponAbilityRepo->findByWeaponId($weaponId);
            foreach ($abilities as &$ability) {
                $ability->element_types =
                    $weaponAbilityElementRepo->getByWeaponAbilityId($ability->id);
                $ability = $ability->toArray();
            }

            $weapon['abilities'] = $abilities;
        }

        $items = array_map(function ($item) use ($itemRepo, $itemElementRepo, $itemAbilityRepo) {
            return [
                'item_id' => (int) $item['id'],
                'item_name' => $item['name'],
                'item_description' => $item['description'],
                'quantity' => 1,
                'value' => $itemRepo->getValueById((int) $item['id']),
                'elements' => $itemElementRepo->getByItemId((int) $item['id']),
                'abilities' => $itemAbilityRepo->getByItemId((int) $item['id']),
            ];
        }, $items);

        return [
            'campaign_id' => $campaignId,
            'armors' => $armors,
            'weapons' => $weapons,
            'items' => $items,
        ];
    }
}
