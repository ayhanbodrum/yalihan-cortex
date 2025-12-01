<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'type',
        'icon',
        'color',
        'sort_order',
        'one_cikan',
        'is_featured',
        'meta_data',
    ];

    protected $casts = [
        'one_cikan' => 'boolean',
        'is_featured' => 'boolean',
        'meta_data' => 'array',
    ];

    /**
     * Alt kategoriler
     */
    public function subCategories(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Üst kategori
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Tüm alt kategoriler (recursive)
     */
    public function allSubCategories(): HasMany
    {
        return $this->subCategories()->with('allSubCategories');
    }

    /**
     * Tüm üst kategoriler (recursive)
     */
    public function allParents(): BelongsTo
    {
        return $this->parent()->with('allParents');
    }

    /**
     * Özellikler
     */
    public function features(): HasMany
    {
        return $this->hasMany(Feature::class);
    }

    /**
     * İlanlar
     */
    public function ilanlar(): HasMany
    {
        return $this->hasMany(Ilan::class);
    }

    /**
     * Aktif kategorileri getir
     */
    public function scopeActive($query)
    {
        // Context7: status → one_cikan
        return $query->where('one_cikan', true);
    }

    /**
     * Ana kategorileri getir
     */
    public function scopeMain($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Öne çıkan kategorileri getir
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Tipe göre filtrele
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Tam adı getir
     */
    public function getFullNameAttribute(): string
    {
        if ($this->parent) {
            return $this->parent->name.' > '.$this->name;
        }

        return $this->name;
    }

    /**
     * Slug oluştur
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
}
