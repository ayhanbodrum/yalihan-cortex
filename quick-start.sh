#!/bin/bash

# EmlakPro Quick Start Script
# Context7: Rapid development environment setup

echo "🚀 EmlakPro Quick Start"
echo "======================="

# Project directory
PROJECT_DIR="/Users/macbookpro/Projects/yalihanemlakwarp"

# Add Homebrew paths
export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH"

# Check if we're in the right directory
if [ ! -f "$PROJECT_DIR/artisan" ]; then
    echo "❌ Laravel project not found at $PROJECT_DIR"
    exit 1
fi

cd "$PROJECT_DIR"

# Function to check if server is running
check_server() {
    curl -s http://localhost:8000 > /dev/null 2>&1
    return $?
}

# Function to start Laravel server
start_server() {
    echo "🔄 Starting Laravel server..."
    /opt/homebrew/bin/php artisan serve --host=0.0.0.0 --port=8000 &
    SERVER_PID=$!
    echo "📝 Server PID: $SERVER_PID"

    # Wait a moment for server to start
    sleep 3

    if check_server; then
        echo "✅ Laravel server started successfully"
        echo "🌐 URL: http://localhost:8000"
    else
        echo "❌ Server failed to start"
        return 1
    fi
}

# Function to stop any existing server
stop_server() {
    echo "🛑 Stopping existing servers on port 8000..."
    pkill -f "php.*artisan.*serve" 2>/dev/null || true
    sleep 2
}

# Main execution
echo "🔍 Checking server status..."

if check_server; then
    echo "✅ Server is already running at http://localhost:8000"
    echo ""
    echo "📊 Server Status:"
    lsof -i :8000 2>/dev/null || echo "No detailed process info available"
else
    echo "🔄 Server not running, starting..."
    stop_server
    start_server
fi

echo ""
echo "🎯 Quick Links:"
echo "• Main Dashboard: http://localhost:8000/admin/dashboard"
echo "• Stable Create: http://localhost:8000/stable-create"
echo "• AI Settings: http://localhost:8000/admin/ai-settings"

echo ""
echo "🛠️ Development Commands:"
echo "• Stop server: pkill -f 'php.*artisan.*serve'"
echo "• View logs: tail -f storage/logs/laravel.log"
echo "• Clear cache: php artisan cache:clear"
echo "• Build assets: ./build-assets.sh"

echo ""
echo "✅ Ready for development!"
