<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait SearchableTrait
{
    /**
     * Genel arama scope'u
     *
     * @param Builder $query
     * @param string $search
     * @param array $fields Aranacak alanlar
     * @return Builder
     */
    public function scopeSearch(Builder $query, string $search, array $fields = []): Builder
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
            $fields = ['name', 'title', 'description'];
        }

        return $query->where(function (Builder $q) use ($search, $fields) {
            foreach ($fields as $field) {
                if ($this->getConnection()->getSchemaBuilder()->hasColumn($this->getTable(), $field)) {
                    $q->orWhere($field, 'LIKE', "%{$search}%");
                }
            }
        });
    }

    /**
     * Tam metin arama scope'u
     *
     * @param Builder $query
     * @param string $search
     * @return Builder
     */
    public function scopeFullTextSearch(Builder $query, string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        // MySQL FULLTEXT search desteği varsa kullan
        if (property_exists($this, 'fullTextColumns')) {
            $columns = implode(',', $this->fullTextColumns);
            return $query->whereRaw("MATCH({$columns}) AGAINST(? IN BOOLEAN MODE)", [$search]);
        }

        // Yoksa normal search kullan
        return $this->scopeSearch($query, $search);
    }

    /**
     * Filtreleme scope'u
     *
     * @param Builder $query
     * @param array $filters
     * @return Builder
     */
    public function scopeFilter(Builder $query, array $filters = []): Builder
    {
        foreach ($filters as $field => $value) {
            if (empty($value)) {
                continue;
            }

            if ($this->getConnection()->getSchemaBuilder()->hasColumn($this->getTable(), $field)) {
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
     * Sıralama scope'u
     *
     * @param Builder $query
     * @param string $sortBy
     * @param string $sortDirection
     * @return Builder
     */
    public function scopeSortBy(Builder $query, string $sortBy = 'created_at', string $sortDirection = 'desc'): Builder
    {
        if ($this->getConnection()->getSchemaBuilder()->hasColumn($this->getTable(), $sortBy)) {
            return $query->orderBy($sortBy, $sortDirection);
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Pagination ile birlikte arama
     *
     * @param string $search
     * @param array $filters
     * @param int $perPage
     * @param string $sortBy
     * @param string $sortDirection
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function searchAndPaginate(
        string $search = '',
        array $filters = [],
        int $perPage = 15,
        string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ) {
        return static::search($search)
            ->filter($filters)
            ->sortBy($sortBy, $sortDirection)
            ->paginate($perPage);
    }
}
