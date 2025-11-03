<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TuristikTesisOzellik extends Model
{
    use HasFactory;

    protected $table = 'turistik_tesis_ozellikleri';

    protected $fillable = [
        'turistik_tesis_detay_id',
        'ozellik_adi',
        'ozellik_degeri',
        'ozellik_tipi',
        'onem_derecesi',
        'ai_analiz_agirligi',
    ];

    protected $casts = [
        'ai_analiz_agirligi' => 'decimal:2',
    ];

    /**
     * Turistik tesis detay ilişkisi
     */
    public function turistikTesisDetay(): BelongsTo
    {
        return $this->belongsTo(TuristikTesisDetay::class, 'turistik_tesis_detay_id');
    }

    /**
     * Özellik tipleri enum değerleri
     */
    public static function getOzellikTipleri(): array
    {
        return [
            'konaklama' => 'Konaklama',
            'yemek' => 'Yemek',
            'aktivite' => 'Aktivite',
            'hizmet' => 'Hizmet',
            'ozel' => 'Özel',
        ];
    }

    /**
     * Önem dereceleri enum değerleri
     */
    public static function getOnemDereceleri(): array
    {
        return [
            'dusuk' => 'Düşük',
            'orta' => 'Orta',
            'yuksek' => 'Yüksek',
        ];
    }

    /**
     * Özellik tipi etiketi
     */
    public function getOzellikTipiEtiketiAttribute(): string
    {
        return self::getOzellikTipleri()[$this->ozellik_tipi] ?? $this->ozellik_tipi;
    }

    /**
     * Önem derecesi etiketi
     */
    public function getOnemDerecesiEtiketiAttribute(): string
    {
        return self::getOnemDereceleri()[$this->onem_derecesi] ?? $this->onem_derecesi;
    }

    /**
     * Scope: Belirli özellik tipine göre filtrele
     */
    public function scopeOzellikTipi($query, $tip)
    {
        return $query->where('ozellik_tipi', $tip);
    }

    /**
     * Scope: Belirli önem derecesine göre filtrele
     */
    public function scopeOnemDerecesi($query, $derece)
    {
        return $query->where('onem_derecesi', $derece);
    }

    /**
     * Scope: Yüksek önem derecesine göre filtrele
     */
    public function scopeYuksekOnem($query)
    {
        return $query->where('onem_derecesi', 'yuksek');
    }

    /**
     * Scope: AI analiz ağırlığına göre filtrele
     */
    public function scopeAiAnalizAgirligi($query, $min, $max = null)
    {
        if ($max) {
            return $query->whereBetween('ai_analiz_agirligi', [$min, $max]);
        }

        return $query->where('ai_analiz_agirligi', '>=', $min);
    }
}
