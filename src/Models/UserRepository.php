<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class UserRepository
{
    public function __construct(
        private ?PDO $pdo = null
    ) {
        $this->pdo ??= Database::getConnection();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM users
            WHERE email = :email
            LIMIT 1
        ");

        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function create(array $data): array
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO users (
                public_id,
                username,
                email,
                phone,
                password_hash
            ) VALUES (:public_id, :username, :email, :phone, :password_hash)
        ");

        $stmt->execute([
            'public_id' => $data['public_id'],
            'username' => $data['username'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password_hash' => $data['password_hash']
        ]);

        return $this->findByEmail($data['email']);
    }

    public function existsByEmail(string $email): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT 1
            FROM users
            WHERE email = :email
            LIMIT 1
        ");

        $stmt->execute(['email' => $email]);

        return (bool) $stmt->fetchColumn();
    }

    public function existsByUsername(string $username): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT 1
            FROM users
            WHERE username = :username
            LIMIT 1
        ");

        $stmt->execute(['username' => $username]);

        return (bool) $stmt->fetchColumn();
    }
}
