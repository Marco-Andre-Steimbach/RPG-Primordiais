<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;

class OrderAttributeRepository extends BaseRepository
{
    protected string $table = 'order_attributes';

    public function attach(int $orderId, string $name, int $value): bool
    {
        $sql = "
            INSERT INTO {$this->table} (order_id, attribute_name, attribute_value)
            VALUES (:order_id, :attribute_name, :attribute_value)
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'order_id' => $orderId,
            'attribute_name' => $name,
            'attribute_value' => $value,
        ]);
    }

    public function getByOrder(int $orderId): array
    {
        $sql = "
        SELECT attribute_name, attribute_value
        FROM {$this->table}
        WHERE order_id = :order_id
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['order_id' => $orderId]);

        return $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

}
