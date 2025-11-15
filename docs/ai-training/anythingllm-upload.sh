#!/bin/bash

# AnythingLLM Dosya Upload Kontrol Script'i
# Version: 1.0.0

echo "🎓 Yalıhan Emlak AI Eğitim Paketi - Upload Kontrolü"
echo "=================================================="
echo ""

# Renk kodları
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Sayaçlar
total=0
found=0
missing=0

# Kontrol edilecek dosyalar
files=(
    "00-ANYTHINGLLM-MASTER-TRAINING.md"
    "01-AI-FEATURES-GUIDE.md"
    "02-CONTEXT7-RULES-SIMPLIFIED.md"
    "03-DATABASE-SCHEMA-FOR-AI.md"
    "04-PROMPT-TEMPLATES.md"
    "05-USE-CASES-AND-SCENARIOS.md"
    "06-API-REFERENCE.md"
    "07-EMBEDDING-GUIDE.md"
    "08-TRAINING-CHECKLIST.md"
    "09-OLLAMA-INTEGRATION.md"
    "QUICK-START.md"
    "README.md"
)

echo "📁 Dosya Kontrolü:"
echo ""

for file in "${files[@]}"; do
    ((total++))
    filepath="docs/ai-training/$file"

    if [ -f "$filepath" ]; then
        ((found++))
        size=$(wc -c < "$filepath" | tr -d ' ')
        lines=$(wc -l < "$filepath" | tr -d ' ')
        echo -e "${GREEN}✅${NC} $file"
        echo "   Size: $size bytes, Lines: $lines"
    else
        ((missing++))
        echo -e "${RED}❌${NC} $file - BULUNAMADI"
    fi
done

echo ""
echo "=================================================="
echo -e "Toplam: $total dosya"
echo -e "${GREEN}Hazır: $found${NC}"
echo -e "${RED}Eksik: $missing${NC}"
echo ""

if [ $missing -eq 0 ]; then
    echo -e "${GREEN}✅ TÜM DOSYALAR HAZIR!${NC}"
    echo ""
    echo "🚀 Sonraki Adımlar:"
    echo "1. AnythingLLM'i aç: http://localhost:3001"
    echo "2. New Workspace oluştur: 'Yalıhan Emlak AI'"
    echo "3. Watch Folder ekle: docs/ai-training/"
    echo "4. System Prompt ayarla (07-EMBEDDING-GUIDE.md)"
    echo "5. Test et (08-TRAINING-CHECKLIST.md)"
    echo ""
    echo "📖 Detaylı rehber: docs/ai-training/QUICK-START.md"
else
    echo -e "${RED}⚠️ EKSIK DOSYALAR VAR!${NC}"
    echo "Lütfen eksik dosyaları kontrol edin."
fi

echo ""
echo "=================================================="

# Toplam boyut hesapla
total_size=$(du -sh docs/ai-training/ | cut -f1)
echo "📦 Toplam Paket Boyutu: $total_size"

# Word count
total_words=$(wc -w docs/ai-training/*.md | tail -1 | awk '{print $1}')
echo "📝 Toplam Kelime Sayısı: ~$total_words"

echo ""
echo "🎓 Eğitim paketi hazır! AnythingLLM'e yüklenebilir."
echo ""
