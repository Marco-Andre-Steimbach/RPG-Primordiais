<?php

namespace App\Application\DTOs\Monsters;

use App\Core\Exceptions\ValidationException;

class CreateMonsterAbilityDTO
{
    public string $title;
    public string $description;

    public ?string $dice_formula;
    public int $base_damage;
    public int $bonus_damage;
    public int $bonus_speed;

    public array $element_types;

    public function __construct(array $data)
    {
        $this->title = trim((string) ($data['title'] ?? ''));
        $this->description = trim((string) ($data['description'] ?? ''));

        $this->dice_formula = isset($data['dice_formula'])
            ? trim((string) $data['dice_formula'])
            : null;

        $this->base_damage = (int) ($data['base_damage'] ?? 0);
        $this->bonus_damage = (int) ($data['bonus_damage'] ?? 0);
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

        if ($this->dice_formula !== null && $this->dice_formula === '') {
            $errors['dice_formula'][] = 'dice_formula inválida.';
        }

        foreach ([
            'base_damage' => $this->base_damage,
            'bonus_damage' => $this->bonus_damage,
            'bonus_speed' => $this->bonus_speed,
        ] as $field => $value) {
            if ($value < 0) {
                $errors[$field][] = "$field não pode ser negativo.";
            }
        }

        if (!empty($this->element_types)) {
            if (count($this->element_types) !== count(array_unique($this->element_types))) {
                $errors['element_types'][] = 'element_types contém IDs duplicados.';
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
}
