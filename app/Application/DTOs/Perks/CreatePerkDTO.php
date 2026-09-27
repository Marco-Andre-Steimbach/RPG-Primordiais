<?php

namespace App\Application\DTOs\Perks;

use App\Core\Exceptions\ValidationException;

class CreatePerkDTO
{
    public string $name;
    public string $description;
    public string $type;
    public int $mana_cost;

    public ?int $race_id;
    public ?int $order_id;
    public int $required_level;

    public array $element_types;
    public array $flags;
    public array $attributes;
    public array $ability;

    public array $required_perk_ids;
    public array $sheet_perk_ids;
    public ?array $sheet;

    public function __construct(array $data)
    {
        $this->name = trim((string) ($data['name'] ?? ''));
        $this->description = trim((string) ($data['description'] ?? ''));
        $this->type = (string) ($data['type'] ?? '');
        $this->mana_cost = (int) ($data['mana_cost'] ?? 0);

        $this->race_id = isset($data['race_id'])
            ? (int) $data['race_id']
            : null;

        $this->order_id = isset($data['order_id'])
            ? (int) $data['order_id']
            : null;

        $this->required_level = (int) ($data['required_level'] ?? 1);

        $this->element_types = $this->normalizeIds(
            $data['element_types'] ?? []
        );

        $this->flags = $this->normalizeFlags(
            $data['flags'] ?? []
        );

        $this->attributes = $this->normalizeAttributes(
            $data['attributes'] ?? []
        );

        $this->ability = $this->normalizeAbility(
            $data['ability'] ?? []
        );

        $this->required_perk_ids = $this->normalizeIds(
            $data['required_perk_ids'] ?? []
        );

        $this->sheet_perk_ids = $this->normalizeIds(
            $data['sheet_perk_ids'] ?? []
        );

        $this->sheet = $this->normalizeSheet(
            $data['sheet'] ?? null
        );

        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if ($this->name === '') {
            $errors['name'][] = 'Nome é obrigatório.';
        }

        if ($this->description === '') {
            $errors['description'][] = 'Descrição é obrigatória.';
        }

        if (!in_array($this->type, ['passive', 'active'], true)) {
            $errors['type'][] =
                "Tipo inválido. Use 'passive' ou 'active'.";
        }

        if ($this->mana_cost < 0) {
            $errors['mana_cost'][] =
                'mana_cost não pode ser negativo.';
        }

        if ($this->required_level <= 0) {
            $errors['required_level'][] =
                'required_level deve ser >= 1.';
        }

        if (
            $this->race_id !== null
            && $this->order_id !== null
        ) {
            $errors['relation'][] =
                'Envie apenas race_id ou order_id, nunca ambos.';
        }

        if (
            $this->race_id === null
            && $this->order_id === null
        ) {
            $errors['relation'][] =
                'É obrigatório enviar race_id ou order_id.';
        }

        if (
            $this->race_id !== null
            && $this->race_id <= 0
        ) {
            $errors['race_id'][] =
                'race_id inválido.';
        }

        if (
            $this->order_id !== null
            && $this->order_id <= 0
        ) {
            $errors['order_id'][] =
                'order_id inválido.';
        }

        foreach ($this->attributes as $i => $attr) {
            if (
                !isset($attr['name'])
                || trim((string) $attr['name']) === ''
            ) {
                $errors["attributes.$i.name"][] =
                    'attribute name é obrigatório.';
            }

            if (
                !array_key_exists('value', $attr)
                || !is_int($attr['value'])
            ) {
                $errors["attributes.$i.value"][] =
                    'attribute value deve ser inteiro.';
            }
        }

        if ($this->hasAbility()) {
            if (
                trim(
                    (string) ($this->ability['name'] ?? '')
                ) === ''
            ) {
                $errors['ability.name'][] =
                    'ability.name é obrigatório quando ability for enviado.';
            }

            if (
                trim(
                    (string) (
                        $this->ability['description'] ?? ''
                    )
                ) === ''
            ) {
                $errors['ability.description'][] =
                    'ability.description é obrigatório quando ability for enviado.';
            }

            if (
                isset($this->ability['base_damage'])
                && $this->ability['base_damage'] < 0
            ) {
                $errors['ability.base_damage'][] =
                    'ability.base_damage não pode ser negativo.';
            }

            if (
                isset($this->ability['range'])
                && $this->ability['range'] < 0
            ) {
                $errors['ability.range'][] =
                    'ability.range não pode ser negativo.';
            }

            foreach (
                [
                    'bonus_accuracy',
                    'bonus_damage',
                    'bonus_speed',
                ] as $key
            ) {
                if (
                    isset($this->ability[$key])
                    && $this->ability[$key] < 0
                ) {
                    $errors["ability.$key"][] =
                        "ability.$key não pode ser negativo.";
                }
            }
        }

        if ($this->hasSheet()) {
            if (
                trim(
                    (string) (
                        $this->sheet['sheet_type'] ?? ''
                    )
                ) === ''
            ) {
                $errors['sheet.sheet_type'][] =
                    'sheet.sheet_type é obrigatório.';
            }

            if (
                !in_array(
                    $this->sheet['entity_mode'] ?? '',
                    [
                        'independent',
                        'owner_overlay',
                    ],
                    true
                )
            ) {
                $errors['sheet.entity_mode'][] =
                    'sheet.entity_mode inválido.';
            }

            if (
                !in_array(
                    $this->sheet['hp_source'] ?? '',
                    [
                        'fixed',
                        'owner_max',
                        'owner_current',
                    ],
                    true
                )
            ) {
                $errors['sheet.hp_source'][] =
                    'sheet.hp_source inválido.';
            }

            if (
                ($this->sheet['base_hp'] ?? 0) < 0
            ) {
                $errors['sheet.base_hp'][] =
                    'sheet.base_hp não pode ser negativo.';
            }

            if (
                ($this->sheet['owner_hp_numerator'] ?? 0) < 0
            ) {
                $errors['sheet.owner_hp_numerator'][] =
                    'sheet.owner_hp_numerator não pode ser negativo.';
            }

            if (
                ($this->sheet['owner_hp_denominator'] ?? 0) <= 0
            ) {
                $errors['sheet.owner_hp_denominator'][] =
                    'sheet.owner_hp_denominator deve ser maior que 0.';
            }

            if (
                ($this->sheet['base_mana'] ?? 0) < 0
            ) {
                $errors['sheet.base_mana'][] =
                    'sheet.base_mana não pode ser negativo.';
            }

            if (
                ($this->sheet['owner_mana_numerator'] ?? 0) < 0
            ) {
                $errors['sheet.owner_mana_numerator'][] =
                    'sheet.owner_mana_numerator não pode ser negativo.';
            }

            if (
                ($this->sheet['owner_mana_denominator'] ?? 0) <= 0
            ) {
                $errors['sheet.owner_mana_denominator'][] =
                    'sheet.owner_mana_denominator deve ser maior que 0.';
            }

            if (
                !in_array(
                    $this->sheet['mana_mode'] ?? '',
                    [
                        'derived',
                        'owner',
                    ],
                    true
                )
            ) {
                $errors['sheet.mana_mode'][] =
                    'sheet.mana_mode inválido.';
            }

            if (
                ($this->sheet['mana_cost_multiplier'] ?? 1) < 0
            ) {
                $errors['sheet.mana_cost_multiplier'][] =
                    'sheet.mana_cost_multiplier não pode ser negativo.';
            }

            if (
                ($this->sheet['armor_class'] ?? 0) < 0
            ) {
                $errors['sheet.armor_class'][] =
                    'sheet.armor_class não pode ser negativo.';
            }

            if (
                !in_array(
                    $this->sheet['armor_class_mode'] ?? '',
                    [
                        'fixed',
                        'add',
                    ],
                    true
                )
            ) {
                $errors['sheet.armor_class_mode'][] =
                    'sheet.armor_class_mode inválido.';
            }

            if (
                ($this->sheet['speed'] ?? 0) < 0
            ) {
                $errors['sheet.speed'][] =
                    'sheet.speed não pode ser negativo.';
            }

            if (
                !in_array(
                    $this->sheet['speed_mode'] ?? '',
                    [
                        'fixed',
                        'owner',
                    ],
                    true
                )
            ) {
                $errors['sheet.speed_mode'][] =
                    'sheet.speed_mode inválido.';
            }

            if (
                ($this->sheet['actions_per_turn'] ?? 0) < 0
            ) {
                $errors['sheet.actions_per_turn'][] =
                    'sheet.actions_per_turn não pode ser negativo.';
            }

            if (
                !in_array(
                    $this->sheet['actions_mode'] ?? '',
                    [
                        'fixed',
                        'owner',
                    ],
                    true
                )
            ) {
                $errors['sheet.actions_mode'][] =
                    'sheet.actions_mode inválido.';
            }

            if (
                !in_array(
                    $this->sheet['initiative_mode'] ?? '',
                    [
                        'own',
                        'owner',
                    ],
                    true
                )
            ) {
                $errors['sheet.initiative_mode'][] =
                    'sheet.initiative_mode inválido.';
            }

            if (
                ($this->sheet['duration_unit'] ?? null) !== null
                && !in_array(
                    $this->sheet['duration_unit'],
                    [
                        'turn',
                        'round',
                        'combat',
                    ],
                    true
                )
            ) {
                $errors['sheet.duration_unit'][] =
                    'sheet.duration_unit inválido.';
            }

            if (
                !in_array(
                    $this->sheet['hp_revert_mode'] ?? '',
                    [
                        'none',
                        'proportional',
                    ],
                    true
                )
            ) {
                $errors['sheet.hp_revert_mode'][] =
                    'sheet.hp_revert_mode inválido.';
            }

            if (
                ($this->sheet['duration_formula'] ?? null) !== null
                && ($this->sheet['duration_unit'] ?? null) === null
            ) {
                $errors['sheet.duration_unit'][] =
                    'duration_unit é obrigatório quando duration_formula for informado.';
            }

            if (
                ($this->sheet['duration_formula'] ?? null) === null
                && ($this->sheet['duration_unit'] ?? null) !== null
            ) {
                $errors['sheet.duration_formula'][] =
                    'duration_formula é obrigatório quando duration_unit for informado.';
            }
        }

        if ($errors) {
            throw new ValidationException(
                'Dados inválidos.',
                $errors
            );
        }
    }

