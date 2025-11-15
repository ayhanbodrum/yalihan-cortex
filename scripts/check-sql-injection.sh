#!/bin/bash

# SQL Injection Check
# Pre-commit hook for detecting potential SQL injection vulnerabilities

set -euo pipefail

FILES="$@"

if [ -z "$FILES" ]; then
    exit 0
fi

VIOLATIONS=0

for file in $FILES; do
    if [ ! -f "$file" ]; then
        continue
    fi
    
    # Check for raw SQL with user input (potential SQL injection)
    if grep -qE "DB::(raw|select|statement).*\$_(GET|POST|REQUEST)" "$file" 2>/dev/null; then
        echo "⚠️  Potential SQL injection risk in: $file"
        echo "   → Use parameterized queries or Eloquent instead"
        VIOLATIONS=$((VIOLATIONS + 1))
    fi
    
    # Check for direct variable interpolation in SQL
    if grep -qE "DB::(raw|select|statement).*\"[^\"]*\{\$[a-zA-Z_]" "$file" 2>/dev/null; then
        echo "⚠️  Potential SQL injection risk in: $file"
        echo "   → Use parameterized queries instead"
        VIOLATIONS=$((VIOLATIONS + 1))
    fi
done

if [ $VIOLATIONS -gt 0 ]; then
    echo "❌ $VIOLATIONS potential SQL injection risk(s) found!"
    exit 1
fi

exit 0

