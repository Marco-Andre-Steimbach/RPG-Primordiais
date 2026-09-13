<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class AbilityNewRuleNodeRepository extends BaseRepository
{
    protected string $table = 'ability_new_rule_nodes';

    public function findById(int $id): ?array
    {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE id = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row
            ? $this->mapRow($row)
            : null;
    }

    public function findByFormId(int $abilityFormId): array
    {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE ability_form_id = :ability_form_id
            ORDER BY sort_order ASC, id ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'ability_form_id' => $abilityFormId,
        ]);

        return array_map(
            fn(array $row) => $this->mapRow($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function getTreeByFormId(int $abilityFormId): array
    {
        $nodes = $this->findByFormId($abilityFormId);

        if (!$nodes) {
            return [];
        }

        $childrenByParent = [];

        foreach ($nodes as $node) {
            $parentKey = $node['parent_id'] === null
                ? 'root'
                : (string) $node['parent_id'];

            if (!isset($childrenByParent[$parentKey])) {
                $childrenByParent[$parentKey] = [];
            }

            $childrenByParent[$parentKey][] = $node;
        }

        return $this->buildTree(
            $childrenByParent,
            null
        );
    }

    private function buildTree(
        array $childrenByParent,
        ?int $parentId
    ): array {
        $parentKey = $parentId === null
            ? 'root'
            : (string) $parentId;

        $nodes = $childrenByParent[$parentKey] ?? [];

        foreach ($nodes as &$node) {
            $node['children'] = $this->buildTree(
                $childrenByParent,
                (int) $node['id']
            );
        }

        unset($node);

        return $nodes;
    }

    private function mapRow(array $row): array
    {
        return [
            'id' => (int) $row['id'],

            'ability_form_id' => (int) $row['ability_form_id'],

            'parent_id' => $row['parent_id'] !== null
                ? (int) $row['parent_id']
                : null,

            'node_type' => $row['node_type'],

            'title' => $row['title'] ?? null,
            'description' => $row['description'] ?? null,

            'sort_order' => (int) $row['sort_order'],

            'roll_formula' => $row['roll_formula'] ?? null,

            'roll_min' => $row['roll_min'] !== null
                ? (int) $row['roll_min']
                : null,

            'roll_max' => $row['roll_max'] !== null
                ? (int) $row['roll_max']
                : null,

            'rule_type' => $row['rule_type'] ?? null,

            'actor_roll_formula' => $row['actor_roll_formula'] ?? null,
            'defender_roll_formula' => $row['defender_roll_formula'] ?? null,

            'actor_attribute_id' => $row['actor_attribute_id'] !== null
                ? (int) $row['actor_attribute_id']
                : null,

            'defender_attribute_id' => $row['defender_attribute_id'] !== null
                ? (int) $row['defender_attribute_id']
                : null,

            'range_formula' => $row['range_formula'] ?? null,
            'area_formula' => $row['area_formula'] ?? null,

            'damage_formula' => $row['damage_formula'] ?? null,
            'duration_formula' => $row['duration_formula'] ?? null,

            'action_change_formula' =>
                $row['action_change_formula'] ?? null,

            'movement_formula' => $row['movement_formula'] ?? null,

            'target_type' => $row['target_type'] ?? null,

            'metadata' => $this->decodeMetadata(
                $row['metadata'] ?? null
            ),

            'created_at' => $row['created_at'] ?? null,
            'updated_at' => $row['updated_at'] ?? null,
        ];
    }

    private function decodeMetadata(?string $metadata): ?array
    {
        if ($metadata === null || $metadata === '') {
            return null;
        }

        $decoded = json_decode(
            $metadata,
            true
        );

        return is_array($decoded)
            ? $decoded
            : null;
    }
}
