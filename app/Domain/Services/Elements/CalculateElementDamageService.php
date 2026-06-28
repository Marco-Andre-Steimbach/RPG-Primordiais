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
        $modifier = 0;

        foreach ($dto->attack_elements as $attackId) {
            foreach ($dto->defense_elements as $defenseId) {
                $relations = $this->elements->getRelationsByIds(
                    [$attackId],
                    [$defenseId]
                );

                foreach ($relations as $rel) {
                    $relationModifier = (float) $rel['modifier'];

                    if ($rel['relation_type'] === 'strong') {
                        $modifier += $relationModifier;
                    }

                    if ($rel['relation_type'] === 'weak') {
                        $modifier -= $relationModifier;
                    }
                }
            }
        }

        $multiplier = 1 + $modifier;

        if ($multiplier < 0) {
            $multiplier = 0;
        }

        return [
            'base_damage' => $dto->base_damage,
            'final_damage' => (int) round($dto->base_damage * $multiplier),
            'multiplier' => $multiplier,
            'modifier' => $modifier,
        ];
    }
}