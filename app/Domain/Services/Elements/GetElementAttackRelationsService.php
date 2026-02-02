<?php

namespace App\Domain\Services\Elements;

use App\Infrastructure\Repositories\ElementTypeRepository;

class GetElementAttackRelationsService
{
    private ElementTypeRepository $elements;

    public function __construct()
    {
        $this->elements = new ElementTypeRepository();
    }

    public function execute(array $attackElementIds): array
    {
        $allElements = $this->elements->findAll();

        $relations = $this->elements->getRelationsByIds(
            $attackElementIds,
            array_column($allElements, 'id')
        );

        $map = [];

        foreach ($allElements as $element) {
            $map[$element['id']] = [
                'id' => (int) $element['id'],
                'name' => $element['name'],
                'advantages' => 0,
                'disadvantages' => 0,
                'immunity' => false,
                'multiplier' => 1,
            ];
        }

        foreach ($relations as $relation) {
            $targetId = (int) $relation['target_element_id'];

            if ($relation['relation_type'] === 'immunity') {
                $map[$targetId]['immunity'] = true;
                continue;
            }

            if ($relation['relation_type'] === 'strength') {
                $map[$targetId]['disadvantages']++;
            }

            if ($relation['relation_type'] === 'weakness') {
                $map[$targetId]['advantages']++;
            }
        }

        foreach ($map as &$element) {
            if ($element['immunity']) {
                $element['multiplier'] = 0;
                continue;
            }

            $multiplier =
                1 +
                (0.25 * $element['advantages']) -
                (0.25 * $element['disadvantages']);

            if ($multiplier < 0) {
                $multiplier = 0;
            }

            $element['multiplier'] = $multiplier;
        }

        return array_values($map);
    }
}
