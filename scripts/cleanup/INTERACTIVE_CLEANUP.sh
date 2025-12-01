#!/bin/bash

# Renkler
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Başlık
clear
cat << 'EOF'
╔══════════════════════════════════════════════════════════════════╗
║           🧹 YALİHAN EMLAK TEMİZLİK KONTROL PANELİ             ║
║                    25 Kasım 2025                                 ║
╚══════════════════════════════════════════════════════════════════╝
EOF

echo ""
echo -e "${CYAN}📊 MEVCUT DURUM RAPORU:${NC}"
echo "════════════════════════════════════════════════════════════════"

# Hesaplamalar
ARCHIVE_SIZE=$(du -sh archive/ 2>/dev/null | awk '{print $1}')
DOCS_ARCHIVE_SIZE=$(du -sh docs/archive/ 2>/dev/null | awk '{print $1}')
SCREENSHOTS_SIZE=$(du -sh screenshots/ 2>/dev/null | awk '{print $1}')
VSCODE_SIZE=$(du -sh .vscode/ 2>/dev/null | awk '{print $1}')
CURSOR_SIZE=$(du -sh .cursor/ 2>/dev/null | awk '{print $1}')
YALIHAN_BEKCI_SIZE=$(du -sh .yalihan-bekci/ 2>/dev/null | awk '{print $1}')
VENDOR_SIZE=$(du -sh vendor/ 2>/dev/null | awk '{print $1}')
NODE_MODULES_SIZE=$(du -sh node_modules/ 2>/dev/null | awk '{print $1}')
TOTAL_SIZE=$(du -sh . 2>/dev/null | awk '{print $1}')

echo -e "${YELLOW}SİLİNEBİLECEK KLASÖRLER:${NC}"
echo "  archive/                 : $ARCHIVE_SIZE"
echo "  docs/archive/            : $DOCS_ARCHIVE_SIZE"
echo "  screenshots/             : $SCREENSHOTS_SIZE"
echo "  .vscode/                 : $VSCODE_SIZE"
echo "  .cursor/                 : $CURSOR_SIZE"
echo "  .yalihan-bekci/          : $YALIHAN_BEKCI_SIZE"

echo ""
echo -e "${BLUE}REBUILD EDİLEBİLECEK KLASÖRLER:${NC}"
echo "  vendor/                  : $VENDOR_SIZE"
echo "  node_modules/            : $NODE_MODULES_SIZE"

echo ""
echo -e "${GREEN}PROJE TOPLAMI:${NC}"
echo "  Toplam boyut             : $TOTAL_SIZE"

echo ""
echo "════════════════════════════════════════════════════════════════"

# Seçenekler
cat << 'EOF'

🎯 TEMİZLİK SEÇENEKLERİ:
════════════════════════════════════════════════════════════════

1️⃣  HIZLI TEMİZLİK (Düşük Risk - 4.7 MB)
    ├─ archive/                (228 KB)
    ├─ docs/archive/           (4.5 MB)
    └─ Toplam: ~4.7 MB

2️⃣  ORTA TEMİZLİK (Orta Risk - 28.7 MB)
    ├─ Hızlı +
    ├─ screenshots/            (23 MB)
    ├─ .yalihan-bekci/         (3.7 MB)
    └─ Toplam: ~28.7 MB

3️⃣  İLERİ TEMİZLİK (Yüksek Risk - 123.7 MB)
    ├─ Orta +
    ├─ .vscode/                (71 MB)
    ├─ .cursor/                (26 MB)
    ├─ git history cleanup     (~26 MB)
    └─ Toplam: ~123.7 MB

4️⃣  RADICAL (Maksimum - 538 MB - DIKKAT!)
    ├─ Advanced +
    ├─ vendor/                 (321 MB)
    ├─ node_modules/           (217 MB)
    └─ Toplam: ~538 MB (rebuild gerekli)

5️⃣  ÖZEL SEÇİM (Manuel seçim)

