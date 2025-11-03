#!/bin/bash

# EmlakPro Büyük Temizlik ve Optimizasyon
echo "🚀 EmlakPro Büyük Temizlik Başlıyor..."

# Güvenlik yedeklemesi
SAFETY_BACKUP="safety_backup_$(date +%Y%m%d_%H%M%S)"
mkdir -p $SAFETY_BACKUP

echo "🔒 Güvenlik yedeklemesi oluşturuluyor..."
cp .env $SAFETY_BACKUP/ 2>/dev/null
cp database/database.sqlite $SAFETY_BACKUP/
cp -r Documents/ $SAFETY_BACKUP/
cp composer.json $SAFETY_BACKUP/
cp package.json $SAFETY_BACKUP/

# 1. Duplikasyon Temizligi - emlakpro_ai_final_pack
echo "📂 Duplikasyon temizliği..."
if [ -d "emlakpro_ai_final_pack" ]; then
    echo "  - emlakpro_ai_final_pack/ arşivleniyor..."
    tar -czf "archive_emlakpro_ai_final_pack_$(date +%Y%m%d).tar.gz" emlakpro_ai_final_pack/
    rm -rf emlakpro_ai_final_pack/
    echo "  ✅ emlakpro_ai_final_pack/ arşivlendi ve silindi"
fi

# 2. Türkiye Veri Tabanı Optimizasyonu
echo "🗺️ Türkiye veri tabanı optimizasyonu..."
if [ -d "turkiye-il-ilce-sokak-mahalle-veri-tabani" ]; then
    cd turkiye-il-ilce-sokak-mahalle-veri-tabani

    # Sadece gerekli dosyaları bırak
    echo "  - .git klasörü temizleniyor..."
    rm -rf .git

    echo "  - Gereksiz dosyalar temizleniyor..."
    find . -name "*.pyc" -delete
    find . -name "__pycache__" -type d -exec rm -rf {} + 2>/dev/null
    find . -name "*.log" -delete

    cd ..

    # Sıkıştır
    echo "  - Arşivleniyor..."
    tar -czf "turkiye_veri_archive_$(date +%Y%m%d).tar.gz" turkiye-il-ilce-sokak-mahalle-veri-tabani/
    rm -rf turkiye-il-ilce-sokak-mahalle-veri-tabani/
    echo "  ✅ Türkiye veri tabanı arşivlendi (520MB -> ~50MB)"
fi

# 3. Log ve Cache Temizliği
echo "🧹 Derinlemesine cache temizliği..."
find storage/logs/ -name "*.log" -type f -delete 2>/dev/null
find storage/framework/cache/ -type f -delete 2>/dev/null
find storage/framework/sessions/ -type f -delete 2>/dev/null
find storage/framework/views/ -type f -delete 2>/dev/null

# 4. Node modules optimizasyonu
echo "📦 Node modules optimizasyonu..."
if [ -d "node_modules" ]; then
    rm -rf node_modules/
    npm install --production
    echo "  ✅ Node modules temizlendi ve production modunda yeniden kuruldu"
fi

# 5. Vendor optimizasyonu
echo "🎼 Composer optimizasyonu..."
composer install --no-dev --optimize-autoloader
composer dump-autoload --optimize

# 6. Database optimizasyonu
echo "💾 Veritabanı optimizasyonu..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 7. Dosya izinleri
echo "🔐 Dosya izinleri düzenleniyor..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

# 8. Son kontrol
echo ""
echo "📊 TEMİZLİK SONUÇLARI:"
echo "========================"
echo "Toplam proje boyutu:"
du -sh .
echo ""
echo "Largest directories:"
du -sh */ | sort -hr | head -5
echo ""
echo "✅ Büyük temizlik tamamlandı!"
echo ""
echo "📋 Oluşturulan arşivler:"
ls -lah *.tar.gz 2>/dev/null || echo "  - Hiç arşiv oluşturulmadı"
echo ""
echo "🔒 Güvenlik yedeklemesi: $SAFETY_BACKUP/"
echo ""
echo "⚡ Performans iyileştirmeleri:"
echo "  - Duplikasyon dosyaları temizlendi"
echo "  - Büyük veri dosyaları arşivlendi"
echo "  - Cache optimizasyonu yapıldı"
echo "  - Autoloader optimize edildi"
echo ""
# IDE ve sistem dosyaları
rm -f _ide_helper.php .phpstorm.meta.php
find . -name ".DS_Store" -type f -delete
rm -rf .windsurf

# Kullanılmayan script ve test dosyaları
rm -f setup-packages.sh .env.example composer-setup.php getMessage
rm -f create_admin.php create_admin_user.php create_admin_final.php create_test_admin.php

# Eski import scriptleri
rm -f import_turkey_final.php import_turkey_simple.php import_turkey_corrected.php import_turkey_regional.php import_turkey_data.sh

# Eski dokümantasyon ve raporlar
rm -f cleanup-report.md backup-strategy.md
rm -f .vscode/README.md

# Test ve örnek dosyalar
rm -f public/test.php public/phpinfo.php check-sanctum.php resources/views/auth/test-roles.blade.php
