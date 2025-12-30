<?php

namespace App\Application\DTOs\Characters;

use App\Core\Exceptions\ValidationException;

class CreateCharacterDTO
{
    public string $name;
    public ?string $description;

    public ?int $race_id;
    public ?int $order_id;

    public string $mana_modifier;

    public function __construct(array $data)
    {
        $this->name = trim((string) ($data['name'] ?? ''));
        $this->description = isset($data['description'])
            ? trim((string) $data['description'])
            : null;

        $this->race_id = isset($data['race_id']) ? (int) $data['race_id'] : null;
        $this->order_id = isset($data['order_id']) ? (int) $data['order_id'] : null;

        $this->mana_modifier = strtolower(trim((string) ($data['mana_modifier'] ?? '')));

        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if ($this->name === '') {
            $errors['name'][] = 'Nome do personagem é obrigatório.';
        }

        if ($this->mana_modifier === '') {
            $errors['mana_modifier'][] = 'Modificador de mana é obrigatório.';
        }

        if (!in_array($this->mana_modifier, ['str', 'dex', 'con', 'int', 'wis', 'cha'], true)) {
            $errors['mana_modifier'][] = 'Modificador de mana inválido.';
        }

        if ($this->race_id !== null && $this->race_id <= 0) {
            $errors['race_id'][] = 'race_id inválido.';
        }

        if ($this->order_id !== null && $this->order_id <= 0) {
            $errors['order_id'][] = 'order_id inválido.';
        }

        if ($errors) {
            throw new ValidationException('Dados inválidos.', $errors);
        }
    }
}
