<?php

namespace App\Domain\Services\Campaigns;

use App\Core\Exceptions\ValidationException;
use App\Domain\Models\CampaignCharacterSheet;
use App\Infrastructure\Repositories\CampaignCharacterRepository;
use App\Infrastructure\Repositories\CampaignCharacterAttributesRepository;
use App\Infrastructure\Repositories\CharacterRepository;
use App\Infrastructure\Repositories\RaceRepository;
use App\Infrastructure\Repositories\OrderRepository;
use App\Infrastructure\Repositories\RaceAttributeRepository;
use App\Infrastructure\Repositories\OrderAttributeRepository;
use App\Infrastructure\Repositories\CampaignCharacterPerkRepository;
use App\Infrastructure\Repositories\PerkRepository;
use App\Infrastructure\Repositories\CampaignCharacterWeaponRepository;
use App\Infrastructure\Repositories\WeaponRepository;
use App\Infrastructure\Repositories\WeaponElementTypeRepository;
use App\Infrastructure\Repositories\WeaponAbilityRepository;
use App\Infrastructure\Repositories\WeaponAbilityElementTypeRepository;
use App\Infrastructure\Repositories\CampaignCharacterArmorRepository;
use App\Infrastructure\Repositories\ArmorRepository;
use App\Infrastructure\Repositories\ArmorSlotRepository;
use App\Infrastructure\Repositories\ArmorElementTypeRepository;
use App\Infrastructure\Repositories\ArmorArmorAbilityRepository;
use App\Infrastructure\Repositories\ArmorAbilityRepository;
use App\Infrastructure\Repositories\CampaignCharacterItemRepository;
use App\Infrastructure\Repositories\ItemRepository;
use App\Infrastructure\Repositories\ItemElementTypeRepository;
use App\Infrastructure\Repositories\ItemAbilityRepository;
use App\Infrastructure\Repositories\CampaignCharacterAbilityRepository;
use App\Infrastructure\Repositories\AbilityRepository;
use App\Infrastructure\Repositories\AbilityElementTypeRepository;

