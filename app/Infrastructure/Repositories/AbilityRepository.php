<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use App\Domain\Models\Ability;
use PDO;

class AbilityRepository extends BaseRepository
{
    protected string $table = 'abilities';

    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $params  = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($params)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): ?Ability
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToModel($row) : null;
    }

    public function findByIdWithElements(int $id): ?array
    {
        $ability = $this->findById($id);

        if (!$ability) {
            return null;
        }

        $elementRepo = new AbilityElementTypeRepository();
        $elements = $elementRepo->getElementsByAbility($id);

        return [
            'id' => $ability->id,
            'title' => $ability->title,
            'description' => $ability->description,

            'arcane_title' => $ability->arcane_title,
            'arcane_description' => $ability->arcane_description,

            'mana_cost' => $ability->mana_cost,
            'arcane_mana_cost' => $ability->arcane_mana_cost,

            'dice_formula' => $ability->dice_formula,
            'base_damage' => $ability->base_damage,
            'bonus_speed' => $ability->bonus_speed,

            'required_race_id' => $ability->required_race_id,
            'required_order_id' => $ability->required_order_id,

            'element_types' => $elements,
        ];
    }

    private function mapToModel(array $row): Ability
    {
        return new Ability(
            id: (int) $row['id'],
            title: $row['title'],
            description: $row['description'],
            arcane_title: $row['arcane_title'] ?? null,
            arcane_description: $row['arcane_description'] ?? null,
            mana_cost: (int) $row['mana_cost'],
            arcane_mana_cost: $row['arcane_mana_cost'] !== null
                ? (int) $row['arcane_mana_cost']
                : null,
            dice_formula: $row['dice_formula'] ?? null,
            base_damage: (int) $row['base_damage'],
            bonus_speed: (int) $row['bonus_speed'],
            element_types: [],
            required_race_id: $row['required_race_id'] !== null
                ? (int) $row['required_race_id']
                : null,
            required_order_id: $row['required_order_id'] !== null
                ? (int) $row['required_order_id']
                : null,
            created_at: $row['created_at'] ?? null,
            updated_at: $row['updated_at'] ?? null
        );
    }
}
