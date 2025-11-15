<?php

namespace App\Enums;

/**
 * Kişi Tipi Enum
 *
 * Context7: Type-safe person type enumeration
 * Replaces string-based kisi_tipi field with enum
 *
 * @package App\Enums
 */
enum KisiTipi: string
{
    case ALICI = 'alici';
    case KIRACI = 'kiraci';
    case SATICI = 'satici';
    case EV_SAHIBI = 'ev_sahibi';
    case YATIRIMCI = 'yatirimci';
    case ARACI = 'araci';
    case DANISMAN = 'danisman';

    /**
     * Get human-readable label
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::ALICI => 'Alıcı',
            self::KIRACI => 'Kiracı',
            self::SATICI => 'Satıcı',
            self::EV_SAHIBI => 'Ev Sahibi',
            self::YATIRIMCI => 'Yatırımcı',
            self::ARACI => 'Aracı',
            self::DANISMAN => 'Danışman',
        };
    }

    /**
     * Get description
     *
     * @return string
     */
    public function description(): string
    {
        return match ($this) {
            self::ALICI => 'Gayrimenkul almak isteyen kişi',
            self::KIRACI => 'Gayrimenkul kiralamak isteyen kişi',
            self::SATICI => 'Gayrimenkul satan kişi',
            self::EV_SAHIBI => 'Gayrimenkul sahibi',
            self::YATIRIMCI => 'Yatırım amaçlı gayrimenkul arayan kişi',
            self::ARACI => 'Emlak aracısı',
            self::DANISMAN => 'Gayrimenkul danışmanı',
        };
    }

    /**
     * Get icon/emoji
     *
     * @return string
     */
    public function icon(): string
    {
        return match ($this) {
            self::ALICI => '🏠',
            self::KIRACI => '🔑',
            self::SATICI => '💰',
            self::EV_SAHIBI => '👤',
            self::YATIRIMCI => '📈',
            self::ARACI => '🤝',
            self::DANISMAN => '👔',
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
            self::ALICI => 'blue',
            self::KIRACI => 'green',
            self::SATICI => 'orange',
            self::EV_SAHIBI => 'purple',
            self::YATIRIMCI => 'indigo',
            self::ARACI => 'yellow',
            self::DANISMAN => 'gray',
        };
    }

    /**
     * Check if this person type is a buyer
     *
     * @return bool
     */
    public function isBuyer(): bool
    {
        return in_array($this, [self::ALICI, self::YATIRIMCI]);
    }

    /**
     * Check if this person type is a renter
     *
     * @return bool
     */
    public function isRenter(): bool
    {
        return $this === self::KIRACI;
    }

    /**
     * Check if this person type is a seller
     *
     * @return bool
     */
    public function isSeller(): bool
    {
        return in_array($this, [self::SATICI, self::EV_SAHIBI]);
    }

    /**
     * Check if this person type is a professional
     *
     * @return bool
     */
    public function isProfessional(): bool
    {
        return in_array($this, [self::ARACI, self::DANISMAN]);
    }

    /**
     * Get all values as array
     *
     * @return array
     */
    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    /**
     * Get options for select dropdown
     *
     * @return array
     */
    public static function options(): array
    {
        return array_map(
            fn($case) => [
                'value' => $case->value,
                'label' => $case->label(),
                'icon' => $case->icon(),
                'color' => $case->color(),
            ],
            self::cases()
        );
    }

    /**
     * Create from string (with fallback)
     * Note: PHP enum'larında tryFrom() built-in metodudur ve override edilemez.
     * Bu metod sadece dokümantasyon amaçlıdır.
     * Kullanım: KisiTipi::tryFrom($value) - PHP'nin built-in metodu otomatik null kontrolü yapar.
     *
     * @param string|null $value
     * @return self|null
     */
    // tryFrom() metodunu override etmeye gerek yok - PHP'nin built-in metodu kullanılmalı
}
