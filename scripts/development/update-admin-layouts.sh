#!/bin/bash

# Admin Layout Güncelleme Scripti
# Tüm admin sayfalarını unified layout'a geçirir

echo "🔄 Admin layout'ları unified sistemine geçiriliyor..."

# resources/views/admin/ klasöründeki tüm .blade.php dosyalarını bul ve güncelle
find resources/views/admin -name "*.blade.php" -type f -exec grep -l "admin\.layouts\.app" {} \; | while read file; do
    echo "📝 Güncelleniyor: $file"
    sed -i '' 's/admin\.layouts\.app/admin.layouts.unified/g' "$file"
done

# master layout kullanan dosyaları da güncelle
find resources/views/admin -name "*.blade.php" -type f -exec grep -l "admin\.layouts\.master" {} \; | while read file; do
    echo "📝 Güncelleniyor: $file"
    sed -i '' 's/admin\.layouts\.master/admin.layouts.unified/g' "$file"
done

echo "✅ Tüm admin layout'ları güncellendi!"
echo "📊 Güncellenen dosya sayısı:"
echo "   - app layout: $(find resources/views/admin -name "*.blade.php" -exec grep -l "admin\.layouts\.unified" {} \; | wc -l)"
echo "   - Toplam admin dosyası: $(find resources/views/admin -name "*.blade.php" | wc -l)"
