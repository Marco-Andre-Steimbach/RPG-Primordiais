<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class AbilityNewFormElementTypeRepository extends BaseRepository
{
    protected string $table = 'ability_new_form_element_types';

    public function findByAbilityId(int $abilityId): array
    {
        $sql = "
            SELECT
                fet.ability_form_id,
                fet.element_type_id
            FROM {$this->table} fet
            INNER JOIN ability_new_forms af
                ON af.id = fet.ability_form_id
            WHERE af.ability_id = :ability_id
            ORDER BY
                fet.ability_form_id,
                fet.element_type_id
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'ability_id' => $abilityId,
        ]);

        $result = [];

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $formId = (int) $row['ability_form_id'];

            if (!isset($result[$formId])) {
                $result[$formId] = [];
            }

            $result[$formId][] =
                (int) $row['element_type_id'];
        }

        return $result;
    }

    public function findByFormId(int $abilityFormId): array
    {
        $sql = "
            SELECT element_type_id
            FROM {$this->table}
            WHERE ability_form_id = :ability_form_id
            ORDER BY element_type_id
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'ability_form_id' => $abilityFormId,
        ]);

        return array_map(
            fn(array $row) =>
                (int) $row['element_type_id'],
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }
}
