<?php

namespace App\Enums;

enum PipelineStage: int
{
    case YENI_LEAD = 1;
    case ILETISIM_KURULDU = 2;
    case TEKLIF_VERILDI = 3;
    case GORUSME_YAPILDI = 4;
    case KAZANILDI = 5;
    case KAYBEDILDI = 0;

    public function label(): string
    {
        return match ($this) {
            self::YENI_LEAD => 'Yeni Lead',
            self::ILETISIM_KURULDU => 'İletişim Kuruldu',
            self::TEKLIF_VERILDI => 'Teklif Verildi',
            self::GORUSME_YAPILDI => 'Görüşme Yapıldı',
            self::KAZANILDI => 'Kazanıldı',
            self::KAYBEDILDI => 'Kaybedildi',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::YENI_LEAD => 'gray',
            self::ILETISIM_KURULDU => 'blue',
            self::TEKLIF_VERILDI => 'yellow',
            self::GORUSME_YAPILDI => 'purple',
            self::KAZANILDI => 'green',
            self::KAYBEDILDI => 'red',
        };
    }

    public function next(): ?self
    {
        return match ($this) {
            self::YENI_LEAD => self::ILETISIM_KURULDU,
            self::ILETISIM_KURULDU => self::TEKLIF_VERILDI,
            self::TEKLIF_VERILDI => self::GORUSME_YAPILDI,
            self::GORUSME_YAPILDI => self::KAZANILDI,
            default => null,
        };
    }
}
