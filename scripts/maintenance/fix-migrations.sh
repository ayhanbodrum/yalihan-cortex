#!/bin/bash

# Migration Syntax Auto-Fixer Wrapper Script
# Otomatik migration syntax düzeltme ve Context7 uyumluluk kontrolü
# Kullanım: ./scripts/fix-migrations.sh

echo "🚀 Migration Syntax Auto-Fixer başlatılıyor..."

# Ana dizinde olduğumuzdan emin ol
if [ ! -f "artisan" ]; then
    echo "❌ Hata: Laravel root dizininde değilsiniz!"
    exit 1
fi

# PHP script'i çalıştır
echo "🔧 Migration dosyaları düzeltiliyor..."
php scripts/migration-syntax-auto-fixer.php

echo ""
echo "📋 Context7 uyumluluk kontrolü yapılıyor..."

# Context7 kontrolü
php artisan context7:check

echo ""
echo "🎯 Artisan migrate kontrolü yapılıyor..."

# Migration syntax kontrolü
php artisan migrate --pretend 2>/dev/null

if [ $? -eq 0 ]; then
    echo "✅ Migration dosyaları syntax açısından temiz!"
else
    echo "⚠️ Migration dosyalarında hala syntax hataları olabilir."
    echo "   Manuel kontrol gerekebilir."
fi

echo ""
echo "🎉 Otomatik düzeltme işlemi tamamlandı!"
echo "   Eğer hala hatalar varsa, manuel müdahale gerekebilir."
