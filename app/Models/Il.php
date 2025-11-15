<?php

namespace App\Models;

use App\Modules\BaseModule\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ilce> $ilceler
 * @property-read int|null $ilceler_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Il newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Il newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Il onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Il query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Il withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Il withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Il extends BaseModel
{
    use SoftDeletes;

    protected $table = 'iller';

    protected $fillable = [
        'il_adi',
        'plaka_kodu',
        'telefon_kodu',
        'lat',
        'lng',
    ];

    /**
     * İle ait ilçeleri getiren ilişki
     */
    public function ilceler()
    {
        return $this->hasMany(Ilce::class, 'il_id');
    }

    /**
     * İlin bağlı olduğu ülkeyi getiren ilişki
     */
    public function ulke()
    {
        return $this->belongsTo(Ulke::class, 'ulke_id');
    }
}
