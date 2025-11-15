#!/usr/bin/env php
<?php
/**
 * Duplicate Method Checker
 * Yalıhan Bekçi - 2 Kasım 2025
 *
 * PHP dosyalarında duplicate method tanımlarını tespit eder.
 */

echo "🔍 Duplicate method kontrolü başlıyor...\n";

// Git'te staged PHP dosyalarını al
exec('git diff --cached --name-only --diff-filter=ACM | grep "\.php$"', $files);

if (empty($files)) {
    echo "✅ Kontrol edilecek PHP dosyası yok.\n";
    exit(0);
}

$hasError = false;
$totalDuplicates = 0;

foreach ($files as $file) {
    if (!file_exists($file)) {
        continue;
    }

    $content = file_get_contents($file);

    // Public/protected/private method tanımlarını bul
    preg_match_all(
        '/^\s*(public|protected|private)\s+(?:static\s+)?function\s+(\w+)\s*\(/m',
        $content,
        $matches,
        PREG_SET_ORDER
    );

    if (empty($matches)) {
        continue;
    }

    // Method isimlerini say
    $methodCounts = [];
    $methodLines = [];

    foreach ($matches as $match) {
        $methodName = $match[2];

        if (!isset($methodCounts[$methodName])) {
            $methodCounts[$methodName] = 0;
            $methodLines[$methodName] = [];
        }

        $methodCounts[$methodName]++;

        // Satır numarasını bul
        $beforeMatch = substr($content, 0, strpos($content, $match[0]));
        $lineNumber = substr_count($beforeMatch, "\n") + 1;
        $methodLines[$methodName][] = $lineNumber;
    }

    // Duplicate'leri tespit et
    foreach ($methodCounts as $methodName => $count) {
        if ($count > 1) {
            echo "❌ DUPLICATE METHOD: $file\n";
            echo "   Method: {$methodName}() - {$count} kez tanımlanmış\n";
            echo "   Satırlar: " . implode(', ', $methodLines[$methodName]) . "\n";
            echo "\n";

            $hasError = true;
            $totalDuplicates++;
        }
    }
}

if ($hasError) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "❌ HATA: {$totalDuplicates} duplicate method bulundu!\n";
    echo "\n";
    echo "Düzeltme:\n";
    echo "1. Dosyayı aç\n";
    echo "2. grep -n 'public function methodName' File.php\n";
    echo "3. Eski method'u SİL, yeni method'u BIRAK\n";
    echo "4. git add File.php\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    exit(1);
}

echo "✅ Duplicate method bulunamadı.\n";
exit(0);
