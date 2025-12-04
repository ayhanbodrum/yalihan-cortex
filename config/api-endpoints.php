<?php

/**
 * API Endpoint Registry
 *
 * Context7 Standard: C7-API-ENDPOINT-REGISTRY-2025-12-03
 *
 * Merkezi API endpoint kayıt sistemi.
 * Tüm endpoint'ler burada tanımlanır ve JavaScript'te kullanılır.
 *
 * @version 1.0.0
 * @since 2025-12-03
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Base URLs
    |--------------------------------------------------------------------------
    */
    'base_url' => env('APP_URL', 'http://localhost:8000'),
    'api_prefix' => '/api',
    'api_v1_prefix' => '/api/v1',

    /*
    |--------------------------------------------------------------------------
    | Location API Endpoints
    |--------------------------------------------------------------------------
    */
    'location' => [
        'provinces' => '/api/location/iller',
        'districts' => '/api/location/districts/{id}',
        'neighborhoods' => '/api/location/neighborhoods/{id}',
        'geocode' => '/api/location/geocode',
        'reverse_geocode' => '/api/location/reverse-geocode',
        'nearby' => '/api/location/nearby/{lat}/{lng}/{radius?}',
    ],

    /*
    |--------------------------------------------------------------------------
    | Categories API Endpoints
    |--------------------------------------------------------------------------
    */
    'categories' => [
        'subcategories' => '/api/categories/sub/{parentId}',
        'publication_types' => '/api/categories/publication-types/{categoryId}',
        'fields' => '/api/categories/fields/{categoryId}/{publicationTypeId?}',
        'detail' => '/api/categories/{id}',
    ],

    /*
    |--------------------------------------------------------------------------
    | Live Search API Endpoints
    |--------------------------------------------------------------------------
    */
    'live_search' => [
        'kisiler' => '/api/kisiler/search',
        'danismanlar' => '/api/users/search',
        'sites' => '/api/sites/search',
        'unified' => '/api/search/unified',
    ],

    /*
    |--------------------------------------------------------------------------
    | TKGM API Endpoints
    |--------------------------------------------------------------------------
    */
    'tkgm' => [
        'parsel_sorgu' => '/api/tkgm/parsel-sorgu',
        'yatirim_analizi' => '/api/tkgm/yatirim-analizi',
        'health' => '/api/tkgm/health',
    ],

    /*
    |--------------------------------------------------------------------------
    | Properties API Endpoints
    |--------------------------------------------------------------------------
    */
    'properties' => [
        'tkgm_lookup' => '/api/properties/tkgm-lookup',
        'calculate' => '/api/properties/calculate',
    ],

    /*
    |--------------------------------------------------------------------------
    | Environment API Endpoints
    |--------------------------------------------------------------------------
    */
    'environment' => [
        'analyze' => '/api/environment/analyze',
        'category' => '/api/environment/category/{category}',
        'value_prediction' => '/api/environment/value-prediction',
        'pois' => '/api/environment/pois',
    ],

    /*
    |--------------------------------------------------------------------------
    | AI API Endpoints
    |--------------------------------------------------------------------------
    */
    'ai' => [
        'analyze' => '/api/ai/analyze',
        'suggest' => '/api/ai/suggest',
        'generate' => '/api/ai/generate',
        'health' => '/api/ai/health',
        'start_video_render' => '/api/ai/start-video-render/{ilanId}',
        'video_status' => '/api/ai/video-status/{ilanId}',
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin API Endpoints
    |--------------------------------------------------------------------------
    */
    'admin' => [
        'generate_ai_title' => '/admin/ilanlar/generate-ai-title',
        'generate_ai_description' => '/admin/ilanlar/generate-ai-description',
        'convert_price_to_text' => '/admin/ilanlar/convert-price-to-text',
        'live_search' => '/admin/ilanlar/live-search',
    ],

    /*
    |--------------------------------------------------------------------------
    | Yalihan Cortex API Endpoints
    |--------------------------------------------------------------------------
    */
    'cortex' => [
        'analyze' => '/api/admin/cortex/analyze/{id}',
        'video' => '/api/admin/cortex/video/{id}',
        'photos' => '/api/admin/cortex/photos/{id}',
    ],

    /*
    |--------------------------------------------------------------------------
    | Helper Functions
    |--------------------------------------------------------------------------
    */
    'helpers' => [
        'replace_params' => function ($endpoint, $params = []) {
            $url = $endpoint;
            foreach ($params as $key => $value) {
                $url = str_replace('{' . $key . '}', $value, $url);
                $url = str_replace('{' . $key . '?}', $value, $url);
            }
            // Remove optional parameters that weren't replaced
            $url = preg_replace('/\{[^}]+\?\}/', '', $url);
            return $url;
        },
    ],
];
