<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use App\Domain\Models\ElementType;
use PDO;

class ElementTypeRepository extends BaseRepository
{
    protected string $table = 'element_types';

    public function findAll(): array
    {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?ElementType
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToModel($row) : null;
    }

    public function getRelationsByIds(array $sourceIds, array $targetIds): array
    {
        if (empty($sourceIds) || empty($targetIds)) {
            return [];
        }

        $sourcePlaceholders = implode(',', array_fill(0, count($sourceIds), '?'));
        $targetPlaceholders = implode(',', array_fill(0, count($targetIds), '?'));

        $sql = "
        SELECT 
            source_element_id,
            target_element_id,
            relation_type,
            modifier
        FROM element_type_relations
        WHERE source_element_id IN ($sourcePlaceholders)
          AND target_element_id IN ($targetPlaceholders)
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([...$sourceIds, ...$targetIds]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    private function mapToModel(array $row): ElementType
    {
        return new ElementType(
            id: (int) $row['id'],
            name: $row['name'],
            description: $row['description'],
            created_at: $row['created_at'] ?? null
        );
    }
}
