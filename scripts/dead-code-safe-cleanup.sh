#!/bin/bash

# Dead Code Safe Cleanup Script
# Güvenli dead code temizliği - Sadece gerçekten kullanılmayan kodları temizler

set -euo pipefail

# Source helper libraries
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/lib/common.sh"
source "$SCRIPT_DIR/lib/logger.sh"

# Setup logging
setup_logging_trap

# Archive directory
ARCHIVE_DIR="archive/dead-code-$(date +%Y%m%d)"
mkdir -p "$ARCHIVE_DIR"

print_header "Dead Code Safe Cleanup"

# 1. Orphaned Controllers (Güvenli - Route'a bağlı değil)
print_info "1/3: Orphaned Controller'ları temizliyorum..."

ORPHANED_CONTROLLERS=(
    "app/Http/Controllers/AI/AdvancedAIController.php"
    "app/Http/Controllers/Admin/AdminController.php"
    "app/Http/Controllers/Admin/KategoriOzellikApiController.php"
    "app/Http/Controllers/Admin/MusteriController.php"
    "app/Http/Controllers/Admin/PerformanceController.php"
    "app/Http/Controllers/Admin/PriceController.php"
    "app/Http/Controllers/Admin/TalepRaporController.php"
    "app/Http/Controllers/Api/AIFeatureSuggestionController.php"
    "app/Http/Controllers/Api/AdvancedAIController.php"
    "app/Http/Controllers/Api/AkilliCevreAnaliziController.php"
    "app/Http/Controllers/Api/AnythingLLMProxyController.php"
    "app/Http/Controllers/Api/Context7AdvisorController.php"
    "app/Http/Controllers/Api/Context7AuthController.php"
    "app/Http/Controllers/Api/Context7BaseController.php"
    "app/Http/Controllers/Api/Context7Controller.php"
    "app/Http/Controllers/Api/Context7CrmController.php"
    "app/Http/Controllers/Api/Context7DashboardController.php"
    "app/Http/Controllers/Api/Context7EmlakController.php"
    "app/Http/Controllers/Api/Context7OzellikController.php"
    "app/Http/Controllers/Api/Context7ProjeController.php"
    "app/Http/Controllers/Api/Context7TeamController.php"
    "app/Http/Controllers/Api/Context7TelegramAutomationController.php"
    "app/Http/Controllers/Api/Context7TelegramWebhookController.php"
    "app/Http/Controllers/Api/CrmController.php"
    "app/Http/Controllers/Api/CurrencyController.php"
    "app/Http/Controllers/Api/HybridSearchController.php"
    "app/Http/Controllers/Api/ImageAIController.php"
    "app/Http/Controllers/Api/LanguageController.php"
    "app/Http/Controllers/Api/ListingSearchController.php"
    "app/Http/Controllers/Api/LiveSearchController.php"
    "app/Http/Controllers/Api/NearbyPlacesController.php"
    "app/Http/Controllers/Api/PersonController.php"
    "app/Http/Controllers/Api/PropertyFeatureSuggestionController.php"
    "app/Http/Controllers/Api/PropertyValuationController.php"
    "app/Http/Controllers/Api/SmartFieldController.php"
    "app/Http/Controllers/Frontend/HomeController.php"
    "app/Http/Controllers/Frontend/PreferenceController.php"
)

MOVED=0
SKIPPED=0

for controller in "${ORPHANED_CONTROLLERS[@]}"; do
    if [ -f "$controller" ]; then
        # Son kontrol: Route'larda kullanılıyor mu?
        CONTROLLER_NAME=$(basename "$controller" .php)
        if grep -r "$CONTROLLER_NAME" routes/ &>/dev/null; then
            print_warning "Atlandı: $controller (Route'da kullanılıyor)"
            SKIPPED=$((SKIPPED + 1))
            continue
        fi

        # Archive'e taşı
        ARCHIVE_PATH="$ARCHIVE_DIR/controllers/$(dirname "$controller" | sed 's|app/Http/Controllers/||')"
        mkdir -p "$ARCHIVE_PATH"
        mv "$controller" "$ARCHIVE_PATH/"
        print_success "Taşındı: $controller → $ARCHIVE_PATH/"
        MOVED=$((MOVED + 1))
        log_info "Controller archive'e taşındı: $controller"
    else
        print_warning "Bulunamadı: $controller"
    fi
done

echo ""
print_info "Orphaned Controller Temizliği: $MOVED taşındı, $SKIPPED atlandı"

# 2. Kullanılmayan Trait'ler (Güvenli)
print_info "2/3: Kullanılmayan Trait'leri temizliyorum..."

UNUSED_TRAITS=(
    "app/Traits/SearchableTrait.php"
    "app/Traits/HasActiveScope.php"
    "app/Modules/Auth/Traits/HasRoles.php"
)

MOVED_TRAITS=0

for trait in "${UNUSED_TRAITS[@]}"; do
    if [ -f "$trait" ]; then
        # Son kontrol: Kullanılıyor mu?
        TRAIT_NAME=$(basename "$trait" .php)
        if grep -r "use.*$TRAIT_NAME" app/ &>/dev/null; then
            print_warning "Atlandı: $trait (Kullanılıyor)"
            continue
        fi

        # Archive'e taşı
        ARCHIVE_PATH="$ARCHIVE_DIR/traits/"
        mkdir -p "$ARCHIVE_PATH"
        mv "$trait" "$ARCHIVE_PATH/"
        print_success "Taşındı: $trait → $ARCHIVE_PATH/"
        MOVED_TRAITS=$((MOVED_TRAITS + 1))
        log_info "Trait archive'e taşındı: $trait"
    fi
done

echo ""
print_info "Trait Temizliği: $MOVED_TRAITS taşındı"

# 3. Özet
print_footer "Temizlik Özeti"
echo "Toplam Taşınan: $((MOVED + MOVED_TRAITS)) dosya"
echo "Archive Konumu: $ARCHIVE_DIR"

if [ $MOVED -gt 0 ] || [ $MOVED_TRAITS -gt 0 ]; then
    print_success "Dead code temizliği tamamlandı!"
    log_info "Dead code cleanup tamamlandı: $((MOVED + MOVED_TRAITS)) dosya archive'e taşındı"
else
    print_warning "Temizlenecek dosya bulunamadı"
fi

