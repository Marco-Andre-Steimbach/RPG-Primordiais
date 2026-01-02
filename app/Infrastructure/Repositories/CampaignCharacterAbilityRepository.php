<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class CampaignCharacterAbilityRepository extends BaseRepository
{
    protected string $table = 'campaign_character_abilities';

    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $params  = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$params})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function exists(int $campaignCharacterId, int $abilityId): bool
    {
        $sql = "
            SELECT 1
            FROM {$this->table}
            WHERE campaign_character_id = :ccid
              AND ability_id = :aid
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'ccid' => $campaignCharacterId,
            'aid'  => $abilityId,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function countByCampaignCharacter(int $campaignCharacterId): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM {$this->table}
            WHERE campaign_character_id = :ccid
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['ccid' => $campaignCharacterId]);

        return (int) $stmt->fetchColumn();
    }
    public function findByCampaignCharacter(int $campaignCharacterId): array
    {
        $sql = "
        SELECT *
        FROM {$this->table}
        WHERE campaign_character_id = :id
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $campaignCharacterId]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

}
