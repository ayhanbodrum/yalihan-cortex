#!/bin/bash

# AnythingLLM Embedding Test Script
# Version: 1.0.0

echo "🧪 AnythingLLM Embedding Test"
echo "==============================="
echo ""

# Renk kodları
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Test sayaçları
total_tests=0
passed=0
failed=0

# Test fonksiyonu
test_query() {
    local query=$1
    local expected=$2
    local test_name=$3
    
    ((total_tests++))
    
    echo -e "${BLUE}Test $total_tests:${NC} $test_name"
    echo "Soru: $query"
    echo "Beklenen: $expected"
    echo ""
}

echo "📋 Test Senaryoları:"
echo ""

# Test 1: Context7 Kuralları
test_query \
    "status yerine durum kullanabilir miyim?" \
    "Hayır, 'status' kullan - Context7 kuralı" \
    "Context7 Field Naming"

# Test 2: Ollama Endpoint
test_query \
    "Ollama endpoint'i nedir?" \
    "http://51.75.64.121:11434" \
    "Ollama Configuration"

# Test 3: Başlık Üretimi
test_query \
    "Yalıkavak villa için başlık öner" \
    "3 varyant, JSON format, 60-80 karakter" \
    "Title Generation"

# Test 4: Para Birimi
test_query \
    "TRY sembolü nedir?" \
    "₺ (Türk Lirası sembolü)" \
    "Currency Symbols"

# Test 5: Ton Profilleri
test_query \
    "Kaç tane ton profili var?" \
    "4 adet: seo, kurumsal, hizli_satis, luks" \
    "Tone Profiles"

# Test 6: Açıklama Uzunluğu
test_query \
    "İlan açıklaması kaç kelime olmalı?" \
    "200-250 kelime, 3 paragraf" \
    "Description Length"

# Test 7: Lokasyon Hiyerarşisi
test_query \
    "Lokasyon hiyerarşisi nasıl?" \
    "Ülke → İl → İlçe → Mahalle" \
    "Location Hierarchy"

# Test 8: İlan Referans Numarası
test_query \
    "Referans numarası formatı nedir?" \
    "YE-{YAYIN}-{LOK}-{KAT}-{SIRA}" \
    "Reference Number Format"

# Test 9: CRM Skoru
test_query \
    "CRM skoru nasıl hesaplanır?" \
    "0-100, 4 kriter: İlan, Satış, Aktiflik, Bütçe" \
    "CRM Score Calculation"

# Test 10: Neo Design System
test_query \
    "Neo Design System prefix nedir?" \
    "neo-* (neo-btn, neo-card, neo-input)" \
    "Neo Design System"

echo ""
echo "==============================="
echo -e "Toplam Test: $total_tests"
echo ""
echo "📝 NOT: Bu test'leri AnythingLLM chat'inde manuel çalıştırın."
echo "Her soruyu sorun ve AI yanıtını kontrol edin."
echo ""
echo "✅ BAŞARILI embedding için: 10/10 test passed olmalı"
echo ""
echo "�� Detaylı test senaryoları: 08-TRAINING-CHECKLIST.md"
echo ""

# Doküman sayısı kontrolü
doc_count=$(ls -1 docs/ai-training/*.md 2>/dev/null | wc -l | tr -d ' ')
echo "📁 Toplam Doküman: $doc_count"

if [ "$doc_count" -ge "10" ]; then
    echo -e "${GREEN}✅ Yeterli doküman sayısı ($doc_count/10+)${NC}"
else
    echo -e "${RED}❌ Yetersiz doküman ($doc_count/10)${NC}"
fi

echo ""
echo "🚀 Kurulum için: ./docs/ai-training/QUICK-START.md"
echo ""
