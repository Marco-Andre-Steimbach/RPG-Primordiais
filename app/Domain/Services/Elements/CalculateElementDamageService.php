<?php

namespace App\Domain\Services\Elements;

use App\Application\DTOs\Elements\CalculateDamageDTO;
use App\Infrastructure\Repositories\ElementTypeRepository;

class CalculateElementDamageService
{
    private ElementTypeRepository $elements;

    public function __construct()
    {
        $this->elements = new ElementTypeRepository();
    }

    public function execute(CalculateDamageDTO $dto): array
    {
        $relations = $this->elements->getRelationsByIds(
            $dto->attack_elements,
            $dto->defense_elements
        );

        $advantages = 0;
        $disadvantages = 0;
        $hasImmunity = false;

        foreach ($relations as $relation) {
            if ($relation['relation_type'] === 'immunity') {
                $hasImmunity = true;
                break;
            }

            if ($relation['relation_type'] === 'strength') {
                $advantages++;
            }

            if ($relation['relation_type'] === 'weakness') {
                $disadvantages++;
            }
        }

        if ($hasImmunity) {
            return [
                'base_damage' => $dto->base_damage,
                'final_damage' => 0,
                'multiplier' => 0,
                'advantages' => $advantages,
                'disadvantages' => $disadvantages,
                'immunity' => true,
            ];
        }

        $multiplier = 1 + (0.25 * $advantages) - (0.25 * $disadvantages);

        if ($multiplier < 0) {
            $multiplier = 0;
        }

        $finalDamage = (int) round($dto->base_damage * $multiplier);

        return [
            'base_damage' => $dto->base_damage,
            'final_damage' => $finalDamage,
            'multiplier' => $multiplier,
            'advantages' => $advantages,
            'disadvantages' => $disadvantages,
            'immunity' => false,
        ];
    }
}
