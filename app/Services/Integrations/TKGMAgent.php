<?php

namespace App\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TKGMAgent
{
    public function getParcelData(float $lat, float $lon): ?array
    {
        // TKGM Endpoint
        $url = "https://cbsapi.tkgm.gov.tr/megsiswebapi.v3.1/api/parsel/{$lat}/{$lon}/";

        // Taklitçi Headerlar
        $headers = [
            'Accept'          => 'application/json, text/javascript, */*; q=0.01',
            'Accept-Language' => 'tr',
            'Connection'      => 'keep-alive',
            'Origin'          => 'https://parselsorgu.tkgm.gov.tr',
            'Referer'         => 'https://parselsorgu.tkgm.gov.tr/',
            'Sec-Fetch-Dest'  => 'empty',
            'Sec-Fetch-Mode'  => 'cors',
            'Sec-Fetch-Site'  => 'same-site',
            'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
        ];

        try {
            $response = Http::withOptions([
                'verify' => false,
                'timeout' => 10,
            ])->withHeaders($headers)->get($url);

            if ($response->successful()) {
                $jsonData = $response->json();
                if ($jsonData) {
                    return $this->normalizeData($jsonData);
                }
                Log::warning('TKGM API: Boş JSON yanıtı', ['url' => $url]);
                return null;
            }

            Log::warning('TKGM API Hatası', [
                'status' => $response->status(),
                'url' => $url,
                'body' => $response->body(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('TKGM Bağlantı Hatası', [
                'message' => $e->getMessage(),
                'url' => $url,
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    private function normalizeData($data): array
    {
        if (!is_array($data)) {
            Log::warning('TKGM API: Geçersiz veri formatı', ['data' => $data]);
            return [];
        }

        $props = $data['properties'] ?? $data;

        if (!is_array($props)) {
            Log::warning('TKGM API: Properties bulunamadı', ['data' => $data]);
            return [];
        }

        return [
            'il'        => $props['ilAd'] ?? null,
            'ilce'      => $props['ilceAd'] ?? null,
            'mahalle'   => $props['mahalleAd'] ?? null,
            'ada'       => $props['adaNo'] ?? null,
            'parsel'    => $props['parselNo'] ?? null,
            'nitelik'   => $props['nitelik'] ?? null,
            'alan_m2'   => isset($props['alan']) ? (float) $props['alan'] : null,
            'mevkii'    => $props['mevkii'] ?? null,
            'pafta'     => $props['pafta'] ?? null,
        ];
    }
}
