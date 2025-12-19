<?php

namespace App\Domain\Services\Monsters;

use App\Application\DTOs\Monsters\CreateMonsterDTO;
use App\Core\Exceptions\ConflictException;
use App\Core\Exceptions\ValidationException;
use App\Domain\Models\Monster;
use App\Infrastructure\Repositories\MonsterRepository;
use App\Infrastructure\Repositories\MonsterElementTypeRepository;

class CreateMonsterService
{
    private MonsterRepository $monsters;
    private MonsterElementTypeRepository $elements;

    public function __construct()
    {
        $this->monsters = new MonsterRepository();
        $this->elements = new MonsterElementTypeRepository();
    }

    public function execute(CreateMonsterDTO $dto): Monster
    {
        if ($this->monsters->existsByName($dto->name)) {
            throw new ConflictException('Já existe um monstro com esse nome.');
        }

        $monsterId = $this->monsters->create([
            'name' => $dto->name,
            'description' => $dto->description,

            'base_hp' => $dto->base_hp,
            'base_ac' => $dto->base_ac,
            'base_speed' => $dto->base_speed,

            'actions_per_turn' => $dto->actions_per_turn,

            'base_str' => $dto->base_str,
            'base_dex' => $dto->base_dex,
            'base_con' => $dto->base_con,
            'base_wis' => $dto->base_wis,
            'base_int' => $dto->base_int,
        ]);

        if (!$monsterId) {
            throw new ValidationException('Falha ao criar monstro.');
        }

        foreach ($dto->element_types as $elementTypeId) {
            $this->elements->attach($monsterId, $elementTypeId);
        }

        $monster = $this->monsters->findById($monsterId);

        if (!$monster) {
            throw new ValidationException('Erro ao carregar monstro criado.');
        }

        $monster->element_types = $dto->element_types;

        return $monster;
    }
}
