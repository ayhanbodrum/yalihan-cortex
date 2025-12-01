#!/bin/bash

# Universal MCP Servers Starter
# Tüm Context7 MCP serverlarını başlatır

PROJECT_ROOT=$(pwd)
LOG_DIR="$PROJECT_ROOT/logs/mcp"
PID_DIR="$PROJECT_ROOT/logs/pids"

# Create log directories
mkdir -p "$LOG_DIR"
mkdir -p "$PID_DIR"

echo "🚀 Starting Universal Context7 MCP Servers..."
echo "📁 Project Root: $PROJECT_ROOT"
echo "📝 Logs: $LOG_DIR"

# Function to start a server
start_server() {
    local server_name=$1
    local server_script=$2
    local port=$3

    echo "🔌 Starting $server_name on port $port..."

    # Kill existing process if running
    if [ -f "$PID_DIR/$server_name.pid" ]; then
        local old_pid=$(cat "$PID_DIR/$server_name.pid")
        if kill -0 $old_pid 2>/dev/null; then
            echo "⚠️  Killing existing $server_name process ($old_pid)"
            kill $old_pid
        fi
        rm -f "$PID_DIR/$server_name.pid"
    fi

    # Set environment
    export PROJECT_ROOT=$PROJECT_ROOT
    export MCP_SERVER_PORT=$port

    # Start server in background
    cd "$PROJECT_ROOT/mcp-servers"
    node "$server_script" > "$LOG_DIR/$server_name.log" 2>&1 &
    local pid=$!

    # Save PID
    echo $pid > "$PID_DIR/$server_name.pid"

    # Check if started successfully
    sleep 2
    if kill -0 $pid 2>/dev/null; then
        echo "✅ $server_name started successfully (PID: $pid)"
        return 0
    else
        echo "❌ Failed to start $server_name"
        return 1
    fi
}

# Install MCP dependencies if needed
if [ ! -d "$PROJECT_ROOT/mcp-servers/node_modules" ]; then
    echo "📦 Installing MCP server dependencies..."
    cd "$PROJECT_ROOT/mcp-servers"
    npm install
    cd "$PROJECT_ROOT"
fi

# Create required directories
mkdir -p yalihan-bekci/{knowledge,reports,config}
mkdir -p reports/context7
mkdir -p .context7/cache

echo ""
echo "🤖 Starting MCP Servers..."
echo "=========================="

# Start Upstash Context7 MCP (Library docs)
echo "🔍 Starting Upstash Context7 MCP..."
export DB_CONNECTION=mysql
export DB_HOST=127.0.0.1
export DB_PORT=3306
export DB_DATABASE=yalihanemlak_db
export DB_USERNAME=root
export DB_PASSWORD=""
export CTX7_REPO_ROOT="$PROJECT_ROOT"
export CTX7_MEMORY_TARGET="$PROJECT_ROOT/docs/context7-master.md"
export CTX7_RULES_FILE="$PROJECT_ROOT/docs/context7-rules.md"
export CTX7_CONFIG_FILE="$PROJECT_ROOT/config/context7.json"

npx -y @upstash/context7-mcp@latest > "$LOG_DIR/context7-upstash.log" 2>&1 &
upstash_pid=$!
echo $upstash_pid > "$PID_DIR/context7-upstash.pid"
echo "✅ Upstash Context7 MCP started (PID: $upstash_pid)"

# Start Yalıhan Bekçi MCP (AI Learning)
start_server "yalihan-bekci" "yalihan-bekci-mcp.js" 4001

# Start Context7 Validator MCP (Real-time validation)
start_server "context7-validator" "context7-validator-mcp.js" 4002

echo ""
echo "🎯 Server Status Summary:"
echo "========================="
echo "📚 Upstash Context7 MCP    : PORT Auto (Library docs)"
echo "🤖 Yalıhan Bekçi MCP       : PORT 4001 (AI Learning)"
echo "🔍 Context7 Validator MCP   : PORT 4002 (Validation)"
echo ""
echo "📝 Logs: $LOG_DIR/"
echo "🔧 PIDs: $PID_DIR/"
echo ""
echo "🏃‍♂️ All MCP servers are running!"
echo "💡 Use './scripts/stop-mcp-servers.sh' to stop all servers"
echo ""
echo "🔗 MCP Integration Ready for:"
echo "   - 🖱️  Cursor IDE"
echo "   - 🌊 Windsurf IDE"
echo "   - ⚡ Warp Terminal"
echo "   - 📝 VS Code"
echo "   - 🐙 GitHub Copilot"
