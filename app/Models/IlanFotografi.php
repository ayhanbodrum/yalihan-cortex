<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $ilan_id
 * @property string $dosya_yolu
 * @property int $sira
 * @property bool $kapak_fotografi
 * @property string|null $alt_text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Ilan $ilan
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanFotografi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanFotografi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanFotografi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanFotografi whereAltText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanFotografi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanFotografi whereDosyaYolu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanFotografi whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanFotografi whereIlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanFotografi whereKapakFotografi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanFotografi whereSira($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanFotografi whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class IlanFotografi extends Model
{
    use HasFactory;

    protected $table = 'ilan_fotograflari';

    protected $fillable = [
        'ilan_id',
        'dosya_yolu',
        'sira',
        'kapak_fotografi',
        'alt_text',
    ];

    protected $casts = [
        'kapak_fotografi' => 'boolean',
        'sira' => 'integer',
    ];

    /**
     * İlana erişim için ilişki
     */
    public function ilan()
    {
        return $this->belongsTo(Ilan::class, 'ilan_id');
    }

    /**
     * Accessor: Fotoğraf URL'i
     */
    public function getUrlAttribute()
    {
        return $this->dosya_yolu ? \Illuminate\Support\Facades\Storage::url($this->dosya_yolu) : null;
    }
}
