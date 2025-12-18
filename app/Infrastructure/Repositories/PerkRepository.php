<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use App\Domain\Models\Perk;
use PDO;

class PerkRepository extends BaseRepository
{
    protected string $table = 'perks';

    public function create(array $data): int
    {
        $sql = "
            INSERT INTO {$this->table}
            (name, description, type, mana_cost)
            VALUES (:name, :description, :type, :mana_cost)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'],
            'type' => $data['type'],
            'mana_cost' => $data['mana_cost'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): ?Perk
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Perk(
            id: (int) $row['id'],
            name: $row['name'],
            description: $row['description'],
            type: $row['type'],
            mana_cost: (int) $row['mana_cost'],
            race_id: null,
            order_id: null,
            required_level: 1,
            element_types: [],
            flags: [],
            attributes: [],
            ability: [],
            created_at: $row['created_at'] ?? null,
            updated_at: $row['updated_at'] ?? null
        );
    }

    public function existsByName(string $name): bool
    {
        $sql = "SELECT 1 FROM {$this->table} WHERE name = :name LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['name' => $name]);

        return (bool) $stmt->fetchColumn();
    }
}
