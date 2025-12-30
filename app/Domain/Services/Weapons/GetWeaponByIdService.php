<?php

namespace App\Domain\Services\Weapons;

use App\Core\Exceptions\ValidationException;
use App\Infrastructure\Repositories\WeaponRepository;
use App\Infrastructure\Repositories\WeaponElementTypeRepository;
use App\Infrastructure\Repositories\WeaponAbilityRepository;
use App\Infrastructure\Repositories\WeaponAbilityElementTypeRepository;

class GetWeaponByIdService
{
    private WeaponRepository $weapons;
    private WeaponElementTypeRepository $weaponElements;
    private WeaponAbilityRepository $abilities;
    private WeaponAbilityElementTypeRepository $abilityElements;

    public function __construct()
    {
        $this->weapons         = new WeaponRepository();
        $this->weaponElements  = new WeaponElementTypeRepository();
        $this->abilities       = new WeaponAbilityRepository();
        $this->abilityElements = new WeaponAbilityElementTypeRepository();
    }

    public function execute(int $weaponId): array
    {
        $weapon = $this->weapons->findByIdWithItemAndDamageType($weaponId);

        if (!$weapon) {
            throw new ValidationException(
                'Arma não encontrada.',
                ['weapon_id' => ['Arma inválida.']]
            );
        }

        $weapon['element_types'] =
            $this->weaponElements->getByWeaponId($weaponId);

        $abilities = $this->abilities->findByWeaponId($weaponId);

        foreach ($abilities as &$ability) {
            $ability->element_types =
                $this->abilityElements->getByWeaponAbilityId($ability->id);

            $ability = $ability->toArray();
        }

        $weapon['abilities'] = $abilities;

        return $weapon;
    }
}
