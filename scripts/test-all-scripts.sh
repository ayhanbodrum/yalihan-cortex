#!/bin/bash
# Test All Scripts
# Usage: ./scripts/test-all-scripts.sh

set -euo pipefail

# Source common functions
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/lib/common.sh"

# Test results
PASSED=0
FAILED=0
TOTAL=0

# Test a script
test_script() {
    local script=$1
    local script_name=$(basename "$script")
    
    TOTAL=$((TOTAL + 1))
    
    print_info "Testing: $script_name"
    
    # Test 1: Syntax check
    if bash -n "$script" 2>&1; then
        print_success "  ✅ Syntax OK"
    else
        print_error "  ❌ Syntax Error"
        FAILED=$((FAILED + 1))
        return 1
    fi
    
    # Test 2: Executable check
    if [ -x "$script" ]; then
        print_success "  ✅ Executable"
    else
        print_warning "  ⚠️  Not executable (chmod +x recommended)"
    fi
    
    # Test 3: Help check (if --help exists)
    if grep -q "help\|usage\|--help" "$script" 2>/dev/null; then
        if "$script" --help &>/dev/null || "$script" -h &>/dev/null; then
            print_success "  ✅ Help option works"
        else
            print_warning "  ⚠️  Help option exists but doesn't work"
        fi
    fi
    
    # Test 4: Dry-run check (if --dry-run exists)
    if grep -q "dry-run\|--dry-run" "$script" 2>/dev/null; then
        if "$script" --dry-run &>/dev/null; then
            print_success "  ✅ Dry-run works"
        else
            print_warning "  ⚠️  Dry-run exists but doesn't work"
        fi
    fi
    
    PASSED=$((PASSED + 1))
    echo ""
    return 0
}

# Main
print_header "Script Test Suite"

# Find all scripts
SCRIPTS=(
    scripts/context7*.sh
    scripts/check-*.sh
    scripts/fix-*.sh
)

# Test each script
for script_pattern in "${SCRIPTS[@]}"; do
    for script in $script_pattern; do
        if [ -f "$script" ]; then
            test_script "$script"
        fi
    done
done

# Summary
print_footer "Test Sonuçları"
echo "Toplam: $TOTAL"
print_success "Başarılı: $PASSED"
if [ $FAILED -gt 0 ]; then
    print_error "Başarısız: $FAILED"
    exit 1
else
    print_success "Tüm testler başarılı! ✅"
    exit 0
fi

