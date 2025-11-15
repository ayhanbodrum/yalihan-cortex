#!/bin/bash
# Context7 Unified Compliance Scanner
# Birleştirilmiş scanner script'i - Tüm scanner özelliklerini içerir
# Kullanım: ./scripts/context7-scanner-unified.sh [--mode=full|quick|pre-commit] [--format=json|markdown|text] [--report=file]

set -euo pipefail

# Source helper libraries
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/lib/common.sh"
source "$SCRIPT_DIR/lib/logger.sh"

# Setup logging
setup_logging_trap

# Default values
MODE="full"
FORMAT="text"
REPORT_FILE=""
QUICK_MODE=false
PRE_COMMIT_MODE=false

# Parse arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        --mode=*)
            MODE="${1#*=}"
            shift
            ;;
        --format=*)
            FORMAT="${1#*=}"
            shift
            ;;
        --report=*)
            REPORT_FILE="${1#*=}"
            shift
            ;;
        --quick)
            QUICK_MODE=true
            MODE="quick"
            shift
            ;;
        --pre-commit)
            PRE_COMMIT_MODE=true
            MODE="pre-commit"
            shift
            ;;
        --help|-h)
            echo "Context7 Unified Compliance Scanner"
            echo ""
            echo "Kullanım: $0 [OPTIONS]"
            echo ""
            echo "Options:"
            echo "  --mode=full|quick|pre-commit  Tarama modu (default: full)"
            echo "  --format=json|markdown|text   Çıktı formatı (default: text)"
            echo "  --report=FILE                 Rapor dosyası"
            echo "  --quick                       Hızlı tarama modu"
            echo "  --pre-commit                  Pre-commit modu"
            echo "  --help, -h                    Bu yardım mesajını göster"
            echo ""
            echo "Örnekler:"
            echo "  $0 --mode=full --format=markdown --report=report.md"
            echo "  $0 --quick"
            echo "  $0 --pre-commit"
            exit 0
            ;;
        *)
            print_error "Bilinmeyen parametre: $1"
            echo "Kullanım: $0 --help"
            exit 1
            ;;
    esac
done

# Validate mode
case $MODE in
    full|quick|pre-commit)
        ;;
    *)
        print_error "Geçersiz mod: $MODE"
        echo "Geçerli modlar: full, quick, pre-commit"
        exit 1
        ;;
esac

# Validate format
case $FORMAT in
    json|markdown|text)
        ;;
    *)
        print_error "Geçersiz format: $FORMAT"
        echo "Geçerli formatlar: json, markdown, text"
        exit 1
        ;;
esac

# Check dependencies
check_dependencies "bash" "grep" "find"

# Initialize
VIOLATIONS=0
VIOLATION_LIST=()

print_header "Context7 Unified Compliance Scanner"
print_info "Mod: $MODE | Format: $FORMAT"
log_info "Scanner başlatıldı: mode=$MODE, format=$FORMAT"

# Function to add violation
add_violation() {
    local severity=$1
    local file=$2
    local line=$3
    local pattern=$4
    local message=$5
    
    VIOLATIONS=$((VIOLATIONS + 1))
    VIOLATION_LIST+=("$severity|$file|$line|$pattern|$message")
    
    if [ "$FORMAT" = "text" ]; then
        print_error "❌ $severity: $file:$line"
        echo "   Pattern: $pattern"
        echo "   → $message"
        echo ""
    fi
}

