<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KisiEtkilesim extends Model
{
    use HasFactory;

    protected $table = 'kisi_etkilesimler';

    protected $fillable = [
        'kisi_id',
        'kullanici_id',
        'tip',
        'notlar',
        'etkilesim_tarihi',
        'status',
        'display_order',
    ];

    protected $casts = [
        'etkilesim_tarihi' => 'datetime',
        'status' => 'integer',
        'display_order' => 'integer',
    ];

    /**
     * İlişkiler
     */
    public function kisi()
    {
        return $this->belongsTo(Kisi::class);
    }

    public function kullanici()
    {
        return $this->belongsTo(User::class, 'kullanici_id');
    }

    /**
     * Scope'lar
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 1);
    }

    public function scopeTipGore($query, $tip)
    {
        return $query->where('tip', $tip);
    }

    public function scopeSonEtkilesimler($query, $limit = 10)
    {
        return $query->orderBy('etkilesim_tarihi', 'desc')->limit($limit);
    }
}
