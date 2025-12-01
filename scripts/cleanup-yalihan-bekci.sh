#!/bin/bash

# Yalıhan Bekçi Dosya Temizlik Script'i
# Tarih: 30 Kasım 2025
# Amaç: Gereksiz dosyaları temizle, klasör yapısını optimize et

set -e  # Hata durumunda dur

echo "🧹 Yalıhan Bekçi Dosya Temizliği Başlıyor..."
echo "================================================"
echo ""

# Proje root dizini
PROJECT_ROOT="/Users/macbookpro/Projects/yalihanai"
cd "$PROJECT_ROOT"

# Backup oluştur
echo "📦 Backup oluşturuluyor..."
BACKUP_FILE="yalihan-bekci-backup-$(date +%Y%m%d-%H%M%S).tar.gz"
tar -czf "$BACKUP_FILE" .yalihan-bekci/
echo "✅ Backup oluşturuldu: $BACKUP_FILE"
echo ""

# Başlangıç istatistikleri
echo "📊 Başlangıç İstatistikleri:"
echo "   - Ana dizin: $(ls -1 .yalihan-bekci/*.md 2>/dev/null | wc -l | tr -d ' ') dosya"
echo "   - Reports: $(ls -1 .yalihan-bekci/reports/ 2>/dev/null | wc -l | tr -d ' ') dosya"
echo "   - Knowledge: $(ls -1 .yalihan-bekci/knowledge/ 2>/dev/null | wc -l | tr -d ' ') dosya"
echo "   - Toplam boyut: $(du -sh .yalihan-bekci/ | cut -f1)"
echo ""

# 1. Ana dizin temizliği
echo "🗑️  1. Ana dizin temizleniyor..."
cd .yalihan-bekci/

# 11 Kasım tarihli günlük raporları sil
DELETED_COUNT=0

# Tüm 2025-11-11 tarihli dosyaları sil
for file in *_2025-11-11.md; do
    if [ -f "$file" ]; then
        rm -f "$file"
        ((DELETED_COUNT++))
    fi
done

# Belirli pattern'lerdeki dosyaları sil
rm -f BUGUN_TAMAMLANAN_ISLER_*.md 2>/dev/null || true
rm -f CODE_DUPLICATION_*.md 2>/dev/null || true
rm -f DEAD_CODE_*.md 2>/dev/null || true
rm -f PERFORMANCE_*.md 2>/dev/null || true
rm -f SECURITY_*.md 2>/dev/null || true
rm -f REFACTORING_*.md 2>/dev/null || true
rm -f ACTION_PLAN_*.md 2>/dev/null || true
rm -f COMPREHENSIVE_*.md 2>/dev/null || true
rm -f DEPENDENCY_*.md 2>/dev/null || true
rm -f DISABLED_*.md 2>/dev/null || true
rm -f EK_ISLER_*.md 2>/dev/null || true
rm -f FINAL_SUMMARY_*.md 2>/dev/null || true
rm -f FIXES_*.md 2>/dev/null || true
rm -f GOREV_DURUMU_*.md 2>/dev/null || true
rm -f INCOMPLETE_*.md 2>/dev/null || true
rm -f KALAN_SORUNLAR_*.md 2>/dev/null || true
rm -f LINT_AND_*.md 2>/dev/null || true
rm -f MIGRATION_HATASI_*.md 2>/dev/null || true
rm -f ORPHANED_*.md 2>/dev/null || true
rm -f SCRIPT_*.md 2>/dev/null || true
rm -f SONRAKI_ADIMLAR_*.md 2>/dev/null || true
rm -f TEST_COVERAGE_*.md 2>/dev/null || true
rm -f TODAY_SUMMARY_*.md 2>/dev/null || true
rm -f TODO_*.md 2>/dev/null || true
rm -f YARIN_ICIN_*.md 2>/dev/null || true
rm -f YAYIN_TIPLERI_*.md 2>/dev/null || true

# Standart dosyaları knowledge/ klasörüne taşı
if [ -f "FILTERABLE_TRAIT_USAGE.md" ]; then
    mv FILTERABLE_TRAIT_USAGE.md knowledge/ 2>/dev/null || true
fi

if [ -f "COMPREHENSIVE_CODE_CHECK_REHBERI.md" ]; then
    mv COMPREHENSIVE_CODE_CHECK_REHBERI.md knowledge/ 2>/dev/null || true
fi

