<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use App\Domain\Models\Character;
use PDO;

class CharacterRepository extends BaseRepository
{
    protected string $table = 'characters';

    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $params  = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($params)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): ?Character
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToModel($row) : null;
    }

public function findAllBasic(): array
{
    $sql = "
        SELECT
            c.id,
            c.name,
            c.race_id,
            c.order_id,
            u.nickname AS created_by
        FROM {$this->table} c
        INNER JOIN users u ON u.id = c.created_by
        ORDER BY c.name
    ";

    return $this->db
        ->query($sql)
        ->fetchAll(PDO::FETCH_ASSOC);
}


    public function findByUser(int $userId): array
    {
        $sql = "
            SELECT id, name, race_id, order_id
            FROM {$this->table}
            WHERE created_by = :user_id
            ORDER BY name
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByIdWithAbilities(int $characterId): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $characterId]);

        $character = $stmt->fetch(PDO::FETCH_ASSOC);

        return $character ?: null;
    }

    private function mapToModel(array $row): Character
    {
        return new Character(
            id: (int) $row['id'],
            name: $row['name'],
            description: $row['description'] ?? null,
            race_id: $row['race_id'] ? (int) $row['race_id'] : null,
            order_id: $row['order_id'] ? (int) $row['order_id'] : null,
            mana_modifier: $row['mana_modifier'],
            created_by: $row['created_by'] ?? null,
            created_at: $row['created_at'] ?? null,
            updated_at: $row['updated_at'] ?? null
        );
    }
}
