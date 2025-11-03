<?php

namespace App\Modules\Emlak\Models;

use App\Modules\BaseModule\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $feature_id
 * @property string $locale
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Modules\Emlak\Models\Feature $feature
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureTranslation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureTranslation whereFeatureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureTranslation whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureTranslation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureTranslation withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureTranslation withoutTrashed()
 *
 * @mixin \Eloquent
 */
class FeatureTranslation extends BaseModel
{
    use SoftDeletes;

    /**
     * İlişkilendirilmiş tablo adı
     *
     * @var string
     */
    protected $table = 'feature_translations';

    /**
     * Toplu atanabilir alanlar
     *
     * @var array
     */
    protected $fillable = [
        'feature_id',
        'locale',
        'name',
        'description',
    ];

    /**
     * Bu çevirinin ait olduğu özellik
     */
    public function feature()
    {
        return $this->belongsTo(Feature::class, 'feature_id');
    }
}
