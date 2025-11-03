<?php

namespace App\Modules\Crm\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $ad
 * @property string|null $renk
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Modules\Crm\Models\Kisi> $kisiler
 * @property-read int|null $kisiler_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Etiket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Etiket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Etiket query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Etiket whereAd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Etiket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Etiket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Etiket whereRenk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Etiket whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Etiket extends Model
{
    use HasFactory;

    protected $table = 'etiketler';

    protected $fillable = [
        'ad',
        'renk',
    ];

    /**
     * Bu etikete sahip kişileri getiren ilişki.
     */
    public function kisiler()
    {
        return $this->belongsToMany(Kisi::class, 'etiket_kisi', 'etiket_id', 'kisi_id')->withTimestamps();
    }
}
