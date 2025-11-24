<?php

namespace App\Services\Market;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Market Data Service
 *
 * Context7 Standardı: C7-MARKET-DATA-SERVICE-2025-11-20
 *
 * Cross-database query ile yalihan_market DB'den veri toplar.
 */
class MarketDataService
{
    /**
     * Market DB adı
     */
    protected string $marketDb = 'yalihan_market';

    /**
     * Cache TTL (saniye)
     */
    protected int $cacheTTL = 3600;

    /**
     * Arsa için market verilerini topla
     */
    public function collectLandPlotData(int $ilId, ?int $ilceId = null, ?int $mahalleId = null): array
    {
        $cacheKey = "market_data_land_{$ilId}_{$ilceId}_{$mahalleId}";

        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($ilId, $ilceId, $mahalleId) {
            try {
                $data = [
                    'listings' => $this->getMarketListings($ilId, $ilceId, $mahalleId),
                    'price_history' => $this->getPriceHistory($ilId, $ilceId, $mahalleId),
                    'price_stats' => $this->getPriceStats($ilId, $ilceId, $mahalleId),
                ];

                return $data;
            } catch (\Exception $e) {
                Log::error('Market data toplama hatası', [
                    'error' => $e->getMessage(),
                    'il_id' => $ilId,
                    'ilce_id' => $ilceId,
                    'mahalle_id' => $mahalleId,
                ]);

                return [
                    'listings' => [],
                    'price_history' => [],
                    'price_stats' => [],
                ];
            }
        });
    }

    /**
     * Market listings topla
     */
    protected function getMarketListings(int $ilId, ?int $ilceId = null, ?int $mahalleId = null): array
    {
        $query = DB::connection('market')->table('market_listings')
            ->where('il_id', $ilId)
            ->where('status', 'active')
            ->select('id', 'portal_name', 'fiyat', 'm2', 'il_id', 'ilce_id', 'mahalle_id', 'created_at');

        if ($ilceId) {
            $query->where('ilce_id', $ilceId);
        }

        if ($mahalleId) {
            $query->where('mahalle_id', $mahalleId);
        }

        return $query->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->toArray();
    }

    /**
     * Fiyat geçmişi topla
     */
    protected function getPriceHistory(int $ilId, ?int $ilceId = null, ?int $mahalleId = null): array
    {
        $query = DB::connection('market')->table('market_price_history')
            ->where('il_id', $ilId)
            ->select('id', 'fiyat', 'm2', 'tarih', 'il_id', 'ilce_id', 'mahalle_id');

        if ($ilceId) {
            $query->where('ilce_id', $ilceId);
        }

        if ($mahalleId) {
            $query->where('mahalle_id', $mahalleId);
        }

        return $query->orderBy('tarih', 'desc')
            ->limit(100)
            ->get()
            ->toArray();
    }

    /**
     * Fiyat istatistikleri topla
     */
    protected function getPriceStats(int $ilId, ?int $ilceId = null, ?int $mahalleId = null): array
    {
        $query = DB::connection('market')->table('market_price_stats')
            ->where('il_id', $ilId)
            ->select('*');

        if ($ilceId) {
            $query->where('ilce_id', $ilceId);
        }

        if ($mahalleId) {
            $query->where('mahalle_id', $mahalleId);
        }

        return $query->first();
    }
}

