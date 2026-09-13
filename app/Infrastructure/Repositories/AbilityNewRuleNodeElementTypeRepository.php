<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class AbilityNewRuleNodeElementTypeRepository extends BaseRepository
{
    protected string $table = 'ability_new_rule_node_element_types';

    public function findByFormId(int $abilityFormId): array
    {
        $sql = "
            SELECT
                net.ability_rule_node_id,
                net.element_type_id
            FROM {$this->table} net
            INNER JOIN ability_new_rule_nodes arn
                ON arn.id = net.ability_rule_node_id
            WHERE arn.ability_form_id = :ability_form_id
            ORDER BY
                net.ability_rule_node_id,
                net.element_type_id
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'ability_form_id' => $abilityFormId,
        ]);

        $result = [];

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $nodeId =
                (int) $row['ability_rule_node_id'];

            if (!isset($result[$nodeId])) {
                $result[$nodeId] = [];
            }

            $result[$nodeId][] =
                (int) $row['element_type_id'];
        }

        return $result;
    }

    public function findByNodeId(int $abilityRuleNodeId): array
    {
        $sql = "
            SELECT element_type_id
            FROM {$this->table}
            WHERE ability_rule_node_id = :ability_rule_node_id
            ORDER BY element_type_id
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'ability_rule_node_id' =>
                $abilityRuleNodeId,
        ]);

        return array_map(
            fn(array $row) =>
                (int) $row['element_type_id'],
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }
}
