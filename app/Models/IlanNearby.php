<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * İlan Nearby Places Model
 *
 * Context7: WikiMapia ve TurkiyeAPI entegrasyonu için
 * nearby places verilerini saklar
 */
class IlanNearby extends Model
{
    use HasFactory;

    protected $table = 'ilan_nearby';

    protected $fillable = [
        'ilan_id',
        'summary',
        'items',
        'fetched_at',
    ];

    protected $casts = [
        'summary' => 'array',
        'items' => 'array',
        'fetched_at' => 'datetime',
    ];

    /**
     * İlan ilişkisi
     */
    public function ilan()
    {
        return $this->belongsTo(Ilan::class, 'ilan_id');
    }
}
