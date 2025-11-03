#!/bin/bash

# EmlakPro Asset Build Script
# Context7: Automatic path detection and asset compilation

echo "🚀 EmlakPro Asset Build Script"
echo "=================================="

# Add Homebrew paths to PATH
export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH"

# Check Node.js and npm
if command -v node >/dev/null 2>&1; then
    echo "✅ Node.js version: $(node --version)"
else
    echo "❌ Node.js not found in PATH"
    exit 1
fi

if command -v npm >/dev/null 2>&1; then
    echo "✅ npm version: $(npm --version)"
else
    echo "❌ npm not found in PATH"
    exit 1
fi

# Check if we're in the right directory
if [ ! -f "package.json" ]; then
    echo "❌ package.json not found. Please run from project root."
    exit 1
fi

echo ""
echo "📦 Installing dependencies..."
npm install

echo ""
echo "🔨 Building assets with Vite..."
npx vite build

echo ""
echo "✅ Asset build completed!"
echo ""
echo "📊 Build Results:"
ls -la public/build/ 2>/dev/null || echo "No build files found in public/build/"

echo ""
echo "🎯 Next Steps:"
echo "1. Restart Laravel server if needed"
echo "2. Test pages: http://localhost:8000/stable-create"
echo "3. Check browser console for any remaining issues"
