<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $ilan_id
 * @property int $talep_id
 * @property int $one_cikan
 * @property string $status
 * @property string|null $notlar
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Ilan $ilan
 * @property-read \App\Models\Talep $talep
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTalepEslesme newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTalepEslesme newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTalepEslesme query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTalepEslesme whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTalepEslesme whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTalepEslesme whereOneCikan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTalepEslesme whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTalepEslesme whereIlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTalepEslesme whereNotlar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTalepEslesme whereTalepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTalepEslesme whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class IlanTalepEslesme extends Model
{
    use HasFactory;

    protected $table = 'eslesmeler';

    protected $fillable = [
        'ilan_id',
        'talep_id',
        'one_cikan',
        'status',
        'notlar',
    ];

    protected $casts = [
        'one_cikan' => 'integer',
    ];

    /**
     * Eşleşmenin ilanına erişim için ilişki
     */
    public function ilan()
    {
        return $this->belongsTo(Ilan::class, 'ilan_id');
    }

    /**
     * Eşleşmenin talebine erişim için ilişki
     */
    public function talep()
    {
        return $this->belongsTo(Talep::class, 'talep_id');
    }
}
