#!/bin/bash

# 🔍 Intelligent Documentation Search
# Akıllı arama - aktif dosyalarda + archive içinde

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
NC='\033[0m' # No Color

# Check arguments
if [ $# -eq 0 ]; then
    echo -e "${RED}❌ Kullanım: $0 <arama_terimi>${NC}"
    echo ""
    echo "Örnek:"
    echo "  $0 Context7"
    echo "  $0 'AI System'"
    echo "  $0 database"
    exit 1
fi

QUERY="$1"

echo ""
echo -e "${PURPLE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}🔍 AKILLI DOKÜMANTASYON ARAASI${NC}"
echo -e "${PURPLE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""
echo -e "${YELLOW}📝 Arama terimi:${NC} \"$QUERY\""
echo ""

# Function: Search in active files
search_active() {
    echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${GREEN}📄 AKTİF DOSYALARDA ARAMA${NC}"
    echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo ""

    local results=$(grep -r -i "$QUERY" docs/ \
        --include="*.md" \
        --exclude-dir=archive \
        -n -H 2>/dev/null | head -20)

    if [ -z "$results" ]; then
        echo -e "${YELLOW}⚠️  Aktif dosyalarda sonuç bulunamadı${NC}"
    else
        echo "$results" | while IFS=: read -r file line content; do
            echo -e "${BLUE}📄 $(basename "$file")${NC} ${YELLOW}(satır $line)${NC}"
            echo -e "   ${content}"
            echo ""
        done
    fi
}

# Function: Search in archive
search_archive() {
    echo ""
    echo -e "${PURPLE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${PURPLE}📦 ARCHIVE'DE ARAMA${NC}"
    echo -e "${PURPLE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo ""

    if [ -f "docs/archive/legacy-docs-2024-2025.tar.gz" ]; then
        local archive_results=$(tar -xzf docs/archive/legacy-docs-2024-2025.tar.gz -O 2>/dev/null | grep -i "$QUERY" | head -10)

        if [ -z "$archive_results" ]; then
            echo -e "${YELLOW}⚠️  Archive'de sonuç bulunamadı${NC}"
        else
            local count=$(echo "$archive_results" | wc -l | xargs)
            echo -e "${GREEN}✅ Archive'de $count sonuç bulundu (ilk 10):${NC}"
            echo ""
            echo "$archive_results" | nl -w2 -s'. '
        fi
    else
        echo -e "${YELLOW}⚠️  Archive dosyası bulunamadı${NC}"
    fi
}

# Function: Search in root
search_root() {
    echo ""
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${BLUE}📋 ROOT DİZİNDE ARAMA${NC}"
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo ""

    local root_results=$(grep -i "$QUERY" *.md 2>/dev/null | head -10)

    if [ -z "$root_results" ]; then
        echo -e "${YELLOW}⚠️  Root'ta sonuç bulunamadı${NC}"
    else
        echo "$root_results" | while IFS=: read -r file content; do
            echo -e "${GREEN}✅ $file${NC}"
            echo -e "   ${content}"
            echo ""
        done
    fi
}

# Main execution
search_active
search_archive
search_root

echo ""
echo -e "${PURPLE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}✅ Arama tamamlandı!${NC}"
echo -e "${PURPLE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

