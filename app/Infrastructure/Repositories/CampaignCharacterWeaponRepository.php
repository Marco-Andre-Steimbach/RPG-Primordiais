<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class CampaignCharacterWeaponRepository extends BaseRepository
{
    protected string $table = 'campaign_character_weapons';

    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $params  = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$params})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function exists(int $campaignCharacterId, int $weaponId): bool
    {
        $sql = "
            SELECT 1
            FROM {$this->table}
            WHERE campaign_character_id = :campaign_character_id
              AND weapon_id = :weapon_id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'campaign_character_id' => $campaignCharacterId,
            'weapon_id' => $weaponId,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function findActiveByCampaignCharacter(int $campaignCharacterId): array
    {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE campaign_character_id = :campaign_character_id
              AND is_active = 1
            ORDER BY id DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'campaign_character_id' => $campaignCharacterId,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function countActiveByCampaignCharacter(int $campaignCharacterId): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM {$this->table}
            WHERE campaign_character_id = :campaign_character_id
              AND is_active = 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'campaign_character_id' => $campaignCharacterId,
        ]);

        return (int) $stmt->fetchColumn();
    }

    public function deactivate(int $id): bool
    {
        $sql = "
            UPDATE {$this->table}
            SET is_active = 0, is_equipped = 0
            WHERE id = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function unequipAll(int $campaignCharacterId): bool
    {
        $sql = "
            UPDATE {$this->table}
            SET is_equipped = 0
            WHERE campaign_character_id = :campaign_character_id
              AND is_active = 1
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'campaign_character_id' => $campaignCharacterId,
        ]);
    }
}
