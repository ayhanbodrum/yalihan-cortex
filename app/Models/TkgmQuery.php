<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 * 🗃️ TKGM QUERY MODEL
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 *
 * Her TKGM sorgusu bu tabloya kaydedilir.
 * Learning Engine bu verilerden öğrenir.
 *
 * @property int $id
 * @property string|null $ada
 * @property string|null $parsel
 * @property int|null $il_id
 * @property int|null $ilce_id
 * @property int|null $mahalle_id
 * @property float|null $enlem
 * @property float|null $boylam
 * @property float|null $alan_m2
 * @property float|null $kaks
 * @property int|null $taks
 * @property string|null $imar_statusu
 * @property string|null $nitelik
 * @property int|null $gabari
 * @property int|null $ilan_id
 * @property float|null $satis_fiyati
 * @property string|null $satis_tarihi
 * @property int|null $satis_suresi_gun
 * @property string $query_source
 * @property int|null $user_id
 * @property string $queried_at
 * @property array|null $tkgm_raw_data
 * @property int $status
 *
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 */
class TkgmQuery extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tkgm_queries';

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // ✅ CONTEXT7: FILLABLE FIELDS
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    protected $fillable = [
        // Ada/Parsel
        'ada',
        'parsel',

        // Lokasyon
        'il_id',
        'ilce_id',
        'mahalle_id',
        'enlem',
        'boylam',

        // TKGM Verileri
        'alan_m2',
        'kaks',
        'taks',
        'imar_statusu',
        'nitelik',
        'gabari',

        // İlan & Satış
        'ilan_id',
        'satis_fiyati',
        'satis_tarihi',
        'satis_suresi_gun',

        // Meta
        'query_source',
        'user_id',
        'queried_at',
        'tkgm_raw_data',
        'status',
    ];

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // ✅ CONTEXT7: CASTS
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    protected $casts = [
        'il_id' => 'integer',
        'ilce_id' => 'integer',
        'mahalle_id' => 'integer',
        'enlem' => 'decimal:8',
        'boylam' => 'decimal:8',
        'alan_m2' => 'decimal:2',
        'kaks' => 'decimal:2',
        'taks' => 'integer',
        'gabari' => 'integer',
        'ilan_id' => 'integer',
        'satis_fiyati' => 'decimal:2',
        'satis_tarihi' => 'date',
        'satis_suresi_gun' => 'integer',
        'user_id' => 'integer',
        'queried_at' => 'datetime',
        'tkgm_raw_data' => 'array',
        'status' => 'boolean',
    ];

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // 🔗 RELATIONSHIPS
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    /**
     * İl ilişkisi
     */
    public function il()
    {
        return $this->belongsTo(\App\Models\Il::class, 'il_id');
    }

    /**
     * İlçe ilişkisi
     */
    public function ilce()
    {
        return $this->belongsTo(\App\Models\Ilce::class, 'ilce_id');
    }

    /**
     * Mahalle ilişkisi
     */
    public function mahalle()
    {
        return $this->belongsTo(\App\Models\Mahalle::class, 'mahalle_id');
    }

    /**
     * İlan ilişkisi (opsiyonel)
     */
    public function ilan()
    {
        return $this->belongsTo(\App\Models\Ilan::class, 'ilan_id');
    }

    /**
     * Kullanıcı ilişkisi
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // 📊 SCOPES (Hızlı filtreleme)
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    /**
     * Sadece satılanlar
     */
    public function scopeSold($query)
    {
        return $query->whereNotNull('satis_fiyati');
    }

    /**
     * Belirli lokasyon
     */
    public function scopeLocation($query, $ilId, $ilceId = null, $mahalleId = null)
    {
        $query->where('il_id', $ilId);

        if ($ilceId) {
            $query->where('ilce_id', $ilceId);
        }

        if ($mahalleId) {
            $query->where('mahalle_id', $mahalleId);
        }

        return $query;
    }

    /**
     * Belirli tarih aralığı
     */
    public function scopeDateRange($query, $startDate, $endDate = null)
    {
        $query->where('queried_at', '>=', $startDate);

        if ($endDate) {
            $query->where('queried_at', '<=', $endDate);
        }

        return $query;
    }

    /**
     * Aktif kayıtlar
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // 🧮 COMPUTED PROPERTIES
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    /**
     * Birim fiyat (m² başına)
     */
    public function getBirimFiyatAttribute()
    {
        if ($this->satis_fiyati && $this->alan_m2 > 0) {
            return round($this->satis_fiyati / $this->alan_m2, 2);
        }

        return null;
    }

    /**
     * Potansiyel inşaat alanı (KAKS ile)
     */
    public function getInsaatAlaniAttribute()
    {
        if ($this->alan_m2 && $this->kaks) {
            return round($this->alan_m2 * $this->kaks, 2);
        }

        return null;
    }
}
