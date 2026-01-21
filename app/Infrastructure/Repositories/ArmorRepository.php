<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use App\Domain\Models\Armor;
use PDO;

class ArmorRepository extends BaseRepository
{
    protected string $table = 'armors';

    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $params  = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($params)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function existsByItemId(int $itemId): bool
    {
        $sql = "SELECT 1 FROM {$this->table} WHERE item_id = :item_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['item_id' => $itemId]);

        return (bool) $stmt->fetchColumn();
    }
    public function findByItemId(int $itemId): ?Armor
    {
        $sql = "SELECT * FROM {$this->table} WHERE item_id = :item_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['item_id' => $itemId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToModel($row) : null;
    }


    public function findById(int $id): ?Armor
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToModel($row) : null;
    }

    private function mapToModel(array $row): Armor
    {
        return new Armor(
            id: (int) $row['id'],
            item_id: (int) $row['item_id'],
            armor_slot_id: (int) $row['armor_slot_id'],
            armor_class_bonus: (int) $row['armor_class_bonus'],
            min_strength_required: (int) $row['min_strength_required'],
            speed_penalty: (int) $row['speed_penalty'],
            weak_damage_type_id: isset($row['weak_damage_type_id'])
                ? (int) $row['weak_damage_type_id']
                : null,
            element_types: [],
            armor_abilities: [],
            created_at: $row['created_at'] ?? null
        );
    }

    public function findAllWithItemAndSlot(): array
    {
        $sql = "
            SELECT
                a.id AS armor_id,
                a.item_id,
                i.name AS item_name,
                i.description AS item_description,
                a.armor_slot_id,
                s.name AS slot_name,
                a.armor_class_bonus,
                a.min_strength_required,
                a.speed_penalty,
                a.weak_damage_type_id,
                a.created_at
            FROM armors a
            INNER JOIN items i ON i.id = a.item_id
            INNER JOIN armor_slots s ON s.id = a.armor_slot_id
            ORDER BY i.name
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
