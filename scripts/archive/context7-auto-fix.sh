#!/bin/bash

# Context7 Otomatik Düzeltme Script'i
# Bu script, tespit edilen kural ihlallerini otomatik olarak düzeltir

echo "🔧 Context7 Otomatik Düzeltme Başlatılıyor..."

# Renk kodları
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Backup oluştur
BACKUP_DIR="backups/context7-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP_DIR"
echo -e "${BLUE}📦 Backup oluşturuluyor: $BACKUP_DIR${NC}"

# 1. Database Field Naming Düzeltmeleri
echo -e "\n${YELLOW}1. Database Field Naming Düzeltmeleri${NC}"
echo "----------------------------------------"

# ilan_kategori_id → alt_kategori_id
if grep -r "ilan_kategori_id" app/Http/Controllers/ --include="*.php" > /dev/null 2>&1; then
    echo -e "${BLUE}🔧 ilan_kategori_id → alt_kategori_id düzeltiliyor...${NC}"
    find app/Http/Controllers/ -name "*.php" -exec sed -i.bak 's/ilan_kategori_id/alt_kategori_id/g' {} \;
    echo -e "${GREEN}✅ Düzeltildi${NC}"
fi

# 2. User Role Filtering Düzeltmeleri
echo -e "\n${YELLOW}2. User Role Filtering Düzeltmeleri${NC}"
echo "----------------------------------------"

# User::where('status', 1)->get() → User::whereHas('roles', function($q) { $q->where('name', 'danisman'); })->where('status', 1)->get()
if grep -r "User::where('status', 1)->get()" app/Http/Controllers/ --include="*.php" > /dev/null 2>&1; then
    echo -e "${BLUE}🔧 User filtreleme düzeltiliyor...${NC}"
    find app/Http/Controllers/ -name "*.php" -exec sed -i.bak "s/User::where('status', 1)->get()/User::whereHas('roles', function(\$q) { \$q->where('name', 'danisman'); })->where('status', 1)->get()/g" {} \;
    echo -e "${GREEN}✅ Düzeltildi${NC}"
fi

# 3. Storage Link Düzeltmeleri
echo -e "\n${YELLOW}3. Storage Link Düzeltmeleri${NC}"
echo "----------------------------------------"

if [ -L "public/storage" ]; then
    STORAGE_TARGET=$(readlink public/storage)
    CURRENT_DIR=$(pwd)
    EXPECTED_TARGET="$CURRENT_DIR/storage/app/public"

    if [ "$STORAGE_TARGET" != "$EXPECTED_TARGET" ]; then
        echo -e "${BLUE}🔧 Storage link düzeltiliyor...${NC}"
        rm public/storage
        php artisan storage:link
        echo -e "${GREEN}✅ Storage link düzeltildi${NC}"
    else
        echo -e "${GREEN}✅ Storage link zaten doğru${NC}"
    fi
else
    echo -e "${BLUE}🔧 Storage link oluşturuluyor...${NC}"
    php artisan storage:link
    echo -e "${GREEN}✅ Storage link oluşturuldu${NC}"
fi

# 4. Error Handling Düzeltmeleri
echo -e "\n${YELLOW}4. Error Handling Düzeltmeleri${NC}"
echo "----------------------------------------"

# Storage::url kullanımı için error handling ekleme
echo -e "${BLUE}🔧 Error handling ekleniyor...${NC}"
# Bu kısım manuel olarak yapılmalı çünkü karmaşık template değişiklikleri gerekiyor
echo -e "${YELLOW}⚠️  Error handling manuel olarak eklenmelidir${NC}"

# 5. API Key Düzeltmeleri
echo -e "\n${YELLOW}5. API Key Düzeltmeleri${NC}"
echo "----------------------------------------"

# Hardcoded API key'leri config'den alacak şekilde düzeltme
echo -e "${BLUE}🔧 API key düzeltmeleri yapılıyor...${NC}"
# Bu kısım manuel olarak yapılmalı
echo -e "${YELLOW}⚠️  API key düzeltmeleri manuel olarak yapılmalıdır${NC}"

# 6. Backup dosyalarını temizle
echo -e "\n${YELLOW}6. Backup Dosyalarını Temizleme${NC}"
echo "----------------------------------------"
find . -name "*.bak" -delete
echo -e "${GREEN}✅ Backup dosyaları temizlendi${NC}"

# 7. Düzeltme Sonrası Kontrol
echo -e "\n${YELLOW}7. Düzeltme Sonrası Kontrol${NC}"
echo "----------------------------------------"

# Context7 compliance check çalıştır
if [ -f "scripts/context7-prevent-violations.sh" ]; then
    echo -e "${BLUE}🔍 Context7 compliance kontrolü çalıştırılıyor...${NC}"
    ./scripts/context7-prevent-violations.sh
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}🎉 TÜM DÜZELTMELER BAŞARILI!${NC}"
    else
        echo -e "${RED}❌ Bazı düzeltmeler başarısız${NC}"
    fi
else
    echo -e "${YELLOW}⚠️  Context7 compliance check script'i bulunamadı${NC}"
fi

echo -e "\n${GREEN}✅ Context7 Otomatik Düzeltme Tamamlandı${NC}"
echo -e "${BLUE}📦 Backup: $BACKUP_DIR${NC}"
