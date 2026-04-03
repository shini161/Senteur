<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class AddressRepository
{
    public function __construct(
        private ?PDO $pdo = null
    ) {
        $this->pdo ??= Database::getConnection();
    }

    public function findByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                id,
                user_id,
                full_name,
                address_line,
                city,
                postal_code,
                country,
                is_default,
                created_at
            FROM user_addresses
            WHERE user_id = :user_id
            ORDER BY is_default DESC, created_at DESC, id DESC
        ");

        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll();
    }

    public function findByIdForUser(int $id, int $userId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                id,
                user_id,
                full_name,
                address_line,
                city,
                postal_code,
                country,
                is_default,
                created_at
            FROM user_addresses
            WHERE id = :id
              AND user_id = :user_id
            LIMIT 1
        ");

        $stmt->execute([
            'id' => $id,
            'user_id' => $userId,
        ]);

        $address = $stmt->fetch();

        return $address ?: null;
    }

    public function create(array $data): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO user_addresses (
                user_id,
                full_name,
                address_line,
                city,
                postal_code,
                country,
                is_default
            ) VALUES (
                :user_id,
                :full_name,
                :address_line,
                :city,
                :postal_code,
                :country,
                :is_default
            )
        ");

        $stmt->execute([
            'user_id' => $data['user_id'],
            'full_name' => $data['full_name'],
            'address_line' => $data['address_line'],
            'city' => $data['city'],
            'postal_code' => $data['postal_code'],
            'country' => $data['country'],
            'is_default' => $data['is_default'] ? 1 : 0,
        ]);
    }

    public function delete(int $id, int $userId): void
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM user_addresses
            WHERE id = :id
              AND user_id = :user_id
        ");

        $stmt->execute([
            'id' => $id,
            'user_id' => $userId,
        ]);
    }

    public function clearDefaultForUser(int $userId): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE user_addresses
            SET is_default = 0
            WHERE user_id = :user_id
        ");

        $stmt->execute(['user_id' => $userId]);
    }

    public function setDefault(int $id, int $userId): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE user_addresses
            SET is_default = 1
            WHERE id = :id
              AND user_id = :user_id
        ");

        $stmt->execute([
            'id' => $id,
            'user_id' => $userId,
        ]);
    }

    public function findAnyForUser(int $userId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                id,
                user_id,
                full_name,
                address_line,
                city,
                postal_code,
                country,
                is_default,
                created_at
            FROM user_addresses
            WHERE user_id = :user_id
            ORDER BY created_at ASC, id ASC
            LIMIT 1
        ");

        $stmt->execute(['user_id' => $userId]);
        $address = $stmt->fetch();

        return $address ?: null;
    }

    public function hasAnyForUser(int $userId): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT 1
            FROM user_addresses
            WHERE user_id = :user_id
            LIMIT 1
        ");

        $stmt->execute(['user_id' => $userId]);

        return (bool) $stmt->fetchColumn();
    }
}
