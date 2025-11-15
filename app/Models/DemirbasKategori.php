<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class DemirbasKategori extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'parent_id',
        'kategori_id', // ✅ Context7: Hangi ilan kategorisi için geçerli
        'yayin_tipi_id', // ✅ Context7: Hangi yayın tipi için geçerli
        'display_order', // ✅ Context7: order → display_order
        'status', // ✅ Context7: boolean status
    ];

    protected $casts = [
        'status' => 'boolean', // ✅ Context7: boolean status
        'display_order' => 'integer', // ✅ Context7: display_order
    ];

    /**
     * Üst kategori ilişkisi (self-referencing)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(DemirbasKategori::class, 'parent_id');
    }

    /**
     * Alt kategoriler ilişkisi
     */
    public function children(): HasMany
    {
        return $this->hasMany(DemirbasKategori::class, 'parent_id')
            ->where('status', true)
            ->orderBy('display_order');
    }

    /**
     * Demirbaşlar ilişkisi
     */
    public function demirbaslar(): HasMany
    {
        return $this->hasMany(Demirbas::class, 'kategori_id')
            ->where('status', true)
            ->orderBy('display_order');
    }

    /**
     * İlan kategorisi ilişkisi
     */
    public function ilanKategori(): BelongsTo
    {
        return $this->belongsTo(IlanKategori::class, 'kategori_id');
    }

    /**
     * Yayın tipi ilişkisi
     */
    public function yayinTipi(): BelongsTo
    {
        return $this->belongsTo(IlanKategoriYayinTipi::class, 'yayin_tipi_id');
    }

    /**
     * Aktif scope
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Kategoriye göre filtrele
     */
    public function scopeForKategori($query, $kategoriId)
    {
        return $query->where(function ($q) use ($kategoriId) {
            $q->where('kategori_id', $kategoriId)
                ->orWhereNull('kategori_id'); // Tüm kategoriler için geçerli olanlar
        });
    }

    /**
     * Yayın tipine göre filtrele
     */
    public function scopeForYayinTipi($query, $yayinTipiId)
    {
        return $query->where(function ($q) use ($yayinTipiId) {
            $q->where('yayin_tipi_id', $yayinTipiId)
                ->orWhereNull('yayin_tipi_id'); // Tüm yayın tipleri için geçerli olanlar
        });
    }

    /**
     * Auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($kategori) {
            if (empty($kategori->slug)) {
                $baseSlug = Str::slug($kategori->name);
                $slug = $baseSlug;
                $counter = 1;

                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }

                $kategori->slug = $slug;
            }
        });
    }
}
