<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class CampaignCharacterArmorRepository extends BaseRepository
{
    protected string $table = 'campaign_character_armors';

    public function exists(int $campaignCharacterId, int $armorId): bool
    {
        $sql = "
            SELECT 1
            FROM {$this->table}
            WHERE campaign_character_id = :cid
              AND armor_id = :aid
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'cid' => $campaignCharacterId,
            'aid' => $armorId,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function deactivateBySlot(int $campaignCharacterId, int $slotId): void
    {
        $sql = "
            UPDATE {$this->table} cca
            INNER JOIN armors a ON a.id = cca.armor_id
            SET cca.is_active = 0
            WHERE cca.campaign_character_id = :cid
              AND a.armor_slot_id = :slot
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'cid'  => $campaignCharacterId,
            'slot' => $slotId,
        ]);
    }

    public function unequipBySlot(int $campaignCharacterId, int $slotId): void
    {
        $sql = "
            UPDATE {$this->table} cca
            INNER JOIN armors a ON a.id = cca.armor_id
            SET cca.is_equipped = 0
            WHERE cca.campaign_character_id = :cid
              AND a.armor_slot_id = :slot
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'cid'  => $campaignCharacterId,
            'slot' => $slotId,
        ]);
    }

    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $params  = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($params)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }
    public function findActiveByCampaignCharacter(int $campaignCharacterId): array
    {
        $sql = "
        SELECT *
        FROM {$this->table}
        WHERE campaign_character_id = :id
          AND is_active = 1
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $campaignCharacterId]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

}
