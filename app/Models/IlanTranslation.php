<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $ilan_id
 * @property string $locale
 * @property string|null $baslik
 * @property string|null $slug
 * @property string|null $aciklama
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Ilan $ilan
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTranslation whereAciklama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTranslation whereBaslik($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTranslation whereIlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTranslation whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTranslation whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTranslation whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class IlanTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['ilan_id', 'locale', 'baslik', 'slug', 'aciklama'];

    /**
     * İlana erişim için ilişki
     */
    public function ilan()
    {
        return $this->belongsTo(Ilan::class, 'ilan_id');
    }
}
