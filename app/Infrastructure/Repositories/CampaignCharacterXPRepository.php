<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class CampaignCharacterXPRepository extends BaseRepository
{
    protected string $table = 'campaign_character_xp';

    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $params  = ':' . implode(', :', array_keys($data));

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

    public function updateByCampaignCharacterId(
        int $campaignCharacterId,
        int $currentXP,
        int $totalXP
    ): void {
        $sql = "
            UPDATE {$this->table}
            SET current_xp = :current_xp,
                total_xp   = :total_xp
            WHERE campaign_character_id = :campaign_character_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'current_xp'            => $currentXP,
            'total_xp'              => $totalXP,
            'campaign_character_id' => $campaignCharacterId,
        ]);
    }
}
