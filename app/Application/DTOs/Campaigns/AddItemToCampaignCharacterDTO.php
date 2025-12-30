<?php

namespace App\Application\DTOs\Campaigns;

use App\Core\Exceptions\ValidationException;

class AddItemToCampaignCharacterDTO
{
    public int $item_id;
    public int $quantity;

    public function __construct(array $data)
    {
        $this->item_id  = (int) ($data['item_id'] ?? 0);
        $this->quantity = (int) ($data['quantity'] ?? 1);

        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if ($this->item_id <= 0) {
            $errors['item_id'][] = 'item_id inválido.';
        }

        if ($this->quantity <= 0) {
            $errors['quantity'][] = 'Quantidade deve ser maior que zero.';
        }

        if ($errors) {
            throw new ValidationException('Dados inválidos.', $errors);
        }
    }
}
