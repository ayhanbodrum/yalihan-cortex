#!/bin/bash
# 🧹 Yalıhan Emlak - Code Cleanup & Search Tool
# Version: 1.0.0
# Description: Powerful search, replace, and cleanup tool for codebase

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
MAGENTA='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color
BOLD='\033[1m'

# Project paths
# Eğer environment variable set edilmişse onu kullan, yoksa otomatik tespit et
if [ -z "$YALIHAN_PROJECT_ROOT" ]; then
    PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
else
    PROJECT_ROOT="$YALIHAN_PROJECT_ROOT"
fi

# Manuel override için (script argümanı olarak)
if [ "$1" = "--project-root" ]; then
    PROJECT_ROOT="$2"
    shift 2
fi

RESOURCES_DIR="$PROJECT_ROOT/resources"
PUBLIC_DIR="$PROJECT_ROOT/public"
APP_DIR="$PROJECT_ROOT/app"
ROUTES_DIR="$PROJECT_ROOT/routes"

# Banner
print_banner() {
    echo -e "${CYAN}╔══════════════════════════════════════════════════════════╗${NC}"
    echo -e "${CYAN}║${NC}  ${BOLD}🧹 Yalıhan Temizlik - Kod Temizleme Aracı${NC}         ${CYAN}║${NC}"
    echo -e "${CYAN}║${NC}  ${BLUE}Güçlü Arama, Değiştirme ve Temizlik${NC}                ${CYAN}║${NC}"
    echo -e "${CYAN}╚══════════════════════════════════════════════════════════╝${NC}"
    echo -e "${MAGENTA}📁 Proje Dizini: ${YELLOW}$PROJECT_ROOT${NC}"
    echo ""
}

# Help menu
print_help() {
    echo -e "${BOLD}KULLANIM:${NC}"
    echo "  ./code-cleanup-tool.sh [MODE] [OPTIONS]"
    echo "  ./code-cleanup-tool.sh --project-root /path/to/project [MODE] [OPTIONS]"
    echo ""
    echo -e "${BOLD}PROJE DİZİNİ AYARLAMA (3 yol):${NC}"
    echo -e "  ${YELLOW}1. Otomatik (Önerilen):${NC}"
    echo "     cd /Users/macbookpro/Projects/yalihanemlakwarp"
    echo "     ./scripts/yalihan-temizlik search 'pattern'"
    echo ""
    echo -e "  ${YELLOW}2. Environment Variable:${NC}"
    echo "     export YALIHAN_PROJECT_ROOT=/Users/macbookpro/Projects/yalihanemlakwarp"
    echo "     yalihan-temizlik search 'pattern'"
    echo ""
    echo -e "  ${YELLOW}3. Manuel Override:${NC}"
    echo "     yalihan-temizlik --project-root /path/to/project search 'pattern'"
    echo ""
    echo -e "${BOLD}MODLAR:${NC}"
    echo -e "  ${GREEN}search${NC}           Pattern arama (grep)"
    echo -e "  ${GREEN}replace${NC}          Pattern değiştir (sed)"
    echo -e "  ${GREEN}find-unused${NC}      Kullanılmayan dosyaları bul"
    echo -e "  ${GREEN}find-duplicates${NC}  Duplicate dosyaları bul"
    echo -e "  ${GREEN}cleanup-empty${NC}    Boş dosyaları temizle"
    echo -e "  ${GREEN}analyze${NC}          Kod analizi ve rapor"
    echo ""
    echo -e "${BOLD}ÖRNEKLER:${NC}"
    echo "  # Pattern arama"
    echo "  yalihan-temizlik search 'Alpine'"
    echo ""
    echo "  # Pattern değiştir (dry-run)"
    echo "  yalihan-temizlik replace 'eski-class' 'yeni-class' --dry-run"
    echo ""
    echo "  # Gerçek değişiklik yap"
    echo "  yalihan-temizlik replace 'eski-class' 'yeni-class' --execute"
    echo ""
    echo "  # Boş dosyaları bul ve sil"
    echo "  yalihan-temizlik cleanup-empty --execute"
    echo ""
    echo "  # Kullanılmayan dosyaları bul"
    echo "  yalihan-temizlik find-unused 'resources/views/admin/ilanlar'"
    echo ""
    echo "  # Kod analizi"
    echo "  yalihan-temizlik analyze"
    echo ""
}

