<?php

namespace App\Enums;

/**
 * Ana Kategori Enum
 *
 * Context7: Type-safe main category enumeration
 *
 * @package App\Enums
 */
enum AnaKategori: string
{
    case KONUT = 'konut';
    case ISYERI = 'isyeri';
    case ARSA = 'arsa';
    case YAZLIK = 'yazlik';
    case TURISTIK = 'turistik';
    case TARIM = 'tarim';

    /**
     * Get human-readable label
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::KONUT => 'Konut',
            self::ISYERI => 'İşyeri',
            self::ARSA => 'Arsa',
            self::YAZLIK => 'Yazlık',
            self::TURISTIK => 'Turistik Tesis',
            self::TARIM => 'Tarım',
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
            self::KONUT => 'Ev, daire, villa gibi konut türleri',
            self::ISYERI => 'Dükkan, ofis, fabrika gibi işyerleri',
            self::ARSA => 'İmar ve tarım arsaları',
            self::YAZLIK => 'Yazlık ev ve tatil evleri',
            self::TURISTIK => 'Otel, motel, pansiyon gibi tesisler',
            self::TARIM => 'Tarım arazileri ve çiftlikler',
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
            self::KONUT => '🏠',
            self::ISYERI => '🏢',
            self::ARSA => '🏞️',
            self::YAZLIK => '🏖️',
            self::TURISTIK => '🏨',
            self::TARIM => '🌾',
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
            self::KONUT => 'blue',
            self::ISYERI => 'purple',
            self::ARSA => 'green',
            self::YAZLIK => 'orange',
            self::TURISTIK => 'red',
            self::TARIM => 'yellow',
        };
    }

    /**
     * Check if this category is residential
     *
     * @return bool
     */
    public function isResidential(): bool
    {
        return in_array($this, [self::KONUT, self::YAZLIK]);
    }

    /**
     * Check if this category is commercial
     *
     * @return bool
     */
    public function isCommercial(): bool
    {
        return in_array($this, [self::ISYERI, self::TURISTIK]);
    }

    /**
     * Check if this category is land
     *
     * @return bool
     */
    public function isLand(): bool
    {
        return in_array($this, [self::ARSA, self::TARIM]);
    }

    /**
     * Check if this category supports daily rental
     *
     * @return bool
     */
    public function supportsDailyRental(): bool
    {
        return in_array($this, [self::YAZLIK, self::TURISTIK]);
    }

    /**
     * Get required fields for this category
     *
     * @return array
     */
    public function requiredFields(): array
    {
        return match ($this) {
            self::KONUT => ['oda_sayisi', 'salon_sayisi', 'banyo_sayisi', 'kat', 'binada_kat_sayisi'],
            self::ISYERI => ['alan', 'kat', 'binada_kat_sayisi'],
            self::ARSA => ['alan', 'imar_statusu', 'ada_no', 'parsel_no'],
            self::YAZLIK => ['oda_sayisi', 'salon_sayisi', 'havuz', 'denize_uzaklik'],
            self::TURISTIK => ['alan', 'oda_sayisi', 'yatak_kapasitesi'],
            self::TARIM => ['alan', 'sulama', 'tapu_tipi'],
        };
    }

    /**
     * Get optional fields for this category
     *
     * @return array
     */
    public function optionalFields(): array
    {
        return match ($this) {
            self::KONUT => ['balkon', 'garaj', 'asansor', 'site_icerisinde'],
            self::ISYERI => ['takas_uygun', 'kredi_uygun'],
            self::ARSA => ['kaks', 'taks', 'gabari'],
            self::YAZLIK => ['klima', 'jakuzi', 'barbekü'],
            self::TURISTIK => ['spa', 'restoran', 'otopark'],
            self::TARIM => ['sulama', 'elektrik', 'yol'],
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
