<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use App\Domain\Models\EncounterMonster;
use PDO;

class EncounterMonsterRepository extends BaseRepository
{
    protected string $table = 'encounter_monsters';

    public function create(array $data): int
    {
        $sql = "
            INSERT INTO {$this->table}
            (" . implode(', ', array_keys($data)) . ")
            VALUES (:" . implode(', :', array_keys($data)) . ")
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): ?EncounterMonster
    {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE id = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row
            ? $this->mapToModel($row)
            : null;
    }

    public function findByEncounter(int $encounterId): array
    {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE encounter_id = :encounter_id
            ORDER BY id ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'encounter_id' => $encounterId,
        ]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            fn(array $row) => $this->mapToModel($row),
            $rows
        );
    }

    public function updateHp(int $id, int $hp): void
    {
        $sql = "
            UPDATE {$this->table}
            SET current_hp = :hp
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'hp' => $hp,
            'id' => $id,
        ]);
    }

    private function mapToModel(array $row): EncounterMonster
    {
        return new EncounterMonster(
            id: (int) $row['id'],
            encounter_id: (int) $row['encounter_id'],
            monster_id: (int) $row['monster_id'],
            monster_level: (int) $row['monster_level'],
            current_hp: (int) $row['current_hp'],
            max_hp: (int) $row['max_hp'],
            created_at: $row['created_at'] ?? null
        );
    }
}
