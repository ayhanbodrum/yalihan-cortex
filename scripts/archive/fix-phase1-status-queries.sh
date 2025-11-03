#!/bin/bash

# ═══════════════════════════════════════════════════════════════════════════
# PHASE 1 STATUS QUERY FIX SCRIPT
# ═══════════════════════════════════════════════════════════════════════════
#
# This script automatically fixes all status queries for PHASE 1 tables:
# - ilanlar, kisiler, projeler, ozellikler, talepler
#
# Converts:
#   where('status', 'Aktif')  → where('status', true)
#   where('status', 'aktif')  → where('status', true)
#   where('status', 'Pasif')  → where('status', false)
#   where('status', 'pasif')  → where('status', false)
#
# ═══════════════════════════════════════════════════════════════════════════

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"

cd "$PROJECT_ROOT"

echo "╔══════════════════════════════════════════════════════════════════════════╗"
echo "║                                                                          ║"
echo "║  🔧 PHASE 1 STATUS QUERY FIX SCRIPT                                     ║"
echo "║                                                                          ║"
echo "╚══════════════════════════════════════════════════════════════════════════╝"
echo ""

# Create backup
BACKUP_DIR="storage/backups/phase1-status-fix-$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"

echo "📦 Backup oluşturuluyor: $BACKUP_DIR"
echo ""

# Find all files that need fixing
FILES=$(grep -rl "where('status'.*'Aktif')\|where('status'.*'aktif')\|where('status'.*'Pasif')\|where('status'.*'pasif')" app/ --include="*.php" | sort -u)

if [ -z "$FILES" ]; then
    echo "✅ Hiçbir dosya güncellenmedi - zaten temiz!"
    exit 0
fi

echo "📝 Düzeltilecek dosyalar:"
echo "$FILES" | sed 's/^/   • /'
echo ""

FIXED_COUNT=0

# Fix each file
while IFS= read -r file; do
    if [ ! -f "$file" ]; then
        continue
    fi

    echo "🔧 Düzeltiliyor: $file"

    # Backup original file
    cp "$file" "$BACKUP_DIR/$(basename $file).bak"

    # Fix status queries
    # Active status: 'Aktif' or 'aktif' → true
    sed -i.tmp "s/where('status',\s*'Aktif')/where('status', true)/g" "$file"
    sed -i.tmp "s/where('status',\s*'aktif')/where('status', true)/g" "$file"
    sed -i.tmp "s/where(\"status\",\s*\"Aktif\")/where('status', true)/g" "$file"
    sed -i.tmp "s/where(\"status\",\s*\"aktif\")/where('status', true)/g" "$file"

    # Inactive status: 'Pasif' or 'pasif' → false
    sed -i.tmp "s/where('status',\s*'Pasif')/where('status', false)/g" "$file"
    sed -i.tmp "s/where('status',\s*'pasif')/where('status', false)/g" "$file"
    sed -i.tmp "s/where(\"status\",\s*\"Pasif\")/where('status', false)/g" "$file"
    sed -i.tmp "s/where(\"status\",\s*\"pasif\")/where('status', false)/g" "$file"

    # Remove temp file
    rm -f "$file.tmp"

    FIXED_COUNT=$((FIXED_COUNT + 1))

done <<< "$FILES"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "✅ TAMAMLANDI!"
echo ""
echo "   Düzeltilen dosya sayısı: $FIXED_COUNT"
echo "   Backup klasörü: $BACKUP_DIR"
echo ""
echo "🔍 Değişiklikleri görmek için:"
echo "   git diff app/"
echo ""
echo "🔙 Geri almak için (eğer sorun olursa):"
echo "   cp $BACKUP_DIR/*.bak app/"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

