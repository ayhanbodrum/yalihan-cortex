<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KisiTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'kisi_id',
        'kullanici_id',
        'baslik',
        'aciklama',
        'tarih',
        'saat',
        'oncelik',
        'status',
        'tamamlanma_tarihi',
        'display_order',
    ];

    protected $casts = [
        'tarih' => 'date',
        'tamamlanma_tarihi' => 'datetime',
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
    public function scopeBekleyen($query)
    {
        return $query->where('status', 0);
    }

    public function scopeTamamlanan($query)
    {
        return $query->where('status', 1);
    }

    public function scopeBugun($query)
    {
        return $query->whereDate('tarih', today());
    }

    public function scopeGecmis($query)
    {
        return $query->where('tarih', '<', today())->where('status', 0);
    }

    /**
     * Mutators
     */
    public function getOncelikRenkAttribute()
    {
        return match ($this->oncelik) {
            'kritik' => 'red',
            'yuksek' => 'orange',
            'normal' => 'blue',
            'dusuk' => 'gray',
            default => 'gray',
        };
    }
}
