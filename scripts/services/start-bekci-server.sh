#!/bin/bash

# Yalıhan Bekçi MCP Server Starter
# Usage: ./start-bekci-server.sh [port]

PORT=${1:-4001}
PROJECT_ROOT=$(pwd)

echo "🚀 Starting Yalıhan Bekçi MCP Server..."
echo "📁 Project Root: $PROJECT_ROOT"
echo "🔌 Port: $PORT"

# Install dependencies if needed
if [ ! -d "mcp-servers/node_modules" ]; then
    echo "📦 Installing MCP dependencies..."
    cd mcp-servers
    npm install
    cd ..
fi

# Create required directories
mkdir -p yalihan-bekci/knowledge
mkdir -p yalihan-bekci/reports
mkdir -p yalihan-bekci/config

# Set environment variables
export PROJECT_ROOT=$PROJECT_ROOT
export MCP_SERVER_PORT=$PORT
export NODE_ENV=development

echo "🤖 Starting Yalıhan Bekçi MCP Server on port $PORT..."
echo "🧠 Knowledge base: $PROJECT_ROOT/yalihan-bekci/knowledge"
echo "📊 Reports: $PROJECT_ROOT/yalihan-bekci/reports"

# Start the MCP server
cd mcp-servers && node yalihan-bekci-mcp.js
