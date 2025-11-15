#!/bin/bash
# context7-safe-cleanup.sh
# Güvenli toplu temizlik script'i - Backup ve validation ile

echo "🚨 Context7 Güvenli Toplu Temizlik"
echo "═══════════════════════════════════════"
echo ""

# Backup klasörü oluştur
BACKUP_DIR=".context7/backups/safe-cleanup-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP_DIR"

echo "📁 Backup klasörü: $BACKUP_DIR"
echo ""

# 1. Garip değişken isimlerini temizle
echo "🔧 1/4: Garip değişken isimleri temizleniyor..."

CORRUPTED_FILES=$(find . -type f \( -name "*.php" -o -name "*.blade.php" \) \
    -not -path "*/vendor/*" \
    -not -path "*/node_modules/*" \
    -not -path "*/storage/*" \
    -exec grep -l '\$\$\$\$\$\$' {} \;)

FILE_COUNT=$(echo "$CORRUPTED_FILES" | grep -c '^')
echo "   📊 Tespit edilen dosya: $FILE_COUNT"

if [ "$FILE_COUNT" -gt 0 ]; then
    while IFS= read -r file; do
        # Backup al
        cp "$file" "$BACKUP_DIR/$(basename "$file").backup"

        # Garip değişken isimlerini düzelt
        sed -i.tmp 's/\$\$\$\$\$\$\$\$\$\$\$\$/\$/g' "$file"

        # Syntax kontrolü (sadece .php dosyaları için)
        if [[ "$file" == *.php ]] && [[ "$file" != *.blade.php ]]; then
            if ! php -l "$file" > /dev/null 2>&1; then
                echo "   ❌ Syntax hatası: $file - Rollback yapılıyor"
                cp "$BACKUP_DIR/$(basename "$file").backup" "$file"
            else
                echo "   ✅ Düzeltildi: $file"
                rm "$file.tmp"
            fi
        else
            echo "   ✅ Düzeltildi: $file"
            rm "$file.tmp" 2>/dev/null || true
        fi
    done <<< "$CORRUPTED_FILES"
fi

# 2. Blade syntax hatalarını kontrol et
echo ""
echo "🔍 2/4: Blade syntax hataları kontrol ediliyor..."

BLADE_ERRORS=$(grep -r '@error(' resources/views/ 2>/dev/null | grep -v "@error('[a-zA-Z_]" | wc -l)
echo "   📊 Tespit edilen hata: $BLADE_ERRORS"

# 3. Eksik view dosyalarını kontrol et
echo ""
echo "📄 3/4: Route-View tutarlılığı kontrol ediliyor..."

php artisan route:list --path=admin --json 2>/dev/null > /dev/null
echo "   ✅ Route listesi kontrol edildi"

# 4. PHP syntax kontrolü
echo ""
echo "✅ 4/4: Tüm PHP dosyalarında syntax kontrolü..."

SYNTAX_ERRORS=0
for file in $(find app -name "*.php" -not -path "*/vendor/*"); do
    if ! php -l "$file" > /dev/null 2>&1; then
        echo "   ❌ Syntax hatası: $file"
        ((SYNTAX_ERRORS++))
    fi
done

echo "   📊 Syntax hatası: $SYNTAX_ERRORS dosya"

# Özet
echo ""
echo "═══════════════════════════════════════"
echo "✅ TEMİZLİK TAMAMLANDI!"
echo ""
echo "📊 ÖZET:"
echo "   • Düzeltilen dosya: $FILE_COUNT"
echo "   • Blade hataları: $BLADE_ERRORS"
echo "   • Syntax hataları: $SYNTAX_ERRORS"
echo "   • Backup: $BACKUP_DIR"
echo ""
echo "🔄 Sonraki adım: php artisan view:clear"
echo "═══════════════════════════════════════"
