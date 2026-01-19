<?php

namespace App\Application\DTOs\Weapons;

use App\Core\Exceptions\ValidationException;

class CreateWeaponAbilityDTO
{
    public string $title;
    public string $description;

    public ?string $dice_formula;
    public int $range;

    public int $base_damage;
    public int $bonus_damage;
    public int $bonus_accuracy;
    public int $bonus_speed;

    public array $element_types;

    public function __construct(array $data)
    {
        $this->title = trim((string) ($data['title'] ?? ''));
        $this->description = trim((string) ($data['description'] ?? ''));
        $this->range = (int) ($data['range'] ?? 1);

        $this->dice_formula = isset($data['dice_formula'])
            ? trim((string) $data['dice_formula'])
            : null;
        
        $this->base_damage = (int) ($data['base_damage'] ?? 0);
        $this->bonus_damage = (int) ($data['bonus_damage'] ?? 0);
        $this->bonus_accuracy = (int) ($data['bonus_accuracy'] ?? 0);
        $this->bonus_speed = (int) ($data['bonus_speed'] ?? 0);

        $this->element_types = $this->normalizeElementTypes($data['element_types'] ?? []);

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

        if (empty($this->element_types)) {
            $errors['element_types'][] = 'Toda habilidade de arma deve possuir ao menos um element_type.';
        }

        if (count($this->element_types) !== count(array_unique($this->element_types))) {
            $errors['element_types'][] = 'element_types contém IDs duplicados.';
        }
        
        if ($this->range <= 0) {
            $errors['range'][] = 'range deve ser maior que zero.';
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
