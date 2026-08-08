<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class CampaignCharacterResourcesRepository extends BaseRepository
{
    protected string $table = 'campaign_character_resources';

    public function findByCampaignCharacterId(
        int $campaignCharacterId
    ): ?array {
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

    public function create(array $data): int
    {
        $sql = "
            INSERT INTO {$this->table}
            (" . implode(', ', array_keys($data)) . ")
            VALUES (:" . implode(', :', array_keys($data)) . ")
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function updateByCampaignCharacterId(
        int $campaignCharacterId,
        array $data
    ): bool {
        if (empty($data)) {
            return false;
        }

        $fields = [];

        foreach (array_keys($data) as $key) {
            $fields[] = "{$key} = :{$key}";
        }

        $sql = "
            UPDATE {$this->table}
            SET " . implode(', ', $fields) . "
            WHERE campaign_character_id = :campaign_character_id
        ";

        $data['campaign_character_id'] = $campaignCharacterId;

        $stmt = $this->db->prepare($sql);

        return $stmt->execute($data);
    }
}
