<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AddressRepository;
use RuntimeException;

/**
 * Owns validation and default-address rules for user shipping addresses.
 */
class AddressService
{
    public function __construct(
        private AddressRepository $addresses
    ) {}

    /**
     * Returns every saved address for a user ordered by default/newest first.
     */
    public function getAllForUser(int $userId): array
    {
        return $this->addresses->findByUserId($userId);
    }

    /**
     * Validates and creates a new address while maintaining the single-default rule.
     */
    public function createForUser(int $userId, array $data): void
    {
        $fullName = trim($data['full_name'] ?? '');
        $addressLine = trim($data['address_line'] ?? '');
        $city = trim($data['city'] ?? '');
        $postalCode = trim($data['postal_code'] ?? '');
        $country = trim($data['country'] ?? '');
        $isDefault = ! empty($data['is_default']);

        if (
            $fullName === '' ||
            $addressLine === '' ||
            $city === '' ||
            $postalCode === '' ||
            $country === ''
        ) {
            throw new RuntimeException('All address fields are required.');
        }

        if (mb_strlen($fullName) > 150) {
            throw new RuntimeException('Full name is too long.');
        }

        if (mb_strlen($addressLine) > 255) {
            throw new RuntimeException('Address line is too long.');
        }

        if (mb_strlen($city) > 100) {
            throw new RuntimeException('City is too long.');
        }

        if (mb_strlen($postalCode) > 20) {
            throw new RuntimeException('Postal code is too long.');
        }

        if (mb_strlen($country) > 100) {
            throw new RuntimeException('Country is too long.');
        }

        if ($this->addresses->countByUserId($userId) >= 10) {
            throw new RuntimeException('You can save up to 10 addresses. Delete one to add another.');
        }

        // The first address, or any explicitly default address, becomes the
        // user's sole default shipping destination.
        if ($isDefault || ! $this->addresses->hasAnyForUser($userId)) {
            $this->addresses->clearDefaultForUser($userId);
            $isDefault = true;
        }

        $this->addresses->create([
            'user_id' => $userId,
            'full_name' => $fullName,
            'address_line' => $addressLine,
            'city' => $city,
            'postal_code' => $postalCode,
            'country' => $country,
            'is_default' => $isDefault,
        ]);
    }

    /**
     * Deletes one address and reassigns the default flag when needed.
     */
    public function deleteForUser(int $id, int $userId): void
    {
        $address = $this->addresses->findByIdForUser($id, $userId);

        if (! $address) {
            return;
        }

        $wasDefault = (bool) $address['is_default'];

        $this->addresses->delete($id, $userId);

        if ($wasDefault) {
            $next = $this->addresses->findAnyForUser($userId);

            if ($next) {
                $this->addresses->clearDefaultForUser($userId);
                $this->addresses->setDefault((int) $next['id'], $userId);
            }
        }
    }

    /**
     * Makes one of the user's addresses the default choice for checkout.
     */
    public function setDefaultForUser(int $id, int $userId): void
    {
        $address = $this->addresses->findByIdForUser($id, $userId);

        if (! $address) {
            throw new RuntimeException('Address not found.');
        }

        $this->addresses->clearDefaultForUser($userId);
        $this->addresses->setDefault($id, $userId);
    }
}
