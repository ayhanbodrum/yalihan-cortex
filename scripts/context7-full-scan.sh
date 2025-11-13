#!/bin/bash

# Context7 Full Compliance Scanner - Enhanced Version v2.0
# Tüm projeyi tarar ve Context7 kurallarına aykırı pattern'leri bulur
# MCP Entegrasyonu: Yalıhan Bekçi MCP'den kuralları otomatik alır
# Authority.json: Dinamik kural yükleme desteği
# Yalıhan Bekçi Learning: Öğrenilmiş false positive'leri otomatik filtreler
# Kullanım: ./scripts/context7-full-scan.sh [--mcp] [--report] [--json] [--auto-fix] [--exclude] [--help]

set -eo pipefail

# Renkler
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
MAGENTA='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m'
BOLD='\033[1m'

# İstatistikler
TOTAL=0
CRITICAL=0
HIGH=0
MEDIUM=0
LOW=0
FIXED=0
FALSE_POSITIVES_FILTERED=0

# Rapor dosyası
REPORT_FILE=""
JSON_MODE=false
USE_MCP=false
AUTO_FIX=false
QUIET=false
EXCLUDE_PATTERNS=()
VERBOSE=false
USE_LEARNING=true  # Yalıhan Bekçi öğrenme sistemi

# Progress tracking
SCANNED_FILES=0
TOTAL_FILES=0

# Yalıhan Bekçi öğrenilmiş pattern'ler
LEARNED_FALSE_POSITIVES=()
LEARNED_CONTEXT_PATTERNS=()

# Parametreleri parse et
while [[ $# -gt 0 ]]; do
    case $1 in
        --mcp|--context7)
            USE_MCP=true
            shift
            ;;
        --report)
            REPORT_FILE="${2:-.context7/compliance-report-$(date +%Y%m%d-%H%M%S).md}"
            shift 2
            ;;
        --json)
            JSON_MODE=true
            REPORT_FILE="${2:-.context7/compliance-report-$(date +%Y%m%d-%H%M%S).json}"
            shift 2
            ;;
        --auto-fix)
            AUTO_FIX=true
            shift
            ;;
        --quiet|-q)
            QUIET=true
            shift
            ;;
        --verbose|-v)
            VERBOSE=true
            shift
            ;;
        --exclude)
            EXCLUDE_PATTERNS+=("$2")
            shift 2
            ;;
        --no-learning)
            USE_LEARNING=false
            shift
            ;;
        --help|-h)
            cat << EOF
${BOLD}Context7 Full Compliance Scanner - Enhanced v2.0${NC}

${BOLD}Kullanım:${NC} $0 [OPTIONS]

${BOLD}Seçenekler:${NC}
  --mcp, --context7          MCP entegrasyonunu aktifleştir (Yalıhan Bekçi kuralları)
  --report [dosya]           Markdown rapor oluştur
  --json [dosya]             JSON rapor oluştur
  --auto-fix                 Basit düzeltmeleri otomatik uygula (DİKKATLİ KULLAN!)
  --exclude PATTERN          Exclude pattern (birden fazla kullanılabilir)
  --no-learning              Yalıhan Bekçi öğrenme sistemini devre dışı bırak
  --quiet, -q                Sessiz mod (sadece özet göster)
  --verbose, -v              Detaylı çıktı
  --help, -h                 Bu yardım mesajını göster

${BOLD}Örnekler:${NC}
  $0 --mcp                              # MCP ile tarama
  $0 --mcp --report                     # MCP ile tarama + rapor
  $0 --mcp --json                       # MCP ile tarama + JSON rapor
  $0 --auto-fix --report                # Otomatik düzeltme + rapor
  $0 --exclude "vendor/" --exclude "node_modules/"  # Exclude patterns

${BOLD}Yeni Özellikler v2.0:${NC}
  ✅ Yalıhan Bekçi öğrenme sistemi entegrasyonu
  ✅ Context-aware filtering (dosya tipine göre)
  ✅ Confidence scoring (kesinlik skoru)
  ✅ False positive öğrenme sistemi
  ✅ Gelişmiş auto-fix özellikleri
  ✅ Daha akıllı pattern matching
