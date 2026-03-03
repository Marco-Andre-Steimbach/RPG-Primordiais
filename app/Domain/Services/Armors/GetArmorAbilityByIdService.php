<?php

namespace App\Domain\Services\Armors;

use App\Core\Exceptions\ValidationException;
use App\Domain\Models\ArmorAbility;
use App\Infrastructure\Repositories\ArmorAbilityRepository;

class GetArmorAbilityByIdService
{
    private ArmorAbilityRepository $abilities;

    public function __construct()
    {
        $this->abilities = new ArmorAbilityRepository();
    }

    public function execute(int $id): ArmorAbility
    {
        $ability = $this->abilities->findById($id);

        if (!$ability) {
            throw new ValidationException('Habilidade de armadura não encontrada.');
        }

        return $ability;
    }
}