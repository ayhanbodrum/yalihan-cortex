<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Context7 API Configuration
    |--------------------------------------------------------------------------
    |
    | Bu dosya Context7 API entegrasyonu için gerekli konfigürasyonları içerir.
    | API URL, API Key ve diğer ayarları buradan yönetebilirsiniz.
    |
    */

    'api' => [
        'url' => env('CONTEXT7_API_URL', 'https://context7.com/api/v1'),
        'key' => env('CONTEXT7_API_KEY', 'ctx7sk-85fd9334-5f91-472c-8d4a-c087fd3f9d7d'),
        'timeout' => env('CONTEXT7_API_TIMEOUT', 30),
        'retry_attempts' => env('CONTEXT7_API_RETRY_ATTEMPTS', 3),
    ],

    'mcp' => [
        'url' => env('CONTEXT7_MCP_URL', 'https://mcp.context7.com/mcp'),
        'enabled' => env('CONTEXT7_MCP_ENABLED', true),
    ],

    'features' => [
        'ai_chat' => env('CONTEXT7_AI_CHAT_ENABLED', true),
        'smart_search' => env('CONTEXT7_SMART_SEARCH_ENABLED', true),
        'auto_suggestions' => env('CONTEXT7_AUTO_SUGGESTIONS_ENABLED', true),
        'analytics' => env('CONTEXT7_ANALYTICS_ENABLED', true),
    ],

    'cache' => [
        'enabled' => env('CONTEXT7_CACHE_ENABLED', true),
        'ttl' => env('CONTEXT7_CACHE_TTL', 3600), // 1 hour
        'prefix' => env('CONTEXT7_CACHE_PREFIX', 'context7_'),
    ],

    'memory' => [
        'prefix' => env('CONTEXT7_MEMORY_PREFIX', 'context7:memory:'),
        'ttl' => env('CONTEXT7_MEMORY_TTL', 86400), // 24 hours
    ],

    'rate_limiting' => [
        'enabled' => env('CONTEXT7_RATE_LIMITING_ENABLED', true),
        'max_requests_per_minute' => env('CONTEXT7_MAX_REQUESTS_PER_MINUTE', 60),
        'max_requests_per_hour' => env('CONTEXT7_MAX_REQUESTS_PER_HOUR', 1000),
    ],

    'logging' => [
        'enabled' => env('CONTEXT7_LOGGING_ENABLED', true),
        'level' => env('CONTEXT7_LOG_LEVEL', 'info'),
        'channel' => env('CONTEXT7_LOG_CHANNEL', 'daily'),
    ],
];
