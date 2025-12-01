#!/bin/bash

# Dokümantasyon Konsolidasyon ve Temizlik Script'i
# Tarih: 30 Kasım 2025
# Amaç: Tekrarlayan dosyaları temizle, boş klasörleri sil, yapıyı organize et

set -e

echo "📚 Dokümantasyon Temizliği Başlıyor..."
echo "========================================"
echo ""

PROJECT_ROOT="/Users/macbookpro/Projects/yalihanai"
cd "$PROJECT_ROOT"

# Backup oluştur
echo "📦 Backup oluşturuluyor..."
BACKUP_FILE="docs-backup-$(date +%Y%m%d-%H%M%S).tar.gz"
tar -czf "$BACKUP_FILE" docs/ reports/ aiegitim/ 2>/dev/null || true
echo "✅ Backup: $BACKUP_FILE"
echo ""

# Başlangıç istatistikleri
echo "📊 Başlangıç İstatistikleri:"
echo "   - docs/ klasör sayısı: $(find docs -type d | wc -l | tr -d ' ')"
echo "   - docs/active/ dosyaları: $(ls -1 docs/active/*.md 2>/dev/null | wc -l | tr -d ' ')"
echo "   - aiegitim/ var mı: $([ -d aiegitim ] && echo 'Evet' || echo 'Hayır')"
echo ""

# Faz 1: Boş klasörleri sil
echo "🗑️  Faz 1: Boş klasörleri silme..."
DELETED_DIRS=0

for dir in docs/roadmaps docs/modules docs/n8n-workflows docs/usage docs/rules; do
    if [ -d "$dir" ]; then
        if [ -z "$(ls -A $dir 2>/dev/null)" ]; then
            rmdir "$dir" 2>/dev/null && echo "   ✓ $dir silindi (boştu)" && ((DELETED_DIRS++))
        else
            echo "   - $dir boş değil, atlanıyor"
        fi
    fi
done

echo "✅ $DELETED_DIRS boş klasör silindi"
echo ""

# Faz 2: Eski dosyaları arşivle
echo "📦 Faz 2: Eski dosyaları arşivleme..."
mkdir -p docs/archive/2025-11/old-plans
ARCHIVED_FILES=0

# Eski plan dosyaları
if [ -f "docs/frontend-global-redesign-plan.md" ]; then
    mv docs/frontend-global-redesign-plan.md docs/archive/2025-11/old-plans/
    echo "   ✓ frontend-global-redesign-plan.md arşivlendi"
    ((ARCHIVED_FILES++))
fi

if [ -f "docs/migration-auto-fixer.md" ]; then
    mv docs/migration-auto-fixer.md docs/archive/2025-11/old-plans/
    echo "   ✓ migration-auto-fixer.md arşivlendi"
    ((ARCHIVED_FILES++))
fi

# Alt klasörleri arşivle
for subdir in features modules rules; do
    if [ -d "docs/$subdir" ] && [ "$(ls -A docs/$subdir 2>/dev/null)" ]; then
        mkdir -p "docs/archive/2025-11/$subdir"
        FILE_COUNT=$(ls -1 docs/$subdir | wc -l | tr -d ' ')
        mv docs/$subdir/* docs/archive/2025-11/$subdir/ 2>/dev/null || true
        rmdir docs/$subdir 2>/dev/null || true
        echo "   ✓ $subdir/ arşivlendi ($FILE_COUNT dosya)"
        ARCHIVED_FILES=$((ARCHIVED_FILES + FILE_COUNT))
    fi
done

echo "✅ $ARCHIVED_FILES dosya arşivlendi"
echo ""

# Faz 3: aiegitim/ klasörünü birleştir
echo "🔄 Faz 3: aiegitim/ klasörünü birleştirme..."
MOVED_FILES=0

if [ -d "aiegitim" ]; then
    for file in aiegitim/*.md; do
        if [ -f "$file" ]; then
            BASENAME=$(basename "$file")
            mv "$file" docs/ai-training/
            echo "   ✓ $BASENAME taşındı"
            ((MOVED_FILES++))
        fi
    done

    if [ -z "$(ls -A aiegitim 2>/dev/null)" ]; then
        rmdir aiegitim && echo "   ✓ aiegitim/ klasörü kaldırıldı"
    fi
fi

echo "✅ $MOVED_FILES dosya taşındı"
echo ""

# Faz 4: Reports temizliği
echo "🗑️  Faz 4: Eski raporları temizleme..."
DELETED_REPORTS=0

if [ -d "reports/archive/2025-11-04" ]; then
    REPORT_COUNT=$(find reports/archive/2025-11-04 -type f | wc -l | tr -d ' ')
    rm -rf reports/archive/2025-11-04/
    echo "   ✓ 2025-11-04 arşivi silindi ($REPORT_COUNT dosya)"
    DELETED_REPORTS=$REPORT_COUNT
fi

echo "✅ $DELETED_REPORTS eski rapor silindi"
echo ""

# Sonuç
echo "========================================"
echo "✅ Dokümantasyon Temizliği Tamamlandı!"
echo ""
echo "📊 Sonuç İstatistikleri:"
echo "   - docs/ klasör sayısı: $(find docs -type d | wc -l | tr -d ' ')"
echo "   - docs/active/ dosyaları: $(ls -1 docs/active/*.md 2>/dev/null | wc -l | tr -d ' ')"
echo "   - docs/archive/ boyutu: $(du -sh docs/archive 2>/dev/null | cut -f1 || echo '0')"
echo "   - aiegitim/ klasörü: $([ -d aiegitim ] && echo 'Hala var' || echo 'Kaldırıldı ✓')"
echo ""
echo "📈 Temizlik Özeti:"
echo "   - Silinen klasör: $DELETED_DIRS"
echo "   - Arşivlenen dosya: $ARCHIVED_FILES"
echo "   - Taşınan dosya: $MOVED_FILES"
echo "   - Silinen rapor: $DELETED_REPORTS"
echo ""
echo "💾 Backup dosyası: $BACKUP_FILE"
echo ""
echo "🎉 Temizlik başarıyla tamamlandı!"
