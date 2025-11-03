<?php

/**
 * Ultra Migration Fixer - En karmaşık syntax hatalarını düzelten son çare script
 */

$migrationsDir = __DIR__ . '/../database/migrations';
$fixedCount = 0;
$totalChecked = 0;

echo "🚨 Ultra Migration Fixer başlatılıyor...\n";

foreach (glob($migrationsDir . '/*.php') as $filePath) {
    $filename = basename($filePath);
    $totalChecked++;

    // İlk syntax check
    $syntaxCheck = shell_exec("php -l " . escapeshellarg($filePath) . " 2>&1");
    if (strpos($syntaxCheck, 'No syntax errors') !== false) {
        continue; // Bu dosya temiz
    }

    echo "⚡ $filename ";

    $content = file_get_contents($filePath);
    $originalContent = $content;

    // Step 1: Completely reformat malformed down() functions
    $content = preg_replace(
        '/public function down\(\)\s*:\s*void\s*\{\s*\/\/[^}]*\}\s*\}\s*\};?/',
        "public function down(): void\n    {\n        // Bu migrationda yapılacak bir işlem yok\n    }\n};",
        $content
    );

    // Step 2: Fix multiple closing braces issues
    $content = preg_replace('/\}\s*\}\s*\}\s*;?/', "}\n};", $content);
    $content = preg_replace('/\}\s*\}\s*;/', "}\n};", $content);

    // Step 3: Fix specific patterns like "public function down() : void { comment }"
    $content = preg_replace(
        '/public function down\(\)\s*:\s*void\s*\{\s*\/\/[^}]*\}\s*\}/',
        "public function down(): void\n    {\n        // Bu migrationda yapılacak bir işlem yok\n    }",
        $content
    );

    // Step 4: Clean up weird spacing and double functions
    $content = preg_replace('/\n{3,}/', "\n\n", $content);

    // Step 5: Ensure proper class structure
    if (strpos($content, 'return new class extends Migration') !== false) {
        // Make sure it ends properly
        if (!preg_match('/\};\s*$/', $content)) {
            $content = rtrim($content) . "\n};";
        }

        // Fix cases where class doesn't close properly
        $lines = explode("\n", $content);
        $inClass = false;
        $braceCount = 0;
        $newLines = [];

        foreach ($lines as $line) {
            if (strpos($line, 'return new class extends Migration') !== false) {
                $inClass = true;
                $braceCount = 0;
            }

            if ($inClass) {
                $braceCount += substr_count($line, '{') - substr_count($line, '}');
            }

            $newLines[] = $line;
        }

        // If we're still in class and brace count is wrong, fix it
        if ($inClass && $braceCount > 0) {
            // Remove any existing problematic ending
            while (count($newLines) > 0 && trim(end($newLines)) === '') {
                array_pop($newLines);
            }
            if (count($newLines) > 0 && strpos(end($newLines), '};') !== false) {
                array_pop($newLines);
            }
            $newLines[] = '};';
        }

        $content = implode("\n", $newLines);
    }

    // Step 6: Final cleanup - remove weird patterns
    $content = preg_replace('/\s*\}\s*\}\s*\};/', "\n    }\n};", $content);

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

echo "\n📊 Ultra Migration Fixer Özeti:\n";
echo "📁 Toplam kontrol edilen: $totalChecked\n";
echo "✅ Düzeltilen dosyalar: $fixedCount\n";

// Final syntax check
echo "\n🔍 Final syntax kontrolü...\n";
$syntaxErrors = shell_exec("find " . escapeshellarg($migrationsDir) . " -name '*.php' -exec php -l {} \\; 2>&1 | grep -c 'Parse error\\|Fatal error\\|syntax error' || echo '0'");
echo "🎯 Kalan syntax hataları: " . trim($syntaxErrors) . "\n";

if (trim($syntaxErrors) == '0') {
    echo "🎉🎉🎉 TÜM SYNTAX HATALARI DÜZELTİLDİ! 🎉🎉🎉\n";
} else {
    echo "⚠️ Hâlâ " . trim($syntaxErrors) . " syntax hatası mevcut.\n";

    // Show a few remaining examples
    echo "\n🔍 Kalan hata örnekleri:\n";
    $examples = shell_exec("find " . escapeshellarg($migrationsDir) . " -name '*.php' -exec php -l {} \\; 2>&1 | grep -A1 'Parse error\\|Fatal error\\|syntax error' | head -10");
    echo $examples . "\n";
}

echo "\n🚨 Ultra Migration Fixer tamamlandı!\n";
