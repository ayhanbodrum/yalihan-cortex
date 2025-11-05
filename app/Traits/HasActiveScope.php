<?php

namespace App\Traits;

/**
 * HasActiveScope Trait
 * 
 * Context7 Standardı: C7-ACTIVE-SCOPE-TRAIT-2025-11-05
 * 
 * 18+ modelde tekrarlanan scopeActive metodunu bir trait'e çıkarır
 * DRY prensibi - Code duplication önlendi
 */
trait HasActiveScope
{
    /**
     * Scope a query to only include active records.
     * 
     * Context7: Farklı modellerde farklı field'lar kullanılabilir:
     * - status = 'active' veya status = 1
     * - one_cikan = true
     * - is_active = true
     * - enabled = true
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        // Önce status field'ını kontrol et (en yaygın)
        if ($this->getConnection()->getSchemaBuilder()->hasColumn($this->getTable(), 'status')) {
            return $query->where(function ($q) {
                $q->where('status', 'active')
                  ->orWhere('status', 1)
                  ->orWhere('status', 'Aktif');
            });
        }
        
        // one_cikan field'ı varsa (Category model)
        if ($this->getConnection()->getSchemaBuilder()->hasColumn($this->getTable(), 'one_cikan')) {
            return $query->where('one_cikan', true);
        }
        
        // is_active field'ı varsa
        if ($this->getConnection()->getSchemaBuilder()->hasColumn($this->getTable(), 'is_active')) {
            return $query->where('is_active', true);
        }
        
        // enabled field'ı varsa
        if ($this->getConnection()->getSchemaBuilder()->hasColumn($this->getTable(), 'enabled')) {
            return $query->where('enabled', true);
        }
        
        // Hiçbiri yoksa hiçbir filtreleme yapma (backward compatibility)
        return $query;
    }
}

