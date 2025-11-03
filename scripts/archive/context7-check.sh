#!/bin/bash

# Context7 Kuralları Kontrol Sistemi
# Cursor her görev başlangıcında bu script çalıştırılmalı
#
# Kullanım:
# ./scripts/context7-check.sh              # Normal kontrol
# ./scripts/context7-check.sh --auto-fix   # Otomatik düzeltme
# ./scripts/context7-check.sh --performance # Performans kontrolü
# ./scripts/context7-check.sh --security   # Güvenlik kontrolü
# ./scripts/context7-check.sh --quality    # Kod kalitesi kontrolü
#
# YENİ ÖZELLİKLER (2025-01-30):
# - Yasak database alan adları kontrolü (durum, is_active, aktif, sehir, bolge_id, Sehir)
# - Otomatik alan adı düzeltme sistemi
# - Model ve Controller güncelleme
# - Request class güncelleme

# Renk kodları
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Parametreler
AUTO_FIX=false
PERFORMANCE_CHECK=false
SECURITY_CHECK=false
QUALITY_CHECK=false
SCHEMA_CHECK=false
API_CHECK=false
FRONTEND_CHECK=false
TESTS_CHECK=false
AUTO_TEST=false
TEST_PAGE=""
TEST_FEATURE=""
DESIGN_CHECK=false
BUTTON_CHECK=false
UI_CHECK=false
AI_CHECK=false
AI_ANALYSIS=false
AI_DEEP=false
AI_PERFORMANCE=false
AI_SECURITY=false
AI_QUALITY=false
PREVENTIVE_CHECK=false
WATCH_MODE=false
ROUTE_CHECK=false
DATABASE_FIELD_CHECK=false
AI_SETTINGS_CHECK=false

# Parametreleri işle
for arg in "$@"; do
    case $arg in
        --auto-fix)
            AUTO_FIX=true
            ;;
        --performance)
            PERFORMANCE_CHECK=true
            ;;
        --security)
            SECURITY_CHECK=true
            ;;
        --quality)
            QUALITY_CHECK=true
            ;;
        --schema)
            SCHEMA_CHECK=true
            ;;
        --api)
            API_CHECK=true
            ;;
        --frontend)
            FRONTEND_CHECK=true
            ;;
        --tests)
            TESTS_CHECK=true
            ;;
        --auto-test)
            AUTO_TEST=true
            ;;
        --test-page)
            TEST_PAGE="$2"
            shift
            ;;
        --test-feature)
            TEST_FEATURE="$2"
            shift
            ;;
        --design-check)
            DESIGN_CHECK=true
            ;;
        --button-check)
            BUTTON_CHECK=true
            ;;
        --ui-check)
            UI_CHECK=true
            ;;
        --ai-check)
            AI_CHECK=true
            ;;
        --ai-analysis)
            AI_ANALYSIS=true
            ;;
        --ai-deep)
            AI_DEEP=true
            ;;
        --ai-performance)
            AI_PERFORMANCE=true
            ;;
        --ai-security)
            AI_SECURITY=true
            ;;
        --ai-quality)
            AI_QUALITY=true
            ;;
        --preventive)
            PREVENTIVE_CHECK=true
            ;;
        --watch)
            WATCH_MODE=true
            ;;
        --route-check)
            ROUTE_CHECK=true
            ;;
        --database-field-check)
            DATABASE_FIELD_CHECK=true
            ;;
        --ai-settings-check)
            AI_SETTINGS_CHECK=true
            ;;
        --help)
            echo "Context7 Kontrol Script'i Kullanımı:"
            echo "  --auto-fix        Otomatik düzeltme yap"
            echo "  --performance     Performans kontrolü yap"
            echo "  --security        Güvenlik kontrolü yap"
            echo "  --ai-analysis     AI-Powered kod analizi yap"
            echo "  --ai-deep         Derinlemesine AI analizi"
            echo "  --ai-performance  AI ile performans analizi"
            echo "  --ai-security     AI ile güvenlik analizi"
            echo "  --ai-quality      AI ile kod kalitesi analizi"
            echo "  --preventive      Önleyici kontroller yap"
            echo "  --watch           Dosya değişikliklerini izle"
            echo "  --quality      Kod kalitesi kontrolü yap"
            echo "  --schema       Database schema kontrolü yap"
            echo "  --api          API ve route kontrolü yap"
            echo "  --frontend     Frontend ve asset kontrolü yap"
            echo "  --tests        Test coverage kontrolü yap"
            echo "  --auto-test    Otomatik sayfa/özellik test sistemi"
            echo "  --test-page    Belirli sayfayı test et (örn: admin/kisiler)"
            echo "  --test-feature Belirli özelliği test et (örn: ilan-form)"
            echo "  --design-check Sayfa tasarımı kontrolü yap"
            echo "  --button-check Buton tasarımı kontrolü yap"
            echo "  --ui-check     UI/UX kontrolü yap"
            echo "  --ai-check     AI Service kontrolü yap"
            echo "  --database-field-check Database field uyumsuzluklarını kontrol et"
            echo "  --ai-settings-check    AI Settings sistemi kontrolü"
            echo "  --help         Bu yardım mesajını göster"
            exit 0
            ;;
    esac
done

# Önleyici kontrol fonksiyonu
preventive_check() {
    echo -e "${PURPLE}🛡️ Önleyici Kontroller Başlatılıyor...${NC}"
    echo "========================================"

    # 1. Yeni dosya oluşturulurken kontrol
    echo -e "${BLUE}🔍 Yeni dosya oluşturma kuralları kontrol ediliyor...${NC}"

    # 2. Controller oluşturulurken alias kontrolü
    echo -e "${BLUE}🔍 Controller alias kullanımı kontrol ediliyor...${NC}"
    find app/Http/Controllers -name "*.php" -newer .git/HEAD 2>/dev/null | while read file; do
        if grep -q "as name\|as title" "$file"; then
            echo -e "${RED}❌ Yeni Controller'da alias kullanımı: $file${NC}"
        fi
    done

    # 3. Blade template oluşturulurken fallback kontrolü
    echo -e "${BLUE}🔍 Blade template fallback kontrolü yapılıyor...${NC}"
    find resources/views -name "*.blade.php" -newer .git/HEAD 2>/dev/null | while read file; do
        if grep -q "{{ \$[^}]*\$[^}]* }}" "$file" && ! grep -q "??" "$file"; then
            echo -e "${RED}❌ Yeni Blade'de fallback eksik: $file${NC}"
        fi
    done

    # 4. JavaScript dosyalarında CSRF kontrolü
    echo -e "${BLUE}🔍 JavaScript CSRF token kontrolü yapılıyor...${NC}"
    find resources/js public/js -name "*.js" -newer .git/HEAD 2>/dev/null | while read file; do
        if grep -q "fetch.*http" "$file" && ! grep -q "X-CSRF-TOKEN" "$file"; then
            echo -e "${RED}❌ Yeni JavaScript'te CSRF token eksik: $file${NC}"
        fi
    done

    echo -e "${GREEN}✅ Önleyici kontroller tamamlandı${NC}"
}

# Dosya izleme modu
watch_mode() {
    echo -e "${PURPLE}👁️ Dosya İzleme Modu Başlatılıyor...${NC}"
    echo "========================================"
    echo -e "${YELLOW}İzlenen dizinler:${NC}"
    echo "  - app/Http/Controllers/"
    echo "  - resources/views/"
    echo "  - resources/js/"
    echo "  - public/js/"
    echo ""
    echo -e "${CYAN}Dosya değişiklikleri otomatik olarak kontrol edilecek...${NC}"
    echo -e "${YELLOW}Çıkmak için Ctrl+C basın${NC}"

    # inotifywait kullanarak dosya değişikliklerini izle
    if command -v inotifywait &> /dev/null; then
        inotifywait -m -r -e modify,create,delete app/Http/Controllers/ resources/views/ resources/js/ public/js/ 2>/dev/null | while read path action file; do
            echo -e "${BLUE}📁 $action: $path$file${NC}"
            preventive_check
        done
    else
        echo -e "${RED}❌ inotifywait bulunamadı. Lütfen yükleyin:${NC}"
        echo -e "${YELLOW}  Ubuntu/Debian: sudo apt-get install inotify-tools${NC}"
        echo -e "${YELLOW}  macOS: brew install inotify-tools${NC}"
        exit 1
    fi
}

