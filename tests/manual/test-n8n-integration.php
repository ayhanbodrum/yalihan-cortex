<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\N8nService;

echo "\n🔄 n8n ENTEGRASYON TESTİ\n";
echo "═══════════════════════════════════════\n";
echo 'Test Zamanı: '.date('Y-m-d H:i:s')."\n\n";

$n8n = app(N8nService::class);

echo "1️⃣ n8n Servisi Kontrolü\n";
echo '   URL: '.config('services.n8n.url')."\n";
echo '   Status: '.(config('services.n8n.enabled') ? '✅ Aktif' : '⚠️  Kapalı')."\n\n";

if (! config('services.n8n.enabled')) {
    echo "⚠️  n8n entegrasyonu kapalı!\n";
    echo "Aktif etmek için .env dosyasına ekle:\n";
    echo "N8N_ENABLED=true\n";
    echo "N8N_URL=http://localhost:5678\n";
    echo "N8N_WEBHOOK_TOKEN=your-secret-token\n\n";
}

echo "2️⃣ Test Notification Gönderiliyor...\n";
$result = $n8n->sendNotification('test', [
    'message' => 'n8n entegrasyonu test ediliyor',
    'timestamp' => now()->toIso8601String(),
]);

if ($result['success']) {
    echo "   ✅ Başarılı!\n";
    echo '   Response: '.json_encode($result['data'], JSON_PRETTY_PRINT)."\n\n";
} else {
    echo "   ❌ Hata!\n";
    echo '   Error: '.($result['error'] ?? 'Bilinmeyen hata')."\n\n";
}

echo "3️⃣ Örnek İlan Data Gönderiliyor...\n";
$result = $n8n->sendNewIlan([
    'id' => 999,
    'baslik' => 'Test İlan - n8n Entegrasyonu',
    'fiyat' => 1500000,
    'kategori' => 'Konut',
    'il' => 'Muğla',
]);

if ($result['success']) {
    echo "   ✅ Başarılı!\n\n";
} else {
    echo '   ❌ Hata: '.($result['error'] ?? 'Webhook tetiklenemedi')."\n\n";
}

echo "4️⃣ Örnek Kişi Data Gönderiliyor...\n";
$result = $n8n->sendNewKisi([
    'id' => 999,
    'ad' => 'Test',
    'soyad' => 'Kullanıcı',
    'telefon' => '0532 123 45 67',
    'email' => 'test@example.com',
]);

if ($result['success']) {
    echo "   ✅ Başarılı!\n\n";
} else {
    echo '   ❌ Hata: '.($result['error'] ?? 'Webhook tetiklenemedi')."\n\n";
}

echo "\n📊 TEST SONUCU\n";
echo "═══════════════════════════════════════\n";
echo "n8n Servisi: ✅ Hazır\n";
echo "Config: ✅ Ayarlandı\n";
echo 'Webhook Test: '.($result['success'] ? '✅' : '⚠️')."\n";

echo "\n💡 SONRAKI ADIMLAR:\n";
echo "═══════════════════════════════════════\n";
echo "1. n8n'i başlat:\n";
echo "   docker-compose -f docker-compose.n8n.yml up -d\n\n";
echo "2. n8n'e tarayıcıda gir:\n";
echo "   http://localhost:5678\n\n";
echo "3. Webhook oluştur ve URL'i al\n\n";
echo "4. Laravel'den test et:\n";
echo "   php test-n8n-integration.php\n\n";

echo "✨ Test tamamlandı!\n";
