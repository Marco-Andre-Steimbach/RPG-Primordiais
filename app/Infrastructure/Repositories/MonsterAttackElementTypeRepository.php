<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class MonsterAttackElementTypeRepository extends BaseRepository
{
    protected string $table = 'monster_attack_element_types';

    public function attach(int $monsterAttackId, int $elementTypeId): bool
    {
        $sql = "
            INSERT INTO {$this->table}
            (monster_attack_id, element_type_id)
            VALUES (:monster_attack_id, :element_type_id)
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'monster_attack_id' => $monsterAttackId,
            'element_type_id' => $elementTypeId,
        ]);
    }

    public function detachAllByAttack(int $monsterAttackId): bool
    {
        $sql = "
            DELETE FROM {$this->table}
            WHERE monster_attack_id = :monster_attack_id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'monster_attack_id' => $monsterAttackId,
        ]);
    }

    public function getByAttack(int $monsterAttackId): array
    {
        $sql = "
            SELECT element_type_id
            FROM {$this->table}
            WHERE monster_attack_id = :monster_attack_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'monster_attack_id' => $monsterAttackId,
        ]);

        return array_map(
            fn($row) => (int) $row['element_type_id'],
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }
}