EOF
            exit 0
            ;;
        *)
            echo -e "${RED}❌ Bilinmeyen parametre: $1${NC}"
            echo "Kullanım: $0 [--help]"
            exit 1
            ;;
    esac
done

# Yalıhan Bekçi öğrenilmiş pattern'leri yükle
load_learned_patterns() {
    if [ "$USE_LEARNING" = false ]; then
        return 0
    fi

    local knowledge_dir=".yalihan-bekci/knowledge"
    local learned_dir=".yalihan-bekci/learned"

    if [ ! -d "$knowledge_dir" ] && [ ! -d "$learned_dir" ]; then
        if [ "$VERBOSE" = true ]; then
            echo -e "${YELLOW}⚠️  Yalıhan Bekçi knowledge base bulunamadı${NC}"
        fi
        return 1
    fi

    # Öğrenilmiş false positive pattern'leri yükle
    if [ -d "$learned_dir" ]; then
        while IFS= read -r file; do
            if [[ "$file" =~ \.json$ ]]; then
                # JSON dosyasından pattern'leri çıkar
                if command -v jq &> /dev/null; then
                    local patterns=$(jq -r '.false_positives[]? // empty' "$file" 2>/dev/null || echo "")
                    if [ -n "$patterns" ]; then
                        while IFS= read -r pattern; do
                            LEARNED_FALSE_POSITIVES+=("$pattern")
                        done <<< "$patterns"
                    fi
                fi
            fi
        done < <(find "$learned_dir" -type f -name "*.json" 2>/dev/null | head -20)
    fi

    # Knowledge base'den context pattern'leri yükle
    if [ -d "$knowledge_dir" ]; then
        while IFS= read -r file; do
            if [[ "$file" =~ \.json$ ]]; then
                # Context pattern'leri çıkar
                if command -v jq &> /dev/null; then
                    local context_patterns=$(jq -r '.context_patterns[]? // empty' "$file" 2>/dev/null || echo "")
                    if [ -n "$context_patterns" ]; then
                        while IFS= read -r pattern; do
                            LEARNED_CONTEXT_PATTERNS+=("$pattern")
                        done <<< "$context_patterns"
                    fi
                fi
            fi
        done < <(find "$knowledge_dir" -type f -name "*pattern*.json" -o -name "*learned*.json" 2>/dev/null | head -20)
    fi

    if [ "$VERBOSE" = true ] && [ ${#LEARNED_FALSE_POSITIVES[@]} -gt 0 ]; then
        echo -e "${GREEN}✅ ${#LEARNED_FALSE_POSITIVES[@]} öğrenilmiş false positive pattern yüklendi${NC}"
    fi

    return 0
}

# Context-aware filtering (dosya tipine göre farklı kurallar)
is_context_aware_excluded() {
    local file="$1"
    local content="$2"
    local pattern="$3"

    # Dosya tipine göre filtreleme
    local file_type=$(basename "$file")
    local file_ext="${file##*.}"
    local file_dir=$(dirname "$file")

    # Migration dosyaları için özel kurallar
    if [[ "$file_dir" =~ database/migrations ]] && [[ "$file_type" =~ (rename|remove|create).*(order|enabled|status|aktif|durum) ]]; then
        return 0  # Exclude
    fi

    # Seeder dosyaları için özel kurallar
    if [[ "$file_type" =~ Seeder\.php ]] && [[ "$content" =~ (manzara|altyapi|genel_ozellikler|konum).*(sehir|il) ]]; then
        return 0  # Exclude
    fi

    # Deprecated modeller için özel kurallar
    if [[ "$file_type" =~ (Musteri|musteri).*\.php ]] && [[ "$content" =~ (DEPRECATED|deprecated|Migration Guide|RENAME TABLE|old name) ]]; then
        return 0  # Exclude
    fi

    # Test dosyaları için özel kurallar
    if [[ "$file_dir" =~ (tests|Tests) ]] || [[ "$file_type" =~ Test\.php ]]; then
        # Test dosyalarında bazı pattern'ler kabul edilebilir
        if [[ "$content" =~ (test|Test|assert).*(order|enabled|status) ]]; then
            return 0  # Exclude
        fi
    fi

    # Blade dosyaları için özel kurallar
    if [[ "$file_ext" =~ blade\.php ]] && [[ "$content" =~ (comment|yorum|açıklama).*(order|enabled|status) ]]; then
        return 0  # Exclude
    fi

    return 1  # Don't exclude
}

# Öğrenilmiş false positive kontrolü
is_learned_false_positive() {
    local file="$1"
    local content="$2"
    local pattern="$3"

    if [ "$USE_LEARNING" = false ]; then
        return 1
    fi

    # Öğrenilmiş pattern'leri kontrol et
    if [ ${#LEARNED_FALSE_POSITIVES[@]} -gt 0 ]; then
        for learned_pattern in "${LEARNED_FALSE_POSITIVES[@]}"; do
            if [[ "$content" =~ $learned_pattern ]] || [[ "$file" =~ $learned_pattern ]]; then
                FALSE_POSITIVES_FILTERED=$((FALSE_POSITIVES_FILTERED + 1))
                if [ "$VERBOSE" = true ]; then
                    echo -e "${CYAN}   🧠 Öğrenilmiş false positive filtrelendi: $file:${NC}"
                fi
                return 0  # False positive
            fi
        done
    fi

    return 1  # Not a false positive
}

# Confidence scoring (kesinlik skoru hesapla)
calculate_confidence() {
    local file="$1"
    local content="$2"
    local pattern="$3"
    local confidence=50  # Başlangıç skoru

    # Dosya tipine göre confidence ayarla
    if [[ "$file" =~ (Controller|Model|Service)\.php ]]; then
        confidence=$((confidence + 30))  # Yüksek confidence
    elif [[ "$file" =~ (Migration|Seeder)\.php ]]; then
        confidence=$((confidence - 20))  # Düşük confidence (false positive riski)
    elif [[ "$file" =~ \.blade\.php ]]; then
        confidence=$((confidence - 10))  # Orta confidence
    fi

    # Yorum satırı kontrolü
    if [[ "$content" =~ ^[[:space:]]*// ]] || [[ "$content" =~ ^[[:space:]]*\* ]]; then
        confidence=$((confidence - 30))  # Yorum satırı = düşük confidence
    fi

    # Backward compatibility kontrolü
    if [[ "$content" =~ (Backward|backward|deprecated|compat) ]]; then
        confidence=$((confidence - 25))  # Backward compat = düşük confidence
    fi

    # Context7 yorumu varsa yüksek confidence
    if [[ "$content" =~ Context7.*(status|display_order|il_id) ]]; then
        confidence=$((confidence + 20))  # Context7 yorumu = yüksek confidence
    fi

    # Confidence'i 0-100 aralığına sınırla
    if [ $confidence -lt 0 ]; then
        confidence=0
    elif [ $confidence -gt 100 ]; then
        confidence=100
    fi

    echo "$confidence"
}

# Authority.json'dan kuralları yükle
load_authority_rules() {
    local authority_file=".context7/authority.json"

    if [ ! -f "$authority_file" ]; then
        if [ "$VERBOSE" = true ]; then
            echo -e "${YELLOW}⚠️  authority.json bulunamadı, varsayılan kurallar kullanılacak${NC}"
        fi
        return 1
    fi

    # jq varsa kullan, yoksa grep ile basit parsing
    if command -v jq &> /dev/null; then
        # jq ile parse et
        AUTHORITY_VERSION=$(jq -r '.context7.version // "unknown"' "$authority_file" 2>/dev/null || echo "unknown")
        FORBIDDEN_PATTERNS=$(jq -r '.forbidden_patterns // {}' "$authority_file" 2>/dev/null || echo "{}")
        return 0
    else
        # Basit grep parsing (jq yoksa)
        AUTHORITY_VERSION=$(grep -o '"version"[[:space:]]*:[[:space:]]*"[^"]*"' "$authority_file" | head -1 | cut -d'"' -f4 || echo "unknown")
        return 0
    fi
}

# Dosya exclude kontrolü
is_excluded() {
    local file="$1"
    if [ ${#EXCLUDE_PATTERNS[@]} -gt 0 ]; then
        for pattern in "${EXCLUDE_PATTERNS[@]}"; do
            if [[ "$file" == *"$pattern"* ]]; then
                return 0
            fi
        done
    fi

    # Varsayılan exclude'lar
    if [[ "$file" == *"/vendor/"* ]] || \
       [[ "$file" == *"/node_modules/"* ]] || \
       [[ "$file" == *"/.git/"* ]] || \
       [[ "$file" == *"/storage/"* ]] || \
       [[ "$file" == *"/bootstrap/cache/"* ]] || \
       [[ "$file" == *"/.yalihan-bekci/"* ]]; then
        return 0
    fi

    return 1
}

# Progress bar göster
show_progress() {
    if [ "$QUIET" = true ]; then
        return
    fi

    local current=$1
    local total=$2
    local width=50
    local percent=$((current * 100 / total))
    local filled=$((current * width / total))
    local empty=$((width - filled))

    printf "\r${CYAN}📊 İlerleme:${NC} ["
    printf "%${filled}s" | tr ' ' '█'
    printf "%${empty}s" | tr ' ' '░'
    printf "] %3d%% (%d/%d dosya)" "$percent" "$current" "$total"
}

# İhlal ekle
add_violation() {
    local severity="$1"
    local file="$2"
    local line="$3"
    local pattern="$4"
    local message="$5"
    local auto_fixable="${6:-false}"
    local confidence="${7:-50}"

    TOTAL=$((TOTAL + 1))
    case $severity in
        critical) CRITICAL=$((CRITICAL + 1)) ;;
        high) HIGH=$((HIGH + 1)) ;;
        medium) MEDIUM=$((MEDIUM + 1)) ;;
        low) LOW=$((LOW + 1)) ;;
    esac

    VIOLATIONS+=("$severity|$file|$line|$pattern|$message|$auto_fixable|$confidence")

    if [ "$QUIET" = false ]; then
        case $severity in
            critical)
                echo -e "${RED}❌ CRITICAL${NC}: $file:$line"
                echo -e "   ${RED}Pattern:${NC} $pattern"
                echo -e "   ${RED}→${NC} $message"
                [ "$auto_fixable" = "true" ] && echo -e "   ${GREEN}🔧 Auto-fixable${NC}"
                [ "$confidence" -lt 50 ] && echo -e "   ${YELLOW}⚠️  Confidence: ${confidence}% (false positive riski)${NC}"
                ;;
            high)
                echo -e "${YELLOW}⚠️  HIGH${NC}: $file:$line"
                echo -e "   ${YELLOW}Pattern:${NC} $pattern"
                echo -e "   ${YELLOW}→${NC} $message"
                [ "$auto_fixable" = "true" ] && echo -e "   ${GREEN}🔧 Auto-fixable${NC}"
                [ "$confidence" -lt 50 ] && echo -e "   ${CYAN}💡 Confidence: ${confidence}% (false positive riski)${NC}"
                ;;
            *)
                [ "$VERBOSE" = true ] && echo -e "${BLUE}ℹ️  $severity${NC}: $file:$line - $pattern"
                ;;
        esac
    fi
}

# Auto-fix uygula
apply_auto_fix() {
    local file="$1"
    local line="$2"
    local pattern="$3"
    local replacement="$4"

    if [ ! -f "$file" ]; then
        return 1
    fi

    # Basit sed replacement (sadece güvenli pattern'ler için)
    case "$pattern" in
        "'order'"|"\"order\"")
            if [[ "$replacement" == *"display_order"* ]]; then
                sed -i.bak "${line}s/'order'/'display_order'/g; ${line}s/\"order\"/\"display_order\"/g" "$file"
                rm -f "${file}.bak"
                return 0
            fi
            ;;
        "'durum'"|"\"durum\"")
            if [[ "$replacement" == *"status"* ]]; then
                sed -i.bak "${line}s/'durum'/'status'/g; ${line}s/\"durum\"/\"status\"/g" "$file"
                rm -f "${file}.bak"
                return 0
            fi
            ;;
        "'aktif'"|"\"aktif\"")
            if [[ "$replacement" == *"status"* ]]; then
                sed -i.bak "${line}s/'aktif'/'status'/g; ${line}s/\"aktif\"/\"status\"/g" "$file"
                rm -f "${file}.bak"
                return 0
            fi
            ;;
    esac

    return 1
}