echo "✅ Ana dizin temizlendi: $(ls -1 *.md 2>/dev/null | wc -l | tr -d ' ') dosya kaldı"
echo ""

# 2. Reports klasörü temizliği
echo "🗑️  2. Reports klasörü temizleniyor..."
cd reports/

# Archive klasörü oluştur
mkdir -p ../archive/2025-11/reports

# Eski comprehensive-code-check raporlarını temizle (sadece son 3'ü kalsın)
echo "   - Eski comprehensive-code-check raporları temizleniyor..."
COMP_FILES=($(ls -1t comprehensive-code-check-*.json 2>/dev/null))
COMP_COUNT=${#COMP_FILES[@]}

if [ $COMP_COUNT -gt 3 ]; then
    # İlk 3'ü atla, geri kalanları sil
    for ((i=3; i<$COMP_COUNT; i++)); do
        mv "${COMP_FILES[$i]}" ../archive/2025-11/reports/ 2>/dev/null || rm -f "${COMP_FILES[$i]}"
    done
    echo "   ✓ $((COMP_COUNT - 3)) comprehensive-code-check raporu arşivlendi"
fi

# Eski dead-code-analysis raporlarını temizle (sadece son 3'ü kalsın)
echo "   - Eski dead-code-analysis raporları temizleniyor..."
DEAD_JSON=($(ls -1t dead-code-analysis-*.json 2>/dev/null))
DEAD_JSON_COUNT=${#DEAD_JSON[@]}

if [ $DEAD_JSON_COUNT -gt 3 ]; then
    for ((i=3; i<$DEAD_JSON_COUNT; i++)); do
        mv "${DEAD_JSON[$i]}" ../archive/2025-11/reports/ 2>/dev/null || rm -f "${DEAD_JSON[$i]}"
    done
    echo "   ✓ $((DEAD_JSON_COUNT - 3)) dead-code-analysis JSON raporu arşivlendi"
fi

DEAD_MD=($(ls -1t dead-code-analysis-*.md 2>/dev/null))
DEAD_MD_COUNT=${#DEAD_MD[@]}

if [ $DEAD_MD_COUNT -gt 3 ]; then
    for ((i=3; i<$DEAD_MD_COUNT; i++)); do
        mv "${DEAD_MD[$i]}" ../archive/2025-11/reports/ 2>/dev/null || rm -f "${DEAD_MD[$i]}"
    done
    echo "   ✓ $((DEAD_MD_COUNT - 3)) dead-code-analysis MD raporu arşivlendi"
fi

# Incomplete dosyaları sil
rm -f incomplete-code-analysis-*.json 2>/dev/null || true

echo "✅ Reports temizlendi: $(ls -1 | wc -l | tr -d ' ') dosya kaldı"
echo ""

# 3. Knowledge klasörü temizliği
echo "🗑️  3. Knowledge klasörü temizleniyor..."
cd ../knowledge/

# Eski ve kullanılmayan dosyaları sil
rm -f dizin-temizlik-*.json 2>/dev/null || true
rm -f documentation-context7-cleanup-*.json 2>/dev/null || true
rm -f gece-temizlik-*.json 2>/dev/null || true
rm -f todo-2025-11-11.json 2>/dev/null || true
rm -f todo-analysis-2025-11-05.json 2>/dev/null || true
rm -f tum-veriler-eklendi-*.json 2>/dev/null || true

echo "✅ Knowledge temizlendi: $(ls -1 | wc -l | tr -d ' ') dosya kaldı"
echo ""

# 4. Sonuç özeti
cd "$PROJECT_ROOT"
echo "================================================"
echo "✅ Temizlik Tamamlandı!"
echo ""
echo "📊 Sonuç İstatistikleri:"
echo "   - Ana dizin: $(ls -1 .yalihan-bekci/*.md 2>/dev/null | wc -l | tr -d ' ') dosya"
echo "   - Reports: $(ls -1 .yalihan-bekci/reports/ 2>/dev/null | wc -l | tr -d ' ') dosya"
echo "   - Knowledge: $(ls -1 .yalihan-bekci/knowledge/ 2>/dev/null | wc -l | tr -d ' ') dosya"
echo "   - Toplam boyut: $(du -sh .yalihan-bekci/ | cut -f1)"
echo ""
echo "💾 Backup dosyası: $BACKUP_FILE"
echo "📁 Arşivlenen dosyalar: .yalihan-bekci/archive/2025-11/reports/"
echo ""
echo "🎉 Temizlik başarıyla tamamlandı!"
