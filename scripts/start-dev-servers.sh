#!/bin/bash

# 🚀 Development Servers Starter
# Laravel + Yalıhan Bekçi birlikte başlatma

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"
LOG_DIR="$PROJECT_DIR/storage/logs"
PID_DIR="$PROJECT_DIR/storage/pids"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Create directories
mkdir -p "$LOG_DIR"
mkdir -p "$PID_DIR"

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "🚀 Development Servers Başlatılıyor"
echo ""
echo "   • Laravel Development Server"
echo "   • Yalıhan Bekçi Watch"
echo "   • USTA Watch (Pattern Sync)"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

cd "$PROJECT_DIR"

# Function to check if port is in use
check_port() {
    lsof -i :$1 > /dev/null 2>&1
    return $?
}

# Function to start Laravel server
start_laravel() {
    if check_port 8000; then
        echo -e "${YELLOW}⚠️  Laravel sunucusu zaten çalışıyor (port 8000)${NC}"
        return 0
    fi

    echo -e "${BLUE}🔄 Laravel development sunucusu başlatılıyor...${NC}"

    php artisan serve --host=0.0.0.0 --port=8000 > "$LOG_DIR/laravel-server.log" 2>&1 &
    LARAVEL_PID=$!
    echo $LARAVEL_PID > "$PID_DIR/laravel-server.pid"

    sleep 2

    if check_port 8000; then
        echo -e "${GREEN}✅ Laravel sunucusu başlatıldı!${NC}"
        echo -e "   📍 URL: http://localhost:8000"
        echo -e "   📝 PID: $LARAVEL_PID"
        echo -e "   📄 Log: $LOG_DIR/laravel-server.log"
        return 0
    else
        echo -e "${RED}❌ Laravel sunucusu başlatılamadı!${NC}"
        return 1
    fi
}

# Function to start Yalıhan Bekçi watch
start_bekci_watch() {
    if [ -f "$PROJECT_DIR/storage/bekci-watch.pid" ]; then
        PID=$(cat "$PROJECT_DIR/storage/bekci-watch.pid")
        if ps -p "$PID" > /dev/null 2>&1; then
            echo -e "${YELLOW}⚠️  Yalıhan Bekçi zaten çalışıyor (PID: $PID)${NC}"
            return 0
        fi
    fi

    echo -e "${BLUE}🛡️  Yalıhan Bekçi watch mode başlatılıyor...${NC}"

    bash "$SCRIPT_DIR/bekci-watch.sh" start

    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ Yalıhan Bekçi başlatıldı!${NC}"
        return 0
    else
        echo -e "${RED}❌ Yalıhan Bekçi başlatılamadı!${NC}"
        return 1
    fi
}

# Function to start USTA Watch (Pattern Sync)
start_usta_watch() {
    if [ -f "$PROJECT_DIR/storage/usta-watch.pid" ]; then
        PID=$(cat "$PROJECT_DIR/storage/usta-watch.pid")
        if ps -p "$PID" > /dev/null 2>&1; then
            echo -e "${YELLOW}⚠️  USTA Watch zaten çalışıyor (PID: $PID)${NC}"
            return 0
        fi
    fi

    echo -e "${BLUE}🎯 USTA Watch başlatılıyor...${NC}"

    bash "$SCRIPT_DIR/usta-watch.sh" start

    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ USTA Watch başlatıldı!${NC}"
        return 0
    else
        echo -e "${RED}❌ USTA Watch başlatılamadı!${NC}"
        return 1
    fi
}

