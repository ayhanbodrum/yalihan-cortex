#!/bin/bash

# Environment Variables Kontrol Script'i
# Production deployment öncesi kontrol için

echo "🔍 Environment Variables Kontrolü..."
echo ""

# .env dosyası var mı?
if [ ! -f .env ]; then
    echo "❌ .env dosyası bulunamadı!"
    exit 1
fi

echo "✅ .env dosyası bulundu"
echo ""

# Gerekli değişkenler
REQUIRED_VARS=(
    "APP_KEY"
    "APP_ENV"
    "APP_URL"
    "DB_DATABASE"
    "DB_USERNAME"
    "DB_PASSWORD"
    "TELEGRAM_BOT_TOKEN"
    "N8N_WEBHOOK_SECRET"
)

MISSING_VARS=()

for var in "${REQUIRED_VARS[@]}"; do
    if grep -q "^${var}=" .env && ! grep -q "^${var}=$" .env && ! grep -q "^${var}=\s*$" .env; then
        echo "✅ $var"
    else
        echo "❌ $var EKSİK veya BOŞ!"
        MISSING_VARS+=("$var")
    fi
done

echo ""

# Opsiyonel ama önerilen değişkenler
OPTIONAL_VARS=(
    "DEEPSEEK_API_KEY"
    "OPENAI_API_KEY"
    "GEMINI_API_KEY"
    "OLLAMA_URL"
    "ANYTHINGLLM_URL"
    "REDIS_HOST"
    "MAIL_HOST"
)

echo "📋 Opsiyonel Değişkenler:"
for var in "${OPTIONAL_VARS[@]}"; do
    if grep -q "^${var}=" .env && ! grep -q "^${var}=$" .env; then
        echo "✅ $var"
    else
        echo "⚠️  $var (opsiyonel)"
    fi
done

echo ""

# Sonuç
if [ ${#MISSING_VARS[@]} -eq 0 ]; then
    echo "✅ Tüm gerekli değişkenler mevcut!"
    exit 0
else
    echo "❌ Eksik değişkenler: ${MISSING_VARS[*]}"
    exit 1
fi

