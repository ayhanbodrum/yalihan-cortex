<?php

namespace App\Enums;

enum TalepStatus: string
{
    case AKTIF = 'Aktif';
    case BEKLEMEDE = 'Beklemede';
    case IPTAL = 'İptal';
    case TAMAMLANDI = 'Tamamlandı';
    case ACIL = 'Acil';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return [
            ['value' => self::AKTIF->value, 'label' => 'Aktif'],
            ['value' => self::BEKLEMEDE->value, 'label' => 'Beklemede'],
            ['value' => self::IPTAL->value, 'label' => 'İptal'],
            ['value' => self::TAMAMLANDI->value, 'label' => 'Tamamlandı'],
            ['value' => self::ACIL->value, 'label' => 'Acil'],
        ];
    }
}