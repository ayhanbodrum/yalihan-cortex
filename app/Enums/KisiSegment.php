<?php

namespace App\Enums;

enum KisiSegment: string
{
    case POTANSIYEL = 'potansiyel';
    case AKTIF = 'aktif';
    case ESKI = 'eski';
    case VIP = 'vip';

    public function label(): string
    {
        return match ($this) {
            self::POTANSIYEL => 'Potansiyel Müşteri',
            self::AKTIF => 'Aktif Müşteri',
            self::ESKI => 'Eski Müşteri',
            self::VIP => 'VIP Müşteri',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::POTANSIYEL => 'gray',
            self::AKTIF => 'green',
            self::ESKI => 'orange',
            self::VIP => 'purple',
        };
    }
}
