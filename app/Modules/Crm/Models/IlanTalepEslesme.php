<?php

namespace App\Modules\Crm\Models;

use App\Modules\BaseModule\Models\BaseModel;
use App\Modules\Emlak\Models\Ilan;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read Ilan|null $ilan
 * @property-read \App\Modules\Crm\Models\Talep|null $talep
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTalepEslesme newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTalepEslesme newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTalepEslesme onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTalepEslesme query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTalepEslesme withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IlanTalepEslesme withoutTrashed()
 *
 * @mixin \Eloquent
 */
class IlanTalepEslesme extends BaseModel
{
    use SoftDeletes;

    /**
     * İlişkilendirilmiş tablo adı
     */
    protected $table = 'ilan_talep_eslesme';

    /**
     * Toplu atanabilir alanlar
     */
    protected $fillable = [
        'ilan_id',
        'talep_id',
        'eslesme_statusu', // Yeni, İncelendi, Gösterildi, Beğenilmedi, vb.
        'eslesme_puani',
        'yorumlar',
    ];

    /**
     * Cast edilecek özellikler
     */
    protected $casts = [
        'eslesme_puani' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Eşleştirilen ilan
     */
    public function ilan()
    {
        return $this->belongsTo(Ilan::class, 'ilan_id');
    }

    /**
     * Eşleştirilen talep
     */
    public function talep()
    {
        return $this->belongsTo(Talep::class, 'talep_id');
    }
}
