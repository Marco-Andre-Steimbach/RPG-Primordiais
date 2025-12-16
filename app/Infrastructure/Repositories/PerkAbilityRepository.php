<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;

class PerkAbilityRepository extends BaseRepository
{
    protected string $table = 'perk_abilities';

    public function create(int $perkId, array $data): int
    {
        $sql = "
            INSERT INTO {$this->table} (
                perk_id,
                name,
                description,
                dice_formula,
                base_damage,
                bonus_accuracy,
                bonus_damage,
                bonus_speed
            ) VALUES (
                :perk_id,
                :name,
                :description,
                :dice_formula,
                :base_damage,
                :bonus_accuracy,
                :bonus_damage,
                :bonus_speed
            )
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'perk_id'        => $perkId,
            'name'           => $data['name'],
            'description'    => $data['description'],
            'dice_formula'   => $data['dice_formula'] ?? null,
            'base_damage'    => $data['base_damage'] ?? 0,
            'bonus_accuracy' => $data['bonus_accuracy'] ?? 0,
            'bonus_damage'   => $data['bonus_damage'] ?? 0,
            'bonus_speed'    => $data['bonus_speed'] ?? 0,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function findByPerk(int $perkId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE perk_id = :perk_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['perk_id' => $perkId]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
