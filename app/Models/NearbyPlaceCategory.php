<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

/**
 * @property int $id
 * @property array<array-key, mixed> $name
 * @property string $slug
 * @property string|null $icon
 * @property string|null $api_keyword
 * @property bool $status
 * @property int $display_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translations
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NearbyPlaceCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NearbyPlaceCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NearbyPlaceCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NearbyPlaceCategory whereApiKeyword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NearbyPlaceCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NearbyPlaceCategory whereDisplayOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NearbyPlaceCategory whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NearbyPlaceCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NearbyPlaceCategory whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NearbyPlaceCategory whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NearbyPlaceCategory whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NearbyPlaceCategory whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NearbyPlaceCategory whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NearbyPlaceCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NearbyPlaceCategory whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NearbyPlaceCategory whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class NearbyPlaceCategory extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = ['name'];

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'api_keyword',
        'status',
        'display_order',
    ];

    protected $casts = [
        'name' => 'array',
        'status' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $nameTr = is_array($category->name) ? ($category->name['tr'] ?? '') : $category->name;
                if (is_string($nameTr) && ! empty($nameTr)) {
                    $category->slug = Str::slug($nameTr);
                } elseif (is_array($category->name) && ! empty($category->name)) {
                    // Eğer 'tr' yoksa, mevcut ilk dil çevirisini kullan
                    $firstLocaleValue = reset($category->name);
                    if (is_string($firstLocaleValue) && ! empty($firstLocaleValue)) {
                        $category->slug = Str::slug($firstLocaleValue);
                    } else {
                        // Geçici benzersiz bir slug oluştur
                        $category->slug = 'temp-'.uniqid();
                    }
                } else {
                    // İsim boşsa veya uygun değilse geçici slug
                    $category->slug = 'temp-'.uniqid();
                }
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->getOriginal('slug'))) {
                $nameTr = is_array($category->name) ? ($category->name['tr'] ?? '') : $category->name;
                if (is_string($nameTr) && ! empty($nameTr)) {
                    $category->slug = Str::slug($nameTr);
                } elseif (is_array($category->name) && ! empty($category->name)) {
                    $firstLocaleValue = reset($category->name);
                    if (is_string($firstLocaleValue) && ! empty($firstLocaleValue)) {
                        $category->slug = Str::slug($firstLocaleValue);
                    }
                }
            }
        });
    }
}
