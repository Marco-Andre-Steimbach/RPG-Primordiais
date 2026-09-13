<?php

namespace App\Application\DTOs\Abilities;

use App\Core\Exceptions\ValidationException;

class CreateAbilityDTO
{
    public int $character_id;

    public string $title;
    public string $description;

    public ?string $arcane_title;
    public ?string $arcane_description;

    public int $mana_cost;
    public ?int $arcane_mana_cost;

    public array $normal_element_types;
    public array $arcane_element_types;

    public ?int $required_race_id;
    public ?int $required_order_id;

    public function __construct(array $data)
    {
        $this->character_id =
            (int) ($data['character_id'] ?? 0);

        $this->title =
            trim((string) ($data['title'] ?? ''));

        $this->description =
            trim((string) ($data['description'] ?? ''));

        $this->arcane_title =
            $this->normalizeNullableString(
                $data['arcane_title'] ?? null
            );

        $this->arcane_description =
            $this->normalizeNullableString(
                $data['arcane_description'] ?? null
            );

        $this->mana_cost =
            (int) ($data['mana_cost'] ?? 0);

        $this->arcane_mana_cost =
            isset($data['arcane_mana_cost'])
                && $data['arcane_mana_cost'] !== ''
                    ? (int) $data['arcane_mana_cost']
                    : null;

        $this->normal_element_types =
            $this->normalizeElementTypes(
                $data['normal_element_types'] ?? []
            );

        $this->arcane_element_types =
            $this->normalizeElementTypes(
                $data['arcane_element_types'] ?? []
            );

        $this->required_race_id =
            isset($data['required_race_id'])
                && $data['required_race_id'] !== ''
                    ? (int) $data['required_race_id']
                    : null;

        $this->required_order_id =
            isset($data['required_order_id'])
                && $data['required_order_id'] !== ''
                    ? (int) $data['required_order_id']
                    : null;

        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if ($this->character_id <= 0) {
            $errors['character_id'][] =
                'Personagem é obrigatório.';
        }

        if ($this->title === '') {
            $errors['title'][] =
                'Título da habilidade é obrigatório.';
        }

        if ($this->description === '') {
            $errors['description'][] =
                'Descrição da habilidade é obrigatória.';
        }

        if ($this->mana_cost < 0) {
            $errors['mana_cost'][] =
                'mana_cost não pode ser negativo.';
        }

        if (empty($this->normal_element_types)) {
            $errors['normal_element_types'][] =
                'A habilidade deve possuir ao menos um tipo elemental.';
        }

        $hasArcaneData =
            $this->arcane_title !== null
            || $this->arcane_description !== null
            || $this->arcane_mana_cost !== null
            || !empty($this->arcane_element_types);

        if ($hasArcaneData) {
            if ($this->arcane_title === null) {
                $errors['arcane_title'][] =
                    'Título da Queima Arcana é obrigatório.';
            }

            if ($this->arcane_description === null) {
                $errors['arcane_description'][] =
                    'Descrição da Queima Arcana é obrigatória.';
            }

            if ($this->arcane_mana_cost === null) {
                $errors['arcane_mana_cost'][] =
                    'Mana da Queima Arcana é obrigatória.';
            } elseif ($this->arcane_mana_cost < 0) {
                $errors['arcane_mana_cost'][] =
                    'arcane_mana_cost não pode ser negativo.';
            }

            if (empty($this->arcane_element_types)) {
                $errors['arcane_element_types'][] =
                    'A Queima Arcana deve possuir ao menos um tipo elemental.';
            }
        }

        if (
            $this->required_race_id !== null
            && $this->required_race_id <= 0
        ) {
            $errors['required_race_id'][] =
                'required_race_id inválido.';
        }

        if (
            $this->required_order_id !== null
            && $this->required_order_id <= 0
        ) {
            $errors['required_order_id'][] =
                'required_order_id inválido.';
        }

        if ($errors) {
            throw new ValidationException(
                'Dados inválidos.',
                $errors
            );
        }
    }

    private function normalizeElementTypes(
        mixed $value
    ): array {
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

    private function normalizeNullableString(
        mixed $value
    ): ?string {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === ''
            ? null
            : $value;
    }
}
