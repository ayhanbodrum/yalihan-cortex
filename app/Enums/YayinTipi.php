<?php

namespace App\Enums;

/**
 * Yayın Tipi Enum
 *
 * Context7: Type-safe publication type enumeration
 * Replaces string-based yayin_tipi field with enum
 *
 * @package App\Enums
 */
enum YayinTipi: string
{
    case SATILIK = 'satilik';
    case KIRALIK = 'kiralik';
    case DEVREN = 'devren';
    case GUNLUK_KIRALIK = 'gunluk_kiralik';

    /**
     * Get human-readable label
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::SATILIK => 'Satılık',
            self::KIRALIK => 'Kiralık',
            self::DEVREN => 'Devren',
            self::GUNLUK_KIRALIK => 'Günlük Kiralık',
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
            self::SATILIK => 'Satış amaçlı ilan',
            self::KIRALIK => 'Kiralama amaçlı ilan',
            self::DEVREN => 'İşyeri devir',
            self::GUNLUK_KIRALIK => 'Yazlık/günlük kiralama',
        };
    }

    /**
     * Get icon
     *
     * @return string
     */
    public function icon(): string
    {
        return match ($this) {
            self::SATILIK => '💰',
            self::KIRALIK => '🔑',
            self::DEVREN => '🔄',
            self::GUNLUK_KIRALIK => '🏖️',
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
            self::SATILIK => 'blue',
            self::KIRALIK => 'green',
            self::DEVREN => 'orange',
            self::GUNLUK_KIRALIK => 'purple',
        };
    }

    /**
     * Check if this is a sale type
     *
     * @return bool
     */
    public function isSale(): bool
    {
        return in_array($this, [self::SATILIK, self::DEVREN]);
    }

    /**
     * Check if this is a rental type
     *
     * @return bool
     */
    public function isRental(): bool
    {
        return in_array($this, [self::KIRALIK, self::GUNLUK_KIRALIK]);
    }

    /**
     * Check if this requires daily pricing
     *
     * @return bool
     */
    public function requiresDailyPricing(): bool
    {
        return $this === self::GUNLUK_KIRALIK;
    }

    /**
     * Check if this requires transfer fee
     *
     * @return bool
     */
    public function requiresTransferFee(): bool
    {
        return $this === self::DEVREN;
    }

    /**
     * Get price label
     *
     * @return string
     */
    public function priceLabel(): string
    {
        return match ($this) {
            self::SATILIK => 'Satış Fiyatı',
            self::KIRALIK => 'Aylık Kira',
            self::DEVREN => 'Devir Bedeli',
            self::GUNLUK_KIRALIK => 'Günlük Fiyat',
        };
    }

    /**
     * Get contract type
     *
     * @return string
     */
    public function contractType(): string
    {
        return match ($this) {
            self::SATILIK => 'Satış Sözleşmesi',
            self::KIRALIK => 'Kira Sözleşmesi',
            self::DEVREN => 'Devir Sözleşmesi',
            self::GUNLUK_KIRALIK => 'Günlük Kiralama Sözleşmesi',
        };
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
                'description' => $case->description(),
            ],
            self::cases()
        );
    }

    /**
     * Create from string (with fallback)
     *
     * @param string|null $value
     * @return self|null
     */
    public static function tryFrom(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }

        // Enum'lar built-in tryFrom() metoduna sahip, parent kullanılamaz
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }

        return null;
    }
}
