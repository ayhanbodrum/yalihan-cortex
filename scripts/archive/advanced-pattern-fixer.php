<?php

/**
 * Advanced Pattern Fixer - Yaygın syntax hatalarını düzelten gelişmiş script
 */

$migrationsDir = __DIR__ . '/../database/migrations';
$fixedFiles = [];
$errorFiles = [];

echo "🚀 Advanced Pattern Fixer başlatılıyor...\n";

foreach (glob($migrationsDir . '/*.php') as $filePath) {
    $filename = basename($filePath);
    echo "🔍 Kontrol ediliyor: $filename\n";

    $content = file_get_contents($filePath);
    $originalContent = $content;

    // 1. Fix malformed down() functions - single line format
    $content = preg_replace(
        '/public function down\(\)\s*:\s*void\s*\{\s*([^}]+);\s*\};/',
        "public function down(): void\n    {\n        \$1;\n    }\n};",
        $content
    );

    // 2. Fix unexpected token ";" - incomplete class structure
    $content = preg_replace(
        '/(return new class extends Migration\s*\{\s*)(public function up\(\)[^}]*\}\s*)(public function down\(\)[^}]*\}\s*);/',
        '$1$2$3}',
        $content
    );

    // 3. Fix missing closing braces after up() function
    $content = preg_replace(
        '/(public function up\(\)[^{]*\{[^}]*\}\s*)(public function down\(\))/s',
        '$1' . "\n\n    " . '$2',
        $content
    );

    // 4. Fix malformed single-line functions
    $content = preg_replace(
        '/public function (up|down)\(\)\s*:\s*void\s*\{\s*([^}]+)\s*\};/',
        "public function \$1(): void\n    {\n        \$2\n    }",
        $content
    );

    // 5. Ensure proper class ending
    if (preg_match('/return new class extends Migration/', $content)) {
        // Remove multiple closing braces/semicolons at end
        $content = preg_replace('/\}[\s}]*;[\s}]*$/', '};', $content);

        // Add missing closing brace if needed
        if (!preg_match('/\};\s*$/', $content)) {
            $content = rtrim($content) . "\n};";
        }
    }

    // 6. Fix unexpected token "public" - missing closing brace for up()
    $content = preg_replace(
        '/(public function up\(\)[^{]*\{[^}]*)\s+(public function down\(\))/s',
        '$1' . "\n    }\n\n    " . '$2',
        $content
    );

    // 7. Clean up extra whitespace and normalize formatting
    $content = preg_replace('/\n{3,}/', "\n\n", $content);
    $content = preg_replace('/\s+\};\s*$/', "\n};", $content);

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
    foreach (array_slice($fixedFiles, 0, 10) as $file) {
        echo "  - $file\n";
    }
    if (count($fixedFiles) > 10) {
        echo "  ... ve " . (count($fixedFiles) - 10) . " dosya daha\n";
    }
}

echo "\n🎉 Advanced Pattern Fixer tamamlandı!\n";
