<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use PDO;

class UserRoleRepository extends BaseRepository
{
    protected string $table = 'user_roles';

    public function attachRole(int $userId, int $roleId): bool
    {
        $sql = "INSERT IGNORE INTO {$this->table} (user_id, role_id) VALUES (:user_id, :role_id)";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'user_id' => $userId,
            'role_id' => $roleId
        ]);
    }

    public function getRolesByUser(int $userId): array
    {
        $sql = "SELECT role_id FROM {$this->table} WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return array_map('intval', $stmt->fetchAll(\PDO::FETCH_COLUMN));
    }

    public function getRoleNamesByUser(int $userId): array
    {
        $sql = "
            SELECT roles.role_name
            FROM {$this->table}
            INNER JOIN roles ON roles.id = {$this->table}.role_id
            WHERE {$this->table}.user_id = :user_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function getRoleIdByName(string $role): ?int
    {
        $sql = "SELECT id FROM roles WHERE role_name = :role LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['role' => $role]);

        $id = $stmt->fetchColumn();

        return $id ? (int) $id : null;
    }
}
