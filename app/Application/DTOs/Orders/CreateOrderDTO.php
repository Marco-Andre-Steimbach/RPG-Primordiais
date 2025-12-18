<?php

namespace App\Application\DTOs\Orders;

use App\Core\Exceptions\ValidationException;

class CreateOrderDTO
{
    public string $name;
    public ?string $description;
    public ?int $required_race_id;
    public array $attributes;

    public function __construct(array $data)
    {
        $this->name = trim($data['name'] ?? '');
        $this->description = $data['description'] ?? null;
        $this->required_race_id = isset($data['required_race_id'])
            ? (int) $data['required_race_id']
            : null;

        $this->attributes = $data['attributes'] ?? [];

        $this->validate();
    }

    private function validate(): void
    {
        if ($this->name === '') {
            throw new ValidationException('Nome da ordem é obrigatório.');
        }

        foreach ($this->attributes as $attribute) {
            if (
                empty($attribute['name'])
                || !isset($attribute['value'])
            ) {
                throw new ValidationException(
                    'Cada atributo deve possuir name e value.'
                );
            }
        }
    }
}
