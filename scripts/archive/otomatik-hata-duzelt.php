<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n🔧 Otomatik Hata Düzeltici\n";
echo "═══════════════════════════════════════\n\n";

$raporDosyasi = __DIR__ . '/../admin-detayli-test-raporu.md';

if (!file_exists($raporDosyasi)) {
    echo "❌ Test raporu bulunamadı!\n";
    echo "Önce test çalıştırın: node scripts/admin-detayli-test.mjs\n";
    exit(1);
}

$rapor = file_get_contents($raporDosyasi);

echo "📋 Test raporu okunuyor...\n\n";

$hatalar = [];

if (preg_match_all('/Tanımsız değişken: \$(\w+)/', $rapor, $matches)) {
    foreach ($matches[1] as $variable) {
        $hatalar[] = [
            'tip' => 'undefined_variable',
            'variable' => $variable,
        ];
    }
}

if (preg_match_all('/Tablo eksik: (\w+)/', $rapor, $matches)) {
    foreach ($matches[1] as $table) {
        $hatalar[] = [
            'tip' => 'missing_table',
            'table' => $table,
        ];
    }
}

echo "✅ " . count($hatalar) . " hata tespit edildi\n\n";

$duzeltmeler = 0;

foreach ($hatalar as $hata) {
    if ($hata['tip'] === 'undefined_variable') {
        $variable = $hata['variable'];
        echo "🔧 Düzeltiliyor: \${$variable} undefined\n";
        
        if ($variable === 'taslak') {
            echo "   → Kişiler için taslak istatistiği ekleniyor...\n";
            $controllerPath = 'app/Http/Controllers/Admin/KisiController.php';
            if (file_exists($controllerPath)) {
                $content = file_get_contents($controllerPath);
                if (!str_contains($content, "'taslak'")) {
                    $content = str_replace(
                        "'pasif' => Kisi::pasif()->count(),",
                        "'pasif' => Kisi::pasif()->count(),\n            'taslak' => 0,",
                        $content
                    );
                    file_put_contents($controllerPath, $content);
                    echo "   ✅ KisiController güncellendi\n";
                    $duzeltmeler++;
                }
            }
        }
        
        if ($variable === 'status') {
            echo "   → \$status değişkeni controller'lara ekleniyor...\n";
            
            $controllers = [
                'app/Modules/TakimYonetimi/Http/Controllers/TakimController.php',
                'app/Modules/TakimYonetimi/Http/Controllers/GorevController.php',
            ];
            
            foreach ($controllers as $controllerPath) {
                if (file_exists($controllerPath)) {
                    $content = file_get_contents($controllerPath);
                    if (!preg_match('/\$status\s*=\s*\$request->get/', $content)) {
                        $content = preg_replace(
                            '/(public function index\(Request \$request\)\s*\{)/',
                            '$1' . "\n        \$status = \$request->get('status');",
                            $content,
                            1
                        );
                        file_put_contents($controllerPath, $content);
                        echo "   ✅ " . basename($controllerPath) . " güncellendi\n";
                        $duzeltmeler++;
                    }
                }
            }
        }
    }
    
    if ($hata['tip'] === 'missing_table') {
        $table = $hata['table'];
        echo "🔧 Eksik tablo oluşturuluyor: {$table}\n";
        
        echo "   ⚠️  Manuel migration oluşturulmalı\n";
        echo "   → php artisan make:migration create_{$table}_table\n";
    }
}

echo "\n📊 Özet:\n";
echo "✅ Düzeltilen: {$duzeltmeler}\n";
echo "⚠️  Manuel müdahale gereken: " . (count($hatalar) - $duzeltmeler) . "\n";

if ($duzeltmeler > 0) {
    echo "\n🔄 Testi tekrar çalıştır:\n";
    echo "node scripts/admin-detayli-test.mjs\n";
}

echo "\n✨ Otomatik hata düzeltici tamamlandı!\n";

