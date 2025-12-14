<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;

class RaceAttributeRepository extends BaseRepository
{
    protected string $table = 'race_attributes';

    public function attachAttributes(int $raceId, array $attributes): void
    {
        $sql = "
            INSERT INTO {$this->table} (race_id, attribute_name, attribute_value)
            VALUES (:race_id, :attribute_name, :attribute_value)
        ";

        $stmt = $this->db->prepare($sql);

        foreach ($attributes as $name => $value) {
            $stmt->execute([
                'race_id' => $raceId,
                'attribute_name' => $name,
                'attribute_value' => $value,
            ]);
        }
    }

    public function getByRace(int $raceId): array
    {
        $sql = "
            SELECT attribute_name, attribute_value
            FROM {$this->table}
            WHERE race_id = :race_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['race_id' => $raceId]);

        return $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
    }
}
