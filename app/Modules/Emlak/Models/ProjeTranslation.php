<?php

namespace App\Modules\Emlak\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $proje_id
 * @property string $locale
 * @property string $proje_adi
 * @property string|null $aciklama
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Modules\Emlak\Models\Proje $proje
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjeTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjeTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjeTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjeTranslation whereAciklama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjeTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjeTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjeTranslation whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjeTranslation whereProjeAdi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjeTranslation whereProjeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjeTranslation whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ProjeTranslation extends Model
{
    /**
     * İlişkilendirilmiş tablo adı
     */
    protected $table = 'proje_translations';

    /**
     * Toplu atanabilir alanlar
     */
    protected $fillable = [
        'proje_id',
        'locale',
        'proje_adi',
        'aciklama',
    ];

    /**
     * Çevirinin ait olduğu proje
     */
    public function proje()
    {
        return $this->belongsTo(Proje::class, 'proje_id');
    }
}
