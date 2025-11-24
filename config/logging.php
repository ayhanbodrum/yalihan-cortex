<?php

return [
    'default' => env('LOG_CHANNEL', 'stack'),

    'deprecations' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
            'replace_placeholders' => true,
        ],

        'stderr' => [
            'driver' => 'mono',
            'handler' => Monolog\Handler\StreamHandler::class,
            'with' => [
                'stream' => 'php://stderr',
            ],
            'level' => env('LOG_LEVEL', 'debug'),
            'formatter' => Monolog\Formatter\LineFormatter::class,
            'formatter_with' => [
                'format' => null,
                'dateFormat' => 'Y-m-d H:i:s',
                'allowInlineLineBreaks' => true,
            ],
            'replace_placeholders' => true,
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'module_errors' => [
            'driver' => 'single',
            'path' => storage_path('logs/module_errors.log'),
            'level' => 'debug',
            'replace_placeholders' => true,
        ],

        'security' => [
            'driver' => 'daily',
            'path' => storage_path('logs/security.log'),
            'level' => 'info',
            'days' => 30,
            'replace_placeholders' => true,
        ],

        'crm' => [
            'driver' => 'daily',
            'path' => storage_path('logs/crm.log'),
            'level' => 'info',
            'days' => 30,
            'replace_placeholders' => true,
        ],

        'module_changes' => [
            'driver' => 'daily',
            'path' => storage_path('logs/module_changes.log'),
            'level' => 'info',
            'days' => 30,
            'replace_placeholders' => true,
        ],
    ],
];
