<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class CampaignCharacterItemRepository extends BaseRepository
{
    protected string $table = 'campaign_character_items';

    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $params  = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$params})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function findByCampaignCharacterAndItem(int $campaignCharacterId, int $itemId): ?array
    {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE campaign_character_id = :campaign_character_id
              AND item_id = :item_id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'campaign_character_id' => $campaignCharacterId,
            'item_id' => $itemId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function increaseQuantity(int $id, int $amount): void
    {
        $sql = "
            UPDATE {$this->table}
            SET quantity = quantity + :amount
            WHERE id = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'amount' => $amount,
            'id' => $id,
        ]);
    }

    public function findByCampaignCharacter(int $campaignCharacterId): array
    {
        $sql = "
        SELECT *
        FROM {$this->table}
        WHERE campaign_character_id = :id
          AND is_active = 1
          AND quantity > 0
        ORDER BY created_at
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $campaignCharacterId]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    public function exists(int $campaignCharacterId, int $itemId): bool
    {
        $sql = "
            SELECT 1
            FROM {$this->table}
            WHERE campaign_character_id = :campaign_character_id
              AND item_id = :item_id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'campaign_character_id' => $campaignCharacterId,
            'item_id' => $itemId,
        ]);

        return (bool) $stmt->fetchColumn();
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

    public function findByCharacterAndItem(
        int $campaignCharacterId,
        int $itemId
    ): ?array {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE campaign_character_id = :character_id
              AND item_id = :item_id
              AND is_active = 1
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'character_id' => $campaignCharacterId,
            'item_id' => $itemId,
        ]);

        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function updateQuantityByCharacterAndItem(
        int $campaignCharacterId,
        int $itemId,
        int $quantity
    ): void {
        $sql = "
            UPDATE {$this->table}
            SET quantity = :quantity
            WHERE campaign_character_id = :character_id
              AND item_id = :item_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'quantity' => $quantity,
            'character_id' => $campaignCharacterId,
            'item_id' => $itemId,
        ]);
    }

    public function deactivateByCharacterAndItem(
        int $campaignCharacterId,
        int $itemId
    ): void {
        $sql = "
            UPDATE {$this->table}
            SET is_active = 0
            WHERE campaign_character_id = :character_id
              AND item_id = :item_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'character_id' => $campaignCharacterId,
            'item_id' => $itemId,
        ]);
    }
}
