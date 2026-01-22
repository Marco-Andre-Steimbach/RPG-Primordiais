<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use App\Domain\Models\Campaign;
use PDO;

class CampaignRepository extends BaseRepository
{
    protected string $table = 'campaigns';

    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $params  = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$params})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): ?Campaign
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToModel($row) : null;
    }

    public function findAll(): array
    {
        $sql = "
            SELECT id, name, description, created_by, created_at
            FROM {$this->table}
            ORDER BY created_at DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllByUser(int $userId): array
    {
        $sql = "
            SELECT id, name, description, created_at
            FROM {$this->table}
            WHERE created_by = :user_id
            ORDER BY created_at DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllWithBasicInfo(): array
    {
        $sql = "
            SELECT
                c.id,
                c.name,
                c.description,
                c.created_at,
                u.nickname AS master_name,
                COUNT(cc.id) AS characters_count
            FROM campaigns c
            INNER JOIN users u
                ON u.id = c.master_user_id
            LEFT JOIN campaign_characters cc
                ON cc.campaign_id = c.id
            GROUP BY c.id
            ORDER BY c.created_at DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByUserParticipation(int $userId): array
    {
        $sql = "
        SELECT DISTINCT c.*
        FROM campaigns c
        LEFT JOIN campaign_characters cc
            ON cc.campaign_id = c.id
        WHERE c.created_by = :master_id
           OR cc.user_id = :player_id
        ORDER BY c.created_at DESC
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'master_id' => $userId,
            'player_id' => $userId,
        ]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function mapToModel(array $row): Campaign
    {
        return new Campaign(
            id: (int) $row['id'],
            name: $row['name'],
            description: $row['description'] ?? null,
            created_by: (int) $row['created_by'],
            created_at: $row['created_at'] ?? null,
            updated_at: $row['updated_at'] ?? null
        );
    }
}
