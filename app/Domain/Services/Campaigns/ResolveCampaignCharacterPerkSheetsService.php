<?php

namespace App\Domain\Services\Campaigns;

class ResolveCampaignCharacterPerkSheetsService
{
    public function execute(
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

            $sheetPerks =
                $this->getOwnedSheetPerks(
                    $perks,
                    $sheetPerkId
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
                    $this->resolveSheet(
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

    private function getOwnedSheetPerks(
        array $perks,
        int $sheetPerkId
    ): array {
        $sheetPerks = [];

        foreach ($perks as $candidate) {
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

        return $sheetPerks;
    }

    private function resolveSheet(
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

        $resolvedHp =
            match ($hpSource) {
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
                $sheet[
                    'speed_mode'
                ]
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
                $sheet[
                    'actions_mode'
                ]
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
                    $sheet[
                        'auto_spawn'
                    ]
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
