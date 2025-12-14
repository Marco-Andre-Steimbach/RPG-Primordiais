<?php

namespace App\Application\DTOs\Races;

use App\Core\Exceptions\ValidationException;

class RaceCreateDTO
{
    public string $name;
    public ?string $description;
    public array $attributes;

    public function __construct(array $data)
    {
        $this->name = trim($data['name'] ?? '');
        $this->description = $data['description'] ?? null;
        $this->attributes = $data['attributes'] ?? [];

        $this->validate();
    }

    private function validate(): void
    {
        if ($this->name === '') {
            throw new ValidationException('Nome da raça é obrigatório.');
        }

        if (empty($this->attributes)) {
            throw new ValidationException('Atributos da raça são obrigatórios.');
        }
    }
}
