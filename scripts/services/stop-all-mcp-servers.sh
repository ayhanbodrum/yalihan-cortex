#!/bin/bash

# Universal MCP Servers Stopper
# Tüm Context7 MCP serverlarını durdurur

PROJECT_ROOT=$(pwd)
PID_DIR="$PROJECT_ROOT/logs/pids"

echo "🛑 Stopping Universal Context7 MCP Servers..."

# Function to stop a server
stop_server() {
    local server_name=$1
    local pid_file="$PID_DIR/$server_name.pid"

    if [ -f "$pid_file" ]; then
        local pid=$(cat "$pid_file")
        if kill -0 $pid 2>/dev/null; then
            echo "🔴 Stopping $server_name (PID: $pid)..."
            kill $pid

            # Wait for graceful shutdown
            for i in {1..5}; do
                if ! kill -0 $pid 2>/dev/null; then
                    echo "✅ $server_name stopped successfully"
                    rm -f "$pid_file"
                    return 0
                fi
                sleep 1
            done

            # Force kill if still running
            echo "⚠️  Force killing $server_name..."
            kill -9 $pid 2>/dev/null
            rm -f "$pid_file"
            echo "✅ $server_name force stopped"
        else
            echo "⚠️  $server_name PID file exists but process not running"
            rm -f "$pid_file"
        fi
    else
        echo "ℹ️  $server_name not running (no PID file)"
    fi
}

# Stop all MCP servers
echo ""
echo "🔴 Stopping MCP Servers..."
echo "=========================="

stop_server "context7-upstash"
stop_server "yalihan-bekci"
stop_server "context7-validator"

# Kill any remaining MCP processes
echo ""
echo "🔍 Checking for remaining MCP processes..."

# Check for Node.js MCP processes
mcp_processes=$(pgrep -f "mcp.*\.js" || true)
if [ ! -z "$mcp_processes" ]; then
    echo "⚠️  Found remaining MCP processes: $mcp_processes"
    echo "🔪 Killing remaining MCP processes..."
    pkill -f "mcp.*\.js" || true
fi

# Check for Context7 processes
ctx7_processes=$(pgrep -f "context7" || true)
if [ ! -z "$ctx7_processes" ]; then
    echo "⚠️  Found remaining Context7 processes: $ctx7_processes"
    echo "🔪 Killing remaining Context7 processes..."
    pkill -f "context7" || true
fi

echo ""
echo "✅ All MCP servers have been stopped!"
echo "📁 Log files preserved in: $PROJECT_ROOT/logs/mcp/"
echo ""
echo "💡 Use './scripts/services/start-all-mcp-servers.sh' to restart all servers"
