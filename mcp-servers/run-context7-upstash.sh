#!/bin/bash

# Context7 Upstash MCP Wrapper
# .env dosyasından CONTEXT7_API_KEY'i yükleyip Context7 Upstash MCP'yi başlatır

set -e

# Project root
PROJECT_ROOT="/Users/macbookpro/Projects/yalihanai"

# .env dosyasını yükle
if [ -f "$PROJECT_ROOT/.env" ]; then
    export $(grep -v '^#' "$PROJECT_ROOT/.env" | grep CONTEXT7_API_KEY | xargs)
fi

# CONTEXT7_API_KEY kontrolü
if [ -z "$CONTEXT7_API_KEY" ]; then
    echo "❌ CONTEXT7_API_KEY bulunamadı! (.env dosyasını kontrol edin)" >&2
    exit 1
fi

# Context7 Upstash MCP'yi başlat
exec npx -y @upstash/context7-mcp --api-key "$CONTEXT7_API_KEY"



