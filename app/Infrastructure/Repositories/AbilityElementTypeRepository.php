<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class AbilityElementTypeRepository extends BaseRepository
{
    protected string $table = 'ability_element_types';

    public function attach(int $abilityId, int $elementTypeId): void
    {
        $sql = "
            INSERT INTO {$this->table} (ability_id, element_type_id)
            VALUES (:ability_id, :element_type_id)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'ability_id' => $abilityId,
            'element_type_id' => $elementTypeId,
        ]);
    }

    public function getByAbilityId(int $abilityId): array
    {
        $sql = "
            SELECT element_type_id
            FROM {$this->table}
            WHERE ability_id = :ability_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['ability_id' => $abilityId]);

        return array_map(
            fn ($row) => (int) $row['element_type_id'],
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }
    public function getElementsByAbility(int $abilityId): array
{
    $sql = "
        SELECT et.id, et.name
        FROM ability_element_types aet
        JOIN element_types et ON et.id = aet.element_type_id
        WHERE aet.ability_id = :ability_id
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute(['ability_id' => $abilityId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
