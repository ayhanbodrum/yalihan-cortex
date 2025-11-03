<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $feature_category_id
 * @property string $locale
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
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
    protected $fillable = [
        'feature_category_id',
        'locale',
        'name',
    ];
}
