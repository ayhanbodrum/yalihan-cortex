<?php

namespace App\Modules\Talep\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TalepAnaliz extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'talep_analizler';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'talep_id',
        'kullanici_id',
        'analiz_verisi',
        'eslesen_ilanlar',
        'eslesme_skoru',
        'analiz_tarihi',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'analiz_verisi' => 'array',
        'eslesen_ilanlar' => 'array',
        'analiz_tarihi' => 'datetime',
    ];

    /**
     * Talep ile ilişki
     */
    public function talep()
    {
        return $this->belongsTo(\App\Models\Talep::class);
    }

    /**
     * Kullanıcı ile ilişki
     */
    public function kullanici()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Eşleşme skoruna göre eşleşen ilanları grupla
     */
    public function gruplandirEslesmeler()
    {
        $eslesmeler = collect($this->eslesen_ilanlar ?? []);

        return [
            'yuksek' => $eslesmeler->where('eslesme_yuzdesi', '>=', 80)->values(),
            'orta' => $eslesmeler->where('eslesme_yuzdesi', '>=', 60)->where('eslesme_yuzdesi', '<', 80)->values(),
            'dusuk' => $eslesmeler->where('eslesme_yuzdesi', '<', 60)->values(),
        ];
    }
}
