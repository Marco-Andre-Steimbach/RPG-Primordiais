<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use App\Domain\Models\Weapon;
use PDO;

class WeaponRepository extends BaseRepository
{
    protected string $table = 'weapons';

    public function create(array $data): int
    {
        $columns = implode(', ', array_map(
            fn($col) => "`$col`",
            array_keys($data)
        ));
        $params  = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($params)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function existsById(int $id): bool
    {
        $sql = "SELECT 1 FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return (bool) $stmt->fetchColumn();
    }

    public function existsByItemId(int $itemId): bool
    {
        $sql = "SELECT 1 FROM {$this->table} WHERE item_id = :item_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['item_id' => $itemId]);

        return (bool) $stmt->fetchColumn();
    }

    public function findById(int $id): ?Weapon
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToModel($row) : null;
    }

    public function findByItemId(int $itemId): ?Weapon
    {
        $sql = "SELECT * FROM {$this->table} WHERE item_id = :item_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['item_id' => $itemId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToModel($row) : null;
    }


    public function findByIdWithItemAndDamageType(int $weaponId): ?array
    {
        $sql = "
            SELECT
                w.id,
                w.item_id,
                i.name AS item_name,
                i.description AS item_description,
                w.weapon_damage_type_id,
                wdt.name AS damage_type,
                w.dice_formula,
                w.base_damage,
                w.range,
                w.bonus_accuracy,
                w.bonus_speed,
                w.ammo_item_id,
                w.ammo_per_use,
                w.created_at
            FROM weapons w
            INNER JOIN items i
                ON i.id = w.item_id
            INNER JOIN weapon_damage_types wdt
                ON wdt.id = w.weapon_damage_type_id
            WHERE w.id = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $weaponId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findAllWithItemAndDamageType(): array
    {
        $sql = "
            SELECT
                w.id,
                w.item_id,
                i.name AS item_name,
                i.description AS item_description,
                w.weapon_damage_type_id,
                wdt.name AS damage_type,
                w.dice_formula,
                w.base_damage,
                w.bonus_accuracy,
                w.range,
                w.bonus_speed,
                w.ammo_item_id,
                w.ammo_per_use,
                w.created_at
            FROM weapons w
            INNER JOIN items i
                ON i.id = w.item_id
            INNER JOIN weapon_damage_types wdt
                ON wdt.id = w.weapon_damage_type_id
            ORDER BY i.name
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existsByAmmoItemId(int $itemId): bool
    {
        $sql = "
        SELECT 1
        FROM {$this->table}
        WHERE ammo_item_id = :item_id
        LIMIT 1
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['item_id' => $itemId]);

        return (bool) $stmt->fetchColumn();
    }

    private function mapToModel(array $row): Weapon
    {
        return new Weapon(
            id: (int) $row['id'],
            item_id: (int) $row['item_id'],
            weapon_damage_type_id: (int) $row['weapon_damage_type_id'],
            dice_formula: $row['dice_formula'],
            base_damage: (int) $row['base_damage'],
            bonus_accuracy: (int) $row['bonus_accuracy'],
            bonus_speed: (int) $row['bonus_speed'],
            range: (int) $row['range'],
            ammo_item_id: $row['ammo_item_id'] !== null ? (int) $row['ammo_item_id'] : null,
            ammo_per_use: (int) $row['ammo_per_use'],
            element_types: [],
            created_at: $row['created_at'] ?? null
        );
    }
}
