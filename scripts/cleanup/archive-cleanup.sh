#!/bin/bash

###############################################################################
# Archive & Backup Cleanup Script - Yalıhan Emlak
# Güvenli temizlik için interaktif script
# Tarih: 25 Kasım 2025
###############################################################################

set -e

# Renkler
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Loglar
log_info() { echo -e "${BLUE}[INFO]${NC} $1"; }
log_success() { echo -e "${GREEN}[✓]${NC} $1"; }
log_warning() { echo -e "${YELLOW}[⚠]${NC} $1"; }
log_error() { echo -e "${RED}[✗]${NC} $1"; }

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "$PROJECT_ROOT"

###############################################################################
# CLEANUP FONKSIYONLARI
###############################################################################

cleanup_phase_1() {
    log_info "Faze 1: Düşük Risk Temizliği Başlatılıyor..."
    log_warning "Bu faze tamamen güvenlidir (2.5 MB tasarruf)"

    # Tar.gz arşivleri
    if [[ -f "docs/archive/2024-docs-archive.tar.gz" ]]; then
        log_info "Siliniyor: docs/archive/2024-docs-archive.tar.gz"
        rm -f "docs/archive/2024-docs-archive.tar.gz"
        log_success "Silindi"
    fi

    if [[ -f "docs/archive/legacy-docs-2024-2025.tar.gz" ]]; then
        log_info "Siliniyor: docs/archive/legacy-docs-2024-2025.tar.gz"
        rm -f "docs/archive/legacy-docs-2024-2025.tar.gz"
        log_success "Silindi"
    fi

    if [[ -f "docs/archive/old-complex-docs-archive.tar.gz" ]]; then
        log_info "Siliniyor: docs/archive/old-complex-docs-archive.tar.gz"
        rm -f "docs/archive/old-complex-docs-archive.tar.gz"
        log_success "Silindi"
    fi

    # Dead code arşivleri
    if [[ -d "archive/dead-code-20251111" ]]; then
        log_info "Siliniyor: archive/dead-code-20251111"
        rm -rf "archive/dead-code-20251111"
        log_success "Silindi"
    fi

    if [[ -d "archive/dead-code-safe-20251111" ]]; then
        log_info "Siliniyor: archive/dead-code-safe-20251111"
        rm -rf "archive/dead-code-safe-20251111"
        log_success "Silindi"
    fi

    # Yalıhan Bekçi eski arşivi
    if [[ -d ".yalihan-bekci/archive" ]]; then
        log_info "Siliniyor: .yalihan-bekci/archive"
        rm -rf ".yalihan-bekci/archive"
        log_success "Silindi"
    fi

    log_success "Faze 1 Tamamlandı (2.5 MB tasarruf)"
}

