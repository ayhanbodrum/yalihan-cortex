<?php

namespace App\Models;

use App\Traits\HasFeatures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class IlanKategori extends Model
{
    use HasFeatures;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ilan_kategorileri';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'parent_id',
        'seviye',
        'status', // Context7 kuralı: status → status
        'display_order', // Context7: order → display_order
        'slug',
        'icon',
        'aciklama', // Database column name
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean', // Context7 kuralı: status → status
        'seviye' => 'integer',
        'display_order' => 'integer', // Context7: order → display_order
    ];

    /**
     * Accessor for 'ad' attribute (backward compatibility)
     */
    public function getAdAttribute()
    {
        return $this->name;
    }

    /**
     * Mutator for 'ad' attribute (backward compatibility)
     */
    public function setAdAttribute($value)
    {
        $this->attributes['name'] = $value;
    }

    /**
     * Accessor for 'active' attribute (backward compatibility)
     */
    public function getActiveAttribute()
    {
        return $this->status; // Context7 kuralı: status → status
    }

    /**
     * Mutator for 'active' attribute (backward compatibility)
     */
    public function setActiveAttribute($value)
    {
        $this->attributes['status'] = $value; // Context7 kuralı: status → status
    }

    /**
     * display_order kullanımı
     */
    // public function getOrderAttribute()
    // {
    //     return $this->display_order;
    // }

    /**
     * display_order kullanımı
     */
    // public function setOrderAttribute($value)
    // {
    //     $this->attributes['display_order'] = $value;
    // }

    /**
     * Üst kategoriyi döndüren ilişki (self-referencing)
     */
    public function parent()
    {
        return $this->belongsTo(IlanKategori::class, 'parent_id');
    }

    /**
     * Alt kategorileri döndüren ilişki (self-referencing)
     * ✅ Context7: display_order kullan
     */
    public function children()
    {
        return $this->hasMany(IlanKategori::class, 'parent_id');
    }

    /**
     * Aktif alt kategorileri döndüren ilişki
     * ✅ Context7: display_order kullan
     */
    public function statusChildren()
    {
        return $this->hasMany(IlanKategori::class, 'parent_id')
            ->where('status', true); // Context7 kuralı: status → status
    }

    /**
     * Context7: Yayın tipleri ilişkisi (ilan_kategori_yayin_tipleri tablosu)
     * ✅ Context7: display_order kullan (order değil)
     */
    public function yayinTipleri()
    {
        return $this->hasMany(IlanKategoriYayinTipi::class, 'kategori_id')
            ->where('status', true) // Context7: status kullanımı
            ->orderBy('display_order', 'ASC')
            ->orderBy('yayin_tipi', 'ASC');
    }

    /**
     * Sadece yayın tiplerini döndüren bir ilişki (seviye=2) - DEPRECATED
     * ✅ Context7: display_order kullan (order değil)
     * 
     * ⚠️ DEPRECATED: Artık ilan_kategori_yayin_tipleri tablosu kullanılıyor
     * Bu ilişki backward compatibility için korunuyor
     */
    public function yayinTipleriLegacy()
    {
        return $this->hasMany(IlanKategori::class, 'parent_id')
            ->where('seviye', 2)
            ->where('status', true); // Context7 kuralı: status → status
    }

    // İlan İlişkileri

    /**
     * Bu kategoriye ait ana kategorili ilanlar
     */
    public function anaKategoriIlanlar()
    {
        return $this->hasMany(Ilan::class, 'ana_kategori_id');
    }

    /**
     * Bu kategoriye ait alt kategorili ilanlar
     */
    public function altKategoriIlanlar()
    {
        return $this->hasMany(Ilan::class, 'alt_kategori_id');
    }

    /**
     * Bu kategoriye ait yayın tipi ilanları (string olarak eşleşen)
     */
    public function yayinTipiIlanlar()
    {
        if ($this->seviye == 2) {
            return Ilan::where('yayinlama_tipi', $this->name);
        }

        return Ilan::whereRaw('1 = 0'); // Boş query
    }

    /**
     * Bu kategorinin ilişkilendirildiği tüm ilanlar (ana, alt veya yayın tipi id üzerinden)
     * Not: withCount('ilanlar') ve $kategori->ilanlar()->count() için tek bir ilişki altında toplandı.
     */
    public function ilanlar()
    {
        $ilanTable = (new Ilan())->getTable(); // 'ilanlar'

        $hasKategori = Schema::hasColumn($ilanTable, 'kategori_id');
        $hasAna = Schema::hasColumn($ilanTable, 'ana_kategori_id');
        $hasAlt = Schema::hasColumn($ilanTable, 'alt_kategori_id');
        $hasYayin = Schema::hasColumn($ilanTable, 'yayin_tipi_id');

        // Tek kolonlu şema: kategori_id
        if ($hasKategori) {
            return $this->hasMany(Ilan::class, 'kategori_id');
        }

        // Çok kolonlu şema: ana/alt/yayin_tipi_id
        if ((int) ($this->seviye ?? -1) === 2 && $hasYayin) {
            return $this->hasMany(Ilan::class, 'yayin_tipi_id');
        }
        if (!is_null($this->parent_id) && $hasAlt) {
            return $this->hasMany(Ilan::class, 'alt_kategori_id');
        }
        if ($hasAna) {
            return $this->hasMany(Ilan::class, 'ana_kategori_id');
        }

        // Hiçbiri yoksa boş ilişki döndür (şema uyumsuzluğu)
        return $this->hasMany(Ilan::class, 'kategori_id')->whereRaw('1 = 0');
    }

    /**
     * Bu kategorinin tüm ilanları (ana, alt ve yayın tipi olarak)
     */
    public function tumIlanlar()
    {
        return $this->anaKategoriIlanlar()
            ->union($this->altKategoriIlanlar())
            ->union($this->yayinTipiIlanlar());
    }

    /**
     * Bu kategorinin status ilanları
     * Context7 FIX: 'active' yerine 'yayinda' kullanılmalı
     */
    public function statusIlanlar()
    {
        if ($this->seviye == 0) {
            return $this->anaKategoriIlanlar()->where('status', 'yayinda');
        } elseif ($this->seviye == 1) {
            return $this->altKategoriIlanlar()->where('status', 'yayinda');
        } else {
            return $this->yayinTipiIlanlar()->where('status', 'yayinda');
        }
    }

    /**
     * Bu kategoriye ait özellikler
     */
    public function features()
    {
        return $this->hasMany(\App\Models\Ozellik::class, 'kategori_id');
    }

    /**
     * Aktif scope - Bu method kategorileri filtrelemek için kullanılır
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1); // Context7 kuralı: status → status
    }

    /**
     * Ana kategorileri filtreleyen scope (seviye=0)
     */
    public function scopeAnaKategoriler($query)
    {
        return $query->where('seviye', 0)->orderBy('display_order');
    }

    /**
     * Alt kategorileri filtreleyen scope (seviye=1)
     */
    public function scopeAltKategoriler($query)
    {
        return $query->where('seviye', 1)->orderBy('display_order');
    }

    /**
     * Aktif alt kategorileri filtreleyen scope (seviye=1 + status=true)
     * Context7: Status filtresi ile aktif alt kategorileri getirir
     */
    public function scopeAktifAltKategoriler($query)
    {
        return $query->where('seviye', 1)
            ->where('status', true) // Context7: status kullanımı
            ->orderBy('display_order');
    }

    /**
     * Belirli bir ana kategoriye ait alt kategorileri filtreleyen scope
     * Context7: Ana kategori ID'sine göre alt kategorileri getirir
     */
    public function scopeAltKategorileriAnaKategoriyeGore($query, $anaKategoriId)
    {
        return $query->where('seviye', 1)
            ->where('parent_id', $anaKategoriId)
            ->where('status', true) // Context7: status kullanımı
            ->orderBy('display_order');
    }

    /**
     * Yayın tiplerini filtreleyen scope (seviye=2)
     */
    public function scopeYayinTipleri($query)
    {
        return $query->where('seviye', 2);
    }

    /**
     * Sıralı scope - kategorileri order sırasına göre sıralar
     */
    public function scopeSiralı($query)
    {
        return $query->orderBy('display_order');
    }

    /**
     * Model boot metodu - slug ve siralama işlemleri için
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($kategori) {
            // Auto-generate unique slug if not provided
            if (empty($kategori->slug)) {
                $baseSlug = \Illuminate\Support\Str::slug($kategori->name ?: $kategori->ad);
                $slug = $baseSlug;
                $counter = 1;

                // Unique slug oluştur
                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }

                $kategori->slug = $slug;
            }
        });

        static::updating(function ($kategori) {
            // Auto-update unique slug if name changes and slug was not manually set
            if (($kategori->isDirty('name') || $kategori->isDirty('ad')) && ! $kategori->isDirty('slug')) {
                $baseSlug = \Illuminate\Support\Str::slug($kategori->name ?: $kategori->ad);
                $slug = $baseSlug;
                $counter = 1;

                // Unique slug oluştur (mevcut kategori hariç)
                while (static::where('slug', $slug)->where('id', '!=', $kategori->id)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }

                $kategori->slug = $slug;
            }
        });
    }

    /**
     * Kategori oluşturma için validation kuralları
     */
    public static function getValidationRules($id = null): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('ilan_kategorileri')
                    ->where('parent_id', request('parent_id'))
                    ->where('seviye', request('seviye'))
                    ->ignore($id),
            ],
            'parent_id' => 'nullable|exists:ilan_kategorileri,id',
            'seviye' => 'required|integer|in:0,1,2',
            'status' => 'boolean',
            'display_order' => 'nullable|integer|min:1',
            // REMOVED: 'order' => 'nullable|integer|min:1', // Backward compatibility - REMOVED: Causes Laravel to select 'order' column
            'icon' => 'nullable|string|max:100',
            'aciklama' => 'nullable|string|max:500',
        ];
    }

    /**
     * Seviye açıklamaları
     */
    public static function getSeviyeAciklamalari(): array
    {
        return [
            0 => 'Ana Kategori',
            1 => 'Alt Kategori',
            2 => 'Yayın Tipi',
        ];
    }

    /**
     * Seviye açıklaması accessor
     */
    public function getSeviyeAciklamasiAttribute(): string
    {
        return self::getSeviyeAciklamalari()[$this->seviye] ?? 'Bilinmeyen';
    }

    /**
     * Kategori yolunu getir (Breadcrumb için)
     */
    public function getPathAttribute(): string
    {
        $path = collect();
        $current = $this;

        while ($current) {
            $path->prepend($current->name);
            $current = $current->parent;
        }

        return $path->implode(' > ');
    }

    /**
     * Alt kategori sayısı
     */
    public function getChildrenCountAttribute(): int
    {
        return $this->children()->count();
    }

    /**
     * Kategoride status çocuk var mı?
     */
    public function hasActiveChildren(): bool
    {
        return $this->children()->where('status', true)->exists();
    }

    /**
     * Description accessor for backward compatibility
     */
    public function getDescriptionAttribute()
    {
        return $this->aciklama;
    }

    /**
     * Description mutator for backward compatibility
     */
    public function setDescriptionAttribute($value)
    {
        $this->attributes['aciklama'] = $value;
    }
}
