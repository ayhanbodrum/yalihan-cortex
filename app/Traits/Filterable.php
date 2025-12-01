<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Filterable Trait
 *
 * Context7 Standardı: C7-FILTERABLE-TRAIT-2025-11-11
 *
 * Standart filtreleme, arama, sıralama ve tarih aralığı işlemleri için trait
 * Code duplication'ı azaltmak ve tutarlı filter logic sağlamak için oluşturuldu
 */
trait Filterable
{
    /**
     * Request'ten gelen filtreleri uygula
     *
     * @param  Request|array  $filters  Request object veya filter array
     * @param  array  $allowedFilters  İzin verilen filter alanları (güvenlik için)
     */
    public function scopeApplyFilters(Builder $query, $filters, array $allowedFilters = []): Builder
    {
        // Request object ise array'e çevir
        if ($filters instanceof Request) {
            // ✅ REFACTORED: Field mapping desteği (örn: 'kategori' => 'kategori_id')
            $filterData = [];
            foreach ($allowedFilters as $key => $value) {
                if (is_numeric($key)) {
                    // Direct field name
                    $fieldName = $value;
                    $requestKey = $value;
                } else {
                    // Mapped field (key => value)
                    $fieldName = $key;
                    $requestKey = $value;
                }

                if ($filters->filled($requestKey)) {
                    $filterData[$fieldName] = $filters->input($requestKey);
                }
            }
            $filters = $filterData;
        }

        if (empty($filters) || ! is_array($filters)) {
            return $query;
        }

        // ✅ OPTIMIZED: Schema builder'ı cache'le
        $schema = $this->getConnection()->getSchemaBuilder();
        $tableName = $this->getTable();
        $validColumns = [];

        foreach ($filters as $field => $value) {
            // Boş değerleri atla
            if ($value === null || $value === '' || (is_array($value) && empty($value))) {
                continue;
            }

            // Column kontrolü cache'le
            if (! isset($validColumns[$field])) {
                $validColumns[$field] = $schema->hasColumn($tableName, $field);
            }

            if ($validColumns[$field]) {
                if (is_array($value)) {
                    $query->whereIn($field, $value);
                } else {
                    $query->where($field, $value);
                }
            }
        }

        return $query;
    }

    /**
     * Genel arama scope'u
     *
     * @param  string|null  $search  Arama terimi
     * @param  array  $fields  Aranacak alanlar (boşsa searchable property kullanılır)
     */
    public function scopeSearch(Builder $query, ?string $search, array $fields = []): Builder
    {
        if (empty($search)) {
            return $query;
        }

        // Eğer fields belirtilmemişse, searchable fields kullan
        if (empty($fields) && property_exists($this, 'searchable')) {
            $fields = $this->searchable;
        }

        // Varsayılan aranabilir alanlar
        if (empty($fields)) {
            $fields = ['name', 'title', 'baslik', 'aciklama', 'description'];
        }

        return $query->where(function (Builder $q) use ($search, $fields) {
            $schema = $this->getConnection()->getSchemaBuilder();
            $tableName = $this->getTable();

            foreach ($fields as $field) {
                if ($schema->hasColumn($tableName, $field)) {
                    $q->orWhere($field, 'LIKE', "%{$search}%");
                }
            }
        });
    }

    /**
     * İlişki üzerinden arama (whereHas)
     *
     * @param  string  $relation  İlişki adı
     * @param  string|null  $search  Arama terimi
     * @param  array  $fields  Aranacak alanlar
     */
    public function scopeSearchRelation(Builder $query, string $relation, ?string $search, array $fields = []): Builder
    {
        if (empty($search) || empty($fields)) {
            return $query;
        }

        return $query->whereHas($relation, function (Builder $q) use ($search, $fields) {
            foreach ($fields as $field) {
                $q->orWhere($field, 'LIKE', "%{$search}%");
            }
        });
    }

    /**
     * Sıralama scope'u
     *
     * @param  string|null  $sortBy  Sıralama alanı
     * @param  string  $sortDirection  Sıralama yönü (asc/desc)
     * @param  string  $defaultSort  Varsayılan sıralama alanı
     */
    public function scopeSort(Builder $query, ?string $sortBy = null, string $sortDirection = 'desc', string $defaultSort = 'created_at'): Builder
    {
        $sortBy = $sortBy ?: $defaultSort;
        $sortDirection = strtolower($sortDirection) === 'asc' ? 'asc' : 'desc';

        $query->reorder();
        if ($sortBy === 'fiyat') {
            try {
                $driver = $query->getConnection()->getDriverName();
            } catch (\Throwable $e) {
                $driver = 'mysql';
            }
            if ($driver === 'sqlite') {
                if ($sortDirection === 'desc') {
                    $query->orderByRaw('(0 + fiyat) DESC');
                } else {
                    $query->orderByRaw('(0 + fiyat) ASC');
                }
                $query->orderBy($defaultSort, $sortDirection);
                $query->orderBy('id', $sortDirection);
            } else {
                if ($sortDirection === 'desc') {
                    $query->orderByRaw('(0 + fiyat) DESC');
                } else {
                    $query->orderByRaw('(0 + fiyat) ASC');
                }
                $query->orderBy($defaultSort, $sortDirection);
                $query->orderBy('id', $sortDirection);
            }

            return $query;
        }
        if ($this->getConnection()->getSchemaBuilder()->hasColumn($this->getTable(), $sortBy)) {
            return $query->orderBy($sortBy, $sortDirection);
        }

        return $query->orderBy($defaultSort, $sortDirection);
    }

