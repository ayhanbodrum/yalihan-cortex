<?php

namespace App\Services\AI;

use App\Models\Ilan;
use App\Models\Kisi;

/**
 * AI İlan Geçmişi Analiz Servisi
 *
 * Context7 Standardı: C7-AI-ILAN-GECMIS-2025-10-11
 * Context7 Kural #69: AI Geçmiş Analizi
 *
 * Kişinin önceki ilanlarından öğrenerek yeni ilanlar için öneriler üretir
 */
class IlanGecmisAIService
{
    /**
     * Kişinin ilan geçmişini analiz et
     */
    public function analyzeKisiHistory($kisiId)
    {
        $kisi = Kisi::find($kisiId);

        if (! $kisi) {
            return [
                'success' => false,
                'message' => 'Kişi bulunamadı',
            ];
        }

        $oncekiIlanlar = Ilan::where('ilan_sahibi_id', $kisiId)
            ->with(['anaKategori', 'altKategori', 'il', 'ilce', 'fotograflar'])
            ->latest()
            ->limit(20)
            ->get();

        if ($oncekiIlanlar->isEmpty()) {
            return [
                'success' => true,
                'has_history' => false,
                'message' => 'Bu kişinin önceki ilanı yok. İlk ilan oluşturuluyor.',
                'oneriler' => $this->getDefaultSuggestions(),
            ];
        }

        return [
            'success' => true,
            'has_history' => true,
            'kisi' => [
                'id' => $kisi->id,
                'tam_ad' => $kisi->tam_ad,
                'crm_score' => $kisi->crm_score ?? 0,
            ],
            'total_ilanlar' => $oncekiIlanlar->count(),
            'baslik_analizi' => $this->analyzeBasliklar($oncekiIlanlar),
            'aciklama_analizi' => $this->analyzeAciklamalar($oncekiIlanlar),
            'fiyat_trendi' => $this->analyzeFiyatlar($oncekiIlanlar),
            'kategori_tercihi' => $this->analyzeKategoriler($oncekiIlanlar),
            'lokasyon_dagilimi' => $this->analyzeLokasyonlar($oncekiIlanlar),
            'fotograf_analizi' => $this->analyzeFotograflar($oncekiIlanlar),
            'basari_metrikleri' => $this->calculateSuccessMetrics($oncekiIlanlar),
            'oneriler' => $this->generateSuggestions($oncekiIlanlar),
        ];
    }

    /**
     * Başlık kalitesi analizi
     */
    protected function analyzeBasliklar($ilanlar)
    {
        $basliklar = $ilanlar->pluck('baslik')->filter();

        if ($basliklar->isEmpty()) {
            return ['message' => 'Başlık verisi yok'];
        }

        $uzunluklar = $basliklar->map(fn ($b) => mb_strlen($b));

        // En başarılı başlığı bul (görüntülenme bazlı)
        $enBasarili = $ilanlar->sortByDesc('goruntulenme_sayisi')->first();

        return [
            'toplam_baslik' => $basliklar->count(),
            'ortalama_uzunluk' => round($uzunluklar->avg(), 0),
            'min_uzunluk' => $uzunluklar->min(),
            'max_uzunluk' => $uzunluklar->max(),
            'onerilen_uzunluk' => 60, // SEO optimal
            'en_basarili' => $enBasarili ? [
                'baslik' => $enBasarili->baslik,
                'uzunluk' => mb_strlen($enBasarili->baslik),
                'goruntulenme' => $enBasarili->goruntulenme_sayisi ?? 0,
            ] : null,
            'kalite_skoru' => $this->calculateBaslikKalite($basliklar),
            'seo_analiz' => $this->analyzeSEOKeywords($basliklar),
        ];
    }

