<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Site/Apartman Model
 * 
 * Context7: Site/Apartman yönetimi için
 * - Site adı, toplam daire sayısı
 * - Portal entegrasyonu
 * - İlan ilişkisi
 */
class SiteApartman extends Model
{
    use HasFactory;

    protected $table = 'site_apartmanlar';

    protected $fillable = [
        'name',
        'tip',
        'toplam_daire_sayisi',
        'adres',
        'il_id',
        'ilce_id',
        'mahalle_id',
        'latitude',
        'longitude',
        'site_ozellikleri',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'site_ozellikleri' => 'array',
        'latitude' => 'decimal:8,6',
        'longitude' => 'decimal:9,6',
        'toplam_daire_sayisi' => 'integer'
    ];

    /**
     * İl ilişkisi
     */
    public function il()
    {
        return $this->belongsTo(Il::class, 'il_id');
    }

    /**
     * İlçe ilişkisi
     */
    public function ilce()
    {
        return $this->belongsTo(Ilce::class, 'ilce_id');
    }

    /**
     * Mahalle ilişkisi
     */
    public function mahalle()
    {
        return $this->belongsTo(Mahalle::class, 'mahalle_id');
    }

    /**
     * İlanlar ilişkisi
     */
    public function ilanlar()
    {
        return $this->hasMany(Ilan::class, 'site_id');
    }

    /**
     * Oluşturan kullanıcı
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Güncelleyen kullanıcı
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope: Aktif siteler
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: İl bazında filtreleme
     */
    public function scopeByIl($query, $ilId)
    {
        return $query->where('il_id', $ilId);
    }

    /**
     * Scope: İlçe bazında filtreleme
     */
    public function scopeByIlce($query, $ilceId)
    {
        return $query->where('ilce_id', $ilceId);
    }

    /**
     * Tam adres getir
     */
    public function getFullAddressAttribute()
    {
        $parts = [];
        
        if ($this->adres) {
            $parts[] = $this->adres;
        }
        
        if ($this->mahalle) {
            $parts[] = $this->mahalle->mahalle_adi;
        }
        
        if ($this->ilce) {
            $parts[] = $this->ilce->ilce_adi;
        }
        
        if ($this->il) {
            $parts[] = $this->il->il_adi;
        }
        
        return implode(', ', $parts);
    }

    /**
     * Site özelliklerini getir
     */
    public function getSiteFeaturesAttribute()
    {
        return $this->site_ozellikleri ?? [];
    }

    /**
     * İlan sayısını getir
     */
    public function getIlanCountAttribute()
    {
        return $this->ilanlar()->count();
    }

    /**
     * Aktif ilan sayısını getir
     */
    public function getActiveIlanCountAttribute()
    {
        return $this->ilanlar()->where('status', 'Aktif')->count();
    }
}