<?php

namespace App\Domain\Services\Items;

use App\Application\DTOs\Items\CreateItemAbilityDTO;
use App\Core\Exceptions\ValidationException;
use App\Domain\Models\ItemAbility;
use App\Infrastructure\Repositories\ItemAbilityRepository;

class CreateItemAbilityService
{
    private ItemAbilityRepository $abilities;

    public function __construct()
    {
        $this->abilities = new ItemAbilityRepository();
    }

    public function execute(CreateItemAbilityDTO $dto): ItemAbility
    {
        $abilityId = $this->abilities->create([
            'title' => $dto->title,
            'description' => $dto->description,
            'dice_formula' => $dto->dice_formula,
            'base_damage' => $dto->base_damage,
            'bonus_damage' => $dto->bonus_damage,
            'bonus_accuracy' => $dto->bonus_accuracy,
            'bonus_speed' => $dto->bonus_speed,
            'range' => $dto->range,
            'is_consumable' => (int) $dto->is_consumable,
            'max_uses' => $dto->max_uses,
            'override_element_type_id' => $dto->override_element_type_id,
        ]);

        if (!$abilityId) {
            throw new ValidationException('Falha ao criar habilidade de item.');
        }

        $ability = $this->abilities->findById($abilityId);

        if (!$ability) {
            throw new ValidationException('Erro ao carregar habilidade criada.');
        }

        return $ability;
    }
}
