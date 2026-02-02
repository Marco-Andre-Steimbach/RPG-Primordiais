<?php

namespace App\Domain\Services\Elements;

use App\Infrastructure\Repositories\ElementTypeRepository;

class GetElementRelationsService
{
    private ElementTypeRepository $elements;

    public function __construct()
    {
        $this->elements = new ElementTypeRepository();
    }

    public function execute(array $defenseElementIds): array
    {
        $allElements = $this->elements->findAll();

        $relations = $this->elements->getRelationsByIds(
            array_column($allElements, 'id'),
            $defenseElementIds
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
            $sourceId = (int) $relation['source_element_id'];

            if ($relation['relation_type'] === 'immunity') {
                $map[$sourceId]['immunity'] = true;
                continue;
            }

            if ($relation['relation_type'] === 'strength') {
                $map[$sourceId]['advantages']++;
            }

            if ($relation['relation_type'] === 'weakness') {
                $map[$sourceId]['disadvantages']++;
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
