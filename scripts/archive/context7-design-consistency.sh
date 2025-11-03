#!/bin/bash
set -euo pipefail

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🎨 Context7 Design Consistency Check (Neo Design System)${NC}"
echo "========================================================"

ERRORS=0

check_section() { echo "\n$1"; echo "----------------------------------------"; }
report_ok() { echo -e "${GREEN}✅ $1${NC}"; }
report_warn() { echo -e "${YELLOW}⚠️  $1${NC}"; }
report_err() { echo -e "${RED}❌ $1${NC}"; ERRORS=$((ERRORS+1)); }

check_section "1) Legacy CSS sınıfları (yasak)"
LEGACY_COUNT=$(grep -RIn "\b(btn-|card-|form-)" resources/views/ 2>/dev/null | wc -l || true)
if [ "$LEGACY_COUNT" -gt 0 ]; then
  report_err "Legacy sınıflar bulundu: $LEGACY_COUNT adet"
  grep -RIn "\b(btn-|card-|form-)" resources/views/ 2>/dev/null | head -15
else
  report_ok "Legacy sınıflar bulunamadı"
fi

check_section "2) Neo Design System sınıfları (beklenen kullanım)"
NEO_COUNT=$(grep -RIn "neo-(btn|card|input|form|badge|table|grid)" resources/views/ 2>/dev/null | wc -l || true)
if [ "$NEO_COUNT" -gt 0 ]; then
  report_ok "Neo sınıfları kullanılıyor: $NEO_COUNT referans"
else
  report_warn "Neo sınıfları referansı bulunamadı. Tasarım sistemi entegre mi?"
fi

check_section "3) Responsive sınıflar (sm:/md:/lg:/xl:)"
RESP_COUNT=$(grep -RIn "\b(sm:|md:|lg:|xl:)" resources/views/ 2>/dev/null | wc -l || true)
if [ "$RESP_COUNT" -gt 0 ]; then
  report_ok "Responsive sınıflar kullanılıyor: $RESP_COUNT referans"
else
  report_warn "Responsive sınıf bulunamadı. Mobil uyumluluk kontrol edilmeli"
fi

check_section "4) Dark mode sınıfları (dark:)"
DARK_COUNT=$(grep -RIn "\bdark:" resources/views/ 2>/dev/null | wc -l || true)
if [ "$DARK_COUNT" -gt 0 ]; then
  report_ok "Dark mode sınıfları kullanılıyor: $DARK_COUNT referans"
else
  report_warn "Dark mode sınıfı bulunamadı"
fi

check_section "5) Zorunlu asset/yapı kontrolü"
# Neo CSS asset'inin layout'lara dahil edilip edilmediğini basit kontrol
if grep -RIn "neo-components.css" resources/views/layouts/ 2>/dev/null | grep -q "link\|@vite"; then
  report_ok "Neo CSS asset include mevcut (neo-components.css)"
else
  report_warn "Neo CSS asset include bulunamadı (neo-components.css)"
fi

check_section "6) Blade component kullanımı (x-neo.*)"
NEO_CMP_COUNT=$(grep -RIn "<x-neo\." resources/views/ 2>/dev/null | wc -l || true)
if [ "$NEO_CMP_COUNT" -gt 0 ]; then
  report_ok "Neo Blade component kullanımı var: $NEO_CMP_COUNT"
else
  report_warn "Neo Blade component kullanımı bulunamadı"
fi

check_section "7) Bootstrap/JQuery kalıntıları (yasak)"
BOOT_COUNT=$(grep -RIn "\b(container|row|col-\d|col-sm-|col-md-|col-lg-|modal|jquery|\$\()" resources/views/ 2>/dev/null | wc -l || true)
if [ "$BOOT_COUNT" -gt 0 ]; then
  report_err "Bootstrap/jQuery kalıntıları bulundu: $BOOT_COUNT referans"
  grep -RIn "\b(container|row|col-\d|col-sm-|col-md-|col-lg-|modal|jquery|\$\()" resources/views/ 2>/dev/null | head -15
else
  report_ok "Bootstrap/jQuery kalıntısı yok"
fi

check_section "8) Inline style ve !important (risk)"
STYLE_COUNT=$(grep -RIn "style=|!important" resources/views/ 2>/dev/null | wc -l || true)
if [ "$STYLE_COUNT" -gt 0 ]; then
  report_warn "Inline style/!important kullanımı: $STYLE_COUNT referans"
else
  report_ok "Inline style/!important yok"
fi

echo "\n========================================================"
if [ "$ERRORS" -eq 0 ]; then
  echo -e "${GREEN}🎉 Tasarım tutarlılığı kontrolü başarıyla geçti${NC}"
  exit 0
else
  echo -e "${RED}❌ Tasarım tutarlılığı sorunları tespit edildi (ERRORS=$ERRORS)${NC}"
  exit 1
fi
