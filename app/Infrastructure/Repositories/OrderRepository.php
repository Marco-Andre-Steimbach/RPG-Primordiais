<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use App\Domain\Models\Order;
use PDO;

class OrderRepository extends BaseRepository
{
    protected string $table = 'orders';

    public function existsByName(string $name): bool
    {
        $sql = "SELECT id FROM {$this->table} WHERE name = :name LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['name' => $name]);

        return (bool) $stmt->fetch();
    }

    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $params = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($params)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): ?Order
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToModel($row) : null;
    }

    private function mapToModel(array $row): Order
    {
        return new Order(
            id: (int) $row['id'],
            name: $row['name'],
            description: $row['description'] ?? null,
            required_race_id: $row['required_race_id'] !== null ? (int) $row['required_race_id'] : null,
            created_at: $row['created_at'] ?? null,
            updated_at: $row['updated_at'] ?? null
        );
    }
}
