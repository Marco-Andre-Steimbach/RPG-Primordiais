<?php

namespace App\Application\DTOs\Campaigns;

use App\Core\Exceptions\ValidationException;

class ChangeCharacterGoldAmountDTO
{
    public int $campaign_character_id;
    public int $amount;
    public string $operation;

    public function __construct(array $data)
    {
        $this->campaign_character_id = (int) ($data['campaign_character_id'] ?? 0);
        $this->amount = (int) ($data['amount'] ?? 0);
        $this->operation = trim((string) ($data['operation'] ?? ''));

        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if ($this->campaign_character_id <= 0) {
            $errors['campaign_character_id'][] = 'ID inválido.';
        }

        if ($this->amount <= 0) {
            $errors['amount'][] = 'amount deve ser maior que zero.';
        }

        if (!in_array($this->operation, ['add', 'remove'], true)) {
            $errors['operation'][] = 'operation inválida. Use add ou remove.';
        }

        if ($errors) {
            throw new ValidationException('Dados inválidos.', $errors);
        }
    }
}
