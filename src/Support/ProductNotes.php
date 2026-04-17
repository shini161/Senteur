<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Shared note-bucket helpers for both admin editing and storefront display.
 */
final class ProductNotes
{
    public const GENERAL = 'general';
    public const TOP = 'top';
    public const HEART = 'heart';
    public const LEGACY_MIDDLE = 'middle';
    public const BASE = 'base';

    /**
     * @var list<string>
     */
    public const ORDER = [
        self::GENERAL,
        self::TOP,
        self::HEART,
        self::BASE,
    ];

    /**
     * @var list<string>
     */
    public const PYRAMID_ORDER = [
        self::TOP,
        self::HEART,
        self::BASE,
    ];

    /**
     * Returns an empty note structure keyed by every supported note bucket.
     *
     * @return array{general: array<int, mixed>, top: array<int, mixed>, heart: array<int, mixed>, base: array<int, mixed>}
     */
    public static function emptyBuckets(): array
    {
        return [
            self::GENERAL => [],
            self::TOP => [],
            self::HEART => [],
            self::BASE => [],
        ];
    }

    /**
     * Returns the admin-facing note editor sections and helper copy.
     *
     * @return array<string, array{label: string, description: string}>
     */
    public static function adminStageMeta(): array
    {
        return [
            self::GENERAL => [
                'label' => 'Fragrance Notes',
                'description' => 'Use this when you only have a flat note list and no verified pyramid.',
            ],
            self::TOP => [
                'label' => 'Top notes',
                'description' => 'The first impression and lift that hits immediately.',
            ],
            self::HEART => [
                'label' => 'Heart notes',
                'description' => 'The heart of the perfume once the opening settles.',
            ],
            self::BASE => [
                'label' => 'Base notes',
                'description' => 'The lasting trail and depth left behind on skin.',
            ],
        ];
    }

    /**
     * Returns the customer-facing note group label for a given note bucket.
     */
    public static function displayLabel(string $type): string
    {
        return match ($type) {
            self::GENERAL => 'Fragrance Notes',
            self::TOP => 'Top notes',
            self::HEART, self::LEGACY_MIDDLE => 'Heart notes',
            self::BASE => 'Base notes',
            default => 'Fragrance Notes',
        };
    }

    /**
     * Normalizes legacy database values into the current public bucket names.
     */
    public static function normalizeType(string $type): string
    {
        return $type === self::LEGACY_MIDDLE ? self::HEART : $type;
    }

    /**
     * Returns the database note types that should satisfy a given filter bucket.
     *
     * @return list<string>
     */
    public static function databaseTypesForFilter(string $type): array
    {
        return match ($type) {
            self::HEART => [self::HEART, self::LEGACY_MIDDLE, self::GENERAL],
            self::TOP, self::BASE => [$type, self::GENERAL],
            default => [$type],
        };
    }

    /**
     * Returns the note buckets that should be shown on the storefront.
     *
     * If any pyramid data exists, the storefront prefers those staged notes.
     * Otherwise it falls back to a single flat list of general notes.
     *
     * @param array<string, mixed> $notes
     * @return list<string>
     */
    public static function preferredStorefrontTypes(array $notes): array
    {
        foreach (self::PYRAMID_ORDER as $type) {
            if (! empty($notes[$type])) {
                return self::PYRAMID_ORDER;
            }
        }

        return ! empty($notes[self::GENERAL]) ? [self::GENERAL] : [];
    }
}
