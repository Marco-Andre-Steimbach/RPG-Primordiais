<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class PerkFlagRepository extends BaseRepository
{
    protected string $table = 'perk_flags';

    public function createMany(int $perkId, array $flags): void
    {
        $sql = "
            INSERT INTO {$this->table}
            (perk_id, flag_name)
            VALUES (:perk_id, :flag_name)
        ";

        $stmt = $this->db->prepare($sql);

        foreach ($flags as $flag) {
            $stmt->execute([
                'perk_id' => $perkId,
                'flag_name' => $flag,
            ]);
        }
    }

    public function getByPerk(int $perkId): array
    {
        $sql = "
            SELECT flag_name
            FROM {$this->table}
            WHERE perk_id = :perk_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['perk_id' => $perkId]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function attach(int $perkId, string $flag): bool
    {
        $sql = "
            INSERT INTO {$this->table} (perk_id, flag_name)
            VALUES (:perk_id, :flag_name)
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'perk_id' => $perkId,
            'flag_name' => $flag,
        ]);
    }

    public function deleteByPerk(int $perkId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE perk_id = :perk_id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute(['perk_id' => $perkId]);
    }
}
