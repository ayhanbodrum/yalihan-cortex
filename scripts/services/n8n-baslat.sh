#!/bin/bash

echo "🔄 n8n Kurulum ve Başlatma"
echo "═══════════════════════════════════════"
echo ""

if ! command -v docker &> /dev/null; then
    echo "❌ Docker kurulu değil!"
    echo "Docker'ı kurmak için: https://docs.docker.com/get-docker/"
    exit 1
fi

echo "✅ Docker kurulu"
echo ""

echo "📦 n8n container'ı başlatılıyor..."
echo ""

docker-compose -f docker-compose.n8n.yml up -d

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ n8n başarıyla başlatıldı!"
    echo ""
    echo "╔═══════════════════════════════════════════════════╗"
    echo "║   🎉 n8n HAZIR                                    ║"
    echo "╠═══════════════════════════════════════════════════╣"
    echo "║                                                   ║"
    echo "║  🌐 URL: http://localhost:5678                    ║"
    echo "║  👤 Kullanıcı: admin                              ║"
    echo "║  🔑 Şifre: admin123                               ║"
    echo "║                                                   ║"
    echo "╚═══════════════════════════════════════════════════╝"
    echo ""
    echo "📋 Sonraki Adımlar:"
    echo ""
    echo "1. Tarayıcıda aç: http://localhost:5678"
    echo "2. admin / admin123 ile giriş yap"
    echo "3. İlk workflow'u oluştur"
    echo "4. Laravel'den test et:"
    echo "   php test-n8n-integration.php"
    echo ""
    echo "📚 Dokümantasyon:"
    echo "   docs/integrations/n8n-entegrasyonu.md"
    echo ""
    echo "🛑 Durdurmak için:"
    echo "   docker-compose -f docker-compose.n8n.yml down"
    echo ""
else
    echo ""
    echo "❌ n8n başlatılamadı!"
    echo "Hata loglarını kontrol et:"
    echo "   docker-compose -f docker-compose.n8n.yml logs"
    echo ""
    exit 1
fi
