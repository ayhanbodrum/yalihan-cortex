<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ozellik extends Model
{
    use HasFactory;

    protected $table = 'ozellikler';

    protected $fillable = [
        'ad',
        'kategori_id',
        'aciklama',
        'veri_tipi',
        'veri_secenekleri',
        'birim',
        'zorunlu',
        'sira',
        'status', // Context7: status → status
    ];

    protected $casts = [
        'status' => 'boolean', // Context7: status → status
        'sira' => 'integer',
        'veri_secenekleri' => 'array',
        'zorunlu' => 'boolean',
    ];

    public function kategori()
    {
        return $this->belongsTo(OzellikKategori::class, 'kategori_id');
    }
}
