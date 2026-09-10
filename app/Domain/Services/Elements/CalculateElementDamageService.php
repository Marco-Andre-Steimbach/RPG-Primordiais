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

        $modifier = round($modifier, 10);

        if (abs($modifier) < 0.000000001) {
            $modifier = 0.0;
        }

        $multiplier = 1 + $modifier;

        if ($multiplier < 0) {
            $multiplier = 0;
        }

        $finalDamage = (int) round($dto->base_damage * $multiplier);

        $damageZeroed = false;
        $zeroDamagePercentage = null;

        if ($finalDamage <= 0) {
            $damageZeroed = true;
            $zeroDamagePercentage = random_int(1, 10);

            $finalDamage = max(
                1,
                (int) round($dto->base_damage * ($zeroDamagePercentage / 100))
            );
        }

        return [
            'base_damage' => $dto->base_damage,
            'final_damage' => $finalDamage,
            'multiplier' => $multiplier,
            'modifier' => $modifier,
            'damage_zeroed' => $damageZeroed,
            'zero_damage_percentage' => $zeroDamagePercentage,
        ];
    }
}
