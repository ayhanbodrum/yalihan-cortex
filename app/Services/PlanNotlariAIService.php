<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class PlanNotlariAIService
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * TKGM parsel verilerinden AI destekli plan notları analizi
     */
    public function planNotlariAnalizi($parselData, $teknikBilgiler = [])
    {
        try {
            $prompt = $this->buildPlanNotlariPrompt($parselData, $teknikBilgiler);

            $response = $this->aiService->generate($prompt, [
                'max_tokens' => 2000,
                'temperature' => 0.7,
            ]);

            return $this->parsePlanNotlariResponse($response);

        } catch (\Exception $e) {
            Log::error('Plan notları AI analizi hatası', [
                'error' => $e->getMessage(),
                'parsel' => $parselData,
            ]);

            return $this->fallbackPlanNotlari($parselData);
        }
    }

    /**
     * AI için plan notları prompt'u oluştur
     */
    private function buildPlanNotlariPrompt($parselData, $teknikBilgiler)
    {
        $prompt = "TKGM Parsel Analizi ve Plan Notları\n\n";

        // Parsel temel bilgileri
        $prompt .= "📋 PARSEL BİLGİLERİ:\n";
        $prompt .= '- İl: '.($parselData['il'] ?? 'Belirtilmemiş')."\n";
        $prompt .= '- İlçe: '.($parselData['ilce'] ?? 'Belirtilmemiş')."\n";
        $prompt .= '- Mahalle: '.($parselData['mahalle'] ?? 'Belirtilmemiş')."\n";
        $prompt .= '- Ada: '.($parselData['ada'] ?? 'Belirtilmemiş')."\n";
        $prompt .= '- Parsel: '.($parselData['parsel'] ?? 'Belirtilmemiş')."\n";
        $prompt .= '- Tapu Alanı: '.($parselData['tapu_alani'] ?? 'Belirtilmemiş')." m²\n";
        $prompt .= '- Nitelik: '.($parselData['nitelik'] ?? 'Belirtilmemiş')."\n";
        $prompt .= '- Mevkii: '.($parselData['mevkii'] ?? 'Belirtilmemiş')."\n\n";

        // İmar bilgileri
        if (isset($parselData['imar_durumu'])) {
            $prompt .= "🏗️ İMAR BİLGİLERİ:\n";
            $prompt .= '- TAKS: %'.($parselData['imar_durumu']['taks'] ?? 'Belirtilmemiş')."\n";
            $prompt .= '- KAKS: '.($parselData['imar_durumu']['kaks'] ?? 'Belirtilmemiş')."\n";
            $prompt .= '- Taban Alanı: '.($parselData['imar_durumu']['taban_alani'] ?? 'Belirtilmemiş')." m²\n";
            $prompt .= '- İnşaat Alanı: '.($parselData['imar_durumu']['insaat_alani'] ?? 'Belirtilmemiş')." m²\n\n";
        }

        // Teknik bilgiler
        if (! empty($teknikBilgiler)) {
            $prompt .= "⚙️ TEKNİK BİLGİLER:\n";
            foreach ($teknikBilgiler as $key => $value) {
                $prompt .= '- '.ucfirst(str_replace('_', ' ', $key)).': '.$value."\n";
            }
            $prompt .= "\n";
        }

        $prompt .= "📝 İSTENEN ANALİZ:\n";
        $prompt .= "1. Bu parsel için detaylı plan notları yazın\n";
        $prompt .= "2. İnşaat potansiyelini değerlendirin\n";
        $prompt .= "3. Yatırım önerileri sunun\n";
        $prompt .= "4. Olası riskleri belirtin\n";
        $prompt .= "5. Geliştirme stratejileri öner\n";
        $prompt .= "6. Finansal projeksiyonlar yapın (mümkünse)\n\n";

        $prompt .= "Lütfen yanıtınızı JSON formatında şu şekilde yapılandırın:\n";
        $prompt .= "{\n";
        $prompt .= "  \"plan_notlari\": \"Detaylı plan açıklaması\",\n";
        $prompt .= "  \"insaat_potansiyeli\": \"İnşaat potansiyeli analizi\",\n";
        $prompt .= "  \"yatirim_onerileri\": [\"Öneri 1\", \"Öneri 2\"],\n";
        $prompt .= "  \"riskler\": [\"Risk 1\", \"Risk 2\"],\n";
        $prompt .= "  \"gelistirme_stratejileri\": [\"Strateji 1\", \"Strateji 2\"],\n";
        $prompt .= "  \"finansal_projeksiyon\": {\n";
        $prompt .= "    \"tahmini_inşaat_maliyet\": \"Tahmin\",\n";
        $prompt .= "    \"beklenen_deger\": \"Tahmin\",\n";
        $prompt .= "    \"roi_tahmini\": \"Tahmin\"\n";
        $prompt .= "  },\n";
        $prompt .= "  \"sonuc_skoru\": 85\n";
        $prompt .= '}';

        return $prompt;
    }

    /**
     * AI yanıtını parse et
     */
    private function parsePlanNotlariResponse($response)
    {
        if (empty($response)) {
            throw new \Exception('AI yanıtı boş');
        }

        // AIService artık array formatında dönüyor
        if (is_array($response) && isset($response['data'])) {
            $content = $response['data'];
        } else {
            $content = $response; // Fallback için
        }

        // JSON kısmını bul ve parse et
        if (preg_match('/\{.*\}/s', $content, $matches)) {
            $jsonData = json_decode($matches[0], true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return [
                    'success' => true,
                    'ai_analiz' => $jsonData,
                    'raw_response' => $content,
                ];
            }
        }

        // JSON parse edilememediyse metin olarak döndür
        return [
            'success' => true,
            'ai_analiz' => [
                'plan_notlari' => $content,
                'sonuc_skoru' => 70,
            ],
            'raw_response' => $content,
        ];
    }

    /**
     * AI hatası durumunda fallback plan notları
     */
    private function fallbackPlanNotlari($parselData)
    {
        $planNotlari = "Bu parsel için temel analiz:\n\n";

        // Temel değerlendirme
        if (isset($parselData['imar_durumu'])) {
            $kaks = $parselData['imar_durumu']['kaks'] ?? 0;
            $taks = $parselData['imar_durumu']['taks'] ?? 0;

            $planNotlari .= "İmar Durumu: KAKS {$kaks}, TAKS %{$taks}\n";

            if ($kaks >= 1.0) {
                $planNotlari .= "✅ İyi inşaat potansiyeli mevcut\n";
            } else {
                $planNotlari .= "⚠️ Sınırlı inşaat potansiyeli\n";
            }
        }

        $planNotlari .= "\nDetaylı analiz için uzman görüşü alınması önerilir.";

        return [
            'success' => true,
            'ai_analiz' => [
                'plan_notlari' => $planNotlari,
                'yatirim_onerileri' => ['Uzman değerlendirmesi alın'],
                'riskler' => ['Detaylı analiz gerekli'],
                'sonuc_skoru' => 50,
            ],
            'fallback' => true,
        ];
    }

    /**
     * İlan yayınlama için optimize edilmiş plan notları
     */
    public function ilanPlanNotlari($parselData, $aiAnaliz)
    {
        $ilanNotlari = [];

        // Ana başlık
        $lokasyon = trim(($parselData['mahalle'] ?? '').', '.($parselData['ilce'] ?? '').', '.($parselData['il'] ?? ''));
        $ilanNotlari['baslik'] = "Yatırım Fırsatı - {$lokasyon} {$parselData['ada']}/{$parselData['parsel']}";

        // Kısa açıklama
        $ilanNotlari['kisa_aciklama'] = substr($aiAnaliz['plan_notlari'] ?? 'Yatırım potansiyeli yüksek arsa', 0, 200);

        // Öne çıkan özellikler
        $ilanNotlari['ozellikler'] = [
            'Alan: '.($parselData['tapu_alani'] ?? 'Belirtilmemiş').' m²',
            'KAKS: '.($parselData['imar_durumu']['kaks'] ?? 'Belirtilmemiş'),
            'TAKS: %'.($parselData['imar_durumu']['taks'] ?? 'Belirtilmemiş'),
            'İnşaat Alanı: '.($parselData['imar_durumu']['insaat_alani'] ?? 'Belirtilmemiş').' m²',
        ];

        // Yatırım puanı
        $ilanNotlari['yatirim_puani'] = $aiAnaliz['sonuc_skoru'] ?? 70;

        return $ilanNotlari;
    }
}
