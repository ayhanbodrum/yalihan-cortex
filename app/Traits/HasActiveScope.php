<?php

namespace App\Traits;

/**
 * HasActiveScope Trait
 *
 * Context7 Standardı: C7-ACTIVE-SCOPE-TRAIT-2025-11-06
 *
 * 18+ modelde tekrarlanan scopeActive metodunu bir trait'e çıkarır
 * DRY prensibi - Code duplication önlendi
 *
 * ⚠️ IMPORTANT: enabled field FORBIDDEN by Context7 (removed 2025-11-06)
 */
trait HasActiveScope
{
    /**
     * Scope a query to only include active records.
     *
     * Context7: Farklı modellerde farklı field'lar kullanılabilir:
     * - status = 'active' veya status = 1 veya status = true (PREFERRED)
     * - one_cikan = true
     * - is_active = true
     *
     * ❌ FORBIDDEN: enabled field (Context7 rule violation)
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        // Önce status field'ını kontrol et (Context7 PREFERRED)
        if ($this->getConnection()->getSchemaBuilder()->hasColumn($this->getTable(), 'status')) {
            return $query->where(function ($q) {
                $q->where('status', 'active')
                    ->orWhere('status', 1)
                    ->orWhere('status', true)
                    ->orWhere('status', 'Aktif')
                    ->orWhere('status', 'yayinda');
            });
        }

        // one_cikan field'ı varsa (Category model)
        if ($this->getConnection()->getSchemaBuilder()->hasColumn($this->getTable(), 'one_cikan')) {
            return $query->where('one_cikan', true);
        }

        // is_active field'ı varsa (legacy support)
        if ($this->getConnection()->getSchemaBuilder()->hasColumn($this->getTable(), 'is_active')) {
            return $query->where('is_active', true);
        }

        // ❌ REMOVED: enabled field support (Context7 violation)
        // Context7 Note: enabled field is FORBIDDEN, use status instead
        // See: .context7/ENABLED_FIELD_FORBIDDEN.md

        // Hiçbiri yoksa hiçbir filtreleme yapma (backward compatibility)
        return $query;
    }
}
