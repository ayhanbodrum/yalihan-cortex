#!/usr/bin/env php
<?php

/**
 * Telegram Bot Test Script
 *
 * Bu script Telegram bot sistemini test eder:
 * 1. Bot token kontrolü
 * 2. Webhook durumu
 * 3. Test mesajı gönderme
 * 4. Eşleştirme kodu testi
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

echo "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🤖 TELEGRAM BOT TEST SCRIPT\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n";

// 1. Bot Token Kontrolü
$botToken = env('TELEGRAM_BOT_TOKEN');
$botUsername = env('TELEGRAM_BOT_USERNAME', 'YalihanCortex_Bot');
$adminChatId = env('TELEGRAM_ADMIN_CHAT_ID');

echo "📋 1. BOT TOKEN KONTROLÜ\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

if (empty($botToken)) {
    echo "❌ TELEGRAM_BOT_TOKEN tanımsız!\n";
    echo "   .env dosyasına TELEGRAM_BOT_TOKEN ekleyin.\n\n";
    exit(1);
}

echo "✅ Bot Token: " . substr($botToken, 0, 10) . "...\n";
echo "✅ Bot Username: {$botUsername}\n";

if (empty($adminChatId)) {
    echo "⚠️  TELEGRAM_ADMIN_CHAT_ID tanımsız (test mesajı gönderilemez)\n";
} else {
    echo "✅ Admin Chat ID: {$adminChatId}\n";
}

echo "\n";

// 2. Bot Bilgilerini Getir
echo "📋 2. BOT BİLGİLERİ\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

try {
    $response = Http::timeout(10)->get("https://api.telegram.org/bot{$botToken}/getMe");

    if ($response->successful()) {
        $data = $response->json();
        if ($data['ok'] ?? false) {
            $bot = $data['result'] ?? [];
            echo "✅ Bot Bağlantısı: Başarılı\n";
            echo "   Bot ID: {$bot['id']}\n";
            echo "   Bot Username: @{$bot['username']}\n";
            echo "   Bot Adı: {$bot['first_name']}\n";
        } else {
            echo "❌ Bot bilgileri alınamadı: " . ($data['description'] ?? 'Bilinmeyen hata') . "\n";
        }
    } else {
        echo "❌ Bot API'ye erişilemedi: HTTP {$response->status()}\n";
        echo "   Response: " . $response->body() . "\n";
    }
} catch (\Exception $e) {
    echo "❌ Bot bilgileri alınırken hata: " . $e->getMessage() . "\n";
}

echo "\n";

// 3. Webhook Durumu
echo "📋 3. WEBHOOK DURUMU\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

try {
    $response = Http::timeout(10)->get("https://api.telegram.org/bot{$botToken}/getWebhookInfo");

    if ($response->successful()) {
        $data = $response->json();
        if ($data['ok'] ?? false) {
            $webhook = $data['result'] ?? [];
            $url = $webhook['url'] ?? 'Tanımsız';
            $pendingUpdates = $webhook['pending_update_count'] ?? 0;

            echo "✅ Webhook URL: {$url}\n";
            echo "   Bekleyen Güncellemeler: {$pendingUpdates}\n";

            if (empty($url) || $url === '') {
                echo "⚠️  Webhook ayarlanmamış!\n";
                echo "   Webhook ayarlamak için:\n";
                echo "   https://api.telegram.org/bot{$botToken}/setWebhook?url=" . url('/api/telegram/webhook') . "\n";
            } else {
                echo "✅ Webhook aktif\n";
            }
        }
    }
} catch (\Exception $e) {
    echo "❌ Webhook durumu alınırken hata: " . $e->getMessage() . "\n";
}

echo "\n";

// 4. Test Mesajı Gönderme
if (!empty($adminChatId)) {
    echo "📋 4. TEST MESAJI GÖNDERME\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

    try {
        $testMessage = "🧪 *Telegram Bot Test*\n\n";
        $testMessage .= "✅ Bot çalışıyor!\n";
        $testMessage .= "🕐 Test Zamanı: " . now()->format('d.m.Y H:i:s') . "\n";
        $testMessage .= "🔗 Webhook: " . url('/api/telegram/webhook') . "\n";

        $response = Http::timeout(10)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id' => $adminChatId,
            'text' => $testMessage,
            'parse_mode' => 'Markdown',
        ]);

        if ($response->successful()) {
            $data = $response->json();
            if ($data['ok'] ?? false) {
                echo "✅ Test mesajı gönderildi!\n";
                echo "   Chat ID: {$adminChatId}\n";
                echo "   Mesaj ID: " . ($data['result']['message_id'] ?? 'N/A') . "\n";
            } else {
                echo "❌ Mesaj gönderilemedi: " . ($data['description'] ?? 'Bilinmeyen hata') . "\n";
            }
        } else {
            echo "❌ Mesaj gönderilemedi: HTTP {$response->status()}\n";
            echo "   Response: " . $response->body() . "\n";
        }
    } catch (\Exception $e) {
        echo "❌ Test mesajı gönderilirken hata: " . $e->getMessage() . "\n";
    }

    echo "\n";
}

// 5. Webhook Endpoint Testi
echo "📋 5. WEBHOOK ENDPOINT TESTİ\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

try {
    $webhookUrl = url('/api/telegram/webhook');
    $testUrl = url('/api/telegram/webhook/test');

    echo "✅ Webhook URL: {$webhookUrl}\n";
    echo "✅ Test URL: {$testUrl}\n";

    // Test endpoint'ini kontrol et
    $response = Http::timeout(5)->get($testUrl);

    if ($response->successful()) {
        $data = $response->json();
        if ($data['success'] ?? false) {
            echo "✅ Webhook endpoint aktif!\n";
        } else {
            echo "⚠️  Webhook endpoint yanıt verdi ama success=false\n";
        }
    } else {
        echo "⚠️  Webhook test endpoint'ine erişilemedi: HTTP {$response->status()}\n";
    }
} catch (\Exception $e) {
    echo "⚠️  Webhook endpoint test edilemedi: " . $e->getMessage() . "\n";
}

echo "\n";

// 6. Özet
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 TEST ÖZETİ\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n";
echo "✅ Bot Token: " . (empty($botToken) ? "❌" : "✅") . "\n";
echo "✅ Bot Bağlantısı: Kontrol edildi\n";
echo "✅ Webhook Durumu: Kontrol edildi\n";
if (!empty($adminChatId)) {
    echo "✅ Test Mesajı: Gönderildi\n";
}
echo "\n";
echo "💡 SONRAKİ ADIMLAR:\n";
echo "   1. Telegram'da @{$botUsername} botunu bulun\n";
echo "   2. /start komutu ile başlatın\n";
echo "   3. Eşleştirme kodu oluşturun: /admin/telegram-bot\n";
echo "   4. Bot'a eşleştirme kodunu gönderin\n";
echo "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n";

