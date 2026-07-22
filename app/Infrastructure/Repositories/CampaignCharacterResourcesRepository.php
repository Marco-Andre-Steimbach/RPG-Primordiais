<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class CampaignCharacterResourcesRepository extends BaseRepository
{
    protected string $table = 'campaign_character_resources';

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
            'campaign_character_id' => $campaignCharacterId
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function updateResources(
        int $campaignCharacterId,
        int $currentHp,
        int $currentMana,
        int $currentSanity
    ): void {
        $sql = "
            UPDATE {$this->table}
            SET
                current_hp = :current_hp,
                current_mana = :current_mana,
                current_sanity = :current_sanity
            WHERE campaign_character_id = :campaign_character_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'current_hp' => $currentHp,
            'current_mana' => $currentMana,
            'current_sanity' => $currentSanity,
            'campaign_character_id' => $campaignCharacterId,
        ]);
    }
}
