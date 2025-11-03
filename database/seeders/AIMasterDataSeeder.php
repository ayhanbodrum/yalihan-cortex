<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
use App\Models\FeatureCategory;
use App\Models\Feature;
use App\Models\IlanKategori;

class AIMasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->createAISettings();
        $this->createAIProviderSettings();
        $this->createAITrainingData();
        $this->createAIPrompts();
    }

    private function createAISettings()
    {
        $aiSettings = [
            // AI Provider Settings
            ['key' => 'ai_provider', 'value' => 'openai', 'type' => 'string', 'group' => 'ai', 'description' => 'Aktif AI provider'],
            ['key' => 'ai_default_tone', 'value' => 'professional', 'type' => 'string', 'group' => 'ai', 'description' => 'Varsayılan AI ton'],
            ['key' => 'ai_default_variant_count', 'value' => '3', 'type' => 'integer', 'group' => 'ai', 'description' => 'Varsayılan varyant sayısı'],
            ['key' => 'ai_default_ab_test', 'value' => 'false', 'type' => 'boolean', 'group' => 'ai', 'description' => 'A/B test aktif mi'],
            ['key' => 'ai_default_languages', 'value' => 'tr,en', 'type' => 'string', 'group' => 'ai', 'description' => 'Desteklenen diller'],
            
            // AI Performance Settings
            ['key' => 'ai_max_tokens', 'value' => '2000', 'type' => 'integer', 'group' => 'ai', 'description' => 'Maksimum token sayısı'],
            ['key' => 'ai_temperature', 'value' => '0.7', 'type' => 'string', 'group' => 'ai', 'description' => 'AI yaratıcılık seviyesi'],
            ['key' => 'ai_timeout', 'value' => '30', 'type' => 'integer', 'group' => 'ai', 'description' => 'AI istek timeout süresi'],
            ['key' => 'ai_retry_attempts', 'value' => '3', 'type' => 'integer', 'group' => 'ai', 'description' => 'AI istek yeniden deneme sayısı'],
            
            // AI Feature Flags
            ['key' => 'ai_auto_suggestions', 'value' => 'true', 'type' => 'boolean', 'group' => 'ai', 'description' => 'Otomatik öneriler aktif mi'],
            ['key' => 'ai_content_generation', 'value' => 'true', 'type' => 'boolean', 'group' => 'ai', 'description' => 'İçerik üretimi aktif mi'],
            ['key' => 'ai_analysis_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'ai', 'description' => 'AI analiz aktif mi'],
            ['key' => 'ai_training_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'ai', 'description' => 'AI eğitim aktif mi'],
            
            // AI Monitoring
            ['key' => 'ai_log_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'ai', 'description' => 'AI log aktif mi'],
            ['key' => 'ai_analytics_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'ai', 'description' => 'AI analitik aktif mi'],
            ['key' => 'ai_cost_tracking', 'value' => 'true', 'type' => 'boolean', 'group' => 'ai', 'description' => 'AI maliyet takibi aktif mi'],
        ];

        foreach ($aiSettings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    private function createAIProviderSettings()
    {
        $providerSettings = [
            // OpenAI Settings
            ['key' => 'openai_api_key', 'value' => '', 'type' => 'string', 'group' => 'ai_providers', 'description' => 'OpenAI API Key'],
            ['key' => 'openai_model', 'value' => 'gpt-3.5-turbo', 'type' => 'string', 'group' => 'ai_providers', 'description' => 'OpenAI Model'],
            ['key' => 'openai_organization', 'value' => '', 'type' => 'string', 'group' => 'ai_providers', 'description' => 'OpenAI Organization ID'],
            
            // Google Gemini Settings
            ['key' => 'google_api_key', 'value' => '', 'type' => 'string', 'group' => 'ai_providers', 'description' => 'Google Gemini API Key'],
            ['key' => 'google_model', 'value' => 'gemini-pro', 'type' => 'string', 'group' => 'ai_providers', 'description' => 'Google Gemini Model'],
            
            // Claude Settings
            ['key' => 'claude_api_key', 'value' => '', 'type' => 'string', 'group' => 'ai_providers', 'description' => 'Claude API Key'],
            ['key' => 'claude_model', 'value' => 'claude-3-sonnet-20240229', 'type' => 'string', 'group' => 'ai_providers', 'description' => 'Claude Model'],
            
            // DeepSeek Settings
            ['key' => 'deepseek_api_key', 'value' => '', 'type' => 'string', 'group' => 'ai_providers', 'description' => 'DeepSeek API Key'],
            ['key' => 'deepseek_model', 'value' => 'deepseek-chat', 'type' => 'string', 'group' => 'ai_providers', 'description' => 'DeepSeek Model'],
            
            // Ollama Settings
            ['key' => 'ollama_url', 'value' => 'http://localhost:11434', 'type' => 'string', 'group' => 'ai_providers', 'description' => 'Ollama URL'],
            ['key' => 'ollama_model', 'value' => 'llama2', 'type' => 'string', 'group' => 'ai_providers', 'description' => 'Ollama Model'],
        ];

        foreach ($providerSettings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    private function createAITrainingData()
    {
        // AI Training Data for Categories
        $categoryTrainingData = [
            [
                'name' => 'Konut Analizi',
                'description' => 'Konut tipi emlaklar için AI analiz verileri',
                'training_data' => [
                    'categories' => ['Daire', 'Villa', 'Müstakil Ev', 'Rezidans'],
                    'features' => ['Oda Sayısı', 'Metrekare', 'Balkon', 'Asansör', 'Güvenlik'],
                    'keywords' => ['satılık', 'kiralık', '3+1', '4+1', 'deniz manzarası'],
                    'price_ranges' => ['0-500000', '500000-1000000', '1000000-2000000', '2000000+']
                ]
            ],
            [
                'name' => 'Arsa Analizi',
                'description' => 'Arsa tipi emlaklar için AI analiz verileri',
                'training_data' => [
                    'categories' => ['Arsa', 'Bahçe', 'Tarla', 'Bağ'],
                    'features' => ['İmarlı', 'Ada Parsel', 'Tapu Durumu', 'Emsal'],
                    'keywords' => ['satılık arsa', 'imar', 'parsel', 'tapulu'],
                    'price_ranges' => ['0-100000', '100000-300000', '300000-500000', '500000+']
                ]
            ],
            [
                'name' => 'İşyeri Analizi',
                'description' => 'İşyeri tipi emlaklar için AI analiz verileri',
                'training_data' => [
                    'categories' => ['Ofis', 'Mağaza', 'Depo', 'Atölye'],
                    'features' => ['Metrekare', 'Cephe', 'Otopark', 'Güvenlik'],
                    'keywords' => ['işyeri', 'ofis', 'mağaza', 'depo'],
                    'price_ranges' => ['0-2000000', '2000000-5000000', '5000000-10000000', '10000000+']
                ]
            ]
        ];

        foreach ($categoryTrainingData as $data) {
            Setting::firstOrCreate(
                ['key' => 'ai_training_' . strtolower(str_replace(' ', '_', $data['name']))],
                [
                    'value' => json_encode($data['training_data']),
                    'type' => 'json',
                    'group' => 'ai_training',
                    'description' => $data['description']
                ]
            );
        }
    }

    private function createAIPrompts()
    {
        $aiPrompts = [
            // Category Analysis Prompts
            [
                'key' => 'ai_prompt_category_analysis',
                'value' => 'Bu emlak kategorilerini analiz et ve optimizasyon önerileri sun. Kategorilerin kullanım sıklığını, popülerlik durumunu ve iyileştirme alanlarını değerlendir.',
                'type' => 'string',
                'group' => 'ai_prompts',
                'description' => 'Kategori analizi için AI prompt'
            ],
            [
                'key' => 'ai_prompt_feature_suggestions',
                'value' => 'Bu emlak özelliklerini incele ve eksik özellikler için öneriler sun. Mevcut özelliklerin kullanıcı deneyimini nasıl iyileştirebileceğini değerlendir.',
                'type' => 'string',
                'group' => 'ai_prompts',
                'description' => 'Özellik önerileri için AI prompt'
            ],
            [
                'key' => 'ai_prompt_content_generation',
                'value' => 'Bu emlak ilanı için çekici ve SEO uyumlu bir başlık ve açıklama oluştur. Hedef kitleyi düşünerek profesyonel ve satış odaklı bir içerik hazırla.',
                'type' => 'string',
                'group' => 'ai_prompts',
                'description' => 'İçerik üretimi için AI prompt'
            ],
            [
                'key' => 'ai_prompt_price_analysis',
                'value' => 'Bu emlak için piyasa analizi yaparak uygun fiyat önerisi sun. Bölge, özellikler ve mevcut piyasa koşullarını dikkate al.',
                'type' => 'string',
                'group' => 'ai_prompts',
                'description' => 'Fiyat analizi için AI prompt'
            ],
            [
                'key' => 'ai_prompt_seo_optimization',
                'value' => 'Bu emlak ilanını SEO açısından optimize et. Anahtar kelimeler, meta açıklamalar ve arama motoru uyumluluğunu iyileştir.',
                'type' => 'string',
                'group' => 'ai_prompts',
                'description' => 'SEO optimizasyonu için AI prompt'
            ],
            
            // Market Analysis Prompts
            [
                'key' => 'ai_prompt_market_trends',
                'value' => 'Emlak piyasası trendlerini analiz et ve gelecek öngörüleri sun. Bölgesel fiyat hareketleri, talep değişimleri ve yatırım fırsatlarını değerlendir.',
                'type' => 'string',
                'group' => 'ai_prompts',
                'description' => 'Piyasa trendleri için AI prompt'
            ],
            [
                'key' => 'ai_prompt_location_analysis',
                'value' => 'Bu lokasyonun emlak değerini analiz et. Ulaşım, sosyal tesisler, eğitim ve sağlık hizmetleri açısından değerlendirme yap.',
                'type' => 'string',
                'group' => 'ai_prompts',
                'description' => 'Lokasyon analizi için AI prompt'
            ],
            
            // User Experience Prompts
            [
                'key' => 'ai_prompt_user_behavior',
                'value' => 'Kullanıcı davranış verilerini analiz et ve platform iyileştirme önerileri sun. Arama kalıpları, tıklama davranışları ve kullanıcı tercihlerini değerlendir.',
                'type' => 'string',
                'group' => 'ai_prompts',
                'description' => 'Kullanıcı davranış analizi için AI prompt'
            ],
            [
                'key' => 'ai_prompt_recommendations',
                'value' => 'Bu kullanıcı için kişiselleştirilmiş emlak önerileri sun. Tercihler, bütçe ve ihtiyaçları dikkate alarak uygun ilanları öner.',
                'type' => 'string',
                'group' => 'ai_prompts',
                'description' => 'Kişiselleştirilmiş öneriler için AI prompt'
            ]
        ];

        foreach ($aiPrompts as $prompt) {
            Setting::firstOrCreate(
                ['key' => $prompt['key']],
                $prompt
            );
        }
    }
}
