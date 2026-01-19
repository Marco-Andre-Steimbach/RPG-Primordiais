<?php

namespace App\Domain\Services\Armors;

use App\Application\DTOs\Armors\CreateArmorAbilityDTO;
use App\Core\Exceptions\ValidationException;
use App\Domain\Models\ArmorAbility;
use App\Infrastructure\Repositories\ArmorAbilityRepository;

class CreateArmorAbilityService
{
    private ArmorAbilityRepository $abilities;

    public function __construct()
    {
        $this->abilities = new ArmorAbilityRepository();
    }

    public function execute(CreateArmorAbilityDTO $dto): ArmorAbility
    {
        $abilityId = $this->abilities->create([
            'title' => $dto->title,
            'description' => $dto->description,
            'dice_formula' => $dto->dice_formula,
            'base_damage' => $dto->base_damage,
            'armor_class_bonus' => $dto->armor_class_bonus,
            'bonus_speed' => $dto->bonus_speed,
            'range' => $dto->range,
        ]);        

        if (!$abilityId) {
            throw new ValidationException('Falha ao criar habilidade de armadura.');
        }

        $ability = $this->abilities->findById($abilityId);

        if (!$ability) {
            throw new ValidationException('Erro ao carregar habilidade criada.');
        }

        return $ability;
    }
}
