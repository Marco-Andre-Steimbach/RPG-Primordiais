<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class AbilityNewFormRepository extends BaseRepository
{
    protected string $table = 'ability_new_forms';

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

    public function findByAbilityId(int $abilityId): array
    {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE ability_id = :ability_id
            ORDER BY
                CASE form_type
                    WHEN 'normal' THEN 1
                    WHEN 'arcane' THEN 2
                    ELSE 3
                END,
                id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'ability_id' => $abilityId,
        ]);

        return array_map(
            fn(array $row) => $this->mapRow($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function findByAbilityIdAndType(
        int $abilityId,
        string $formType
    ): ?array {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE ability_id = :ability_id
              AND form_type = :form_type
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'ability_id' => $abilityId,
            'form_type' => $formType,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row
            ? $this->mapRow($row)
            : null;
    }

    private function mapRow(array $row): array
    {
        return [
            'id' => (int) $row['id'],
            'ability_id' => (int) $row['ability_id'],

            'form_type' => $row['form_type'],

            'title' => $row['title'],
            'description' => $row['description'],

            'mana_cost' => (int) $row['mana_cost'],

            'activation_type' => $row['activation_type'],
            'action_cost' => (int) $row['action_cost'],

            'uses_per_turn' => $row['uses_per_turn'] !== null
                ? (int) $row['uses_per_turn']
                : null,

            'uses_per_combat' => $row['uses_per_combat'] !== null
                ? (int) $row['uses_per_combat']
                : null,

            'range_formula' => $row['range_formula'] ?? null,
            'area_formula' => $row['area_formula'] ?? null,

            'target_type' => $row['target_type'] ?? null,

            'affects_allies' => (bool) $row['affects_allies'],
            'affects_enemies' => (bool) $row['affects_enemies'],

            'rule_type' => $row['rule_type'],

            'actor_roll_formula' => $row['actor_roll_formula'] ?? null,
            'defender_roll_formula' => $row['defender_roll_formula'] ?? null,

            'actor_attribute_id' => $row['actor_attribute_id'] !== null
                ? (int) $row['actor_attribute_id']
                : null,

            'defender_attribute_id' => $row['defender_attribute_id'] !== null
                ? (int) $row['defender_attribute_id']
                : null,

            'rule_description' => $row['rule_description'] ?? null,

            'created_at' => $row['created_at'] ?? null,
            'updated_at' => $row['updated_at'] ?? null,
        ];
    }
}
