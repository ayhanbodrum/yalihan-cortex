#!/bin/bash

echo "═════════════════════════════════════════════════"
echo "🤖 Admin Panel Otomatik Crawler"
echo "═════════════════════════════════════════════════"
echo ""

if [ ! -f "scripts/admin-otomatik-test.mjs" ]; then
    echo "❌ scripts/admin-otomatik-test.mjs bulunamadı!"
    exit 1
fi

if ! command -v node &> /dev/null; then
    echo "❌ Node.js kurulu değil!"
    exit 1
fi

if [ ! -d "node_modules/puppeteer" ]; then
    echo "📦 Puppeteer kuruluyor..."
    npm install --save-dev puppeteer
    echo ""
fi

echo "✅ Hazırlık tamamlandı"
echo ""
echo "🚀 Crawler başlatılıyor..."
echo ""
echo "═════════════════════════════════════════════════"
echo ""

node scripts/admin-otomatik-test.mjs

EXIT_CODE=$?

echo ""
echo "═════════════════════════════════════════════════"
echo ""

if [ $EXIT_CODE -eq 0 ]; then
    echo "✅ Tüm testler başarılı!"
    echo ""
    echo "📋 Rapor: admin-test-report.md"
    echo "📸 Ekran görüntüleri: screenshots/admin-test/"
else
    echo "⚠️  Bazı testlerde hatalar bulundu"
    echo ""
    echo "📋 Detaylı rapor: admin-test-report.md"
    echo "📸 Hatalı sayfa görüntüleri: screenshots/admin-test/"
    echo ""
    echo "💡 Hataları görüntülemek için:"
    echo "   cat admin-test-report.md"
fi

echo ""
echo "✨ Test tamamlandı!"
echo ""
