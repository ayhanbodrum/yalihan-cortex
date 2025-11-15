#!/bin/bash

# Context7 Daily Compliance Check
# Günlük otomatik Context7 compliance kontrolü
# Kullanım: ./scripts/context7-daily-check.sh [--help]

set -euo pipefail

# Help option
if [[ "${1:-}" == "--help" ]] || [[ "${1:-}" == "-h" ]]; then
    echo "Context7 Daily Compliance Check"
    echo ""
    echo "Kullanım: $0 [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  --help, -h    Bu yardım mesajını göster"
    echo ""
    echo "Açıklama:"
    echo "  Günlük otomatik Context7 compliance kontrolü yapar."
    echo "  Rapor .context7/daily-reports/ klasörüne kaydedilir."
    echo ""
    exit 0
fi

# Source helper libraries
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/lib/common.sh"
source "$SCRIPT_DIR/lib/logger.sh"

# Setup logging
setup_logging_trap

# Tarih
DATE=$(date +%Y%m%d)
TIMESTAMP=$(date +%Y%m%d-%H%M%S)
REPORT_DIR=".context7/daily-reports"
REPORT_FILE="${REPORT_DIR}/compliance-${TIMESTAMP}.md"

# Rapor dizini oluştur
mkdir -p "$REPORT_DIR"

# Check dependencies
check_dependencies "bash" "grep"

print_header "Context7 Daily Compliance Check"

# Context7 full scan çalıştır
print_info "Context7 Compliance Scanner çalıştırılıyor..."
log_info "Tarama başlatıldı: $REPORT_FILE"

if ./scripts/context7-full-scan.sh --report "$REPORT_FILE" 2>&1 | tee "${REPORT_DIR}/scan-${TIMESTAMP}.log"; then
    VIOLATIONS=$(grep -c "Toplam İhlal:" "$REPORT_FILE" || echo "0")

    if [ "$VIOLATIONS" -eq 0 ]; then
        echo ""
        print_success "Harika! Hiç ihlal bulunamadı."
        print_success "Context7 compliance: %100"
        log_info "Tarama tamamlandı: 0 ihlal"
        exit_with_success "Günlük kontrol başarılı"
    else
        echo ""
        print_error "$VIOLATIONS ihlal bulundu!"
        print_warning "Detaylı rapor: $REPORT_FILE"
        log_error "Tarama tamamlandı: $VIOLATIONS ihlal"
        exit_with_error "Günlük kontrol başarısız: $VIOLATIONS ihlal" 1
    fi
else
    print_error "Tarama başarısız!"
    log_error "Tarama hatası"
    exit_with_error "Context7 tarama başarısız" 1
fi