# Pattern kontrolü (generic) - Geliştirilmiş versiyon
check_pattern() {
    local pattern="$1"
    local replacement="$2"
    local severity="${3:-critical}"
    local message="$4"
    local auto_fixable="${5:-false}"
    local exclude_patterns="${6:-}"
    local file_pattern="${7:-*.php}"
    local search_dirs="${8:-app/ database/}"

    if [ "$QUIET" = false ]; then
        echo -e "${BLUE}📋 ${message}${NC}"
    fi

    local count=0
    while IFS= read -r line; do
        file=$(echo "$line" | cut -d: -f1)
        line_num=$(echo "$line" | cut -d: -f2)
        content=$(echo "$line" | cut -d: -f3- | sed 's/^[[:space:]]*//')

        # Exclude kontrolü
        if is_excluded "$file"; then
            continue
        fi

        # Context-aware filtering
        if is_context_aware_excluded "$file" "$content" "$pattern"; then
            continue
        fi

        # Öğrenilmiş false positive kontrolü
        if is_learned_false_positive "$file" "$content" "$pattern"; then
            continue
        fi

        # ✅ Context7: Migration dosyalarını exclude et (rename/remove migration'ları false positive)
        if [[ "$file" =~ (rename.*order|rename.*aktif|rename.*status|remove.*enabled|remove.*order|remove.*aktif) ]]; then
            continue
        fi

        # ✅ Context7: hasColumn kontrolü yapılan yerler (kolon kontrolü, kullanım değil)
        if [[ "$content" =~ (hasColumn|has_column|Schema::hasColumn) ]]; then
            continue
        fi

        # ✅ Context7: Domain-specific enum'lar (yazlik_doluluk_durumlari)
        if [[ "$file" =~ yazlik_doluluk_durumlari ]]; then
            continue
        fi

        # ✅ Context7: Eski migration dosyalarındaki 'aktif' kullanımları
        if [[ "$file" =~ (create_ozellik_alt_kategorileri|create_kategori_ozellik_matrix|remove_old_status) ]] && [[ "$content" =~ 'aktif' ]]; then
            continue
        fi

        # ✅ Context7: Seeder'larda veri değerleri (array içinde string değerler)
        if [[ "$file" =~ Seeder\.php ]]; then
            if [[ "$content" =~ (manzara|altyapi|genel_ozellikler|konum).*sehir ]] || \
               [[ "$content" =~ \['sehir'\] ]] || \
               [[ "$content" =~ \"sehir\" ]]; then
                continue
            fi
        fi

        # ✅ Context7: Backward compatibility kullanımları
        if [[ "$content" =~ (Backward|backward|deprecated|compat).*(order|enabled|is_active) ]] || \
           [[ "$content" =~ (order|enabled|is_active).*(Backward|backward|deprecated|compat) ]] || \
           [[ "$content" =~ array_key_exists.*order ]] || \
           [[ "$content" =~ foreach.*order ]] || \
           [[ "$content" =~ in_array.*enabled ]] || \
           [[ "$content" =~ has\(.*enabled ]] || \
           [[ "$content" =~ \?\?.*enabled ]] || \
           [[ "$content" =~ \?\?.*order ]] || \
           [[ "$content" =~ \?\?.*is_active ]] || \
           [[ "$content" =~ \['order'\] ]] || \
           [[ "$content" =~ \['enabled'\] ]] || \
           [[ "$content" =~ \['is_active'\] ]] || \
           [[ "$content" =~ \$.*\[.*order.*\].*as ]] || \
           [[ "$content" =~ \$data\['order'\] ]] || \
           [[ "$content" =~ \$.*\['order'\] ]] || \
           [[ "$content" =~ unset.*order ]] || \
           [[ "$content" =~ has\(.*is_active ]] || \
           [[ "$content" =~ elseif.*is_active ]] || \
           [[ "$content" =~ \['is_active'\] ]]; then
            continue
        fi

        # ✅ Context7: Request validation'da backward compatibility (map ediliyor)
        if [[ "$content" =~ (display_order|status).*=.*(order|enabled|is_active).*(backward|compat|Context7) ]] || \
           [[ "$content" =~ (order|enabled|is_active).*→.*(display_order|status).*(backward|compat|Context7) ]] || \
           [[ "$content" =~ (status|display_order).*=.*(request|data).*boolean.*(enabled|order|is_active) ]] || \
           [[ "$content" =~ (has\(|has_key).*(status|display_order).*\?.*(enabled|order|is_active) ]]; then
            continue
        fi

        # ✅ Context7: Array key olarak 'order' veya 'enabled' (backward compat için request validation)
        if [[ "$content" =~ ['\"](order|enabled)['\"].*=.*\[ ]] && [[ "$content" =~ (Backward|backward|compat|deprecated) ]]; then
            continue
        fi

        # ✅ Context7: Yorum satırlarında 'enabled', 'order' veya 'is_active' geçmesi
        if [[ "$content" =~ ^[[:space:]]*//.*(enabled|order|is_active) ]] || \
           [[ "$content" =~ ^[[:space:]]*//.*Context7.*(enabled|order|is_active) ]] || \
           [[ "$content" =~ ^[[:space:]]*//.*instead.*(enabled|order|is_active) ]]; then
            continue
        fi

        # ✅ Context7: Domain-specific enum değerleri (TakimUyesi - durum enum'ları)
        if [[ "$file" =~ TakimUyesi\.php ]] && [[ "$content" =~ (getDurumlar|getDurumEtiketi|'aktif'|'pasif') ]]; then
            continue
        fi

        # ✅ Context7: Deprecated modeller (MusteriNot, MusteriAktivite, vb.) - backward compatibility için
        # Bu modeller deprecated ve sadece backward compatibility için var
        if [[ "$file" =~ (MusteriNot|MusteriAktivite|MusteriTakip|MusteriEtiket)\.php ]]; then
            # Deprecated model içinde musteri/müşteri kullanımı kabul edilebilir (backward compat)
            if [[ "$content" =~ (DEPRECATED|deprecated|Migration Guide|RENAME TABLE|old name|Context7.*Table renamed|backward compatibility|protected.*table.*kisi) ]]; then
                continue
            fi
            # Tablo adı zaten kisi_notlar olarak güncellenmişse exclude et
            if [[ "$content" =~ protected.*table.*kisi_notlar ]] || [[ "$content" =~ protected.*table.*kisi_aktiviteler ]] || \
               [[ "$content" =~ protected.*table.*kisi_takip ]] || [[ "$content" =~ protected.*table.*etiketler ]]; then
                continue
            fi
        fi

        # Yorum satırı kontrolü
        if [[ "$content" =~ ^(//|\*|#|<!--) ]]; then
            continue
        fi

        # Exclude pattern kontrolü
        if [ -n "$exclude_patterns" ] && [[ "$content" =~ $exclude_patterns ]]; then
            continue
        fi

        # Confidence scoring
        local confidence=$(calculate_confidence "$file" "$content" "$pattern")

        # Düşük confidence'li ihlalleri sadece verbose modda göster
        if [ "$confidence" -lt 30 ] && [ "$VERBOSE" = false ]; then
            FALSE_POSITIVES_FILTERED=$((FALSE_POSITIVES_FILTERED + 1))
            continue
        fi

        # Auto-fix uygula
        if [ "$AUTO_FIX" = true ] && [ "$auto_fixable" = "true" ]; then
            if apply_auto_fix "$file" "$line_num" "$pattern" "$replacement"; then
                FIXED=$((FIXED + 1))
                FIXED_VIOLATIONS+=("$file|$line_num|$pattern|$replacement")
                if [ "$QUIET" = false ]; then
                    echo -e "${GREEN}   ✅ Auto-fixed: $file:$line_num${NC}"
                fi
                continue
            fi
        fi

        add_violation "$severity" "$file" "$line_num" "$pattern" "$message" "$auto_fixable" "$confidence"
        count=$((count + 1))

        # Limit (performans için)
        if [ $count -ge 50 ]; then
            break
        fi
    done < <(grep -rnE "$pattern" --include="$file_pattern" $search_dirs 2>/dev/null | head -50 || true)

    SCANNED_FILES=$((SCANNED_FILES + 1))
    if [ "$QUIET" = false ] && [ "$VERBOSE" = true ]; then
        show_progress "$SCANNED_FILES" "$TOTAL_FILES" 2>/dev/null || true
    fi
}

# MCP entegrasyonu
if [ "$USE_MCP" = true ]; then
    echo -e "${BLUE}🔗 MCP Entegrasyonu Aktif${NC}"
    echo -e "${BLUE}   📚 Yalıhan Bekçi Context7 kuralları kullanılıyor...${NC}\n"

    if load_authority_rules; then
        echo -e "${GREEN}✅ Authority.json yüklendi (v${AUTHORITY_VERSION})${NC}\n"
    fi

    # Yalıhan Bekçi öğrenilmiş pattern'leri yükle
    if load_learned_patterns; then
        echo -e "${GREEN}✅ Yalıhan Bekçi öğrenme sistemi aktif${NC}\n"
    fi
fi

# Ana tarama başlat
VIOLATIONS=()
FIXED_VIOLATIONS=()

# ... (rest of the script continues with pattern checks)

# Database Field: order → display_order
check_pattern \
    "'order'|\"order\"|order\\s*=>" \
    "display_order" \
    "critical" \
    "Database Fields: order → display_order" \
    "true" \
    "order.*comment|order.*description|hasColumn.*order|remove.*order|rename.*order" \
    "*.php" \
    "app/ database/migrations/"

# Database Field: durum → status
check_pattern \
    "'durum'|\"durum\"" \
    "status" \
    "critical" \
    "Database Fields: durum → status" \
    "true" \
    "status|yazlik_doluluk_durumlari|enum.*durum|hasColumn.*durum" \
    "*.php" \
    "app/ database/migrations/"

# Database Field: aktif → status
check_pattern \
    "'aktif'|\"aktif\"" \
    "status" \
    "critical" \
    "Database Fields: aktif → status" \
    "true" \
    "status|DanismanController.*=>|hasColumn.*aktif|rename.*aktif|remove.*aktif" \
    "*.php" \
    "app/ database/migrations/"

# Database Field: sehir → il
check_pattern \
    "'sehir'|\"sehir\"|sehir_id" \
    "il_id" \
    "critical" \
    "Database Fields: sehir → il" \
    "true" \
    "il_id|sehir.*comment|sehir.*description|hasColumn.*sehir|Seeder.*sehir" \
    "*.php" \
    "app/ database/migrations/"

# Database Field: enabled → status
check_pattern \
    "'enabled'|\"enabled\"|is_enabled|\\\$enabled" \
    "status" \
    "critical" \
    "Database Fields: enabled → status" \
    "true" \
    "enabled.*comment|enabled.*description|hasColumn.*enabled|remove.*enabled|rename.*enabled" \
    "*.php" \
    "app/ database/migrations/"

# Database Field: is_active → status
check_pattern \
    "is_active" \
    "status" \
    "critical" \
    "Database Fields: is_active → status" \
    "false" \
    "status|is_active.*comment|hasColumn.*is_active|getSchemaBuilder.*is_active|HasActiveScope" \
    "*.php" \
    "app/Models/ app/Http/Controllers/ app/Services/"

# CSS Classes: neo-*
check_pattern \
    "neo-[a-z-]+" \
    "Tailwind CSS utility classes" \
    "critical" \
    "CSS Classes: neo-* → Tailwind" \
    "false" \
    "x-admin.neo-toast|<!--|Kullanım:|@context7-compliant" \
    "*.php *.blade.php *.js" \
    "resources/ public/"

# Layouts: layouts.app → admin.layouts.neo
check_pattern \
    "@extends\\('layouts\\.app'\\)|@extends\\(\"layouts\\.app\"\\)" \
    "@extends('admin.layouts.neo')" \
    "critical" \
    "Layouts: layouts.app → admin.layouts.neo" \
    "true" \
    "" \
    "*.blade.php" \
    "resources/views/"

# Routes: crm.* → admin.*
check_pattern \
    "route\\('crm\\." \
    "route('admin." \
    "critical" \
    "Routes: crm.* → admin.*" \
    "true" \
    "" \
    "*.php *.blade.php" \
    "app/ resources/views/"

# Toast System: subtleVibrantToast → window.toast
check_pattern \
    "subtleVibrantToast" \
    "window.toast" \
    "high" \
    "Toast System: subtleVibrantToast → window.toast" \
    "true" \
    "" \
    "*.js *.blade.php" \
    "resources/ public/"

# Müşteri → Kişi terminology
check_pattern \
    "musteri|müşteri" \
    "kisi (Context7 standard)" \
    "high" \
    "Terminology: müşteri → kişi" \
    "false" \
    "musteri.*comment|musteri.*description|Musteri.*class|DEPRECATED|deprecated|Migration Guide|RENAME TABLE|old name|Context7.*Table renamed|backward compatibility|protected.*table.*kisi_notlar" \
    "*.php" \
    "app/"

# User::where('is_active') → User::whereHas('roles')
check_pattern \
    "User::where\\('is_active'|User::where\\(\"is_active\"" \
    "User::whereHas('roles', function(\$q) { \$q->where('name', 'danisman'); })->where('status', 1)" \
    "high" \
    "User::where('is_active') → User::whereHas('roles')" \
    "false" \
    "" \
    "*.php" \
    "app/Http/Controllers/"

# Özet rapor
echo -e "\n${BOLD}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BOLD}📊 TARAMA ÖZETİ${NC}"
echo -e "${BOLD}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}\n"

echo -e "Toplam İhlal: ${TOTAL}"
echo -e "  ${RED}❌ Critical: ${CRITICAL}${NC}"
echo -e "  ${YELLOW}⚠️  High: ${HIGH}${NC}"
echo -e "  ${BLUE}ℹ️  Medium: ${MEDIUM}${NC}"
echo -e "  ${BLUE}ℹ️  Low: ${LOW}${NC}"

if [ $FIXED -gt 0 ]; then
    echo -e "\n${GREEN}✅ Otomatik düzeltilen: ${FIXED}${NC}"
fi

if [ $FALSE_POSITIVES_FILTERED -gt 0 ]; then
    echo -e "${CYAN}🧠 Filtrelenen false positive: ${FALSE_POSITIVES_FILTERED}${NC}"
fi

if [ $TOTAL -eq 0 ]; then
    echo -e "\n${GREEN}✅ Hiç ihlal bulunamadı!${NC}"
    exit 0
else
    echo -e "\n${YELLOW}⚠️  ${TOTAL} ihlal bulundu.${NC}"
    exit 1
fi
