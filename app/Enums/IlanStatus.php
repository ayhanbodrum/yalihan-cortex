<?php

namespace App\Enums;

/**
 * İlan Status Enum
 *
 * Context7: Type-safe listing status enumeration
 * Replaces string-based status field with enum
 *
 * @package App\Enums
 */
enum IlanStatus: string
{
    case TASLAK = 'taslak';
    case YAYINDA = 'yayinda';
    case AKTIF = 'Aktif'; // Backward compatibility
    case PASIF = 'pasif';
    case ARSIV = 'arsiv';
    case ONAY_BEKLIYOR = 'onay_bekliyor';
    case REDDEDILDI = 'reddedildi';
    case SATISILDI = 'satisildi';
    case KIRASILDI = 'kirasildi';

    /**
     * Get human-readable label
     *
     * @return string
     */
    public function label(): string
    {
        return match($this) {
            self::TASLAK => 'Taslak',
            self::YAYINDA => 'Yayında',
            self::AKTIF => 'Aktif', // Backward compatibility
            self::PASIF => 'Pasif',
            self::ARSIV => 'Arşiv',
            self::ONAY_BEKLIYOR => 'Onay Bekliyor',
            self::REDDEDILDI => 'Reddedildi',
            self::SATISILDI => 'Satışıldı',
            self::KIRASILDI => 'Kirası Verildi',
        };
    }

    /**
     * Get description
     *
     * @return string
     */
    public function description(): string
    {
        return match($this) {
            self::TASLAK => 'İlan henüz yayınlanmadı',
            self::YAYINDA => 'İlan aktif olarak yayında',
            self::AKTIF => 'İlan aktif olarak yayında', // Backward compatibility
            self::PASIF => 'İlan geçici olarak pasif',
            self::ARSIV => 'İlan arşivlendi',
            self::ONAY_BEKLIYOR => 'İlan onay bekliyor',
            self::REDDEDILDI => 'İlan reddedildi',
            self::SATISILDI => 'İlan satışı tamamlandı',
            self::KIRASILDI => 'İlanın kirası verildi',
        };
    }

    /**
     * Get badge color for UI
     *
     * @return string Tailwind CSS color class
     */
    public function color(): string
    {
        return match($this) {
            self::TASLAK => 'gray',
            self::YAYINDA => 'green',
            self::AKTIF => 'green', // Backward compatibility
            self::PASIF => 'yellow',
            self::ARSIV => 'slate',
            self::ONAY_BEKLIYOR => 'blue',
            self::REDDEDILDI => 'red',
            self::SATISILDI => 'purple',
            self::KIRASILDI => 'indigo',
        };
    }

    /**
     * Get icon
     *
     * @return string
     */
    public function icon(): string
    {
        return match($this) {
            self::TASLAK => '📝',
            self::YAYINDA => '✅',
            self::AKTIF => '✅', // Backward compatibility
            self::PASIF => '⏸️',
            self::ARSIV => '📦',
            self::ONAY_BEKLIYOR => '⏳',
            self::REDDEDILDI => '❌',
            self::SATISILDI => '🎉',
            self::KIRASILDI => '🔑',
        };
    }

    /**
     * Check if listing is active
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return in_array($this, [self::YAYINDA, self::AKTIF]);
    }

    /**
     * Check if listing is visible to public
     *
     * @return bool
     */
    public function isPublic(): bool
    {
        return in_array($this, [self::YAYINDA, self::AKTIF]);
    }

    /**
     * Check if listing is completed
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return in_array($this, [self::SATISILDI, self::KIRASILDI]);
    }

    /**
     * Check if listing is pending
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return in_array($this, [self::TASLAK, self::ONAY_BEKLIYOR]);
    }

    /**
     * Check if listing can be edited
     *
     * @return bool
     */
    public function isEditable(): bool
    {
        return !in_array($this, [self::SATISILDI, self::KIRASILDI, self::ARSIV]);
    }

    /**
     * Check if listing can be published
     *
     * @return bool
     */
    public function canPublish(): bool
    {
        return in_array($this, [self::TASLAK, self::PASIF, self::ONAY_BEKLIYOR]);
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
     * Get active statuses
     *
     * @return array
     */
    public static function activeStatuses(): array
    {
        return [self::YAYINDA, self::AKTIF];
    }

    /**
     * Get completed statuses
     *
     * @return array
     */
    public static function completedStatuses(): array
    {
        return [self::SATISILDI, self::KIRASILDI];
    }
}
