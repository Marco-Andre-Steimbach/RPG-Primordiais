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

    public function execute(array $selectedElementIds): array
    {
        $allElements = $this->elements->findAll();
        $allElementIds = array_map(
            fn ($element) => (int) $element['id'],
            $allElements
        );

        $selectedElementIds = array_map('intval', $selectedElementIds);

        $attackRelations = $this->calculateRelations(
            $selectedElementIds,
            $allElementIds,
            $allElements,
            'target'
        );

        $defenseRelations = $this->calculateRelations(
            $allElementIds,
            $selectedElementIds,
            $allElements,
            'source'
        );

        $individualRelations = $this->getIndividualRelations(
            $selectedElementIds,
            $allElementIds,
            $allElements
        );

        return [
            'selected_elements' => array_values(array_filter(
                $allElements,
                fn ($element) => in_array(
                    (int) $element['id'],
                    $selectedElementIds,
                    true
                )
            )),

            'relations' => $individualRelations,

            'defense' => [
                'strong_against_me' => array_values(array_filter(
                    $defenseRelations,
                    fn ($item) => $item['modifier'] > 0
                )),

                'weak_against_me' => array_values(array_filter(
                    $defenseRelations,
                    fn ($item) => $item['modifier'] < 0
                )),

                'all' => $defenseRelations,
            ],

            'attack' => [
                'strong_against' => array_values(array_filter(
                    $attackRelations,
                    fn ($item) => $item['modifier'] > 0
                )),

                'weak_against' => array_values(array_filter(
                    $attackRelations,
                    fn ($item) => $item['modifier'] < 0
                )),

                'all' => $attackRelations,
            ],
        ];
    }

    private function calculateRelations(
        array $attackElementIds,
        array $defenseElementIds,
        array $allElements,
        string $groupBy
    ): array {
        $relations = $this->elements->getRelationsByIds(
            $attackElementIds,
            $defenseElementIds
        );

        $map = [];

        foreach ($allElements as $element) {
            $elementId = (int) $element['id'];

            $map[$elementId] = [
                'id' => $elementId,
                'name' => $element['name'],
                'modifier' => 0.0,
                'multiplier' => 1.0,
                'relations' => [],
            ];
        }

        foreach ($relations as $relation) {
            $sourceId = (int) $relation['source_element_id'];
            $targetId = (int) $relation['target_element_id'];

            $mapId = $groupBy === 'source'
                ? $sourceId
                : $targetId;

            if (!isset($map[$mapId])) {
                continue;
            }

            $relationModifier = (float) $relation['modifier'];
            $modifier = 0;

            if ($relation['relation_type'] === 'strong') {
                $modifier = $relationModifier;
            }

            if ($relation['relation_type'] === 'weak') {
                $modifier = -$relationModifier;
            }

            $map[$mapId]['modifier'] += $modifier;

            $map[$mapId]['relations'][] = [
                'source_element_id' => $sourceId,
                'target_element_id' => $targetId,
                'relation_type' => $relation['relation_type'],
                'modifier' => $modifier,
            ];
        }

        foreach ($map as &$item) {
            $item['modifier'] = $this->normalizeModifier(
                $item['modifier']
            );

            $item['multiplier'] = max(
                0,
                1 + $item['modifier']
            );
        }

        unset($item);

        return array_values($map);
    }

    private function getIndividualRelations(
        array $selectedElementIds,
        array $allElementIds,
        array $allElements
    ): array {
        $relations = $this->elements->getRelationsByIds(
            $selectedElementIds,
            $allElementIds
        );

        $elementNames = [];

        foreach ($allElements as $element) {
            $elementNames[(int) $element['id']] = $element['name'];
        }

        $positive = [];
        $negative = [];

        foreach ($relations as $relation) {
            if ($relation['relation_type'] === 'normal') {
                continue;
            }

            $sourceId = (int) $relation['source_element_id'];
            $targetId = (int) $relation['target_element_id'];
            $modifier = (float) $relation['modifier'];

            $item = [
                'source_element_id' => $sourceId,
                'source_element_name' => $elementNames[$sourceId] ?? null,
                'target_element_id' => $targetId,
                'target_element_name' => $elementNames[$targetId] ?? null,
                'relation_type' => $relation['relation_type'],
                'modifier' => $modifier,
            ];

            if ($relation['relation_type'] === 'strong') {
                $positive[] = $item;
            }

            if ($relation['relation_type'] === 'weak') {
                $negative[] = $item;
            }
        }

        return [
            'positive' => $positive,
            'negative' => $negative,
        ];
    }

    private function normalizeModifier(float $modifier): float
    {
        $modifier = round($modifier, 10);

        if (abs($modifier) < 0.000000001) {
            return 0.0;
        }

        return $modifier;
    }
}