    /**
     * Açıklama analizi
     */
    protected function analyzeAciklamalar($ilanlar)
    {
        $aciklamalar = $ilanlar->pluck('aciklama')->filter();

        if ($aciklamalar->isEmpty()) {
            return ['message' => 'Açıklama verisi yok'];
        }

        $kelimeSayilari = $aciklamalar->map(fn ($a) => str_word_count($a));

        return [
            'toplam_aciklama' => $aciklamalar->count(),
            'ortalama_kelime' => round($kelimeSayilari->avg(), 0),
            'min_kelime' => $kelimeSayilari->min(),
            'max_kelime' => $kelimeSayilari->max(),
            'onerilen_kelime' => 200, // Optimal: 200-250 kelime
            'detay_seviyesi' => $this->calculateDetailLevel($kelimeSayilari->avg()),
            'paragraf_analizi' => $this->analyzeParagraphs($aciklamalar),
        ];
    }

    /**
     * Fiyat trendi analizi
     */
    protected function analyzeFiyatlar($ilanlar)
    {
        $fiyatlar = $ilanlar->pluck('fiyat')->filter();

        if ($fiyatlar->isEmpty()) {
            return ['message' => 'Fiyat verisi yok'];
        }

        $ortalama = $fiyatlar->avg();
        $min = $fiyatlar->min();
        $max = $fiyatlar->max();

        // Para birimi dağılımı
        $paraBirimleri = $ilanlar->pluck('para_birimi')->filter()->countBy();

        return [
            'ortalama_fiyat' => round($ortalama, 0),
            'min_fiyat' => $min,
            'max_fiyat' => $max,
            'fiyat_araliği' => $max - $min,
            'para_birimi_dagilimi' => $paraBirimleri->toArray(),
            'en_cok_kullanilan' => $paraBirimleri->sortDesc()->keys()->first() ?? 'TRY',
            'trend' => $this->calculatePriceTrend($ilanlar),
            'piyasa_pozisyon' => $this->compareToPazarOrtalamasi($ortalama),
        ];
    }

    /**
     * Kategori tercihleri analizi
     */
    protected function analyzeKategoriler($ilanlar)
    {
        $kategoriler = $ilanlar->groupBy('alt_kategori_id');

        $tercihler = [];
        foreach ($kategoriler as $kategoriId => $ilanlarGroup) {
            $kategori = $ilanlarGroup->first()->altKategori;

            $tercihler[] = [
                'kategori_id' => $kategoriId,
                'kategori_adi' => $kategori->name ?? 'Bilinmiyor',
                'ilan_sayisi' => $ilanlarGroup->count(),
                'ortalama_fiyat' => round($ilanlarGroup->avg('fiyat'), 0),
                'basari_orani' => $this->calculateSuccessRate($ilanlarGroup),
                'yuzde' => round(($ilanlarGroup->count() / $ilanlar->count()) * 100, 1),
            ];
        }

        // En çok kullanılan kategoriye göre sırala
        usort($tercihler, fn ($a, $b) => $b['ilan_sayisi'] <=> $a['ilan_sayisi']);

        return [
            'tercihler' => $tercihler,
            'en_cok_kullanilan' => $tercihler[0] ?? null,
            'kategori_cesitliligi' => count($tercihler),
        ];
    }

    /**
     * Lokasyon dağılımı analizi
     */
    protected function analyzeLokasyonlar($ilanlar)
    {
        $iller = $ilanlar->groupBy('il_id');
        $ilceler = $ilanlar->groupBy('ilce_id');

        $ilDagilimi = [];
        foreach ($iller as $ilId => $ilanlarGroup) {
            $il = $ilanlarGroup->first()->il;
            $ilDagilimi[] = [
                'il_id' => $ilId,
                'il_adi' => $il->il_adi ?? 'Bilinmiyor',
                'ilan_sayisi' => $ilanlarGroup->count(),
                'yuzde' => round(($ilanlarGroup->count() / $ilanlar->count()) * 100, 1),
            ];
        }

        usort($ilDagilimi, fn ($a, $b) => $b['ilan_sayisi'] <=> $a['ilan_sayisi']);

        return [
            'il_dagilimi' => $ilDagilimi,
            'en_cok_kullanilan_il' => $ilDagilimi[0] ?? null,
            'il_cesitliligi' => count($ilDagilimi),
        ];
    }

