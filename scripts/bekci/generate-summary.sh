#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DATE_STAMP="$(date +%Y-%m-%d_%H-%M-%S)"
REPORT_DIR="$ROOT_DIR/../yalihan-bekci/reports/summary"
OUTPUT_FILE="$REPORT_DIR/bekci-summary-$DATE_STAMP.md"

mkdir -p "$REPORT_DIR"

CONTEXT7_LOG="$(mktemp)"
INCOMPLETE_LOG="$(mktemp)"

php artisan context7:check > "$CONTEXT7_LOG"
php scripts/find-incomplete-code.php > "$INCOMPLETE_LOG"

TODO_COUNT="$(grep -m1 'TODO/FIXME' "$INCOMPLETE_LOG" | grep -oE '[0-9]+')"
STUB_COUNT="$(grep -m1 'Stub Metodlar' "$INCOMPLETE_LOG" | grep -oE '[0-9]+')"
COMMENTED_COUNT="$(grep -m1 'Yorum Satırına Alınmış Kod' "$INCOMPLETE_LOG" | grep -oE '[0-9]+')"

INCOMPLETE_JSON="$(grep -m1 'Rapor:' "$INCOMPLETE_LOG" | awk -F'Rapor: ' '{print $2}' | tr -d '[:space:]')"

{
  echo "# Yalıhan Bekçi Gündemi — $(date +%Y-%m-%d)"
  echo
  echo "## 1. Context7 Durumu"
  cat "$CONTEXT7_LOG"
  echo
  echo "## 2. Yarım Kalmış Kod Özeti"
  echo "- TODO/FIXME Yorumları: ${TODO_COUNT}"
  echo "- Stub Metodlar: ${STUB_COUNT}"
  echo "- Yorum Satırına Alınmış Kod: ${COMMENTED_COUNT}"
  if [[ -n "$INCOMPLETE_JSON" ]]; then
    echo "- Detaylı JSON Raporu: $INCOMPLETE_JSON"
  fi
  echo
  echo "### Detaylı Çıktı"
  echo "<details>"
  echo "<summary>Analiz Logu</summary>"
  echo
  echo '```'
  cat "$INCOMPLETE_LOG"
  echo '```'
  echo
  echo "</details>"
} > "$OUTPUT_FILE"

rm -f "$CONTEXT7_LOG" "$INCOMPLETE_LOG"

echo "Bekçi özeti oluşturuldu: $OUTPUT_FILE"

