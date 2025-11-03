<?php

return [
    /*
    |--------------------------------------------------------------------------
    | TestSprite MCP Sunucu Ayarları
    |--------------------------------------------------------------------------
    |
    | Bu ayarlar TestSprite MCP sunucusunun nasıl çalışacağını belirler.
    |
    */

    'server_url' => env('TESTSPRITE_SERVER_URL', 'http://localhost:3333'),
    'node_path' => env('TESTSPRITE_NODE_PATH', 'node'),
    
    /*
    |--------------------------------------------------------------------------
    | Otomatik Düzeltme
    |--------------------------------------------------------------------------
    |
    | Bu ayar, TestSprite MCP'nin tespit ettiği basit hataları otomatik
    | olarak düzeltip düzeltmeyeceğini belirler.
    |
    */
    
    'auto_correct' => env('TESTSPRITE_AUTO_CORRECT', false),
    
    /*
    |--------------------------------------------------------------------------
    | Bildirim Ayarları
    |--------------------------------------------------------------------------
    |
    | Bu ayarlar, TestSprite MCP'nin hata durumlarında nasıl bildirim
    | göndereceğini belirler.
    |
    */
    
    'notifications' => [
        'enabled' => env('TESTSPRITE_NOTIFICATIONS_ENABLED', true),
        'channels' => [
            'mail' => [
                'enabled' => env('TESTSPRITE_MAIL_NOTIFICATIONS_ENABLED', false),
                'recipients' => explode(',', env('TESTSPRITE_MAIL_RECIPIENTS', '')),
            ],
            'slack' => [
                'enabled' => env('TESTSPRITE_SLACK_NOTIFICATIONS_ENABLED', false),
                'webhook' => env('TESTSPRITE_SLACK_WEBHOOK', ''),
                'channel' => env('TESTSPRITE_SLACK_CHANNEL', '#testsprite'),
            ],
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Test Kuralları
    |--------------------------------------------------------------------------
    |
    | Bu ayarlar, TestSprite MCP'nin hangi kuralları uygulayacağını belirler.
    |
    */
    
    'rules' => [
        'migrations' => [
            'enforce_module_structure' => true,
            'check_syntax' => true,
            'validate_semantic_versioning' => true,
        ],
        'seeders' => [
            'enforce_module_structure' => true,
            'check_syntax' => true,
            'validate_dependencies' => true,
        ],
        'code_standards' => [
            'enforce_psr12' => true,
            'check_vue_composition_api' => true,
            'validate_blade_strict_mode' => true,
        ],
        'security' => [
            'check_env_files' => true,
            'validate_api_keys' => true,
            'enforce_encryption' => true,
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Rapor Ayarları
    |--------------------------------------------------------------------------
    |
    | Bu ayarlar, TestSprite MCP'nin raporları nereye ve nasıl kaydedeceğini
    | belirler.
    |
    */
    
    'reports' => [
        'path' => storage_path('app/testsprite/reports'),
        'keep_days' => 30, // Raporların kaç gün saklanacağı
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Zamanlama Ayarları
    |--------------------------------------------------------------------------
    |
    | Bu ayarlar, TestSprite MCP'nin testleri ne sıklıkla çalıştıracağını
    | belirler.
    |
    */
    
    'schedule' => [
        'daily_at' => '03:00', // Her gün saat 03:00'da çalıştır
        'run_on_pre_commit' => true, // Pre-commit hook'ta çalıştır
    ],
];