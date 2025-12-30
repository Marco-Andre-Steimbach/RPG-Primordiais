<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class CharacterAbilityRepository extends BaseRepository
{
    protected string $table = 'character_abilities';

    public function attach(int $characterId, int $abilityId): void
    {
        if ($this->exists($characterId, $abilityId)) {
            return;
        }

        $sql = "
            INSERT INTO {$this->table} (character_id, ability_id)
            VALUES (:character_id, :ability_id)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'character_id' => $characterId,
            'ability_id' => $abilityId,
        ]);
    }

    public function exists(int $characterId, int $abilityId): bool
    {
        $sql = "
            SELECT 1
            FROM {$this->table}
            WHERE character_id = :character_id
              AND ability_id = :ability_id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'character_id' => $characterId,
            'ability_id' => $abilityId,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function getAbilitiesByCharacter(int $characterId): array
    {
        $sql = "
            SELECT ability_id
            FROM {$this->table}
            WHERE character_id = :character_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'character_id' => $characterId,
        ]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
