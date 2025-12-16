<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class RacePerkRepository extends BaseRepository
{
    protected string $table = 'race_perks';

    public function attachPerk(
        int $raceId,
        int $perkId,
        int $requiredLevel = 1
    ): bool {
        $sql = "
            INSERT INTO {$this->table}
            (race_id, perk_id, required_level)
            VALUES (:race_id, :perk_id, :required_level)
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'race_id' => $raceId,
            'perk_id' => $perkId,
            'required_level' => $requiredLevel,
        ]);
    }

    public function exists(
        int $raceId,
        int $perkId
    ): bool {
        $sql = "
            SELECT 1
            FROM {$this->table}
            WHERE race_id = :race_id
              AND perk_id = :perk_id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'race_id' => $raceId,
            'perk_id' => $perkId,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function getPerksByRace(int $raceId): array
    {
        $sql = "
            SELECT perk_id, required_level
            FROM {$this->table}
            WHERE race_id = :race_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['race_id' => $raceId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
