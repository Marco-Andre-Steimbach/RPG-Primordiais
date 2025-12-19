<?php

namespace App\Domain\Services\Monsters;

use App\Application\DTOs\Monsters\CreateMonsterAttackDTO;
use App\Core\Exceptions\ConflictException;
use App\Core\Exceptions\ValidationException;
use App\Domain\Models\MonsterAttack;
use App\Infrastructure\Repositories\MonsterAttackRepository;
use App\Infrastructure\Repositories\MonsterAttackElementTypeRepository;

class CreateMonsterAttackService
{
    private MonsterAttackRepository $attacks;
    private MonsterAttackElementTypeRepository $elements;

    public function __construct()
    {
        $this->attacks = new MonsterAttackRepository();
        $this->elements = new MonsterAttackElementTypeRepository();
    }

    public function execute(CreateMonsterAttackDTO $dto): MonsterAttack
    {
        if ($this->attacks->existsByName($dto->name)) {
            throw new ConflictException('Já existe um ataque de monstro com esse nome.');
        }

        $attackId = $this->attacks->create([
            'name' => $dto->name,
            'description' => $dto->description,
            'dice_formula' => $dto->dice_formula,
            'base_damage' => $dto->base_damage,
            'bonus_accuracy' => $dto->bonus_accuracy,
        ]);

        if (!$attackId) {
            throw new ValidationException('Falha ao criar ataque de monstro.');
        }

        foreach ($dto->element_types as $elementTypeId) {
            $this->elements->attach(
                $attackId,
                $elementTypeId
            );
        }

        $attack = $this->attacks->findById($attackId);

        if (!$attack) {
            throw new ValidationException('Erro ao carregar ataque criado.');
        }

        $attack->element_types = $dto->element_types;

        return $attack;
    }
}
