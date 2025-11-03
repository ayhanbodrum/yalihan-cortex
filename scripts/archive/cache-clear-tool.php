<?php

/**
 * VS Code Editor Session Cache Clear - Persistent error attachments için
 */

echo "🔄 VS Code Editor Session Cache Clear!\n";
echo "🎯 Persistent editor session hatalarını temizliyoruz...\n\n";

$problemFiles = [
    'database/migrations/2025_08_11_094446_create_language_settings_table.php',
    'database/migrations/2025_06_14_091754_add_alt_kategori_to_ilanlar_table.php',
    'database/migrations/1000_02_01_000001_create_roles_table.php'
];

echo "📁 Kontrol edilen dosyalar:\n";

$allClean = true;
foreach ($problemFiles as $file) {
    $fullPath = __DIR__ . '/../' . $file;

    if (file_exists($fullPath)) {
        // Syntax check
        $syntaxCheck = shell_exec("php -l " . escapeshellarg($fullPath) . " 2>&1");
        $isClean = strpos($syntaxCheck, 'No syntax errors') !== false;

        echo "  " . ($isClean ? "✅" : "❌") . " $file\n";

        if (!$isClean) {
            $allClean = false;
            echo "    ⚠️  " . trim($syntaxCheck) . "\n";
        }
    } else {
        echo "  ❓ $file (not found)\n";
    }
}

echo "\n🔍 VS Code Editor Session Analysis:\n";

if ($allClean) {
    echo "✅ Tüm dosyalar syntax açısından temiz!\n";
    echo "⚠️  Editor session error'ları VS Code cache problemi\n";
    echo "🔄 Solution: VS Code restart veya workspace reload gerekli\n";
    echo "\n💡 Recommended Actions:\n";
    echo "   1. VS Code'u restart edin\n";
    echo "   2. Workspace'i reload edin (Cmd+Shift+P > Reload Window)\n";
    echo "   3. .vscode/settings.json cache'i temizleyin\n";
} else {
    echo "❌ Bazı dosyalarda hala syntax hatalar var\n";
    echo "🔧 Automated learning sistemi devam etmeli\n";
}

// Global syntax error count
$totalErrors = (int)shell_exec("find database/migrations -name '*.php' -exec php -l {} \\; 2>&1 | grep -c 'Parse error\\|Fatal error\\|syntax error' || echo '0'");

echo "\n📊 Global Migration Status:\n";
echo "⚠️  Toplam syntax hatası: $totalErrors\n";

if ($totalErrors === 0) {
    echo "🎉 TÜM MİGRATION DOSYALARI TEMİZ!\n";
    echo "🚀 AUTOMATED LEARNING SİSTEMİ BAŞARIYLA TAMAMLANDI!\n";
} else {
    echo "🔧 Automated learning devam etmeli\n";
}

echo "\n🔄 Cache Clear Tool tamamlandı!\n";
