<?php

namespace App\Infrastructure\Repositories;

use App\Core\Database\BaseRepository;
use App\Domain\Models\User;
use PDO;

class UserRepository extends BaseRepository
{
    protected string $table = 'users';

    public function existsByEmail(string $email): bool
    {
        $sql = "SELECT id FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        return (bool) $stmt->fetch();
    }

    public function existsByNickname(?string $nickname): bool
    {
        if ($nickname === null) {
            return false;
        }

        $sql = "SELECT id FROM {$this->table} WHERE nickname = :nickname LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['nickname' => $nickname]);
        return (bool) $stmt->fetch();
    }

    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $params  = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($params)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): ?User
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row ? $this->mapToModel($row) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? $this->mapToModel($data) : null;
    }

    public function findByNickname(string $nickname): ?User
    {
        $sql = "SELECT * FROM {$this->table} WHERE nickname = :nickname LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['nickname' => $nickname]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? $this->mapToModel($data) : null;
    }


    public function findByLogin(string $login): ?User
    {
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            return $this->findByEmail($login);
        }

        return $this->findByNickname($login);
    }

    public function updateUser(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    public function emailInUse(string $email, int $ignoreId): bool
    {
        $sql = "SELECT id FROM {$this->table}
            WHERE email = :email AND id != :id LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'email' => $email,
            'id' => $ignoreId,
        ]);

        return (bool) $stmt->fetch();
    }

    public function nicknameInUse(?string $nickname, int $ignoreId): bool
    {
        if (!$nickname) {
            return false;
        }

        $sql = "SELECT id FROM {$this->table}
            WHERE nickname = :nickname AND id != :id LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'nickname' => $nickname,
            'id' => $ignoreId,
        ]);

        return (bool) $stmt->fetch();
    }

    public function updatePassword(int $id, string $hashedPassword): bool
    {
        $sql = "UPDATE {$this->table} SET password = :password WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'password' => $hashedPassword,
            'id' => $id,
        ]);
    }

    private function mapToModel(array $row): User
    {
        return new User(
            id: (int) $row['id'],
            first_name: $row['first_name'],
            last_name: $row['last_name'],
            nickname: $row['nickname'],
            email: $row['email'],
            password: $row['password'],
            created_at: $row['created_at'] ?? null,
            updated_at: $row['updated_at'] ?? null
        );
    }

}
