<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use App\Domain\Models\Ability;
use PDO;

class AbilityRepository extends BaseRepository
{
    protected string $table = 'abilities';

    public function create(array $data): int
    {
        $columns =
            implode(
                ', ',
                array_keys($data)
            );

        $params =
            ':' . implode(
                ', :',
                array_keys($data)
            );

        $sql = "
            INSERT INTO {$this->table}
            (
                {$columns}
            )
            VALUES
            (
                {$params}
            )
        ";

        $stmt =
            $this->db->prepare(
                $sql
            );

        $stmt->execute(
            $data
        );

        return (int)
            $this->db->lastInsertId();
    }

    public function findById(
        int $id
    ): ?Ability {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE id = :id
            LIMIT 1
        ";

        $stmt =
            $this->db->prepare(
                $sql
            );

        $stmt->execute([
            'id' => $id,
        ]);

        $row =
            $stmt->fetch(
                PDO::FETCH_ASSOC
            );

        return $row
            ? $this->mapToModel($row)
            : null;
    }

    public function findByIdWithElements(
        int $id
    ): ?array {
        $ability =
            $this->findById($id);

        if (!$ability) {
            return null;
        }

        $elementRepo =
            new AbilityElementTypeRepository();

        $ability->normal_element_types =
            $elementRepo->getByAbilityIdAndForm(
                $id,
                'normal'
            );

        $ability->arcane_element_types =
            $elementRepo->getByAbilityIdAndForm(
                $id,
                'arcane'
            );

        return $ability->toArray();
    }

    public function findByCharacterId(
        int $characterId
    ): array {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE character_id = :character_id
            ORDER BY id DESC
        ";

        $stmt =
            $this->db->prepare(
                $sql
            );

        $stmt->execute([
            'character_id' =>
                $characterId,
        ]);

        $rows =
            $stmt->fetchAll(
                PDO::FETCH_ASSOC
            );

        return array_map(
            fn(array $row) =>
                $this->mapToModel($row),
            $rows
        );
    }

    private function mapToModel(
        array $row
    ): Ability {
        return new Ability(
            id:
                (int) $row['id'],

            character_id:
                $row['character_id'] !== null
                    ? (int) $row['character_id']
                    : null,

            title:
                $row['title'],

            description:
                $row['description'],

            arcane_title:
                $row['arcane_title']
                ?? null,

            arcane_description:
                $row['arcane_description']
                ?? null,

            mana_cost:
                (int) $row['mana_cost'],

            arcane_mana_cost:
                $row['arcane_mana_cost'] !== null
                    ? (int) $row['arcane_mana_cost']
                    : null,

            normal_element_types:
                [],

            arcane_element_types:
                [],

            required_race_id:
                $row['required_race_id'] !== null
                    ? (int) $row['required_race_id']
                    : null,

            required_order_id:
                $row['required_order_id'] !== null
                    ? (int) $row['required_order_id']
                    : null,

            status:
                $row['status']
                ?? 'draft',

            converted_ability_id:
                $row['converted_ability_id'] !== null
                    ? (int) $row['converted_ability_id']
                    : null,

            created_at:
                $row['created_at']
                ?? null,

            updated_at:
                $row['updated_at']
                ?? null
        );
    }
}
