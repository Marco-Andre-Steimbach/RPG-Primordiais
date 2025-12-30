<?php

namespace App\Application\DTOs\Items;

use App\Core\Exceptions\ValidationException;

class CreateItemAbilityDTO
{
    public string $title;
    public string $description;

    public ?string $dice_formula;

    public int $base_damage;
    public int $bonus_damage;
    public int $bonus_accuracy;
    public int $bonus_speed;

    public bool $is_consumable;
    public ?int $max_uses;

    public ?int $override_element_type_id;

    public function __construct(array $data)
    {
        $this->title = trim((string) ($data['title'] ?? ''));
        $this->description = trim((string) ($data['description'] ?? ''));

        $this->dice_formula = isset($data['dice_formula'])
            ? trim((string) $data['dice_formula'])
            : null;

        $this->base_damage = (int) ($data['base_damage'] ?? 0);
        $this->bonus_damage = (int) ($data['bonus_damage'] ?? 0);
        $this->bonus_accuracy = (int) ($data['bonus_accuracy'] ?? 0);
        $this->bonus_speed = (int) ($data['bonus_speed'] ?? 0);

        $this->is_consumable = (bool) ($data['is_consumable'] ?? false);
        $this->max_uses = isset($data['max_uses']) ? (int) $data['max_uses'] : null;

        $this->override_element_type_id = isset($data['override_element_type_id'])
            ? (int) $data['override_element_type_id']
            : null;

        $this->validate();
    }

private function validate(): void
{
    $errors = [];

    if ($this->title === '') {
        $errors['title'][] = 'Título da habilidade é obrigatório.';
    }

    if ($this->description === '') {
        $errors['description'][] = 'Descrição da habilidade é obrigatória.';
    }

    if ($this->base_damage < 0) {
        $errors['base_damage'][] = 'base_damage não pode ser negativo.';
    }

    if ($this->is_consumable && ($this->max_uses === null || $this->max_uses <= 0)) {
        $errors['max_uses'][] = 'max_uses é obrigatório para habilidades consumíveis.';
    }

    if (!$this->is_consumable && $this->max_uses !== null) {
        $errors['max_uses'][] = 'max_uses só pode ser usado se is_consumable for true.';
    }

    if ($errors) {
        throw new ValidationException('Dados inválidos.', $errors);
    }
}

}
