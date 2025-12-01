<?php

/**
 * Market Intelligence API Test Script
 *
 * Context7: Market Intelligence - Test Endpoints
 * Kullanım: php tests/manual/test-market-intelligence.php
 */

require __DIR__ . '/../../vendor/autoload.php';

use Carbon\Carbon;

$baseUrl = 'http://127.0.0.1:8000';

echo "\n🧪 MARKET INTELLIGENCE API TEST RAPORU\n";
echo "========================================\n";
echo "Sunucu: {$baseUrl}\n";
echo 'Test Zamanı: ' . date('Y-m-d H:i:s') . "\n\n";

// Test 1: Aktif Bölgeleri Getir
echo "📋 TEST 1: Aktif Bölgeleri Getir\n";
echo "   GET /api/admin/market-intelligence/active-regions\n";
$ch = curl_init($baseUrl . '/api/admin/market-intelligence/active-regions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$body = substr($response, $headerSize);
$json = json_decode($body, true);

if ($httpCode === 200 && isset($json['success']) && $json['success']) {
    echo "   ✅ Başarılı - HTTP {$httpCode}\n";
    echo "   📊 Aktif Bölge Sayısı: " . count($json['data'] ?? []) . "\n";
} else {
    echo "   ❌ Hata - HTTP {$httpCode}\n";
    echo "   📄 Yanıt: " . substr($body, 0, 200) . "\n";
}
echo "\n";

// Test 2: Sync Endpoint (Test Verisi)
echo "📋 TEST 2: Veri Senkronizasyonu (Test Verisi)\n";
echo "   POST /api/admin/market-intelligence/sync\n";

$testData = [
    'source' => 'sahibinden',
    'region' => [
        'il_id' => 7,
        'ilce_id' => 123,
    ],
    'listings' => [
        [
            'external_id' => 'TEST_' . time(),
            'url' => 'https://sahibinden.com/ilan/test-' . time(),
            'title' => 'Test İlan - Deniz Manzaralı 3+1 Daire',
            'price' => 1500000,
            'currency' => 'TRY',
            'location_il' => 'Antalya',
            'location_ilce' => 'Muratpaşa',
            'location_mahalle' => 'Konyaaltı',
            'm2_brut' => 120,
            'm2_net' => 100,
            'room_count' => '3+1',
            'listing_date' => \Carbon\Carbon::now()->subDays(15)->toDateString(), // 15 gün önce (taze kategori)
            'snapshot_data' => [
                'test' => true,
                'created_at' => now()->toIso8601String(),
            ],
        ],
        [
            'external_id' => 'TEST_YORGUN_' . time(),
            'url' => 'https://sahibinden.com/ilan/test-yorgun-' . time(),
            'title' => 'Test İlan - Yorgun İlan (45 Gün)',
            'price' => 1800000,
            'currency' => 'TRY',
            'location_il' => 'Antalya',
            'location_ilce' => 'Muratpaşa',
            'm2_brut' => 150,
            'm2_net' => 130,
            'room_count' => '4+1',
            'listing_date' => \Carbon\Carbon::now()->subDays(45)->toDateString(), // 45 gün önce (yorgun kategori)
            'snapshot_data' => [
                'test' => true,
                'created_at' => Carbon::now()->toIso8601String(),
            ],
        ],
    ],
];

$ch = curl_init($baseUrl . '/api/admin/market-intelligence/sync');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
]);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$body = substr($response, $headerSize);
$json = json_decode($body, true);

if ($httpCode === 200 && isset($json['success']) && $json['success']) {
    echo "   ✅ Başarılı - HTTP {$httpCode}\n";
    echo "   📊 Senkronize Edilen: " . ($json['data']['synced_count'] ?? 0) . " ilan\n";
    echo "   🆕 Yeni: " . ($json['data']['new_count'] ?? 0) . " ilan\n";
    echo "   🔄 Güncellenen: " . ($json['data']['updated_count'] ?? 0) . " ilan\n";
    echo "   📝 Mesaj: " . ($json['message'] ?? '') . "\n";
} else {
    echo "   ❌ Hata - HTTP {$httpCode}\n";
    echo "   📄 Yanıt: " . substr($body, 0, 300) . "\n";
}
echo "\n";

// Test 3: İlan Yaşı Analizi (Tinker benzeri)
echo "📋 TEST 3: İlan Yaşı Analizi\n";
echo "   Model Metodları Testi\n";

// PHP artisan tinker benzeri test
require __DIR__ . '/../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $listing = \App\Models\MarketListing::where('source', 'sahibinden')
        ->whereNotNull('listing_date')
        ->first();

    if ($listing) {
        $age = $listing->getAgeInDays();
        $isTired = $listing->isTired();
        $category = $listing->getAgeCategory();

        echo "   ✅ İlan Bulundu: {$listing->title}\n";
        echo "   📅 İlan Tarihi: {$listing->listing_date}\n";
        echo "   ⏰ İlan Yaşı: {$age} gün\n";
        echo "   🏷️  Kategori: {$category}\n";
        echo "   😴 Yorgun mu? " . ($isTired ? 'Evet (30+ gün)' : 'Hayır') . "\n";
    } else {
        echo "   ⚠️  Test için ilan bulunamadı. Önce sync endpoint'i çalıştırın.\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Hata: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 4: Query Scopes Testi
echo "📋 TEST 4: Query Scopes Testi\n";
echo "   Yorgun/Yeni İlan Filtreleme\n";

try {
    $tiredCount = \App\Models\MarketListing::tired()->count();
    $newCount = \App\Models\MarketListing::new()->count();
    $totalCount = \App\Models\MarketListing::count();

    echo "   📊 Toplam İlan: {$totalCount}\n";
    echo "   😴 Yorgun İlanlar (30+ gün): {$tiredCount}\n";
    echo "   🆕 Yeni İlanlar (0-7 gün): {$newCount}\n";
} catch (\Exception $e) {
    echo "   ❌ Hata: " . $e->getMessage() . "\n";
}
echo "\n";

echo "🎯 TEST TAMAMLANDI!\n";
echo "========================================\n";
echo "\n";
echo "💡 İpuçları:\n";
echo "   - Sync endpoint'i test etmek için yukarıdaki test verisini kullanın\n";
echo "   - Gerçek veriler için n8n bot entegrasyonu gerekli\n";
echo "   - Settings view oluşturulduğunda bölge seçimi yapılabilir\n";
echo "\n";