# Check order column
check_order_column() {
    print_info "📋 Database Fields: order → display_order"
    
    local search_path="app/"
    if [ "$MODE" = "pre-commit" ]; then
        search_path=$(git diff --cached --name-only --diff-filter=ACM | grep -E '\.(php)$' || true)
    fi
    
    while IFS= read -r line; do
        local file=$(echo "$line" | cut -d: -f1)
        local line_num=$(echo "$line" | cut -d: -f2)
        local content=$(echo "$line" | cut -d: -f3- | sed 's/^[[:space:]]*//')
        
        # Skip comments
        if [[ "$content" =~ ^(//|\*|#) ]]; then
            continue
        fi
        
        # Skip if display_order is already used
        if [[ "$content" =~ display_order ]]; then
            continue
        fi
        
        # Skip orderBy, orderByRaw, etc.
        if [[ "$content" =~ orderBy|orderByRaw|orderByDesc|reorder ]]; then
            continue
        fi
        
        add_violation "CRITICAL" "$file" "$line_num" "'order'" "Use 'display_order' instead of 'order'"
    done < <(grep -rnE "'order'|\"order\"|order\s*=>" $search_path --include="*.php" 2>/dev/null | head -20 || true)
}

# Check status fields
check_status_fields() {
    print_info "📋 Status Fields: enabled/aktif/is_active → status"
    
    local search_path="app/"
    if [ "$MODE" = "pre-commit" ]; then
        search_path=$(git diff --cached --name-only --diff-filter=ACM | grep -E '\.(php)$' || true)
    fi
    
    while IFS= read -r line; do
        local file=$(echo "$line" | cut -d: -f1)
        local line_num=$(echo "$line" | cut -d: -f2)
        local content=$(echo "$line" | cut -d: -f3- | sed 's/^[[:space:]]*//')
        
        # Skip comments
        if [[ "$content" =~ ^(//|\*|#) ]]; then
            continue
        fi
        
        # Skip if status is already used
        if [[ "$content" =~ status ]]; then
            continue
        fi
        
        add_violation "CRITICAL" "$file" "$line_num" "enabled/aktif/is_active" "Use 'status' instead"
    done < <(grep -rnE "'enabled'|\"enabled\"|'aktif'|\"aktif\"|'is_active'|\"is_active\"" $search_path --include="*.php" 2>/dev/null | head -20 || true)
}

# Main scan function
run_scan() {
    log_info "Tarama başlatıldı"
    
    if [ "$MODE" = "quick" ]; then
        # Quick mode: Only check critical violations
        check_order_column
    elif [ "$MODE" = "pre-commit" ]; then
        # Pre-commit mode: Check staged files only
        check_order_column
        check_status_fields
    else
        # Full mode: Check everything
        check_order_column
        check_status_fields
        # Add more checks here
    fi
    
    log_info "Tarama tamamlandı: $VIOLATIONS ihlal"
}

# Generate report
generate_report() {
    if [ -z "$REPORT_FILE" ]; then
        REPORT_FILE=".context7/compliance-report-$(date +%Y%m%d-%H%M%S).md"
    fi
    
    mkdir -p "$(dirname "$REPORT_FILE")"
    
    if [ "$FORMAT" = "markdown" ]; then
        {
            echo "# Context7 Compliance Report"
            echo ""
            echo "**Tarih:** $(date '+%Y-%m-%d %H:%M:%S')"
            echo "**Mod:** $MODE"
            echo "**Toplam İhlal:** $VIOLATIONS"
            echo ""
            echo "## İhlaller"
            echo ""
            for violation in "${VIOLATION_LIST[@]}"; do
                IFS='|' read -r severity file line pattern message <<< "$violation"
                echo "### $severity: $file:$line"
                echo "- **Pattern:** $pattern"
                echo "- **Mesaj:** $message"
                echo ""
            done
        } > "$REPORT_FILE"
        
        print_success "Rapor oluşturuldu: $REPORT_FILE"
    elif [ "$FORMAT" = "json" ]; then
        {
            echo "{"
            echo "  \"date\": \"$(date '+%Y-%m-%d %H:%M:%S')\","
            echo "  \"mode\": \"$MODE\","
            echo "  \"violations\": $VIOLATIONS,"
            echo "  \"violation_list\": ["
            for i in "${!VIOLATION_LIST[@]}"; do
                IFS='|' read -r severity file line pattern message <<< "${VIOLATION_LIST[$i]}"
                echo "    {"
                echo "      \"severity\": \"$severity\","
                echo "      \"file\": \"$file\","
                echo "      \"line\": \"$line\","
                echo "      \"pattern\": \"$pattern\","
                echo "      \"message\": \"$message\""
                if [ $i -lt $((${#VIOLATION_LIST[@]} - 1)) ]; then
                    echo "    },"
                else
                    echo "    }"
                fi
            done
            echo "  ]"
            echo "}"
        } > "$REPORT_FILE"
        
        print_success "Rapor oluşturuldu: $REPORT_FILE"
    fi
}

# Main execution
run_scan

# Generate report if requested
if [ -n "$REPORT_FILE" ] || [ "$FORMAT" != "text" ]; then
    generate_report
fi

# Summary
print_footer "Tarama Özeti"
echo "Toplam İhlal: $VIOLATIONS"

if [ $VIOLATIONS -eq 0 ]; then
    print_success "Harika! Hiç ihlal bulunamadı."
    log_info "Tarama başarılı: 0 ihlal"
    exit_with_success "Context7 compliance check passed"
else
    print_error "$VIOLATIONS ihlal bulundu!"
    log_error "Tarama başarısız: $VIOLATIONS ihlal"
    exit_with_error "Context7 compliance check failed: $VIOLATIONS violations" 1
fi

