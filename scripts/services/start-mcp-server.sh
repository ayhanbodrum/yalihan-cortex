#!/bin/bash

echo "🚀 Starting Yalıhan Bekçi MCP Server..."

# Navigate to MCP servers directory
cd mcp-servers

# Check if node_modules exists
if [ ! -d "node_modules" ]; then
    echo "📦 Installing dependencies..."
    npm install
fi

# Start the server
echo "🔥 Starting MCP Server..."
node yalihan-bekci-mcp.js