6️⃣  SADECE RAPOR (hiçbir şey silme)

0️⃣  ÇIKIS
════════════════════════════════════════════════════════════════

EOF

read -p "Seçiminizi yapın (0-6): " choice

case $choice in
    1)
        echo -e "\n${CYAN}🧹 HIZLI TEMİZLİK BAŞLANILIYOR...${NC}\n"
        quick_cleanup
        ;;
    2)
        echo -e "\n${YELLOW}⚠️  ORTA TEMİZLİK BAŞLANILIYOR...${NC}\n"
        medium_cleanup
        ;;
    3)
        echo -e "\n${RED}⚠️  İLERİ TEMİZLİK BAŞLANILIYOR...${NC}\n"
        read -p "Emin misiniz? (evet/hayır): " confirm
        if [ "$confirm" = "evet" ]; then
            advanced_cleanup
        else
            echo "İptal edildi."
        fi
        ;;
    4)
        echo -e "\n${RED}🚨 RADICAL TEMİZLİK - SİSTEM YENİDEN KURULACAK${NC}\n"
        read -p "ÇOĞU KÜTÜPHANE SİLİNECEK! Emin misiniz? (evet/hayır): " confirm
        if [ "$confirm" = "evet" ]; then
            radical_cleanup
        else
            echo "İptal edildi."
        fi
        ;;
    5)
        echo -e "\n${CYAN}📋 MANUEL SEÇİM MODU${NC}\n"
        custom_cleanup
        ;;
    6)
        echo -e "\n${BLUE}📊 RAPOR MODU (Hiçbir şey silinmeyecek)${NC}\n"
        report_only
        ;;
    0)
        echo "Çıkılıyor..."
        exit 0
        ;;
    *)
        echo -e "${RED}Geçersiz seçim!${NC}"
        exit 1
        ;;
esac

# Fonksiyonlar

quick_cleanup() {
    echo "Yedek oluşturuluyor..."
    tar -czf backups/backup-$(date +%Y%m%d-%H%M%S).tar.gz \
        archive/ docs/archive/ 2>/dev/null

    echo "archive/ siliniyor..."
    rm -rf archive/

    echo "docs/archive/ siliniyor..."
    rm -rf docs/archive/

    echo -e "\n${GREEN}✅ HIZLI TEMİZLİK TAMAMLANDI${NC}"
    show_summary "quick"
}

medium_cleanup() {
    echo "Yedek oluşturuluyor..."
    tar -czf backups/backup-$(date +%Y%m%d-%H%M%S).tar.gz \
        archive/ docs/archive/ screenshots/ .yalihan-bekci/ 2>/dev/null

    echo "archive/ siliniyor..."
    rm -rf archive/
    echo "docs/archive/ siliniyor..."
    rm -rf docs/archive/
    echo "screenshots/ siliniyor..."
    rm -rf screenshots/
    echo ".yalihan-bekci/ siliniyor..."
    rm -rf .yalihan-bekci/

    echo -e "\n${GREEN}✅ ORTA TEMİZLİK TAMAMLANDI${NC}"
    show_summary "medium"
}

advanced_cleanup() {
    echo "Yedek oluşturuluyor..."
    tar -czf backups/backup-$(date +%Y%m%d-%H%M%S).tar.gz \
        archive/ docs/archive/ screenshots/ .yalihan-bekci/ \
        .vscode/ .cursor/ 2>/dev/null

    echo "Hızlı temizlik yapılıyor..."
    rm -rf archive/ docs/archive/ screenshots/ .yalihan-bekci/

    echo ".vscode/ temizleniyor..."
    rm -rf .vscode/

    echo ".cursor/ temizleniyor..."
    rm -rf .cursor/

    echo "Git geçmiş temizleniyor..."
    git reflog expire --expire=now --all
    git gc --prune=now

    echo -e "\n${GREEN}✅ İLERİ TEMİZLİK TAMAMLANDI${NC}"
    show_summary "advanced"
}

