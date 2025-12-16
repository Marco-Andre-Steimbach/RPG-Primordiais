<?php

namespace App\Application\DTOs\Perks;

use App\Core\Exceptions\ValidationException;

class CreatePerkDTO
{
    public string $name;
    public string $description;
    public string $type;
    public int $mana_cost;

    public int $race_id;
    public int $required_level;

    public array $element_types;
    public array $flags;
    public array $attributes;
    public array $ability;

    public function __construct(array $data)
    {
        $this->name = trim((string) ($data['name'] ?? ''));
        $this->description = trim((string) ($data['description'] ?? ''));
        $this->type = (string) ($data['type'] ?? '');
        $this->mana_cost = (int) ($data['mana_cost'] ?? 0);

        $this->race_id = (int) ($data['race_id'] ?? 0);
        $this->required_level = (int) ($data['required_level'] ?? 1);

        $this->element_types = $this->normalizeElementTypes($data['element_types'] ?? []);
        $this->flags = $this->normalizeFlags($data['flags'] ?? []);
        $this->attributes = $this->normalizeAttributes($data['attributes'] ?? []);
        $this->ability = $this->normalizeAbility($data['ability'] ?? null);

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
            $errors['type'][] = "Tipo inválido. Use 'passive' ou 'active'.";
        }

        if ($this->mana_cost < 0) {
            $errors['mana_cost'][] = 'mana_cost não pode ser negativo.';
        }

        if ($this->race_id <= 0) {
            $errors['race_id'][] = 'race_id é obrigatório.';
        }

        if ($this->required_level <= 0) {
            $errors['required_level'][] = 'required_level deve ser >= 1.';
        }

        if (!empty($this->element_types) && count(array_unique($this->element_types)) !== count($this->element_types)) {
            $errors['element_types'][] = 'element_types contém IDs duplicados.';
        }

        if (!empty($this->flags) && count(array_unique($this->flags)) !== count($this->flags)) {
            $errors['flags'][] = 'flags contém valores duplicados.';
        }

        foreach ($this->attributes as $i => $attr) {
            if (!isset($attr['name']) || trim((string) $attr['name']) === '') {
                $errors["attributes.$i.name"][] = 'attribute name é obrigatório.';
            }

            if (!isset($attr['value']) || !is_int($attr['value'])) {
                $errors["attributes.$i.value"][] = 'attribute value deve ser inteiro.';
            }
        }

        if ($this->ability !== []) {
            if (trim((string) ($this->ability['name'] ?? '')) === '') {
                $errors['ability.name'][] = 'ability.name é obrigatório quando ability for enviado.';
            }

            if (trim((string) ($this->ability['description'] ?? '')) === '') {
                $errors['ability.description'][] = 'ability.description é obrigatório quando ability for enviado.';
            }

            if (isset($this->ability['base_damage']) && $this->ability['base_damage'] < 0) {
                $errors['ability.base_damage'][] = 'ability.base_damage não pode ser negativo.';
            }

            foreach (['bonus_accuracy', 'bonus_damage', 'bonus_speed'] as $k) {
                if (isset($this->ability[$k]) && $this->ability[$k] < 0) {
                    $errors["ability.$k"][] = "ability.$k não pode ser negativo.";
                }
            }
        }

        if ($errors) {
            throw new ValidationException('Dados inválidos.', $errors);
        }
    }

    private function normalizeElementTypes(mixed $value): array
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

        return array_values(array_unique($ids));
    }

    private function normalizeFlags(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $flags = [];

        foreach ($value as $flag) {
            $f = trim((string) $flag);
            if ($f !== '') {
                $flags[] = $f;
            }
        }

        $flags = array_values(array_unique($flags));

        return $flags;
    }

    private function normalizeAttributes(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $attrs = [];

        foreach ($value as $item) {
            if (!is_array($item)) {
                continue;
            }

            $name = trim((string) ($item['name'] ?? ''));
            $val = $item['value'] ?? null;

            if ($name === '') {
                continue;
            }

            if (!is_int($val)) {
                if (is_numeric($val)) {
                    $val = (int) $val;
                } else {
                    continue;
                }
            }

            $attrs[] = [
                'name' => $name,
                'value' => (int) $val,
            ];
        }

        return $attrs;
    }

    private function normalizeAbility(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $name = trim((string) ($value['name'] ?? ''));
        $description = trim((string) ($value['description'] ?? ''));
        $dice_formula = isset($value['dice_formula']) ? trim((string) $value['dice_formula']) : null;

        $base_damage = isset($value['base_damage']) ? (int) $value['base_damage'] : 0;
        $bonus_accuracy = isset($value['bonus_accuracy']) ? (int) $value['bonus_accuracy'] : 0;
        $bonus_damage = isset($value['bonus_damage']) ? (int) $value['bonus_damage'] : 0;
        $bonus_speed = isset($value['bonus_speed']) ? (int) $value['bonus_speed'] : 0;

        return [
            'name' => $name,
            'description' => $description,
            'dice_formula' => $dice_formula !== '' ? $dice_formula : null,
            'base_damage' => $base_damage,
            'bonus_accuracy' => $bonus_accuracy,
            'bonus_damage' => $bonus_damage,
            'bonus_speed' => $bonus_speed,
        ];
    }

    public function hasAbility(): bool
    {
        return $this->ability !== [];
    }
}
