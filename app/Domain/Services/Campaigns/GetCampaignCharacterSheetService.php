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
use App\Infrastructure\Repositories\PerkAbilityRepository;
use App\Infrastructure\Repositories\PerkElementTypeRepository;
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
use App\Infrastructure\Repositories\AbilityNewRepository;
use App\Infrastructure\Repositories\AbilityElementTypeRepository;
use App\Infrastructure\Repositories\CampaignCharacterXPRepository;
use App\Infrastructure\Repositories\CampaignCharacterGoldRepository;

class GetCampaignCharacterSheetService
{
    public function execute(
        int $campaignId,
        int $characterId
    ): array {
        $campaignCharacterRepo =
            new CampaignCharacterRepository();

        $attributesRepo =
            new CampaignCharacterAttributesRepository();

        $characterRepo =
            new CharacterRepository();

        $raceRepo =
            new RaceRepository();

        $orderRepo =
            new OrderRepository();

        $raceAttributeRepo =
            new RaceAttributeRepository();

        $orderAttributeRepo =
            new OrderAttributeRepository();

        $perkRepo =
            new CampaignCharacterPerkRepository();

        $perkBaseRepo =
            new PerkRepository();

        $perkAttributesRepo =
            new PerkAttributeRepository();

        $perkAbilityRepo =
            new PerkAbilityRepository();

        $perkElementRepo =
            new PerkElementTypeRepository();

        $weaponRepo =
            new CampaignCharacterWeaponRepository();

        $weaponBaseRepo =
            new WeaponRepository();

        $weaponElementRepo =
            new WeaponElementTypeRepository();

        $weaponAbilityRepo =
            new WeaponAbilityRepository();

        $weaponAbilityElementRepo =
            new WeaponAbilityElementTypeRepository();

        $armorRepo =
            new CampaignCharacterArmorRepository();

        $armorBaseRepo =
            new ArmorRepository();

        $armorSlotRepo =
            new ArmorSlotRepository();

        $armorElementRepo =
            new ArmorElementTypeRepository();

        $armorArmorAbilityRepo =
            new ArmorArmorAbilityRepository();

        $armorAbilityRepo =
            new ArmorAbilityRepository();

        $itemRepo =
            new CampaignCharacterItemRepository();

        $itemBaseRepo =
            new ItemRepository();

        $itemElementRepo =
            new ItemElementTypeRepository();

        $itemAbilityRepo =
            new ItemAbilityRepository();

        $abilityRepo =
            new CampaignCharacterAbilityRepository();

        $abilityBaseRepo =
            new AbilityRepository();

        $abilityNewRepo =
            new AbilityNewRepository();

        $abilityElementRepo =
            new AbilityElementTypeRepository();

        $xpRepo =
            new CampaignCharacterXPRepository();

        $goldRepo =
            new CampaignCharacterGoldRepository();

        $campaignCharacter =
            $campaignCharacterRepo
                ->findByCampaignAndCharacter(
                    $campaignId,
                    $characterId
                );

        if (!$campaignCharacter) {
            throw new ValidationException(
                'Personagem inválido.',
                [
                    'character_id' => [
                        'Este personagem não pertence à campanha.',
                    ],
                ]
            );
        }

        $campaignCharacterId =
            (int) $campaignCharacter['id'];

        $pendingLevelUps =
            (int) (
                $campaignCharacter['pending_level_ups']
                ?? 0
            );

        $xpData =
            $xpRepo->findByCampaignCharacterId(
                $campaignCharacterId
            );

        $goldData =
            $goldRepo->findByCampaignCharacterId(
                $campaignCharacterId
            );

        $currentXP =
            $xpData
                ? (int) $xpData['current_xp']
                : 0;

        $totalXP =
            $xpData
                ? (int) $xpData['total_xp']
                : 0;

        $gold =
            $goldData
                ? (int) $goldData['gold']
                : 0;

        $level =
            (int) $campaignCharacter['level'];

        $xpToNextLevel =
            $level * 1000;

        if ($currentXP >= $xpToNextLevel) {
            $xpRemaining = 0;
            $currentXP = $xpToNextLevel;
        } else {
            $xpRemaining =
                $xpToNextLevel - $currentXP;
        }

        $attributes =
            $attributesRepo
                ->findByCampaignCharacterId(
                    $campaignCharacterId
                );

        if (!$attributes) {
            throw new ValidationException(
                'Atributos não encontrados.',
                [
                    'attributes' => [
                        'Personagem sem atributos.',
                    ],
                ]
            );
        }

        $character =
            $characterRepo->findById(
                (int) $campaignCharacter['character_id']
            );

        if (!$character) {
            throw new ValidationException(
                'Personagem inválido.',
                [
                    'character_id' => [
                        'Personagem base não encontrado.',
                    ],
                ]
            );
        }

        $race =
            $raceRepo->findById(
                $character->race_id
            );

        $order =
            $character->order_id
                ? $orderRepo->findById(
                    $character->order_id
                )
                : null;

        $baseAttributes = [
            'str' =>
                (int) $attributes['str'],

            'dex' =>
                (int) $attributes['dex'],

            'con' =>
                (int) $attributes['con'],

            'intt' =>
                (int) $attributes['intt'],

            'wis' =>
                (int) $attributes['wis'],

            'cha' =>
                (int) $attributes['cha'],
        ];

        $raceAttributes = [
            'str' => 0,
            'dex' => 0,
            'con' => 0,
            'intt' => 0,
            'wis' => 0,
            'cha' => 0,
        ];

        if ($race) {
            $rawRaceAttributes =
                $raceAttributeRepo->getByRace(
                    $race->id
                );

            foreach (
                $rawRaceAttributes
                as $name => $value
            ) {
                if ($name === 'int') {
                    $raceAttributes['intt'] =
                        (int) $value;

                    continue;
                }

                if (
                    array_key_exists(
                        $name,
                        $raceAttributes
                    )
                ) {
                    $raceAttributes[$name] =
                        (int) $value;
                }
            }
        }

        $orderAttributes = [
            'str' => 0,
            'dex' => 0,
            'con' => 0,
            'intt' => 0,
            'wis' => 0,
            'cha' => 0,
        ];

        if ($order) {
            $rawOrderAttributes =
                $orderAttributeRepo->getByOrder(
                    $order->id
                );

            foreach (
                $rawOrderAttributes
                as $name => $value
            ) {
                if ($name === 'int') {
                    $orderAttributes['intt'] =
                        (int) $value;

                    continue;
                }

                if (
                    array_key_exists(
                        $name,
                        $orderAttributes
                    )
                ) {
                    $orderAttributes[$name] =
                        (int) $value;
                }
            }
        }

        $perkAttributes = [
            'str' => 0,
            'dex' => 0,
            'con' => 0,
            'intt' => 0,
            'wis' => 0,
            'cha' => 0,
        ];

        $perkHpMax = 0;
        $perkManaMax = 0;
        $perkSanityMax = 0;
        $perkArmorClass = 0;
        $perkSpeed = 0;

        $perks = [];

        foreach (
            $perkRepo->findByCampaignCharacter(
                $campaignCharacterId
            )
            as $perkRow
        ) {
            $perk =
                $perkBaseRepo->findById(
                    (int) $perkRow['perk_id']
                );

            if (!$perk) {
                continue;
            }

            $rawPerkAttributes =
                $perkAttributesRepo->getByPerk(
                    $perk->id
                );

            $perkAbilities =
                $perkAbilityRepo->findByPerk(
                    $perk->id
                );

            $perkElements =
                $perkElementRepo
                    ->getElementTypesByPerk(
                        $perk->id
                    );

            foreach (
                $rawPerkAttributes
                as $attr
            ) {
                if (
                    !isset(
                        $attr['attribute_name'],
                        $attr['attribute_value']
                    )
                ) {
                    continue;
                }

                $name =
                    $attr['attribute_name'] === 'int'
                        ? 'intt'
                        : $attr['attribute_name'];

                $value =
                    (int) $attr['attribute_value'];

                if (
                    array_key_exists(
                        $name,
                        $perkAttributes
                    )
                ) {
                    $perkAttributes[$name] +=
                        $value;

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
                }
            }

            $perk->attributes =
                $rawPerkAttributes;

            $perk->ability =
                $perkAbilities;

            $perkData =
                $perk->toArray();

            $perkData['element_types'] =
                $perkElements;

            $perkData['has_attributes'] =
                !empty($rawPerkAttributes);

            $perkData['has_ability'] =
                !empty($perkAbilities);

            $perks[] =
                $perkData;
        }

        $sheet =
            new CampaignCharacterSheet(
                campaign_character_id:
                    $campaignCharacterId,

                level:
                    $level,

                mana_modifier:
                    $character->mana_modifier,

                baseAttributes:
                    $baseAttributes,

                raceAttributes:
                    $raceAttributes,

                orderAttributes:
                    $orderAttributes,

                perkAttributes:
                    $perkAttributes,

                sanity_max:
                    (int) $attributes['sanity_max']
                    + $perkSanityMax,

                sanity_current:
                    (int) $attributes['sanity']
            );

        $baseArr =
            $sheet->toArray();

        $baseArr['hp_max'] =
            max(
                1,
                (int) $baseArr['hp_max']
                + $perkHpMax
            );

        $baseArr['mana_max'] =
            max(
                0,
                (int) $baseArr['mana_max']
                + $perkManaMax
            );

        $strModifier =
            (int) (
                $baseArr['modifiers']['str']
                ?? 0
            );

        $dexModifier =
            (int) (
                $baseArr['modifiers']['dex']
                ?? 0
            );

        $dexSpeedBonus =
            intdiv(
                max(
                    0,
                    $dexModifier
                ),
                5
            ) * 2;

        $speed =
            4
            + $perkSpeed
            + $dexSpeedBonus;

        $armorClass =
            $sheet->getBaseArmorClass()
            + $perkArmorClass;

        $armors = [];

        foreach (
            $armorRepo->findActiveByCampaignCharacter(
                $campaignCharacterId
            )
            as $armorRow
        ) {
            $armor =
                $armorBaseRepo->findById(
                    (int) $armorRow['armor_id']
                );

            if (!$armor) {
                continue;
            }

            $itemInfo =
                $itemBaseRepo
                    ->findNameDescriptionByItemId(
                        (int) $armorRow['item_id']
                    );

            $slot =
                $armorSlotRepo->findById(
                    $armor->armor_slot_id
                );

            $abilityIds =
                $armorArmorAbilityRepo
                    ->getByArmorId(
                        $armor->id
                    );

            $abilities = [];

            foreach (
                $abilityIds
                as $abilityId
            ) {
                $ability =
                    $armorAbilityRepo->findById(
                        (int) $abilityId
                    );

                if ($ability) {
                    $abilities[] =
                        $ability->toArray();
                }
            }

            $isEquipped =
                (int) (
                    $armorRow['is_equipped']
                    ?? 0
                ) === 1;

            if ($isEquipped) {
                $armorClass +=
                    (int) $armor
                        ->armor_class_bonus;

                $minStrengthRequired =
                    (int) (
                        $armor
                            ->min_strength_required
                        ?? 0
                    );

                if (
                    $strModifier
                    < $minStrengthRequired
                ) {
                    $speed -=
                        (int) (
                            $armor
                                ->speed_penalty
                            ?? 0
                        );
                }
            }

            $armors[] = [
                'armor' =>
                    array_merge(
                        $armor->toArray(),
                        [
                            'item_name' =>
                                $itemInfo['name']
                                ?? null,

                            'item_description' =>
                                $itemInfo['description']
                                ?? null,
                        ]
                    ),

                'slot' =>
                    $slot,

                'elements' =>
                    $armorElementRepo
                        ->getByArmorId(
                            $armor->id
                        ),

                'abilities' =>
                    $abilities,

                'is_equipped' =>
                    $isEquipped,
            ];
        }

        $finalArmorClass =
            max(
                0,
                $armorClass
            );

        $finalSpeed =
            max(
                0,
                $speed
            );

        $perkSheets =
            $this->buildPerkSheets(
                $perks,
                (int) $baseArr['hp_max'],
                (int) $baseArr['mana_max'],
                $finalArmorClass,
                $finalSpeed
            );

        $weapons = [];

        $weaponAbilityIds = [];

        foreach (
            $weaponRepo->findActiveByCampaignCharacter(
                $campaignCharacterId
            )
            as $weaponRow
        ) {
            $weapon =
                $weaponBaseRepo
                    ->findByIdWithItemAndDamageType(
                        $weaponRow['weapon_id']
                    );

            if (!$weapon) {
                continue;
            }

            $weapon['element_types'] =
                $weaponElementRepo
                    ->getByWeaponId(
                        $weaponRow['weapon_id']
                    );

            $abilities =
                $weaponAbilityRepo
                    ->findByWeaponId(
                        $weaponRow['weapon_id']
                    );

            foreach (
                $abilities
                as $index => $ability
            ) {
                $weaponAbilityIds[] =
                    (int) $ability->id;

                $ability->element_types =
                    $weaponAbilityElementRepo
                        ->getByWeaponAbilityId(
                            $ability->id
                        );

                $abilities[$index] =
                    $ability->toArray();
            }

            $weapon['abilities'] =
                $abilities;

            $weapon['is_equipped'] =
                isset(
                    $weaponRow['is_equipped']
                )
                    ? (bool) $weaponRow[
                        'is_equipped'
                    ]
                    : false;

            $weapons[] =
                $weapon;
        }

        $weaponAbilityIds =
            array_values(
                array_unique(
                    $weaponAbilityIds
                )
            );

        $items = [];

        foreach (
            $itemRepo->findActiveByCampaignCharacter(
                $campaignCharacterId
            )
            as $itemRow
        ) {
            $item =
                $itemBaseRepo->findById(
                    $itemRow['item_id']
                );

            if (!$item) {
                continue;
            }

            $abilityIds =
                $itemAbilityRepo->getByItemId(
                    $item->id
                );

            $abilities = [];

            foreach (
                $abilityIds
                as $abilityId
            ) {
                $ability =
                    $itemAbilityRepo->findById(
                        $abilityId
                    );

                if ($ability) {
                    $abilities[] =
                        $ability->toArray();
                }
            }

            $items[] = [
                'item' =>
                    $item->toArray(),

                'quantity' =>
                    (int) $itemRow['quantity'],

                'elements' =>
                    $itemElementRepo
                        ->getByItemId(
                            $item->id
                        ),

                'abilities' =>
                    $abilities,
            ];
        }

        $abilities = [];

        foreach (
            $abilityRepo->findByCampaignCharacter(
                $campaignCharacterId
            )
            as $abilityRow
        ) {
            $abilityId =
                (int) $abilityRow['ability_id'];

            $newAbility =
                $abilityNewRepo
                    ->findCompleteById(
                        $abilityId
                    );

            if ($newAbility) {
                $abilities[] = [
                    'ability' =>
                        $newAbility,

                    'elements' =>
                        [],

                    'schema' =>
                        'new',
                ];

                continue;
            }

            $ability =
                $abilityBaseRepo->findById(
                    $abilityId
                );

            if (!$ability) {
                continue;
            }

            $abilities[] = [
                'ability' =>
                    $ability->toArray(),

                'elements' =>
                    $abilityElementRepo
                        ->getByAbilityId(
                            $ability->id
                        ),

                'schema' =>
                    'legacy',
            ];
        }

        return [
            'base' =>
                $baseArr,

            'race' =>
                $race
                    ? $race->toArray()
                    : null,

            'order' =>
                $order
                    ? $order->toArray()
                    : null,

            'derived' => [
                'armor_class' =>
                    $finalArmorClass,

                'speed' =>
                    $finalSpeed,
            ],

            'perks' =>
                $perks,

            'perk_sheets' =>
                $perkSheets,

            'weapons' =>
                $weapons,

            'armors' =>
                $armors,

            'items' =>
                $items,

            'abilities' =>
                $abilities,

            'progression' => [
                'level' =>
                    $level,

                'pending_level_ups' =>
                    $pendingLevelUps,

                'xp' => [
                    'current' =>
                        $currentXP,

                    'total' =>
                        $totalXP,

                    'to_next_level' =>
                        $xpRemaining,

                    'required_for_next_level' =>
                        $xpToNextLevel,
                ],

                'gold' =>
                    $gold,
            ],
        ];
    }

