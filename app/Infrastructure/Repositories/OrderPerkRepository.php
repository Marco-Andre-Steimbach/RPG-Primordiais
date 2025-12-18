<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class OrderPerkRepository extends BaseRepository
{
    protected string $table = 'order_perks';

    public function attachPerk(
        int $orderId,
        int $perkId,
        int $requiredLevel = 1
    ): bool {
        $sql = "
            INSERT INTO {$this->table}
            (order_id, perk_id, required_level)
            VALUES (:order_id, :perk_id, :required_level)
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'order_id' => $orderId,
            'perk_id' => $perkId,
            'required_level' => $requiredLevel,
        ]);
    }

    public function exists(
        int $orderId,
        int $perkId
    ): bool {
        $sql = "
            SELECT 1
            FROM {$this->table}
            WHERE order_id = :order_id
              AND perk_id = :perk_id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'order_id' => $orderId,
            'perk_id' => $perkId,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function getPerksByOrder(int $orderId): array
    {
        $sql = "
            SELECT perk_id, required_level
            FROM {$this->table}
            WHERE order_id = :order_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['order_id' => $orderId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function findByPerkId(int $perkId): ?array
    {
        $sql = "
        SELECT order_id, required_level
        FROM {$this->table}
        WHERE perk_id = :perk_id
        LIMIT 1
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['perk_id' => $perkId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

}
