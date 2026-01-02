<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class ItemElementTypeRepository extends BaseRepository
{
    protected string $table = 'item_element_types';

    public function attach(int $itemId, int $elementTypeId): void
    {
        $sql = "
            INSERT INTO {$this->table} (item_id, element_type_id)
            VALUES (:item_id, :element_type_id)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'item_id' => $itemId,
            'element_type_id' => $elementTypeId,
        ]);
    }

    public function getByItemId(int $itemId): array
    {
        $sql = "
            SELECT element_type_id
            FROM {$this->table}
            WHERE item_id = :item_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['item_id' => $itemId]);

        return array_map(
            fn($row) => (int) $row['element_type_id'],
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }
}