    private function buildPerkSheets(
        array $perks,
        int $ownerMaxHp,
        int $ownerMaxMana,
        int $ownerArmorClass,
        int $ownerSpeed
    ): array {
        $result = [];

        foreach ($perks as $perk) {
            $sheet =
                $perk['sheet'] ?? null;

            if (
                !is_array($sheet)
                || $sheet === []
            ) {
                continue;
            }

            $sheetPerkId =
                (int) (
                    $perk['id']
                    ?? 0
                );

            if ($sheetPerkId <= 0) {
                continue;
            }

            $sheetPerks = [];

            foreach (
                $perks
                as $candidate
            ) {
                $sheetPerkIds =
                    $candidate[
                        'sheet_perk_ids'
                    ] ?? [];

                if (
                    !is_array(
                        $sheetPerkIds
                    )
                ) {
                    continue;
                }

                $sheetPerkIds =
                    array_map(
                        'intval',
                        $sheetPerkIds
                    );

                if (
                    !in_array(
                        $sheetPerkId,
                        $sheetPerkIds,
                        true
                    )
                ) {
                    continue;
                }

                $sheetPerks[] =
                    $candidate;
            }

            usort(
                $sheetPerks,
                fn(
                    array $left,
                    array $right
                ): int =>
                    (int) (
                        $left['id']
                        ?? 0
                    )
                    <=>
                    (int) (
                        $right['id']
                        ?? 0
                    )
            );

            $result[] = [
                'perk_id' =>
                    $sheetPerkId,

                'name' =>
                    $perk['name']
                    ?? null,

                'description' =>
                    $perk['description']
                    ?? null,

                'sheet_type' =>
                    $sheet['sheet_type']
                    ?? null,

                'entity_mode' =>
                    $sheet['entity_mode']
                    ?? null,

                'config' =>
                    $sheet,

                'resolved' =>
                    $this->resolvePerkSheet(
                        $sheet,
                        $ownerMaxHp,
                        $ownerMaxMana,
                        $ownerArmorClass,
                        $ownerSpeed
                    ),

                'perks' =>
                    $sheetPerks,
            ];
        }

        return $result;
    }

