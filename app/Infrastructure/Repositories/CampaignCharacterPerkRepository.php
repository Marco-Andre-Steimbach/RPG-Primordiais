<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class CampaignCharacterPerkRepository extends BaseRepository
{
    protected string $table = 'campaign_character_perks';

    public function exists(
        int $campaignCharacterId,
        int $perkId
    ): bool {
        $sql = "
            SELECT 1
            FROM {$this->table}
            WHERE campaign_character_id = :campaign_character_id
              AND perk_id = :perk_id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'campaign_character_id' =>
                $campaignCharacterId,

            'perk_id' =>
                $perkId,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function create(
        array $data
    ): bool {
        $columns =
            implode(
                ', ',
                array_keys($data)
            );

        $params =
            ':' . implode(
                ', :',
                array_keys($data)
            );

        $sql = "
            INSERT INTO {$this->table}
            ({$columns})
            VALUES
            ({$params})
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute($data);
    }

    public function countByCampaignCharacter(
        int $campaignCharacterId
    ): int {
        $sql = "
            SELECT COUNT(*)
            FROM {$this->table}
            WHERE campaign_character_id = :campaign_character_id
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'campaign_character_id' =>
                $campaignCharacterId,
        ]);

        return (int) $stmt->fetchColumn();
    }

    public function findByCampaignCharacter(
        int $campaignCharacterId
    ): array {
        $sql = "
            SELECT
                id,
                campaign_character_id,
                perk_id,
                created_at
            FROM {$this->table}
            WHERE campaign_character_id = :campaign_character_id
            ORDER BY id ASC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'campaign_character_id' =>
                $campaignCharacterId,
        ]);

        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    public function getPerkIdsByCampaignCharacter(
        int $campaignCharacterId
    ): array {
        $sql = "
            SELECT perk_id
            FROM {$this->table}
            WHERE campaign_character_id = :campaign_character_id
            ORDER BY id ASC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'campaign_character_id' =>
                $campaignCharacterId,
        ]);

        $rows = $stmt->fetchAll(
            PDO::FETCH_COLUMN
        );

        return array_map(
            'intval',
            $rows ?: []
        );
    }
}
