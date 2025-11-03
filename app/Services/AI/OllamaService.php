<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Ollama AI Service
 *
 * Context7 Standardı: C7-OLLAMA-SERVICE-2025-10-11
 *
 * Ollama entegrasyonu için servis sınıfı
 * Model: gemma2:2b (hafif ve hızlı)
 * Endpoint: http://51.75.64.121:11434
 */
class OllamaService
{
    /**
     * Ollama API URL
     */
    protected string $apiUrl;

    /**
     * Ollama Model
     */
    protected string $model;

    /**
     * Cache süresi (saniye)
     */
    protected int $cacheTTL = 3600;

    public function __construct()
    {
        $this->apiUrl = config('ai.ollama_api_url', 'http://51.75.64.121:11434');
        $this->model = config('ai.ollama_model', 'gemma2:2b');
    }

    /**
     * İlan başlığı üret
     *
     * @param array $data
     * @return array
     */
    public function generateTitle(array $data): array
    {
        $cacheKey = 'ollama_title_' . md5(json_encode($data));

        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($data) {
            $prompt = $this->buildTitlePrompt($data);

            try {
                $response = $this->sendRequest($prompt);

                if (isset($response['response'])) {
                    return $this->parseTitleResponse($response['response']);
                }
            } catch (\Exception $e) {
                Log::error('Ollama title generation failed', ['error' => $e->getMessage()]);
            }

            return $this->getFallbackTitles($data);
        });
    }

    /**
     * İlan açıklaması üret
     *
     * @param array $data
     * @return string
     */
    public function generateDescription(array $data): string
    {
        $cacheKey = 'ollama_desc_' . md5(json_encode($data));

        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($data) {
            $prompt = $this->buildDescriptionPrompt($data);

            try {
                $response = $this->sendRequest($prompt);

                if (isset($response['response'])) {
                    return trim($response['response']);
                }
            } catch (\Exception $e) {
                Log::error('Ollama description generation failed', ['error' => $e->getMessage()]);
            }

            return $this->getFallbackDescription($data);
        });
    }

    /**
     * Lokasyon analizi yap
     *
     * @param array $locationData
     * @return array
     */
    public function analyzeLocation(array $locationData): array
    {
        $cacheKey = 'ollama_location_' . md5(json_encode($locationData));

        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($locationData) {
            $prompt = $this->buildLocationAnalysisPrompt($locationData);

            try {
                $response = $this->sendRequest($prompt);

                if (isset($response['response'])) {
                    return $this->parseLocationAnalysis($response['response']);
                }
            } catch (\Exception $e) {
                Log::error('Ollama location analysis failed', ['error' => $e->getMessage()]);
            }

            return $this->getFallbackLocationAnalysis();
        });
    }

    /**
     * Fiyat önerisi ver
     *
     * @param array $propertyData
     * @return array
     */
    public function suggestPrice(array $propertyData): array
    {
        $prompt = $this->buildPriceSuggestionPrompt($propertyData);

        try {
            $response = $this->sendRequest($prompt);

            if (isset($response['response'])) {
                return $this->parsePriceSuggestions($response['response'], $propertyData['base_price'] ?? 0);
            }
        } catch (\Exception $e) {
            Log::error('Ollama price suggestion failed', ['error' => $e->getMessage()]);
        }

        return $this->getFallbackPriceSuggestions($propertyData['base_price'] ?? 0);
    }

    /**
     * Ollama API'ye istek gönder
     *
     * @param string $prompt
     * @param int $maxTokens
     * @return array
     */
    protected function sendRequest(string $prompt, int $maxTokens = 500): array
    {
        $response = Http::timeout(30)
            ->post($this->apiUrl . '/api/generate', [
                'model' => $this->model,
                'prompt' => $prompt,
                'stream' => false,
                'options' => [
                    'temperature' => 0.7,
                    'top_p' => 0.9,
                    'num_predict' => $maxTokens,
                ]
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Ollama API request failed: ' . $response->status());
    }

    /**
     * Başlık prompt'u oluştur
     *
     * AI Eğitim Paketi Template (04-PROMPT-TEMPLATES.md)
     */
    protected function buildTitlePrompt(array $data): string
    {
        $kategori = $data['kategori'] ?? 'Gayrimenkul';
        $lokasyon = $data['lokasyon'] ?? '';
        $yayin_tipi = $data['yayin_tipi'] ?? 'Satılık';
        $fiyat = $data['fiyat'] ?? '';
        $tone = $data['tone'] ?? 'seo';

        $toneDescriptions = [
            'seo' => 'SEO optimize edilmiş, anahtar kelime yoğun, detaylı',
            'kurumsal' => 'Profesyonel, yatırım odaklı, kurumsal dil',
            'hizli_satis' => 'Dikkat çekici, acil, heyecan verici',
            'luks' => 'Prestijli, özel, ayrıcalıklı dil'
        ];

        $toneInstruction = $toneDescriptions[$tone] ?? $toneDescriptions['seo'];

        // AI Eğitim Paketi - Optimize Prompt Template
        return "Sen bir emlak uzmanısın. Aşağıdaki bilgilere göre {$toneInstruction} tonunda 3 farklı ilan başlığı oluştur.

Kategori: {$kategori}
Yayın Tipi: {$yayin_tipi}
Lokasyon: {$lokasyon}
Fiyat: {$fiyat}
Ton: {$tone}

Kurallar:
- Her başlık 60-80 karakter arası
- Lokasyon mutlaka geçmeli
- SEO uyumlu anahtar kelimeler
- Sadece başlıkları yaz, numaralama yapma
- Emoji kullanma

Başlıklar:";
    }

    /**
     * Açıklama prompt'u oluştur
     *
     * AI Eğitim Paketi Template (04-PROMPT-TEMPLATES.md)
     */
    protected function buildDescriptionPrompt(array $data): string
    {
        $kategori = $data['kategori'] ?? 'Gayrimenkul';
        $lokasyon = $data['lokasyon'] ?? '';
        $fiyat = $data['fiyat'] ?? '';
        $metrekare = $data['metrekare'] ?? '';
        $oda_sayisi = $data['oda_sayisi'] ?? '';
        $tone = $data['tone'] ?? 'seo';

        // AI Eğitim Paketi - Optimize Prompt Template
        return "Sen profesyonel bir emlak danışmanısın. Aşağıdaki özellikte profesyonel ilan açıklaması yaz.

Kategori: {$kategori}
Lokasyon: {$lokasyon}
Fiyat: {$fiyat}
Metrekare: {$metrekare} m²
Oda Sayısı: {$oda_sayisi}
Ton: {$tone}

Kurallar:
- 200-250 kelime
- 3 paragraf
- Türkçe dilbilgisi kurallarına uygun
- SEO uyumlu anahtar kelimeler
- Müşteri odaklı ve çekici ton

Paragraf Yapısı:
1. Genel tanıtım + Lokasyon avantajları (60-80 kelime)
2. Teknik detaylar + Özellikler (80-100 kelime)
3. Çevre, ulaşım, yatırım değeri (60-80 kelime)

Açıklama:";
    }

    /**
     * Lokasyon analizi prompt'u
     *
     * AI Eğitim Paketi Template (04-PROMPT-TEMPLATES.md)
     */
    protected function buildLocationAnalysisPrompt(array $locationData): string
    {
        $il = $locationData['il'] ?? '';
        $ilce = $locationData['ilce'] ?? '';
        $mahalle = $locationData['mahalle'] ?? '';

        // AI Eğitim Paketi - Lokasyon Analizi Template
        return "Sen bir gayrimenkul analistisin. Aşağıdaki lokasyon için kısa analiz yap.

Lokasyon: {$il}, {$ilce}, {$mahalle}

Değerlendirme Kriterleri:
- Merkeze yakınlık (0-25 puan)
- Sosyal tesisler (okul, hastane) (0-20 puan)
- Ulaşım (toplu taşıma, otoyol) (0-20 puan)
- Altyapı (0-20 puan)
- Gelişim potansiyeli (0-15 puan)

Çıktı Formatı:
Skor: [0-100]
Harf: [A/B/C/D]
Potansiyel: [Yüksek/Orta/Düşük]
Gerekçe: [Kısa açıklama, max 100 kelime]

Analiz:";
    }

    /**
     * Fiyat önerisi prompt'u
     *
     * AI Eğitim Paketi Template (04-PROMPT-TEMPLATES.md)
     */
    protected function buildPriceSuggestionPrompt(array $propertyData): string
    {
        $basePrice = $propertyData['base_price'] ?? 0;
        $kategori = $propertyData['kategori'] ?? '';
        $metrekare = $propertyData['metrekare'] ?? 0;
        $lokasyon = $propertyData['lokasyon'] ?? '';

        // AI Eğitim Paketi - Fiyat Analizi Template
        return "Fiyat analizi yap ve 3 öneri sun.

Girilen Fiyat: {$basePrice} TRY
Kategori: {$kategori}
Lokasyon: {$lokasyon}
Alan: {$metrekare} m²

Hesapla:
- m² başı fiyat
- Bölge ortalaması ile karşılaştır
- 3 seviyeli öneri:
  1. Pazarlık (-10%): Hızlı satış
  2. Piyasa (+5%): Ortalama
  3. Premium (+15%): Özel özellikler

Format:
[Seviye]: [Fiyat] - [Gerekçe]

Analiz:";
    }

    /**
     * Başlık yanıtını parse et
     */
    protected function parseTitleResponse(string $response): array
    {
        $lines = array_filter(array_map('trim', explode("\n", $response)));
        $titles = [];

        foreach ($lines as $line) {
            // Numaraları ve özel karakterleri temizle
            $clean = preg_replace('/^[\d\.\-\*]+\s*/', '', $line);
            if (strlen($clean) > 20 && strlen($clean) < 150) {
                $titles[] = $clean;
            }
        }

        return array_slice($titles, 0, 3);
    }

    /**
     * Lokasyon analizi parse et
     */
    protected function parseLocationAnalysis(string $response): array
    {
        // Basit parsing
        $score = 85;
        $grade = 'A';
        $potential = 'Yüksek';

        if (preg_match('/Skor[:\s]+(\d+)/', $response, $matches)) {
            $score = (int) $matches[1];
        }

        if (preg_match('/Harf[:\s]+([A-F])/', $response, $matches)) {
            $grade = $matches[1];
        }

        if (preg_match('/Potansiyel[:\s]+(Yüksek|Orta|Düşük)/i', $response, $matches)) {
            $potential = $matches[1];
        }

        return [
            'score' => $score,
            'grade' => $grade,
            'potential' => $potential
        ];
    }

    /**
     * Fiyat önerileri parse et
     */
    protected function parsePriceSuggestions(string $response, float $basePrice): array
    {
        $numbers = [];
        preg_match_all('/[\d.,]+/', $response, $matches);

        foreach ($matches[0] as $match) {
            $num = floatval(str_replace([',', '.'], ['', ''], $match));
            if ($num > 0) {
                $numbers[] = $num;
            }
        }

        if (count($numbers) >= 3) {
            return [
                ['label' => 'Pazarlık Payı', 'value' => $numbers[0]],
                ['label' => 'Piyasa Ortalaması', 'value' => $numbers[1]],
                ['label' => 'Premium Fiyat', 'value' => $numbers[2]],
            ];
        }

        return $this->getFallbackPriceSuggestions($basePrice);
    }

    /**
     * Fallback başlıklar
     */
    protected function getFallbackTitles(array $data): array
    {
        $lokasyon = $data['lokasyon'] ?? 'Bodrum';
        $kategori = $data['kategori'] ?? 'Gayrimenkul';
        $yayin_tipi = $data['yayin_tipi'] ?? 'Satılık';

        return [
            "{$lokasyon} {$yayin_tipi} {$kategori}",
            "{$yayin_tipi} {$kategori} - {$lokasyon}",
            "{$lokasyon}'da {$yayin_tipi} {$kategori}"
        ];
    }

    /**
     * Fallback açıklama
     */
    protected function getFallbackDescription(array $data): string
    {
        return "Profesyonel bir ilan açıklaması hazırlanıyor...";
    }

    /**
     * Fallback lokasyon analizi
     */
    protected function getFallbackLocationAnalysis(): array
    {
        return [
            'score' => 75,
            'grade' => 'B',
            'potential' => 'Orta'
        ];
    }

    /**
     * Fallback fiyat önerileri
     */
    protected function getFallbackPriceSuggestions(float $basePrice): array
    {
        if ($basePrice <= 0) {
            return [];
        }

        return [
            ['label' => 'Pazarlık Payı (-10%)', 'value' => $basePrice * 0.9],
            ['label' => 'Piyasa Ortalaması (+5%)', 'value' => $basePrice * 1.05],
            ['label' => 'Premium Fiyat (+15%)', 'value' => $basePrice * 1.15],
        ];
    }

    /**
     * Health check
     *
     * @return bool
     */
    public function isHealthy(): bool
    {
        try {
            $response = Http::timeout(5)->get($this->apiUrl);
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
