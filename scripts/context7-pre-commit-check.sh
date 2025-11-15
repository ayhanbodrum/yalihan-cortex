#!/bin/bash

# Context7 Pre-commit Check
# Git commit öncesi Context7 compliance kontrolü
# Kullanım: Git pre-commit hook olarak kullanılır

set -euo pipefail

# Renkler
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Staged dosyaları al
STAGED_FILES=$(git diff --cached --name-only --diff-filter=ACM | grep -E '\.(php|blade\.php)$' || true)

if [ -z "$STAGED_FILES" ]; then
    exit 0
fi

VIOLATIONS=0

echo -e "${BLUE}🔍 Context7 Pre-commit Check${NC}"
echo ""

# Her staged dosyayı kontrol et
for file in $STAGED_FILES; do
    # Migration dosyaları için özel kontrol
    if [[ "$file" =~ migrations.*\.php$ ]]; then
        # order kontrolü
        if grep -qE "'order'|\"order\"|order\s*=>" "$file" 2>/dev/null | grep -qv "display_order\|orderBy\|orderByRaw\|orderByDesc\|reorder\|Context7\|//\|/\*"; then
            echo -e "${RED}❌ Context7 Violation:${NC} $file"
            echo -e "   ${RED}Pattern:${NC} 'order' column usage"
            echo -e "   ${RED}→${NC} Use 'display_order' instead of 'order'"
            echo ""
            VIOLATIONS=$((VIOLATIONS + 1))
        fi
        
        # enabled/aktif/is_active kontrolü
        if grep -qE "'enabled'|\"enabled\"|'aktif'|\"aktif\"|'is_active'|\"is_active\"" "$file" 2>/dev/null | grep -qv "status\|Context7\|//\|/\*"; then
            echo -e "${RED}❌ Context7 Violation:${NC} $file"
            echo -e "   ${RED}Pattern:${NC} 'enabled'/'aktif'/'is_active' usage"
            echo -e "   ${RED}→${NC} Use 'status' instead"
            echo ""
            VIOLATIONS=$((VIOLATIONS + 1))
        fi
    fi
    
    # Model dosyaları için kontrol
    if [[ "$file" =~ Models.*\.php$ ]]; then
        # order kontrolü
        if grep -qE "'order'|\"order\"|order\s*=>" "$file" 2>/dev/null | grep -qv "display_order\|orderBy\|orderByRaw\|orderByDesc\|reorder\|Context7\|//\|/\*"; then
            echo -e "${RED}❌ Context7 Violation:${NC} $file"
            echo -e "   ${RED}Pattern:${NC} 'order' column usage"
            echo -e "   ${RED}→${NC} Use 'display_order' instead of 'order'"
            echo ""
            VIOLATIONS=$((VIOLATIONS + 1))
        fi
    fi
    
    # Route dosyaları için kontrol
    if [[ "$file" =~ routes.*\.php$ ]]; then
        # crm.* route kontrolü
        if grep -qE "route\('crm\." "$file" 2>/dev/null | grep -qv "admin\|Context7\|//\|/\*"; then
            echo -e "${RED}❌ Context7 Violation:${NC} $file"
            echo -e "   ${RED}Pattern:${NC} 'crm.*' route usage"
            echo -e "   ${RED}→${NC} Use 'admin.*' instead of 'crm.*'"
            echo ""
            VIOLATIONS=$((VIOLATIONS + 1))
        fi
    fi
    
    # Blade dosyaları için kontrol
    if [[ "$file" =~ \.blade\.php$ ]]; then
        # layouts.app kontrolü
        if grep -qE "@extends\('layouts\.app'\)" "$file" 2>/dev/null | grep -qv "admin.layouts.neo\|Context7\|//"; then
            echo -e "${RED}❌ Context7 Violation:${NC} $file"
            echo -e "   ${RED}Pattern:${NC} 'layouts.app' usage"
            echo -e "   ${RED}→${NC} Use 'admin.layouts.neo' instead"
            echo ""
            VIOLATIONS=$((VIOLATIONS + 1))
        fi
    fi
done

# İhlal varsa commit'i engelle
if [ $VIOLATIONS -gt 0 ]; then
    echo -e "${RED}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${RED}🚨 $VIOLATIONS Context7 ihlali bulundu!${NC}"
    echo -e "${RED}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo ""
    echo -e "${YELLOW}Context7 Standartları:${NC}"
    echo -e "   - .context7/ORDER_DISPLAY_ORDER_STANDARD.md"
    echo -e "   - .context7/MIGRATION_STANDARDS.md"
    echo -e "   - .context7/authority.json"
    echo ""
    echo -e "${YELLOW}Commit engellendi. Lütfen ihlalleri düzeltin.${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Context7 compliance check passed!${NC}"
exit 0