    /**
     * Tarih aralığı filtreleme
     *
     * @param  string|null  $startDate  Başlangıç tarihi
     * @param  string|null  $endDate  Bitiş tarihi
     * @param  string  $column  Tarih kolonu (varsayılan: created_at)
     */
    public function scopeDateRange(Builder $query, ?string $startDate = null, ?string $endDate = null, string $column = 'created_at'): Builder
    {
        // Column kontrolü
        if (! $this->getConnection()->getSchemaBuilder()->hasColumn($this->getTable(), $column)) {
            return $query;
        }

        if ($startDate) {
            try {
                $start = Carbon::parse($startDate)->startOfDay();
                $query->whereDate($column, '>=', $start);
            } catch (\Exception $e) {
                // Geçersiz tarih formatı, atla
            }
        }

        if ($endDate) {
            try {
                $end = Carbon::parse($endDate)->endOfDay();
                $query->whereDate($column, '<=', $end);
            } catch (\Exception $e) {
                // Geçersiz tarih formatı, atla
            }
        }

        return $query;
    }

    /**
     * Fiyat aralığı filtreleme
     *
     * @param  float|null  $minPrice  Minimum fiyat
     * @param  float|null  $maxPrice  Maksimum fiyat
     * @param  string  $column  Fiyat kolonu (varsayılan: fiyat)
     */
    public function scopePriceRange(Builder $query, ?float $minPrice = null, ?float $maxPrice = null, string $column = 'fiyat'): Builder
    {
        // Column kontrolü
        if (! $this->getConnection()->getSchemaBuilder()->hasColumn($this->getTable(), $column)) {
            return $query;
        }

        if ($minPrice !== null && $minPrice > 0) {
            $query->where($column, '>=', $minPrice);
        }

        if ($maxPrice !== null && $maxPrice > 0) {
            $query->where($column, '<=', $maxPrice);
        }

        return $query;
    }

    /**
     * Status filtreleme (Context7 uyumlu)
     *
     * @param  mixed  $status  Status değeri (true/false, 1/0, 'active'/'inactive')
     * @param  string  $column  Status kolonu (varsayılan: status)
     */
    public function scopeByStatus(Builder $query, $status, string $column = 'status'): Builder
    {
        // Column kontrolü
        if (! $this->getConnection()->getSchemaBuilder()->hasColumn($this->getTable(), $column)) {
            return $query;
        }

        // ✅ Context7: String status değerlerini doğrudan kullan (örn: Ilan modeli için 'Aktif', 'Pasif', 'Taslak')
        // Eğer status bir string ise, doğrudan kullan (boolean'a çevirme)
        // Bu, Ilan gibi string-based status kullanan modeller için kritik
        if (is_string($status)) {
            // Bilinen string status değerleri için doğrudan kullan
            $knownStringStatuses = ['Aktif', 'Pasif', 'Taslak', 'Beklemede', 'yayinda', 'arsiv', 'onay_bekliyor', 'reddedildi', 'satisildi', 'kirasildi'];
            if (in_array($status, $knownStringStatuses)) {
                return $query->where($column, $status);
            }

            // Eğer bilinen string değerlerden biri değilse, boolean'a çevir (backward compatibility)
            // Context7: enabled → status (backward compatibility için 'enabled' hala kabul ediliyor)
            $status = in_array(strtolower($status), ['active', 'aktif', '1', 'true', 'enabled']);
        }

        return $query->where($column, $status);
    }

    /**
     * Request'ten tüm filtreleri uygula (all-in-one method)
     *
     * @param  array  $options  Seçenekler
     *                          - search_fields: Arama alanları
     *                          - allowed_filters: İzin verilen filter'lar
     *                          - date_column: Tarih kolonu
     *                          - price_column: Fiyat kolonu
     *                          - default_sort: Varsayılan sıralama
     */
    public function scopeFilterFromRequest(Builder $query, Request $request, array $options = []): Builder
    {
        // Search
        if ($request->filled('search')) {
            $searchFields = $options['search_fields'] ?? [];
            $query->search($request->search, $searchFields);
        }

        // Filters
        $allowedFilters = $options['allowed_filters'] ?? [];
        $query->applyFilters($request, $allowedFilters);

        // Date range
        if ($request->filled('start_date') || $request->filled('end_date')) {
            $dateColumn = $options['date_column'] ?? 'created_at';
            $query->dateRange($request->start_date, $request->end_date, $dateColumn);
        }

        // Price range
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $priceColumn = $options['price_column'] ?? 'fiyat';
            $query->priceRange(
                $request->filled('min_price') ? (float) $request->min_price : null,
                $request->filled('max_price') ? (float) $request->max_price : null,
                $priceColumn
            );
        }

        // Status filter
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Sort
        $defaultSort = $options['default_sort'] ?? 'created_at';
        $query->sort($request->sort_by, $request->sort_order ?? 'desc', $defaultSort);

        return $query;
    }

    /**
     * Pagination ile birlikte filtreleme (all-in-one)
     *
     * @param  array  $options  Seçenekler
     * @param  int  $perPage  Sayfa başına kayıt sayısı
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function filterAndPaginate(Request $request, array $options = [], int $perPage = 15)
    {
        return static::filterFromRequest(static::query(), $request, $options)
            ->paginate($perPage);
    }
}
