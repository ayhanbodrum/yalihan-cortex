#!/bin/bash

echo "🧠 TestSprite Context7 Auto-Learning Başlatılıyor..."
echo ""

# Env değişkenlerini yükle
export AUTO_LEARN=true
export CONTEXT7_MODE=true
export PROJECT_ROOT=$(pwd)

# 1. Context7 kurallarını öğren
echo "📚 Context7 master dökümanları okunuyor..."
php artisan testsprite:auto-learn

echo ""
echo "✅ Öğrenme tamamlandı!"
echo ""

# 2. MCP sunucusunu başlat
echo "🚀 MCP sunucusu başlatılıyor..."
cd testsprite/server

# Node modules kontrolü
if [ ! -d "node_modules" ]; then
    echo "📦 Node modules yükleniyor..."
    npm install
fi

# Sunucuyu başlat
node index.js &
MCP_PID=$!

echo ""
echo "✅ TestSprite MCP çalışıyor!"
echo "�� Port: 3333"
echo "🧠 Auto-Learning: ENABLED"
echo ""
echo "📊 Kullanılabilir endpoint'ler:"
echo "  - http://localhost:3333/context7/rules"
echo "  - http://localhost:3333/context7/validate"
echo "  - http://localhost:3333/patterns/common"
echo "  - http://localhost:3333/run-tests"
echo ""
echo "�� Durdurmak için: kill $MCP_PID"
echo "   veya: killall node"
echo ""

# PID'yi kaydet
echo $MCP_PID > testsprite-mcp.pid

echo "💾 PID kaydedildi: testsprite-mcp.pid"
echo "🎉 Hazır! Cursor artık otomatik kullanabilir."
