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

];
