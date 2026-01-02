<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class CampaignCharacterRepository extends BaseRepository
{
    protected string $table = 'campaign_characters';

    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $params = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$params})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function exists(int $campaignId, int $characterId): bool
    {
        $sql = "
            SELECT 1
            FROM {$this->table}
            WHERE campaign_id = :campaign_id
              AND character_id = :character_id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'campaign_id' => $campaignId,
            'character_id' => $characterId,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }
    public function countByCampaign(int $campaignId): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE campaign_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $campaignId]);

        return (int) $stmt->fetchColumn();
    }

    public function findByCampaignId(int $campaignId): array
    {
        $sql = "
        SELECT
            id,
            campaign_id,
            character_id,
            user_id,
            level
        FROM {$this->table}
        WHERE campaign_id = :campaign_id
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['campaign_id' => $campaignId]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findByCampaignAndCharacter(int $campaignId, int $characterId): ?array
    {
        $sql = "
        SELECT *
        FROM {$this->table}
        WHERE campaign_id = :campaign_id
          AND character_id = :character_id
        LIMIT 1
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'campaign_id' => $campaignId,
            'character_id' => $characterId,
        ]);

        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }


}
