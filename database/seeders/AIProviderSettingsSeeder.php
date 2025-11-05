<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

/**
 * AI Provider Settings Seeder
 * 
 * Context7 Standardı: C7-AI-PROVIDER-SEEDER-2025-11-05
 * 
 * AI provider ayarlarını veritabanına ekler.
 * - Provider seçimi (OpenAI, Gemini, Claude, DeepSeek, Ollama)
 * - API key'leri (boş başlangıç, kullanıcı dolduracak)
 * - Model seçenekleri
 * - Varsayılan ayarlar
 */
class AIProviderSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🤖 AI Provider ayarları seed ediliyor...');

        $settings = [
            // Provider Selection
            [
                'key' => 'ai_provider',
                'value' => 'openai',
                'type' => 'string',
                'group' => 'ai',
                'description' => 'Aktif AI provider (openai, google, anthropic, deepseek, ollama)'
            ],

            // OpenAI Settings
            [
                'key' => 'openai_api_key',
                'value' => '',
                'type' => 'string',
                'group' => 'ai',
                'description' => 'OpenAI API Key (sk-...)'
            ],
            [
                'key' => 'openai_model',
                'value' => 'gpt-3.5-turbo',
                'type' => 'string',
                'group' => 'ai',
                'description' => 'OpenAI Model (gpt-3.5-turbo, gpt-4, gpt-4-turbo)'
            ],

            // Google Gemini Settings
            [
                'key' => 'google_api_key',
                'value' => '',
                'type' => 'string',
                'group' => 'ai',
                'description' => 'Google Gemini API Key'
            ],
            [
                'key' => 'google_model',
                'value' => 'gemini-pro',
                'type' => 'string',
                'group' => 'ai',
                'description' => 'Google Gemini Model (gemini-pro, gemini-pro-vision)'
            ],

            // Anthropic Claude Settings
            [
                'key' => 'claude_api_key',
                'value' => '',
                'type' => 'string',
                'group' => 'ai',
                'description' => 'Anthropic Claude API Key'
            ],
            [
                'key' => 'claude_model',
                'value' => 'claude-3-sonnet-20240229',
                'type' => 'string',
                'group' => 'ai',
                'description' => 'Claude Model (claude-3-sonnet, claude-3-opus, claude-3-haiku)'
            ],

            // DeepSeek Settings
            [
                'key' => 'deepseek_api_key',
                'value' => '',
                'type' => 'string',
                'group' => 'ai',
                'description' => 'DeepSeek API Key'
            ],
            [
                'key' => 'deepseek_model',
                'value' => 'deepseek-chat',
                'type' => 'string',
                'group' => 'ai',
                'description' => 'DeepSeek Model (deepseek-chat, deepseek-coder)'
            ],

            // Ollama Settings (Local)
            [
                'key' => 'ollama_url',
                'value' => 'http://localhost:11434',
                'type' => 'string',
                'group' => 'ai',
                'description' => 'Ollama API URL (Local AI Server)'
            ],
            [
                'key' => 'ollama_model',
                'value' => 'llama2',
                'type' => 'string',
                'group' => 'ai',
                'description' => 'Ollama Model (llama2, mistral, codellama, etc.)'
            ],

            // Default AI Settings
            [
                'key' => 'ai_default_tone',
                'value' => 'professional',
                'type' => 'string',
                'group' => 'ai',
                'description' => 'Default AI tone (professional, friendly, casual)'
            ],
            [
                'key' => 'ai_default_variant_count',
                'value' => '3',
                'type' => 'integer',
                'group' => 'ai',
                'description' => 'Default number of AI variants to generate'
            ],
            [
                'key' => 'ai_enabled',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'ai',
                'description' => 'AI system enabled (0=disabled, 1=enabled)'
            ],
            [
                'key' => 'ai_max_tokens',
                'value' => '500',
                'type' => 'integer',
                'group' => 'ai',
                'description' => 'Maximum tokens for AI responses'
            ],
            [
                'key' => 'ai_temperature',
                'value' => '0.7',
                'type' => 'decimal',
                'group' => 'ai',
                'description' => 'AI temperature (0.0-1.0, higher = more creative)'
            ],
        ];

        $created = 0;
        $updated = 0;

        foreach ($settings as $setting) {
            $existing = Setting::where('key', $setting['key'])->first();
            
            if ($existing) {
                $existing->update($setting);
                $updated++;
            } else {
                Setting::create($setting);
                $created++;
            }
        }

        $this->command->info("  ✅ {$created} yeni ayar eklendi");
        if ($updated > 0) {
            $this->command->info("  🔄 {$updated} ayar güncellendi");
        }
        $this->command->info("  📊 Toplam: " . count($settings) . " AI ayarı");
        $this->command->info('✅ AI Provider ayarları tamamlandı!');
    }
}

