<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use App\Domain\Models\Encounter;
use PDO;

class EncounterRepository extends BaseRepository
{
    protected string $table = 'encounters';

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

    public function findById(int $id): ?Encounter
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToModel($row) : null;
    }

    public function updateStatus(int $id, string $status): void
    {
        $sql = "
        UPDATE {$this->table}
        SET status = :status
        WHERE id = :id
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'status' => $status,
            'id'     => $id,
        ]);
    }

    public function findAllBasic(
    int $campaignId,
    ?string $status = null
): array {
    $sql = "
        SELECT
            id,
            name,
            status
        FROM {$this->table}
        WHERE campaign_id = :campaign_id
    ";

    $params = [
        'campaign_id' => $campaignId
    ];

    if ($status !== null) {
        $sql .= "
            AND status = :status
        ";

        $params['status'] = $status;
    }

    $sql .= "
        ORDER BY
            FIELD(
                status,
                'active',
                'pending',
                'finished'
            ),
            created_at DESC
    ";

    $stmt = $this->db->prepare($sql);

    $stmt->execute($params);

    return $stmt->fetchAll(
        \PDO::FETCH_ASSOC
    );
}


    private function mapToModel(array $row): Encounter
    {
        return new Encounter(
            id: (int) $row['id'],
            campaign_id: (int) $row['campaign_id'],
            name: $row['name'],
            description: $row['description'],
            status: $row['status'],
            created_at: $row['created_at'] ?? null
        );
    }
}
