<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use App\Domain\Models\MonsterAbility;
use PDO;

class MonsterAbilityRepository extends BaseRepository
{
    protected string $table = 'monster_abilities';

    public function create(array $data): int
    {
        $sql = "
            INSERT INTO {$this->table}
            (title, description, dice_formula, base_damage, bonus_damage, bonus_speed)
            VALUES (:title, :description, :dice_formula, :base_damage, :bonus_damage, :bonus_speed)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'],
            'dice_formula' => $data['dice_formula'],
            'base_damage' => $data['base_damage'],
            'bonus_damage' => $data['bonus_damage'],
            'bonus_speed' => $data['bonus_speed'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): ?MonsterAbility
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new MonsterAbility(
            id: (int) $row['id'],
            title: $row['title'],
            description: $row['description'],
            dice_formula: $row['dice_formula'],
            base_damage: (int) $row['base_damage'],
            bonus_damage: (int) $row['bonus_damage'],
            bonus_speed: (int) $row['bonus_speed'],
            created_at: $row['created_at'] ?? null
        );
    }

    public function findManyByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $sql = "SELECT * FROM {$this->table} WHERE id IN ($placeholders)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($ids);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function existsByTitle(string $title): bool
    {
        $sql = "SELECT 1 FROM {$this->table} WHERE title = :title LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['title' => $title]);

        return (bool) $stmt->fetchColumn();
    }
}
