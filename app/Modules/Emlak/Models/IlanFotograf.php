<?php

namespace App\Modules\Emlak\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read string $url
 * @property-read \App\Modules\Emlak\Models\Ilan|null $ilan
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanFotograf anaFotograf()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanFotograf newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanFotograf newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanFotograf query()
 *
 * @mixin \Eloquent
 */
class IlanFotograf extends Model
{
    use HasFactory;

    /**
     * Tablo adı
     *
     * @var string
     */
    protected $table = 'ilan_fotograflar';

    /**
     * Toplu atanabilir alanlar
     *
     * @var array
     */
    protected $fillable = [
        'ilan_id', 'fotograf_yolu', 'sira', 'ana_fotograf',
    ];

    /**
     * Fotoğrafın bağlı olduğu ilan ilişkisi
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ilan()
    {
        return $this->belongsTo(Ilan::class, 'ilan_id');
    }

    /**
     * Ana fotoğrafları filtreler
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAnaFotograf($query)
    {
        return $query->where('ana_fotograf', true);
    }

    /**
     * Fotoğraf URL'ini döndürür
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return asset('storage/'.$this->fotograf_yolu);
    }
}
