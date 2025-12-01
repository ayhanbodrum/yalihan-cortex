#!/bin/bash

# Telegram Bot Local Development Setup Script
# Bu script local development için ngrok kullanarak webhook'u ayarlar

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🔧 TELEGRAM BOT LOCAL SETUP"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Bot token
BOT_TOKEN="7834521220:AAFLKxa18v4UFPj46Fh-esL-8uMdmuXxy70"

# 1. ngrok kontrolü
if ! command -v ngrok &> /dev/null; then
    echo "❌ ngrok bulunamadı!"
    echo ""
    echo "💡 ngrok kurulumu:"
    echo "   macOS: brew install ngrok/ngrok/ngrok"
    echo "   veya: https://ngrok.com/download"
    echo ""
    exit 1
fi

echo "✅ ngrok bulundu"
echo ""

# 2. Port kontrolü
if ! lsof -ti:8000 &> /dev/null; then
    echo "⚠️  Port 8000 kullanılmıyor"
    echo "   Laravel sunucusu çalışmıyor olabilir"
    echo "   → php artisan serve"
    echo ""
fi

# 3. ngrok başlat (arka planda)
echo "🚀 ngrok başlatılıyor..."
ngrok http 8000 > /tmp/ngrok.log 2>&1 &
NGROK_PID=$!

# ngrok'un başlaması için bekle
sleep 3

# 4. ngrok URL'ini al
NGROK_URL=$(curl -s http://localhost:4040/api/tunnels | grep -o '"public_url":"https://[^"]*"' | head -1 | cut -d'"' -f4)

if [ -z "$NGROK_URL" ]; then
    echo "❌ ngrok URL'i alınamadı"
    echo "   ngrok çalışıyor mu kontrol edin: http://localhost:4040"
    kill $NGROK_PID 2>/dev/null
    exit 1
fi

echo "✅ ngrok URL: $NGROK_URL"
echo ""

# 5. Webhook URL'ini oluştur
WEBHOOK_URL="${NGROK_URL}/api/telegram/webhook"
echo "🌐 Webhook URL: $WEBHOOK_URL"
echo ""

# 6. Webhook'u ayarla
echo "📡 Webhook ayarlanıyor..."
RESPONSE=$(curl -s -X POST "https://api.telegram.org/bot${BOT_TOKEN}/setWebhook?url=${WEBHOOK_URL}")

if echo "$RESPONSE" | grep -q '"ok":true'; then
    echo "✅ Webhook başarıyla ayarlandı!"
    echo ""
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo "📋 SONRAKİ ADIMLAR:"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo ""
    echo "1. Laravel sunucusu çalışıyor olmalı:"
    echo "   → php artisan serve"
    echo ""
    echo "2. Telegram'da @YalihanCortex_Bot'u test edin:"
    echo "   → /start komutu"
    echo "   → Eşleştirme kodu gönderin"
    echo ""
    echo "3. ngrok'u durdurmak için:"
    echo "   → kill $NGROK_PID"
    echo "   → veya: pkill ngrok"
    echo ""
    echo "4. Webhook durumunu kontrol etmek için:"
    echo "   → curl https://api.telegram.org/bot${BOT_TOKEN}/getWebhookInfo"
    echo ""
else
    echo "❌ Webhook ayarlanamadı!"
    echo "   Response: $RESPONSE"
    kill $NGROK_PID 2>/dev/null
    exit 1
fi

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

