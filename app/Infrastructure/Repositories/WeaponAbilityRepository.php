<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use App\Domain\Models\WeaponAbility;
use PDO;

class WeaponAbilityRepository extends BaseRepository
{
    protected string $table = 'weapon_abilities';

    public function create(array $data): int
    {
        $columns = implode(', ', array_map(
            fn($col) => "`$col`",
            array_keys($data)
        ));

        $params = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($params)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): ?WeaponAbility
    {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE id = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToModel($row) : null;
    }

    public function findByWeaponId(int $weaponId): array
    {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE weapon_id = :weapon_id
            ORDER BY id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['weapon_id' => $weaponId]);

        return array_map(
            fn($row) => $this->mapToModel($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    private function mapToModel(array $row): WeaponAbility
    {
        return new WeaponAbility(
            id: (int) $row['id'],
            weapon_id: (int) $row['weapon_id'],
            title: $row['title'],
            description: $row['description'],
            dice_formula: $row['dice_formula'] ?? null,
            range: $row['range'] ?? 1,
            base_damage: (int) $row['base_damage'],
            bonus_damage: (int) $row['bonus_damage'],
            bonus_accuracy: (int) $row['bonus_accuracy'],
            bonus_speed: (int) $row['bonus_speed'],
            element_types: [],
            created_at: $row['created_at'] ?? null
        );
    }
}
