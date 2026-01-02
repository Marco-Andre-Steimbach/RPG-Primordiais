<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use App\Domain\Models\ArmorAbility;
use PDO;

class ArmorAbilityRepository extends BaseRepository
{
    protected string $table = 'armor_abilities';

    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
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

    public function findById(int $id): ?ArmorAbility
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToModel($row) : null;
    }

    public function getByArmorId(int $armorId): array
    {
        $sql = "
        SELECT a.*
        FROM {$this->table} aa
        INNER JOIN abilities a ON a.id = aa.ability_id
        WHERE aa.armor_id = :id
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $armorId]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    private function mapToModel(array $row): ArmorAbility
    {
        return new ArmorAbility(
            id: (int) $row['id'],
            title: $row['title'],
            description: $row['description'],
            dice_formula: $row['dice_formula'] ?? null,
            base_damage: (int) $row['base_damage'],
            armor_class_bonus: (int) $row['armor_class_bonus'],
            bonus_speed: (int) $row['bonus_speed'],
            created_at: $row['created_at'] ?? null
        );
    }
}