    /**
     * Fotoğraf analizi
     */
    protected function analyzeFotograflar($ilanlar)
    {
        $fotoSayilari = $ilanlar->map(fn ($i) => $i->fotograflar->count());

        return [
            'ortalama_foto' => round($fotoSayilari->avg(), 0),
            'min_foto' => $fotoSayilari->min(),
            'max_foto' => $fotoSayilari->max(),
            'onerilen_foto' => 8, // Optimal: 8-12 fotoğraf
            'kapak_foto_kullanimi' => $ilanlar->filter(fn ($i) => $i->fotograflar->where('kapak_fotografi', true)->count() > 0)->count(),
        ];
    }

    /**
     * Başarı metrikleri hesaplama
     */
    protected function calculateSuccessMetrics($ilanlar)
    {
        $aktif = $ilanlar->where('status', 'Aktif')->count();
        $satilan = $ilanlar->where('status', 'Satıldı')->count();
        $kiralanan = $ilanlar->where('status', 'Kiralandı')->count();

        $toplamGoruntulenme = $ilanlar->sum('goruntulenme_sayisi') ?? 0;
        $toplamFavori = $ilanlar->sum('favori_sayisi') ?? 0;

        return [
            'aktif_oran' => $ilanlar->count() > 0 ? round(($aktif / $ilanlar->count()) * 100, 1) : 0,
            'satis_oran' => $ilanlar->count() > 0 ? round(($satilan / $ilanlar->count()) * 100, 1) : 0,
            'kiralama_oran' => $ilanlar->count() > 0 ? round(($kiralanan / $ilanlar->count()) * 100, 1) : 0,
            'ortalama_goruntulenme' => $ilanlar->count() > 0 ? round($toplamGoruntulenme / $ilanlar->count(), 0) : 0,
            'ortalama_favori' => $ilanlar->count() > 0 ? round($toplamFavori / $ilanlar->count(), 0) : 0,
            'basari_skoru' => $this->calculateOverallSuccessScore($ilanlar),
        ];
    }

    /**
     * Yeni ilan için öneriler üret
     */
    public function generateSuggestions($oncekiIlanlar)
    {
        $baslikAnaliz = $this->analyzeBasliklar($oncekiIlanlar);
        $aciklamaAnaliz = $this->analyzeAciklamalar($oncekiIlanlar);
        $fiyatAnaliz = $this->analyzeFiyatlar($oncekiIlanlar);
        $fotografAnaliz = $this->analyzeFotograflar($oncekiIlanlar);
        $kategoriAnaliz = $this->analyzeKategoriler($oncekiIlanlar);
        $lokasyonAnaliz = $this->analyzeLokasyonlar($oncekiIlanlar);

        $oneriler = [];

        // Başlık önerisi
        if (isset($baslikAnaliz['ortalama_uzunluk'])) {
            $uzunluk = $baslikAnaliz['ortalama_uzunluk'];
            if ($uzunluk < 50) {
                $oneriler[] = "💡 Başlık uzunluğunu artırın. Önceki ilanlarınızda ortalama {$uzunluk} karakter kullanmışsınız. SEO için 60-80 karakter önerilir.";
            } elseif ($uzunluk > 90) {
                $oneriler[] = "💡 Başlık çok uzun. Önceki ilanlarınızda ortalama {$uzunluk} karakter. 60-80 arası daha iyi.";
            } else {
                $oneriler[] = "✅ Başlık uzunluğunuz ideal ({$uzunluk} karakter). Aynı şekilde devam edin.";
            }
        }

        // Açıklama önerisi
        if (isset($aciklamaAnaliz['ortalama_kelime'])) {
            $kelime = $aciklamaAnaliz['ortalama_kelime'];
            if ($kelime < 150) {
                $oneriler[] = "💡 Açıklama detayını artırın. Önceki ilanlarınızda ortalama {$kelime} kelime. 200-250 kelime önerilir.";
            } else {
                $oneriler[] = "✅ Açıklama uzunluğunuz yeterli ({$kelime} kelime).";
            }
        }

        // Fiyat önerisi
        if (isset($fiyatAnaliz['ortalama_fiyat'])) {
            $ortalama = number_format($fiyatAnaliz['ortalama_fiyat'], 0, ',', '.');
            $paraBirimi = $fiyatAnaliz['en_cok_kullanilan'] ?? 'TRY';
            $oneriler[] = "💰 Önceki fiyat ortalamanız: {$ortalama} {$paraBirimi}. Bölge ve kategori uygunluğunu kontrol edin.";
        }

        // Fotoğraf önerisi
        if (isset($fotografAnaliz['ortalama_foto'])) {
            $foto = $fotografAnaliz['ortalama_foto'];
            if ($foto < 6) {
                $oneriler[] = "📸 Daha fazla fotoğraf ekleyin. Önceki ilanlarınızda ortalama {$foto} fotoğraf. 8-12 fotoğraf önerilir.";
            } else {
                $oneriler[] = "✅ Fotoğraf sayınız yeterli ({$foto} fotoğraf).";
            }
        }

        // Kategori önerisi
        if (isset($kategoriAnaliz['en_cok_kullanilan'])) {
            $kategori = $kategoriAnaliz['en_cok_kullanilan'];
            $oneriler[] = "🏠 En çok kullandığınız kategori: {$kategori['kategori_adi']} ({$kategori['ilan_sayisi']} ilan, {$kategori['yuzde']}%)";
        }

        // Lokasyon önerisi
        if (isset($lokasyonAnaliz['en_cok_kullanilan_il'])) {
            $il = $lokasyonAnaliz['en_cok_kullanilan_il'];
            $oneriler[] = "📍 En çok kullandığınız lokasyon: {$il['il_adi']} ({$il['ilan_sayisi']} ilan)";
        }

        return $oneriler;
    }

