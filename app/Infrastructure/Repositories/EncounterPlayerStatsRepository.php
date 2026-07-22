<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class EncounterPlayerStatsRepository extends BaseRepository
{
    protected string $table = 'encounter_player_stats';

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

    public function findByEncounterPlayerId(
        int $encounterPlayerId
    ): ?array {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE encounter_player_id = :encounter_player_id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'encounter_player_id' => $encounterPlayerId
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function updateStats(
        int $encounterPlayerId,
        array $data
    ): void {
        if (!$data) {
            return;
        }

        $fields = [];

        foreach (array_keys($data) as $column) {
            $fields[] = "{$column} = :{$column}";
        }

        $sql = "
            UPDATE {$this->table}
            SET " . implode(', ', $fields) . "
            WHERE encounter_player_id = :encounter_player_id
        ";

        $data['encounter_player_id'] = $encounterPlayerId;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
    }
}
