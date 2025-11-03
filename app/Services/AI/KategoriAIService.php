<?php

namespace App\Services\AI;

use App\Models\IlanKategori;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Context7 Uyumlu Kategori AI Servisi
 *
 * Bu servis, kategori yönetimi için AI destekli özellikler sunar:
 * - Akıllı kategori açıklama üretimi
 * - SEO optimizasyonu
 * - Kategori analizi ve önerileri
 * - Hiyerarşi optimizasyonu
 */
class KategoriAIService
{
    /**
     * AI servisi instance'ı
     */
    private $aiService;

    public function __construct()
    {
        // Multi-provider AI service (DeepSeek, GPT-4, Gemini)
        $this->aiService = app('App\Services\AIService');
    }

    /**
     * Kategori için AI açıklama üret
     *
     * @param IlanKategori $kategori
     * @param array $options
     * @return array
     */
    public function generateDescription(IlanKategori $kategori, array $options = []): array
    {
        try {
            // Prompt dosyasını yükle
            $promptPath = base_path('ai/prompts/kategori-aciklama-olustur.prompt.md');

            if (!file_exists($promptPath)) {
                throw new \Exception('Kategori açıklama prompt dosyası bulunamadı');
            }

            $promptTemplate = file_get_contents($promptPath);

            // Kategori verilerini hazırla
            $kategoriData = [
                'kategori_adi' => $kategori->name,
                'kategori_seviyesi' => $kategori->seviye ?? ($kategori->parent_id ? 2 : 1),
                'parent_kategori' => $kategori->parent ? $kategori->parent->name : null,
                'mevcut_aciklama' => $kategori->description,
                'ilan_sayisi' => $kategori->ilanlar()->count(),
                'alt_kategoriler' => $kategori->children->pluck('name')->toArray(),
                'yayin_tipleri' => $kategori->yayinTipleri->pluck('name')->toArray() ?? [],
                'seo_keywords' => $options['seo_keywords'] ?? [],
                'hedef_kitle' => $options['hedef_kitle'] ?? 'Genel',
            ];

            // AI prompt oluştur
            $prompt = $this->buildPrompt($promptTemplate, $kategoriData);

            // AI'dan sonuç al
            $aiResponse = $this->aiService->generate($prompt, [
                'provider' => $options['provider'] ?? 'deepseek',
                'max_tokens' => 500,
                'temperature' => 0.7,
            ]);

            // Cache'le (24 saat)
            $cacheKey = "kategori_ai_description_{$kategori->id}";
            Cache::put($cacheKey, $aiResponse, now()->addHours(24));

            return [
                'success' => true,
                'kategori_id' => $kategori->id,
                'description' => $aiResponse['result'] ?? '',
                'seo_data' => $aiResponse['seo_data'] ?? [],
                'metadata' => $aiResponse['metadata'] ?? [],
                'cache_key' => $cacheKey,
            ];

        } catch (\Exception $e) {
            Log::error('KategoriAIService::generateDescription hatası: ' . $e->getMessage(), [
                'kategori_id' => $kategori->id,
                'trace' => $e->getTraceAsString(),
            ]);

            // Fallback açıklama
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'fallback_description' => $this->getFallbackDescription($kategori),
            ];
        }
    }

    /**
     * Kategori SEO optimizasyonu
     *
     * @param IlanKategori $kategori
     * @return array
     */
    public function optimizeSEO(IlanKategori $kategori): array
    {
        try {
            $promptPath = base_path('ai/prompts/kategori-seo-optimizasyon.prompt.md');

            if (!file_exists($promptPath)) {
                throw new \Exception('Kategori SEO prompt dosyası bulunamadı');
            }

            $promptTemplate = file_get_contents($promptPath);

            $seoData = [
                'kategori_adi' => $kategori->name,
                'kategori_seviyesi' => $kategori->seviye ?? ($kategori->parent_id ? 2 : 1),
                'ilan_sayisi' => $kategori->ilanlar()->count(),
                'parent_kategori' => $kategori->parent ? $kategori->parent->name : null,
                'mevcut_seo' => [
                    'title' => $kategori->seo_title ?? null,
                    'description' => $kategori->seo_description ?? null,
                    'keywords' => $kategori->seo_keywords ?? null,
                ],
            ];

            $prompt = $this->buildPrompt($promptTemplate, $seoData);

            $aiResponse = $this->aiService->generate($prompt, [
                'provider' => 'deepseek',
                'max_tokens' => 800,
                'temperature' => 0.5, // Daha tutarlı sonuçlar için düşük
            ]);

            return [
                'success' => true,
                'kategori_id' => $kategori->id,
                'seo_recommendations' => $aiResponse,
            ];

        } catch (\Exception $e) {
            Log::error('KategoriAIService::optimizeSEO hatası: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Tüm kategoriler için akıllı öneriler
     *
     * @param string $analizTipi
     * @return array
     */
    public function analyzeAllCategories(string $analizTipi = 'hiyerarşi'): array
    {
        try {
            $tumKategoriler = IlanKategori::with(['parent', 'children'])
                ->withCount('ilanlar')
                ->get();

            $kategoriIstatistikleri = [
                'toplam' => $tumKategoriler->count(),
                'ana_kategoriler' => $tumKategoriler->where('seviye', 1)->count(),
                'alt_kategoriler' => $tumKategoriler->where('seviye', 2)->count(),
                'bos_kategoriler' => $tumKategoriler->where('ilanlar_count', 0)->count(),
                'ortalama_ilan' => $tumKategoriler->avg('ilanlar_count'),
            ];

            $ilanDagilimi = $tumKategoriler->map(function ($kategori) {
                return [
                    'id' => $kategori->id,
                    'name' => $kategori->name,
                    'ilan_sayisi' => $kategori->ilanlar_count,
                    'yuzde' => $tumKategoriler->sum('ilanlar_count') > 0
                        ? round(($kategori->ilanlar_count / $tumKategoriler->sum('ilanlar_count')) * 100, 2)
                        : 0,
                ];
            });

            // Prompt hazırla
            $promptPath = base_path('ai/prompts/kategori-akilli-oneriler.prompt.md');

            if (!file_exists($promptPath)) {
                throw new \Exception('Kategori akıllı öneriler prompt dosyası bulunamadı');
            }

            $promptTemplate = file_get_contents($promptPath);

            $analysisData = [
                'tum_kategoriler' => $tumKategoriler->map(function ($k) {
                    return [
                        'id' => $k->id,
                        'name' => $k->name,
                        'seviye' => $k->seviye ?? ($k->parent_id ? 2 : 1),
                        'parent_id' => $k->parent_id,
                        'ilan_sayisi' => $k->ilanlar_count,
                        'status' => $k->status,
                    ];
                })->toArray(),
                'analiz_tipi' => $analizTipi,
                'kategori_istatistikleri' => $kategoriIstatistikleri,
                'ilan_dagilimi' => $ilanDagilimi,
            ];

            $prompt = $this->buildPrompt($promptTemplate, $analysisData);

            $aiResponse = $this->aiService->generate($prompt, [
                'provider' => 'deepseek',
                'max_tokens' => 2000,
                'temperature' => 0.6,
            ]);

            // Cache'le (6 saat)
            $cacheKey = "kategori_ai_analysis_{$analizTipi}_" . md5(serialize($analysisData));
            Cache::put($cacheKey, $aiResponse, now()->addHours(6));

            return [
                'success' => true,
                'analysis_type' => $analizTipi,
                'statistics' => $kategoriIstatistikleri,
                'recommendations' => $aiResponse,
                'cache_key' => $cacheKey,
            ];

        } catch (\Exception $e) {
            Log::error('KategoriAIService::analyzeAllCategories hatası: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'fallback_recommendations' => $this->getBasicRecommendations(),
            ];
        }
    }

    /**
     * Prompt template'i verilerle doldur
     *
     * @param string $template
     * @param array $data
     * @return string
     */
    private function buildPrompt(string $template, array $data): string
    {
        $prompt = "Context7 Kategori AI Analizi\n\n";
        $prompt .= "Kategori Verileri:\n";
        $prompt .= json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $prompt .= "\n\n";
        $prompt .= "Lütfen yukarıdaki verilere göre profesyonel ve Context7 uyumlu sonuç üret.";

        return $prompt;
    }

    /**
     * Fallback açıklama üret (AI başarısız olursa)
     *
     * @param IlanKategori $kategori
     * @return string
     */
    private function getFallbackDescription(IlanKategori $kategori): string
    {
        $ilanSayisi = $kategori->ilanlar()->count();
        $parent = $kategori->parent ? $kategori->parent->name : '';

        if ($kategori->parent_id) {
            return "{$kategori->name} kategorisinde {$ilanSayisi} adet ilan bulunmaktadır. " .
                   "{$parent} ana kategorisi altında yer alan bu kategori, " .
                   "geniş ilan seçenekleri ve profesyonel danışmanlık hizmeti sunmaktadır.";
        } else {
            $altKategoriSayisi = $kategori->children()->count();
            return "{$kategori->name} ana kategorisinde {$ilanSayisi} adet ilan ve " .
                   "{$altKategoriSayisi} alt kategori bulunmaktadır. " .
                   "Geniş seçenekler ve profesyonel hizmet ile size en uygun emlağı bulmak artık çok kolay.";
        }
    }

    /**
     * Basit öneriler üret (AI fallback)
     *
     * @return array
     */
    private function getBasicRecommendations(): array
    {
        return [
            [
                'type' => 'basic_check',
                'priority' => 'Orta',
                'title' => 'Kategori Yapısını Kontrol Edin',
                'description' => 'Boş kategorileri temizleyin ve aktif kategorileri optimize edin.',
                'action_items' => [
                    'Boş kategorileri tespit edin',
                    'Benzer kategorileri birleştirin',
                    'SEO açıklamalarını güncelleyin',
                ],
            ],
        ];
    }

    /**
     * Cache'den AI sonucu al
     *
     * @param string $cacheKey
     * @return array|null
     */
    public function getFromCache(string $cacheKey): ?array
    {
        return Cache::get($cacheKey);
    }

    /**
     * Cache'i temizle
     *
     * @param IlanKategori|null $kategori
     * @return void
     */
    public function clearCache(?IlanKategori $kategori = null): void
    {
        if ($kategori) {
            Cache::forget("kategori_ai_description_{$kategori->id}");
        } else {
            Cache::flush();
        }
    }
}

