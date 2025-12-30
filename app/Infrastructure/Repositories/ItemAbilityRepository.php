<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use App\Domain\Models\ItemAbility;
use PDO;

class ItemAbilityRepository extends BaseRepository
{
    protected string $table = 'item_abilities';

    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $params  = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($params)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): ?ItemAbility
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToModel($row) : null;
    }

    private function mapToModel(array $row): ItemAbility
    {
        return new ItemAbility(
            id: (int) $row['id'],
            title: $row['title'],
            description: $row['description'],
            dice_formula: $row['dice_formula'] ?? null,
            base_damage: (int) $row['base_damage'],
            bonus_damage: (int) $row['bonus_damage'],
            bonus_accuracy: (int) $row['bonus_accuracy'],
            bonus_speed: (int) $row['bonus_speed'],
            is_consumable: (bool) $row['is_consumable'],
            max_uses: $row['max_uses'] !== null ? (int) $row['max_uses'] : null,
            override_element_type_id: $row['override_element_type_id'] !== null
                ? (int) $row['override_element_type_id']
                : null,
            created_at: $row['created_at'] ?? null,
            updated_at: $row['updated_at'] ?? null
        );
    }

    public function attach(int $itemId, int $abilityId): void
    {
        $sql = "
            INSERT INTO item_item_abilities (item_id, item_ability_id)
            VALUES (:item_id, :item_ability_id)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'item_id' => $itemId,
            'item_ability_id' => $abilityId,
        ]);
    }

    public function getByItemId(int $itemId): array
    {
        $sql = "
            SELECT item_ability_id
            FROM item_item_abilities
            WHERE item_id = :item_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['item_id' => $itemId]);

        return array_map(
            fn ($row) => (int) $row['item_ability_id'],
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }
}
