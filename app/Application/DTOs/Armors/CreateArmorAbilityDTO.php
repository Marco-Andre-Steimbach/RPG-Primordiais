<?php

namespace App\Application\DTOs\Armors;

use App\Core\Exceptions\ValidationException;

class CreateArmorAbilityDTO
{
    public string $title;
    public string $description;

    public ?string $dice_formula;

    public int $range;

    public int $base_damage;
    public int $armor_class_bonus;
    public int $bonus_speed;

    public function __construct(array $data)
    {
        $this->title = trim((string) ($data['title'] ?? ''));
        $this->description = trim((string) ($data['description'] ?? ''));

        $this->dice_formula = isset($data['dice_formula'])
            ? trim((string) $data['dice_formula'])
            : null;

        $this->base_damage = (int) ($data['base_damage'] ?? 0);
        $this->range = (int) ($data['range'] ?? 1);
        $this->armor_class_bonus = (int) ($data['armor_class_bonus'] ?? 0);
        $this->bonus_speed = (int) ($data['bonus_speed'] ?? 0);

        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if ($this->title === '') {
            $errors['title'][] = 'Título é obrigatório.';
        }

        if ($this->description === '') {
            $errors['description'][] = 'Descrição é obrigatória.';
        }

        if ($this->base_damage < 0) {
            $errors['base_damage'][] = 'base_damage não pode ser negativo.';
        }

        if ($this->range <= 0) {
            $errors['range'][] = 'range deve ser maior que zero.';
        }        

        if ($this->armor_class_bonus < 0) {
            $errors['armor_class_bonus'][] = 'armor_class_bonus não pode ser negativo.';
        }

        if ($errors) {
            throw new ValidationException('Dados inválidos.', $errors);
        }
    }
}
