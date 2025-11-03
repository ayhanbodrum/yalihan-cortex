<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n🔧 Gelişmiş Otomatik Hata Düzeltici\n";
echo "═══════════════════════════════════════\n\n";

$raporlar = [
    __DIR__ . '/../admin-kapsamli-test-raporu.md',
    __DIR__ . '/../admin-detayli-test-raporu.md',
];

$hatalar = [];

foreach ($raporlar as $raporDosyasi) {
    if (!file_exists($raporDosyasi)) continue;

    $rapor = file_get_contents($raporDosyasi);

    if (preg_match_all('/Tanımsız değişken: \$(\w+).*?Controller: ([\w\/]+)/s', $rapor, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $hatalar[] = [
                'tip' => 'undefined_variable',
                'variable' => $match[1],
                'controller' => $match[2] ?? null,
            ];
        }
    }

    if (preg_match_all('/Tanımsız değişken: \$(\w+)/s', $rapor, $matches)) {
        foreach ($matches[1] as $variable) {
            if (!in_array($variable, array_column($hatalar, 'variable'))) {
                $hatalar[] = [
                    'tip' => 'undefined_variable',
                    'variable' => $variable,
                ];
            }
        }
    }
}

$hatalar = array_unique($hatalar, SORT_REGULAR);

echo "📋 Tespit edilen hatalar: " . count($hatalar) . "\n\n";

$duzeltmeler = 0;

foreach ($hatalar as $hata) {
    if ($hata['tip'] === 'undefined_variable') {
        $variable = $hata['variable'];
        echo "🔧 Düzeltiliyor: \${$variable}\n";

        switch ($variable) {
            case 'taslak':
                $path = 'app/Http/Controllers/Admin/KisiController.php';
                if (file_exists($path)) {
                    $content = file_get_contents($path);
                    if (!str_contains($content, "'taslak'")) {
                        $content = str_replace(
                            "            'pasif' => Kisi::pasif()->count(),",
                            "            'pasif' => Kisi::pasif()->count(),\n            'taslak' => 0,",
                            $content
                        );
                        file_put_contents($path, $content);
                        echo "   ✅ KisiController::index() - taslak eklendi\n";
                        $duzeltmeler++;
                    }
                }
                break;

            case 'danismanlar':
                $path = 'app/Http/Controllers/Admin/KisiController.php';
                if (file_exists($path)) {
                    $content = file_get_contents($path);
                    if (preg_match('/public function edit\([^)]+\)/', $content)) {
                        $content = preg_replace(
                            '/(public function edit\([^)]+\)\s*\{)/',
                            '$1' . "\n        \$danismanlar = \App\Models\User::whereHas('roles', function(\$q) { \$q->where('name', 'danisman'); })->get();",
                            $content,
                            1
                        );
                        file_put_contents($path, $content);
                        echo "   ✅ KisiController::edit() - danismanlar eklendi\n";
                        $duzeltmeler++;
                    }
                }
                break;

            case 'status':
                $paths = [
                    'app/Modules/TakimYonetimi/Http/Controllers/TakimController.php',
                    'app/Modules/TakimYonetimi/Http/Controllers/GorevController.php',
                ];

                foreach ($paths as $path) {
                    if (!file_exists($path)) continue;

                    $content = file_get_contents($path);
                    if (!preg_match('/\$status\s*=/', $content)) {
                        $content = preg_replace(
                            '/(public function index\(Request \$request\)\s*\{)/',
                            '$1' . "\n        \$status = \$request->get('status');",
                            $content,
                            1
                        );
                        file_put_contents($path, $content);
                        echo "   ✅ " . basename($path) . " - status eklendi\n";
                        $duzeltmeler++;
                    }
                }
                break;

            case 'ustKategoriler':
                $path = 'app/Http/Controllers/Admin/IlanKategoriController.php';
                if (file_exists($path)) {
                    $content = file_get_contents($path);
                    if (!str_contains($content, '$ustKategoriler')) {
                        $content = preg_replace(
                            '/(public function edit\([^)]+\)\s*\{)/',
                            '$1' . "\n        \$ustKategoriler = \App\Models\IlanKategori::whereNull('parent_id')->get();",
                            $content,
                            1
                        );
                        file_put_contents($path, $content);
                        echo "   ✅ IlanKategoriController::edit() - ustKategoriler eklendi\n";
                        $duzeltmeler++;
                    }
                }
                break;

            default:
                echo "   ⚠️  Otomatik düzeltme yok - manuel kontrol gerekli\n";
        }
    }
}

echo "\n📊 Düzeltme Özeti:\n";
echo "✅ Otomatik düzeltilen: {$duzeltmeler}\n";
echo "⚠️  Manuel gereken: " . (count($hatalar) - $duzeltmeler) . "\n\n";

if ($duzeltmeler > 0) {
    echo "🔄 Cache temizleniyor...\n";
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    echo "   ✅ Cache temizlendi\n\n";

    echo "📋 Testi tekrar çalıştırın:\n";
    echo "   node scripts/admin-kapsamli-test.mjs\n\n";
}

echo "✨ Gelişmiş hata düzeltici tamamlandı!\n";
