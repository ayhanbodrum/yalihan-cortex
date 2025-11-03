<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $feature_id
 * @property string $locale
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureTranslation whereFeatureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureTranslation whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureTranslation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureTranslation whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class FeatureTranslation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'feature_id',
        'locale',
        'name',
        'description',
    ];
}