    /**
     * Başlık kalite skoru hesaplama
     */
    protected function calculateBaslikKalite($basliklar)
    {
        $toplamSkor = 0;
        $count = 0;

        foreach ($basliklar as $baslik) {
            $skor = 0;
            $uzunluk = mb_strlen($baslik);

            // Uzunluk skoru (0-40)
            if ($uzunluk >= 60 && $uzunluk <= 80) {
                $skor += 40;
            } elseif ($uzunluk >= 50 && $uzunluk < 60) {
                $skor += 30;
            } elseif ($uzunluk > 80 && $uzunluk <= 90) {
                $skor += 30;
            } else {
                $skor += 20;
            }

            // SEO keyword varlığı (0-30)
            $keywords = ['satılık', 'kiralık', 'villa', 'daire', 'arsa', 'deniz', 'merkez', 'lüks'];
            $foundKeywords = 0;
            foreach ($keywords as $keyword) {
                if (stripos($baslik, $keyword) !== false) {
                    $foundKeywords++;
                }
            }
            $skor += min($foundKeywords * 10, 30);

            // Lokasyon varlığı (0-30)
            if (preg_match('/\b(Bodrum|Yalıkavak|Gümbet|İstanbul|Ankara|Muğla)\b/i', $baslik)) {
                $skor += 30;
            }

            $toplamSkor += $skor;
            $count++;
        }

        return $count > 0 ? round($toplamSkor / $count, 0) : 0;
    }

    /**
     * SEO keyword analizi
     */
    protected function analyzeSEOKeywords($basliklar)
    {
        $keywords = ['satılık', 'kiralık', 'villa', 'daire', 'arsa', 'deniz', 'manzara', 'lüks', 'merkez'];
        $kullanim = [];

        foreach ($keywords as $keyword) {
            $count = 0;
            foreach ($basliklar as $baslik) {
                if (stripos($baslik, $keyword) !== false) {
                    $count++;
                }
            }
            if ($count > 0) {
                $kullanim[$keyword] = [
                    'count' => $count,
                    'percentage' => round(($count / $basliklar->count()) * 100, 1),
                ];
            }
        }

        return $kullanim;
    }

