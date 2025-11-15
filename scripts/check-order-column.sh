#!/bin/bash

# Context7 Order Column Check
# Pre-commit hook script for checking 'order' column usage
# Context7 Standard: order → display_order

set -euo pipefail

# Source helper libraries
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/lib/common.sh"
source "$SCRIPT_DIR/lib/logger.sh"

# Get files from pre-commit
FILES="$@"

# If no files provided, exit
if [ -z "$FILES" ]; then
    exit 0
fi

VIOLATIONS=0

log_info "Pre-commit hook: Order column check başladı"

# Check each file
for file in $FILES; do
    # Skip if file doesn't exist
    if [ ! -f "$file" ]; then
        continue
    fi
    
    # Check for 'order' column usage (excluding display_order, orderBy, etc.)
    if grep -rnE "'order'|\"order\"|order\s*=>" "$file" 2>/dev/null | grep -v "display_order\|orderBy\|orderByRaw\|orderByDesc\|reorder\|Context7\|//\|/\*\|@order\|order.*display\|display.*order" > /dev/null; then
        print_error "Context7 Violation: $file"
        echo "   Pattern: 'order' column usage"
        echo "   → Use 'display_order' instead of 'order'"
        echo ""
        log_error "İhlal bulundu: $file"
        VIOLATIONS=$((VIOLATIONS + 1))
    fi
done

# Exit with error if violations found
if [ $VIOLATIONS -gt 0 ]; then
    print_error "$VIOLATIONS violation(s) found!"
    echo "Context7 Standard: 'order' → 'display_order'"
    echo "See: .context7/ORDER_DISPLAY_ORDER_STANDARD.md"
    log_error "Pre-commit hook başarısız: $VIOLATIONS ihlal"
    exit 1
fi

print_success "Context7 compliance check passed!"
log_info "Pre-commit hook başarılı: 0 ihlal"
exit 0

