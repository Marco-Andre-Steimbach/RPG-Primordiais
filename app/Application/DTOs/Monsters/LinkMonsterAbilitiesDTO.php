<?php

namespace App\Application\DTOs\Monsters;

use App\Core\Exceptions\ValidationException;

class LinkMonsterAbilitiesDTO
{
    public int $monster_id;
    public array $ability_ids;

    public function __construct(int $monsterId, array $data)
    {
        $this->monster_id  = $monsterId;
        $this->ability_ids = $data['ability_ids'] ?? [];

        $this->validate();
    }

    private function validate(): void
    {
        if ($this->monster_id <= 0) {
            throw new ValidationException('ID do monstro inválido.');
        }

        if (!is_array($this->ability_ids) || empty($this->ability_ids)) {
            throw new ValidationException('ability_ids deve ser um array não vazio.');
        }

        foreach ($this->ability_ids as $abilityId) {
            if (!is_int($abilityId) || $abilityId <= 0) {
                throw new ValidationException('ability_ids deve conter apenas IDs válidos.');
            }
        }
    }
}
