<?php

namespace App\Application\DTOs\Campaigns;

use App\Core\Exceptions\ValidationException;

class ChangeCharacterXPAmountDTO
{
    public int $amount;
    public string $operation;

    public function __construct(array $data)
    {
        $this->amount = (int) ($data['amount'] ?? 0);
        $this->operation = trim((string) ($data['operation'] ?? ''));

        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

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
