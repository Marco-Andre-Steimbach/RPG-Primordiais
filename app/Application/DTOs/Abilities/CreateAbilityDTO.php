<?php

namespace App\Application\DTOs\Abilities;

use App\Core\Exceptions\ValidationException;

class CreateAbilityDTO
{
    public string $title;
    public string $description;

    public ?string $arcane_title;
    public ?string $arcane_description;

    public int $mana_cost;
    public ?int $arcane_mana_cost;

    public ?string $dice_formula;
    public int $base_damage;
    public int $bonus_speed;

    public array $element_types;

    public ?int $required_race_id;
    public ?int $required_order_id;

    public function __construct(array $data)
    {
        $this->title = trim((string) ($data['title'] ?? ''));
        $this->description = trim((string) ($data['description'] ?? ''));

        $this->arcane_title = isset($data['arcane_title'])
            ? trim((string) $data['arcane_title'])
            : null;

        $this->arcane_description = isset($data['arcane_description'])
            ? trim((string) $data['arcane_description'])
            : null;

        $this->mana_cost = (int) ($data['mana_cost'] ?? 0);
        $this->arcane_mana_cost = isset($data['arcane_mana_cost'])
            ? (int) $data['arcane_mana_cost']
            : null;

        $this->dice_formula = isset($data['dice_formula'])
            ? trim((string) $data['dice_formula'])
            : null;

        $this->base_damage = (int) ($data['base_damage'] ?? 0);
        $this->bonus_speed = (int) ($data['bonus_speed'] ?? 0);

        $this->element_types = $this->normalizeElementTypes(
            $data['element_types'] ?? []
        );

        $this->required_race_id = isset($data['required_race_id'])
            ? (int) $data['required_race_id']
            : null;

        $this->required_order_id = isset($data['required_order_id'])
            ? (int) $data['required_order_id']
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

        if ($this->mana_cost < 0) {
            $errors['mana_cost'][] = 'mana_cost não pode ser negativo.';
        }

        if ($this->arcane_mana_cost !== null) {
            if ($this->arcane_mana_cost <= $this->mana_cost) {
                $errors['arcane_mana_cost'][] =
                    'arcane_mana_cost deve ser maior que mana_cost.';
            }

            if ($this->arcane_title === '' || $this->arcane_description === '') {
                $errors['arcane'][] =
                    'Título e descrição arcana são obrigatórios quando arcane_mana_cost é informado.';
            }
        }

        if ($this->base_damage < 0) {
            $errors['base_damage'][] = 'base_damage não pode ser negativo.';
        }

        if ($this->bonus_speed < 0) {
            $errors['bonus_speed'][] = 'bonus_speed não pode ser negativo.';
        }

        if (empty($this->element_types)) {
            $errors['element_types'][] =
                'A habilidade deve possuir ao menos um tipo elemental.';
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
