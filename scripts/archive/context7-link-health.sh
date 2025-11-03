#!/bin/bash
BASE="http://127.0.0.1:8000"
COOKIE_JAR="storage/tmp/link_health_cookies.txt"
mkdir -p storage/tmp >/dev/null 2>&1
: > "$COOKIE_JAR"

urls=(
  "/admin"
  "/admin/dashboard"
  "/admin/ilanlar"
  "/admin/kullanicilar"
  "/admin/talepler"
  "/admin/valuation/dashboard"
  "/admin/valuation/parcel-search"
  "/admin/valuation/calculate"
)

echo "# Context7 Link Health Report"
for path in "${urls[@]}"; do
  code=$(curl -s -o /dev/null -w "%{http_code}" -L -c "$COOKIE_JAR" -b "$COOKIE_JAR" "$BASE$path")
  printf "%-40s %s\n" "$path" "$code"
done
