#!/bin/bash
set -euo pipefail

usage() {
  cat <<USAGE
Context7 Control
Usage:
  $0 check             # run preventive checks (pre-commit grade)
  $0 fix               # run auto-fix then re-check
  $0 report            # print violations log summary if exists
  $0 scan-ilan         # report where 'ilan_kategori_id' appears (code-only)
  $0 fix-ilan          # safely replace 'ilan_kategori_id' -> 'alt_kategori_id' in controllers/views (no migrations)

Notes:
- 'fix-ilan' creates .bak backups and restricts replacements to app/Http, resources/views, public/js
- Migrations/seeders require manual review; script prints locations
USAGE
}

ROOT_DIR=$(pwd)

case "${1:-}" in
  check)
    if [[ -x scripts/context7-prevent-violations.sh ]]; then
      scripts/context7-prevent-violations.sh
    else
      echo "prevent script missing: scripts/context7-prevent-violations.sh"; exit 2
    fi
    ;;
  fix)
    if [[ -x scripts/context7-auto-fix.sh ]]; then
      scripts/context7-auto-fix.sh || true
      if [[ -x scripts/context7-prevent-violations.sh ]]; then
        scripts/context7-prevent-violations.sh
      fi
    else
      echo "auto-fix script missing: scripts/context7-auto-fix.sh"; exit 2
    fi
    ;;
  report)
    if [[ -f docs/context7-violations-log.md ]]; then
      echo "--- Violations Log Head ---"; head -100 docs/context7-violations-log.md
    else
      echo "No violations log found"
    fi
    ;;
  scan-ilan)
    echo "Scanning for ilan_kategori_id occurrences..."
    grep -RIn --line-number --color=never "\\bilan_kategori_id\\b" app/ resources/ public/js || true
    echo "Note: occurrences in database/migrations or seeders shown below for MANUAL review:" 
    grep -RIn --line-number --color=never "\\bilan_kategori_id\\b" database || true
    ;;
  fix-ilan)
    echo "Creating backups and replacing in controllers/views/public js..."
    mapfile -t FILES < <(grep -RIl "\\bilan_kategori_id\\b" app/Http resources/ public/js || true)
    for f in "${FILES[@]}"; do
      [[ -f "$f" ]] || continue
      cp "$f" "$f.bak"
      sed -i '' 's/\bilan_kategori_id\b/alt_kategori_id/g' "$f"
      echo "Fixed: $f"
    done
    echo "Done. Review .bak files if needed."
    ;;
  *)
    usage; exit 1;;
 esac
