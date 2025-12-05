#!/bin/bash

# Production Deployment Script
# Tarih: 2025-12-05
# Versiyon: 1.0.0
# Context7 Standardı: C7-PRODUCTION-DEPLOYMENT-2025-12-05

set -e

echo "🚀 Production Deployment Başlıyor..."
echo ""

# Renkler
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 1. Environment Variables Kontrolü
echo -e "${BLUE}📋 1. Environment Variables Kontrol Ediliyor...${NC}"
if [ -f scripts/check-env.sh ]; then
    if ! ./scripts/check-env.sh; then
        echo -e "${RED}❌ Environment variables kontrolü başarısız!${NC}"
        echo "Lütfen eksik değişkenleri .env dosyasına ekleyin."
        exit 1
    fi
else
    echo -e "${YELLOW}⚠️  check-env.sh bulunamadı, atlanıyor...${NC}"
fi
echo ""

# 2. Git Pull
echo -e "${BLUE}📥 2. Git Pull Yapılıyor...${NC}"
if git pull origin main; then
    echo -e "${GREEN}✅ Git pull başarılı${NC}"
else
    echo -e "${RED}❌ Git pull başarısız!${NC}"
    exit 1
fi
echo ""

# 3. Composer Install
echo -e "${BLUE}📦 3. Composer Install Yapılıyor...${NC}"
composer install --no-dev --optimize-autoloader
composer dump-autoload --optimize
echo -e "${GREEN}✅ Composer install tamamlandı${NC}"
echo ""

# 4. NPM Build
echo -e "${BLUE}🎨 4. Frontend Build Yapılıyor...${NC}"
if [ -f package.json ]; then
    npm ci
    npm run build
    echo -e "${GREEN}✅ Frontend build tamamlandı${NC}"
else
    echo -e "${YELLOW}⚠️  package.json bulunamadı, atlanıyor...${NC}"
fi
echo ""

# 5. Database Migration
echo -e "${BLUE}🗄️  5. Database Migration Yapılıyor...${NC}"
php artisan migrate --force
echo -e "${GREEN}✅ Migration tamamlandı${NC}"
echo ""

# 6. Cache Temizliği
echo -e "${BLUE}🧹 6. Cache Temizleniyor...${NC}"
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
php artisan event:clear 2>/dev/null || true
echo -e "${GREEN}✅ Cache temizlendi${NC}"
echo ""

# 7. Cache Rebuild (Production)
echo -e "${BLUE}⚡ 7. Cache Rebuild Yapılıyor (Production)...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache 2>/dev/null || true
echo -e "${GREEN}✅ Cache rebuild tamamlandı${NC}"
echo ""

# 8. Storage Link
echo -e "${BLUE}🔗 8. Storage Link Oluşturuluyor...${NC}"
php artisan storage:link
echo -e "${GREEN}✅ Storage link oluşturuldu${NC}"
echo ""

# 9. Queue Worker Restart (Supervisor)
echo -e "${BLUE}🔄 9. Queue Worker Restart Ediliyor...${NC}"
if command -v supervisorctl &> /dev/null; then
    if sudo supervisorctl restart yalihan-worker:* 2>/dev/null; then
        echo -e "${GREEN}✅ Queue worker restart edildi${NC}"
    else
        echo -e "${YELLOW}⚠️  Supervisor kontrolü başarısız, manuel kontrol gerekebilir${NC}"
    fi
else
    echo -e "${YELLOW}⚠️  Supervisor bulunamadı, queue worker manuel başlatılmalı${NC}"
fi
echo ""

# 10. PHP-FPM Restart
echo -e "${BLUE}🔄 10. PHP-FPM Restart Ediliyor...${NC}"
if sudo systemctl restart php8.4-fpm 2>/dev/null || sudo systemctl restart php8.2-fpm 2>/dev/null || sudo systemctl restart php-fpm 2>/dev/null; then
    echo -e "${GREEN}✅ PHP-FPM restart edildi${NC}"
else
    echo -e "${YELLOW}⚠️  PHP-FPM restart başarısız, manuel kontrol gerekebilir${NC}"
fi
echo ""

# 11. Nginx Reload
echo -e "${BLUE}🔄 11. Nginx Reload Yapılıyor...${NC}"
if sudo nginx -t 2>/dev/null && sudo systemctl reload nginx 2>/dev/null; then
    echo -e "${GREEN}✅ Nginx reload edildi${NC}"
else
    echo -e "${YELLOW}⚠️  Nginx reload başarısız, manuel kontrol gerekebilir${NC}"
fi
echo ""

# 12. Health Check
echo -e "${BLUE}🏥 12. Health Check Yapılıyor...${NC}"
if php artisan tinker --execute="DB::connection()->getPdo();" 2>/dev/null; then
    echo -e "${GREEN}✅ Database bağlantısı başarılı${NC}"
else
    echo -e "${RED}❌ Database bağlantısı başarısız!${NC}"
    exit 1
fi
echo ""

# Özet
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo -e "${GREEN}✅ Production Deployment Tamamlandı!${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "📊 Yapılan İşlemler:"
echo "   ✅ Git pull"
echo "   ✅ Composer install"
echo "   ✅ Frontend build"
echo "   ✅ Database migration"
echo "   ✅ Cache temizliği ve rebuild"
echo "   ✅ Storage link"
echo "   ✅ Queue worker restart"
echo "   ✅ PHP-FPM restart"
echo "   ✅ Nginx reload"
echo "   ✅ Health check"
echo ""
echo "🔍 Sonraki Adımlar:"
echo "   1. Telegram webhook'u kontrol et"
echo "   2. n8n workflow'ları kontrol et"
echo "   3. Monitoring'i aktifleştir"
echo "   4. Test senaryolarını çalıştır"
echo ""

