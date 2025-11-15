#!/bin/bash

# Context7 Full Scan - MCP Enhanced Version
# Context7 MCP entegrasyonu ile geliştirilmiş versiyon
# 
# Özellikler:
# 1. Yalıhan Bekçi MCP'den kuralları alır
# 2. Context7 compliance kontrolü yapar
# 3. MCP'ye sonuçları bildirir
#
# Kullanım: ./scripts/context7-full-scan-mcp.sh [--mcp] [--report] [--json]

set -euo pipefail

# Renkler
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# MCP entegrasyonu
USE_MCP=false
MCP_RULES_FILE=""
MCP_RESULTS_DIR=".yalihan-bekci/reports/mcp-scan"

# Parametreleri parse et
while [[ $# -gt 0 ]]; do
    case $1 in
        --mcp|--context7)
            USE_MCP=true
            shift
            ;;
        --report)
            REPORT_FILE="${2:-.context7/compliance-report-$(date +%Y%m%d-%H%M%S).md}"
            shift 2
            ;;
        --json)
            JSON_MODE=true
            REPORT_FILE="${2:-.context7/compliance-report-$(date +%Y%m%d-%H%M%S).json}"
            shift 2
            ;;
        --help)
            echo "Context7 Full Scan - MCP Enhanced"
            echo ""
            echo "Kullanım: $0 [--mcp] [--report [dosya]] [--json [dosya]]"
            echo ""
            echo "Seçenekler:"
            echo "  --mcp, --context7    MCP entegrasyonunu aktifleştir"
            echo "  --report [dosya]    Markdown rapor oluştur"
            echo "  --json [dosya]      JSON rapor oluştur"
            echo "  --help              Bu yardım mesajını göster"
            echo ""
            echo "Örnekler:"
            echo "  $0 --mcp                    # MCP ile tarama"
            echo "  $0 --mcp --report          # MCP ile tarama + rapor"
            echo "  $0 --mcp --json             # MCP ile tarama + JSON rapor"
            exit 0
            ;;
        *)
            echo "Bilinmeyen parametre: $1"
            echo "Kullanım: $0 [--mcp] [--report] [--json] [--help]"
            exit 1
            ;;
    esac
done

# MCP entegrasyonu
if [ "$USE_MCP" = true ]; then
    echo -e "${BLUE}🔗 MCP Entegrasyonu Aktif${NC}\n"
    
    # Yalıhan Bekçi MCP'den kuralları al
    if [ -f ".context7/authority.json" ]; then
        echo -e "${BLUE}📚 Context7 kuralları MCP'den alınıyor...${NC}"
        
        # authority.json'dan kuralları çıkar
        MCP_RULES_FILE=".context7/authority.json"
        echo -e "${GREEN}   ✅ Kurallar yüklendi: $MCP_RULES_FILE${NC}"
    else
        echo -e "${YELLOW}   ⚠️  authority.json bulunamadı, yerel kurallar kullanılacak${NC}"
    fi
    
    # MCP sonuç dizinini oluştur
    mkdir -p "$MCP_RESULTS_DIR"
    echo -e "${GREEN}   ✅ MCP sonuç dizini hazır: $MCP_RESULTS_DIR${NC}\n"
fi

# ... existing scan code ...

# MCP'ye sonuçları bildir
if [ "$USE_MCP" = true ] && [ -n "$REPORT_FILE" ]; then
    echo -e "\n${BLUE}📤 MCP'ye sonuçlar bildiriliyor...${NC}"
    
    MCP_REPORT_FILE="$MCP_RESULTS_DIR/scan-$(date +%Y%m%d-%H%M%S).json"
    
    # JSON formatında MCP raporu oluştur
    {
        echo "{"
        echo "  \"timestamp\": \"$(date '+%Y-%m-%d %H:%M:%S')\","
        echo "  \"source\": \"context7-full-scan-mcp.sh\","
        echo "  \"mcp_integration\": true,"
        echo "  \"rules_source\": \"$MCP_RULES_FILE\","
        echo "  \"summary\": {"
        echo "    \"total\": $TOTAL,"
        echo "    \"critical\": $CRITICAL,"
        echo "    \"high\": $HIGH,"
        echo "    \"medium\": $MEDIUM,"
        echo "    \"low\": $LOW"
        echo "  },"
        echo "  \"report_file\": \"$REPORT_FILE\""
        echo "}"
    } > "$MCP_REPORT_FILE"
    
    echo -e "${GREEN}   ✅ MCP raporu oluşturuldu: $MCP_REPORT_FILE${NC}"
fi

# ... rest of the script ...

