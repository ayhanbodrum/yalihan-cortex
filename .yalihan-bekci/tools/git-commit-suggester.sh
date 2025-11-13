#!/bin/bash

# Yalıhan Bekçi - Git Commit Suggester
# Git değişikliklerini analiz edip commit önerisi yapar
# Kullanım: ./yalihan-bekci/tools/git-commit-suggester.sh [--check] [--suggest] [--warn]

set -eo pipefail

# Renkler
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m'
BOLD='\033[1m'

# Modlar
CHECK_MODE=false
SUGGEST_MODE=false
WARN_MODE=false

# Parametreleri parse et
while [[ $# -gt 0 ]]; do
    case $1 in
        --check)
            CHECK_MODE=true
            shift
            ;;
        --suggest)
            SUGGEST_MODE=true
            shift
            ;;
        --warn)
            WARN_MODE=true
            shift
            ;;
        *)
            echo -e "${RED}❌ Bilinmeyen parametre: $1${NC}"
            exit 1
            ;;
    esac
done

# Varsayılan: Tüm modlar aktif
if [ "$CHECK_MODE" = false ] && [ "$SUGGEST_MODE" = false ] && [ "$WARN_MODE" = false ]; then
    CHECK_MODE=true
    SUGGEST_MODE=true
    WARN_MODE=true
fi

# Git durumunu kontrol et
check_git_status() {
    if ! command -v git &> /dev/null; then
        echo -e "${RED}❌ Git bulunamadı!${NC}"
        return 1
    fi

    if ! git rev-parse --git-dir &> /dev/null; then
        echo -e "${RED}❌ Git repository bulunamadı!${NC}"
        return 1
    fi

    return 0
}

# Son commit zamanını kontrol et
check_last_commit_time() {
    local last_commit=$(git log -1 --format=%ct 2>/dev/null || echo "0")
    local current_time=$(date +%s)
    local diff=$((current_time - last_commit))
    local hours=$((diff / 3600))

    if [ $hours -ge 2 ]; then
        echo -e "${YELLOW}⚠️  Uzun süredir commit yapılmadı (${hours} saat)${NC}"
        echo -e "${CYAN}   💡 Mantıksal birimler tamamlandıysa commit yap!${NC}"
        return 1
    fi

    return 0
}

# Değişiklikleri analiz et
analyze_changes() {
    local changed_files=$(git diff --name-only HEAD 2>/dev/null | wc -l | tr -d ' ')
    local staged_files=$(git diff --cached --name-only 2>/dev/null | wc -l | tr -d ' ')
    local total_changes=$((changed_files + staged_files))

    if [ "$total_changes" -eq 0 ]; then
        echo -e "${GREEN}✅ Commit edilmemiş değişiklik yok${NC}"
        return 0
    fi

    echo -e "${BLUE}📊 Değişiklik Analizi:${NC}"
    echo -e "   Değişiklik yapılmış dosya: ${total_changes}"
    echo -e "   Staged: ${staged_files}"
    echo -e "   Unstaged: ${changed_files}"

    # Çok fazla değişiklik uyarısı
    if [ "$total_changes" -ge 20 ]; then
        echo -e "${YELLOW}⚠️  Çok fazla değişiklik var (${total_changes} dosya)${NC}"
        echo -e "${CYAN}   💡 Mantıksal gruplara böl ve ayrı commit'ler yap!${NC}"
    fi

    return 1
}

# Debug kodları kontrol et
check_debug_code() {
    local debug_patterns=("console\\.log" "dd\\(" "var_dump\\(" "print_r\\(" "dump\\(")
    local found_debug=false

    for pattern in "${debug_patterns[@]}"; do
        if git diff HEAD 2>/dev/null | grep -qE "$pattern" || \
           git diff --cached HEAD 2>/dev/null | grep -qE "$pattern"; then
            found_debug=true
            break
        fi
    done

    if [ "$found_debug" = true ]; then
        echo -e "${RED}⚠️  Debug kodları bulundu!${NC}"
        echo -e "${CYAN}   💡 Commit yapmadan önce temizle: console.log, dd(), var_dump()${NC}"
        return 1
    fi

    return 0
}

# Yarım kalmış özellik kontrolü
check_incomplete_features() {
    local incomplete_patterns=("TODO" "FIXME" "HACK" "XXX" "BUG")
    local found_incomplete=false

    for pattern in "${incomplete_patterns[@]}"; do
        if git diff HEAD 2>/dev/null | grep -qiE "$pattern" || \
           git diff --cached HEAD 2>/dev/null | grep -qiE "$pattern"; then
            found_incomplete=true
            break
        fi
    done

    if [ "$found_incomplete" = true ]; then
        echo -e "${YELLOW}⚠️  Yarım kalmış özellik işaretleri bulundu (TODO, FIXME, HACK)${NC}"
        echo -e "${CYAN}   💡 Özelliği tamamla, sonra commit yap!${NC}"
        return 1
    fi

    return 0
}

# Değişiklikleri grupla
group_changes() {
    local groups=()
    local staged_files=$(git diff --cached --name-only HEAD 2>/dev/null)
    local unstaged_files=$(git diff --name-only HEAD 2>/dev/null)
    local all_files="${staged_files}"$'\n'"${unstaged_files}"

    # Context7 düzeltmeleri
    if echo "$all_files" | grep -qE "(context7|Context7)"; then
        groups+=("context7")
    fi

    # Model değişiklikleri
    if echo "$all_files" | grep -qE "app/Models/.*\.php"; then
        groups+=("models")
    fi

    # Controller değişiklikleri
    if echo "$all_files" | grep -qE "app/Http/Controllers/.*\.php"; then
        groups+=("controllers")
    fi

    # Script değişiklikleri
    if echo "$all_files" | grep -qE "scripts/.*\.(sh|php)"; then
        groups+=("scripts")
    fi

    # Dokümantasyon değişiklikleri
    if echo "$all_files" | grep -qE "\.(md|txt)$"; then
        groups+=("docs")
    fi

    # Sonuçları global değişkene kaydet
    GROUPS=("${groups[@]}")
    echo "${groups[@]}"
}

