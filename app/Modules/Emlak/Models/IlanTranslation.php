<?php

namespace App\Modules\Emlak\Models;

use App\Modules\BaseModule\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $ilan_id
 * @property string $locale
 * @property string|null $baslik
 * @property string|null $slug
 * @property string|null $aciklama
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Modules\Emlak\Models\Ilan $ilan
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTranslation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTranslation whereAciklama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTranslation whereBaslik($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTranslation whereIlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTranslation whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTranslation whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTranslation withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTranslation withoutTrashed()
 *
 * @mixin \Eloquent
 */
class IlanTranslation extends BaseModel
{
    use SoftDeletes;

    /**
     * İlişkilendirilmiş tablo adı
     *
     * @var string
     */
    protected $table = 'ilan_translations';

    /**
     * Toplu atanabilir alanlar
     *
     * @var array
     */
    protected $fillable = [
        'ilan_id',
        'locale',
        'baslik',
        'aciklama',
        'slug',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    /**
     * Bu çevirinin ait olduğu ilan
     */
    public function ilan()
    {
        return $this->belongsTo(Ilan::class, 'ilan_id');
    }
}
