<?php

namespace App\Application\DTOs\Armors;

use App\Core\Exceptions\ValidationException;

class CreateArmorDTO
{
    public int $item_id;
    public int $armor_slot_id;

    public int $armor_class_bonus;
    public int $min_strength_required;
    public int $speed_penalty;

    public array $element_types;
    public array $armor_abilities;

    public function __construct(array $data)
    {
        $this->item_id = (int) ($data['item_id'] ?? 0);
        $this->armor_slot_id = (int) ($data['armor_slot_id'] ?? 0);

        $this->armor_class_bonus = (int) ($data['armor_class_bonus'] ?? 0);
        $this->min_strength_required = (int) ($data['min_strength_required'] ?? 0);
        $this->speed_penalty = (int) ($data['speed_penalty'] ?? 0);

        $this->element_types = $this->normalizeIds($data['element_types'] ?? []);
        $this->armor_abilities = $this->normalizeIds($data['armor_abilities'] ?? []);

        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if ($this->item_id <= 0) {
            $errors['item_id'][] = 'item_id inválido.';
        }

        if ($this->armor_slot_id <= 0) {
            $errors['armor_slot_id'][] = 'armor_slot_id inválido.';
        }

        if ($this->armor_class_bonus < 0) {
            $errors['armor_class_bonus'][] = 'armor_class_bonus não pode ser negativo.';
        }

        if ($this->min_strength_required < 0) {
            $errors['min_strength_required'][] = 'min_strength_required não pode ser negativo.';
        }

        if ($this->speed_penalty < 0) {
            $errors['speed_penalty'][] = 'speed_penalty não pode ser negativo.';
        }

        if (count($this->element_types) !== count(array_unique($this->element_types))) {
            $errors['element_types'][] = 'element_types contém IDs duplicados.';
        }

        if (count($this->armor_abilities) !== count(array_unique($this->armor_abilities))) {
            $errors['armor_abilities'][] = 'armor_abilities contém IDs duplicados.';
        }

        if ($errors) {
            throw new ValidationException('Dados inválidos.', $errors);
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

        return array_values(array_unique($ids));
    }
}
