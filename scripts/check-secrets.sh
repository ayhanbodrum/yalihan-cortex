#!/bin/bash

# Check for secrets in code
# Pre-commit hook for detecting secrets

set -euo pipefail

FILES="$@"

if [ -z "$FILES" ]; then
    exit 0
fi

# Secret patterns
PATTERNS=(
    "password.*=.*['\"].*['\"]"
    "api_key.*=.*['\"].*['\"]"
    "secret.*=.*['\"].*['\"]"
    "token.*=.*['\"].*['\"]"
    "AKIA[0-9A-Z]{16}"  # AWS Access Key
    "sk_live_[0-9a-zA-Z]{32}"  # Stripe secret key
)

VIOLATIONS=0

for file in $FILES; do
    if [ ! -f "$file" ]; then
        continue
    fi
    
    for pattern in "${PATTERNS[@]}"; do
        if grep -qiE "$pattern" "$file" 2>/dev/null | grep -qv "example\|test\|dummy\|placeholder\|//"; then
            echo "⚠️  Potential secret found in: $file"
            VIOLATIONS=$((VIOLATIONS + 1))
        fi
    done
done

if [ $VIOLATIONS -gt 0 ]; then
    echo "❌ $VIOLATIONS potential secret(s) found!"
    exit 1
fi

exit 0

