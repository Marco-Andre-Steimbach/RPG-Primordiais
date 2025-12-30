<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class ArmorArmorAbilityRepository extends BaseRepository
{
    protected string $table = 'armor_armor_abilities';

    public function attach(int $armorId, int $abilityId): void
    {
        $sql = "
            INSERT INTO {$this->table} (armor_id, armor_ability_id)
            VALUES (:armor_id, :armor_ability_id)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'armor_id' => $armorId,
            'armor_ability_id' => $abilityId,
        ]);
    }

    public function getByArmorId(int $armorId): array
    {
        $sql = "
            SELECT armor_ability_id
            FROM {$this->table}
            WHERE armor_id = :armor_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['armor_id' => $armorId]);

        return array_map(
            fn ($row) => (int) $row['armor_ability_id'],
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }
}
