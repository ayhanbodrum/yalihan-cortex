#!/bin/bash

# Context7 Violation Quick Check
# Hızlı violation kontrolü ve özet rapor

echo "🔍 Context7 Violation Quick Check"
echo "=================================="
echo ""

# Run scanner
php scripts/context7-violation-scanner.php 2>&1 | grep -E "(Files Scanned|Total Violations|CRITICAL|HIGH|MEDIUM|LOW)" | head -6

echo ""
echo "📊 Latest Reports:"
echo "----------------"
ls -lht .yalihan-bekci/reports/context7-* 2>/dev/null | head -5 | awk '{print $9, "(" $5 ")"}'

echo ""
echo "💡 Quick Actions:"
echo "----------------"
echo "1. View detailed report:"
echo "   cat .yalihan-bekci/reports/context7-violations-*.md | less"
echo ""
echo "2. Export to CSV:"
echo "   open .yalihan-bekci/reports/context7-violations-*.csv"
echo ""
echo "3. Generate new detailed report:"
echo "   php scripts/context7-detailed-reporter.php"
echo ""
