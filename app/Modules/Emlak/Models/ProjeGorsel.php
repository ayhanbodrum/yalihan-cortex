<?php

namespace App\Modules\Emlak\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $proje_id
 * @property string $dosya_yolu
 * @property int $sira
 * @property string|null $aciklama
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Modules\Emlak\Models\Proje $proje
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjeGorsel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjeGorsel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjeGorsel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjeGorsel whereAciklama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjeGorsel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjeGorsel whereDosyaYolu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjeGorsel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjeGorsel whereProjeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjeGorsel whereSira($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjeGorsel whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ProjeGorsel extends Model
{
    /**
     * İlişkilendirilmiş tablo adı
     */
    protected $table = 'proje_gorselleri';

    /**
     * Toplu atanabilir alanlar
     */
    protected $fillable = [
        'proje_id',
        'dosya_yolu',
        'sira',
        'aciklama',
    ];

    /**
     * Görselin ait olduğu proje
     */
    public function proje()
    {
        return $this->belongsTo(Proje::class, 'proje_id');
    }
}
