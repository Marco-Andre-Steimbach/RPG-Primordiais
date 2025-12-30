<?php

namespace App\Domain\Services\Armors;

use App\Core\Exceptions\ValidationException;
use App\Domain\Models\Armor;
use App\Infrastructure\Repositories\ArmorRepository;
use App\Infrastructure\Repositories\ArmorElementTypeRepository;
use App\Infrastructure\Repositories\ArmorArmorAbilityRepository;
use App\Infrastructure\Repositories\ArmorAbilityRepository;

class GetArmorByIdService
{
    private ArmorRepository $armors;
    private ArmorElementTypeRepository $elements;
    private ArmorArmorAbilityRepository $links;
    private ArmorAbilityRepository $abilities;

    public function __construct()
    {
        $this->armors = new ArmorRepository();
        $this->elements = new ArmorElementTypeRepository();
        $this->links = new ArmorArmorAbilityRepository();
        $this->abilities = new ArmorAbilityRepository();
    }

    public function execute(int $armorId): Armor
    {
        $armor = $this->armors->findById($armorId);

        if (!$armor) {
            throw new ValidationException('Armadura não encontrada.');
        }

        $armor->element_types = $this->elements->getByArmorId($armorId);

        $abilityIds = $this->links->getByArmorId($armorId);

        $armor->armor_abilities = [];

        foreach ($abilityIds as $abilityId) {
            $ability = $this->abilities->findById($abilityId);
            if ($ability) {
                $armor->armor_abilities[] = $ability;
            }
        }

        return $armor;
    }
}

