<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class WeaponAbilityElementTypeRepository extends BaseRepository
{
    protected string $table = 'weapon_ability_element_types';

    public function attach(int $weaponAbilityId, int $elementTypeId): void
    {
        $sql = "
            INSERT INTO {$this->table} (weapon_ability_id, element_type_id)
            VALUES (:weapon_ability_id, :element_type_id)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'weapon_ability_id' => $weaponAbilityId,
            'element_type_id' => $elementTypeId,
        ]);
    }

    public function getByWeaponAbilityId(int $weaponAbilityId): array
    {
        $sql = "
            SELECT element_type_id
            FROM {$this->table}
            WHERE weapon_ability_id = :weapon_ability_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['weapon_ability_id' => $weaponAbilityId]);

        return array_map(
            fn($row) => (int) $row['element_type_id'],
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }
}