# Function to start Yalıhan Bekçi MCP Server (optional)
start_bekci_mcp() {
    if check_port 4001; then
        echo -e "${YELLOW}⚠️  Yalıhan Bekçi MCP sunucusu zaten çalışıyor (port 4001)${NC}"
        return 0
    fi

    echo -e "${BLUE}🤖 Yalıhan Bekçi MCP sunucusu başlatılıyor...${NC}"

    # Check dependencies
    if [ ! -d "mcp-servers/node_modules" ]; then
        echo -e "${YELLOW}📦 MCP dependencies yükleniyor...${NC}"
        cd mcp-servers
        npm install > "$LOG_DIR/mcp-install.log" 2>&1
        cd "$PROJECT_DIR"
    fi

    # Create required directories
    mkdir -p yalihan-bekci/knowledge
    mkdir -p yalihan-bekci/reports
    mkdir -p yalihan-bekci/config

    # Set environment variables
    export PROJECT_ROOT="$PROJECT_DIR"
    export MCP_SERVER_PORT=4001
    export NODE_ENV=development

    # Start MCP server in background
    cd mcp-servers
    node yalihan-bekci-mcp.js > "$LOG_DIR/bekci-mcp-server.log" 2>&1 &
    MCP_PID=$!
    echo $MCP_PID > "$PID_DIR/bekci-mcp-server.pid"
    cd "$PROJECT_DIR"

    sleep 3

    if check_port 4001; then
        echo -e "${GREEN}✅ Yalıhan Bekçi MCP sunucusu başlatıldı!${NC}"
        echo -e "   📍 Port: 4001"
        echo -e "   📝 PID: $MCP_PID"
        echo -e "   📄 Log: $LOG_DIR/bekci-mcp-server.log"
        return 0
    else
        echo -e "${YELLOW}⚠️  MCP sunucusu başlatıldı ama port kontrolü başarısız${NC}"
        return 0
    fi
}

# Main execution
echo ""
echo "📊 Durum Kontrolü:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Start Laravel
start_laravel
LARAVEL_STATUS=$?

echo ""

# Start Yalıhan Bekçi Watch
start_bekci_watch
BEKCI_WATCH_STATUS=$?

echo ""

# Start USTA Watch (Pattern Sync)
start_usta_watch
USTA_WATCH_STATUS=$?

echo ""

# Start Yalıhan Bekçi MCP (optional, can be skipped if not needed)
if [ "$1" == "--with-mcp" ]; then
    start_bekci_mcp
    BEKCI_MCP_STATUS=$?
    echo ""
fi

# Summary
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "📊 Başlatma Özeti:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

if [ $LARAVEL_STATUS -eq 0 ]; then
    echo -e "${GREEN}✅ Laravel Server${NC}     : http://localhost:8000"
else
    echo -e "${RED}❌ Laravel Server${NC}     : Başlatılamadı"
fi

if [ $BEKCI_WATCH_STATUS -eq 0 ]; then
    echo -e "${GREEN}✅ Yalıhan Bekçi Watch${NC} : Aktif (30s interval)"
else
    echo -e "${RED}❌ Yalıhan Bekçi Watch${NC} : Başlatılamadı"
fi

if [ $USTA_WATCH_STATUS -eq 0 ]; then
    echo -e "${GREEN}✅ USTA Watch${NC}         : Aktif (60s pattern sync)"
else
    echo -e "${RED}❌ USTA Watch${NC}         : Başlatılamadı"
fi

if [ "$1" == "--with-mcp" ]; then
    if [ $BEKCI_MCP_STATUS -eq 0 ]; then
        echo -e "${GREEN}✅ Yalıhan Bekçi MCP${NC}   : Port 4001"
    else
        echo -e "${YELLOW}⚠️  Yalıhan Bekçi MCP${NC}   : Durum belirsiz"
    fi
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "📝 Log Dosyaları:"
echo "   • Laravel: $LOG_DIR/laravel-server.log"
echo "   • Bekçi Watch: $LOG_DIR/bekci-watch.log"
echo "   • USTA Watch: $LOG_DIR/usta-watch.log"
if [ "$1" == "--with-mcp" ]; then
    echo "   • Bekçi MCP: $LOG_DIR/bekci-mcp-server.log"
fi
echo ""
echo "🛑 Durdurmak için:"
echo "   ./scripts/stop-dev-servers.sh"
echo ""
echo "📊 Durum kontrolü:"
echo "   ./scripts/status-dev-servers.sh"
echo ""



