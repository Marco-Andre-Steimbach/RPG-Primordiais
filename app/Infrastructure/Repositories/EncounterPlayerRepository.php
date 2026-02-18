<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class EncounterPlayerRepository extends BaseRepository
{
    protected string $table = 'encounter_players';

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
            ORDER BY id ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['encounter_id' => $encounterId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
