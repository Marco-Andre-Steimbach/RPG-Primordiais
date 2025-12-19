<?php

namespace App\Application\DTOs\Items;

use App\Core\Exceptions\ValidationException;

class CreateItemDTO
{
    public string $name;
    public ?string $description;
    public int $value;

    public array $element_types;
    public array $item_abilities;

    public function __construct(array $data)
    {
        $this->name = trim((string) ($data['name'] ?? ''));
        $this->description = isset($data['description'])
            ? trim((string) $data['description'])
            : null;

        $this->value = (int) ($data['value'] ?? 0);

        $this->element_types = $this->normalizeIds($data['element_types'] ?? []);
        $this->item_abilities = $this->normalizeIds($data['item_abilities'] ?? []);

        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if ($this->name === '') {
            $errors['name'][] = 'Nome do item é obrigatório.';
        }

        if ($this->value < 0) {
            $errors['value'][] = 'Valor do item não pode ser negativo.';
        }

        if (count($this->element_types) !== count(array_unique($this->element_types))) {
            $errors['element_types'][] = 'element_types contém IDs duplicados.';
        }

        if (count($this->item_abilities) !== count(array_unique($this->item_abilities))) {
            $errors['item_abilities'][] = 'item_abilities contém IDs duplicados.';
        }

        if ($errors) {
            throw new ValidationException('Dados inválidos.', $errors);
        }
    }

    private function normalizeIds(mixed $value): array
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