    private function normalizeIds(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $ids = [];

        foreach ($value as $id) {
            $intId = (int) $id;

            if ($intId > 0) {
                $ids[] = $intId;
            }
        }

        return array_values(
            array_unique($ids)
        );
    }

    private function normalizeFlags(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $flags = [];

        foreach ($value as $flag) {
            $normalizedFlag = trim(
                (string) $flag
            );

            if ($normalizedFlag !== '') {
                $flags[] = $normalizedFlag;
            }
        }

        return array_values(
            array_unique($flags)
        );
    }

    private function normalizeAttributes(
        mixed $value
    ): array {
        if (!is_array($value)) {
            return [];
        }

        $attributes = [];

        foreach ($value as $item) {
            if (!is_array($item)) {
                continue;
            }

            $name = trim(
                (string) ($item['name'] ?? '')
            );

            $attributeValue =
                $item['value'] ?? null;

            if ($name === '') {
                continue;
            }

            if (!is_int($attributeValue)) {
                if (is_numeric($attributeValue)) {
                    $attributeValue =
                        (int) $attributeValue;
                } else {
                    continue;
                }
            }

            $attributes[] = [
                'name' => $name,
                'value' => $attributeValue,
            ];
        }

        return $attributes;
    }

    private function normalizeAbility(
        mixed $value
    ): array {
        if (
            !is_array($value)
            || $value === []
        ) {
            return [];
        }

        $name = trim(
            (string) ($value['name'] ?? '')
        );

        $description = trim(
            (string) (
                $value['description'] ?? ''
            )
        );

        $diceFormula =
            isset($value['dice_formula'])
                ? trim(
                    (string) $value['dice_formula']
                )
                : null;

        return [
            'name' => $name,
            'description' => $description,

            'dice_formula' =>
                $diceFormula !== ''
                    ? $diceFormula
                    : null,

            'base_damage' =>
                isset($value['base_damage'])
                    ? (int) $value['base_damage']
                    : 0,

            'bonus_accuracy' =>
                isset($value['bonus_accuracy'])
                    ? (int) $value['bonus_accuracy']
                    : 0,

            'bonus_damage' =>
                isset($value['bonus_damage'])
                    ? (int) $value['bonus_damage']
                    : 0,

            'bonus_speed' =>
                isset($value['bonus_speed'])
                    ? (int) $value['bonus_speed']
                    : 0,

            'range' =>
                isset($value['range'])
                    ? (int) $value['range']
                    : 0,
        ];
    }