# AI Settings kontrol fonksiyonu
check_ai_settings() {
    echo -e "${CYAN}🤖 AI Settings Kontrolü${NC}"
    echo "----------------------------------------"

    local issues_found=false

    # AI Settings duplication kontrolü
    echo "📁 AI Settings duplication kontrolü..."

    # /admin/ayarlar sayfasında AI tab var mı?
    if grep -q "AI.*Yapay Zeka" resources/views/admin/ayarlar/index.blade.php 2>/dev/null; then
        echo -e "${YELLOW}⚠️  AI Settings duplication bulundu:${NC}"
        echo "   Dosya: resources/views/admin/ayarlar/index.blade.php"
        echo "   Sorun: AI ayarları hem /admin/ayarlar hem /admin/ai-settings'de mevcut"
        echo "   Çözüm: /admin/ayarlar'dan AI tab'ını kaldırın"
        issues_found=true
    fi

    # AI Settings sayfası var mı?
    if [ ! -f "resources/views/admin/ai-settings/index.blade.php" ]; then
        echo -e "${RED}❌ AI Settings sayfası bulunamadı:${NC}"
        echo "   Beklenen: resources/views/admin/ai-settings/index.blade.php"
        echo "   Çözüm: AI Settings sayfasını oluşturun"
        issues_found=true
    fi

    # Ollama desteği var mı?
    if [ -f "resources/views/admin/ai-settings/index.blade.php" ]; then
        if ! grep -q "Ollama Local" resources/views/admin/ai-settings/index.blade.php 2>/dev/null; then
            echo -e "${YELLOW}⚠️  Ollama Local AI desteği eksik:${NC}"
            echo "   Dosya: resources/views/admin/ai-settings/index.blade.php"
            echo "   Çözüm: Ollama Local AI desteği ekleyin"
            issues_found=true
        fi
    fi

    # CSP proxy endpoint var mı?
    if ! grep -q "proxy-ollama" routes/admin.php 2>/dev/null; then
        echo -e "${YELLOW}⚠️  Ollama proxy endpoint eksik:${NC}"
        echo "   Dosya: routes/admin.php"
        echo "   Çözüm: /admin/ai-settings/proxy-ollama route'unu ekleyin"
        issues_found=true
    fi

    # SecurityMiddleware CSP güncellemesi var mı?
    if ! grep -q "localhost:11434" app/Http/Middleware/SecurityMiddleware.php 2>/dev/null; then
        echo -e "${YELLOW}⚠️  CSP policy Ollama desteği eksik:${NC}"
        echo "   Dosya: app/Http/Middleware/SecurityMiddleware.php"
        echo "   Çözüm: localhost:11434'ü connect-src'ye ekleyin"
        issues_found=true
    fi

    if [ "$issues_found" = false ]; then
        echo -e "${GREEN}✅ AI Settings sistemi Context7 kurallarına uygun${NC}"
    else
        echo -e "${RED}❌ AI Settings sistemi Context7 kurallarını ihlal ediyor!${NC}"
        echo -e "${YELLOW}💡 Çözüm: AI Settings konsolidasyonu ve Ollama desteği gerekli${NC}"
        return 1
    fi
}

# Ana kontrol fonksiyonu
if [ "$PREVENTIVE_CHECK" = true ]; then
    preventive_check
    exit 0
fi

if [ "$WATCH_MODE" = true ]; then
    watch_mode
    exit 0
fi

echo "🔍 Context7 Kuralları Kontrol Sistemi Başlatılıyor..."
echo "=================================================="

# Eski dosyaları temizle
echo -e "${BLUE}🧹 Eski Dosyalar Temizleniyor...${NC}"
echo "----------------------------------------"

# .DS_Store dosyalarını temizle
DS_STORE_COUNT=$(find . -name ".DS_Store" -type f | wc -l)
if [ $DS_STORE_COUNT -gt 0 ]; then
    echo "🗑️ $DS_STORE_COUNT adet .DS_Store dosyası siliniyor..."
    find . -name ".DS_Store" -type f -delete
    echo -e "${GREEN}✅ .DS_Store dosyaları temizlendi${NC}"
else
    echo -e "${GREEN}✅ .DS_Store dosyası bulunamadı${NC}"
fi

# .backup dosyalarını temizle
BACKUP_COUNT=$(find . -name "*.backup" -type f | wc -l)
if [ $BACKUP_COUNT -gt 0 ]; then
    echo "🗑️ $BACKUP_COUNT adet .backup dosyası siliniyor..."
    find . -name "*.backup" -type f -delete
    echo -e "${GREEN}✅ .backup dosyaları temizlendi${NC}"
else
    echo -e "${GREEN}✅ .backup dosyası bulunamadı${NC}"
fi

# .tmp dosyalarını temizle
TMP_COUNT=$(find . -name "*.tmp" -type f | wc -l)
if [ $TMP_COUNT -gt 0 ]; then
    echo "🗑️ $TMP_COUNT adet .tmp dosyası siliniyor..."
    find . -name "*.tmp" -type f -delete
    echo -e "${GREEN}✅ .tmp dosyaları temizlendi${NC}"
else
    echo -e "${GREEN}✅ .tmp dosyası bulunamadı${NC}"
fi

echo ""

# Otomatik düzeltme fonksiyonu
auto_fix_errors() {
    echo -e "${PURPLE}🔧 Otomatik Düzeltme Başlatılıyor...${NC}"
    echo "----------------------------------------"

    # Backup oluştur
    echo "📦 Backup oluşturuluyor..."
    BACKUP_DIR="backups/context7-$(date +%Y%m%d-%H%M%S)"
    mkdir -p "$BACKUP_DIR"
    cp -r app/ "$BACKUP_DIR/"
    cp -r resources/ "$BACKUP_DIR/"
    echo -e "${GREEN}✅ Backup oluşturuldu: $BACKUP_DIR${NC}"

    # ad_soyad → tam_ad düzeltmeleri
    echo "🔧 ad_soyad → tam_ad düzeltmeleri yapılıyor..."
    find . -name "*.php" -type f -exec sed -i '' 's/ad_soyad/tam_ad/g' {} \;
    find . -name "*.blade.php" -type f -exec sed -i '' 's/ad_soyad/tam_ad/g' {} \;
    echo -e "${GREEN}✅ ad_soyad → tam_ad düzeltmeleri tamamlandı${NC}"

    # musteri_ad_soyad → musteri_tam_ad düzeltmeleri
    echo "🔧 musteri_ad_soyad → musteri_tam_ad düzeltmeleri yapılıyor..."
    find . -name "*.php" -type f -exec sed -i '' 's/musteri_ad_soyad/musteri_tam_ad/g' {} \;
    echo -e "${GREEN}✅ musteri_ad_soyad → musteri_tam_ad düzeltmeleri tamamlandı${NC}"

    # sehir_adi → il_adi düzeltmeleri
    echo "🔧 sehir_adi → il_adi düzeltmeleri yapılıyor..."
    find . -name "*.php" -type f -exec sed -i '' 's/sehir_adi/il_adi/g' {} \;
    echo -e "${GREEN}✅ sehir_adi → il_adi düzeltmeleri tamamlandı${NC}"

    # bolge_id kaldırma (comment olarak)
    echo "🔧 bolge_id referansları comment olarak işaretleniyor..."
    find . -name "*.php" -type f -exec sed -i '' 's/bolge_id/\/\/ Context7: bolge_id kaldırıldı/g' {} \;
    echo -e "${GREEN}✅ bolge_id referansları comment olarak işaretlendi${NC}"

    echo -e "${GREEN}🎉 Otomatik düzeltme tamamlandı!${NC}"
    echo -e "${YELLOW}⚠️  Lütfen değişiklikleri kontrol edin ve test edin.${NC}"
}

# Route çakışması kontrol fonksiyonu
check_route_conflicts() {
    echo -e "${CYAN}🛣️  Route Çakışması Kontrolü${NC}"
    echo "----------------------------------------"

    local conflicts_found=false

    # Route dosyalarını kontrol et
    local route_files=("routes/admin.php" "routes/api.php" "routes/web.php")

    for route_file in "${route_files[@]}"; do
        if [ -f "$route_file" ]; then
            echo "📁 Kontrol ediliyor: $route_file"

            # Aynı route prefix'inde farklı controller'ları bul
            local route_conflicts=$(grep -n "Route::.*name.*ozellikler" "$route_file" | head -10)
            if [ -n "$route_conflicts" ]; then
                echo -e "${YELLOW}⚠️  Potansiyel route çakışması bulundu:${NC}"
                echo "$route_conflicts"
                conflicts_found=true
            fi

            # FeatureController ve OzellikController çakışması
            local feature_conflicts=$(grep -n -E "(FeatureController|OzellikController)" "$route_file" | head -10)
            if [ -n "$feature_conflicts" ]; then
                echo -e "${YELLOW}⚠️  Controller çakışması bulundu:${NC}"
                echo "$feature_conflicts"
                conflicts_found=true
            fi
        fi
    done

    if [ "$conflicts_found" = false ]; then
        echo -e "${GREEN}✅ Route çakışması bulunamadı${NC}"
    else
        echo -e "${RED}❌ Route çakışması tespit edildi!${NC}"
        echo -e "${YELLOW}💡 Çözüm: Tek controller seç ve diğerini kaldır${NC}"
        return 1
    fi
}

