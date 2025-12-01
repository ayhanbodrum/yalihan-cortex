<?php

/**
 * Emergency Function Closer - Down() function closing eksikliklerini düzelten acil script
 */
$migrationsDir = __DIR__.'/../database/migrations';
$fixedCount = 0;
$totalChecked = 0;

echo "🚑 Emergency Function Closer başlatılıyor...\n";

foreach (glob($migrationsDir.'/*.php') as $filePath) {
    $filename = basename($filePath);
    $totalChecked++;

    // İlk syntax check
    $syntaxCheck = shell_exec('php -l '.escapeshellarg($filePath).' 2>&1');
    if (strpos($syntaxCheck, 'No syntax errors') !== false) {
        continue; // Bu dosya temiz
    }

    // Sadece 'expecting "function"' hatası olanları al
    if (strpos($syntaxCheck, 'expecting "function"') === false) {
        continue;
    }

    echo "🚑 $filename ";

    $content = file_get_contents($filePath);
    $originalContent = $content;

    // Bu specific pattern'i düzelt:
    // public function down(): void
    // {
    //     // comment
    // };  <- Bu '}' değil '}' olmalı

    $content = preg_replace(
        '/public function down\(\)\s*:\s*void\s*\{([^}]*)\};/',
        "public function down(): void\n    {\$1\n    }\n};",
        $content
    );

    // İkinci pattern:
    // public function down(): void
    // {
    //     // comment <- kapanış eksik
    // };

    $lines = explode("\n", $content);
    $newLines = [];
    $inDownFunction = false;
    $braceCount = 0;

    for ($i = 0; $i < count($lines); $i++) {
        $line = $lines[$i];

        // down() function başlangıcını tespit et
        if (preg_match('/public function down\(\)/', $line)) {
            $inDownFunction = true;
            $braceCount = 0;
        }

        if ($inDownFunction) {
            $braceCount += substr_count($line, '{') - substr_count($line, '}');

            // Eğer brace count hâlâ pozitifse ve }; ile bitiyorsa
            if ($braceCount > 0 && trim($line) === '};') {
                // Missing } ekle
                $newLines[] = rtrim($lines[$i - 1])."\n    }\n};";
                $inDownFunction = false;

                continue;
            }
        }

        $newLines[] = $line;

        if ($inDownFunction && $braceCount === 0 && strpos($line, '}') !== false) {
            $inDownFunction = false;
        }
    }

    $content = implode("\n", $newLines);

    if ($content !== $originalContent) {
        if (file_put_contents($filePath, $content)) {
            $fixedCount++;
            echo "✅ DÜZELTILDI\n";
        } else {
            echo "❌ HATA\n";
        }
    } else {
        echo "⏭️ Değişiklik yok\n";
    }
}

echo "\n📊 Emergency Function Closer Özeti:\n";
echo "📁 Toplam kontrol edilen: $totalChecked\n";
echo "✅ Düzeltilen dosyalar: $fixedCount\n";

// Final syntax check
echo "\n🔍 Final syntax kontrolü...\n";
$syntaxErrors = shell_exec('find '.escapeshellarg($migrationsDir)." -name '*.php' -exec php -l {} \\; 2>&1 | grep -c 'Parse error\\|Fatal error\\|syntax error' || echo '0'");
echo '🎯 Kalan syntax hataları: '.trim($syntaxErrors)."\n";

if (trim($syntaxErrors) == '0') {
    echo "🎉🎉🎉 TÜM SYNTAX HATALARI DÜZELTİLDİ! 🎉🎉🎉\n";
} else {
    echo '⚠️ Hâlâ '.trim($syntaxErrors)." syntax hatası mevcut.\n";
}

echo "\n🚑 Emergency Function Closer tamamlandı!\n";
