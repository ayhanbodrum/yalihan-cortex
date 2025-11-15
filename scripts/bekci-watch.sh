#!/bin/bash

# Yalıhan Bekçi - Sürekli Gözlem Script'i
# Dış terminalden çalıştırılabilir background process

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"
LOG_DIR="$PROJECT_DIR/storage/logs"
PID_FILE="$PROJECT_DIR/storage/bekci-watch.pid"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Functions
log_info() {
    echo -e "${BLUE}[$(date '+%Y-%m-%d %H:%M:%S')]${NC} $1"
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" >> "$LOG_DIR/bekci-watch.log"
}

log_success() {
    echo -e "${GREEN}[$(date '+%Y-%m-%d %H:%M:%S')]${NC} ✅ $1"
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] ✅ $1" >> "$LOG_DIR/bekci-watch.log"
}

log_warning() {
    echo -e "${YELLOW}[$(date '+%Y-%m-%d %H:%M:%S')]${NC} ⚠️  $1"
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] ⚠️  $1" >> "$LOG_DIR/bekci-watch.log"
}

log_error() {
    echo -e "${RED}[$(date '+%Y-%m-%d %H:%M:%S')]${NC} ❌ $1"
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] ❌ $1" >> "$LOG_DIR/bekci-watch.log"
}

# Check if already running
check_running() {
    if [ -f "$PID_FILE" ]; then
        PID=$(cat "$PID_FILE")
        if ps -p "$PID" > /dev/null 2>&1; then
            return 0
        fi
    fi
    return 1
}

