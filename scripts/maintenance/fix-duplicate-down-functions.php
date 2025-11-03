<?php

/**
 * Duplicate down() function'larını temizlemek için script
 */

$migrationsDir = __DIR__ . '/../database/migrations';
$fixedFiles = [];
$errorFiles = [];

echo "🚀 Duplicate down() function temizleyici başlatılıyor...\n";

foreach (glob($migrationsDir . '/*.php') as $filePath) {
    $filename = basename($filePath);
    echo "🔍 Kontrol ediliyor: $filename\n";

    $content = file_get_contents($filePath);
    $originalContent = $content;

    // Duplicate down() function'larını temizle
    $content = fixDuplicateDownFunctions($content);

    if ($content !== $originalContent) {
        if (file_put_contents($filePath, $content)) {
            $fixedFiles[] = $filename;
            echo "✅ Düzeltildi: $filename\n";
        } else {
            $errorFiles[] = $filename;
            echo "❌ Hata: $filename\n";
        }
    } else {
        echo "✨ Zaten temiz: $filename\n";
    }
}

echo "\n📊 Özet Rapor:\n";
echo "✅ Düzeltilen dosyalar: " . count($fixedFiles) . "\n";
echo "❌ Hata alan dosyalar: " . count($errorFiles) . "\n";

if (!empty($fixedFiles)) {
    echo "\n🔧 Düzeltilen dosyalar:\n";
    foreach ($fixedFiles as $file) {
        echo "  - $file\n";
    }
}

echo "\n🎉 Duplicate down() function temizleyici tamamlandı!\n";

function fixDuplicateDownFunctions($content)
{
    // Pattern 1: Birden fazla down() function'ı var mı kontrol et
    $pattern = '/public function down\(\)\s*:\s*void\s*\{[^}]*\}/s';
    preg_match_all($pattern, $content, $matches);

    if (count($matches[0]) > 1) {
        // İlk down() function'ı bul ve koru
        $firstDownFunction = $matches[0][0];

        // Tüm down() function'larını kaldır
        $content = preg_replace($pattern, '', $content);

        // İlk down() function'ı class sonuna ekle
        $content = preg_replace(
            '/(\s*)\};?\s*$/',
            "\n\n    " . $firstDownFunction . "\n};",
            $content
        );
    }

    // Pattern 2: Boş down() function'ları düzelt
    $content = preg_replace(
        '/public function down\(\)\s*:\s*void\s*\{\s*\}/',
        'public function down(): void
    {
        // Bu migrationda yapılacak bir işlem yok (otomatik temizlik sonrası boş kaldı)
    }',
        $content
    );

    return $content;
}
