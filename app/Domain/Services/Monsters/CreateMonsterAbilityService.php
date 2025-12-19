<?php

namespace App\Domain\Services\Monsters;

use App\Application\DTOs\Monsters\CreateMonsterAbilityDTO;
use App\Core\Exceptions\ConflictException;
use App\Core\Exceptions\ValidationException;
use App\Domain\Models\MonsterAbility;
use App\Infrastructure\Repositories\MonsterAbilityRepository;
use App\Infrastructure\Repositories\MonsterAbilityElementTypeRepository;

class CreateMonsterAbilityService
{
    private MonsterAbilityRepository $abilities;
    private MonsterAbilityElementTypeRepository $elements;

    public function __construct()
    {
        $this->abilities = new MonsterAbilityRepository();
        $this->elements = new MonsterAbilityElementTypeRepository();
    }

    public function execute(CreateMonsterAbilityDTO $dto): MonsterAbility
    {
        if ($this->abilities->existsByTitle($dto->title)) {
            throw new ConflictException('Já existe uma habilidade de monstro com esse título.');
        }

        $abilityId = $this->abilities->create([
            'title' => $dto->title,
            'description' => $dto->description,
            'dice_formula' => $dto->dice_formula,
            'base_damage' => $dto->base_damage,
            'bonus_damage' => $dto->bonus_damage,
            'bonus_speed' => $dto->bonus_speed,
        ]);

        if (!$abilityId) {
            throw new ValidationException('Falha ao criar habilidade de monstro.');
        }

        foreach ($dto->element_types as $elementTypeId) {
            $this->elements->attach(
                $abilityId,
                $elementTypeId
            );
        }

        $ability = $this->abilities->findById($abilityId);

        if (!$ability) {
            throw new ValidationException('Erro ao carregar habilidade criada.');
        }

        $ability->element_types = $dto->element_types;

        return $ability;
    }
}