cleanup_phase_2() {
    log_info "Faze 2: Orta Risk Temizliği Başlatılıyor..."
    log_warning "Backup'ların başka yerde olduğundan emin olun"

    # Backup SQL
    if [[ -f "backup_before_migration_"* ]]; then
        log_info "Siliniyor: Eski backup SQL dosyaları"
        rm -f backup_before_migration_*.sql
        log_success "Silindi"
    fi

    # Deprecated scripts
    if [[ -d "scripts/archive" ]]; then
        log_info "Siliniyor: scripts/archive"
        rm -rf "scripts/archive"
        log_success "Silindi"
    fi

    # Eski SQL script'leri
    if [[ -d "database/scripts" ]]; then
        log_info "Siliniyor: database/scripts/*.sql"
        rm -f "database/scripts"/*.sql 2>/dev/null || true
        log_success "Silindi"
    fi

    # Eski dönem klasörleri
    if [[ -d "docs/archive/2025-11-04-completed" ]]; then
        log_info "Siliniyor: docs/archive/2025-11-04-completed"
        rm -rf "docs/archive/2025-11-04-completed"
        log_success "Silindi"
    fi

    if [[ -d "docs/archive/completed-migrations" ]]; then
        log_info "Siliniyor: docs/archive/completed-migrations"
        rm -rf "docs/archive/completed-migrations"
        log_success "Silindi"
    fi

    log_success "Faze 2 Tamamlandı (1.8 MB tasarruf)"
}

cleanup_phase_3() {
    log_info "Faze 3: Yüksek Risk Temizliği Başlatılıyor..."
    log_warning "Bu adım son onayı gerektirir"

    read -p "Antigravity dosyaları silinsin mi? (e/h): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Ee]$ ]]; then
        log_info "Siliniyor: antigravity dosyaları"
        rm -f antigravity.js antigravity.mjs antigravity_config.json antigravity_rules.md
        log_success "Silindi"
    fi

    read -p "Eski dokümentasyon (SORUN_ANALIZI.md, COMMIT_STRATEGY.md) silinsin mi? (e/h): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Ee]$ ]]; then
        log_info "Siliniyor: Eski dokümentasyon"
        rm -f SORUN_ANALIZI.md COMMIT_STRATEGY.md
        log_success "Silindi"
    fi

    read -p "Schema dump (database/schema/mysql-schema.sql) silinsin mi? (e/h): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Ee]$ ]]; then
        log_info "Siliniyor: database/schema/mysql-schema.sql"
        rm -f "database/schema/mysql-schema.sql"
        log_success "Silindi"
    fi

    log_success "Faze 3 Tamamlandı (0.9 MB tasarruf potansiyel)"
}

print_menu() {
    echo
    echo -e "${BLUE}═══════════════════════════════════════════════${NC}"
    echo -e "${BLUE}  Yalıhan Emlak - Archive Cleanup Script${NC}"
    echo -e "${BLUE}═══════════════════════════════════════════════${NC}"
    echo
    echo "Mevcut Boyutlar:"
    du -sh docs/archive archive .yalihan-bekci/archive 2>/dev/null | awk '{print "  " $1 " → " $2}' || true
    echo
    echo "Temizlik Seçenekleri:"
    echo "  1) Faze 1: Güvenli Temizlik (2.5 MB) - Önerilen ✅"
    echo "  2) Faze 2: Orta Risk (1.8 MB) - Backup kontrol edin"
    echo "  3) Faze 3: Yüksek Risk (0.9 MB) - Onay gerekli"
    echo "  4) Hepsi Çalıştır (Tüm Fazeler)"
    echo "  5) Durum Kontrol Et"
    echo "  6) Çıkış"
    echo
}

check_status() {
    log_info "Proje Boyutları:"
    echo
    du -sh docs/archive archive .yalihan-bekci/archive backup* 2>/dev/null | awk '{printf "  %-45s %8s\n", $2, $1}' || true

    log_info "Temizlenebilir Dosyalar:"
    echo
    [[ -f "docs/archive/2024-docs-archive.tar.gz" ]] && echo "  ✗ docs/archive/2024-docs-archive.tar.gz"
    [[ -f "docs/archive/legacy-docs-2024-2025.tar.gz" ]] && echo "  ✗ docs/archive/legacy-docs-2024-2025.tar.gz"
    [[ -f "docs/archive/old-complex-docs-archive.tar.gz" ]] && echo "  ✗ docs/archive/old-complex-docs-archive.tar.gz"
    [[ -d "archive/dead-code-20251111" ]] && echo "  ✗ archive/dead-code-20251111"
    [[ -d ".yalihan-bekci/archive" ]] && echo "  ✗ .yalihan-bekci/archive"
    [[ -f "backup_before_migration_"* ]] && echo "  ✗ Backup SQL dosyaları"
    [[ -f "antigravity.js" ]] && echo "  ✗ antigravity.js (KULLANILMIYOR)"

    echo
}

###############################################################################
# MAIN
###############################################################################

log_info "Proje Kökü: $PROJECT_ROOT"

while true; do
    print_menu
    read -p "Seçiminiz (1-6): " choice

    case $choice in
        1)
            cleanup_phase_1
            ;;
        2)
            cleanup_phase_2
            ;;
        3)
            cleanup_phase_3
            ;;
        4)
            cleanup_phase_1
            echo
            read -p "Faze 2 devam etsin mi? (e/h): " -n 1 -r
            echo
            [[ $REPLY =~ ^[Ee]$ ]] && cleanup_phase_2
            echo
            read -p "Faze 3 devam etsin mi? (e/h): " -n 1 -r
            echo
            [[ $REPLY =~ ^[Ee]$ ]] && cleanup_phase_3
            ;;
        5)
            check_status
            ;;
        6)
            log_success "Çıkılıyor..."
            exit 0
            ;;
        *)
            log_error "Geçersiz seçim"
            ;;
    esac
done
