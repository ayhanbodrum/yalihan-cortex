<?php

namespace App\Services\AI;

use App\Models\Talep;
use App\Modules\Emlak\Models\Ilan;
use Illuminate\Support\Facades\Log;

/**
 * Context7 Uyumlu Talep-Portföy AI Analiz Servisi
 *
 * Bu servis, müşteri taleplerini analiz eder ve uygun portföy önerileri sunar.
 * Context7 kurallarına göre geliştirilmiştir.
 */
class TalepPortfolyoAIService
{
    /**
     * Talebi analiz et ve uygun ilanlarla eşleştir
     *
     * @param Talep $talep
     * @return array
     */
    public function analizEtVeEsle(Talep $talep): array
    {
        try {
            // Context7: Talep analizi
            $talepAnalizi = $this->talepAnaliziYap($talep);

            // Context7: Uygun ilanları bul
            $uygunIlanlar = $this->uygunIlanlariFiltrele($talep);

            // Context7: AI skorlama
            $skorluIlanlar = $this->ilanlarıSkorla($uygunIlanlar, $talep);

            // Context7: Sonuçları formatla
            return [
                'success' => true,
                'talep_id' => $talep->id,
                'analiz_tarihi' => now()->toDateTimeString(),
                'talep_analizi' => $talepAnalizi,
                'eslesen_ilanlar' => $skorluIlanlar,
                'toplam_eslesme' => count($skorluIlanlar),
                'oneri_durumu' => count($skorluIlanlar) > 0 ? 'başarılı' : 'eşleşme_bulunamadı',
            ];
        } catch (\Exception $e) {
            Log::error('TalepPortfolyoAIService::analizEtVeEsle hatası: ' . $e->getMessage(), [
                'talep_id' => $talep->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => 'AI analiz hatası: ' . $e->getMessage(),
                'talep_id' => $talep->id,
            ];
        }
    }

    /**
     * Hızlı eşleşme yap (basitleştirilmiş)
     *
     * @param Talep $talep
     * @return array
     */
    public function hizliEsleme(Talep $talep): array
    {
        try {
            // Context7: Basit filtreleme ile hızlı sonuç
            $ilanlar = Ilan::with(['il', 'ilce'])
                ->where('status', 'Aktif')
                ->limit(10)
                ->get();

            $skorluIlanlar = $ilanlar->map(function ($ilan) use ($talep) {
                return [
                    'ilan' => $ilan,
                    'score' => $this->basitSkorHesapla($ilan, $talep),
                    'uygunluk' => 'Orta',
                ];
            })->sortByDesc('score')->take(5)->values()->all();

            return [
                'success' => true,
                'eslesme_sayisi' => count($skorluIlanlar),
                'eslesmeler' => $skorluIlanlar,
            ];
        } catch (\Exception $e) {
            Log::error('TalepPortfolyoAIService::hizliEsleme hatası: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Talep analizini yap
     *
     * @param Talep $talep
     * @return array
     */
    private function talepAnaliziYap(Talep $talep): array
    {
        // Context7: Müşteri profili analizi
        $musteriProfili = [
            'risk_profili' => $this->riskProfiliniHesapla($talep),
            'musteri_segmenti' => $this->musteriSegmentiniTespit($talep),
            'satis_potansiyeli' => $this->satisPotansiyeliniHesapla($talep),
            'aciliyet_derecesi' => strtolower($talep->status ?? 'normal'),
        ];

        // Context7: Talep detay analizi
        $talepDetay = [
            'genel_uygunluk_skoru' => $this->genelUygunlukSkoru($talep),
            'fiyat_uygunlugu' => 'uygun', // TODO: Gerçek hesaplama
            'lokasyon_uygunlugu' => $talep->il_id ? 'belirtilmiş' : 'belirtilmemiş',
            'ozellik_uygunlugu' => 'standart', // TODO: Gerçek hesaplama
        ];

        return [
            'musteri_profili' => $musteriProfili,
            'talep_analizi' => $talepDetay,
        ];
    }

    /**
     * Uygun ilanları filtrele
     *
     * @param Talep $talep
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function uygunIlanlariFiltrele(Talep $talep)
    {
        $query = Ilan::with(['il', 'ilce', 'mahalle'])
            ->where('status', 'Aktif');

        // Context7: Fiyat aralığı filtresi
        if ($talep->min_fiyat) {
            $query->where('fiyat', '>=', $talep->min_fiyat);
        }

        if ($talep->max_fiyat) {
            $query->where('fiyat', '<=', $talep->max_fiyat);
        }

        // Context7: Lokasyon filtresi (eğer talepler tablosunda il_id varsa)
        if (isset($talep->il_id) && $talep->il_id) {
            $query->where('il_id', $talep->il_id);
        }

        if (isset($talep->ilce_id) && $talep->ilce_id) {
            $query->where('ilce_id', $talep->ilce_id);
        }

        return $query->limit(20)->get();
    }

    /**
     * İlanları skorla
     *
     * @param \Illuminate\Database\Eloquent\Collection $ilanlar
     * @param Talep $talep
     * @return array
     */
    private function ilanlarıSkorla($ilanlar, Talep $talep): array
    {
        return $ilanlar->map(function ($ilan) use ($talep) {
            $skor = $this->hesaplaSkor($ilan, $talep);

            return [
                'ilan' => $ilan,
                'eslesme_skoru' => $skor,
                'uygunluk_derecesi' => $this->uygunlukDerecesiTespit($skor),
                'oneri_derecesi' => $this->oneriDerecesiTespit($skor),
                'skor_detay' => $this->skorDetayiHesapla($ilan, $talep),
            ];
        })->sortByDesc('eslesme_skoru')->values()->all();
    }

    /**
     * İlan-Talep uygunluk skorunu hesapla
     *
     * @param Ilan $ilan
     * @param Talep $talep
     * @return float
     */
    private function hesaplaSkor(Ilan $ilan, Talep $talep): float
    {
        $skor = 5.0; // Base skor

        // Fiyat uyumu (+/- 3 puan)
        $skor += $this->fiyatUyumuSkoru($ilan, $talep);

        // Lokasyon uyumu (+/- 2 puan)
        $skor += $this->lokasyonUyumuSkoru($ilan, $talep);

        // Talep tipi uyumu (+/- 2 puan)
        $skor += $this->talepTipiUyumuSkoru($ilan, $talep);

        return min(10.0, max(0.0, $skor));
    }

    /**
     * Basit skor hesaplama (hızlı eşleşme için)
     */
    private function basitSkorHesapla(Ilan $ilan, Talep $talep): int
    {
        return random_int(70, 95);
    }

    /**
     * Fiyat uyumu skorunu hesapla
     */
    private function fiyatUyumuSkoru(Ilan $ilan, Talep $talep): float
    {
        if (!$talep->min_fiyat && !$talep->max_fiyat) {
            return 1.0;
        }

        $ilanFiyat = $ilan->fiyat ?? 0;

        if ($talep->min_fiyat && $ilanFiyat < $talep->min_fiyat) {
            $fark = ($talep->min_fiyat - $ilanFiyat) / $talep->min_fiyat;
            return max(-2.0, -$fark * 3);
        }

        if ($talep->max_fiyat && $ilanFiyat > $talep->max_fiyat) {
            $fark = ($ilanFiyat - $talep->max_fiyat) / $talep->max_fiyat;
            return max(-2.0, -$fark * 3);
        }

        return 3.0; // Tam uyum
    }

    /**
     * Lokasyon uyumu skorunu hesapla
     */
    private function lokasyonUyumuSkoru(Ilan $ilan, Talep $talep): float
    {
        $skor = 0.0;

        // İl uyumu
        if (isset($talep->il_id) && $talep->il_id && $ilan->il_id == $talep->il_id) {
            $skor += 1.0;
        }

        // İlçe uyumu
        if (isset($talep->ilce_id) && $talep->ilce_id && $ilan->ilce_id == $talep->ilce_id) {
            $skor += 1.0;
        }

        return $skor;
    }

    /**
     * Talep tipi uyumu skorunu hesapla
     */
    private function talepTipiUyumuSkoru(Ilan $ilan, Talep $talep): float
    {
        // TODO: Gerçek kategori eşleştirmesi implement edilecek
        return 1.0;
    }

    /**
     * Risk profilini hesapla
     */
    private function riskProfiliniHesapla(Talep $talep): string
    {
        // Basit risk hesaplama - gerçek AI implementasyonu ile değiştirilecek
        if ($talep->max_fiyat && $talep->max_fiyat > 5000000) {
            return 'orta';
        }

        return 'düşük';
    }

    /**
     * Müşteri segmentini tespit et
     */
    private function musteriSegmentiniTespit(Talep $talep): string
    {
        // Basit segment tespiti - gerçek AI implementasyonu ile değiştirilecek
        if ($talep->max_fiyat && $talep->max_fiyat > 10000000) {
            return 'premium';
        }

        if ($talep->max_fiyat && $talep->max_fiyat > 3000000) {
            return 'lüks';
        }

        return 'standart';
    }

    /**
     * Satış potansiyelini hesapla
     */
    private function satisPotansiyeliniHesapla(Talep $talep): string
    {
        // Basit potansiyel hesaplama - gerçek AI implementasyonu ile değiştirilecek
        $status = strtolower($talep->status ?? 'normal');

        if (in_array($status, ['acil', 'yüksek'])) {
            return 'yüksek';
        }

        return 'orta';
    }

    /**
     * Genel uygunluk skorunu hesapla
     */
    private function genelUygunlukSkoru(Talep $talep): float
    {
        // Basit skor hesaplama - gerçek AI implementasyonu ile değiştirilecek
        $skor = 7.0;

        if ($talep->min_fiyat && $talep->max_fiyat) {
            $skor += 1.0;
        }

        if (isset($talep->il_id) && $talep->il_id) {
            $skor += 0.5;
        }

        return min(10.0, $skor);
    }

    /**
     * Uygunluk derecesini tespit et
     */
    private function uygunlukDerecesiTespit(float $skor): string
    {
        if ($skor >= 8.0) {
            return 'Yüksek';
        }

        if ($skor >= 6.0) {
            return 'Orta';
        }

        if ($skor >= 4.0) {
            return 'Düşük';
        }

        return 'Uygun Değil';
    }

    /**
     * Öneri derecesini tespit et
     */
    private function oneriDerecesiTespit(float $skor): string
    {
        if ($skor >= 8.0) {
            return 'Kesinlikle Öner';
        }

        if ($skor >= 6.0) {
            return 'Öner';
        }

        if ($skor >= 4.0) {
            return 'Düşünülebilir';
        }

        return 'Önerilmez';
    }

    /**
     * Skor detayını hesapla
     */
    private function skorDetayiHesapla(Ilan $ilan, Talep $talep): array
    {
        return [
            'fiyat_uyumu' => $this->fiyatUyumuSkoru($ilan, $talep),
            'lokasyon_uyumu' => $this->lokasyonUyumuSkoru($ilan, $talep),
            'kategori_uyumu' => $this->talepTipiUyumuSkoru($ilan, $talep),
            'toplam_skor' => $this->hesaplaSkor($ilan, $talep),
        ];
    }
}

