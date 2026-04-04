<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\UserRepository;
use RuntimeException;

class AuthService
{
    public function __construct(
        private UserRepository $users
    ) {}

    public function login(string $email, string $password): array
    {
        $user = $this->users->findByEmail($email);

        if (! $user || ! password_verify($password, $user['password_hash'])) {
            throw new RuntimeException('Invalid credentials');
        }

        return $user;
    }

    public function loginAdmin(string $email, string $password): array
    {
        $user = $this->login($email, $password);

        if (($user['role'] ?? null) !== 'admin') {
            throw new RuntimeException('Invalid credentials');
        }

        return $user;
    }

    public function register(array $data): array
    {
        if ($this->users->existsByEmail($data['email'])) {
            throw new RuntimeException('Email already taken');
        }

        if ($this->users->existsByUsername($data['username'])) {
            throw new RuntimeException('Username already taken');
        }

        $user = $this->users->create([
            'public_id' => $this->generatePublicId(),
            'username' => $data['username'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT)
        ]);

        return $user;
    }

    private function generatePublicId(): string
    {
        return substr(bin2hex(random_bytes(10)), 0, 10);
    }
}