    /**
     * Fiyat trendi hesaplama
     */
    protected function calculatePriceTrend($ilanlar)
    {
        if ($ilanlar->count() < 3) {
            return 'Yetersiz veri';
        }

        // Son 3 ilan ve ilk 3 ilanın fiyat ortalaması
        $sonIlanlar = $ilanlar->take(3);
        $ilkIlanlar = $ilanlar->reverse()->take(3);

        $sonOrtalama = $sonIlanlar->avg('fiyat');
        $ilkOrtalama = $ilkIlanlar->avg('fiyat');

        if ($sonOrtalama > $ilkOrtalama * 1.1) {
            return 'Artış eğilimi (+'.round((($sonOrtalama - $ilkOrtalama) / $ilkOrtalama) * 100, 1).'%)';
        } elseif ($sonOrtalama < $ilkOrtalama * 0.9) {
            return 'Azalış eğilimi (-'.round((($ilkOrtalama - $sonOrtalama) / $ilkOrtalama) * 100, 1).'%)';
        } else {
            return 'Stabil';
        }
    }

    /**
     * Pazar ortalaması ile karşılaştır
     */
    protected function compareToPazarOrtalamasi($fiyat)
    {
        // Basit implementasyon - geliştirilecek
        return 'Analiz için daha fazla veri gerekli';
    }

    /**
     * Başarı oranı hesaplama
     */
    protected function calculateSuccessRate($ilanlar)
    {
        $satilan = $ilanlar->whereIn('status', ['Satıldı', 'Kiralandı'])->count();

        return $ilanlar->count() > 0 ? round(($satilan / $ilanlar->count()) * 100, 1) : 0;
    }

    /**
     * Genel başarı skoru
     */
    protected function calculateOverallSuccessScore($ilanlar)
    {
        $aktif = $ilanlar->where('status', 'Aktif')->count();
        $satilan = $ilanlar->whereIn('status', ['Satıldı', 'Kiralandı'])->count();
        $goruntulenme = $ilanlar->avg('goruntulenme_sayisi') ?? 0;

        $skor = 0;

        // Satış/kiralama oranı (0-40)
        if ($ilanlar->count() > 0) {
            $skor += round(($satilan / $ilanlar->count()) * 40, 0);
        }

        // Aktif oran (0-30)
        if ($ilanlar->count() > 0) {
            $skor += round(($aktif / $ilanlar->count()) * 30, 0);
        }

        // Görüntülenme (0-30)
        if ($goruntulenme > 100) {
            $skor += 30;
        } elseif ($goruntulenme > 50) {
            $skor += 20;
        } elseif ($goruntulenme > 20) {
            $skor += 10;
        }

        return min($skor, 100);
    }

    /**
     * Detay seviyesi değerlendirme
     */
    protected function calculateDetailLevel($avgWords)
    {
        if ($avgWords < 100) {
            return 'Düşük - Daha detaylı açıklama önerilir';
        } elseif ($avgWords < 200) {
            return 'Orta - Kabul edilebilir seviye';
        } elseif ($avgWords < 300) {
            return 'İyi - Yeterli detay var';
        } else {
            return 'Çok Yüksek - Daha özlü olabilir';
        }
    }

    /**
     * Paragraf analizi
     */
    protected function analyzeParagraphs($aciklamalar)
    {
        $paragrafSayilari = $aciklamalar->map(function ($aciklama) {
            return count(array_filter(explode("\n\n", $aciklama)));
        });

        return [
            'ortalama_paragraf' => round($paragrafSayilari->avg(), 0),
            'onerilen_paragraf' => 3,
        ];
    }

    /**
     * Varsayılan öneriler (geçmiş yok ise)
     */
    protected function getDefaultSuggestions()
    {
        return [
            '💡 İlk ilanınız! Başlık 60-80 karakter arasında olmalı.',
            '💡 Açıklama 200-250 kelime, 3 paragraf olarak yazın.',
            '📸 En az 8-12 fotoğraf ekleyin.',
            '💰 Fiyatı piyasa araştırması yaparak belirleyin.',
            '📍 Lokasyon bilgilerini eksiksiz doldurun.',
        ];
    }
}
