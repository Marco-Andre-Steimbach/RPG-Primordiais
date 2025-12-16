<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class PerkElementTypeRepository extends BaseRepository
{
    protected string $table = 'perk_element_types';

    public function attachMany(int $perkId, array $elementTypeIds): void
    {
        $sql = "
            INSERT IGNORE INTO {$this->table}
            (perk_id, element_type_id)
            VALUES (:perk_id, :element_type_id)
        ";

        $stmt = $this->db->prepare($sql);

        foreach ($elementTypeIds as $elementTypeId) {
            $stmt->execute([
                'perk_id' => $perkId,
                'element_type_id' => (int) $elementTypeId,
            ]);
        }
    }

    public function getElementTypesByPerk(int $perkId): array
    {
        $sql = "
            SELECT element_type_id
            FROM {$this->table}
            WHERE perk_id = :perk_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['perk_id' => $perkId]);

        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    public function attach(int $perkId, int $elementTypeId): bool
    {
        $sql = "
            INSERT IGNORE INTO {$this->table} (perk_id, element_type_id)
            VALUES (:perk_id, :element_type_id)
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'perk_id' => $perkId,
            'element_type_id' => $elementTypeId,
        ]);
    }

    public function deleteByPerk(int $perkId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE perk_id = :perk_id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute(['perk_id' => $perkId]);
    }
}