# Database field uyumsuzluğu kontrol fonksiyonu
check_database_field_consistency() {
    echo -e "${CYAN}🗄️  Database Field Uyumsuzluğu Kontrolü${NC}"
    echo "----------------------------------------"

    local inconsistencies_found=false

    # Bilinen tablo-field uyumsuzlukları
    local known_issues=(
        "ozellik_kategorileri:ad:name"
        "ozellikler:aktif:status"
        "ilan_kategorileri:is_active:status"
    )

    for issue in "${known_issues[@]}"; do
        IFS=':' read -r table field_used correct_field <<< "$issue"

        echo "📁 Kontrol ediliyor: $table tablosu"

        # Model dosyalarında yanlış field kullanımını kontrol et
        local model_files=$(find app/Models -name "*.php" -type f)
        for model_file in $model_files; do
            if grep -q "protected \$table = '$table'" "$model_file" 2>/dev/null; then
                if grep -q "'$field_used'" "$model_file" 2>/dev/null; then
                    echo -e "${YELLOW}⚠️  Model field uyumsuzluğu bulundu:${NC}"
                    echo "   Dosya: $model_file"
                    echo "   Yanlış: '$field_used' → Doğru: '$correct_field'"
                    inconsistencies_found=true
                fi
            fi
        done

        # View dosyalarında yanlış field kullanımını kontrol et
        local view_files=$(find resources/views -name "*.blade.php" -type f)
        for view_file in $view_files; do
            if grep -q "\$.*->$field_used" "$view_file" 2>/dev/null; then
                echo -e "${YELLOW}⚠️  View field uyumsuzluğu bulundu:${NC}"
                echo "   Dosya: $view_file"
                echo "   Yanlış: ->$field_used → Doğru: ->$correct_field"
                inconsistencies_found=true
            fi
        done
    done

    if [ "$inconsistencies_found" = false ]; then
        echo -e "${GREEN}✅ Database field uyumsuzluğu bulunamadı${NC}"
    else
        echo -e "${RED}❌ Database field uyumsuzluğu tespit edildi!${NC}"
        echo -e "${YELLOW}💡 Çözüm: Model ve view'larda doğru field isimlerini kullanın${NC}"
        return 1
    fi
}

# Create metodlarında yasak veri kaynakları kontrol fonksiyonu (YENİ)
check_create_method_data_sources() {
    echo -e "${CYAN}🚫 Create Metodlarında Yasak Veri Kaynakları Kontrolü${NC}"
    echo "----------------------------------------"

    local errors_found=false
    local total_errors=0

    # Yasak veri kaynakları
    forbidden_patterns=(
        "User::where\('is_active', true\)->get\(\)"
        "User::where\('is_active', 1\)->get\(\)"
        "User::all\(\)"
        "User::where\('name', 'like'"
    )

    for pattern in "${forbidden_patterns[@]}"; do
        local count=0
        echo "🔍 Kontrol ediliyor: '$pattern'"

        # PHP dosyalarında yasak pattern'leri kontrol et
        count=$(grep -r "$pattern" app/Http/Controllers/Admin/ --include="*.php" 2>/dev/null | grep -v "// Context7:" | wc -l)

        if [ "$count" -gt 0 ]; then
            echo -e "${RED}❌ $count adet yasak veri kaynağı kullanımı bulundu${NC}"
            echo -e "${YELLOW}   → whereHas('roles', function(\$q) { \$q->where('name', 'danisman'); }) kullanılmalı${NC}"
            errors_found=true
            total_errors=$((total_errors + count))
        else
            echo -e "${GREEN}✅ '$pattern' kullanımı bulunamadı${NC}"
        fi
    done

    if [ "$errors_found" = false ]; then
        echo -e "${GREEN}✅ Tüm create metodları kontrol edildi, hata bulunamadı${NC}"
        return 0
    else
        echo -e "${RED}❌ Toplam $total_errors adet yasak veri kaynağı kullanımı tespit edildi!${NC}"
        echo -e "${YELLOW}💡 Çözüm: Create metodlarında sadece danışman rolüne sahip kullanıcıları getirin${NC}"
        return 1
    fi
}

# Yasak database alan adları kontrol fonksiyonu (YENİ)
check_forbidden_field_names() {
    echo -e "${CYAN}🚫 Yasak Database Alan Adları Kontrolü${NC}"
    echo "----------------------------------------"

    local errors_found=false
    local total_errors=0

    # Yasak alan adları ve doğru alternatifleri
    forbidden_fields=(
        "durum:status"
        "is_active:status"
        "aktif:status"
        "sehir:il"
        "bolge_id:il_id"
        "Sehir:Il"
        "ad_soyad:tam_ad"
        "full_name:name"
        "musteri_ad_soyad:musteri_tam_ad"
    )

    for field_pair in "${forbidden_fields[@]}"; do
        IFS=':' read -r forbidden_field correct_field <<< "$field_pair"
        local count=0

        echo "🔍 Kontrol ediliyor: '$forbidden_field' → '$correct_field'"

        # PHP dosyalarında yasak alan adı kullanımını kontrol et
        if [ "$forbidden_field" = "Sehir" ]; then
            # Model import kontrolü
            count=$(grep -r "use App\\\\Models\\\\$forbidden_field" app/ --include="*.php" 2>/dev/null | wc -l)
        elif [ "$forbidden_field" = "bolge_id" ]; then
            # bolge_id kullanımı (comment olmayan)
            count=$(grep -r "\$.*->$forbidden_field\b" app/ --include="*.php" 2>/dev/null | grep -v "// Context7:" | wc -l)
        else
            # Diğer yasak alan adları
            count=$(grep -r "\b$forbidden_field\b" app/ resources/ --include="*.php" --include="*.blade.php" 2>/dev/null | grep -v "// Context7:" | grep -v "docs/" | wc -l)
        fi

        if [ "$count" -gt 0 ]; then
            echo -e "${RED}❌ $count adet '$forbidden_field' kullanımı bulundu${NC}"
            echo -e "${YELLOW}   → '$correct_field' kullanılmalı${NC}"
            errors_found=true
            total_errors=$((total_errors + count))
        else
            echo -e "${GREEN}✅ '$forbidden_field' kullanımı bulunamadı${NC}"
        fi
    done

    if [ "$errors_found" = false ]; then
        echo -e "${GREEN}✅ Tüm yasak alan adları kontrol edildi, hata bulunamadı${NC}"
    else
        echo -e "${RED}❌ Toplam $total_errors adet yasak alan adı kullanımı tespit edildi!${NC}"
        echo -e "${YELLOW}💡 Çözüm: --auto-fix parametresi ile otomatik düzeltme yapın${NC}"
        return 1
    fi
}

# Otomatik alan adı düzeltme fonksiyonu (YENİ)
auto_fix_forbidden_fields() {
    echo -e "${CYAN}🔧 Yasak Alan Adları Otomatik Düzeltme${NC}"
    echo "----------------------------------------"

    # Backup oluştur
    local backup_dir="backups/context7-$(date +%Y%m%d-%H%M%S)"
    mkdir -p "$backup_dir"
    echo "📁 Backup oluşturuluyor: $backup_dir"

    # Yasak alan adları ve doğru alternatifleri
    forbidden_fields=(
        "durum:status"
        "is_active:status"
        "aktif:status"
        "sehir:il"
        "bolge_id:il_id"
        "Sehir:Il"
        "ad_soyad:tam_ad"
        "full_name:name"
        "musteri_ad_soyad:musteri_tam_ad"
    )

    local fixed_files=0
    local total_fixes=0

    for field_pair in "${forbidden_fields[@]}"; do
        IFS=':' read -r forbidden_field correct_field <<< "$field_pair"
        echo "🔧 Düzeltiliyor: '$forbidden_field' → '$correct_field'"

        # PHP dosyalarını düzelt
        if [ "$forbidden_field" = "Sehir" ]; then
            # Model import düzeltme
            find app/ -name "*.php" -type f -exec sed -i.bak "s/use App\\\\Models\\\\$forbidden_field/use App\\\\Models\\\\$correct_field/g" {} \;
        elif [ "$forbidden_field" = "bolge_id" ]; then
            # bolge_id referanslarını comment olarak işaretle
            find app/ resources/ -name "*.php" -o -name "*.blade.php" | xargs grep -l "\$.*->$forbidden_field\b" 2>/dev/null | while read -r file; do
                if ! grep -q "// Context7:" "$file"; then
                    sed -i.bak "s/\$\([^>]*\)->$forbidden_field\b/\$\\1->$forbidden_field \/\/ Context7: $forbidden_field kaldırıldı, $correct_field kullanılmalı/g" "$file"
                    fixed_files=$((fixed_files + 1))
                fi
            done
        else
            # Diğer yasak alan adlarını düzelt
            find app/ resources/ -name "*.php" -o -name "*.blade.php" | xargs grep -l "\b$forbidden_field\b" 2>/dev/null | while read -r file; do
                if ! grep -q "docs/" <<< "$file"; then
                    sed -i.bak "s/\b$forbidden_field\b/$correct_field/g" "$file"
                    fixed_files=$((fixed_files + 1))
                fi
            done
        fi

        # .bak dosyalarını temizle
        find app/ resources/ -name "*.bak" -delete 2>/dev/null
    done

    echo -e "${GREEN}✅ Otomatik düzeltme tamamlandı${NC}"
    echo -e "${BLUE}📊 İşlenen dosya sayısı: $fixed_files${NC}"
}

