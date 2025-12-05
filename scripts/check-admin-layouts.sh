#!/bin/bash
# Admin Layout Tutarlılık Kontrol Scripti
# Context7 Standard: Tüm admin sayfaları admin.layouts.admin kullanmalı

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo "🔍 Admin Layout Tutarlılık Kontrolü"
echo "====================================="
echo ""

ERRORS=0
WARNINGS=0

# Geçersiz layout kullanımlarını bul
INVALID_LAYOUTS=$(grep -r "@extends('admin.layouts.\(neo\|app\)')" resources/views/admin/ --include="*.blade.php" 2>/dev/null || true)

if [ -n "$INVALID_LAYOUTS" ]; then
    echo -e "${RED}❌ HATA: Geçersiz admin layout kullanımları bulundu!${NC}"
    echo ""
    echo "$INVALID_LAYOUTS" | while IFS= read -r line; do
        echo -e "${RED}  $line${NC}"
        ERRORS=$((ERRORS + 1))
    done
    echo ""
    echo -e "${GREEN}✅ ÇÖZÜM: Tüm admin sayfaları @extends('admin.layouts.admin') kullanmalı${NC}"
    echo ""
else
    echo -e "${GREEN}✅ Tüm admin sayfaları doğru layout kullanıyor${NC}"
fi

# Layout dosyasının varlığını kontrol et
if [ ! -f "resources/views/admin/layouts/admin.blade.php" ]; then
    echo -e "${RED}❌ HATA: admin.layouts.admin dosyası bulunamadı!${NC}"
    ERRORS=$((ERRORS + 1))
else
    echo -e "${GREEN}✅ admin.layouts.admin dosyası mevcut${NC}"
fi

# Özet
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
if [ $ERRORS -eq 0 ]; then
    echo -e "${GREEN}✅ TÜM KONTROLLER BAŞARILI!${NC}"
    exit 0
else
    echo -e "${RED}❌ ${ERRORS} HATA bulundu${NC}"
    exit 1
fi

