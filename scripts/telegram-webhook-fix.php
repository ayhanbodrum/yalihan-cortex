#!/usr/bin/env php
<?php

/**
 * Telegram Webhook Fix Script
 * 
 * Webhook sorunlarını tespit eder ve düzeltir
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$botToken = env('TELEGRAM_BOT_TOKEN');
$webhookUrl = env('TELEGRAM_WEBHOOK_URL', 'https://panel.yalihanemlak.com.tr/api/telegram/webhook');

echo "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🔧 TELEGRAM WEBHOOK FIX SCRIPT\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n";

// 1. Mevcut webhook durumunu kontrol et
echo "📋 1. MEVCUT WEBHOOK DURUMU\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

try {
    $response = Http::timeout(10)->get("https://api.telegram.org/bot{$botToken}/getWebhookInfo");
    
    if ($response->successful()) {
        $data = $response->json();
        if ($data['ok'] ?? false) {
            $webhook = $data['result'] ?? [];
            $url = $webhook['url'] ?? 'Tanımsız';
            $pending = $webhook['pending_update_count'] ?? 0;
            $lastError = $webhook['last_error_message'] ?? 'Yok';
            
            echo "✅ Webhook URL: {$url}\n";
            echo "📊 Bekleyen Güncellemeler: {$pending}\n";
            
            if ($lastError !== 'Yok') {
                echo "❌ Son Hata: {$lastError}\n";
            } else {
                echo "✅ Hata yok\n";
            }
        }
    }
} catch (\Exception $e) {
    echo "❌ Webhook durumu alınamadı: " . $e->getMessage() . "\n";
}

echo "\n";

// 2. Webhook URL'ini test et
echo "📋 2. WEBHOOK URL TESTİ\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

try {
    $testUrl = rtrim($webhookUrl, '/') . '/test';
    echo "🔗 Test URL: {$testUrl}\n";
    
    $response = Http::timeout(5)->get($testUrl);
    
    if ($response->successful()) {
        $data = $response->json();
        if ($data['success'] ?? false) {
            echo "✅ Webhook endpoint erişilebilir!\n";
        } else {
            echo "⚠️  Webhook endpoint yanıt verdi ama success=false\n";
        }
    } else {
        echo "❌ Webhook endpoint erişilemiyor: HTTP {$response->status()}\n";
        echo "   Response: " . substr($response->body(), 0, 200) . "\n";
    }
} catch (\Exception $e) {
    echo "❌ Webhook endpoint test edilemedi: " . $e->getMessage() . "\n";
    echo "   💡 Bu, webhook URL'inin erişilemediği anlamına gelir.\n";
    echo "   💡 Production sunucusu çalışmıyor olabilir.\n";
}

echo "\n";

// 3. Çözüm önerileri
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "💡 ÇÖZÜM ÖNERİLERİ\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n";

echo "1. Production Sunucusu Kontrolü:\n";
echo "   → https://panel.yalihanemlak.com.tr/api/telegram/webhook/test\n";
echo "   → Bu URL'e tarayıcıdan erişmeyi deneyin\n";
echo "\n";

echo "2. Local Development için:\n";
echo "   → ngrok veya başka bir tunnel servisi kullanın\n";
echo "   → Webhook URL'ini tunnel URL'ine ayarlayın\n";
echo "\n";

echo "3. Geçici Çözüm (getUpdates):\n";
echo "   → Webhook'u kaldırın: deleteWebhook\n";
echo "   → getUpdates API ile manuel mesaj çekin\n";
echo "   → (Sadece test için, production'da webhook kullanın)\n";
echo "\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n";

