<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use App\Domain\Models\Monster;
use PDO;

class MonsterRepository extends BaseRepository
{
    protected string $table = 'monsters';

    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $params  = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($params)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): ?Monster
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToModel($row) : null;
    }

    public function existsByName(string $name): bool
    {
        $sql = "SELECT 1 FROM {$this->table} WHERE name = :name LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['name' => $name]);

        return (bool) $stmt->fetchColumn();
    }

    private function mapToModel(array $row): Monster
    {
        return new Monster(
            id: (int) $row['id'],
            name: $row['name'],
            description: $row['description'] ?? null,
            base_hp: (int) $row['base_hp'],
            base_ac: (int) $row['base_ac'],
            base_speed: (int) $row['base_speed'],
            actions_per_turn: (int) $row['actions_per_turn'],
            base_str: (int) $row['base_str'],
            base_dex: (int) $row['base_dex'],
            base_con: (int) $row['base_con'],
            base_wis: (int) $row['base_wis'],
            base_int: (int) $row['base_int'],
            created_at: $row['created_at'] ?? null,
            updated_at: $row['updated_at'] ?? null
        );
    }
}
