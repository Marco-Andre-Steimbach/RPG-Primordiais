<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class PerkAttributeRepository extends BaseRepository
{
    protected string $table = 'perk_attributes';

    public function createMany(int $perkId, array $attributes): void
    {
        $sql = "
            INSERT INTO {$this->table}
            (perk_id, attribute_name, attribute_value)
            VALUES (:perk_id, :attribute_name, :attribute_value)
        ";

        $stmt = $this->db->prepare($sql);

        foreach ($attributes as $attribute) {
            $stmt->execute([
                'perk_id' => $perkId,
                'attribute_name' => $attribute['name'],
                'attribute_value' => $attribute['value'],
            ]);
        }
    }

    public function getByPerk(int $perkId): array
    {
        $sql = "
            SELECT attribute_name, attribute_value
            FROM {$this->table}
            WHERE perk_id = :perk_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['perk_id' => $perkId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function attach(int $perkId, string $name, int $value): bool
    {
        $sql = "
            INSERT INTO {$this->table} (perk_id, attribute_name, attribute_value)
            VALUES (:perk_id, :attribute_name, :attribute_value)
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'perk_id' => $perkId,
            'attribute_name' => $name,
            'attribute_value' => $value,
        ]);
    }

    public function deleteByPerk(int $perkId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE perk_id = :perk_id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute(['perk_id' => $perkId]);
    }
}
