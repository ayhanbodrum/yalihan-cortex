<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Market Listing Model
 *
 * Context7: Market Intelligence - Pazar İstihbaratı
 * Dış kaynaklardan (Sahibinden, Hepsiemlak, Emlakjet) çekilecek piyasa verilerini saklar
 */
class MarketListing extends Model
{
    use HasFactory;

    /**
     * Farklı veritabanı bağlantısı kullan
     * Context7: Market Intelligence verileri ayrı veritabanında tutulur
     */
    protected $connection = 'market_intelligence';

    protected $table = 'market_listings';

    protected $fillable = [
        'source',
        'external_id',
        'url',
        'title',
        'price',
        'currency',
        'location_il',
        'location_ilce',
        'location_mahalle',
        'm2_brut',
        'm2_net',
        'room_count',
        'listing_date',
        'last_seen_at',
        'status',
        'snapshot_data',
        'price_history',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'm2_brut' => 'integer',
        'm2_net' => 'integer',
        'listing_date' => 'date',
        'last_seen_at' => 'datetime',
        'status' => 'boolean', // Context7: tinyInteger boolean olarak cast edilir
        'snapshot_data' => 'array',
        'price_history' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope: Aktif ilanlar (status = 1)
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope: Belirli bir kaynaktan gelen ilanlar
     */
    public function scopeSource($query, string $source)
    {
        return $query->where('source', $source);
    }

    /**
     * Scope: Son görülen tarihe göre filtreleme
     */
    public function scopeLastSeenAfter($query, $date)
    {
        return $query->where('last_seen_at', '>=', $date);
    }

    /**
     * Scope: Son görülen tarihe göre filtreleme (önce)
     */
    public function scopeLastSeenBefore($query, $date)
    {
        return $query->where('last_seen_at', '<=', $date);
    }

    /**
     * Fiyat geçmişine yeni kayıt ekle
     */
    public function addPriceHistory(float $price, ?string $date = null): void
    {
        $priceHistory = $this->price_history ?? [];
        $priceHistory[] = [
            'date' => $date ?? now()->toDateString(),
            'price' => $price,
        ];
        $this->price_history = $priceHistory;
        $this->save();
    }

    /**
     * İlanın aktif olup olmadığını kontrol et
     */
    public function isActive(): bool
    {
        return (bool) $this->status;
    }

    /**
     * İlanın pasif olup olmadığını kontrol et
     */
    public function isInactive(): bool
    {
        return !$this->isActive();
    }

    /**
     * İlan yaşını gün cinsinden hesapla
     * Context7: İlan ne kadar zamandır pazarda?
     */
    public function getAgeInDays(): ?int
    {
        if (!$this->listing_date) {
            return null;
        }

        return now()->diffInDays($this->listing_date);
    }

    /**
     * İlan "yorgun" mu? (30 günden fazla pazarda)
     * Context7: Yorgun ilanlar fiyat düşüşüne daha yatkındır
     */
    public function isTired(): bool
    {
        $age = $this->getAgeInDays();
        return $age !== null && $age > 30;
    }

    /**
     * İlan yaş kategorisi
     * Context7: Yeni, Taze, Yorgun, Çok Yorgun
     */
    public function getAgeCategory(): string
    {
        $age = $this->getAgeInDays();

        if ($age === null) {
            return 'bilinmiyor';
        }

        if ($age <= 7) {
            return 'yeni'; // 0-7 gün
        } elseif ($age <= 30) {
            return 'taze'; // 8-30 gün
        } elseif ($age <= 90) {
            return 'yorgun'; // 31-90 gün
        } else {
            return 'cok_yorgun'; // 90+ gün
        }
    }

    /**
     * Scope: Yorgun ilanlar (30+ gün)
     */
    public function scopeTired($query)
    {
        return $query->whereNotNull('listing_date')
            ->where('listing_date', '<=', now()->subDays(30));
    }

    /**
     * Scope: Yeni ilanlar (7 gün içinde)
     */
    public function scopeNew($query)
    {
        return $query->whereNotNull('listing_date')
            ->where('listing_date', '>=', now()->subDays(7));
    }

    /**
     * Scope: Belirli yaş aralığındaki ilanlar
     */
    public function scopeAgeBetween($query, int $minDays, int $maxDays)
    {
        return $query->whereNotNull('listing_date')
            ->where('listing_date', '>=', now()->subDays($maxDays))
            ->where('listing_date', '<=', now()->subDays($minDays));
    }
}
