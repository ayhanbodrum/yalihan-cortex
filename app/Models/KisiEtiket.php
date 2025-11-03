<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * KisiEtiket Pivot Model
 *
 * @property int $id
 * @property int $kisi_id
 * @property int $etiket_id
 * @property int $user_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class KisiEtiket extends Pivot
{
    protected $table = 'kisi_etiket';

    protected $fillable = [
        'kisi_id',
        'etiket_id',
        'user_id',
    ];

    /**
     * Kisi ile ilişki
     */
    public function kisi()
    {
        return $this->belongsTo(Kisi::class, 'kisi_id');
    }

    /**
     * Etiket ile ilişki
     */
    public function etiket()
    {
        return $this->belongsTo(MusteriEtiket::class, 'etiket_id');
    }

    /**
     * User ile ilişki
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
