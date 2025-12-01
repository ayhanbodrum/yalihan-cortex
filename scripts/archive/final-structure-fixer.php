<?php

/**
 * Final Migration Structure Fixer - Kalan yaygın syntax hatalarını düzelten script
 */
$migrationsDir = __DIR__.'/../database/migrations';
$fixedCount = 0;
$totalChecked = 0;

echo "🔧 Final Migration Structure Fixer başlatılıyor...\n";

foreach (glob($migrationsDir.'/*.php') as $filePath) {
    $filename = basename($filePath);
    $totalChecked++;

    $content = file_get_contents($filePath);
    $originalContent = $content;

    echo "🔍 Kontrol: $filename ";

    // Pattern 1: Missing } before }; at end - down() function not closed
    if (preg_match('/public function down\(\)[^}]*\{[^}]*\};$/s', $content)) {
        $content = preg_replace(
            '/(public function down\(\)[^}]*\{[^}]*)\};$/s',
            '$1'."\n    }\n};",
            $content
        );
        echo '✅ Down() closing fix ';
    }

    // Pattern 2: Missing } for up() function when down() exists
    if (preg_match('/public function up\(\)[^}]*\{[^}]*\n\s*public function down/s', $content)) {
        $content = preg_replace(
            '/(public function up\(\)[^}]*\{[^}]*)\n(\s*public function down)/s',
            '$1'."\n    }\n\n$2",
            $content
        );
        echo '✅ Up() closing fix ';
    }

    // Pattern 3: Class ending issues - no }; at end
    if (! preg_match('/\};\s*$/', $content) && strpos($content, 'return new class extends Migration') !== false) {
        $content = rtrim($content)."\n};";
        echo '✅ Class ending fix ';
    }

    // Pattern 4: Extra empty lines and formatting
    $content = preg_replace('/\n{3,}/', "\n\n", $content);

    // Pattern 5: Fix malformed class structure
    $content = preg_replace(
        '/(return new class extends Migration\s*\{\s*)(public function up\(\)[^}]*\}\s*)(public function down\(\)[^}]*\}\s*);/',
        '$1$2$3}',
        $content
    );

    if ($content !== $originalContent) {
        if (file_put_contents($filePath, $content)) {
            $fixedCount++;
            echo " → DÜZELTILDI\n";
        } else {
            echo " → HATA!\n";
        }
    } else {
        echo " → Temiz\n";
    }
}

echo "\n📊 Final Structure Fixer Özeti:\n";
echo "📁 Toplam kontrol edilen: $totalChecked\n";
echo "✅ Düzeltilen dosyalar: $fixedCount\n";

// Final syntax check
echo "\n🔍 Final syntax kontrolü...\n";
$syntaxErrors = shell_exec('find '.escapeshellarg($migrationsDir)." -name '*.php' -exec php -l {} \\; 2>&1 | grep -c 'Parse error\\|Fatal error\\|syntax error' || echo '0'");
echo '🎯 Kalan syntax hataları: '.trim($syntaxErrors)."\n";

if (trim($syntaxErrors) == '0') {
    echo "🎉 TÜM SYNTAX HATALARI DÜZELTİLDİ!\n";
} else {
    echo '⚠️  Hâlâ '.trim($syntaxErrors)." syntax hatası mevcut.\n";
}

echo "\n🏁 Final Migration Structure Fixer tamamlandı!\n";
