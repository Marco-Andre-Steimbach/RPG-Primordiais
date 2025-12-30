<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;

class UserCharacterRepository extends BaseRepository
{
    protected string $table = 'user_characters';

    public function attach(int $userId, int $characterId): void
    {
        $sql = "
            INSERT INTO {$this->table} (user_id, character_id)
            VALUES (:user_id, :character_id)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'character_id' => $characterId,
        ]);
    }

    public function getByUserId(int $userId): array
    {
        $sql = "
            SELECT character_id
            FROM {$this->table}
            WHERE user_id = :user_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return array_map(
            fn ($row) => (int) $row['character_id'],
            $stmt->fetchAll(\PDO::FETCH_ASSOC)
        );
    }
}
