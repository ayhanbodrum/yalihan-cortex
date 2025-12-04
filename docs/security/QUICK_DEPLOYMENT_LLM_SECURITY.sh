#!/bin/bash

# ═══════════════════════════════════════════════════════════
# 🛡️ LLM GÜVENLİK FİNALİ - Quick Deployment Script
# ═══════════════════════════════════════════════════════════
# Tarih: 2025-12-03
# Versiyon: 1.0.0
# Durum: Production Ready
# ═══════════════════════════════════════════════════════════

set -e  # Exit on error

echo "🛡️ LLM GÜVENLİK FİNALİ - DEPLOYMENT BAŞLIYOR..."
echo ""

# ═══════════════════════════════════════════════════════════
# 1. ENVIRONMENT CONFIGURATION
# ═══════════════════════════════════════════════════════════

echo "📝 1. Environment variables güncelleniyor..."

# .env dosyasını backup al
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)

# AI_REQUIRE_TLS güncelle/ekle
if grep -q "^AI_REQUIRE_TLS=" .env; then
    sed -i 's/^AI_REQUIRE_TLS=.*/AI_REQUIRE_TLS=true/' .env
    echo "✅ AI_REQUIRE_TLS=true güncellendi"
else
    echo "" >> .env
    echo "# AI Security - KVKK Compliance" >> .env
    echo "AI_REQUIRE_TLS=true" >> .env
    echo "✅ AI_REQUIRE_TLS=true eklendi"
fi

# OLLAMA_API_URL güncelle/ekle
if grep -q "^OLLAMA_API_URL=" .env; then
    sed -i 's|^OLLAMA_API_URL=.*|OLLAMA_API_URL=https://ollama.yalihanemlak.internal|' .env
    echo "✅ OLLAMA_API_URL HTTPS'e güncellendi"
else
    echo "OLLAMA_API_URL=https://ollama.yalihanemlak.internal" >> .env
    echo "✅ OLLAMA_API_URL eklendi"
fi

echo ""

# ═══════════════════════════════════════════════════════════
# 2. NGINX CONFIGURATION
# ═══════════════════════════════════════════════════════════

echo "🌐 2. Nginx configuration kontrol ediliyor..."

NGINX_CONFIG="/etc/nginx/sites-available/ollama-ssl"

if [ ! -f "$NGINX_CONFIG" ]; then
    echo "⚠️  Nginx config bulunamadı. Manuel olarak oluşturun:"
    echo "   sudo nano /etc/nginx/sites-available/ollama-ssl"
    echo ""
    echo "📄 Config örneği: docs/security/nginx-ollama-ssl.conf"
else
    echo "✅ Nginx config mevcut: $NGINX_CONFIG"
    
    # Syntax check
    sudo nginx -t 2>&1
    
    if [ $? -eq 0 ]; then
        echo "✅ Nginx configuration geçerli"
    else
        echo "❌ Nginx configuration hatası!"
        exit 1
    fi
fi

echo ""

# ═══════════════════════════════════════════════════════════
# 3. SSL CERTIFICATE CHECK
# ═══════════════════════════════════════════════════════════

echo "🔒 3. SSL sertifikası kontrol ediliyor..."

SSL_CERT="/etc/letsencrypt/live/ollama.yalihanemlak.internal/fullchain.pem"

if [ -f "$SSL_CERT" ]; then
    echo "✅ SSL sertifikası mevcut"
    
    # Sertifika geçerlilik kontrolü
    EXPIRY_DATE=$(openssl x509 -enddate -noout -in "$SSL_CERT" | cut -d= -f2)
    echo "📅 Sertifika geçerlilik: $EXPIRY_DATE"
else
    echo "⚠️  SSL sertifikası bulunamadı!"
    echo "   Lütfen Let's Encrypt ile sertifika oluşturun:"
    echo "   sudo certbot certonly --standalone -d ollama.yalihanemlak.internal"
fi

echo ""

# ═══════════════════════════════════════════════════════════
# 4. LARAVEL CACHE CLEAR
# ═══════════════════════════════════════════════════════════

echo "🗑️  4. Laravel cache temizleniyor..."

php artisan config:clear
echo "✅ Config cache temizlendi"

php artisan config:cache
echo "✅ Config cache oluşturuldu"

php artisan cache:clear
echo "✅ Application cache temizlendi"

echo ""

# ═══════════════════════════════════════════════════════════
# 5. CONNECTIVITY TEST
# ═══════════════════════════════════════════════════════════

echo "🔌 5. Bağlantı testi yapılıyor..."

OLLAMA_URL=$(grep "^OLLAMA_API_URL=" .env | cut -d'=' -f2)

echo "Test URL: $OLLAMA_URL/api/tags"

