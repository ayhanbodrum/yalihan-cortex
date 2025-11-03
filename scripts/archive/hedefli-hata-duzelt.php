<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n🔧 Hedefli Otomatik Hata Düzeltici\n";
echo "═══════════════════════════════════════\n\n";

$rapor = 'hedefli-sayfa-test-raporu.md';
if (!file_exists($rapor)) {
    echo "❌ Rapor bulunamadı: $rapor\n";
    exit(1);
}

$raporIcerik = file_get_contents($rapor);
$duzeltmeler = 0;

echo "📋 Rapor analiz ediliyor...\n\n";

// 1. $status hatası - TakimController
echo "🔧 TakimController - \$status düzeltiliyor...\n";
$takimController = 'app/Modules/TakimYonetimi/Http/Controllers/TakimController.php';
if (file_exists($takimController)) {
    $content = file_get_contents($takimController);
    
    // Eğer view'e gönderilmemişse ekle
    if (str_contains($content, "compact('takimUyeleri', 'istatistikler', 'lokasyonlar', 'status')")) {
        echo "   ✅ Zaten doğru\n";
    } else if (str_contains($content, "compact('takimUyeleri', 'istatistikler', 'lokasyonlar')")) {
        $content = str_replace(
            "compact('takimUyeleri', 'istatistikler', 'lokasyonlar')",
            "compact('takimUyeleri', 'istatistikler', 'lokasyonlar', 'status')",
            $content
        );
        file_put_contents($takimController, $content);
        echo "   ✅ \$status compact'e eklendi\n";
        $duzeltmeler++;
    }
}

// 2. $taslak hatası - KisiController view sorunu olabilir
echo "\n🔧 KisiController - \$taslak kontrol ediliyor...\n";
$kisiController = 'app/Http/Controllers/Admin/KisiController.php';
if (file_exists($kisiController)) {
    $content = file_get_contents($kisiController);
    
    // Compact'te var mı?
    if (!str_contains($content, "'taslak'")) {
        // istatistikler array'ine ekle
        if (preg_match('/\'pasif\' => Kisi::pasif\(\)->count\(\),/', $content)) {
            $content = str_replace(
                "'pasif' => Kisi::pasif()->count(),",
                "'pasif' => Kisi::pasif()->count(),\n            'taslak' => 0,",
                $content
            );
            file_put_contents($kisiController, $content);
            echo "   ✅ \$taslak istatistiklere eklendi\n";
            $duzeltmeler++;
        }
    } else {
        echo "   ✅ Zaten var\n";
    }
}

// 3. CRM Dashboard - Eslesme model kullanımı
echo "\n🔧 CRMController - Eslesme ilişkisi düzeltiliyor...\n";
$crmController = 'app/Http/Controllers/Admin/CRMController.php';
if (file_exists($crmController)) {
    $content = file_get_contents($crmController);
    
    // Use statement kontrolü
    if (!str_contains($content, 'use App\Models\Eslesme;')) {
        $content = str_replace(
            "use App\Models\Talep;",
            "use App\Models\Talep;\nuse App\Models\Eslesme;",
            $content
        );
        file_put_contents($crmController, $content);
        echo "   ✅ Eslesme model import edildi\n";
        $duzeltmeler++;
    } else {
        echo "   ✅ Zaten import edilmiş\n";
    }
}

echo "\n📊 Düzeltme Özeti:\n";
echo "✅ Toplam düzeltme: {$duzeltmeler}\n\n";

if ($duzeltmeler > 0) {
    echo "🔄 Cache temizleniyor...\n";
    exec('php artisan cache:clear');
    exec('php artisan view:clear');
    echo "   ✅ Cache temizlendi\n\n";
    
    echo "📋 Tekrar test et:\n";
    echo "   node scripts/hedefli-sayfa-testi.mjs\n\n";
}

echo "✨ Hedefli hata düzeltici tamamlandı!\n";

