<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class CampaignCharacterPerkRepository extends BaseRepository
{
    protected string $table = 'campaign_character_perks';

    public function exists(int $campaignCharacterId, int $perkId): bool
    {
        $sql = "
            SELECT 1 FROM {$this->table}
            WHERE campaign_character_id = :cc
              AND perk_id = :perk
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'cc'   => $campaignCharacterId,
            'perk' => $perkId,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function create(array $data): void
    {
        $columns = implode(', ', array_keys($data));
        $params  = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$params})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
    }

    public function countByCampaignCharacter(int $campaignCharacterId): int
    {
        $sql = "
        SELECT COUNT(*)
        FROM campaign_character_perks
        WHERE campaign_character_id = :id
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $campaignCharacterId]);

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
