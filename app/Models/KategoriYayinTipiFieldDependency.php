<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriYayinTipiFieldDependency extends Model
{
    use HasFactory;

    protected $table = 'kategori_yayin_tipi_field_dependencies';

    protected $fillable = [
        'kategori_slug',
        'yayin_tipi',
        'field_slug',
        'field_name',
        'field_type',
        'field_category',
        'field_options',
        'field_unit',
        'field_icon',
        'status', // Context7: enabled → status
        'required',
        'display_order', // Context7: order → display_order
        'ai_auto_fill',
        'ai_suggestion',
        'ai_calculation',
        'ai_prompt_key',
        'searchable',
        'show_in_card'
    ];

    protected $casts = [
        'field_options' => 'array',
        'status' => 'boolean', // Context7: enabled → status
        'required' => 'boolean',
        'ai_auto_fill' => 'boolean',
        'ai_suggestion' => 'boolean',
        'ai_calculation' => 'boolean',
        'searchable' => 'boolean',
        'show_in_card' => 'boolean',
        'display_order' => 'integer' // Context7: order → display_order
    ];

    /**
     * Scope: Aktif field'ları getir
     */
    public function scopeEnabled($query)
    {
        return $query->where('status', true); // Context7: enabled → status
    }

    /**
     * Scope: Kategori ve yayın tipine göre filtrele
     */
    public function scopeForKategoriYayinTipi($query, $kategoriSlug, $yayinTipi)
    {
        return $query->where('kategori_slug', $kategoriSlug)
                    ->where('yayin_tipi', $yayinTipi);
    }

    /**
     * Scope: AI özellikli field'ları getir
     */
    public function scopeWithAI($query)
    {
        return $query->where(function($q) {
            $q->where('ai_auto_fill', true)
              ->orWhere('ai_suggestion', true)
              ->orWhere('ai_calculation', true);
        });
    }

    /**
     * Scope: Kategoriye göre filtrele
     */
    public function scopeForKategori($query, $kategoriSlug)
    {
        return $query->where('kategori_slug', $kategoriSlug);
    }

    /**
     * Scope: Yayın tipine göre filtrele
     */
    public function scopeForYayinTipi($query, $yayinTipi)
    {
        return $query->where('yayin_tipi', $yayinTipi);
    }

    /**
     * Scope: Field kategorisine göre filtrele
     */
    public function scopeForCategory($query, $fieldCategory)
    {
        return $query->where('field_category', $fieldCategory);
    }

    /**
     * Sıralama scope'u
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('field_name'); // Context7: order → display_order
    }
}
