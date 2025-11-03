<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MusteriNot extends Model
{
    use HasFactory;

    protected $table = 'musteri_notlar';

    protected $fillable = [
        'kisi_id',
        'user_id',
        'not',
        'tip',
        'hatirlatma_tarihi',
        'tamamlandi',
    ];

    protected $casts = [
        'hatirlatma_tarihi' => 'datetime',
        'tamamlandi' => 'boolean',
    ];

    /**
     * Bu notun sahibi kişi
     */
    public function kisi(): BelongsTo
    {
        return $this->belongsTo(Kisi::class);
    }

    /**
     * Bu notu oluşturan kullanıcı
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Önemli notlar
     */
    public function scopeOnemli($query)
    {
        return $query->where('tip', 'Önemli');
    }

    /**
     * Hatırlatma notları
     */
    public function scopeHatirlatma($query)
    {
        return $query->where('tip', 'Hatırlatma');
    }

    /**
     * Tamamlanmamış notlar
     */
    public function scopeTamamlanmamis($query)
    {
        return $query->where('tamamlandi', false);
    }

    /**
     * Geciken hatırlatmalar
     */
    public function scopeGecikenHatirlatmalar($query)
    {
        return $query->where('tip', 'Hatırlatma')
            ->where('tamamlandi', false)
            ->where('hatirlatma_tarihi', '<', now());
    }
}
