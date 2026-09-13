<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class AbilityElementTypeRepository extends BaseRepository
{
    protected string $table =
        'ability_element_types';

    public function attach(
        int $abilityId,
        string $formType,
        int $elementTypeId
    ): void {
        $sql = "
            INSERT IGNORE INTO {$this->table}
            (
                ability_id,
                form_type,
                element_type_id
            )
            VALUES
            (
                :ability_id,
                :form_type,
                :element_type_id
            )
        ";

        $stmt =
            $this->db->prepare(
                $sql
            );

        $stmt->execute([
            'ability_id' =>
                $abilityId,

            'form_type' =>
                $formType,

            'element_type_id' =>
                $elementTypeId,
        ]);
    }

    public function getByAbilityIdAndForm(
        int $abilityId,
        string $formType
    ): array {
        $sql = "
            SELECT element_type_id
            FROM {$this->table}
            WHERE ability_id = :ability_id
              AND form_type = :form_type
            ORDER BY element_type_id
        ";

        $stmt =
            $this->db->prepare(
                $sql
            );

        $stmt->execute([
            'ability_id' =>
                $abilityId,

            'form_type' =>
                $formType,
        ]);

        return array_map(
            fn(array $row) =>
                (int) $row['element_type_id'],

            $stmt->fetchAll(
                PDO::FETCH_ASSOC
            )
        );
    }

    public function getElementsByAbilityAndForm(
        int $abilityId,
        string $formType
    ): array {
        $sql = "
            SELECT
                et.id,
                et.name
            FROM {$this->table} aet
            INNER JOIN element_types et
                ON et.id = aet.element_type_id
            WHERE aet.ability_id = :ability_id
              AND aet.form_type = :form_type
            ORDER BY et.id
        ";

        $stmt =
            $this->db->prepare(
                $sql
            );

        $stmt->execute([
            'ability_id' =>
                $abilityId,

            'form_type' =>
                $formType,
        ]);

        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    public function deleteByAbilityId(
        int $abilityId
    ): void {
        $sql = "
            DELETE FROM {$this->table}
            WHERE ability_id = :ability_id
        ";

        $stmt =
            $this->db->prepare(
                $sql
            );

        $stmt->execute([
            'ability_id' =>
                $abilityId,
        ]);
    }
}