    private function resolvePerkSheet(
        array $sheet,
        int $ownerMaxHp,
        int $ownerMaxMana,
        int $ownerArmorClass,
        int $ownerSpeed
    ): array {
        $hpSource =
            (string) (
                $sheet['hp_source']
                ?? 'owner_max'
            );

        $baseHp =
            (int) (
                $sheet['base_hp']
                ?? 0
            );

        $hpNumerator =
            (int) (
                $sheet[
                    'owner_hp_numerator'
                ]
                ?? 0
            );

        $hpDenominator =
            max(
                1,
                (int) (
                    $sheet[
                        'owner_hp_denominator'
                    ]
                    ?? 1
                )
            );

        $resolvedHp = match (
            $hpSource
        ) {
            'fixed' =>
                $baseHp,

            'owner_max' =>
                $baseHp
                + (int) floor(
                    (
                        $ownerMaxHp
                        * $hpNumerator
                    )
                    / $hpDenominator
                ),

            'owner_current' =>
                null,

            default =>
                null,
        };

        $manaMode =
            (string) (
                $sheet['mana_mode']
                ?? 'derived'
            );

        $baseMana =
            (int) (
                $sheet['base_mana']
                ?? 0
            );

        $manaNumerator =
            (int) (
                $sheet[
                    'owner_mana_numerator'
                ]
                ?? 0
            );

        $manaDenominator =
            max(
                1,
                (int) (
                    $sheet[
                        'owner_mana_denominator'
                    ]
                    ?? 1
                )
            );

        $resolvedMana =
            $manaMode === 'owner'
                ? $ownerMaxMana
                : $baseMana
                    + (int) floor(
                        (
                            $ownerMaxMana
                            * $manaNumerator
                        )
                        / $manaDenominator
                    );

        $armorClassMode =
            (string) (
                $sheet[
                    'armor_class_mode'
                ]
                ?? 'fixed'
            );

        $armorClassValue =
            (int) (
                $sheet[
                    'armor_class'
                ]
                ?? 0
            );

        $resolvedArmorClass =
            $armorClassMode === 'add'
                ? $ownerArmorClass
                    + $armorClassValue
                : $armorClassValue;

        $speedMode =
            (string) (
                $sheet['speed_mode']
                ?? 'fixed'
            );

        $speedValue =
            (int) (
                $sheet['speed']
                ?? 0
            );

        $resolvedSpeed =
            $speedMode === 'owner'
                ? $ownerSpeed
                : $speedValue;

        $actionsMode =
            (string) (
                $sheet['actions_mode']
                ?? 'fixed'
            );

        $resolvedActions =
            $actionsMode === 'owner'
                ? null
                : (int) (
                    $sheet[
                        'actions_per_turn'
                    ]
                    ?? 0
                );

        $initiativeMode =
            (string) (
                $sheet[
                    'initiative_mode'
                ]
                ?? 'own'
            );

        $resolvedInitiative =
            $initiativeMode === 'owner'
                ? null
                : (
                    $sheet[
                        'initiative_rule'
                    ]
                    ?? null
                );

        return [
            'hp' =>
                $resolvedHp,

            'hp_runtime' =>
                $hpSource ===
                'owner_current',

            'mana' =>
                max(
                    0,
                    $resolvedMana
                ),

            'mana_cost_multiplier' =>
                (float) (
                    $sheet[
                        'mana_cost_multiplier'
                    ]
                    ?? 1
                ),

            'armor_class' =>
                max(
                    0,
                    $resolvedArmorClass
                ),

            'speed' =>
                max(
                    0,
                    $resolvedSpeed
                ),

            'actions_per_turn' =>
                $resolvedActions,

            'actions_from_owner' =>
                $actionsMode === 'owner',

            'initiative_rule' =>
                $resolvedInitiative,

            'initiative_from_owner' =>
                $initiativeMode === 'owner',

            'duration_formula' =>
                $sheet[
                    'duration_formula'
                ]
                ?? null,

            'duration_unit' =>
                $sheet[
                    'duration_unit'
                ]
                ?? null,

            'hp_revert_mode' =>
                $sheet[
                    'hp_revert_mode'
                ]
                ?? 'none',

            'auto_spawn' =>
                (bool) (
                    $sheet['auto_spawn']
                    ?? false
                ),

            'restore_hp_on_spawn' =>
                (bool) (
                    $sheet[
                        'restore_hp_on_spawn'
                    ]
                    ?? false
                ),

            'restore_mana_on_spawn' =>
                (bool) (
                    $sheet[
                        'restore_mana_on_spawn'
                    ]
                    ?? false
                ),
        ];
    }
}
