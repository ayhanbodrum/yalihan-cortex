<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Services (AI Only - Maps removed)
    |--------------------------------------------------------------------------
    */

    'google' => [
        'api_key' => env('GOOGLE_API_KEY', ''),
        'model' => env('GOOGLE_MODEL', 'gemini-2.5-flash'),
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Providers
    |--------------------------------------------------------------------------
    */

    'openai' => [
        'api_key' => env('OPENAI_API_KEY', ''),
    ],

    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY', ''),
    ],

    'deepseek' => [
        'api_key' => env('DEEPSEEK_API_KEY', ''),
    ],

    'gemini' => [
        'api_key' => env('GOOGLE_API_KEY', ''),
        'model' => env('GOOGLE_MODEL', 'gemini-2.5-flash'),
    ],

    /*
    |--------------------------------------------------------------------------
    | TKGM (Tapu Kadastro) Servisi
    | Context7 Kural #70: TKGM Entegrasyonu
    |--------------------------------------------------------------------------
    */

    'tkgm' => [
        'base_url' => env('TKGM_BASE_URL', 'https://parselsorgu.tkgm.gov.tr'),
        'api_key' => env('TKGM_API_KEY', ''),
        'timeout' => env('TKGM_TIMEOUT', 10), // seconds
        'cache_enabled' => env('TKGM_CACHE_ENABLED', true),
        'cache_ttl' => env('TKGM_CACHE_TTL', 3600), // 1 hour
    ],

    /*
    |--------------------------------------------------------------------------
    | Wikimapia API
    | Geo-location and Places Data Integration
    | Documentation: https://wikimapia.org/api/
    |--------------------------------------------------------------------------
    */

    'wikimapia' => [
        'base_url' => env('WIKIMAPIA_BASE_URL', 'http://api.wikimapia.org'),
        'api_key' => env('WIKIMAPIA_API_KEY', ''),
        'timeout' => env('WIKIMAPIA_TIMEOUT', 10), // seconds
        'cache_enabled' => env('WIKIMAPIA_CACHE_ENABLED', true),
        'cache_ttl' => env('WIKIMAPIA_CACHE_TTL', 3600), // 1 hour
        'language' => env('WIKIMAPIA_LANGUAGE', 'tr'), // ISO 639-1 format
        'format' => env('WIKIMAPIA_FORMAT', 'json'), // xml, json, jsonp, kml
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenStreetMap Nominatim Service (FREE Alternative)
    |--------------------------------------------------------------------------
    |
    | FREE geocoding and place search service
    | Rate limit: 1 request/second
    | Coverage: Worldwide
    |
    */

    'nominatim' => [
        'base_url' => env('NOMINATIM_BASE_URL', 'https://nominatim.openstreetmap.org'),
        'email' => env('NOMINATIM_EMAIL', env('MAIL_FROM_ADDRESS', 'admin@yalihanemlak.com.tr')),
        'timeout' => env('NOMINATIM_TIMEOUT', 10),
        'cache_enabled' => env('NOMINATIM_CACHE_ENABLED', true),
        'cache_ttl' => env('NOMINATIM_CACHE_TTL', 3600),
    ],

    /*
    |--------------------------------------------------------------------------
    | ElevenLabs TTS Service
    |--------------------------------------------------------------------------
    */

    'elevenlabs' => [
        'base_url' => env('ELEVENLABS_BASE_URL', 'https://api.elevenlabs.io'),
        'api_key' => env('ELEVENLABS_API_KEY', ''),
        'model' => env('ELEVENLABS_MODEL', 'eleven_multilingual_v2'),
        'timeout' => env('ELEVENLABS_TIMEOUT', 20),
    ],

    /*
    |--------------------------------------------------------------------------
    | n8n Integration Service
    | Context7: n8n webhook entegrasyonu için yapılandırma
    |--------------------------------------------------------------------------
    */

    'n8n' => [
        'webhook_url' => env('N8N_WEBHOOK_URL', 'http://localhost:5678'),
        'webhook_secret' => env('N8N_WEBHOOK_SECRET', ''),
        'timeout' => env('N8N_TIMEOUT', 30), // seconds
        'new_ilan_webhook_url' => env('N8N_NEW_ILAN_WEBHOOK', 'https://n8n.yalihanemlak.com.tr/webhook/ilan-olustu'),

        // Context7: Otonom Fiyat Değişim Takibi ve n8n Entegrasyonu
        // Multi-Channel (Telegram, WhatsApp, Email) bildirim desteği
        'ilan_price_changed_webhook_url' => env('N8N_ILAN_PRICE_CHANGED_WEBHOOK', 'https://n8n.yalihanemlak.com.tr/webhook/ilan-fiyat-degisti'),

        // Context7: Takım Yönetimi Otomasyonu - Temel Event Sistemi
        // Multi-Channel (Telegram, WhatsApp, Email) bildirim desteği
        'gorev_created_webhook_url' => env('N8N_GOREV_CREATED_WEBHOOK', 'https://n8n.yalihanemlak.com.tr/webhook/gorev-olustu'),
        'gorev_status_changed_webhook_url' => env('N8N_GOREV_STATUS_CHANGED_WEBHOOK', 'https://n8n.yalihanemlak.com.tr/webhook/gorev-durum-degisti'),
        'gorev_deadline_yaklasiyor_webhook_url' => env('N8N_GOREV_DEADLINE_YAKLASIYOR_WEBHOOK', 'https://n8n.yalihanemlak.com.tr/webhook/gorev-deadline-yaklasiyor'),
        'gorev_gecikti_webhook_url' => env('N8N_GOREV_GECIKTI_WEBHOOK', 'https://n8n.yalihanemlak.com.tr/webhook/gorev-gecikti'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Telegram Bot Service
    | Context7: YalihanCortex_Bot - AI özellikleri ve CRM entegrasyonu
    |--------------------------------------------------------------------------
    */

    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN', ''),
        'bot_username' => env('TELEGRAM_BOT_USERNAME', 'YalihanCortex_Bot'),
        'webhook_url' => env('TELEGRAM_WEBHOOK_URL', 'https://panel.yalihanemlak.com.tr/api/telegram/webhook'),
        'team_channel_id' => env('TELEGRAM_TEAM_CHANNEL_ID', ''),
        'admin_chat_id' => env('TELEGRAM_ADMIN_CHAT_ID', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Frontend API Configuration
    | Context7: Vitrin (Mağaza) ile Panel (Depo) arasındaki internal API
    |--------------------------------------------------------------------------
    */

    'frontend_api' => [
        'internal_key' => env('FRONTEND_API_KEY', ''),
        'allowed_ips' => env('FRONTEND_API_ALLOWED_IPS', '172.17.0.0/16,10.0.0.0/8') ? explode(',', env('FRONTEND_API_ALLOWED_IPS', '172.17.0.0/16,10.0.0.0/8')) : [],
        'log_requests' => env('FRONTEND_API_LOG_REQUESTS', false),
        'rate_limit' => env('FRONTEND_API_RATE_LIMIT', 60), // istek/dakika
    ],

];
