<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class MonsterAbilityLinkRepository extends BaseRepository
{
    protected string $table = 'monster_ability_links';

    public function attach(int $monsterId, int $abilityId): bool
    {
        $sql = "
            INSERT INTO {$this->table}
            (monster_id, monster_ability_id)
            VALUES (:monster_id, :monster_ability_id)
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'monster_id' => $monsterId,
            'monster_ability_id' => $abilityId,
        ]);
    }

    public function exists(int $monsterId, int $abilityId): bool
    {
        $sql = "
            SELECT 1
            FROM {$this->table}
            WHERE monster_id = :monster_id
              AND monster_ability_id = :monster_ability_id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'monster_id' => $monsterId,
            'monster_ability_id' => $abilityId,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function getAbilityIdsByMonster(int $monsterId): array
    {
        $sql = "
            SELECT monster_ability_id
            FROM monster_ability_links
            WHERE monster_id = :monster_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['monster_id' => $monsterId]);

        return array_column($stmt->fetchAll(\PDO::FETCH_ASSOC), 'monster_ability_id');
    }

    public function getAbilitiesByMonster(int $monsterId): array
    {
        $sql = "
            SELECT monster_ability_id
            FROM {$this->table}
            WHERE monster_id = :monster_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['monster_id' => $monsterId]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
