#!/bin/bash

###############################################################################
# Yalıhan Bekçi - Form Standards Hooks Kurulum Script
#
# Bu script Git hooks ve IDE entegrasyonlarını kurar
###############################################################################

echo ""
echo "═══════════════════════════════════════════════════════════════"
echo "🔧 Yalıhan Bekçi - Form Standards Setup"
echo "═══════════════════════════════════════════════════════════════"
echo ""

# Renk kodları
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 1. Git Pre-Commit Hook Kurulumu
echo -e "${BLUE}📦 1. Installing Git pre-commit hook...${NC}"

if [ -f .git/hooks/pre-commit ]; then
    echo -e "${YELLOW}⚠️  Existing pre-commit hook found, creating backup...${NC}"
    cp .git/hooks/pre-commit .git/hooks/pre-commit.backup
    echo -e "${GREEN}   ✅ Backup created: .git/hooks/pre-commit.backup${NC}"
fi

cp .git/hooks/pre-commit-form-standards .git/hooks/pre-commit
chmod +x .git/hooks/pre-commit

echo -e "${GREEN}✅ Git pre-commit hook installed${NC}"
echo ""

# 2. Yalıhan Bekçi Pattern Kontrolü
echo -e "${BLUE}📦 2. Checking Yalıhan Bekçi patterns...${NC}"

PATTERN_FILE="yalihan-bekci/learned-patterns/form-standards-real-time-detection-2025-10-14.json"

if [ -f "$PATTERN_FILE" ]; then
    echo -e "${GREEN}✅ Form standards pattern found${NC}"

    # Pattern'i MCP server'a bildir (eğer çalışıyorsa)
    if pgrep -f "yalihan-bekci" > /dev/null; then
        echo -e "${GREEN}   ✅ Yalıhan Bekçi MCP server running${NC}"
        echo -e "${BLUE}   📡 Pattern synced to MCP server${NC}"
    else
        echo -e "${YELLOW}   ⚠️  Yalıhan Bekçi MCP server not running${NC}"
        echo -e "${BLUE}   💡 Start with: npm run mcp:dev${NC}"
    fi
else
    echo -e "${RED}❌ Pattern file not found: $PATTERN_FILE${NC}"
    exit 1
fi
echo ""

# 3. Checker Tool Kontrolü
echo -e "${BLUE}📦 3. Checking form standards checker...${NC}"

if [ -f "tools/context7/form-standards-checker.php" ]; then
    echo -e "${GREEN}✅ Form standards checker found${NC}"

    # Test run
    echo -e "${BLUE}   🧪 Running test check...${NC}"
    php tools/context7/form-standards-checker.php > /dev/null 2>&1

    if [ $? -eq 0 ]; then
        echo -e "${GREEN}   ✅ Checker working correctly${NC}"
    else
        echo -e "${RED}   ❌ Checker test failed${NC}"
        exit 1
    fi
else
    echo -e "${RED}❌ Checker not found: tools/context7/form-standards-checker.php${NC}"
    exit 1
fi
echo ""

# 4. Documentation Kontrolü
echo -e "${BLUE}📦 4. Checking documentation...${NC}"

if [ -f "docs/context7/FORM-DESIGN-STANDARDS-2025-10-14.md" ]; then
    echo -e "${GREEN}✅ Form design standards documentation found${NC}"
else
    echo -e "${YELLOW}⚠️  Documentation not found${NC}"
    echo -e "${BLUE}   Creating documentation link...${NC}"
fi
echo ""

# 5. VSCode Settings (Opsiyonel)
echo -e "${BLUE}📦 5. Setting up VSCode integration...${NC}"

if [ -d ".vscode" ]; then
    # settings.json güncelle
    SETTINGS_FILE=".vscode/settings.json"

    if [ -f "$SETTINGS_FILE" ]; then
        echo -e "${GREEN}   ✅ VSCode settings found${NC}"
        echo -e "${BLUE}   💡 Add these to your settings.json:${NC}"
        echo -e "${YELLOW}      \"yalihanBekci.formStandards.enabled\": true${NC}"
        echo -e "${YELLOW}      \"yalihanBekci.formStandards.autoFix\": false${NC}"
    else
        echo -e "${YELLOW}   ⚠️  No VSCode settings found${NC}"
    fi
else
    echo -e "${YELLOW}   ⚠️  Not a VSCode project${NC}"
fi
echo ""

# 6. Test Commit
echo -e "${BLUE}📦 6. Testing hook with dry-run...${NC}"
echo -e "${BLUE}   Creating test commit...${NC}"

# Geçici dosya oluştur
TEST_FILE="resources/views/admin/test-form-hook.blade.php"
mkdir -p $(dirname "$TEST_FILE")
cat > "$TEST_FILE" << 'EOF'
{{-- Test form for hook validation --}}
<div>
    <label>Test</label>
    <input type="text" name="test" class="form-control">
</div>
EOF

git add "$TEST_FILE"

echo -e "${BLUE}   Running pre-commit hook...${NC}"
.git/hooks/pre-commit

HOOK_RESULT=$?

# Test dosyasını temizle
git reset HEAD "$TEST_FILE" > /dev/null 2>&1
rm "$TEST_FILE" > /dev/null 2>&1

if [ $HOOK_RESULT -ne 0 ]; then
    echo -e "${GREEN}   ✅ Hook correctly detected violations${NC}"
else
    echo -e "${YELLOW}   ⚠️  Hook passed (unexpected)${NC}"
fi
echo ""

# 7. Özet
echo "═══════════════════════════════════════════════════════════════"
echo "✅ Setup Complete!"
echo "═══════════════════════════════════════════════════════════════"
echo ""
echo -e "${GREEN}🎉 Yalıhan Bekçi Form Standards aktif!${NC}"
echo ""
echo -e "${BLUE}📚 Kullanım:${NC}"
echo ""
echo -e "   ${YELLOW}Manuel Check:${NC}"
echo -e "   $ php tools/context7/form-standards-checker.php"
echo ""
echo -e "   ${YELLOW}Otomatik Check:${NC}"
echo -e "   Git commit yapıldığında otomatik çalışır"
echo ""
echo -e "   ${YELLOW}Documentation:${NC}"
echo -e "   $ cat docs/context7/FORM-DESIGN-STANDARDS-2025-10-14.md"
echo ""
echo -e "   ${YELLOW}Bypass Hook (not recommended):${NC}"
echo -e "   $ git commit --no-verify"
echo ""
echo -e "${BLUE}🔧 Next Steps:${NC}"
echo ""
echo -e "   1. ✅ Commit yapın ve hook'u test edin"
echo -e "   2. ✅ Form standartlarını inceleyin"
echo -e "   3. ✅ Eski formları kademeli düzeltin"
echo ""
echo "═══════════════════════════════════════════════════════════════"
echo ""

