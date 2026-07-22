<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class EncounterInitiativeRepository extends BaseRepository
{
    protected string $table = 'encounters_initiative';

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

    public function findByEncounter(int $encounterId): array
    {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE encounter_id = :encounter_id
            ORDER BY initiative_value DESC, id ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'encounter_id' => $encounterId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByEncounterId(int $encounterId): array
    {
        return $this->findByEncounter($encounterId);
    }

    public function existsForTarget(
        int $encounterId,
        ?int $monsterId,
        ?int $playerId
    ): bool {
        if ($monsterId !== null) {
            $sql = "
                SELECT 1
                FROM {$this->table}
                WHERE encounter_id = :encounter_id
                  AND encounter_monster_id = :monster_id
                LIMIT 1
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'encounter_id' => $encounterId,
                'monster_id' => $monsterId,
            ]);
        } else {
            $sql = "
                SELECT 1
                FROM {$this->table}
                WHERE encounter_id = :encounter_id
                  AND encounter_player_id = :player_id
                LIMIT 1
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'encounter_id' => $encounterId,
                'player_id' => $playerId,
            ]);
        }

        return (bool) $stmt->fetchColumn();
    }

    public function updateValue(int $id, int $value): void
    {
        $sql = "
            UPDATE {$this->table}
            SET initiative_value = :value
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'value' => $value,
            'id' => $id,
        ]);
    }
}
