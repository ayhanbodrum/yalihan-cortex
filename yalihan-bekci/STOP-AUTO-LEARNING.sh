#!/bin/bash

echo "🛑 TestSprite MCP durduruluyor..."

if [ -f "testsprite/server/testsprite-mcp.pid" ]; then
    PID=$(cat testsprite/server/testsprite-mcp.pid)
    kill $PID 2>/dev/null
    rm testsprite/server/testsprite-mcp.pid
    echo "✅ MCP durduruldu (PID: $PID)"
else
    echo "⚠️ PID dosyası bulunamadı, tüm node process'leri durduruluyor..."
    killall node 2>/dev/null
    echo "✅ Tüm node process'leri durduruldu"
fi

echo "👋 TestSprite kapatıldı."
