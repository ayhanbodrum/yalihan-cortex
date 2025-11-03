<?php

namespace App\Modules\Emlak\Models;

use App\Modules\BaseModule\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $category_id
 * @property string $slug
 * @property string|null $name
 * @property int $is_filterable
 * @property int $show_on_card
 * @property int $display_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Modules\Emlak\Models\FeatureCategory $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Modules\Emlak\Models\Ilan> $ilanlar
 * @property-read int|null $ilanlar_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, FeatureTranslation> $translations
 * @property-read int|null $translations_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereDisplayOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereIsFilterable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereShowOnCard($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Feature extends BaseModel
{
    use SoftDeletes;

    /**
     * İlişkilendirilmiş tablo adı
     *
     * @var string
     */
    protected $table = 'features';

    /**
     * Toplu atanabilir alanlar
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'slug',
        'name', // Yeni eklenen name sütunu
        'applies_to', // Uygulama alanı
        'is_filterable',
        'show_on_card',
        'display_order',
        'type', // text, number, boolean, select vb.
        'required',
        'searchable',
        'options', // seçenekler (JSON)
        'unit', // birim (m², adet vb.)
    ];

    /**
     * Otomatik olarak yüklenecek ilişkiler
     */
    protected $with = [];

    /**
     * Cast edilecek özellikler
     *
     * @var array
     */
    protected $casts = [
        'required' => 'boolean',
        'searchable' => 'boolean',
        'options' => 'json',
    ];

    /**
     * Özelliğin kategorisi
     */
    public function category()
    {
        return $this->belongsTo(FeatureCategory::class, 'category_id');
    }

    /**
     * Özelliğin çevirileri
     */
    public function translations()
    {
        return $this->hasMany(FeatureTranslation::class, 'feature_id');
    }

    /**
     * Özellik adını çevirilerden al
     */
    public function getName(): string
    {
        // Eğer name sütunu doluysa onu kullan
        if (! empty($this->name)) {
            return $this->name;
        }

        // Yoksa çevirilerden al
        if ($this->translations->isNotEmpty()) {
            $turkishTranslation = $this->translations->where('locale', 'tr')->first();
            if ($turkishTranslation) {
                return $turkishTranslation->getAttribute('name');
            }

            return $this->translations->first()->getAttribute('name');
        }

        return $this->slug ?? 'İsimsiz Özellik';
    }

    /**
     * Özellik adını set et ve kaydet
     */
    public function setNameFromTranslations(): void
    {
        if (empty($this->name) && $this->translations->isNotEmpty()) {
            $turkishTranslation = $this->translations->where('locale', 'tr')->first();
            if ($turkishTranslation) {
                $this->name = $turkishTranslation->getAttribute('name');
            } else {
                $this->name = $this->translations->first()->getAttribute('name');
            }
            $this->save();
        }
    }

    // Name accessor'u kaldırıldı

    /**
     * Bu özelliğe sahip ilanlar
     */
    public function ilanlar()
    {
        return $this->belongsToMany(Ilan::class, 'listing_feature', 'feature_id', 'ilan_id')
            ->withTimestamps();
    }
}
