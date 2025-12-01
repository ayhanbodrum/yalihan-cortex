<?php

namespace App\Services\Ilan;

class StatusMapper
{
    public static function mapRequestToDb(?string $value): ?string
    {
        if (! $value) {
            return null;
        }
        $map = [
            'active' => 'Aktif',
            'inactive' => 'Pasif',
            'draft' => 'Taslak',
            'pending' => 'Beklemede',
        ];

        return $map[strtolower($value)] ?? $value;
    }

    public static function allowed(): array
    {
        return ['Taslak', 'Aktif', 'Pasif', 'Beklemede'];
    }
}
