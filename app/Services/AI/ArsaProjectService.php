<?php

namespace App\Services\AI;

use App\Services\Logging\LogService;

/**
 * ArsaProjectService
 *
 * Context7 Standardı: C7-ARSA-PROJECT-ANALYSIS-2025-12-03
 *
 * Amaç:
 *  - TKGM verilerinden (alan_m2, kaks) yola çıkarak;
 *    maksimum inşaat alanını, olası konut sayısını ve
 *    tahmini satış gelirini hesaplamak.
 *
 * Not:
 *  - Bölge ortalama fiyatı şu an için mock olarak kullanılıyor
 *    (1.500.000 TRY / konut). İleride Context7 uyumlu
 *    bir piyasa analiz servisi ile entegre edilebilir.
 */
class ArsaProjectService
{
    /**
     * Varsayılan bölge ortalama satış fiyatı (TRY / konut)
     */
    public const DEFAULT_AVERAGE_UNIT_PRICE = 1_500_000;

    /**
     * Arsa için proje kârlılık analizi
     *
     * @param  array   $tkgmData   TKGM'den gelen normalize veri
     *                             Beklenen key'ler:
     *                             - alan_m2 (float|int)
     *                             - kaks (float|int)
     * @param  string  $projeTipi  'villa', 'daire' veya 'otomatik'
     * @return array{
     *     success: bool,
     *     max_insaat_alani: float|null,
     *     max_konut_sayisi: int|null,
     *     tahmini_satis_fiyati: float|null,
     *     bolge_ortalama_fiyat: float,
     *     onerilen_proje_tipi: string|null,
     *     proje_tip_input: string,
     *     assumptions: array,
     *     warnings: array
     * }
     */
    public function calculateProfitPotential(array $tkgmData, string $projeTipi): array
    {
        $warnings = [];

        $alan = isset($tkgmData['alan_m2']) ? (float) $tkgmData['alan_m2'] : null;
        $kaks = isset($tkgmData['kaks']) ? (float) $tkgmData['kaks'] : null;

        if (! $alan || ! $kaks || $alan <= 0 || $kaks <= 0) {
            $warnings[] = 'TKGM verilerinde alan_m2 veya kaks eksik ya da geçersiz. Kârlılık analizi yapılamadı.';

            LogService::warning('ArsaProjectService: Eksik TKGM verisi', [
                'alan_m2' => $tkgmData['alan_m2'] ?? null,
                'kaks' => $tkgmData['kaks'] ?? null,
            ], LogService::CHANNEL_AI);

            return [
                'success' => false,
                'max_insaat_alani' => null,
                'max_konut_sayisi' => null,
                'tahmini_satis_fiyati' => null,
                'bolge_ortalama_fiyat' => self::DEFAULT_AVERAGE_UNIT_PRICE,
                'onerilen_proje_tipi' => null,
                'proje_tip_input' => $projeTipi,
                'assumptions' => [
                    'ortalama_konut_m2' => 200,
                    'bolge_ortalama_fiyat' => self::DEFAULT_AVERAGE_UNIT_PRICE,
                ],
                'warnings' => $warnings,
            ];
        }

        // 1) Maksimum inşaat alanı
        $maxInsaatAlani = $alan * $kaks; // m²

        // 2) Maksimum konut sayısı (ortalama 200 m² konut varsayımı)
        $ortalamaKonutM2 = 200;
        $maxKonutSayisi = (int) floor($maxInsaatAlani / $ortalamaKonutM2);

        if ($maxKonutSayisi <= 0) {
            $warnings[] = 'Hesaplanan maksimum konut sayısı 0 çıktı. Alan veya KAKS değeri çok düşük olabilir.';
        }

        // 3) Tahmini satış fiyatı (mock bölge ortalama fiyatı ile)
        $bolgeOrtalamaFiyat = self::DEFAULT_AVERAGE_UNIT_PRICE;
        $tahminiSatisFiyati = $maxKonutSayisi * $bolgeOrtalamaFiyat;

        // 4) Önerilen proje tipi (Villa / Daire)
        $onerilenProjeTipi = $this->decideProjectType($maxKonutSayisi, $projeTipi);

        return [
            'success' => true,
            'max_insaat_alani' => round($maxInsaatAlani, 2),
            'max_konut_sayisi' => $maxKonutSayisi,
            'tahmini_satis_fiyati' => round($tahminiSatisFiyati, 2),
            'bolge_ortalama_fiyat' => $bolgeOrtalamaFiyat,
            'onerilen_proje_tipi' => $onerilenProjeTipi,
            'proje_tip_input' => $projeTipi,
            'assumptions' => [
                'ortalama_konut_m2' => $ortalamaKonutM2,
                'bolge_ortalama_fiyat' => $bolgeOrtalamaFiyat,
            ],
            'warnings' => $warnings,
        ];
    }

    /**
     * Proje tipini belirle (Villa / Daire)
     *
     * Basit kural seti:
     * - Eğer max konut sayısı ≤ 4 → Villa Projesi
     * - Eğer max konut sayısı > 4 → Daire / Site Projesi
     * - Kullanıcı manuel proje tipi verdiyse onu baz al
     */
    protected function decideProjectType(int $maxKonutSayisi, string $projeTipi): string
    {
        $normalizedInput = strtolower(trim($projeTipi));

        if (in_array($normalizedInput, ['villa', 'daire'], true)) {
            return ucfirst($normalizedInput);
        }

        // Otomatik karar
        if ($maxKonutSayisi <= 4) {
            return 'Villa';
        }

        return 'Daire';
    }
}
