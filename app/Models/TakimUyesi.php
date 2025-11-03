<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TakimUyesi extends Model
{
    use HasFactory;

    protected $table = 'takim_uyeleri';

    protected $fillable = [
        'user_id',
        'pozisyon',
        'departman',
        'lokasyon',
        'performans_skoru',
        'ise_baslama_tarihi',
        'status',
        'notlar',
    ];

    protected $casts = [
        'ise_baslama_tarihi' => 'date',
        'performans_skoru' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
