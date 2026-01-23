<?php

namespace App\Application\DTOs\Elements;

use App\Core\Exceptions\ValidationException;

class GetElementAttackRelationsDTO
{
    public array $attack_elements;

    public function __construct(array $data)
    {
        $this->attack_elements = $this->normalizeElements(
            $data['attack_elements'] ?? []
        );

        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if (empty($this->attack_elements)) {
            $errors['attack_elements'][] = 'attack_elements é obrigatório e não pode ser vazio.';
        }

        if (count(array_unique($this->attack_elements)) !== count($this->attack_elements)) {
            $errors['attack_elements'][] = 'attack_elements contém IDs duplicados.';
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
}