# Start watch mode
start_watch() {
    if check_running; then
        log_error "Yalıhan Bekçi zaten çalışıyor! (PID: $(cat $PID_FILE))"
        echo ""
        echo "Durdurmak için: $0 stop"
        exit 1
    fi

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo ""
    echo "🛡️  Yalıhan Bekçi - Sürekli Gözlem Başlıyor"
    echo ""
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo ""

    log_info "Yalıhan Bekçi başlatılıyor..."
    log_info "Log dosyası: $LOG_DIR/bekci-watch.log"
    log_info "PID dosyası: $PID_FILE"
    echo ""

    # Background process olarak çalıştır
    nohup bash -c "
        cd '$PROJECT_DIR'
        echo \$\$ > '$PID_FILE'

        echo '[$(date '+%Y-%m-%d %H:%M:%S')] 🛡️ Yalıhan Bekçi gözlem başladı' >> '$LOG_DIR/bekci-watch.log'

        LAST_VIOLATIONS=0
        CHECK_INTERVAL=30

        while true; do
            echo '[$(date '+%Y-%m-%d %H:%M:%S')] 🔍 Tarama yapılıyor...' >> '$LOG_DIR/bekci-watch.log'

            # Enforcement check
            OUTPUT=\$(php artisan bekci:enforce --scan 2>&1)

            # İhlal sayısını bul
            VIOLATIONS=\$(echo \"\$OUTPUT\" | grep -oP '\\d+ ihlal bulundu' | grep -oP '\\d+' | head -1)

            if [ -z \"\$VIOLATIONS\" ]; then
                VIOLATIONS=0
            fi

            # Değişiklik varsa bildir
            if [ \"\$VIOLATIONS\" -ne \"\$LAST_VIOLATIONS\" ]; then
                if [ \"\$VIOLATIONS\" -eq 0 ]; then
                    echo '[$(date '+%Y-%m-%d %H:%M:%S')] ✅ Hiç ihlal yok!' >> '$LOG_DIR/bekci-watch.log'
                elif [ \"\$VIOLATIONS\" -gt \"\$LAST_VIOLATIONS\" ]; then
                    echo '[$(date '+%Y-%m-%d %H:%M:%S')] ⚠️ YENİ İHLAL! \$LAST_VIOLATIONS → \$VIOLATIONS' >> '$LOG_DIR/bekci-watch.log'
                    echo '[$(date '+%Y-%m-%d %H:%M:%S')] 🚨 UYARI: İhlal sayısı arttı!' >> '$LOG_DIR/bekci-violations.log'
                else
                    echo '[$(date '+%Y-%m-%d %H:%M:%S')] ✅ İhlal azaldı! \$LAST_VIOLATIONS → \$VIOLATIONS' >> '$LOG_DIR/bekci-watch.log'
                fi
                LAST_VIOLATIONS=\$VIOLATIONS
            fi

            # Context7 check
            CONTEXT7_OUTPUT=\$(php artisan context7:check 2>&1 | grep 'Context7 İhlali' | grep -oP '\\d+')
            if [ ! -z \"\$CONTEXT7_OUTPUT\" ]; then
                echo '[$(date '+%Y-%m-%d %H:%M:%S')] 📊 Context7: \$CONTEXT7_OUTPUT ihlal' >> '$LOG_DIR/bekci-watch.log'
            fi

            sleep \$CHECK_INTERVAL
        done
    " > /dev/null 2>&1 &

    sleep 2

    if check_running; then
        PID=$(cat "$PID_FILE")
        log_success "Yalıhan Bekçi başlatıldı! (PID: $PID)"
        echo ""
        echo "📊 Kontroller:"
        echo "   • Enforcement: Her 30 saniye"
        echo "   • Context7: Her 30 saniye"
        echo ""
        echo "📄 Log takibi:"
        echo "   tail -f $LOG_DIR/bekci-watch.log"
        echo ""
        echo "🛑 Durdurmak için:"
        echo "   $0 stop"
        echo ""
    else
        log_error "Başlatılamadı!"
        exit 1
    fi
}

# Stop watch mode
stop_watch() {
    if ! check_running; then
        log_warning "Yalıhan Bekçi zaten durmuş"
        exit 1
    fi

    PID=$(cat "$PID_FILE")
    log_info "Yalıhan Bekçi durduruluyor... (PID: $PID)"

    kill "$PID" 2>/dev/null
    rm -f "$PID_FILE"

    log_success "Yalıhan Bekçi durduruldu!"
}

# Status check
status_check() {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo ""
    echo "🛡️  Yalıhan Bekçi - Durum"
    echo ""
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo ""

    if check_running; then
        PID=$(cat "$PID_FILE")
        echo "✅ Durum: ÇALIŞIYOR"
        echo "📍 PID: $PID"
        echo "⏱️  Başlangıç: $(ps -p $PID -o lstart= 2>/dev/null)"
        echo ""
        echo "📊 Son 10 log:"
        tail -10 "$LOG_DIR/bekci-watch.log" 2>/dev/null || echo "Log yok"
    else
        echo "❌ Durum: DURMUŞ"
        echo ""
        echo "Başlatmak için:"
        echo "   $0 start"
    fi
    echo ""
}

# Log viewer
view_logs() {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo ""
    echo "📄 Yalıhan Bekçi - Log Viewer"
    echo ""
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo ""

    if [ "$1" == "follow" ]; then
        echo "📡 Canlı log takibi (Ctrl+C ile çık)..."
        echo ""
        tail -f "$LOG_DIR/bekci-watch.log"
    else
        echo "📊 Son 50 log:"
        echo ""
        tail -50 "$LOG_DIR/bekci-watch.log" 2>/dev/null || echo "Log yok"
    fi
}

# Main
case "$1" in
    start)
        start_watch
        ;;
    stop)
        stop_watch
        ;;
    restart)
        stop_watch
        sleep 2
        start_watch
        ;;
    status)
        status_check
        ;;
    logs)
        view_logs "$2"
        ;;
    *)
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
        echo ""
        echo "🛡️  Yalıhan Bekçi - Sürekli Gözlem"
        echo ""
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
        echo ""
        echo "Kullanım: $0 {start|stop|restart|status|logs}"
        echo ""
        echo "Komutlar:"
        echo "  start    - Gözlemi başlat (background)"
        echo "  stop     - Gözlemi durdur"
        echo "  restart  - Yeniden başlat"
        echo "  status   - Durum kontrolü"
        echo "  logs     - Son logları göster"
        echo "  logs follow - Canlı log takibi"
        echo ""
        echo "Örnek:"
        echo "  $0 start"
        echo "  $0 logs follow"
        echo ""
        exit 1
        ;;
esac
