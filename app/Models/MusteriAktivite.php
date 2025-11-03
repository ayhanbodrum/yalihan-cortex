<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MusteriAktivite extends Model
{
    use HasFactory;

    protected $table = 'musteri_aktiviteler';

    protected $fillable = [
        'kisi_id',
        'user_id',
        'aktivite_tipi',
        'aciklama',
        'aktivite_tarihi',
        'status',
        'detaylar',
    ];

    protected $casts = [
        'aktivite_tarihi' => 'datetime',
        'detaylar' => 'array',
    ];

    /**
     * Bu aktivitenin sahibi kişi
     */
    public function kisi(): BelongsTo
    {
        return $this->belongsTo(Kisi::class);
    }

    /**
     * Bu aktiviteyi oluşturan kullanıcı
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Bugünkü aktiviteler
     */
    public function scopeBugun($query)
    {
        return $query->whereDate('aktivite_tarihi', today());
    }

    /**
     * Bu haftaki aktiviteler
     */
    public function scopeBuHafta($query)
    {
        return $query->whereBetween('aktivite_tarihi', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Bu ayki aktiviteler
     */
    public function scopeBuAy($query)
    {
        return $query->whereBetween('aktivite_tarihi', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ]);
    }

    /**
     * Tamamlanan aktiviteler
     */
    public function scopeTamamlandi($query)
    {
        return $query->where('status', 'Tamamlandı');
    }

    /**
     * Bekleyen aktiviteler
     */
    public function scopeBekleyen($query)
    {
        return $query->where('status', 'Bekliyor');
    }
}
