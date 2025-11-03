#!/bin/bash

echo "🚀 Starting YaliHaneMlakWarp MCP Server..."

# Check if node_modules exists
if [ ! -d "node_modules" ]; then
    echo "📦 Installing dependencies..."
    npm install
fi

# Check if .env exists
if [ ! -f ".env" ]; then
    echo "⚙️ Creating .env file..."
    cat > .env << EOL
NODE_ENV=development
TESTSPRITE_API_KEY=sk-user-pKBEIp1I0H15b1vSI5Ky-Qrc2vSI5wvGPvOCFkA-WOOx254eMS527AR0ZDpXpVem6vZhuma3GL2PlCL_vF9XHU3EpgIulEPyrZ667d-6NqCTYCElLe64_hht-xoHwq0UwFA
YALIHANEMLAKWARP_API_URL=http://localhost:8000
EOL
fi

# Start the server
echo "🔥 Starting MCP Server..."
node index.mjs
