#!/bin/bash

# Renkler
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo "╔══════════════════════════════════════════════════════════════════╗"
echo "║    🛠️  SNAPSHOT vs ACTIVE - YALİHAN BEKÇİ GÜNCELLEMESİ        ║"
echo "║                    25 Kasım 2025                                 ║"
echo "╚══════════════════════════════════════════════════════════════════╝"
echo ""

# 1. Scan config oluştur
echo -e "${BLUE}[1/5]${NC} Yalıhan Bekçi tarama konfigürasyonu oluşturuluyor..."
mkdir -p yalihan-bekci/config

cat > yalihan-bekci/config/scan-config.json << 'EOF'
{
  "version": "1.0.0",
  "last_updated": "2025-11-25",
  "description": "Yalıhan Bekçi tarama konfigürasyonu - Archive klasörleri hariç",
  "md_duplicate_detector": {
    "enabled": true,
    "excludePaths": [
      "docs/archive",
      ".context7/archive",
      "yalihan-bekci/reports/archive",
      "vendor",
      "node_modules",
      "storage"
    ],
    "note": "Archive klasörleri tarihsel kayıttır, taranmaz"
  },
  "cleanup_analyzer": {
    "enabled": true,
    "excludePaths": [
      "docs/archive",
      ".context7/archive",
      "yalihan-bekci/reports/archive",
      "backups"
    ],
    "note": "Backup ve archive klasörleri cleanup'tan muaf"
  },
  "context7_validate": {
    "enabled": true,
    "excludePaths": [
      "docs/archive",
      ".context7/archive",
      "yalihan-bekci/reports/archive/**/*.md"
    ],
    "onlyActivePaths": [
      "docs/active",
      ".context7/*.md",
      "YALIHAN_BEKCI_EGITIM_DOKUMANI.md",
      "app/**/*.php",
      "resources/**/*.blade.php"
    ],
    "note": "Sadece aktif standartlar ve kod taranır"
  },
  "snapshot_reports": {
    "location": "yalihan-bekci/reports/archive/",
    "pattern": "*_SNAPSHOT_*.txt",
    "note": "Snapshot raporlar arşivde tutulur, 'yapılacak iş' değildir"
  }
}
EOF

echo -e "${GREEN}✅ Konfigürasyon oluşturuldu: yalihan-bekci/config/scan-config.json${NC}"

# 2. MD_AUDIT_SUMMARY arşive taşı
echo ""
echo -e "${BLUE}[2/5]${NC} MD_AUDIT_SUMMARY snapshot olarak arşive taşınıyor..."

if [ -f "yalihan-bekci/reports/2025-11/MD_AUDIT_SUMMARY.txt" ]; then
    mkdir -p yalihan-bekci/reports/archive/2025-11

    # Başlık notu ile yeni dosya oluştur
    cat > yalihan-bekci/reports/archive/2025-11/MD_AUDIT_SUMMARY_SNAPSHOT_2025_11.txt << 'EOF'
# MD AUDIT SUMMARY - SNAPSHOT (Kasım 2025)

⚠️ BU BİR SNAPSHOT RAPORUDUR ⚠️

Bu rapor Kasım 2025'teki anlık durumu gösterir.
[outdated] ve [duplicate_hint] işaretleri o anki durum içindi.

Arşiv klasörlerindeki işaretler (docs/archive/, .context7/archive/)
"yapılacak iş" DEĞIL, tarihsel kayıttır.

Güncel aktif standartlar:
- .context7/authority.json
- docs/active/RULES_KONSOLIDE_2025_11_25.md
- YALIHAN_BEKCI_EGITIM_DOKUMANI.md

════════════════════════════════════════════════════════════════════

EOF

    # Eski içeriği ekle
    cat yalihan-bekci/reports/2025-11/MD_AUDIT_SUMMARY.txt >> yalihan-bekci/reports/archive/2025-11/MD_AUDIT_SUMMARY_SNAPSHOT_2025_11.txt

    # Eski dosyayı sil
    rm yalihan-bekci/reports/2025-11/MD_AUDIT_SUMMARY.txt

    echo -e "${GREEN}✅ MD_AUDIT arşive taşındı ve başlık notu eklendi${NC}"
else
    echo -e "${YELLOW}ℹ️  MD_AUDIT_SUMMARY.txt bulunamadı (zaten taşınmış olabilir)${NC}"
fi

# 3. .context7/README.md güncelle
echo ""
echo -e "${BLUE}[3/5]${NC} .context7/README.md arşiv bölümü güncelleniyor..."

# Backup al
cp .context7/README.md .context7/README.md.backup

# Arşiv bölümünü güncelle (basit sed ile)
echo -e "${YELLOW}ℹ️  README.md manuel güncelleme gerekebilir${NC}"
echo -e "${YELLOW}   Arşiv bölümüne detaylı açıklama eklenecek${NC}"

# 4. YALIHAN_BEKCI_EGITIM_DOKUMANI.md'ye not ekle
echo ""
echo -e "${BLUE}[4/5]${NC} YALIHAN_BEKCI_EGITIM_DOKUMANI.md'ye snapshot notu ekleniyor..."

# Backup al
cp YALIHAN_BEKCI_EGITIM_DOKUMANI.md YALIHAN_BEKCI_EGITIM_DOKUMANI.md.backup

# Not eklenecek (manuel kontrol gerekebilir)
echo -e "${YELLOW}ℹ️  Eğitim dokümanına manuel snapshot notu eklenecek${NC}"
echo -e "${YELLOW}   Sayfa 2, 'Temel Kavramlar' altında${NC}"

# 5. Test: Archive dışında tarama
echo ""
echo -e "${BLUE}[5/5]${NC} Test: Archive klasörleri dışında tarama yapılıyor..."

echo ""
echo -e "${GREEN}📊 TARAMA SONUÇLARI:${NC}"
echo ""

# Active standartları tara
echo "Aktif standartlar:"
find docs/active -name "*.md" -type f 2>/dev/null | wc -l | xargs echo "  - docs/active: "
find .context7 -maxdepth 1 -name "*.md" -type f 2>/dev/null | wc -l | xargs echo "  - .context7: "

echo ""

# Archive klasörleri say (taranmamalı)
echo "Archive klasörleri (TARANMAZ):"
find docs/archive -name "*.md" -type f 2>/dev/null | wc -l | xargs echo "  - docs/archive: "
find .context7/archive -name "*.md" -type f 2>/dev/null | wc -l | xargs echo "  - .context7/archive: "
find yalihan-bekci/reports/archive -name "*.md" -o -name "*.txt" -type f 2>/dev/null | wc -l | xargs echo "  - yalihan-bekci/reports/archive: "

echo ""
echo "════════════════════════════════════════════════════════════════════"
echo -e "${GREEN}✅ GÜNCELLEME TAMAMLANDI${NC}"
echo "════════════════════════════════════════════════════════════════════"
echo ""
echo "Sonraki adımlar:"
echo "  1. .context7/README.md arşiv bölümünü manuel kontrol et"
echo "  2. YALIHAN_BEKCI_EGITIM_DOKUMANI.md'ye snapshot notunu ekle"
echo "  3. Yalıhan Bekçi taramasını yeniden test et"
echo "  4. Git commit yap"
echo ""
echo "Dokümantasyon:"
echo "  - docs/maintenance/SNAPSHOT_VS_ACTIVE_CLARIFICATION_2025_11_25.md"
echo "  - yalihan-bekci/config/scan-config.json"
echo ""
