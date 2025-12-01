<?php

/**
 * Yarım Kalmış Kod Analizi Scripti
 *
 * Bulur:
 * - TODO/FIXME/HACK yorumları
 * - Boş metodlar (stub)
 * - Devre dışı bırakılmış kodlar
 * - Kullanılmayan route'lar
 * - Yorum satırına alınmış kod blokları
 */
$basePath = __DIR__.'/../';
$patterns = [
    'todos' => [
        'pattern' => '/(TODO|FIXME|HACK|XXX|NOTE|@deprecated)/i',
        'name' => 'TODO/FIXME Yorumları',
    ],
    'disabled_routes' => [
        'pattern' => '/\/\/.*(Route|route).*(disabled|DISABLED|TEMPORARILY)/i',
        'name' => 'Devre Dışı Route\'lar',
    ],
    'empty_methods' => [
        'pattern' => '/function\s+\w+\s*\([^)]*\)\s*\{[\s]*\}/',
        'name' => 'Boş Metodlar',
    ],
    'commented_code' => [
        'pattern' => '/\/\*[\s\S]*?\*\//',
        'name' => 'Yorum Satırına Alınmış Kod',
    ],
    'stub_methods' => [
        'pattern' => '/function\s+\w+\s*\([^)]*\)\s*\{[\s]*(return null;|return;|throw|\/\/)/',
        'name' => 'Stub Metodlar (Yarım Kalmış)',
    ],
];

$results = [];
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($basePath.'app'),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        $relativePath = str_replace($basePath, '', $file->getPathname());

        foreach ($patterns as $key => $pattern) {
            if (preg_match_all($pattern['pattern'], $content, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches[0] as $match) {
                    $line = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                    $results[$key][] = [
                        'file' => $relativePath,
                        'line' => $line,
                        'match' => trim(substr($match[0], 0, 100)),
                    ];
                }
            }
        }
    }
}

// Route dosyalarını kontrol et
$routeFiles = [
    'routes/web.php',
    'routes/api.php',
    'routes/admin.php',
];

foreach ($routeFiles as $routeFile) {
    $filePath = $basePath.$routeFile;
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        if (preg_match_all('/\/\/.*(Route|route).*(disabled|DISABLED|TEMPORARILY)/i', $content, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $match) {
                $line = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                $results['disabled_routes'][] = [
                    'file' => $routeFile,
                    'line' => $line,
                    'match' => trim(substr($match[0], 0, 100)),
                ];
            }
        }
    }
}

// Sonuçları göster
echo "📊 YARIM KALMIŞ KOD ANALİZİ\n";
echo "==========================\n\n";

foreach ($patterns as $key => $pattern) {
    if (isset($results[$key]) && count($results[$key]) > 0) {
        echo "📋 {$pattern['name']}: ".count($results[$key])." adet\n";
        foreach (array_slice($results[$key], 0, 10) as $item) {
            echo "   - {$item['file']}:{$item['line']} - {$item['match']}\n";
        }
        if (count($results[$key]) > 10) {
            echo '   ... ve '.(count($results[$key]) - 10)." adet daha\n";
        }
        echo "\n";
    }
}

// JSON çıktısı
file_put_contents(
    $basePath.'.yalihan-bekci/reports/incomplete-code-analysis-'.date('Y-m-d').'.json',
    json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
);

echo '✅ Analiz tamamlandı. Rapor: .yalihan-bekci/reports/incomplete-code-analysis-'.date('Y-m-d').".json\n";
