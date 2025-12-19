<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;

class MonsterAbilityElementTypeRepository extends BaseRepository
{
    protected string $table = 'monster_ability_element_types';

    public function attach(int $abilityId, int $elementTypeId): bool
    {
        $sql = "
            INSERT INTO {$this->table}
            (monster_ability_id, element_type_id)
            VALUES (:monster_ability_id, :element_type_id)
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'monster_ability_id' => $abilityId,
            'element_type_id' => $elementTypeId,
        ]);
    }

    public function getByAbility(int $abilityId): array
    {
        $sql = "
            SELECT element_type_id
            FROM {$this->table}
            WHERE monster_ability_id = :monster_ability_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'monster_ability_id' => $abilityId,
        ]);

        return array_column(
            $stmt->fetchAll(\PDO::FETCH_ASSOC),
            'element_type_id'
        );
    }
}
