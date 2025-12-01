<?php

/**
 * NEO DESIGN CLEANER
 *
 * Context7 Standardına uygun Neo Design kullanımlarını Tailwind CSS'e çevirir
 *
 * Tarih: 2025-11-01
 * Amaç: neo-btn, neo-card, neo-input kullanımlarını Tailwind ile değiştirmek
 */
$replacements = [
    // BUTTON REPLACEMENTS
    'neo-btn neo-btn-primary' => 'inline-flex items-center px-6 py-3 bg-orange-600 text-white font-semibold rounded-lg shadow-md hover:bg-orange-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-orange-500 focus:outline-none transition-all duration-200',

    'neo-btn neo-btn-secondary' => 'inline-flex items-center px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg shadow-md hover:bg-gray-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-gray-500 focus:outline-none transition-all duration-200',

    'neo-btn neo-btn-outline-secondary' => 'inline-flex items-center px-6 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 font-semibold rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 focus:outline-none transition-all duration-200',

    // CARD REPLACEMENTS
    'neo-card' => 'bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200',

    'neo-card-header' => 'px-6 py-4 border-b border-gray-200 dark:border-gray-700',

    'neo-card-body' => 'px-6 py-4',

    'neo-card-title' => 'text-lg font-bold text-gray-900 dark:text-white',

    // INPUT REPLACEMENTS
    'neo-input' => 'w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200',

    // GRID REPLACEMENTS
    'neo-grid' => 'grid',

    // AVATAR REPLACEMENTS
    'neo-avatar neo-avatar-sm' => 'w-10 h-10 rounded-full flex items-center justify-center',

    // PAGE HEADER
    'neo-page-header' => 'mb-8',

    // CONTENT
    'neo-content' => 'p-6',
];

$files = [
    'resources/views/admin/crm/dashboard.blade.php',
    'resources/views/admin/crm/index.blade.php',
    'resources/views/admin/crm/customers/index.blade.php',
    'resources/views/admin/crm/dashboard-cards.blade.php',
    'resources/views/admin/crm/dashboard-minimal.blade.php',
    'resources/views/admin/notifications/index.blade.php',
    'resources/views/admin/notifications/show.blade.php',
    'resources/views/admin/takim-yonetimi/gorevler/index.blade.php',
    'resources/views/admin/takim-yonetimi/gorevler/edit.blade.php',
    'resources/views/admin/takim-yonetimi/gorevler/show.blade.php',
    'resources/views/admin/takim-yonetimi/gorevler/raporlar.blade.php',
    'resources/views/admin/takim-yonetimi/takim/index.blade.php',
    'resources/views/admin/takim-yonetimi/takim/edit.blade.php',
    'resources/views/admin/takim-yonetimi/takim/performans.blade.php',
    'resources/views/admin/takim-yonetimi/takim/takim-performans.blade.php',
    'resources/views/admin/takim-yonetimi/takim/show.blade.php',
];

$totalFixed = 0;
$report = [];

echo "🔧 NEO DESIGN CLEANER - Context7 Standardı\n";
echo str_repeat('=', 60)."\n\n";

foreach ($files as $file) {
    if (! file_exists($file)) {
        echo "⚠️  Dosya bulunamadı: $file\n";

        continue;
    }

    $content = file_get_contents($file);
    $originalContent = $content;
    $fileFixed = 0;

    foreach ($replacements as $search => $replace) {
        $count = 0;
        $content = str_replace($search, $replace, $content, $count);
        $fileFixed += $count;
        $totalFixed += $count;
    }

    if ($fileFixed > 0) {
        file_put_contents($file, $content);
        $filename = basename($file);
        echo "✅ $filename - $fileFixed düzeltme\n";

        $report[] = [
            'file' => $file,
            'fixes' => $fileFixed,
        ];
    }
}

echo "\n".str_repeat('=', 60)."\n";
echo "📊 ÖZET:\n";
echo "  • Toplam Düzeltme: $totalFixed\n";
echo '  • İşlenen Dosya: '.count($report)."\n";
echo "\n";

// Context7 Authority'ye raporla
$authorityFile = '.context7/authority.json';
if (file_exists($authorityFile)) {
    $authority = json_decode(file_get_contents($authorityFile), true);
    $authority['neo_design_cleanup_2025_11_01'] = [
        'date' => date('Y-m-d H:i:s'),
        'total_fixes' => $totalFixed,
        'files' => count($report),
        'status' => 'COMPLETED',
    ];
    file_put_contents($authorityFile, json_encode($authority, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "✅ Context7 Authority güncellendi\n";
}

echo "\n🎉 NEO DESIGN TEMİZLİĞİ TAMAMLANDI!\n";