    private function normalizeSheet(
        mixed $value
    ): ?array {
        if (
            !is_array($value)
            || $value === []
        ) {
            return null;
        }

        $sheetType = trim(
            (string) (
                $value['sheet_type']
                ?? 'companion'
            )
        );

        $entityMode = trim(
            (string) (
                $value['entity_mode']
                ?? 'independent'
            )
        );

        $hpSource = trim(
            (string) (
                $value['hp_source']
                ?? 'owner_max'
            )
        );

        $manaMode = trim(
            (string) (
                $value['mana_mode']
                ?? 'derived'
            )
        );

        $armorClassMode = trim(
            (string) (
                $value['armor_class_mode']
                ?? 'fixed'
            )
        );

        $speedMode = trim(
            (string) (
                $value['speed_mode']
                ?? 'fixed'
            )
        );

        $actionsMode = trim(
            (string) (
                $value['actions_mode']
                ?? 'fixed'
            )
        );

        $initiativeMode = trim(
            (string) (
                $value['initiative_mode']
                ?? 'own'
            )
        );

        $initiativeRule = isset(
            $value['initiative_rule']
        )
            ? trim(
                (string) $value['initiative_rule']
            )
            : null;

        $durationFormula = isset(
            $value['duration_formula']
        )
            ? trim(
                (string) $value['duration_formula']
            )
            : null;

        $durationUnit = isset(
            $value['duration_unit']
        )
            ? trim(
                (string) $value['duration_unit']
            )
            : null;

        $hpRevertMode = trim(
            (string) (
                $value['hp_revert_mode']
                ?? 'none'
            )
        );

        return [
            'sheet_type' =>
                $sheetType !== ''
                    ? $sheetType
                    : 'companion',

            'entity_mode' =>
                $entityMode !== ''
                    ? $entityMode
                    : 'independent',

            'hp_source' =>
                $hpSource !== ''
                    ? $hpSource
                    : 'owner_max',

            'base_hp' =>
                isset($value['base_hp'])
                    ? (int) $value['base_hp']
                    : 0,

            'owner_hp_numerator' =>
                isset($value['owner_hp_numerator'])
                    ? (int) $value['owner_hp_numerator']
                    : 0,

            'owner_hp_denominator' =>
                isset($value['owner_hp_denominator'])
                    ? (int) $value['owner_hp_denominator']
                    : 1,

            'base_mana' =>
                isset($value['base_mana'])
                    ? (int) $value['base_mana']
                    : 0,

            'owner_mana_numerator' =>
                isset($value['owner_mana_numerator'])
                    ? (int) $value['owner_mana_numerator']
                    : 0,

            'owner_mana_denominator' =>
                isset($value['owner_mana_denominator'])
                    ? (int) $value['owner_mana_denominator']
                    : 1,

            'mana_mode' =>
                $manaMode !== ''
                    ? $manaMode
                    : 'derived',

            'mana_cost_multiplier' =>
                isset($value['mana_cost_multiplier'])
                    ? (float) $value['mana_cost_multiplier']
                    : 1.0,

            'armor_class' =>
                isset($value['armor_class'])
                    ? (int) $value['armor_class']
                    : 0,

            'armor_class_mode' =>
                $armorClassMode !== ''
                    ? $armorClassMode
                    : 'fixed',

            'speed' =>
                isset($value['speed'])
                    ? (int) $value['speed']
                    : 0,

            'speed_mode' =>
                $speedMode !== ''
                    ? $speedMode
                    : 'fixed',

            'actions_per_turn' =>
                isset($value['actions_per_turn'])
                    ? (int) $value['actions_per_turn']
                    : 0,

            'actions_mode' =>
                $actionsMode !== ''
                    ? $actionsMode
                    : 'fixed',

            'initiative_rule' =>
                $initiativeRule !== ''
                    ? $initiativeRule
                    : null,

            'initiative_mode' =>
                $initiativeMode !== ''
                    ? $initiativeMode
                    : 'own',

            'duration_formula' =>
                $durationFormula !== ''
                    ? $durationFormula
                    : null,

            'duration_unit' =>
                $durationUnit !== ''
                    ? $durationUnit
                    : null,

            'hp_revert_mode' =>
                $hpRevertMode !== ''
                    ? $hpRevertMode
                    : 'none',

            'auto_spawn' =>
                $this->normalizeBoolean(
                    $value['auto_spawn']
                    ?? false
                ),

            'restore_hp_on_spawn' =>
                $this->normalizeBoolean(
                    $value['restore_hp_on_spawn']
                    ?? false
                ),

            'restore_mana_on_spawn' =>
                $this->normalizeBoolean(
                    $value['restore_mana_on_spawn']
                    ?? false
                ),
        ];
    }

    private function normalizeBoolean(
        mixed $value
    ): bool {
        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value)) {
            return $value === 1;
        }

        if (is_string($value)) {
            return in_array(
                strtolower(trim($value)),
                ['1', 'true', 'yes', 'on'],
                true
            );
        }

        return false;
    }

    public function hasAbility(): bool
    {
        return $this->ability !== [];
    }

    public function hasAttributes(): bool
    {
        return $this->attributes !== [];
    }

    public function hasRequirements(): bool
    {
        return $this->required_perk_ids !== [];
    }

    public function belongsToSheets(): bool
    {
        return $this->sheet_perk_ids !== [];
    }

    public function hasSheet(): bool
    {
        return $this->sheet !== null;
    }
}
