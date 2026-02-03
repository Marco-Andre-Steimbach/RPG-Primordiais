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
        $advantages = 0;
        $disadvantages = 0;
        $hasImmunity = false;

        foreach ($dto->attack_elements as $attackId) {
            foreach ($dto->defense_elements as $defenseId) {

                // ATAQUE → DEFESA (ÚNICO lugar onde imunidade existe)
                $attackRelations = $this->elements->getRelationsByIds(
                    [$attackId],
                    [$defenseId]
                );

                foreach ($attackRelations as $rel) {
                    if ($rel['relation_type'] === 'immunity') {
                        $hasImmunity = true;
                        break 3;
                    }

                    if ($rel['relation_type'] === 'strength') {
                        $advantages++;
                    }

                    if ($rel['relation_type'] === 'weakness') {
                        $disadvantages++;
                    }
                }

                // DEFESA → ATAQUE (APENAS modificadores, NUNCA imunidade)
                $defenseRelations = $this->elements->getRelationsByIds(
                    [$defenseId],
                    [$attackId]
                );

                foreach ($defenseRelations as $rel) {
                    if ($rel['relation_type'] === 'weakness') {
                        $advantages++;
                    }

                    if ($rel['relation_type'] === 'strength') {
                        $disadvantages++;
                    }
                }
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

        $multiplier =
            1 +
            (0.25 * $advantages) -
            (0.25 * $disadvantages);

        if ($multiplier < 0) {
            $multiplier = 0;
        }

        return [
            'base_damage' => $dto->base_damage,
            'final_damage' => (int) round($dto->base_damage * $multiplier),
            'multiplier' => $multiplier,
            'advantages' => $advantages,
            'disadvantages' => $disadvantages,
            'immunity' => false,
        ];
    }
}