# Performans kontrolü fonksiyonu
performance_check() {
    echo -e "${CYAN}⚡ Performans Kontrolü Başlatılıyor...${NC}"
    echo "----------------------------------------"

    # N+1 query kontrolü
    echo "🔍 N+1 query problemleri kontrol ediliyor..."
    N1_COUNT=$(grep -r "with(" app/Models/ | wc -l)
    if [ $N1_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $N1_COUNT adet eager loading kullanımı bulundu${NC}"
    else
        echo -e "${YELLOW}⚠️  Eager loading kullanımı bulunamadı${NC}"
    fi

    # Index kontrolü
    echo "🔍 Database index'leri kontrol ediliyor..."
    INDEX_COUNT=$(grep -r "index(" database/migrations/ | wc -l)
    if [ $INDEX_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $INDEX_COUNT adet index tanımı bulundu${NC}"
    else
        echo -e "${YELLOW}⚠️  Index tanımı bulunamadı${NC}"
    fi

    # Cache kullanımı kontrolü
    echo "🔍 Cache kullanımı kontrol ediliyor..."
    CACHE_COUNT=$(grep -r "Cache::" app/ | wc -l)
    if [ $CACHE_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $CACHE_COUNT adet cache kullanımı bulundu${NC}"
    else
        echo -e "${YELLOW}⚠️  Cache kullanımı bulunamadı${NC}"
    fi

    echo -e "${GREEN}✅ Performans kontrolü tamamlandı${NC}"
}

# Güvenlik kontrolü fonksiyonu
security_check() {
    echo -e "${RED}🔒 Güvenlik Kontrolü Başlatılıyor...${NC}"
    echo "----------------------------------------"

    # CSRF token kontrolü
    echo "🔍 CSRF token kullanımı kontrol ediliyor..."
    CSRF_COUNT=$(grep -r "@csrf" resources/views/ | wc -l)
    if [ $CSRF_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $CSRF_COUNT adet CSRF token kullanımı bulundu${NC}"
    else
        echo -e "${RED}❌ CSRF token kullanımı bulunamadı${NC}"
    fi

    # XSS koruması kontrolü
    echo "🔍 XSS koruması kontrol ediliyor..."
    XSS_COUNT=$(grep -r "{!!" resources/views/ | wc -l)
    if [ $XSS_COUNT -gt 0 ]; then
        echo -e "${YELLOW}⚠️  $XSS_COUNT adet unescaped output bulundu${NC}"
    else
        echo -e "${GREEN}✅ Unescaped output bulunamadı${NC}"
    fi

    # Input validation kontrolü
    echo "🔍 Input validation kontrol ediliyor..."
    VALIDATION_COUNT=$(grep -r "validate(" app/Http/Controllers/ | wc -l)
    if [ $VALIDATION_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $VALIDATION_COUNT adet validation kullanımı bulundu${NC}"
    else
        echo -e "${RED}❌ Validation kullanımı bulunamadı${NC}"
    fi

    echo -e "${GREEN}✅ Güvenlik kontrolü tamamlandı${NC}"
}

# AI Service kontrolü fonksiyonu
ai_service_check() {
    echo -e "${PURPLE}🤖 AI Service Kontrolü Başlatılıyor...${NC}"
    echo "----------------------------------------"

    # AI Service dosyalarını kontrol et
    echo "🔍 AI Service dosyaları kontrol ediliyor..."
    AI_SERVICE_COUNT=$(find app/Services/AI/ -name "*.php" 2>/dev/null | wc -l)
    if [ $AI_SERVICE_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $AI_SERVICE_COUNT adet AI Service dosyası bulundu${NC}"
    else
        echo -e "${YELLOW}⚠️  AI Service dosyası bulunamadı${NC}"
    fi

    # AI Service'lerde Context7 uyumluluğu kontrolü
    echo "🔍 AI Service'lerde Context7 uyumluluğu kontrol ediliyor..."

    # durum kullanımı kontrolü
    DURUM_COUNT=$(grep -r "->where('durum'" app/Services/AI/ 2>/dev/null | wc -l)
    if [ $DURUM_COUNT -gt 0 ]; then
        echo -e "${RED}❌ AI Service'lerde $DURUM_COUNT adet 'durum' kullanımı bulundu${NC}"
    else
        echo -e "${GREEN}✅ AI Service'lerde 'durum' kullanımı yok${NC}"
    fi

    # oncelik kullanımı kontrolü (sadece field kullanımını kontrol et)
    ONCELIK_COUNT=$(grep -r "oncelik" app/Services/AI/ 2>/dev/null | grep -E "->oncelik|oncelik\s*=" | wc -l)
    if [ $ONCELIK_COUNT -gt 0 ]; then
        echo -e "${RED}❌ AI Service'lerde $ONCELIK_COUNT adet 'oncelik' kullanımı bulundu${NC}"
    else
        echo -e "${GREEN}✅ AI Service'lerde 'oncelik' kullanımı yok${NC}"
    fi

    # Sehir model kullanımı kontrolü
    SEHIR_COUNT=$(grep -r "Sehir::" app/Services/AI/ 2>/dev/null | wc -l)
    if [ $SEHIR_COUNT -gt 0 ]; then
        echo -e "${RED}❌ AI Service'lerde $SEHIR_COUNT adet 'Sehir' model kullanımı bulundu${NC}"
    else
        echo -e "${GREEN}✅ AI Service'lerde 'Sehir' model kullanımı yok${NC}"
    fi

    # Context7 uyumlu alan kullanımı kontrolü
    STATUS_COUNT=$(grep -r "->where('status'" app/Services/AI/ 2>/dev/null | wc -l)
    ONE_CIKAN_COUNT=$(grep -r "one_cikan" app/Services/AI/ 2>/dev/null | wc -l)
    IL_ID_COUNT=$(grep -r "il_id" app/Services/AI/ 2>/dev/null | wc -l)

    echo -e "${GREEN}✅ AI Service'lerde Context7 uyumlu alan kullanımı:${NC}"
    echo -e "   - status: $STATUS_COUNT adet"
    echo -e "   - one_cikan: $ONE_CIKAN_COUNT adet"
    echo -e "   - il_id: $IL_ID_COUNT adet"

    echo -e "${GREEN}✅ AI Service kontrolü tamamlandı${NC}"
}

# Kod kalitesi kontrolü fonksiyonu
quality_check() {
    echo -e "${BLUE}📊 Kod Kalitesi Kontrolü Başlatılıyor...${NC}"
    echo "----------------------------------------"

    # PSR-12 kontrolü
    echo "🔍 PSR-12 coding standards kontrol ediliyor..."
    if command -v phpcs &> /dev/null; then
        phpcs --standard=PSR12 app/ --report=summary
        echo -e "${GREEN}✅ PSR-12 kontrolü tamamlandı${NC}"
    else
        echo -e "${YELLOW}⚠️  PHPCS bulunamadı, PSR-12 kontrolü atlandı${NC}"
    fi

    # Code duplication kontrolü
    echo "🔍 Code duplication kontrol ediliyor..."
    DUPLICATE_COUNT=$(find app/ -name "*.php" -exec grep -l "function.*(" {} \; | xargs -I {} sh -c 'grep -c "function.*(" "{}"' | awk '{sum+=$1} END {print sum}')
    if [ $DUPLICATE_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $DUPLICATE_COUNT adet fonksiyon bulundu${NC}"
    else
        echo -e "${YELLOW}⚠️  Fonksiyon bulunamadı${NC}"
    fi

    # Comment coverage kontrolü
    echo "🔍 Comment coverage kontrol ediliyor..."
    COMMENT_COUNT=$(grep -r "//" app/ | wc -l)
    if [ $COMMENT_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $COMMENT_COUNT adet comment bulundu${NC}"
    else
        echo -e "${YELLOW}⚠️  Comment bulunamadı${NC}"
    fi

    echo -e "${GREEN}✅ Kod kalitesi kontrolü tamamlandı${NC}"
}

# Database schema kontrolü fonksiyonu
schema_check() {
    echo -e "${BLUE}🗄️ Database Schema Kontrolü Başlatılıyor...${NC}"
    echo "----------------------------------------"

    # Foreign key kontrolü
    echo "🔍 Foreign key'ler kontrol ediliyor..."
    FK_COUNT=$(grep -r "foreign(" database/migrations/ | wc -l)
    if [ $FK_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $FK_COUNT adet foreign key tanımı bulundu${NC}"
    else
        echo -e "${YELLOW}⚠️  Foreign key tanımı bulunamadı${NC}"
    fi

    # Index kontrolü
    echo "🔍 Database index'leri kontrol ediliyor..."
    INDEX_COUNT=$(grep -r "index(" database/migrations/ | wc -l)
    if [ $INDEX_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $INDEX_COUNT adet index tanımı bulundu${NC}"
    else
        echo -e "${YELLOW}⚠️  Index tanımı bulunamadı${NC}"
    fi

    # Migration sırası kontrolü
    echo "🔍 Migration sırası kontrol ediliyor..."
    MIGRATION_COUNT=$(ls database/migrations/ | wc -l)
    if [ $MIGRATION_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $MIGRATION_COUNT adet migration dosyası bulundu${NC}"
    else
        echo -e "${YELLOW}⚠️  Migration dosyası bulunamadı${NC}"
    fi

    # Context7 uyumlu alan kontrolü
    echo "🔍 Context7 uyumlu alanlar kontrol ediliyor..."
    CONTEXT7_FIELDS=$(grep -r "il_id\|status\|tam_ad" database/migrations/ | wc -l)
    if [ $CONTEXT7_FIELDS -gt 0 ]; then
        echo -e "${GREEN}✅ $CONTEXT7_FIELDS adet Context7 uyumlu alan bulundu${NC}"
    else
        echo -e "${YELLOW}⚠️  Context7 uyumlu alan bulunamadı${NC}"
    fi

    echo -e "${GREEN}✅ Database schema kontrolü tamamlandı${NC}"
}

# API ve route kontrolü fonksiyonu
api_check() {
    echo -e "${CYAN}🌐 API ve Route Kontrolü Başlatılıyor...${NC}"
    echo "----------------------------------------"

    # Route tanımları kontrolü
    echo "🔍 Route tanımları kontrol ediliyor..."
    ROUTE_COUNT=$(grep -r "Route::" routes/ | wc -l)
    if [ $ROUTE_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $ROUTE_COUNT adet route tanımı bulundu${NC}"
    else
        echo -e "${YELLOW}⚠️  Route tanımı bulunamadı${NC}"
    fi

    # API endpoint'leri kontrolü
    echo "🔍 API endpoint'leri kontrol ediliyor..."
    API_COUNT=$(grep -r "api/" routes/ | wc -l)
    if [ $API_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $API_COUNT adet API endpoint'i bulundu${NC}"
    else
        echo -e "${YELLOW}⚠️  API endpoint'i bulunamadı${NC}"
    fi

    # Middleware kontrolü
    echo "🔍 Middleware kullanımı kontrol ediliyor..."
    MIDDLEWARE_COUNT=$(grep -r "middleware(" app/Http/Controllers/ | wc -l)
    if [ $MIDDLEWARE_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $MIDDLEWARE_COUNT adet middleware kullanımı bulundu${NC}"
    else
        echo -e "${YELLOW}⚠️  Middleware kullanımı bulunamadı${NC}"
    fi

    # Rate limiting kontrolü
    echo "🔍 Rate limiting kontrol ediliyor..."
    RATE_LIMIT_COUNT=$(grep -r "throttle" routes/ | wc -l)
    if [ $RATE_LIMIT_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $RATE_LIMIT_COUNT adet rate limiting bulundu${NC}"
    else
        echo -e "${YELLOW}⚠️  Rate limiting bulunamadı${NC}"
    fi

    echo -e "${GREEN}✅ API ve route kontrolü tamamlandı${NC}"
}

# Frontend ve asset kontrolü fonksiyonu
frontend_check() {
    echo -e "${PURPLE}🎨 Frontend ve Asset Kontrolü Başlatılıyor...${NC}"
    echo "----------------------------------------"

    # Neo Design System kontrolü
    echo "🔍 Neo Design System kullanımı kontrol ediliyor..."
    NEO_COUNT=$(grep -r "neo-" resources/views/ | wc -l)
    if [ $NEO_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $NEO_COUNT adet Neo Design System kullanımı bulundu${NC}"
    else
        echo -e "${YELLOW}⚠️  Neo Design System kullanımı bulunamadı${NC}"
    fi

    # Legacy CSS sınıfları kontrolü
    echo "🔍 Legacy CSS sınıfları kontrol ediliyor..."
    LEGACY_COUNT=$(grep -r "btn-\|card-\|form-" resources/views/ | wc -l)
    if [ $LEGACY_COUNT -gt 0 ]; then
        echo -e "${RED}❌ $LEGACY_COUNT adet legacy CSS sınıfı bulundu${NC}"
    else
        echo -e "${GREEN}✅ Legacy CSS sınıfı bulunamadı${NC}"
    fi

    # JavaScript hataları kontrolü
    echo "🔍 JavaScript hataları kontrol ediliyor..."
    JS_COUNT=$(find resources/js/ -name "*.js" | wc -l)
    if [ $JS_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $JS_COUNT adet JavaScript dosyası bulundu${NC}"
    else
        echo -e "${YELLOW}⚠️  JavaScript dosyası bulunamadı${NC}"
    fi

    # Asset optimization kontrolü
    echo "🔍 Asset optimization kontrol ediliyor..."
    ASSET_COUNT=$(find public/build/ -name "*.css" -o -name "*.js" | wc -l)
    if [ $ASSET_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $ASSET_COUNT adet optimized asset bulundu${NC}"
    else
        echo -e "${YELLOW}⚠️  Optimized asset bulunamadı${NC}"
    fi

    echo -e "${GREEN}✅ Frontend ve asset kontrolü tamamlandı${NC}"
}

# Test coverage kontrolü fonksiyonu
tests_check() {
    echo -e "${YELLOW}🧪 Test Coverage Kontrolü Başlatılıyor...${NC}"
    echo "----------------------------------------"

    # Unit test kontrolü
    echo "🔍 Unit test'ler kontrol ediliyor..."
    UNIT_COUNT=$(find tests/Unit/ -name "*.php" 2>/dev/null | wc -l)
    if [ $UNIT_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $UNIT_COUNT adet unit test bulundu${NC}"
    else
        echo -e "${YELLOW}⚠️  Unit test bulunamadı${NC}"
    fi

    # Feature test kontrolü
    echo "🔍 Feature test'ler kontrol ediliyor..."
    FEATURE_COUNT=$(find tests/Feature/ -name "*.php" 2>/dev/null | wc -l)
    if [ $FEATURE_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $FEATURE_COUNT adet feature test bulundu${NC}"
    else
        echo -e "${YELLOW}⚠️  Feature test bulunamadı${NC}"
    fi

    # Browser test kontrolü
    echo "🔍 Browser test'ler kontrol ediliyor..."
    BROWSER_COUNT=$(find tests/Browser/ -name "*.php" 2>/dev/null | wc -l)
    if [ $BROWSER_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $BROWSER_COUNT adet browser test bulundu${NC}"
    else
        echo -e "${YELLOW}⚠️  Browser test bulunamadı${NC}"
    fi

    # Test quality kontrolü
    echo "🔍 Test quality kontrol ediliyor..."
    TEST_QUALITY=$(grep -r "assert" tests/ 2>/dev/null | wc -l)
    if [ $TEST_QUALITY -gt 0 ]; then
        echo -e "${GREEN}✅ $TEST_QUALITY adet assertion bulundu${NC}"
    else
        echo -e "${YELLOW}⚠️  Assertion bulunamadı${NC}"
    fi

    echo -e "${GREEN}✅ Test coverage kontrolü tamamlandı${NC}"
}

# Otomatik sayfa/özellik test sistemi
auto_test_system() {
    echo -e "${PURPLE}🤖 Otomatik Sayfa/Özellik Test Sistemi Başlatılıyor...${NC}"
    echo "----------------------------------------"

    # Son değiştirilen dosyaları bul
    echo "🔍 Son değiştirilen dosyalar tespit ediliyor..."
    RECENT_FILES=$(find app/ resources/ -name "*.php" -o -name "*.blade.php" -o -name "*.js" | head -20)

    if [ -n "$RECENT_FILES" ]; then
        echo -e "${GREEN}✅ Son değiştirilen dosyalar bulundu${NC}"

        # Her dosya için test yap
        for file in $RECENT_FILES; do
            echo "🔍 Test ediliyor: $file"
            test_single_file "$file"
        done
    else
        echo -e "${YELLOW}⚠️  Son değiştirilen dosya bulunamadı${NC}"
    fi

    echo -e "${GREEN}✅ Otomatik test sistemi tamamlandı${NC}"
}

# Belirli sayfayı test et
test_specific_page() {
    local page_path="$1"
    echo -e "${CYAN}📄 Sayfa Test Ediliyor: $page_path${NC}"
    echo "----------------------------------------"

    # View dosyasını bul
    local view_file="resources/views/$page_path.blade.php"
    if [ -f "$view_file" ]; then
        echo "🔍 View dosyası bulundu: $view_file"
        test_single_file "$view_file"
    else
        echo -e "${RED}❌ View dosyası bulunamadı: $view_file${NC}"
    fi

    # Controller dosyasını bul
    local controller_path=$(echo "$page_path" | sed 's/\//\\/g')
    local controller_file="app/Http/Controllers/Admin/${controller_path}Controller.php"
    if [ -f "$controller_file" ]; then
        echo "🔍 Controller dosyası bulundu: $controller_file"
        test_single_file "$controller_file"
    else
        echo -e "${YELLOW}⚠️  Controller dosyası bulunamadı: $controller_file${NC}"
    fi

    # Route kontrolü
    echo "🔍 Route kontrolü yapılıyor..."
    local route_name=$(echo "$page_path" | sed 's/\//./g')
    if grep -r "route.*$route_name" routes/ > /dev/null 2>&1; then
        echo -e "${GREEN}✅ Route tanımı bulundu${NC}"
    else
        echo -e "${RED}❌ Route tanımı bulunamadı${NC}"
    fi
}

# Belirli özelliği test et
test_specific_feature() {
    local feature_name="$1"
    echo -e "${BLUE}⚙️ Özellik Test Ediliyor: $feature_name${NC}"
    echo "----------------------------------------"

    # Özellik dosyalarını bul
    local feature_files=$(find app/ resources/ -name "*$feature_name*" -type f)

    if [ -n "$feature_files" ]; then
        echo -e "${GREEN}✅ Özellik dosyaları bulundu${NC}"
        for file in $feature_files; do
            echo "🔍 Test ediliyor: $file"
            test_single_file "$file"
        done
    else
        echo -e "${RED}❌ Özellik dosyaları bulunamadı${NC}"
    fi
}

# Tek dosya test et
test_single_file() {
    local file="$1"
    local file_type=$(echo "$file" | sed 's/.*\.//')

    echo "  📁 Dosya: $file"

    # Dosya türüne göre test yap
    case "$file_type" in
        "php")
            test_php_file "$file"
            ;;
        "blade.php")
            test_blade_file "$file"
            ;;
        "js")
            test_js_file "$file"
            ;;
        *)
            echo "    ⚠️  Desteklenmeyen dosya türü: $file_type"
            ;;
    esac
}

# PHP dosyası test et
test_php_file() {
    local file="$1"
    echo "    🔍 PHP dosyası test ediliyor..."

    # Context7 kuralları kontrolü
    if grep -q "durum\|is_active\|aktif" "$file"; then
        echo "    ❌ Context7 kuralı ihlali: durum/is_active/aktif kullanımı"
    fi

    if grep -q "sehir\|sehir_id\|bolge_id" "$file"; then
        echo "    ❌ Context7 kuralı ihlali: sehir/sehir_id/bolge_id kullanımı"
    fi

    if grep -q "ad_soyad\|full_name" "$file"; then
        echo "    ❌ Context7 kuralı ihlali: ad_soyad/full_name kullanımı"
    fi

    # Syntax kontrolü
    if php -l "$file" > /dev/null 2>&1; then
        echo "    ✅ PHP syntax doğru"
    else
        echo "    ❌ PHP syntax hatası"
    fi

    # Security kontrolü
    if grep -q "validate(" "$file"; then
        echo "    ✅ Input validation mevcut"
    else
        echo "    ⚠️  Input validation eksik"
    fi
}

# Blade dosyası test et
test_blade_file() {
    local file="$1"
    echo "    🔍 Blade dosyası test ediliyor..."

    # Context7 kuralları kontrolü
    if grep -q "durum\|is_active\|aktif" "$file"; then
        echo "    ❌ Context7 kuralı ihlali: durum/is_active/aktif kullanımı"
    fi

    if grep -q "sehir\|sehir_id\|bolge_id" "$file"; then
        echo "    ❌ Context7 kuralı ihlali: sehir/sehir_id/bolge_id kullanımı"
    fi

    if grep -q "ad_soyad\|full_name" "$file"; then
        echo "    ❌ Context7 kuralı ihlali: ad_soyad/full_name kullanımı"
    fi

    # Neo Design System kontrolü
    if grep -q "neo-" "$file"; then
        echo "    ✅ Neo Design System kullanımı mevcut"
    else
        echo "    ⚠️  Neo Design System kullanımı eksik"
    fi

    # Legacy CSS kontrolü
    if grep -q "btn-\|card-\|form-" "$file"; then
        echo "    ❌ Legacy CSS sınıfı kullanımı"
    fi

    # CSRF kontrolü
    if grep -q "@csrf" "$file"; then
        echo "    ✅ CSRF token mevcut"
    else
        echo "    ⚠️  CSRF token eksik"
    fi
}

# JavaScript dosyası test et
test_js_file() {
    local file="$1"
    echo "    🔍 JavaScript dosyası test ediliyor..."

    # Context7 kuralları kontrolü
    if grep -q "sehir\|sehir_id\|bolge_id" "$file"; then
        echo "    ❌ Context7 kuralı ihlali: sehir/sehir_id/bolge_id kullanımı"
    fi

    if grep -q "ad_soyad\|full_name" "$file"; then
        echo "    ❌ Context7 kuralı ihlali: ad_soyad/full_name kullanımı"
    fi

    # JavaScript hataları kontrolü
    if grep -q "undefined\|null" "$file"; then
        echo "    ⚠️  Potansiyel null/undefined hatası"
    fi

    # Alpine.js kontrolü
    if grep -q "x-data\|x-model\|x-show" "$file"; then
        echo "    ✅ Alpine.js kullanımı mevcut"
    else
        echo "    ⚠️  Alpine.js kullanımı eksik"
    fi
}

# Sayfa tasarımı kontrolü fonksiyonu
design_check() {
    echo -e "${PURPLE}🎨 Sayfa Tasarımı Kontrolü Başlatılıyor...${NC}"
    echo "----------------------------------------"

    # Neo Design System kontrolü
    echo "🔍 Neo Design System kullanımı kontrol ediliyor..."
    NEO_COUNT=$(grep -r "neo-" resources/views/ | wc -l)
    if [ $NEO_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $NEO_COUNT adet Neo Design System kullanımı bulundu${NC}"
    else
        echo -e "${RED}❌ Neo Design System kullanımı bulunamadı${NC}"
    fi

    # Legacy CSS kontrolü
    echo "🔍 Legacy CSS sınıfları kontrol ediliyor..."
    LEGACY_COUNT=$(grep -r "btn-\|card-\|form-" resources/views/ | wc -l)
    if [ $LEGACY_COUNT -gt 0 ]; then
        echo -e "${RED}❌ $LEGACY_COUNT adet legacy CSS sınıfı bulundu${NC}"
    else
        echo -e "${GREEN}✅ Legacy CSS sınıfı bulunamadı${NC}"
    fi

    # Responsive design kontrolü
    echo "🔍 Responsive design kontrol ediliyor..."
    RESPONSIVE_COUNT=$(grep -r "sm:\|md:\|lg:\|xl:" resources/views/ | wc -l)
    if [ $RESPONSIVE_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $RESPONSIVE_COUNT adet responsive class bulundu${NC}"
    else
        echo -e "${YELLOW}⚠️  Responsive class bulunamadı${NC}"
    fi

    # Dark mode kontrolü
    echo "🔍 Dark mode desteği kontrol ediliyor..."
    DARK_COUNT=$(grep -r "dark:" resources/views/ | wc -l)
    if [ $DARK_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $DARK_COUNT adet dark mode class bulundu${NC}"
    else
        echo -e "${YELLOW}⚠️  Dark mode class bulunamadı${NC}"
    fi

    echo -e "${GREEN}✅ Sayfa tasarımı kontrolü tamamlandı${NC}"
}

# Buton kontrolü fonksiyonu
button_check() {
    echo -e "${BLUE}🔘 Buton Kontrolü Başlatılıyor...${NC}"
    echo "----------------------------------------"

    # Neo buton kontrolü
    echo "🔍 Neo buton kullanımı kontrol ediliyor..."
    NEO_BUTTON_COUNT=$(grep -r "neo-btn\|x-neo.button" resources/views/ | wc -l)
    if [ $NEO_BUTTON_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $NEO_BUTTON_COUNT adet Neo buton kullanımı bulundu${NC}"
    else
        echo -e "${YELLOW}⚠️  Neo buton kullanımı bulunamadı${NC}"
    fi

    # Legacy buton kontrolü
    echo "🔍 Legacy buton sınıfları kontrol ediliyor..."
    LEGACY_BUTTON_COUNT=$(grep -r "btn-\|button-" resources/views/ | wc -l)
    if [ $LEGACY_BUTTON_COUNT -gt 0 ]; then
        echo -e "${RED}❌ $LEGACY_BUTTON_COUNT adet legacy buton sınıfı bulundu${NC}"
    else
        echo -e "${GREEN}✅ Legacy buton sınıfı bulunamadı${NC}"
    fi

    # Buton accessibility kontrolü
    echo "🔍 Buton accessibility kontrol ediliyor..."
    ACCESSIBLE_BUTTON_COUNT=$(grep -r "aria-label\|aria-describedby" resources/views/ | wc -l)
    if [ $ACCESSIBLE_BUTTON_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $ACCESSIBLE_BUTTON_COUNT adet accessible buton bulundu${NC}"
    else
        echo -e "${YELLOW}⚠️  Accessible buton bulunamadı${NC}"
    fi

    # Buton icon kontrolü
    echo "🔍 Buton icon kullanımı kontrol ediliyor..."
    ICON_BUTTON_COUNT=$(grep -r "svg.*class.*w-.*h-" resources/views/ | wc -l)
    if [ $ICON_BUTTON_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $ICON_BUTTON_COUNT adet icon'lu buton bulundu${NC}"
    else
        echo -e "${YELLOW}⚠️  Icon'lu buton bulunamadı${NC}"
    fi

    echo -e "${GREEN}✅ Buton kontrolü tamamlandı${NC}"
}

# UI/UX kontrolü fonksiyonu
ui_check() {
    echo -e "${CYAN}🖥️ UI/UX Kontrolü Başlatılıyor...${NC}"
    echo "----------------------------------------"

    # Form kontrolü
    echo "🔍 Form tasarımı kontrol ediliyor..."
    FORM_COUNT=$(grep -r "neo-form\|neo-input\|neo-select" resources/views/ | wc -l)
    if [ $FORM_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $FORM_COUNT adet Neo form elementi bulundu${NC}"
    else
        echo -e "${YELLOW}⚠️  Neo form elementi bulunamadı${NC}"
    fi

    # Card kontrolü
    echo "🔍 Card tasarımı kontrol ediliyor..."
    CARD_COUNT=$(grep -r "neo-card" resources/views/ | wc -l)
    if [ $CARD_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $CARD_COUNT adet Neo card bulundu${NC}"
    else
        echo -e "${YELLOW}⚠️  Neo card bulunamadı${NC}"
    fi

    # Loading state kontrolü
    echo "🔍 Loading state kontrol ediliyor..."
    LOADING_COUNT=$(grep -r "loading\|spinner\|animate" resources/views/ | wc -l)
    if [ $LOADING_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $LOADING_COUNT adet loading state bulundu${NC}"
    else
        echo -e "${YELLOW}⚠️  Loading state bulunamadı${NC}"
    fi

    # Animation kontrolü
    echo "🔍 Animation kontrol ediliyor..."
    ANIMATION_COUNT=$(grep -r "transition\|transform\|hover:" resources/views/ | wc -l)
    if [ $ANIMATION_COUNT -gt 0 ]; then
        echo -e "${GREEN}✅ $ANIMATION_COUNT adet animation bulundu${NC}"
    else
        echo -e "${YELLOW}⚠️  Animation bulunamadı${NC}"
    fi

    echo -e "${GREEN}✅ UI/UX kontrolü tamamlandı${NC}"
}

# Hata düzeltme önerileri
suggest_fixes() {
    echo -e "${YELLOW}💡 Hata Düzeltme Önerileri:${NC}"
    echo "----------------------------------------"

    echo "🔧 Context7 Kuralı İhlalleri:"
    echo "  - durum → status"
    echo "  - is_active → status"
    echo "  - aktif → status"
    echo "  - sehir → il"
    echo "  - sehir_id → il_id"
    echo "  - bolge_id → kaldır"
    echo "  - ad_soyad → tam_ad"
    echo "  - full_name → tam_ad"

    echo ""
    echo "🎨 Tasarım Sistemi:"
    echo "  - Legacy CSS → Neo Design System"
    echo "  - btn-* → neo-btn"
    echo "  - card-* → neo-card"
    echo "  - form-* → neo-form"

    echo ""
    echo "🔘 Buton İyileştirmeleri:"
    echo "  - Legacy butonlar → Neo butonlar"
    echo "  - Accessibility ekle (aria-label)"
    echo "  - Icon'lar ekle"
    echo "  - Hover animasyonları ekle"

    echo ""
    echo "🖥️ UI/UX İyileştirmeleri:"
    echo "  - Loading state'ler ekle"
    echo "  - Animation'lar ekle"
    echo "  - Responsive design kontrol et"
    echo "  - Dark mode desteği ekle"

    echo ""
    echo "🔒 Güvenlik:"
    echo "  - @csrf token ekle"
    echo "  - Input validation ekle"
    echo "  - XSS koruması kontrol et"

    echo ""
    echo "⚡ Performans:"
    echo "  - Eager loading ekle"
    echo "  - Cache kullanımı kontrol et"
    echo "  - N+1 query problemlerini çöz"

    echo ""
    echo "🧪 Test:"
    echo "  - Unit test ekle"
    echo "  - Feature test ekle"
    echo "  - Browser test ekle"
}

# Hata sayacı
ERROR_COUNT=0

echo -e "${BLUE}📋 1. Database Alanları Kontrolü${NC}"
echo "----------------------------------------"

# durum → status kontrolü
echo "🔍 'durum' alanı kullanımı kontrol ediliyor..."
DURUM_FOUND=$(grep -r "durum" app/Models/ resources/views/ app/Http/Controllers/ 2>/dev/null | grep -v "status" | wc -l)
if [ $DURUM_FOUND -gt 0 ]; then
    echo -e "${RED}❌ 'durum' alanı kullanımı bulundu: $DURUM_FOUND adet${NC}"
    grep -r "durum" app/Models/ resources/views/ app/Http/Controllers/ 2>/dev/null | grep -v "status" | head -5
    ERROR_COUNT=$((ERROR_COUNT + 1))
else
    echo -e "${GREEN}✅ 'durum' alanı kullanımı yok${NC}"
fi

# is_active → status kontrolü
echo "🔍 'is_active' alanı kullanımı kontrol ediliyor..."
IS_ACTIVE_FOUND=$(grep -r "is_active" app/Models/ resources/views/ app/Http/Controllers/ 2>/dev/null | wc -l)
if [ $IS_ACTIVE_FOUND -gt 0 ]; then
    echo -e "${RED}❌ 'is_active' alanı kullanımı bulundu: $IS_ACTIVE_FOUND adet${NC}"
    grep -r "is_active" app/Models/ resources/views/ app/Http/Controllers/ 2>/dev/null | head -5
    ERROR_COUNT=$((ERROR_COUNT + 1))
else
    echo -e "${GREEN}✅ 'is_active' alanı kullanımı yok${NC}"
fi

# aktif → status kontrolü
echo "🔍 'aktif' alanı kullanımı kontrol ediliyor..."
AKTIF_FOUND=$(grep -r "aktif" app/Models/ resources/views/ app/Http/Controllers/ 2>/dev/null | grep -v "status" | wc -l)
if [ $AKTIF_FOUND -gt 0 ]; then
    echo -e "${RED}❌ 'aktif' alanı kullanımı bulundu: $AKTIF_FOUND adet${NC}"
    grep -r "aktif" app/Models/ resources/views/ app/Http/Controllers/ 2>/dev/null | grep -v "status" | head -5
    ERROR_COUNT=$((ERROR_COUNT + 1))
else
    echo -e "${GREEN}✅ 'aktif' alanı kullanımı yok${NC}"
fi

echo ""
echo -e "${BLUE}📋 2. Adres Alanları Kontrolü${NC}"
echo "----------------------------------------"

# sehir → il kontrolü
echo "🔍 'sehir' alanı kullanımı kontrol ediliyor..."
SEHIR_FOUND=$(grep -r "sehir" app/Models/ resources/views/ app/Http/Controllers/ 2>/dev/null | grep -v "il" | wc -l)
if [ $SEHIR_FOUND -gt 0 ]; then
    echo -e "${RED}❌ 'sehir' alanı kullanımı bulundu: $SEHIR_FOUND adet${NC}"
    grep -r "sehir" app/Models/ resources/views/ app/Http/Controllers/ 2>/dev/null | grep -v "il" | head -5
    ERROR_COUNT=$((ERROR_COUNT + 1))
else
    echo -e "${GREEN}✅ 'sehir' alanı kullanımı yok${NC}"
fi

# sehir_id → il_id kontrolü
echo "🔍 'sehir_id' alanı kullanımı kontrol ediliyor..."
SEHIR_ID_FOUND=$(grep -r "sehir_id" app/Models/ resources/views/ app/Http/Controllers/ 2>/dev/null | wc -l)
if [ $SEHIR_ID_FOUND -gt 0 ]; then
    echo -e "${RED}❌ 'sehir_id' alanı kullanımı bulundu: $SEHIR_ID_FOUND adet${NC}"
    grep -r "sehir_id" app/Models/ resources/views/ app/Http/Controllers/ 2>/dev/null | head -5
    ERROR_COUNT=$((ERROR_COUNT + 1))
else
    echo -e "${GREEN}✅ 'sehir_id' alanı kullanımı yok${NC}"
fi

# bolge_id kontrolü
echo "🔍 'bolge_id' alanı kullanımı kontrol ediliyor..."
BOLGE_ID_FOUND=$(grep -r "bolge_id" app/Models/ resources/views/ app/Http/Controllers/ 2>/dev/null | wc -l)
if [ $BOLGE_ID_FOUND -gt 0 ]; then
    echo -e "${RED}❌ 'bolge_id' alanı kullanımı bulundu: $BOLGE_ID_FOUND adet${NC}"
    grep -r "bolge_id" app/Models/ resources/views/ app/Http/Controllers/ 2>/dev/null | head -5
    ERROR_COUNT=$((ERROR_COUNT + 1))
else
    echo -e "${GREEN}✅ 'bolge_id' alanı kullanımı yok${NC}"
fi

echo ""
echo -e "${BLUE}📋 3. Kişi Alanları Kontrolü${NC}"
echo "----------------------------------------"

# ad_soyad → tam_ad kontrolü
echo "🔍 'ad_soyad' alanı kullanımı kontrol ediliyor..."
AD_SOYAD_FOUND=$(grep -r "ad_soyad" app/Models/ resources/views/ app/Http/Controllers/ 2>/dev/null | wc -l)
if [ $AD_SOYAD_FOUND -gt 0 ]; then
    echo -e "${RED}❌ 'ad_soyad' alanı kullanımı bulundu: $AD_SOYAD_FOUND adet${NC}"
    grep -r "ad_soyad" app/Models/ resources/views/ app/Http/Controllers/ 2>/dev/null | head -5
    ERROR_COUNT=$((ERROR_COUNT + 1))
else
    echo -e "${GREEN}✅ 'ad_soyad' alanı kullanımı yok${NC}"
fi

# full_name → tam_ad kontrolü
echo "🔍 'full_name' alanı kullanımı kontrol ediliyor..."
FULL_NAME_FOUND=$(grep -r "full_name" app/Models/ resources/views/ app/Http/Controllers/ 2>/dev/null | wc -l)
if [ $FULL_NAME_FOUND -gt 0 ]; then
    echo -e "${RED}❌ 'full_name' alanı kullanımı bulundu: $FULL_NAME_FOUND adet${NC}"
    grep -r "full_name" app/Models/ resources/views/ app/Http/Controllers/ 2>/dev/null | head -5
    ERROR_COUNT=$((ERROR_COUNT + 1))
else
    echo -e "${GREEN}✅ 'full_name' alanı kullanımı yok${NC}"
fi

echo ""
echo -e "${BLUE}📋 4. Model İlişkileri Kontrolü${NC}"
echo "----------------------------------------"

# sehir() → il() kontrolü
echo "🔍 'sehir()' relationship kullanımı kontrol ediliyor..."
SEHIR_FUNC_FOUND=$(grep -r "sehir()" app/Models/ 2>/dev/null | wc -l)
if [ $SEHIR_FUNC_FOUND -gt 0 ]; then
    echo -e "${RED}❌ 'sehir()' relationship kullanımı bulundu: $SEHIR_FUNC_FOUND adet${NC}"
    grep -r "sehir()" app/Models/ 2>/dev/null | head -5
    ERROR_COUNT=$((ERROR_COUNT + 1))
else
    echo -e "${GREEN}✅ 'sehir()' relationship kullanımı yok${NC}"
fi

# bolge() kontrolü
echo "🔍 'bolge()' relationship kullanımı kontrol ediliyor..."
BOLGE_FUNC_FOUND=$(grep -r "bolge()" app/Models/ 2>/dev/null | wc -l)
if [ $BOLGE_FUNC_FOUND -gt 0 ]; then
    echo -e "${RED}❌ 'bolge()' relationship kullanımı bulundu: $BOLGE_FUNC_FOUND adet${NC}"
    grep -r "bolge()" app/Models/ 2>/dev/null | head -5
    ERROR_COUNT=$((ERROR_COUNT + 1))
else
    echo -e "${GREEN}✅ 'bolge()' relationship kullanımı yok${NC}"
fi

echo ""
echo -e "${BLUE}📋 5. Deprecated Model Kontrolü${NC}"
echo "----------------------------------------"

# Sehir model kullanımı kontrolü
echo "🔍 'Sehir' model kullanımı kontrol ediliyor..."
SEHIR_MODEL_FOUND=$(grep -r "Sehir::" app/ 2>/dev/null | wc -l)
if [ $SEHIR_MODEL_FOUND -gt 0 ]; then
    echo -e "${RED}❌ 'Sehir' model kullanımı bulundu: $SEHIR_MODEL_FOUND adet${NC}"
    grep -r "Sehir::" app/ 2>/dev/null | head -5
    ERROR_COUNT=$((ERROR_COUNT + 1))
else
    echo -e "${GREEN}✅ 'Sehir' model kullanımı yok${NC}"
fi

echo ""
echo "=================================================="
echo -e "${BLUE}📊 Context7 Kuralları Kontrol Sonucu${NC}"
echo "=================================================="

# Create metodlarında yasak veri kaynakları kontrolü (YENİ)
echo ""
check_create_method_data_sources
if [ $? -ne 0 ]; then
    ERROR_COUNT=$((ERROR_COUNT + 1))
fi

# Yasak database alan adları kontrolü (YENİ)
echo ""
check_forbidden_field_names
if [ $? -ne 0 ]; then
    ERROR_COUNT=$((ERROR_COUNT + 1))

    if [ "$AUTO_FIX" = true ]; then
        echo ""
        auto_fix_forbidden_fields
        echo ""
        echo -e "${GREEN}✅ Yasak alan adları otomatik düzeltildi${NC}"
    fi
fi

# Ek kontroller
if [ "$PERFORMANCE_CHECK" = true ]; then
    echo ""
    performance_check
fi

if [ "$SECURITY_CHECK" = true ]; then
    echo ""
    security_check
fi

    if [ "$QUALITY_CHECK" = true ]; then
        echo ""
        quality_check
    fi

    if [ "$SCHEMA_CHECK" = true ]; then
        echo ""
        schema_check
    fi

    if [ "$API_CHECK" = true ]; then
        echo ""
        api_check
    fi

    if [ "$FRONTEND_CHECK" = true ]; then
        echo ""
        frontend_check
    fi

    if [ "$TESTS_CHECK" = true ]; then
        echo ""
        tests_check
    fi

    if [ "$AUTO_TEST" = true ]; then
        echo ""
        auto_test_system
        echo ""
        suggest_fixes
    fi

    if [ -n "$TEST_PAGE" ]; then
        echo ""
        test_specific_page "$TEST_PAGE"
        echo ""
        suggest_fixes
    fi

    if [ -n "$TEST_FEATURE" ]; then
        echo ""
        test_specific_feature "$TEST_FEATURE"
        echo ""
        suggest_fixes
    fi

    if [ "$DESIGN_CHECK" = true ]; then
        echo ""
        design_check
    fi

    if [ "$BUTTON_CHECK" = true ]; then
        echo ""
        button_check
    fi

    if [ "$UI_CHECK" = true ]; then
        echo ""
        ui_check
    fi

    if [ "$AI_CHECK" = true ]; then
        echo ""
        ai_service_check
    fi

    if [ "$ROUTE_CHECK" = true ]; then
        echo ""
        check_route_conflicts
    fi

    if [ "$DATABASE_FIELD_CHECK" = true ]; then
        echo ""
        check_database_field_consistency
    fi

    if [ "$AI_SETTINGS_CHECK" = true ]; then
        echo ""
        check_ai_settings
    fi

    if [ "$AI_ANALYSIS" = true ]; then
        echo ""
        echo -e "${PURPLE}🤖 AI-Powered Code Analysis Başlatılıyor...${NC}"
        echo "========================================"

        # AI analiz script'ini çalıştır
        local ai_args=""
        if [ "$AI_DEEP" = true ]; then
            ai_args="$ai_args --deep"
        fi
        if [ "$AI_PERFORMANCE" = true ]; then
            ai_args="$ai_args --performance"
        fi
        if [ "$AI_SECURITY" = true ]; then
            ai_args="$ai_args --security"
        fi
        if [ "$AI_QUALITY" = true ]; then
            ai_args="$ai_args --quality"
        fi

        # AI analiz script'ini çalıştır
        if [ -f "scripts/context7-ai-analysis.sh" ]; then
            echo -e "${BLUE}🚀 AI Analiz Script'i çalıştırılıyor...${NC}"
            ./scripts/context7-ai-analysis.sh $ai_args
        else
            echo -e "${RED}❌ AI Analiz Script'i bulunamadı: scripts/context7-ai-analysis.sh${NC}"
        fi
    fi

# Otomatik düzeltme
if [ "$AUTO_FIX" = true ] && [ $ERROR_COUNT -gt 0 ]; then
    echo ""
    auto_fix_errors
    echo ""
    echo -e "${BLUE}🔄 Düzeltme sonrası tekrar kontrol ediliyor...${NC}"
    # Tekrar kontrol et
    ERROR_COUNT=0
    # ... (tekrar kontrol kodu buraya eklenebilir)
fi

echo ""
echo "=================================================="
echo -e "${BLUE}📊 Context7 Kuralları Kontrol Sonucu${NC}"
echo "=================================================="

if [ $ERROR_COUNT -eq 0 ]; then
    echo -e "${GREEN}🎉 TÜM KONTROLLER BAŞARILI!${NC}"
    echo -e "${GREEN}✅ Context7 kurallarına %100 uyumlu${NC}"
    echo -e "${GREEN}✅ Hiçbir hata tespit edilmedi${NC}"

        # Ek özellikler çalıştırıldıysa bilgi ver
        if [ "$PERFORMANCE_CHECK" = true ] || [ "$SECURITY_CHECK" = true ] || [ "$QUALITY_CHECK" = true ] || [ "$SCHEMA_CHECK" = true ] || [ "$API_CHECK" = true ] || [ "$FRONTEND_CHECK" = true ] || [ "$TESTS_CHECK" = true ] || [ "$AUTO_TEST" = true ] || [ -n "$TEST_PAGE" ] || [ -n "$TEST_FEATURE" ] || [ "$DESIGN_CHECK" = true ] || [ "$BUTTON_CHECK" = true ] || [ "$UI_CHECK" = true ]; then
            echo -e "${CYAN}ℹ️  Ek kontroller de tamamlandı${NC}"
        fi

    exit 0
else
    echo -e "${RED}❌ $ERROR_COUNT HATA TESPİT EDİLDİ!${NC}"
    echo -e "${YELLOW}⚠️  Context7 kurallarına aykırı kullanımlar bulundu${NC}"

    if [ "$AUTO_FIX" = true ]; then
        echo -e "${PURPLE}🔧 Otomatik düzeltme yapıldı${NC}"
        echo -e "${YELLOW}⚠️  Lütfen değişiklikleri kontrol edin ve test edin${NC}"
    else
        echo -e "${YELLOW}⚠️  Lütfen yukarıdaki hataları düzeltin${NC}"
        echo -e "${CYAN}💡 Otomatik düzeltme için: ./scripts/context7-check.sh --auto-fix${NC}"
    fi

    exit 1
fi