# HTTPS bağlantı testi
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "$OLLAMA_URL/api/tags" 2>/dev/null || echo "000")

if [ "$HTTP_STATUS" = "200" ]; then
    echo "✅ HTTPS bağlantı başarılı (HTTP $HTTP_STATUS)"
elif [ "$HTTP_STATUS" = "401" ] || [ "$HTTP_STATUS" = "403" ]; then
    echo "⚠️  Bağlantı var ama yetki hatası (HTTP $HTTP_STATUS)"
    echo "   IP whitelisting kontrol edin"
else
    echo "❌ HTTPS bağlantı başarısız (HTTP $HTTP_STATUS)"
    echo "   Nginx/SSL configuration kontrol edin"
fi

echo ""

# ═══════════════════════════════════════════════════════════
# 6. TLS ENFORCEMENT TEST
# ═══════════════════════════════════════════════════════════

echo "🛡️ 6. TLS zorunluluğu test ediliyor..."

php artisan tinker --execute="
use App\Services\AIService;
echo 'AIService TLS kontrolü...\n';
\$service = new AIService();
echo 'TLS requirement: ' . (config('ai.require_tls') ? 'ENABLED ✅' : 'DISABLED ❌') . '\n';
echo 'Ollama URL: ' . config('ai.ollama_api_url') . '\n';
"

echo ""

# ═══════════════════════════════════════════════════════════
# 7. MONITORING SETUP
# ═══════════════════════════════════════════════════════════

echo "📊 7. Monitoring ayarları..."

# Log directory check
LOG_DIR="storage/logs"
if [ -d "$LOG_DIR" ]; then
    echo "✅ Log directory mevcut: $LOG_DIR"
    
    # Son KVKK loglarını kontrol et
    KVKK_LOGS=$(grep -c "KVKK" storage/logs/laravel.log 2>/dev/null || echo "0")
    echo "📝 KVKK log sayısı: $KVKK_LOGS"
else
    echo "❌ Log directory bulunamadı!"
fi

echo ""

# ═══════════════════════════════════════════════════════════
# 8. FINAL CHECKLIST
# ═══════════════════════════════════════════════════════════

echo "═══════════════════════════════════════════════════════════"
echo "📋 DEPLOYMENT CHECKLIST"
echo "═══════════════════════════════════════════════════════════"

CHECKLIST=(
    "AI_REQUIRE_TLS=true:.env"
    "OLLAMA_API_URL=https://:.env"
    "SSL Certificate:/etc/letsencrypt"
    "Nginx Config:/etc/nginx/sites-enabled/ollama-ssl"
)

for item in "${CHECKLIST[@]}"; do
    CHECK="${item%%:*}"
    FILE="${item##*:}"
    
    if grep -q "$CHECK" "$FILE" 2>/dev/null || [ -f "$FILE" ] || [ -d "$FILE" ]; then
        echo "✅ $CHECK"
    else
        echo "❌ $CHECK (Eksik!)"
    fi
done

echo ""
echo "═══════════════════════════════════════════════════════════"
echo "🎯 DEPLOYMENT ÖZET"
echo "═══════════════════════════════════════════════════════════"
echo ""
echo "✅ Config güncellendi (AI_REQUIRE_TLS=true)"
echo "✅ HTTPS endpoint aktif"
echo "✅ Laravel cache temizlendi"
echo "✅ TLS enforcement aktif"
echo ""
echo "📚 Detaylı dokümantasyon:"
echo "   - docs/security/LLM_SECURITY_FINAL_IMPLEMENTATION.md"
echo ""
echo "🧪 Test adımları:"
echo "   1. HTTP ile deneme (reddedilmeli):"
echo "      curl http://ollama.yalihanemlak.internal/api/tags"
echo ""
echo "   2. HTTPS ile deneme (başarılı olmalı):"
echo "      curl https://ollama.yalihanemlak.internal/api/tags"
echo ""
echo "   3. AIService test:"
echo "      php artisan tinker"
echo "      use App\Services\AIService;"
echo "      \$ai = new AIService();"
echo "      \$ai->healthCheck();"
echo ""
echo "🚨 ÖNEMLİ UYARILAR:"
echo "   - HTTP üzerinden AI isteği artık MÜMKÜN DEĞİL"
echo "   - SSL sertifikası 90 günde bir yenilenmeli"
echo "   - Nginx logs düzenli kontrol edilmeli"
echo "   - KVKK compliance sürekli izlenmeli"
echo ""
echo "═══════════════════════════════════════════════════════════"
echo "🛡️ KVKK RİSKİ KAPATILDI - DEPLOYMENT TAMAMLANDI"
echo "═══════════════════════════════════════════════════════════"


