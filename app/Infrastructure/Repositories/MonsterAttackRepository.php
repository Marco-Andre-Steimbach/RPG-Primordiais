<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use App\Domain\Models\MonsterAttack;
use PDO;

class MonsterAttackRepository extends BaseRepository
{
    protected string $table = 'monster_attacks';

    public function create(array $data): int
    {
        $sql = "
            INSERT INTO {$this->table}
            (name, description, dice_formula, base_damage, bonus_accuracy)
            VALUES (:name, :description, :dice_formula, :base_damage, :bonus_accuracy)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'],
            'dice_formula' => $data['dice_formula'],
            'base_damage' => $data['base_damage'],
            'bonus_accuracy' => $data['bonus_accuracy'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): ?MonsterAttack
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

    private function mapToModel(array $row): MonsterAttack
    {
        return new MonsterAttack(
            id: (int) $row['id'],
            name: $row['name'],
            description: $row['description'] ?? null,
            dice_formula: $row['dice_formula'],
            base_damage: (int) $row['base_damage'],
            bonus_accuracy: (int) $row['bonus_accuracy'],
            element_types: [],
            created_at: $row['created_at'] ?? null
        );
    }
}
