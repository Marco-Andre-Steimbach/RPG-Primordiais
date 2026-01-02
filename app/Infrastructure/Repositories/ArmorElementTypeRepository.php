<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class ArmorElementTypeRepository extends BaseRepository
{
    protected string $table = 'armor_element_types';

    public function attach(int $armorId, int $elementTypeId): void
    {
        $sql = "
            INSERT INTO {$this->table} (armor_id, element_type_id)
            VALUES (:armor_id, :element_type_id)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'armor_id' => $armorId,
            'element_type_id' => $elementTypeId,
        ]);
    }

    public function getByArmorId(int $armorId): array
    {
        $sql = "
            SELECT element_type_id
            FROM {$this->table}
            WHERE armor_id = :armor_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['armor_id' => $armorId]);

        return array_map(
            fn($row) => (int) $row['element_type_id'],
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }
}
