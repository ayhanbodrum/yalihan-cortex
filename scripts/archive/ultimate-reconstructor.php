<?php

/**
 * Ultimate Migration Reconstructor - Kompleks syntax hatalarını yeniden yapılandırarak düzelten nihai script
 */

$migrationsDir = __DIR__ . '/../database/migrations';
$fixedCount = 0;
$totalChecked = 0;

echo "⚡ Ultimate Migration Reconstructor başlatılıyor...\n";

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

    // Completely reconstruct if class structure is broken
    if (preg_match('/return new class extends Migration/', $content)) {

        // Extract meaningful content from up() and down() functions
        $upContent = '';
        $downContent = '';

        // Find up() function content
        if (preg_match('/public function up\(\)[^{]*\{([^}]*(?:\{[^}]*\}[^}]*)*)/s', $content, $upMatch)) {
            $upContent = trim($upMatch[1]);
        }

        // Find down() function content
        if (preg_match('/public function down\(\)[^{]*\{([^}]*(?:\{[^}]*\}[^}]*)*)/s', $content, $downMatch)) {
            $downContent = trim($downMatch[1]);
        }

        // If no meaningful content found, set defaults
        if (empty($upContent) || strlen($upContent) < 10) {
            $upContent = '// Bu migrationda yapılacak bir işlem yok';
        }
        if (empty($downContent) || strlen($downContent) < 10) {
            $downContent = '// Bu migrationda yapılacak bir işlem yok';
        }

        // Clean up content - remove stray braces and incomplete structures
        $upContent = preg_replace('/^\s*\}\s*public function/', 'public function', $upContent);
        $downContent = preg_replace('/^\s*\}\s*$/', '', $downContent);
        $downContent = preg_replace('/\s*\}\s*;\s*$/', '', $downContent);

        // Reconstruct the entire file with proper structure
        $newContent = "<?php\n\n";
        $newContent .= "use Illuminate\Database\Migrations\Migration;\n";
        $newContent .= "use Illuminate\Database\Schema\Blueprint;\n";
        $newContent .= "use Illuminate\Support\Facades\Schema;\n\n";
        $newContent .= "return new class extends Migration\n{\n";
        $newContent .= "    public function up(): void\n    {\n";

        // Add proper indentation to up content
        $upLines = explode("\n", $upContent);
        foreach ($upLines as $line) {
            if (trim($line)) {
                $newContent .= "        " . trim($line) . "\n";
            }
        }

        $newContent .= "    }\n\n";
        $newContent .= "    public function down(): void\n    {\n";

        // Add proper indentation to down content
        $downLines = explode("\n", $downContent);
        foreach ($downLines as $line) {
            if (trim($line)) {
                $newContent .= "        " . trim($line) . "\n";
            }
        }

        $newContent .= "    }\n};\n";

        $content = $newContent;
    }

    if ($content !== $originalContent) {
        if (file_put_contents($filePath, $content)) {
            $fixedCount++;
            echo "✅ RECONSTRUCT\n";
        } else {
            echo "❌ HATA\n";
        }
    } else {
        echo "⏭️ No change\n";
    }
}

echo "\n📊 Ultimate Migration Reconstructor Özeti:\n";
echo "📁 Toplam kontrol edilen: $totalChecked\n";
echo "✅ Yeniden yapılandırılan dosyalar: $fixedCount\n";

// Final syntax check
echo "\n🔍 Final syntax kontrolü...\n";
$syntaxErrors = shell_exec("find " . escapeshellarg($migrationsDir) . " -name '*.php' -exec php -l {} \\; 2>&1 | grep -c 'Parse error\\|Fatal error\\|syntax error' || echo '0'");
echo "🎯 Kalan syntax hataları: " . trim($syntaxErrors) . "\n";

if (trim($syntaxErrors) == '0') {
    echo "🎉🎉🎉 TÜM MIGRATION SYNTAX HATALARI DÜZELTİLDİ! 🎉🎉🎉\n";
    echo "🚀 Artık tüm migration dosyaları temiz syntax'a sahip!\n";
} else {
    echo "⚠️ Hâlâ " . trim($syntaxErrors) . " syntax hatası mevcut.\n";
}

echo "\n⚡ Ultimate Migration Reconstructor tamamlandı!\n";