radical_cleanup() {
    echo "UYARI: Bu işlem vendor/ ve node_modules/ silecektir!"
    echo "Yedek oluşturuluyor..."
    tar -czf backups/backup-full-$(date +%Y%m%d-%H%M%S).tar.gz \
        archive/ docs/archive/ screenshots/ .yalihan-bekci/ \
        .vscode/ .cursor/ vendor/ node_modules/ 2>/dev/null

    echo "Advanced temizlik yapılıyor..."
    rm -rf archive/ docs/archive/ screenshots/ .yalihan-bekci/ \
           .vscode/ .cursor/

    git reflog expire --expire=now --all
    git gc --prune=now

    echo "vendor/ ve node_modules/ siliniyor..."
    rm -rf vendor/ node_modules/

    echo -e "\n${YELLOW}Yeniden kuruluyor...${NC}"
    composer install
    npm install

    echo -e "\n${GREEN}✅ RADICAL TEMİZLİK + REBUILD TAMAMLANDI${NC}"
    show_summary "radical"
}

custom_cleanup() {
    echo "📋 Manuel seçim modunda her klasör için karar verin:"
    echo ""

    items=(
        "archive/ (228 KB)"
        "docs/archive/ (4.5 MB)"
        "screenshots/ (23 MB)"
        ".yalihan-bekci/ (3.7 MB)"
        ".vscode/ (71 MB)"
        ".cursor/ (26 MB)"
    )

    to_delete=""
    total_cleanup=0

    for item in "${items[@]}"; do
        read -p "Sil: $item? (e/h): " answer
        if [ "$answer" = "e" ] || [ "$answer" = "E" ]; then
            dirname=$(echo "$item" | cut -d' ' -f1)
            to_delete="$to_delete $dirname"
            # Boyutu hesapla (basit)
        fi
    done

    if [ -z "$to_delete" ]; then
        echo "Hiçbir şey silinmedi."
        return
    fi

    echo -e "\n${YELLOW}Yedek oluşturuluyor...${NC}"
    tar -czf backups/backup-custom-$(date +%Y%m%d-%H%M%S).tar.gz \
        $to_delete 2>/dev/null

    echo -e "${YELLOW}Siliniyor...${NC}"
    for item in $to_delete; do
        echo "  Siliniyor: $item"
        rm -rf "$item"
    done

    echo -e "\n${GREEN}✅ MANUEL TEMİZLİK TAMAMLANDI${NC}"
}

report_only() {
    cat << 'EOF'

════════════════════════════════════════════════════════════════
📊 DETAYLI RAPOR (Hiçbir şey silinmedi)
════════════════════════════════════════════════════════════════

✨ ÖNERİLER:

1. archive/ ve docs/archive/ (4.7 MB) - GÜVENLİ, hemen sil
2. screenshots/ (23 MB) - İhtiyacın varsa yedekle, sonra sil
3. .vscode/ ve .cursor/ - IDE specific, isteğe göre sil
4. vendor/ ve node_modules/ - Silebilirsin, "npm install" + "composer install"

📌 NOT:
   - Sildiğin şeyler backups/ klasöründe .tar.gz olarak tutulacak
   - Git geçmişi tamamen silinmez (başka seçeneğe git)

EOF
}

show_summary() {
    mode=$1
    echo ""
    echo "════════════════════════════════════════════════════════════════"
    echo -e "${GREEN}📊 SONUÇ RAPORU${NC}"
    echo "════════════════════════════════════════════════════════════════"

    NEW_SIZE=$(du -sh . 2>/dev/null | awk '{print $1}')

    echo "Yeni toplam boyut: $NEW_SIZE"
    echo "Yedek konumu: backups/"
    echo ""

    if [ -n "$(ls backups/*.tar.gz 2>/dev/null | tail -1)" ]; then
        backup_size=$(ls -lh backups/*.tar.gz 2>/dev/null | tail -1 | awk '{print $5}')
        echo "Son yedek: $backup_size"
    fi
}
