<?php

namespace App\Domain\Services\Armors;

use App\Infrastructure\Repositories\ArmorAbilityRepository;

class GetAllArmorAbilitiesService
{
    private ArmorAbilityRepository $abilities;

    public function __construct()
    {
        $this->abilities = new ArmorAbilityRepository();
    }

    public function execute(): array
    {
        return $this->abilities->findAll();
    }
}