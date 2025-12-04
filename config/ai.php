<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI API Ayarları
    |--------------------------------------------------------------------------
    |
    | Bu dosya, AI servisleriyle entegre çalışacak modüller için gerekli
    | yapılandırma ayarlarını içerir.
    |
    */

    'endpoint' => env('AI_API_ENDPOINT', 'https://api.openai.com/v1/chat/completions'),

    'api_key' => env('OPENAI_API_KEY', null),

    'deepseek_api_key' => env('DEEPSEEK_API_KEY', null),

    'minimax_api_key' => env('MINIMAX_API_KEY', null),
    'minimax_model' => env('MINIMAX_MODEL', 'minimax-m2'),

    'google_api_key' => env('GOOGLE_API_KEY', null),

    'google_model' => env('GOOGLE_MODEL', 'gemini-2.5-flash'),

    'anthropic_api_key' => env('ANTHROPIC_API_KEY', null),

    /*
    |--------------------------------------------------------------------------
    | ⚠️ GÜVENLİK NOTU: Ollama Endpoint (LOCALHOST)
    |--------------------------------------------------------------------------
    |
    | ❗ BU AYAR SADECE SSH TUNNEL VEYA WIREGUARD VPN AKTİF OLDUĞUNDA ÇALIŞIR!
    |
    | Geçici Fix: Public HTTP bağlantısından kaçınmak için localhost kullanılıyor.
    |
    | SSH Tunnel Kurulumu:
    |   ssh -L 11434:localhost:11434 root@51.75.64.121 -N -f
    |
    | WireGuard VPN Kurulumu:
    |   wg-quick up wg0  (10.0.0.1:11434)
    |
    | Production Gereksinimi:
    |   - Nginx Reverse Proxy ile HTTPS zorunlu
    |   - Cloudflare Tunnel veya Let's Encrypt SSL sertifikası
    |   - Rate limiting ve IP whitelisting
    |
    | KVKK Uyarısı: HTTP plain text ile müşteri verileri taşınmamalı!
    |
    */
    'ollama_api_url' => env('OLLAMA_API_URL', 'https://ollama.yalihanemlak.internal'),

    'ollama_model' => env('OLLAMA_MODEL', 'gemma2:2b'),

    'ollama_endpoint' => env('OLLAMA_API_URL', 'https://ollama.yalihanemlak.internal'),

    'provider' => env('AI_PROVIDER', 'ollama'),

    'default_model' => env('AI_DEFAULT_MODEL', 'gemma2:2b'),

    'fallback_model' => env('AI_FALLBACK_MODEL', 'gpt-3.5-turbo'),
    'require_tls' => env('AI_REQUIRE_TLS', false), // ✅ KVKK Compliance: Production'da env ile true yapılmalı, local'de false

    /*
    |--------------------------------------------------------------------------
    | AI Özellik Ayarları
    |--------------------------------------------------------------------------
    |
    | Otomatik açıklama, etiket önerisi gibi özelliklerin açık/kapalı status
    |
    */
    'auto_description' => env('AI_AUTO_DESCRIPTION', false),

    'smart_tags' => env('AI_SMART_TAGS', false),

    // Taksonomi + özellik zenginleştirme
    'enrich_taxonomy' => env('AI_ENRICH_TAXONOMY', true),
    'max_feature_context' => env('AI_MAX_FEATURE_CONTEXT', 25),
    'feature_cache_ttl' => env('AI_FEATURE_CACHE_TTL', 600), // sn

    /*
    |--------------------------------------------------------------------------
    | Cache Süresi (dakika)
    |--------------------------------------------------------------------------
    |
    | AI yanıtlarının önbelleğe alınacağı süre
    |
    */
    'cache_duration' => env('AI_CACHE_DURATION', 60), // 60 dakika

    /*
    |--------------------------------------------------------------------------
    | Prompt Şablonları
    |--------------------------------------------------------------------------
    |
    | Çeşitli modüller için kullanılacak prompt şablonlarının yolları
    |
    */
    'prompts' => [
        'talep_analiz' => 'prompts/talep-analizi.prompt.md',
        'aciklama_olustur' => 'prompts/aciklama-olustur.prompt.md',
        'etiket_oner' => 'prompts/etiket-oner.prompt.md',
    ],
];
