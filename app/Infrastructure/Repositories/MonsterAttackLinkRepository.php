<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class MonsterAttackLinkRepository extends BaseRepository
{
    protected string $table = 'monster_attack_links';

    public function attach(int $monsterId, int $attackId): bool
    {
        $sql = "
            INSERT INTO {$this->table}
            (monster_id, monster_attack_id)
            VALUES (:monster_id, :monster_attack_id)
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'monster_id' => $monsterId,
            'monster_attack_id' => $attackId,
        ]);
    }

    public function exists(int $monsterId, int $attackId): bool
    {
        $sql = "
            SELECT 1
            FROM {$this->table}
            WHERE monster_id = :monster_id
              AND monster_attack_id = :monster_attack_id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'monster_id' => $monsterId,
            'monster_attack_id' => $attackId,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function getAttacksByMonster(int $monsterId): array
    {
        $sql = "
            SELECT monster_attack_id
            FROM {$this->table}
            WHERE monster_id = :monster_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['monster_id' => $monsterId]);

        return array_column(
            $stmt->fetchAll(PDO::FETCH_ASSOC),
            'monster_attack_id'
        );
    }
    public function getAttackIdsByMonster(int $monsterId): array
    {
        $sql = "
        SELECT monster_attack_id
        FROM monster_attack_links
        WHERE monster_id = :monster_id
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['monster_id' => $monsterId]);

        return array_column($stmt->fetchAll(\PDO::FETCH_ASSOC), 'monster_attack_id');
    }
}