# Search function
search_pattern() {
    local pattern="$1"
    local target="${2:-$PROJECT_ROOT}"

    echo -e "${BLUE}🔍 Arama Pattern: ${YELLOW}$pattern${NC}"
    echo -e "${BLUE}📁 Hedef: ${YELLOW}$target${NC}"
    echo ""

    # Count matches
    echo -e "${CYAN}📊 Sonuç Özeti:${NC}"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

    # Blade files
    blade_count=$(grep -r "$pattern" "$target" --include="*.blade.php" 2>/dev/null | wc -l | tr -d ' ')
    echo -e "  Blade Files:      ${GREEN}$blade_count${NC} matches"

    # PHP files
    php_count=$(grep -r "$pattern" "$target" --include="*.php" --exclude="*.blade.php" 2>/dev/null | wc -l | tr -d ' ')
    echo -e "  PHP Files:        ${GREEN}$php_count${NC} matches"

    # JavaScript files
    js_count=$(grep -r "$pattern" "$target" --include="*.js" 2>/dev/null | wc -l | tr -d ' ')
    echo -e "  JavaScript Files: ${GREEN}$js_count${NC} matches"

    # CSS files
    css_count=$(grep -r "$pattern" "$target" --include="*.css" 2>/dev/null | wc -l | tr -d ' ')
    echo -e "  CSS Files:        ${GREEN}$css_count${NC} matches"

    total=$((blade_count + php_count + js_count + css_count))
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo -e "  ${BOLD}TOPLAM:           ${GREEN}$total${NC} ${BOLD}matches${NC}"
    echo ""

    # Show details
    if [ $total -gt 0 ]; then
        echo -e "${CYAN}📋 Detaylı Sonuçlar:${NC}"
        echo ""
        grep -rn "$pattern" "$target" \
            --include="*.blade.php" \
            --include="*.php" \
            --include="*.js" \
            --include="*.css" \
            --color=always 2>/dev/null | head -50

        if [ $total -gt 50 ]; then
            echo ""
            echo -e "${YELLOW}⚠️  İlk 50 sonuç gösterildi. Toplam: $total${NC}"
        fi
    else
        echo -e "${GREEN}✅ Pattern bulunamadı!${NC}"
    fi
}

# Replace function
replace_pattern() {
    local old_pattern="$1"
    local new_pattern="$2"
    local mode="${3:---dry-run}"
    local target="${4:-$PROJECT_ROOT}"

    echo -e "${BLUE}🔄 Değiştirme İşlemi${NC}"
    echo -e "  Eski Pattern: ${RED}$old_pattern${NC}"
    echo -e "  Yeni Pattern: ${GREEN}$new_pattern${NC}"
    echo -e "  Mod: ${YELLOW}$mode${NC}"
    echo ""

    # Find affected files
    affected_files=$(grep -rl "$old_pattern" "$target" \
        --include="*.blade.php" \
        --include="*.php" \
        --include="*.js" \
        --include="*.css" 2>/dev/null || true)

    if [ -z "$affected_files" ]; then
        echo -e "${GREEN}✅ Değiştirilecek dosya bulunamadı!${NC}"
        return
    fi

    file_count=$(echo "$affected_files" | wc -l | tr -d ' ')
    echo -e "${CYAN}📊 Etkilenen Dosyalar: ${GREEN}$file_count${NC}"
    echo ""

    # List affected files
    echo -e "${CYAN}📋 Dosya Listesi:${NC}"
    echo "$affected_files" | head -20

    if [ $file_count -gt 20 ]; then
        echo -e "${YELLOW}   ... ve $(($file_count - 20)) dosya daha${NC}"
    fi
    echo ""

    if [ "$mode" = "--dry-run" ]; then
        echo -e "${YELLOW}⚠️  DRY-RUN MODE: Değişiklik yapılmadı!${NC}"
        echo -e "${YELLOW}   Gerçek değişiklik için: --execute flag'ini kullanın${NC}"
    elif [ "$mode" = "--execute" ]; then
        echo -e "${RED}⚠️  GERÇEK DEĞİŞİKLİK YAPILIYOR!${NC}"
        echo -n "Devam etmek istiyor musunuz? (y/N): "
        read -r confirm

        if [ "$confirm" = "y" ] || [ "$confirm" = "Y" ]; then
            echo ""
            echo -e "${GREEN}🚀 Değişiklikler uygulanıyor...${NC}"

            # Backup
            backup_dir="$PROJECT_ROOT/storage/backups/code-cleanup-$(date +%Y%m%d_%H%M%S)"
            mkdir -p "$backup_dir"

            echo "$affected_files" | while read -r file; do
                # Create backup
                backup_path="$backup_dir${file#$PROJECT_ROOT}"
                mkdir -p "$(dirname "$backup_path")"
                cp "$file" "$backup_path"

                # Replace
                sed -i '' "s|$old_pattern|$new_pattern|g" "$file"
                echo -e "  ${GREEN}✓${NC} $file"
            done

            echo ""
            echo -e "${GREEN}✅ Değişiklikler tamamlandı!${NC}"
            echo -e "${CYAN}📦 Backup: $backup_dir${NC}"
        else
            echo -e "${YELLOW}❌ İşlem iptal edildi!${NC}"
        fi
    fi
}

