<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use App\Domain\Models\Item;
use PDO;

class ItemRepository extends BaseRepository
{
    protected string $table = 'items';

    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $params  = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($params)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): ?Item
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

    public function existsById(int $id): bool
    {
        $sql = "SELECT 1 FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return (bool) $stmt->fetchColumn();
    }

    private function mapToModel(array $row): Item
    {
        return new Item(
            id: (int) $row['id'],
            name: $row['name'],
            description: $row['description'] ?? null,
            value: (int) $row['value'],
            element_types: [],
            item_abilities: [],
            created_at: $row['created_at'] ?? null,
            updated_at: $row['updated_at'] ?? null
        );
    }

    public function findAllBasic(): array
    {
        $sql = "
            SELECT id, name, description, value
            FROM {$this->table}
            ORDER BY id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function findAllNonEquipable(): array
    {
        $sql = "
        SELECT
            i.id,
            i.name,
            i.description,
            i.created_at
        FROM items i
        LEFT JOIN weapons w ON w.item_id = i.id
        LEFT JOIN armors a ON a.item_id = i.id
        WHERE w.id IS NULL
          AND a.id IS NULL
        ORDER BY i.name
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
