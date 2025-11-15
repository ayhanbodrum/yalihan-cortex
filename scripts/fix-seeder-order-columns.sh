#!/bin/bash

# Context7: Seeder dosyalarında 'order' → 'display_order' toplu düzeltme scripti
# Kullanım: ./scripts/fix-seeder-order-columns.sh

echo "🔧 Seeder dosyalarında 'order' → 'display_order' düzeltmesi başlatılıyor..."

# Seeder dizinindeki tüm PHP dosyalarını işle
find database/seeders -name "*.php" -type f | while read file; do
    # 'order' => pattern'ini 'display_order' => olarak değiştir (array key)
    # Ama değişken adlarını değiştirme ($order gibi)
    if grep -q "'order' =>" "$file" || grep -q '"order" =>' "$file"; then
        echo "  📝 Düzeltiliyor: $file"
        
        # PHP array key'lerini değiştir
        sed -i '' "s/'order' =>/'display_order' =>/g" "$file"
        sed -i '' 's/"order" =>/"display_order" =>/g' "$file"
        
        # Array key kullanımlarını da düzelt ($data['order'] → $data['display_order'])
        sed -i '' "s/\['order'\]/['display_order']/g" "$file"
        sed -i '' 's/\["order"\]/["display_order"]/g' "$file"
        
        # orderBy('order') → orderBy('display_order')
        sed -i '' "s/orderBy('order')/orderBy('display_order')/g" "$file"
        sed -i '' 's/orderBy("order")/orderBy("display_order")/g' "$file"
    fi
done

echo "✅ Düzeltme tamamlandı!"
echo "📋 Kontrol için: grep -r \"'order'\" database/seeders/"

