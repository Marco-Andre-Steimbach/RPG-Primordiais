<?php

namespace App\Application\DTOs\Monsters;

use App\Core\Exceptions\ValidationException;

class LinkMonsterAttacksDTO
{
    public int $monster_id;
    public array $attack_ids;

    public function __construct(int $monsterId, array $data)
    {
        $this->monster_id = $monsterId;
        $this->attack_ids = $data['attack_ids'] ?? [];

        $this->validate();
    }

    private function validate(): void
    {
        if ($this->monster_id <= 0) {
            throw new ValidationException('ID do monstro inválido.');
        }

        if (!is_array($this->attack_ids) || empty($this->attack_ids)) {
            throw new ValidationException('attack_ids deve ser um array não vazio.');
        }

        foreach ($this->attack_ids as $attackId) {
            if (!is_int($attackId) || $attackId <= 0) {
                throw new ValidationException('attack_ids deve conter apenas IDs válidos.');
            }
        }
    }
}
