<?php

/**
 * Context7 Auto-Fix Violations Script
 * Yalıhan Bekçi: Forbidden pattern'leri otomatik düzeltir
 *
 * Yasaklı → Context7 Uyumlu:
 * - durum → status
 * - is_active → enabled
 * - aktif → active
 * - sehir_id → city_id
 * - sehir → city
 * - ad_soyad → full_name
 */
$basePath = __DIR__.'/..';
$patterns = [
    // Database column patterns (dikkatli olmalıyız)
    [
        'search' => "->where('durum',",
        'replace' => "->where('status',",
        'description' => 'Query: durum → status',
    ],
    [
        'search' => "->where('is_active',",
        'replace' => "->where('enabled',",
        'description' => 'Query: is_active → enabled',
    ],
    [
        'search' => "->where('aktif',",
        'replace' => "->where('active',",
        'description' => 'Query: aktif → active',
    ],
    [
        'search' => "->where('sehir_id',",
        'replace' => "->where('city_id',",
        'description' => 'Query: sehir_id → city_id',
    ],

    // Array key patterns
    [
        'search' => "'durum'",
        'replace' => "'status'",
        'description' => 'Array key: durum → status',
    ],
    [
        'search' => '"durum"',
        'replace' => '"status"',
        'description' => 'Array key: durum → status (double quote)',
    ],

    // Status value patterns (sadece string değerler)
    [
        'search' => "'status' => 'Aktif'",
        'replace' => "'status' => 'active'",
        'description' => 'Status value: Aktif → active',
    ],
    [
        'search' => "'status' => 'Pasif'",
        'replace' => "'status' => 'inactive'",
        'description' => 'Status value: Pasif → inactive',
    ],
];

$directories = [
    'app/Http/Controllers',
    'app/Models',
    'app/Services',
    'resources/views',
];

$totalFiles = 0;
$totalChanges = 0;
$fileChanges = [];

foreach ($directories as $dir) {
    $path = $basePath.'/'.$dir;
    if (! is_dir($path)) {
        continue;
    }

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($files as $file) {
        if ($file->isDir()) {
            continue;
        }

        $ext = $file->getExtension();
        if (! in_array($ext, ['php', 'blade.php'])) {
            continue;
        }

        $filepath = $file->getPathname();
        $content = file_get_contents($filepath);
        $originalContent = $content;
        $changes = 0;

        foreach ($patterns as $pattern) {
            $count = 0;
            $content = str_replace($pattern['search'], $pattern['replace'], $content, $count);
            if ($count > 0) {
                $changes += $count;
                echo "  ✓ {$pattern['description']}: {$count}x\n";
            }
        }

        if ($content !== $originalContent) {
            file_put_contents($filepath, $content);
            $totalFiles++;
            $totalChanges += $changes;
            $fileChanges[] = [
                'file' => str_replace($basePath.'/', '', $filepath),
                'changes' => $changes,
            ];
            echo '✅ '.basename($filepath)." ({$changes} changes)\n";
        }
    }
}

echo "\n📊 CONTEXT7 AUTO-FIX SUMMARY:\n";
echo "═══════════════════════════════════\n";
echo "Files modified: {$totalFiles}\n";
echo "Total changes: {$totalChanges}\n";
echo "\n📝 Changed files:\n";
foreach ($fileChanges as $change) {
    echo "  - {$change['file']} ({$change['changes']})\n";
}

echo "\n🎉 Context7 compliance improved!\n";
echo "Run: php artisan context7:check to verify\n";
