<?php

namespace App\Application\DTOs\Weapons;

use App\Core\Exceptions\ValidationException;

class CreateWeaponDTO
{
    public int $item_id;
    public int $weapon_damage_type_id;

    public string $dice_formula;
    public int $base_damage;
    public int $bonus_accuracy;
    public int $bonus_speed;
    public int $range;

    public ?int $ammo_item_id;
    public int $ammo_per_use;

    public array $element_types;

    public function __construct(array $data)
    {
        $this->item_id = (int) ($data['item_id'] ?? 0);
        $this->weapon_damage_type_id = (int) ($data['weapon_damage_type_id'] ?? 0);

        $this->dice_formula = trim((string) ($data['dice_formula'] ?? ''));

        $this->base_damage = (int) ($data['base_damage'] ?? 0);
        $this->bonus_accuracy = (int) ($data['bonus_accuracy'] ?? 0);
        $this->bonus_speed = (int) ($data['bonus_speed'] ?? 0);
        $this->range = (int) ($data['range'] ?? 1);

        $this->ammo_item_id = isset($data['ammo_item_id'])
            ? (int) $data['ammo_item_id']
            : null;

        $this->ammo_per_use = (int) ($data['ammo_per_use'] ?? 1);

        $this->element_types = $this->normalizeElementTypes($data['element_types'] ?? []);

        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if ($this->item_id <= 0) {
            $errors['item_id'][] = 'item_id é obrigatório.';
        }

        if ($this->weapon_damage_type_id <= 0) {
            $errors['weapon_damage_type_id'][] = 'weapon_damage_type_id é obrigatório.';
        }

        if ($this->dice_formula === '') {
            $errors['dice_formula'][] = 'dice_formula é obrigatória.';
        }

        if ($this->range <= 0) {
            $errors['range'][] = 'range deve ser maior que zero.';
        }

        foreach (
            [
                'base_damage' => $this->base_damage,
                'bonus_accuracy' => $this->bonus_accuracy,
                'bonus_speed' => $this->bonus_speed,
            ] as $field => $value
        ) {
            if ($value < 0) {
                $errors[$field][] = "$field não pode ser negativo.";
            }
        }

        if ($this->ammo_per_use <= 0) {
            $errors['ammo_per_use'][] = 'ammo_per_use deve ser maior que zero.';
        }

        if (empty($this->element_types)) {
            $errors['element_types'][] = 'Toda arma deve possuir ao menos um element_type.';
        }

        if (count($this->element_types) !== count(array_unique($this->element_types))) {
            $errors['element_types'][] = 'element_types contém IDs duplicados.';
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
}
