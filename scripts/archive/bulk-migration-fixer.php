<?php

/**
 * Bulk Migration Syntax Fixer - Multiple patterns için comprehensive automated fix
 */

echo "🚀 Bulk Migration Syntax Fixer - Automated Learning Final Push!\n";
echo "🎯 Kalan syntax hatalarını pattern matching ile toplu çözüyoruz...\n\n";

$migrationsDir = __DIR__ . '/../database/migrations';
$fixedCount = 0;
$patternsFixed = [];

function fixSchemaCallbackClosures($content) {
    // Pattern 1: Schema callback eksik })
    $content = preg_replace(
        '/(\s*Schema::(create|table)\([^)]+function[^{]*\{[^}]+)\s*}\s*$/m',
        '$1    });',
        $content
    );

    // Pattern 2: if (!Schema::hasTable) eksik }
    $content = preg_replace(
        '/(if\s*\(\s*!Schema::hasTable[^{]+\{[^}]+\}\s*);/s',
        '$1',
        $content
    );

    return $content;
}

function fixFunctionSpacing($content) {
    // Pattern: } ile public function arasında boş satır eksik
    $content = preg_replace(
        '/(\s*}\s*)(public function)/m',
        '$1' . "\n" . '    $2',
        $content
    );

    return $content;
}

function fixUnexpectedTokens($content) {
    // Pattern: unexpected token ";" expecting "function"
    $content = preg_replace(
        '/(\$table->[^;]+;)\s*;\s*(public function)/m',
        '$1' . "\n    }" . "\n\n    " . '$2',
        $content
    );

    return $content;
}

foreach (glob($migrationsDir . '/*.php') as $filePath) {
    $filename = basename($filePath);

    $syntaxCheck = shell_exec("php -l " . escapeshellarg($filePath) . " 2>&1");
    if (strpos($syntaxCheck, 'No syntax errors') !== false) continue;

    echo "🔧 $filename -> ";

    $originalContent = file_get_contents($filePath);
    $content = $originalContent;

    // Apply all fix patterns
    $content = fixSchemaCallbackClosures($content);
    $content = fixFunctionSpacing($content);
    $content = fixUnexpectedTokens($content);

    // Indentation fix
    $lines = explode("\n", $content);
    for ($i = 0; $i < count($lines); $i++) {
        $line = $lines[$i];

        // Schema içi indentation düzelt
        if (preg_match('/^\s*\$table-/', $line) && !preg_match('/^\s{8,}/', $line)) {
            $lines[$i] = '            ' . ltrim($line);
        }

        // Schema callback closure indentation
        if (preg_match('/^\s*}\);?\s*$/', $line) && !preg_match('/^\s{8,}/', $line)) {
            $lines[$i] = '        });';
        }

        // Function indentation
        if (preg_match('/^\s*public function/', $line) && !preg_match('/^\s{4}public/', $line)) {
            $lines[$i] = '    ' . ltrim($line);
        }
    }

    $newContent = implode("\n", $lines);

    if ($newContent !== $originalContent) {
        if (file_put_contents($filePath, $newContent)) {
            // Verify fix
            $verifyCheck = shell_exec("php -l " . escapeshellarg($filePath) . " 2>&1");
            if (strpos($verifyCheck, 'No syntax errors') !== false) {
                echo "✅ FIXED!\n";
                $fixedCount++;
                $patternsFixed[] = $filename;
            } else {
                echo "⚠️ Partial fix, errors remain\n";
            }
        } else {
            echo "❌ Write failed\n";
        }
    } else {
        echo "⏭️ No changes needed\n";
    }
}

echo "\n📊 Bulk Fixer Sonuçları:\n";
echo "🔧 Düzeltilen dosyalar: $fixedCount\n";
if ($fixedCount > 0) {
    echo "📁 Düzeltilen dosyalar:\n";
    foreach ($patternsFixed as $file) {
        echo "   ✅ $file\n";
    }
}

// Final error count
$finalErrors = (int)shell_exec("find " . escapeshellarg($migrationsDir) . " -name '*.php' -exec php -l {} \\; 2>&1 | grep -c 'Parse error\\|Fatal error\\|syntax error' || echo '0'");
$improvement = 192 - $finalErrors;

echo "\n📈 İstatistikler:\n";
echo "⚠️ Önceki hata sayısı: 192\n";
echo "⚠️ Güncel hata sayısı: $finalErrors\n";
echo "📈 Bu session'da düzeltilen: $improvement\n";

if ($finalErrors === 0) {
    echo "\n🎉🎉🎉 TÜM SYNTAX HATALARI ÇÖZÜLDÜ! 🎉🎉🎉\n";
    echo "🚀 AUTOMATED LEARNING SİSTEMİ BAŞARIYLA TAMAMLANDI!\n";
    echo "🎯 376 hatadan 0 hataya! 100% başarı!\n";
} elseif ($improvement > 0) {
    echo "\n✅ Automated learning devam ediyor! Az kaldı...\n";
}

echo "\n🚀 Bulk Migration Syntax Fixer tamamlandı!\n";
