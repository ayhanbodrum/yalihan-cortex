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
        'display_order', // Context7: order → display_order (migration: 2025_11_11_103353)
        'status', // Context7: status → status
    ];

    protected $casts = [
        'status' => 'boolean', // Context7: status → status
        'display_order' => 'integer', // Context7: order → display_order
        'veri_secenekleri' => 'array',
        'zorunlu' => 'boolean',
    ];

    public function kategori()
    {
        return $this->belongsTo(OzellikKategori::class, 'kategori_id');
    }
}