# Commit mesajı öner
suggest_commit_message() {
    local suggestions=()

    # GROUPS dizisini kullan (group_changes tarafından set edildi)
    for group in "${GROUPS[@]}"; do
        case $group in
            context7)
                suggestions+=("fix(context7): improve compliance")
                ;;
            models)
                suggestions+=("fix(models): update field mappings")
                ;;
            controllers)
                suggestions+=("fix(controllers): update logic")
                ;;
            scripts)
                suggestions+=("enhance(scripts): improve functionality")
                ;;
            docs)
                suggestions+=("docs: update documentation")
                ;;
        esac
    done

    if [ ${#suggestions[@]} -eq 0 ]; then
        suggestions+=("chore: code changes")
    fi

    # Sonuçları global değişkene kaydet
    SUGGESTIONS=("${suggestions[@]}")
    echo "${suggestions[@]}"
}

# Ana fonksiyon
main() {
    echo -e "${BOLD}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${BOLD}🧠 Yalıhan Bekçi - Git Commit Suggester${NC}"
    echo -e "${BOLD}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}\n"

    if ! check_git_status; then
        exit 1
    fi

    local has_issues=false

    # Kontroller
    if [ "$CHECK_MODE" = true ]; then
        echo -e "${BLUE}🔍 Kontroller:${NC}\n"

        if ! check_last_commit_time; then
            has_issues=true
        fi

        if analyze_changes; then
            exit 0
        fi

        if ! check_debug_code; then
            has_issues=true
        fi

        if ! check_incomplete_features; then
            has_issues=true
        fi

        echo ""
    fi

    # Uyarılar
    if [ "$WARN_MODE" = true ] && [ "$has_issues" = true ]; then
        echo -e "${YELLOW}⚠️  UYARILAR:${NC}"
        echo -e "${CYAN}   Commit yapmadan önce yukarıdaki sorunları düzelt!${NC}\n"
    fi

    # Commit önerileri
    if [ "$SUGGEST_MODE" = true ]; then
        echo -e "${GREEN}💡 Commit Önerileri:${NC}\n"

        # Grupları al ve önerileri oluştur
        group_changes > /dev/null
        suggest_commit_message > /dev/null
        
        local group_count=${#SUGGESTIONS[@]}

        if [ $group_count -eq 0 ]; then
            echo -e "${CYAN}💡 Genel commit önerisi:${NC}"
            echo -e "   ${BOLD}git commit -m \"chore: code changes\"${NC}\n"
        elif [ $group_count -eq 1 ]; then
            echo -e "${GREEN}✅ Tek mantıksal grup:${NC}"
            echo -e "   ${BOLD}git commit -m \"${SUGGESTIONS[0]}\"${NC}\n"
        else
            echo -e "${CYAN}📦 ${group_count} mantıksal grup bulundu. Ayrı commit'ler önerilir:${NC}\n"
            for i in "${!SUGGESTIONS[@]}"; do
                echo -e "   ${GREEN}$((i+1)).${NC} ${BOLD}git commit -m \"${SUGGESTIONS[i]}\"${NC}"
            done
            echo ""
        fi

        # Dosya gruplama önerisi
        local changed_files=$(git diff --name-only HEAD 2>/dev/null | head -10)
        if [ -n "$changed_files" ]; then
            echo -e "${BLUE}📁 Değişen dosyalar (ilk 10):${NC}"
            echo "$changed_files" | while read -r file; do
                echo -e "   - $file"
            done
            echo ""
        fi
    fi

    # Öğrenme sistemi
    echo -e "${CYAN}🧠 Yalıhan Bekçi Öğrenme:${NC}"
    echo -e "   Bu analiz sonuçları .yalihan-bekci/learned/ klasörüne kaydediliyor...\n"

    # Sonuçları kaydet
    local knowledge_file=".yalihan-bekci/learned/git-commit-analysis-$(date +%Y%m%d-%H%M%S).json"
    mkdir -p "$(dirname "$knowledge_file")"

    # Grupları JSON array'e çevir
    local groups_json="["
    for i in "${!GROUPS[@]}"; do
        if [ $i -gt 0 ]; then
            groups_json+=","
        fi
        groups_json+="\"${GROUPS[i]}\""
    done
    groups_json+="]"

    # Önerileri JSON array'e çevir
    local suggestions_json="["
    for i in "${!SUGGESTIONS[@]}"; do
        if [ $i -gt 0 ]; then
            suggestions_json+=","
        fi
        suggestions_json+="\"${SUGGESTIONS[i]}\""
    done
    suggestions_json+="]"

    cat > "$knowledge_file" << EOF
{
  "timestamp": "$(date -u +%Y-%m-%dT%H:%M:%SZ)",
  "analysis": {
    "changed_files": $(git diff --name-only HEAD 2>/dev/null | wc -l | tr -d ' '),
    "staged_files": $(git diff --cached --name-only HEAD 2>/dev/null | wc -l | tr -d ' '),
    "groups": ${groups_json},
    "suggestions": ${suggestions_json},
    "has_issues": $has_issues
  },
  "learned_patterns": {
    "commit_frequency": "optimal",
    "grouping_strategy": "by_logical_unit"
  }
}
EOF

    echo -e "${GREEN}✅ Analiz kaydedildi: ${knowledge_file}${NC}"
}

# Çalıştır
main

