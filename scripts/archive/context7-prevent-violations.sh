#!/bin/bash

# Context7 Kural İhlali Önleme Script'i
# Bu script, Context7 kurallarına aykırı kod yazılmasını önler

echo "🔍 Context7 Kural İhlali Kontrolü Başlatılıyor..."

# Renk kodları
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Hata sayacı
ERROR_COUNT=0

# 1. Database Field Naming Kontrolü
echo -e "\n${YELLOW}1. Database Field Naming Kontrolü${NC}"
echo "----------------------------------------"

# Yasak alan adları kontrolü
FORBIDDEN_FIELDS=("ilan_kategori_id" "durum" "is_active" "aktif" "sehir" "sehir_id" "bolge_id" "ad_soyad" "full_name")

for field in "${FORBIDDEN_FIELDS[@]}"; do
    if grep -r "$field" app/Http/Controllers/ app/Models/ resources/views/ --include="*.php" --include="*.blade.php" > /dev/null 2>&1; then
        echo -e "${RED}❌ YASAK ALAN ADI: $field${NC}"
        echo "   Dosyalar:"
        grep -r "$field" app/Http/Controllers/ app/Models/ resources/views/ --include="*.php" --include="*.blade.php" | head -5
        ((ERROR_COUNT++))
    else
        echo -e "${GREEN}✅ $field kullanımı yok${NC}"
    fi
done

# 2. User Role Filtering Kontrolü
echo -e "\n${YELLOW}2. User Role Filtering Kontrolü${NC}"
echo "----------------------------------------"

# Yasak kullanıcı filtreleme kontrolü
if grep -r "User::where('status', 1)->get()" app/Http/Controllers/ --include="*.php" > /dev/null 2>&1; then
    echo -e "${RED}❌ YASAK: Tüm kullanıcıları getirme${NC}"
    echo "   Dosyalar:"
    grep -r "User::where('status', 1)->get()" app/Http/Controllers/ --include="*.php"
    ((ERROR_COUNT++))
else
    echo -e "${GREEN}✅ Doğru kullanıcı filtreleme${NC}"
fi

# 3. Storage Link Kontrolü
echo -e "\n${YELLOW}3. Storage Link Kontrolü${NC}"
echo "----------------------------------------"

if [ -L "public/storage" ]; then
    STORAGE_TARGET=$(readlink public/storage)
    CURRENT_DIR=$(pwd)
    EXPECTED_TARGET="$CURRENT_DIR/storage/app/public"

    if [ "$STORAGE_TARGET" = "$EXPECTED_TARGET" ]; then
        echo -e "${GREEN}✅ Storage link doğru${NC}"
    else
        echo -e "${RED}❌ Storage link yanlış${NC}"
        echo "   Mevcut: $STORAGE_TARGET"
        echo "   Beklenen: $EXPECTED_TARGET"
        ((ERROR_COUNT++))
    fi
else
    echo -e "${RED}❌ Storage link bulunamadı${NC}"
    ((ERROR_COUNT++))
fi

# 4. Error Handling Kontrolü
echo -e "\n${YELLOW}4. Error Handling Kontrolü${NC}"
echo "----------------------------------------"

# Storage::url kullanımı kontrolü
if grep -r "Storage::url" resources/views/ --include="*.blade.php" | grep -v "Storage::exists" > /dev/null 2>&1; then
    echo -e "${RED}❌ YASAK: Storage::url error handling olmadan${NC}"
    echo "   Dosyalar:"
    grep -r "Storage::url" resources/views/ --include="*.blade.php" | grep -v "Storage::exists" | head -3
    ((ERROR_COUNT++))
else
    echo -e "${GREEN}✅ Storage::url error handling ile kullanılıyor${NC}"
fi

# 5. API Key Kontrolü
echo -e "\n${YELLOW}5. API Key Kontrolü${NC}"
echo "----------------------------------------"

# Google Maps API key kontrolü
if grep -r "maps.googleapis.com" resources/views/ --include="*.blade.php" | grep -v "config.*api_key" > /dev/null 2>&1; then
    echo -e "${RED}❌ YASAK: Hardcoded API key${NC}"
    echo "   Dosyalar:"
    grep -r "maps.googleapis.com" resources/views/ --include="*.blade.php" | grep -v "config.*api_key"
    ((ERROR_COUNT++))
else
    echo -e "${GREEN}✅ API key config'den alınıyor${NC}"
fi

# 6. Context7 Compliance Özeti
echo -e "\n${YELLOW}6. Context7 Compliance Özeti${NC}"
echo "----------------------------------------"

if [ $ERROR_COUNT -eq 0 ]; then
    echo -e "${GREEN}🎉 TÜM KONTROLLER BAŞARILI!${NC}"
    echo -e "${GREEN}✅ Context7 kurallarına %100 uyumlu${NC}"
    exit 0
else
    echo -e "${RED}❌ $ERROR_COUNT KURAL İHLALİ TESPİT EDİLDİ!${NC}"
    echo -e "${RED}🚫 Commit iptal edildi${NC}"
    echo -e "${YELLOW}💡 Düzeltmeler için: ./scripts/context7-auto-fix.sh${NC}"
    exit 1
fi