# Find unused files
find_unused_files() {
    local target="${1:-resources/views/admin}"

    echo -e "${BLUE}🔍 Kullanılmayan Dosyaları Bulma${NC}"
    echo -e "  Hedef: ${YELLOW}$target${NC}"
    echo ""

    unused_count=0
    unused_files=""

    # Find all blade files
    find "$target" -name "*.blade.php" -type f 2>/dev/null | while read -r file; do
        filename=$(basename "$file" .blade.php)

        # Skip index, create, edit, show (CRUD standard files)
        if [[ "$filename" =~ ^(index|create|edit|show)$ ]]; then
            continue
        fi

        # Search for usage
        usage_count=$(grep -r "$filename" "$PROJECT_ROOT" \
            --include="*.blade.php" \
            --include="*.php" \
            --exclude="$filename.blade.php" 2>/dev/null | wc -l | tr -d ' ')

        if [ "$usage_count" -eq 0 ]; then
            echo -e "${RED}❌ Kullanılmıyor:${NC} $file"
            unused_count=$((unused_count + 1))
        fi
    done

    if [ $unused_count -eq 0 ]; then
        echo -e "${GREEN}✅ Kullanılmayan dosya bulunamadı!${NC}"
    else
        echo ""
        echo -e "${CYAN}📊 Toplam: ${RED}$unused_count${NC} ${CYAN}kullanılmayan dosya${NC}"
    fi
}

# Find duplicate files
find_duplicate_files() {
    echo -e "${BLUE}🔍 Duplicate Dosyaları Bulma${NC}"
    echo ""

    # Find files with similar names
    echo -e "${CYAN}📋 Benzer İsimli Dosyalar:${NC}"
    echo ""

    # Search for common duplicate patterns
    patterns=(
        "*-v2.js"
        "*-clean.js"
        "*-fixed.js"
        "*-final.js"
        "*-working.js"
        "*-simple.js"
        "*-old.blade.php"
        "*-backup.blade.php"
    )

    duplicate_count=0
    for pattern in "${patterns[@]}"; do
        files=$(find "$PROJECT_ROOT" -name "$pattern" -type f 2>/dev/null)
        if [ -n "$files" ]; then
            echo -e "${YELLOW}Pattern: $pattern${NC}"
            echo "$files"
            duplicate_count=$((duplicate_count + $(echo "$files" | wc -l | tr -d ' ')))
            echo ""
        fi
    done

    if [ $duplicate_count -eq 0 ]; then
        echo -e "${GREEN}✅ Duplicate dosya bulunamadı!${NC}"
    else
        echo -e "${CYAN}📊 Toplam: ${YELLOW}$duplicate_count${NC} ${CYAN}potansiyel duplicate${NC}"
    fi
}

# Cleanup empty files
cleanup_empty_files() {
    local mode="${1:---dry-run}"

    echo -e "${BLUE}🧹 Boş Dosyaları Temizleme${NC}"
    echo -e "  Mod: ${YELLOW}$mode${NC}"
    echo ""

    # Find empty files
    empty_files=$(find "$PROJECT_ROOT" \( -name "*.js" -o -name "*.blade.php" -o -name "*.css" \) -type f -size 0 2>/dev/null)

    if [ -z "$empty_files" ]; then
        echo -e "${GREEN}✅ Boş dosya bulunamadı!${NC}"
        return
    fi

    empty_count=$(echo "$empty_files" | wc -l | tr -d ' ')

    echo -e "${CYAN}📋 Boş Dosyalar: ${RED}$empty_count${NC}"
    echo "$empty_files"
    echo ""

    if [ "$mode" = "--dry-run" ]; then
        echo -e "${YELLOW}⚠️  DRY-RUN MODE: Dosyalar silinmedi!${NC}"
        echo -e "${YELLOW}   Silmek için: --execute flag'ini kullanın${NC}"
    elif [ "$mode" = "--execute" ]; then
        echo -n "Bu dosyaları silmek istiyor musunuz? (y/N): "
        read -r confirm

        if [ "$confirm" = "y" ] || [ "$confirm" = "Y" ]; then
            echo "$empty_files" | while read -r file; do
                rm -f "$file"
                echo -e "  ${GREEN}✓${NC} Silindi: $file"
            done
            echo ""
            echo -e "${GREEN}✅ $empty_count dosya silindi!${NC}"
        else
            echo -e "${YELLOW}❌ İşlem iptal edildi!${NC}"
        fi
    fi
}

