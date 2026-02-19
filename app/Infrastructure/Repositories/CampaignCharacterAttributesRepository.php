<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class CampaignCharacterAttributesRepository extends BaseRepository
{
    protected string $table = 'campaign_character_attributes';

    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $params = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$params})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function findByCampaignCharacterId(int $campaignCharacterId): ?array
    {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE campaign_character_id = :campaign_character_id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'campaign_character_id' => $campaignCharacterId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function updateByCampaignCharacterId(int $campaignCharacterId, array $data): void
    {
        if (empty($data)) {
            return;
        }

        $set = [];
        foreach (array_keys($data) as $col) {
            $set[] = "{$col} = :{$col}";
        }

        $sql = "
            UPDATE {$this->table}
            SET " . implode(', ', $set) . "
            WHERE campaign_character_id = :campaign_character_id
        ";

        $data['campaign_character_id'] = $campaignCharacterId;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
    }
    public function incrementAttribute(int $campaignCharacterId, string $attribute): void
    {
        $sql = "
        UPDATE campaign_character_attributes
        SET {$attribute} = {$attribute} + 1
        WHERE campaign_character_id = :id
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $campaignCharacterId
        ]);
    }
}
