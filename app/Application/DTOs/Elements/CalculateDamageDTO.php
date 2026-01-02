<?php

namespace App\Application\DTOs\Elements;

use App\Core\Exceptions\ValidationException;

class CalculateDamageDTO
{
    public array $attack_elements;
    public array $defense_elements;
    public float $base_damage;

    public function __construct(array $data)
    {
        $this->attack_elements = $this->normalizeElements($data['attack_elements'] ?? []);
        $this->defense_elements = $this->normalizeElements($data['defense_elements'] ?? []);
        $this->base_damage = $this->normalizeDamage($data['base_damage'] ?? null);

        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if (empty($this->attack_elements)) {
            $errors['attack_elements'][] = 'attack_elements é obrigatório e não pode ser vazio.';
        }

        if (empty($this->defense_elements)) {
            $errors['defense_elements'][] = 'defense_elements é obrigatório e não pode ser vazio.';
        }

        if ($this->base_damage < 0) {
            $errors['base_damage'][] = 'base_damage não pode ser negativo.';
        }

        if (count(array_unique($this->attack_elements)) !== count($this->attack_elements)) {
            $errors['attack_elements'][] = 'attack_elements contém IDs duplicados.';
        }

        if (count(array_unique($this->defense_elements)) !== count($this->defense_elements)) {
            $errors['defense_elements'][] = 'defense_elements contém IDs duplicados.';
        }

        if ($errors) {
            throw new ValidationException('Dados inválidos.', $errors);
        }
    }

    private function normalizeElements(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $elements = [];

        foreach ($value as $id) {
            $intId = (int) $id;
            if ($intId > 0) {
                $elements[] = $intId;
            }
        }

        return array_values(array_unique($elements));
    }

    private function normalizeDamage(mixed $value): float
    {
        if (!is_numeric($value)) {
            return 0;
        }

        return (float) $value;
    }
}
