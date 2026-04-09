<?php

declare(strict_types=1);

function formatPhone(string $phone): string
{
    $phone = preg_replace('/[^\d+]/', '', $phone);

    if (! $phone) {
        return '';
    }

    if (str_starts_with($phone, '+39')) {
        $rest = substr($phone, 3);

        if (strlen($rest) >= 9) {
            return '+39 ' .
                substr($rest, 0, 3) . ' ' .
                substr($rest, 3, 3) . ' ' .
                substr($rest, 6);
        }

        return '+39 ' . $rest;
    }

    return $phone;
}
