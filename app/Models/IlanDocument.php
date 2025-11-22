<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IlanDocument extends Model
{
    protected $table = 'ilan_dokumanlar';

    protected $fillable = [
        'ilan_id',
        'title',
        'type',
        'url',
        'path',
        'description',
        'created_by',
    ];

    public function ilan(): BelongsTo
    {
        return $this->belongsTo(Ilan::class, 'ilan_id');
    }
}