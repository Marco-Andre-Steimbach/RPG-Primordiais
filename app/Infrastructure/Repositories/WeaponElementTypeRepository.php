<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class WeaponElementTypeRepository extends BaseRepository
{
    protected string $table = 'weapon_element_types';

    public function attach(int $weaponId, int $elementTypeId): void
    {
        $sql = "
            INSERT INTO {$this->table} (weapon_id, element_type_id)
            VALUES (:weapon_id, :element_type_id)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'weapon_id' => $weaponId,
            'element_type_id' => $elementTypeId,
        ]);
    }

    public function getByWeaponId(int $weaponId): array
    {
        $sql = "
            SELECT element_type_id
            FROM {$this->table}
            WHERE weapon_id = :weapon_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['weapon_id' => $weaponId]);

        return array_map(
            fn($row) => (int) $row['element_type_id'],
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }
}