class GetCampaignCharacterSheetService
{
    public function execute(int $campaignId, int $characterId): array
    {
        $campaignCharacterRepo = new CampaignCharacterRepository();
        $attributesRepo = new CampaignCharacterAttributesRepository();
        $characterRepo = new CharacterRepository();
        $raceRepo = new RaceRepository();
        $orderRepo = new OrderRepository();

        $raceAttributeRepo = new RaceAttributeRepository();
        $orderAttributeRepo = new OrderAttributeRepository();

        $perkRepo = new CampaignCharacterPerkRepository();
        $perkBaseRepo = new PerkRepository();

        $weaponRepo = new CampaignCharacterWeaponRepository();
        $weaponBaseRepo = new WeaponRepository();
        $weaponElementRepo = new WeaponElementTypeRepository();
        $weaponAbilityRepo = new WeaponAbilityRepository();
        $weaponAbilityElementRepo = new WeaponAbilityElementTypeRepository();

        $armorRepo = new CampaignCharacterArmorRepository();
        $armorBaseRepo = new ArmorRepository();
        $armorSlotRepo = new ArmorSlotRepository();
        $armorElementRepo = new ArmorElementTypeRepository();
        $armorArmorAbilityRepo = new ArmorArmorAbilityRepository();
        $armorAbilityRepo = new ArmorAbilityRepository();

        $itemRepo = new CampaignCharacterItemRepository();
        $itemBaseRepo = new ItemRepository();
        $itemElementRepo = new ItemElementTypeRepository();
        $itemAbilityRepo = new ItemAbilityRepository();

        $abilityRepo = new CampaignCharacterAbilityRepository();
        $abilityBaseRepo = new AbilityRepository();
        $abilityElementRepo = new AbilityElementTypeRepository();

        $campaignCharacter = $campaignCharacterRepo
            ->findByCampaignAndCharacter($campaignId, $characterId);

        if (!$campaignCharacter) {
            throw new ValidationException(
                'Personagem inválido.',
                ['character_id' => ['Este personagem não pertence à campanha.']]
            );
        }

        $campaignCharacterId = (int) $campaignCharacter['id'];

        $attributes = $attributesRepo->findByCampaignCharacterId($campaignCharacterId);

        if (!$attributes) {
            throw new ValidationException(
                'Atributos não encontrados.',
                ['attributes' => ['Personagem sem atributos.']]
            );
        }

        $character = $characterRepo->findById((int) $campaignCharacter['character_id']);
        $race = $character ? $raceRepo->findById($character->race_id) : null;
        $order = $character && $character->order_id
            ? $orderRepo->findById($character->order_id)
            : null;

        $baseAttributes = [
            'str'  => (int) $attributes['str'],
            'dex'  => (int) $attributes['dex'],
            'con'  => (int) $attributes['con'],
            'intt' => (int) $attributes['intt'],
            'wis'  => (int) $attributes['wis'],
            'cha'  => (int) $attributes['cha'],
        ];

        $raceAttributes = [
            'str'  => 0,
            'dex'  => 0,
            'con'  => 0,
            'intt' => 0,
            'wis'  => 0,
            'cha'  => 0,
        ];

        if ($race) {
            $rawRaceAttributes = $raceAttributeRepo->getByRace($race->id);

            foreach ($rawRaceAttributes as $name => $value) {
                if ($name === 'int') {
                    $raceAttributes['intt'] = (int) $value;
                    continue;
                }

                if (array_key_exists($name, $raceAttributes)) {
                    $raceAttributes[$name] = (int) $value;
                }
            }
        }

        $orderAttributes = [
            'str'  => 0,
            'dex'  => 0,
            'con'  => 0,
            'intt' => 0,
            'wis'  => 0,
            'cha'  => 0,
        ];

        if ($order) {
            $rawOrderAttributes = $orderAttributeRepo->getByOrder($order->id);

            foreach ($rawOrderAttributes as $name => $value) {
                if ($name === 'int') {
                    $orderAttributes['intt'] = (int) $value;
                    continue;
                }

                if (array_key_exists($name, $orderAttributes)) {
                    $orderAttributes[$name] = (int) $value;
                }
            }
        }

        $sheet = new CampaignCharacterSheet(
            campaign_character_id: $campaignCharacterId,
            level: (int) $campaignCharacter['level'],
            mana_modifier: 'int',
            baseAttributes: $baseAttributes,
            raceAttributes: $raceAttributes,
            orderAttributes: $orderAttributes,
            sanity_max: (int) $attributes['sanity_max'],
            sanity_current: (int) $attributes['sanity']
        );

        $perks = [];
        foreach ($perkRepo->findByCampaignCharacter($campaignCharacterId) as $perk) {
            $base = $perkBaseRepo->findById($perk['perk_id']);
            if ($base) {
                $perks[] = $base->toArray();
            }
        }

        $speed = 4;
        $armorClass = $sheet->getBaseArmorClass();
        $armors = [];

        foreach ($armorRepo->findActiveByCampaignCharacter($campaignCharacterId) as $armorRow) {
            $armor = $armorBaseRepo->findById($armorRow['armor_id']);
            if (!$armor) {
                continue;
            }

            $slot = $armorSlotRepo->findById($armor->armor_slot_id);

            $finalAttributes = $sheet->getFinalAttributes();
            if ($finalAttributes['str'] < $armor->min_strength_required) {
                $speed -= 1;
            }

            $armorClass += $armor->armor_class_bonus;

            $abilityIds = $armorArmorAbilityRepo->getByArmorId($armor->id);
            $abilities = [];

            foreach ($abilityIds as $abilityId) {
                $ability = $armorAbilityRepo->findById($abilityId);
                if ($ability) {
                    $abilities[] = $ability->toArray();
                }
            }

            $armors[] = [
                'armor' => $armor->toArray(),
                'slot' => $slot,
                'elements' => $armorElementRepo->getByArmorId($armor->id),
                'abilities' => $abilities,
                'is_equipped' => (bool) $armorRow['is_equipped'],
            ];
        }

        $weapons = [];
        foreach ($weaponRepo->findActiveByCampaignCharacter($campaignCharacterId) as $weaponRow) {
            $weapon = $weaponBaseRepo->findByIdWithItemAndDamageType($weaponRow['weapon_id']);
            if (!$weapon) {
                continue;
            }

            $weapon['element_types'] = $weaponElementRepo->getByWeaponId($weaponRow['weapon_id']);

            $abilities = $weaponAbilityRepo->findByWeaponId($weaponRow['weapon_id']);
            foreach ($abilities as &$ability) {
                $ability->element_types
                    = $weaponAbilityElementRepo->getByWeaponAbilityId($ability->id);
                $ability = $ability->toArray();
            }

            $weapon['abilities'] = $abilities;
            $weapon['is_equipped'] = (bool) $weaponRow['is_equipped'];

            $weapons[] = $weapon;
        }

        $items = [];
        foreach ($itemRepo->findByCampaignCharacter($campaignCharacterId) as $itemRow) {
            $item = $itemBaseRepo->findById($itemRow['item_id']);
            if (!$item) {
                continue;
            }

            $items[] = [
                'item' => $item->toArray(),
                'quantity' => (int) $itemRow['quantity'],
                'elements' => $itemElementRepo->getByItemId($item->id),
                'abilities' => $itemAbilityRepo->getByItemId($item->id),
            ];
        }

        $abilities = [];
        foreach ($abilityRepo->findByCampaignCharacter($campaignCharacterId) as $abilityRow) {
            $ability = $abilityBaseRepo->findById($abilityRow['ability_id']);
            if (!$ability) {
                continue;
            }

            $abilities[] = [
                'ability' => $ability->toArray(),
                'elements' => $abilityElementRepo->getByAbilityId($ability->id),
            ];
        }

        return [
            'base' => $sheet->toArray(),
            'race' => $race ? $race->toArray() : null,
            'order' => $order ? $order->toArray() : null,
            'derived' => [
                'armor_class' => $armorClass,
                'speed' => max(0, $speed),
            ],
            'perks' => $perks,
            'weapons' => $weapons,
            'armors' => $armors,
            'items' => $items,
            'abilities' => $abilities,
        ];
    }
}
