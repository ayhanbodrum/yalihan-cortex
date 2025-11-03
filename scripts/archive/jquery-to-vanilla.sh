#!/bin/bash

# jQuery → Vanilla JS Otomatik Migration
# $.ajax() → fetch() dönüşümü

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "📦 JQUERY → VANILLA JS MIGRATION"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

FILES=(
    "public/js/address-select.js"
    "public/js/admin/location-helper.js"
    "public/js/admin/location-map-helper.js"
    "public/js/modules/ilan-form.js"
)

TOTAL=${#FILES[@]}
CURRENT=0

for FILE in "${FILES[@]}"; do
    CURRENT=$((CURRENT + 1))
    echo "[$CURRENT/$TOTAL] Migrating: $FILE"
    
    if [ ! -f "$FILE" ]; then
        echo "   ⚠️  Dosya bulunamadı, atlanıyor..."
        continue
    fi
    
    # Backup
    cp "$FILE" "$FILE.backup-$(date +%Y%m%d)"
    
    # $.ajax() → fetch() conversion
    sed -i.tmp 's/\$\.ajax({/fetch(url).then(response => response.json()).then(data => {/g' "$FILE"
    sed -i.tmp 's/success: function(data) {/\/\/ Fetch response/g' "$FILE"
    sed -i.tmp 's/error: function(xhr) {/}).catch(error => {/g' "$FILE"
    
    # $.each() → forEach() conversion  
    sed -i.tmp 's/\$\.each(data, function(key, value) {/data.forEach((value, key) => {/g' "$FILE"
    
    # $() → document.querySelector()
    sed -i.tmp "s/\$('#/document.getElementById('/g" "$FILE"
    sed -i.tmp "s/\$('\\./document.querySelector('./g" "$FILE"
    
    # .html() → .innerHTML
    sed -i.tmp 's/\.html(/\.innerHTML = /g' "$FILE"
    
    # .val() → .value
    sed -i.tmp 's/\.val()/\.value/g' "$FILE"
    
    # .show() → .style.display
    sed -i.tmp 's/\.show()/\.style.display = "block"/g' "$FILE"
    sed -i.tmp 's/\.hide()/\.style.display = "none"/g' "$FILE"
    
    # Cleanup temp files
    rm -f "$FILE.tmp"
    
    echo "   ✅ Migration tamamlandı"
done

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "✅ $TOTAL dosya migrate edildi!"
echo ""
echo "⚠️  ÖNEMLİ:"
echo "   • Backup dosyaları oluşturuldu (.backup-*)"
echo "   • Manuel test yapın!"
echo "   • Syntax hataları olabilir"
echo ""
echo "Geri almak için:"
echo "   for f in *.backup-*; do mv \$f \${f%.backup-*}; done"
echo ""
