<?php

namespace App\Domain\Services\Weapons;

use App\Infrastructure\Repositories\WeaponRepository;
use App\Infrastructure\Repositories\WeaponElementTypeRepository;

class GetAllWeaponsService
{
    private WeaponRepository $weapons;
    private WeaponElementTypeRepository $elements;

    public function __construct()
    {
        $this->weapons  = new WeaponRepository();
        $this->elements = new WeaponElementTypeRepository();
    }

    public function execute(): array
    {
        $weapons = $this->weapons->findAllWithItemAndDamageType();

        foreach ($weapons as &$weapon) {
            $weapon['element_types']
                = $this->elements->getByWeaponId((int) $weapon['id']);
        }

        return $weapons;
    }
}
