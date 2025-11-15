#!/bin/bash

# TestSprite MCP Test Betiği
echo "TestSprite MCP Test Betiği Başlatılıyor..."

# Sunucu dizinine git
cd "$(dirname "$0")/server"

# Node.js bağımlılıklarını kontrol et
if [ ! -d "node_modules" ]; then
    echo "Node.js bağımlılıkları kuruluyor..."
    npm install
fi

# Sunucuyu başlat
echo "TestSprite MCP sunucusu başlatılıyor..."
node index.js &
SERVER_PID=$!

# Sunucunun başlaması için bekle
sleep 3

# Test istekleri gönder
echo "Test istekleri gönderiliyor..."

# Migration testi
echo "Migration testi yapılıyor..."
curl -s -X POST http://localhost:3333/api/test -H "Content-Type: application/json" -d '{"type":"migrations"}' | jq .

# Seeder testi
echo "Seeder testi yapılıyor..."
curl -s -X POST http://localhost:3333/api/test -H "Content-Type: application/json" -d '{"type":"seeders"}' | jq .

# Rapor oluşturma testi
echo "Rapor oluşturma testi yapılıyor..."
curl -s -X POST http://localhost:3333/api/report -H "Content-Type: application/json" -d '{"type":"summary"}' | jq .

# Sunucuyu durdur
echo "TestSprite MCP sunucusu durduruluyor..."
kill $SERVER_PID

echo "Test tamamlandı."
