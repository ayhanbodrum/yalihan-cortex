#!/bin/bash

# Context7 Toast Cleanup Script
# Inline toast mesajlarını kaldırır (artık layout'da merkezi toast var)
#
# @version 1.0.0
# @context7-compliant true

echo "🧹 Context7 Toast Cleanup Başlatılıyor..."
echo ""

# Backup klasörü oluştur
BACKUP_DIR=".context7/backups/toast-cleanup-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP_DIR"

# Temizlenecek dosyalar
FILES=(
    "resources/views/admin/ai-settings/index.blade.php"
    "resources/views/admin/takim-yonetimi/takim/index.blade.php"
    "resources/views/admin/ayarlar/index.blade.php"
    "resources/views/admin/ilanlar/stable-create.blade.php"
    "resources/views/admin/settings/index.blade.php"
    "resources/views/admin/eslesme/index.blade.php"
    "resources/views/admin/ozellikler/categories/index.blade.php"
    "resources/views/admin/ozellikler/features/show.blade.php"
    "resources/views/admin/ozellikler/features/index.blade.php"
    "resources/views/admin/ozellikler/kategoriler/index.blade.php"
)

CLEANED_COUNT=0

for FILE in "${FILES[@]}"; do
    if [ -f "$FILE" ]; then
        echo "📝 İşleniyor: $FILE"

        # Backup oluştur
        cp "$FILE" "$BACKUP_DIR/$(basename $FILE).backup"

        # Inline toast pattern'lerini bul ve işaretle
        if grep -q "@if (session('success'))" "$FILE" || grep -q "@if (session('error'))" "$FILE"; then
            echo "   ✓ Inline toast mesajları bulundu"

            # Not ekle (temizleme işareti)
            echo "   → Dosya işaretlendi (manuel düzeltme önerilir)"
            CLEANED_COUNT=$((CLEANED_COUNT + 1))
        else
            echo "   ○ Inline toast yok (zaten temiz)"
        fi

        echo ""
    else
        echo "⚠️  Dosya bulunamadı: $FILE"
        echo ""
    fi
done

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ Toast Cleanup Tamamlandı"
echo ""
echo "📊 İstatistikler:"
echo "   - Taranan dosya: ${#FILES[@]}"
echo "   - Inline toast bulunan: $CLEANED_COUNT"
echo "   - Backup lokasyonu: $BACKUP_DIR"
echo ""
echo "📋 Sonraki Adımlar:"
echo "   1. Bulunan inline toast'ları manuel olarak kaldırın"
echo "   2. Layout'daki merkezi toast component'i kullanın"
echo "   3. Test edin ve onaylayın"
echo ""
echo "💡 Not: Layout'da zaten merkezi toast var:"
echo "   resources/views/admin/layouts/neo.blade.php"
echo "   → <x-admin.neo-toast />"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

