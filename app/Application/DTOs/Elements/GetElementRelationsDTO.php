<?php

namespace App\Application\DTOs\Elements;

use App\Core\Exceptions\ValidationException;

class GetElementRelationsDTO
{
    public array $defense_elements;

    public function __construct(array $data)
    {
        $this->defense_elements = $this->normalizeElements(
            $data['defense_elements'] ?? []
        );

        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if (empty($this->defense_elements)) {
            $errors['defense_elements'][] = 'defense_elements é obrigatório e não pode ser vazio.';
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
}