# Code analysis
analyze_code() {
    echo -e "${BLUE}📊 Kod Analizi${NC}"
    echo ""

    echo -e "${CYAN}╔══════════════════════════════════════════════════════════╗${NC}"
    echo -e "${CYAN}║${NC}  ${BOLD}GENEL İSTATİSTİKLER${NC}                                   ${CYAN}║${NC}"
    echo -e "${CYAN}╚══════════════════════════════════════════════════════════╝${NC}"
    echo ""

    # Blade files
    blade_count=$(find "$RESOURCES_DIR" -name "*.blade.php" -type f 2>/dev/null | wc -l | tr -d ' ')
    echo -e "  Blade Dosyaları:     ${GREEN}$blade_count${NC}"

    # PHP files
    php_count=$(find "$APP_DIR" -name "*.php" -type f 2>/dev/null | wc -l | tr -d ' ')
    echo -e "  PHP Dosyaları:       ${GREEN}$php_count${NC}"

    # JavaScript files
    js_count=$(find "$RESOURCES_DIR/js" -name "*.js" -type f 2>/dev/null | wc -l | tr -d ' ')
    js_public_count=$(find "$PUBLIC_DIR/js" -name "*.js" -type f 2>/dev/null | wc -l | tr -d ' ')
    echo -e "  JavaScript (src):    ${GREEN}$js_count${NC}"
    echo -e "  JavaScript (public): ${GREEN}$js_public_count${NC}"

    # CSS files
    css_count=$(find "$RESOURCES_DIR" -name "*.css" -type f 2>/dev/null | wc -l | tr -d ' ')
    echo -e "  CSS Dosyaları:       ${GREEN}$css_count${NC}"

    echo ""
    echo -e "${CYAN}╔══════════════════════════════════════════════════════════╗${NC}"
    echo -e "${CYAN}║${NC}  ${BOLD}DOSYA BOYU ANALİZİ${NC}                                   ${CYAN}║${NC}"
    echo -e "${CYAN}╚══════════════════════════════════════════════════════════╝${NC}"
    echo ""

    # Largest PROJECT files (exclude node_modules, vendor)
    echo -e "${YELLOW}📈 En Büyük 10 Proje Dosyası (node_modules/vendor hariç):${NC}"
    find "$PROJECT_ROOT" \( -name "*.blade.php" -o -name "*.js" -o -name "*.css" -o -name "*.php" \) -type f \
        ! -path "*/node_modules/*" \
        ! -path "*/vendor/*" \
        ! -path "*/storage/*" \
        -exec du -h {} + 2>/dev/null | sort -rh | head -10

    echo ""
    echo -e "${YELLOW}📦 En Büyük 5 Dependencies (node_modules/vendor):${NC}"
    find "$PROJECT_ROOT" \( -name "*.js" -o -name "*.css" \) -type f \
        \( -path "*/node_modules/*" -o -path "*/vendor/*" \) \
        -exec du -h {} + 2>/dev/null | sort -rh | head -5

    echo ""
    echo -e "${YELLOW}📉 Boş Dosyalar:${NC}"
    empty_count=$(find "$PROJECT_ROOT" \( -name "*.js" -o -name "*.blade.php" \) -type f -size 0 2>/dev/null | wc -l | tr -d ' ')
    if [ $empty_count -gt 0 ]; then
        echo -e "  ${RED}⚠️  $empty_count boş dosya bulundu!${NC}"
    else
        echo -e "  ${GREEN}✅ Boş dosya yok!${NC}"
    fi
}

# Main
main() {
    print_banner

    case "$1" in
        search)
            if [ -z "$2" ]; then
                echo -e "${RED}❌ Pattern belirtilmedi!${NC}"
                echo "Kullanım: $0 search 'pattern' [target]"
                exit 1
            fi
            search_pattern "$2" "$3"
            ;;
        replace)
            if [ -z "$2" ] || [ -z "$3" ]; then
                echo -e "${RED}❌ Pattern belirtilmedi!${NC}"
                echo "Kullanım: $0 replace 'old' 'new' [--dry-run|--execute]"
                exit 1
            fi
            replace_pattern "$2" "$3" "$4" "$5"
            ;;
        find-unused)
            find_unused_files "$2"
            ;;
        find-duplicates)
            find_duplicate_files
            ;;
        cleanup-empty)
            cleanup_empty_files "$2"
            ;;
        analyze)
            analyze_code
            ;;
        help|--help|-h)
            print_help
            ;;
        *)
            echo -e "${RED}❌ Bilinmeyen komut: $1${NC}"
            echo ""
            print_help
            exit 1
            ;;
    esac
}

# Run
main "$@"

