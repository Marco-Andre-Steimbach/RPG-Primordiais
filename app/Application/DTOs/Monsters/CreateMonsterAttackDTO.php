<?php

namespace App\Application\DTOs\Monsters;

use App\Core\Exceptions\ValidationException;

class CreateMonsterAttackDTO
{
    public string $name;
    public ?string $description;

    public string $dice_formula;
    public int $base_damage;
    public int $bonus_accuracy;

    public array $element_types;

    public function __construct(array $data)
    {
        $this->name = trim((string) ($data['name'] ?? ''));
        $this->description = isset($data['description'])
            ? trim((string) $data['description'])
            : null;

        $this->dice_formula = trim((string) ($data['dice_formula'] ?? ''));
        $this->base_damage = (int) ($data['base_damage'] ?? 0);
        $this->bonus_accuracy = (int) ($data['bonus_accuracy'] ?? 0);

        $this->element_types = $this->normalizeElementTypes($data['element_types'] ?? []);

        $this->validate();
    }

    private function normalizeElementTypes(array $elementTypes): array
    {
        $normalized = [];

        foreach ($elementTypes as $id) {
            $id = (int) $id;
            if ($id > 0) {
                $normalized[] = $id;
            }
        }

        return array_values(array_unique($normalized));
    }

    private function validate(): void
    {
        $errors = [];

        if ($this->name === '') {
            $errors['name'][] = 'Nome do ataque é obrigatório.';
        }

        if ($this->dice_formula === '') {
            $errors['dice_formula'][] = 'dice_formula é obrigatória.';
        }

        if ($this->base_damage < 0) {
            $errors['base_damage'][] = 'base_damage não pode ser negativo.';
        }

        if ($this->bonus_accuracy < 0) {
            $errors['bonus_accuracy'][] = 'bonus_accuracy não pode ser negativo.';
        }

        if (
            !empty($this->element_types)
            && count($this->element_types) !== count(array_unique($this->element_types))
        ) {
            $errors['element_types'][] = 'element_types contém valores duplicados.';
        }

        if ($errors) {
            throw new ValidationException('Dados inválidos para criação de ataque de monstro.', $errors);
        }
    }
}
