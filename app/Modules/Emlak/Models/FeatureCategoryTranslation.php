<?php

namespace App\Modules\Emlak\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $feature_category_id
 * @property string $locale
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Modules\Emlak\Models\FeatureCategory|null $category
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureCategoryTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureCategoryTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureCategoryTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureCategoryTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureCategoryTranslation whereFeatureCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureCategoryTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureCategoryTranslation whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureCategoryTranslation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureCategoryTranslation whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class FeatureCategoryTranslation extends Model
{
    /**
     * İlişkilendirilmiş tablo adı
     *
     * @var string
     */
    protected $table = 'feature_category_translations';

    /**
     * Toplu atanabilir alanlar
     *
     * @var array
     */
    protected $fillable = [
        'feature_category_id',
        'locale',
        'name',
        'description',
    ];

    /**
     * Bu çevirinin ait olduğu kategori
     */
    public function category()
    {
        return $this->belongsTo(FeatureCategory::class, 'feature_category_id');
    }
}
