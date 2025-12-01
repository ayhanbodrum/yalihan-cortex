<?php

namespace App\Enums;

/**
 * İlan Status Enum
 *
 * Context7: Type-safe listing status enumeration
 * Replaces string-based status field with enum
 */
enum IlanStatus: string
{
    case TASLAK = 'Taslak'; // ✅ Context7: Database değeri ile uyumlu
    case YAYINDA = 'yayinda'; // ⚠️ Legacy: Kullanımdan kaldırılmalı, 'Aktif' kullanılmalı
    case AKTIF = 'Aktif'; // ✅ Context7: Database değeri ile uyumlu
    case PASIF = 'Pasif'; // ✅ Context7: Database değeri ile uyumlu
    case BEKLEMEDE = 'Beklemede'; // ✅ Context7: Database değeri ile uyumlu
    case ARSIV = 'arsiv'; // ⚠️ Legacy: Kullanımdan kaldırılmalı
    case ONAY_BEKLIYOR = 'onay_bekliyor'; // ⚠️ Legacy: 'Beklemede' kullanılmalı
    case REDDEDILDI = 'reddedildi'; // ⚠️ Legacy: Kullanımdan kaldırılmalı
    case SATISILDI = 'satisildi'; // ⚠️ Legacy: Kullanımdan kaldırılmalı
    case KIRASILDI = 'kirasildi'; // ⚠️ Legacy: Kullanımdan kaldırılmalı

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match ($this) {
            self::TASLAK => 'Taslak',
            self::YAYINDA => 'Yayında', // ⚠️ Legacy
            self::AKTIF => 'Aktif',
            self::PASIF => 'Pasif',
            self::BEKLEMEDE => 'Beklemede',
            self::ARSIV => 'Arşiv', // ⚠️ Legacy
            self::ONAY_BEKLIYOR => 'Onay Bekliyor', // ⚠️ Legacy: 'Beklemede' kullanılmalı
            self::REDDEDILDI => 'Reddedildi', // ⚠️ Legacy
            self::SATISILDI => 'Satışıldı', // ⚠️ Legacy
            self::KIRASILDI => 'Kirası Verildi', // ⚠️ Legacy
        };
    }

    /**
     * Get description
     */
    public function description(): string
    {
        return match ($this) {
            self::TASLAK => 'İlan henüz yayınlanmadı',
            self::YAYINDA => 'İlan aktif olarak yayında', // ⚠️ Legacy
            self::AKTIF => 'İlan aktif olarak yayında',
            self::PASIF => 'İlan geçici olarak pasif',
            self::BEKLEMEDE => 'İlan onay bekliyor',
            self::ARSIV => 'İlan arşivlendi', // ⚠️ Legacy
            self::ONAY_BEKLIYOR => 'İlan onay bekliyor', // ⚠️ Legacy: 'Beklemede' kullanılmalı
            self::REDDEDILDI => 'İlan reddedildi', // ⚠️ Legacy
            self::SATISILDI => 'İlan satışı tamamlandı', // ⚠️ Legacy
            self::KIRASILDI => 'İlanın kirası verildi', // ⚠️ Legacy
        };
    }

    /**
     * Get badge color for UI
     *
     * @return string Tailwind CSS color class
     */
    public function color(): string
    {
        return match ($this) {
            self::TASLAK => 'gray',
            self::YAYINDA => 'green', // ⚠️ Legacy
            self::AKTIF => 'green',
            self::PASIF => 'yellow',
            self::BEKLEMEDE => 'blue',
            self::ARSIV => 'slate', // ⚠️ Legacy
            self::ONAY_BEKLIYOR => 'blue', // ⚠️ Legacy: 'Beklemede' kullanılmalı
            self::REDDEDILDI => 'red', // ⚠️ Legacy
            self::SATISILDI => 'purple', // ⚠️ Legacy
            self::KIRASILDI => 'indigo', // ⚠️ Legacy
        };
    }

    /**
     * Get icon
     */
    public function icon(): string
    {
        return match ($this) {
            self::TASLAK => '📝',
            self::YAYINDA => '✅', // ⚠️ Legacy
            self::AKTIF => '✅',
            self::PASIF => '⏸️',
            self::BEKLEMEDE => '⏳',
            self::ARSIV => '📦', // ⚠️ Legacy
            self::ONAY_BEKLIYOR => '⏳', // ⚠️ Legacy: 'Beklemede' kullanılmalı
            self::REDDEDILDI => '❌', // ⚠️ Legacy
            self::SATISILDI => '🎉', // ⚠️ Legacy
            self::KIRASILDI => '🔑', // ⚠️ Legacy
        };
    }

    /**
     * Check if listing is active
     */
    public function isActive(): bool
    {
        return in_array($this, [self::YAYINDA, self::AKTIF]);
    }

    /**
     * Check if listing is visible to public
     */
    public function isPublic(): bool
    {
        return in_array($this, [self::YAYINDA, self::AKTIF]);
    }

    /**
     * Check if listing is completed
     */
    public function isCompleted(): bool
    {
        return in_array($this, [self::SATISILDI, self::KIRASILDI]);
    }

    /**
     * Check if listing is pending
     */
    public function isPending(): bool
    {
        return in_array($this, [self::TASLAK, self::BEKLEMEDE, self::ONAY_BEKLIYOR]);
    }

    /**
     * Check if listing can be edited
     */
    public function isEditable(): bool
    {
        return ! in_array($this, [self::SATISILDI, self::KIRASILDI, self::ARSIV]);
    }

    /**
     * Check if listing can be published
     */
    public function canPublish(): bool
    {
        return in_array($this, [self::TASLAK, self::PASIF, self::BEKLEMEDE, self::ONAY_BEKLIYOR]);
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
                'description' => $case->description(),
            ],
            self::cases()
        );
    }

    /**
     * Get active statuses
     */
    public static function activeStatuses(): array
    {
        return [self::YAYINDA, self::AKTIF];
    }

    /**
     * Get completed statuses
     */
    public static function completedStatuses(): array
    {
        return [self::SATISILDI, self::KIRASILDI];
    }
}
