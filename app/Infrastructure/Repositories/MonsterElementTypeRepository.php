<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class MonsterElementTypeRepository extends BaseRepository
{
    protected string $table = 'monster_element_types';

    public function attach(int $monsterId, int $elementTypeId): bool
    {
        $sql = "
            INSERT INTO {$this->table}
            (monster_id, element_type_id)
            VALUES (:monster_id, :element_type_id)
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'monster_id' => $monsterId,
            'element_type_id' => $elementTypeId,
        ]);
    }

    public function exists(int $monsterId, int $elementTypeId): bool
    {
        $sql = "
            SELECT 1
            FROM {$this->table}
            WHERE monster_id = :monster_id
              AND element_type_id = :element_type_id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'monster_id' => $monsterId,
            'element_type_id' => $elementTypeId,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function getByMonster(int $monsterId): array
    {
        $sql = "
            SELECT element_type_id
            FROM {$this->table}
            WHERE monster_id = :monster_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['monster_id' => $monsterId]);

        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'element_type_id');
    }
}
