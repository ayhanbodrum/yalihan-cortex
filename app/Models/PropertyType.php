<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PropertyType extends Model
{
    use HasFactory;

    protected $table = 'property_types';

    protected $fillable = [
        'name',
        'description',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * İlan türüne ait ilanlar
     */
    public function ilanlar(): HasMany
    {
        return $this->hasMany(Ilan::class, 'yayin_tipi_id');
    }

    /**
     * Aktif ilan türleri
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Sıralı ilan türleri
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * İlan türü adını getir
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name;
    }

    /**
     * İlan türü açıklamasını getir
     */
    public function getDisplayDescriptionAttribute(): string
    {
        return $this->description ?? '';
    }

    /**
     * İlan türü statusunu getir
     */
    public function getStatusTextAttribute(): string
    {
        return $this->status ? 'Aktif' : 'Pasif';
    }

    /**
     * İlan türü rengini getir
     */
    public function getStatusColorAttribute(): string
    {
        return $this->status ? 'green' : 'red';
    }
}
