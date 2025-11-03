<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\KisiNot
 *
 * @property int $id
 * @property int $kisi_id
 * @property int $user_id
 * @property string $aciklama
 * @property string $tip
 * @property bool $onemli
 * @property \Carbon\Carbon|null $görüşme_tarihi
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Kisi $kisi
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|KisiNot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KisiNot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KisiNot query()
 * @method static \Illuminate\Database\Eloquent\Builder|KisiNot whereAciklama($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KisiNot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KisiNot whereGörüşmeTarihi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KisiNot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KisiNot whereKisiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KisiNot whereOnemli($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KisiNot whereTip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KisiNot whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KisiNot whereUserId($value)
 *
 * @mixin \Eloquent
 */
class KisiNot extends Model
{
    use HasFactory;

    protected $table = 'kisi_notlar';

    protected $fillable = [
        'kisi_id',
        'user_id',
        'aciklama',
        'tip',
        'onemli',
        'görüşme_tarihi',
    ];

    protected $casts = [
        'onemli' => 'boolean',
        'görüşme_tarihi' => 'datetime',
    ];

    /**
     * Bu notun ait olduğu kişi
     */
    public function kisi()
    {
        return $this->belongsTo(Kisi::class, 'kisi_id');
    }

    /**
     * Bu notu ekleyen kullanıcı
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Önemli notları getir
     */
    public function scopeOnemli($query)
    {
        return $query->where('onemli', true);
    }

    /**
     * Tip bazlı filtreleme
     */
    public function scopeTip($query, $tip)
    {
        return $query->where('tip', $tip);
    }

    /**
     * Tarih sıralaması (en yeni önce)
     */
    public function scopeYeni($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Görüşme tarihi yaklaşan notlar
     */
    public function scopeGörüşmeYaklaşan($query)
    {
        return $query->whereNotNull('görüşme_tarihi')
            ->where('görüşme_tarihi', '>=', now())
            ->where('görüşme_tarihi', '<=', now()->addDays(7))
            ->orderBy('görüşme_tarihi', 'asc');
    }

    /**
     * Not tipinin rengini getir
     */
    public function getTipRengiAttribute()
    {
        $renkler = [
            'genel' => '#6B7280',
            'arama' => '#3B82F6',
            'görüşme' => '#10B981',
            'satış' => '#F59E0B',
            'diğer' => '#8B5CF6',
        ];

        return $renkler[$this->tip] ?? '#6B7280';
    }

    /**
     * Not tipinin ikonunu getir
     */
    public function getTipIkonuAttribute()
    {
        $ikonlar = [
            'genel' => 'fas fa-sticky-note',
            'arama' => 'fas fa-phone',
            'görüşme' => 'fas fa-calendar-alt',
            'satış' => 'fas fa-handshake',
            'diğer' => 'fas fa-info-circle',
        ];

        return $ikonlar[$this->tip] ?? 'fas fa-sticky-note';
    }
}
