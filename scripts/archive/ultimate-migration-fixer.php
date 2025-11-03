<?php

/**
 * Ultimate Migration Fixer - Tüm migration syntax hatalarını çözen kapsamlı script
 */

$migrationsDir = __DIR__ . '/../database/migrations';
$fixedFiles = [];
$errorFiles = [];

echo "🚀 Ultimate Migration Fixer başlatılıyor...\n";

foreach (glob($migrationsDir . '/*.php') as $filePath) {
    $filename = basename($filePath);
    echo "🔍 Kontrol ediliyor: $filename\n";

    $content = file_get_contents($filePath);
    $originalContent = $content;

    // 1. Duplicate down() function'larını temizle
    $content = fixDuplicateDownFunctions($content);

    // 2. Class structure sorunlarını düzelt
    $content = fixClassStructure($content);

    // 3. Function syntax sorunlarını düzelt
    $content = fixFunctionSyntax($content);

    // 4. Unmatched braces sorunlarını düzelt
    $content = fixUnmatchedBraces($content);

    // 5. Missing function wrappers ekle
    $content = addMissingFunctionWrappers($content);

    // 6. Final cleanup
    $content = finalCleanup($content);

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
    foreach (array_slice($fixedFiles, 0, 20) as $file) {
        echo "  - $file\n";
    }
    if (count($fixedFiles) > 20) {
        echo "  ... ve " . (count($fixedFiles) - 20) . " dosya daha\n";
    }
}

echo "\n🎉 Ultimate Migration Fixer tamamlandı!\n";

function fixDuplicateDownFunctions($content)
{
    // Birden fazla down() function varsa, ilkini koru diğerlerini sil
    $pattern = '/public function down\(\)\s*:\s*void\s*\{[^}]*\}/s';
    preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE);

    if (count($matches[0]) > 1) {
        // En son match'ten başlayarak geriye doğru sil (offset'ler bozulmasın)
        for ($i = count($matches[0]) - 1; $i > 0; $i--) {
            $match = $matches[0][$i];
            $content = substr_replace($content, '', $match[1], strlen($match[0]));
        }
    }

    return $content;
}

function fixClassStructure($content)
{
    // Missing opening brace for anonymous class
    $content = preg_replace(
        '/(return new class extends Migration)\s*\n(?!\s*\{)/',
        '$1' . "\n" . '{',
        $content
    );

    // Fix "unexpected fully qualified name" - missing opening brace
    $content = preg_replace(
        '/(return new class extends Migration)\s*\\\\n/',
        '$1' . "\n" . '{',
        $content
    );

    // Fix unexpected public after class declaration
    $content = preg_replace(
        '/(class[^{]*extends[^{]*Migration)\s*(public function)/',
        '$1' . "\n" . '{' . "\n" . '    $2',
        $content
    );

    return $content;
}

function fixFunctionSyntax($content)
{
    // Fix incomplete function declarations
    $content = preg_replace(
        '/public function (up|down)\(\)\s*\{\s*\/\/[^\n]*\n(?!\s*[{}]|\s*Schema|\s*DB|\s*if|\s*try|\s*return)/',
        'public function $1(): void' . "\n" . '    {' . "\n" . '        // Migration content goes here' . "\n" . '    }' . "\n\n" . '    ',
        $content
    );

    // Fix missing return types
    $content = preg_replace(
        '/public function (up|down)\(\)\s*\{/',
        'public function $1(): void' . "\n" . '    {',
        $content
    );

    // Fix unexpected token public - add newlines
    $content = preg_replace(
        '/(\})\s*(public function)/',
        '$1' . "\n\n" . '    $2',
        $content
    );

    return $content;
}

function fixUnmatchedBraces($content)
{
    // Remove extra closing braces at end
    $content = preg_replace('/\}\s*\}\s*;\s*$/', '};', $content);
    $content = preg_replace('/\}\s*\};\s*$/', '};', $content);

    // Fix unmatched single closing braces
    $content = preg_replace('/(\}\s*)\}(\s*$)/', '$1$2', $content);

    return $content;
}

function addMissingFunctionWrappers($content)
{
    // Fix unexpected variable - wrap in function
    $content = preg_replace(
        '/(class[^{]*\{)\s*(\$table)/',
        '$1' . "\n" . '    public function up(): void' . "\n" . '    {' . "\n" . '        Schema::table(\'table_name\', function (Blueprint $2) {',
        $content
    );

    // Fix unexpected if/catch/else - wrap in function
    $content = preg_replace(
        '/(class[^{]*\{)\s*(if|catch|else|try)/',
        '$1' . "\n" . '    public function up(): void' . "\n" . '    {' . "\n" . '        $2',
        $content
    );

    // Fix unexpected token ";" - likely incomplete statement
    $content = preg_replace(
        '/(class[^{]*\{)\s*;\s*(public|$)/',
        '$1' . "\n" . '    public function up(): void' . "\n" . '    {' . "\n" . '        // Migration content goes here' . "\n" . '    }' . "\n\n" . '    $2',
        $content
    );

    return $content;
}

function finalCleanup($content)
{
    // Ensure proper class ending
    if (!preg_match('/\};\s*$/', $content) && preg_match('/return new class extends Migration/', $content)) {
        $content = rtrim($content);
        if (!preg_match('/\}\s*$/', $content)) {
            $content .= "\n};";
        } else {
            $content = preg_replace('/\}\s*$/', "\n};", $content);
        }
    }

    // Add missing down() function if not exists
    if (preg_match('/public function up\(\)/', $content) && !preg_match('/public function down\(\)/', $content)) {
        $content = preg_replace(
            '/(\}\s*);?\s*$/',
            "\n\n" . '    public function down(): void' . "\n" . '    {' . "\n" . '        // Bu migrationda yapılacak bir işlem yok (otomatik temizlik sonrası boş kaldı)' . "\n" . '    }' . "\n" . '};',
            $content
        );
    }

    // Clean up extra whitespace
    $content = preg_replace('/\n{3,}/', "\n\n", $content);

    return $content;
}
