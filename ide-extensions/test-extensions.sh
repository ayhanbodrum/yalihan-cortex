#!/bin/bash

echo "🧪 Testing Context7 IDE Extensions"
echo "=================================="

# Test 1: Check MCP Server connectivity
echo "Testing MCP Server connectivity..."
if curl -s "http://localhost:4001" > /dev/null; then
    echo "✅ MCP Server is running"
else
    echo "❌ MCP Server is not responding"
    echo "   Run: ./scripts/services/start-mcp-server.sh"
fi

# Test 2: Validate Context7 configuration
echo "Testing Context7 configuration..."
if [ -f "config/context7.json" ]; then
    echo "✅ Context7 configuration found"
else
    echo "❌ Context7 configuration missing"
fi

# Test 3: Check authority file
echo "Testing authority file..."
if [ -f "config/authority.json" ]; then
    echo "✅ Authority file found"
else
    echo "❌ Authority file missing"
fi

# Test 4: Test Laravel commands
echo "Testing Laravel commands..."
if php artisan list | grep -q "context7:validate"; then
    echo "✅ Context7 validation command available"
else
    echo "❌ Context7 validation command not found"
fi

if php artisan list | grep -q "bekci:learn"; then
    echo "✅ Yalıhan Bekçi learning command available"
else
    echo "❌ Yalıhan Bekçi learning command not found"
fi

if php artisan list | grep -q "ideas:generate"; then
    echo "✅ Ideas generation command available"
else
    echo "❌ Ideas generation command not found"
fi

# Test 5: Check IDE configuration files
echo "Testing IDE configuration files..."
ide_configs=(
    ".cursor/extensions/context7-cursor-extension.json"
    ".windsurf/extensions/context7-windsurf-integration.json"
    ".vscode/extensions/context7-extension/package.json"
    ".warp/workflows/context7-yalihan-bekci.json"
)

for config in "${ide_configs[@]}"; do
    if [ -f "$config" ]; then
        echo "✅ $config found"
    else
        echo "❌ $config missing"
    fi
done

echo ""
echo "Test completed! Check any ❌ items for issues."
