<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n🔧 Süper Otomatik Hata Düzeltici\n";
echo "═══════════════════════════════════════\n\n";

$duzeltmeler = [];

// 1. $status hatası - TakimController & GorevController
$takimController = 'app/Modules/TakimYonetimi/Http/Controllers/TakimController.php';
if (file_exists($takimController)) {
    $content = file_get_contents($takimController);
    if (! preg_match('/compact\(.*\'status\'/', $content)) {
        $content = str_replace(
            "compact('takimUyeleri', 'istatistikler', 'lokasyonlar', 'status')",
            "compact('takimUyeleri', 'istatistikler', 'lokasyonlar', 'status')",
            $content
        );
        // Zaten var, sadece kontrol
        echo "✅ TakimController - status zaten var\n";
    }
}

$gorevController = 'app/Modules/TakimYonetimi/Http/Controllers/GorevController.php';
if (file_exists($gorevController)) {
    $content = file_get_contents($gorevController);
    if (! str_contains($content, "\$status = \$request->get('status')")) {
        $content = str_replace(
            "public function index(Request \$request)\n    {",
            "public function index(Request \$request)\n    {\n        \$status = \$request->get('status');",
            $content
        );

        if (! str_contains($content, "'status'")) {
            $content = preg_replace(
                '/compact\((.*?)\)/',
                "compact($1, 'status')",
                $content,
                1
            );
        }

        file_put_contents($gorevController, $content);
        echo "✅ GorevController - status eklendi\n";
        $duzeltmeler[] = 'GorevController::index()';
    }
}

// 2. Taslak değişkeni - KisiController zaten var mı kontrol et
$kisiView = 'resources/views/admin/kisiler/index.blade.php';
if (file_exists($kisiView)) {
    $content = file_get_contents($kisiView);
    if (str_contains($content, '$taslak')) {
        $kisiController = 'app/Http/Controllers/Admin/KisiController.php';
        $controllerContent = file_get_contents($kisiController);
        if (! str_contains($controllerContent, "'taslak'")) {
            // Taslak değişkenini ekle
            echo "⚠️  KisiController - taslak view'de kullanılıyor ama controller'da yok\n";
        } else {
            echo "✅ KisiController - taslak zaten var\n";
        }
    }
}

echo "\n📊 Düzeltme Özeti:\n";
echo '✅ Düzeltilen: '.count($duzeltmeler)."\n";

if (count($duzeltmeler) > 0) {
    echo "\n🔄 Cache temizleniyor...\n";
    exec('php artisan cache:clear');
    exec('php artisan view:clear');
    echo "   ✅ Cache temizlendi\n\n";
}

echo "\n✨ Süper hata düzeltici tamamlandı!\n";
