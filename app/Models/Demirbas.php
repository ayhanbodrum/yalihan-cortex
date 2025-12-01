<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Demirbas extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'brand',
        'icon',
        'description',
        'kategori_id', // ✅ Context7: Hangi demirbaş kategorisi altında
        'ilan_kategori_id', // ✅ Context7: Hangi ilan kategorisi için geçerli (opsiyonel)
        'yayin_tipi_id', // ✅ Context7: Hangi yayın tipi için geçerli (opsiyonel)
        'display_order', // ✅ Context7: order → display_order
        'status', // ✅ Context7: boolean status
    ];

    protected $casts = [
        'status' => 'boolean', // ✅ Context7: boolean status
        'display_order' => 'integer', // ✅ Context7: display_order
    ];

    /**
     * Demirbaş kategorisi ilişkisi
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(DemirbasKategori::class, 'kategori_id');
    }

    /**
     * İlan kategorisi ilişkisi
     */
    public function ilanKategori(): BelongsTo
    {
        return $this->belongsTo(IlanKategori::class, 'ilan_kategori_id');
    }

    /**
     * Yayın tipi ilişkisi
     */
    public function yayinTipi(): BelongsTo
    {
        return $this->belongsTo(IlanKategoriYayinTipi::class, 'yayin_tipi_id');
    }

    /**
     * İlanlar ilişkisi (pivot)
     */
    public function ilanlar()
    {
        return $this->belongsToMany(Ilan::class, 'ilan_demirbas', 'demirbas_id', 'ilan_id')
            ->withPivot(['brand', 'model', 'quantity', 'notes', 'display_order', 'status'])
            ->withTimestamps();
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
            $q->where('ilan_kategori_id', $kategoriId)
                ->orWhereNull('ilan_kategori_id'); // Tüm kategoriler için geçerli olanlar
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

        static::creating(function ($demirbas) {
            if (empty($demirbas->slug)) {
                $baseSlug = Str::slug($demirbas->name);
                $slug = $baseSlug;
                $counter = 1;

                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug.'-'.$counter;
                    $counter++;
                }

                $demirbas->slug = $slug;
            }
        });
    }
}
