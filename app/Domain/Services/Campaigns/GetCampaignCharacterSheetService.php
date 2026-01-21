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
use App\Infrastructure\Repositories\PerkAttributeRepository;
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
use App\Infrastructure\Repositories\CampaignCharacterXPRepository;
use App\Infrastructure\Repositories\CampaignCharacterGoldRepository;

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
        $perkAttributesRepo = new PerkAttributeRepository();

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
        $xpRepo = new CampaignCharacterXPRepository();
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

        $xpData = $xpRepo->findByCampaignCharacterId($campaignCharacterId);
        $goldData = $goldRepo->findByCampaignCharacterId($campaignCharacterId);

        $currentXP = $xpData ? (int) $xpData['current_xp'] : 0;
        $totalXP   = $xpData ? (int) $xpData['total_xp'] : 0;
        $gold      = $goldData ? (int) $goldData['gold'] : 0;

        $level = (int) $campaignCharacter['level'];

        $xpToNextLevel = $level * 1000;
        $xpRemaining = max(0, $xpToNextLevel - $currentXP);

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

        $perkAttributes = [
            'str'  => 0,
            'dex'  => 0,
            'con'  => 0,
            'intt' => 0,
            'wis'  => 0,
            'cha'  => 0,
        ];

        $perkHpMax = 0;
        $perkManaMax = 0;
        $perkSanityMax = 0;
        $perkArmorClass = 0;
        $perkSpeed = 0;

        $perks = [];

        foreach ($perkRepo->findByCampaignCharacter($campaignCharacterId) as $perkRow) {
            $perk = $perkBaseRepo->findById($perkRow['perk_id']);
            if (!$perk) {
                continue;
            }

            $rawPerkAttributes = $perkAttributesRepo->getByPerk($perk->id);

            foreach ($rawPerkAttributes as $attr) {
                if (!isset($attr['attribute_name'], $attr['attribute_value'])) {
                    continue;
                }

                $name = $attr['attribute_name'] === 'int'
                    ? 'intt'
                    : $attr['attribute_name'];

                $value = (int) $attr['attribute_value'];

                if (array_key_exists($name, $perkAttributes)) {
                    $perkAttributes[$name] += $value;
                    continue;
                }

                if ($name === 'hp_max') {
                    $perkHpMax += $value;
                    continue;
                }

                if ($name === 'mana_max') {
                    $perkManaMax += $value;
                    continue;
                }

                if ($name === 'sanity') {
                    $perkSanityMax += $value;
                    continue;
                }

                if ($name === 'armor_class') {
                    $perkArmorClass += $value;
                    continue;
                }

                if ($name === 'speed') {
                    $perkSpeed += $value;
                    continue;
                }
            }

            $perks[] = $perk->toArray();
        }

        $sheet = new CampaignCharacterSheet(
            campaign_character_id: $campaignCharacterId,
            level: (int) $campaignCharacter['level'],
            mana_modifier: $character->mana_modifier,
            baseAttributes: $baseAttributes,
            raceAttributes: $raceAttributes,
            orderAttributes: $orderAttributes,
            perkAttributes: $perkAttributes,
            sanity_max: (int) $attributes['sanity_max'] + $perkSanityMax,
            sanity_current: (int) $attributes['sanity']
        );

        $speed = 4 + $perkSpeed;
        $armorClass = $sheet->getBaseArmorClass() + $perkArmorClass;

        $armors = [];

        foreach ($armorRepo->findActiveByCampaignCharacter($campaignCharacterId) as $armorRow) {
            $armor = $armorBaseRepo->findById($armorRow['armor_id']);
            if (!$armor) {
                continue;
            }

            $slot = $armorSlotRepo->findById($armor->armor_slot_id);

            $abilityIds = $armorArmorAbilityRepo->getByArmorId($armor->id);
            $abilities = [];

            foreach ($abilityIds as $abilityId) {
                $ability = $armorAbilityRepo->findById($abilityId);
                if ($ability) {
                    $abilities[] = $ability->toArray();
                }
            }

            $isEquipped = isset($armorRow['is_equipped'])
                ? (bool) $armorRow['is_equipped']
                : false;

            if ($isEquipped) {
                $armorClass += (int) $armor->armor_class_bonus;
                $speed -= (int) ($armor->speed_penalty ?? 0);
            }

            $armors[] = [
                'armor' => $armor->toArray(),
                'slot' => $slot,
                'elements' => $armorElementRepo->getByArmorId($armor->id),
                'abilities' => $abilities,
                'is_equipped' => $isEquipped,
            ];
        }

        $weapons = [];
        $weaponAbilityIds = [];

        foreach ($weaponRepo->findActiveByCampaignCharacter($campaignCharacterId) as $weaponRow) {
            $weapon = $weaponBaseRepo->findByIdWithItemAndDamageType($weaponRow['weapon_id']);
            if (!$weapon) {
                continue;
            }

            $weapon['element_types'] = $weaponElementRepo->getByWeaponId($weaponRow['weapon_id']);

            $abilities = $weaponAbilityRepo->findByWeaponId($weaponRow['weapon_id']);
            foreach ($abilities as $index => $ability) {
                $weaponAbilityIds[] = (int) $ability->id;
                $ability->element_types =
                    $weaponAbilityElementRepo->getByWeaponAbilityId($ability->id);
                $abilities[$index] = $ability->toArray();
            }

            $weapon['abilities'] = $abilities;
            $weapon['is_equipped'] = isset($weaponRow['is_equipped'])
                ? (bool) $weaponRow['is_equipped']
                : false;

            $weapons[] = $weapon;
        }

        $weaponAbilityIds = array_values(array_unique($weaponAbilityIds));

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

            if (in_array((int) $ability->id, $weaponAbilityIds, true)) {
                continue;
            }

            $abilities[] = [
                'ability' => $ability->toArray(),
                'elements' => $abilityElementRepo->getByAbilityId($ability->id),
            ];
        }

        $baseArr = $sheet->toArray();
        $baseArr['hp_max'] = max(1, (int) $baseArr['hp_max'] + $perkHpMax);
        $baseArr['mana_max'] = max(0, (int) $baseArr['mana_max'] + $perkManaMax);

        return [
            'base' => $baseArr,
            'race' => $race ? $race->toArray() : null,
            'order' => $order ? $order->toArray() : null,
            'derived' => [
                'armor_class' => max(0, $armorClass),
                'speed' => max(0, $speed),
            ],
            'perks' => $perks,
            'weapons' => $weapons,
            'armors' => $armors,
            'items' => $items,
            'abilities' => $abilities,
            'progression' => [
                'level' => $level,
                'xp' => [
                    'current' => $currentXP,
                    'total' => $totalXP,
                    'to_next_level' => $xpRemaining,
                    'required_for_next_level' => $xpToNextLevel,
                ],
                'gold' => $gold,
            ],
        ];
    }
}
