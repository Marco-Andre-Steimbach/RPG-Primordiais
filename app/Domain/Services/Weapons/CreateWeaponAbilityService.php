<?php

namespace App\Domain\Services\Weapons;

use App\Application\DTOs\Weapons\CreateWeaponAbilityDTO;
use App\Core\Exceptions\ValidationException;
use App\Domain\Models\WeaponAbility;
use App\Infrastructure\Repositories\WeaponAbilityRepository;
use App\Infrastructure\Repositories\WeaponAbilityElementTypeRepository;
use App\Infrastructure\Repositories\WeaponRepository;

class CreateWeaponAbilityService
{
    private WeaponAbilityRepository $abilities;
    private WeaponAbilityElementTypeRepository $elements;
    private WeaponRepository $weapons;

    public function __construct()
    {
        $this->abilities = new WeaponAbilityRepository();
        $this->elements  = new WeaponAbilityElementTypeRepository();
        $this->weapons   = new WeaponRepository();
    }

    public function execute(int $weaponId, CreateWeaponAbilityDTO $dto): WeaponAbility
    {
        if (!$this->weapons->findById($weaponId)) {
            throw new ValidationException(
                'Arma inválida.',
                ['weapon_id' => ['Arma não encontrada.']]
            );
        }

        $abilityId = $this->abilities->create([
            'weapon_id' => $weaponId,
            'title' => $dto->title,
            'description' => $dto->description,
            'dice_formula' => $dto->dice_formula,
            'range' => $dto->range,
            'base_damage' => $dto->base_damage,
            'bonus_damage' => $dto->bonus_damage,
            'bonus_accuracy' => $dto->bonus_accuracy,
            'bonus_speed' => $dto->bonus_speed,
        ]);

        if (!$abilityId) {
            throw new ValidationException('Falha ao criar habilidade de arma.');
        }

        foreach ($dto->element_types as $elementTypeId) {
            $this->elements->attach($abilityId, $elementTypeId);
        }

        $ability = $this->abilities->findById($abilityId);

        if (!$ability) {
            throw new ValidationException('Erro ao carregar habilidade criada.');
        }

        $ability->element_types = $dto->element_types;

        return $ability;
    }
}
