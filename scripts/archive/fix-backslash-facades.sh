#!/bin/bash
# Auto-fix: Backslash facade kullanımlarını düzelt
# Yalıhan Bekçi - 2 Kasım 2025

echo "🔧 Backslash facade kullanımları düzeltiliyor..."

# Staged PHP dosyalarını al
FILES=$(git diff --cached --name-only --diff-filter=ACM | grep "\.php$")

if [ -z "$FILES" ]; then
    echo "✅ Düzeltilecek PHP dosyası yok."
    exit 0
fi

FIXED_COUNT=0

for FILE in $FILES; do
    if [ -f "$FILE" ]; then
        # Backslash facade kullanımlarını düzelt
        if grep -q '\\Cache::' "$FILE" || \
           grep -q '\\DB::' "$FILE" || \
           grep -q '\\Log::' "$FILE" || \
           grep -q '\\Auth::' "$FILE" || \
           grep -q '\\View::' "$FILE"; then
            
            echo "📝 Düzeltiliyor: $FILE"
            
            # macOS'ta sed -i '' kullanılır, Linux'ta sed -i
            if [[ "$OSTYPE" == "darwin"* ]]; then
                sed -i '' 's/\\Cache::/Cache::/g' "$FILE"
                sed -i '' 's/\\DB::/DB::/g' "$FILE"
                sed -i '' 's/\\Log::/Log::/g' "$FILE"
                sed -i '' 's/\\Auth::/Auth::/g' "$FILE"
                sed -i '' 's/\\View::/View::/g' "$FILE"
            else
                sed -i 's/\\Cache::/Cache::/g' "$FILE"
                sed -i 's/\\DB::/DB::/g' "$FILE"
                sed -i 's/\\Log::/Log::/g' "$FILE"
                sed -i 's/\\Auth::/Auth::/g' "$FILE"
                sed -i 's/\\View::/View::/g' "$FILE"
            fi
            
            # use statements kontrolü
            NEEDS_CACHE=$(grep -c 'Cache::' "$FILE")
            HAS_CACHE=$(grep -c '^use.*Facades.*Cache' "$FILE")
            
            if [ "$NEEDS_CACHE" -gt 0 ] && [ "$HAS_CACHE" -eq 0 ]; then
                echo "⚠️  WARNING: $FILE - Cache kullanıyor ama 'use' statement yok!"
                echo "   'use Illuminate\\Support\\Facades\\Cache;' eklemelisin!"
            fi
            
            FIXED_COUNT=$((FIXED_COUNT + 1))
            
            # Düzeltilen dosyayı stage'e ekle
            git add "$FILE"
        fi
    fi
done

if [ $FIXED_COUNT -gt 0 ]; then
    echo "✅ $FIXED_COUNT dosya düzeltildi ve stage'e eklendi."
else
    echo "✅ Düzeltilecek backslash facade kullanımı bulunamadı."
fi

exit 0

