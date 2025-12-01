<?php

namespace App\Enums;

/**
 * Kişi CRM Status Enum
 *
 * Context7: Type-safe CRM status enumeration
 * Replaces string-based crm_status field with enum
 */
enum KisiStatus: string
{
    case SICAK = 'sicak';
    case ILGILI = 'ilgili';
    case TAKIPTE = 'takipte';
    case SOGUK = 'soguk';
    case PASIF = 'pasif';
    case POTANSIYEL = 'potansiyel';
    case MUSTERI = 'musteri';

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match ($this) {
            self::SICAK => 'Sıcak',
            self::ILGILI => 'İlgili',
            self::TAKIPTE => 'Takipte',
            self::SOGUK => 'Soğuk',
            self::PASIF => 'Pasif',
            self::POTANSIYEL => 'Potansiyel',
            self::MUSTERI => 'Müşteri',
        };
    }

    /**
     * Get description
     */
    public function description(): string
    {
        return match ($this) {
            self::SICAK => 'Yüksek satış potansiyeli olan, aktif ilgilenen müşteri',
            self::ILGILI => 'İlgileniyor, takip edilmeli',
            self::TAKIPTE => 'Aktif takip sürecinde',
            self::SOGUK => 'Düşük ilgi gösteren, pasif müşteri',
            self::PASIF => 'Pasif durumda, takip edilmiyor',
            self::POTANSIYEL => 'Potansiyel müşteri, henüz aktif değil',
            self::MUSTERI => 'Aktif müşteri, işlem yapmış',
        };
    }

    /**
     * Get icon/emoji
     */
    public function icon(): string
    {
        return match ($this) {
            self::SICAK => '🔥',
            self::ILGILI => '👀',
            self::TAKIPTE => '📞',
            self::SOGUK => '❄️',
            self::PASIF => '😴',
            self::POTANSIYEL => '💡',
            self::MUSTERI => '✅',
        };
    }

    /**
     * Get color for UI
     *
     * @return string Tailwind CSS color class
     */
    public function color(): string
    {
        return match ($this) {
            self::SICAK => 'red',
            self::ILGILI => 'orange',
            self::TAKIPTE => 'blue',
            self::SOGUK => 'gray',
            self::PASIF => 'slate',
            self::POTANSIYEL => 'yellow',
            self::MUSTERI => 'green',
        };
    }

    /**
     * Check if this status is active (requires follow-up)
     */
    public function isActive(): bool
    {
        return in_array($this, [self::SICAK, self::ILGILI, self::TAKIPTE, self::POTANSIYEL, self::MUSTERI]);
    }

    /**
     * Check if this status requires immediate attention
     */
    public function requiresAttention(): bool
    {
        return in_array($this, [self::SICAK, self::ILGILI, self::TAKIPTE]);
    }

    /**
     * Get all values as array
     */
    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }

    /**
     * Get options for select dropdown
     */
    public static function options(): array
    {
        return array_map(
            fn ($case) => [
                'value' => $case->value,
                'label' => $case->label(),
                'icon' => $case->icon(),
                'color' => $case->color(),
            ],
            self::cases()
        );
    }
}
