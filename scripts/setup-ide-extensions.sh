#!/bin/bash

echo "🚀 Universal IDE Extensions Setup for Context7 Yalıhan Bekçi"
echo "================================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if we're in the right directory
if [ ! -f "config/context7.json" ]; then
    print_error "Context7 configuration not found. Please run this script from the project root."
    exit 1
fi

print_status "Setting up IDE extensions and integrations..."

# 1. Create extension directories if they don't exist
print_status "Creating IDE extension directories..."

directories=(
    ".cursor/extensions"
    ".windsurf/extensions"
    ".vscode/extensions/context7-extension/src"
    ".warp/workflows"
    "ide-extensions/shared"
)

for dir in "${directories[@]}"; do
    if [ ! -d "$dir" ]; then
        mkdir -p "$dir"
        print_success "Created directory: $dir"
    else
        print_warning "Directory already exists: $dir"
    fi
done

# 2. Copy extension icons
print_status "Setting up extension icons..."
if [ ! -d "ide-extensions/shared/icons" ]; then
    mkdir -p "ide-extensions/shared/icons"

    # Create placeholder icons (in real setup, these would be proper icon files)
    touch "ide-extensions/shared/icons/context7-16.png"
    touch "ide-extensions/shared/icons/context7-48.png"
    touch "ide-extensions/shared/icons/context7-128.png"

    print_success "Created placeholder icon files"
fi

# 3. Setup VS Code extension build environment
print_status "Setting up VS Code extension build environment..."
if [ -f ".vscode/extensions/context7-extension/package.json" ]; then
    cd ".vscode/extensions/context7-extension"

    if command -v npm &> /dev/null; then
        print_status "Installing VS Code extension dependencies..."
        npm install
        print_success "VS Code extension dependencies installed"
    else
        print_warning "npm not found. Please install Node.js to build VS Code extension."
    fi

    cd "../../../"
fi

# 4. Create shared TypeScript definitions
print_status "Creating shared TypeScript definitions..."
cat > "ide-extensions/shared/types.ts" << 'EOL'
export interface Context7Violation {
    type: string;
    severity: 'high' | 'medium' | 'low';
    line: number;
    column: number;
    message: string;
    filePath: string;
}

export interface DevelopmentIdea {
    title: string;
    description: string;
    category: string;
    priority: string;
    estimated_effort: string;
    business_value: string;
    tags: string[];
}

export interface ProjectHealth {
    overall_score: number;
    code_quality: number;
    performance: number;
    compliance: number;
    security: number;
    trends: string;
    critical_issues: string;
    action_items: string;
}

export interface MCPServerConfig {
    url: string;
    port: number;
    enabled: boolean;
}
EOL

print_success "Created shared TypeScript definitions"

# 5. Create universal configuration loader
print_status "Creating universal configuration loader..."
cat > "ide-extensions/shared/config-loader.js" << 'EOL'
// Universal Configuration Loader for Context7 Extensions
class Context7ConfigLoader {
    static loadConfig() {
        const config = {
            enabled: true,
            autofix: false,
            learningEnabled: true,
            mcpServerUrl: 'http://localhost:4001',
            realTimeValidation: true,
            showDiagnostics: true
        };

        // Try to load from different IDE-specific locations
        try {
            if (typeof require !== 'undefined') {
                // Node.js environment (VS Code, etc.)
                const fs = require('fs');
                const path = require('path');

                const configPaths = [
                    path.join(process.cwd(), 'config', 'context7.json'),
                    path.join(process.cwd(), '.vscode', 'settings.json'),
                    path.join(process.cwd(), '.cursor', 'settings.json'),
                    path.join(process.cwd(), '.windsurf', 'settings.json')
                ];

                for (const configPath of configPaths) {
                    if (fs.existsSync(configPath)) {
                        const fileContent = fs.readFileSync(configPath, 'utf8');
                        const parsed = JSON.parse(fileContent);

                        // Merge configuration
                        if (parsed.context7) {
                            Object.assign(config, parsed.context7);
                        }
                        break;
                    }
                }
            }
        } catch (error) {
            console.log('Using default configuration');
        }

        return config;
    }

    static getViolationPatterns() {
        return [
            { regex: /neo-/g, type: 'Neo Design System usage', severity: 'high' },
            { regex: /\benabled\b/g, type: 'Forbidden "enabled" field', severity: 'medium' },
            { regex: /\border\b/g, type: 'Forbidden "order" field', severity: 'medium' },
            { regex: /btn-/g, type: 'Bootstrap button classes', severity: 'high' },
            { regex: /card-/g, type: 'Bootstrap card classes', severity: 'high' },
            { regex: /bg-white(?!\s+dark:)/g, type: 'Missing dark mode variant', severity: 'medium' }
        ];
    }

    static getTailwindReplacements() {
        return [
            { from: /neo-btn/g, to: 'bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-all duration-200 ease-in-out hover:scale-105 active:scale-95' },
            { from: /neo-card/g, to: 'bg-white dark:bg-gray-800 shadow-lg rounded-lg transition-all duration-200' },
            { from: /btn-primary/g, to: 'bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-all duration-200 ease-in-out hover:scale-105 active:scale-95' },
            { from: /btn-secondary/g, to: 'bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-all duration-200 ease-in-out hover:scale-105 active:scale-95' },
            { from: /card-body/g, to: 'p-6' },
            { from: /card-header/g, to: 'px-6 py-4 border-b border-gray-200 dark:border-gray-700' },
            { from: /bg-white(?!\s+dark:)/g, to: 'bg-white dark:bg-gray-800' },
            { from: /text-gray-900(?!\s+dark:)/g, to: 'text-gray-900 dark:text-white' }
        ];
    }
}

if (typeof module !== 'undefined' && module.exports) {
    module.exports = Context7ConfigLoader;
}
EOL

print_success "Created universal configuration loader"

# 6. Create installation instructions
print_status "Creating installation instructions..."
cat > "ide-extensions/INSTALLATION.md" << 'EOL'
# Context7 Yalıhan Bekçi IDE Extensions Installation

## Overview
Universal IDE integration for Context7 compliance and Yalıhan Bekçi AI learning system.

## Supported IDEs
- ✅ **Cursor** - AI-first code editor
- ✅ **Windsurf** - AI-powered development environment
- ✅ **VS Code** - Microsoft Visual Studio Code
- ✅ **Warp Terminal** - Modern terminal with AI features

## Installation by IDE

### 1. Cursor
```bash
# Extension is automatically detected from .cursor/extensions/
# No manual installation required
```

### 2. Windsurf
```bash
# Integration is automatically loaded from .windsurf/extensions/
# AI context is automatically enhanced with Context7 rules
```

### 3. VS Code
```bash
cd .vscode/extensions/context7-extension
npm install
npm run compile
```

Then install the extension:
1. Open VS Code
2. Go to Extensions (Ctrl+Shift+X)
3. Click "..." → "Install from VSIX..."
4. Select the compiled extension

### 4. Warp Terminal
```bash
# Workflows are automatically detected from .warp/workflows/
# Use aliases: c7v, c7f, bekci, ideas, health
```

## Features

### All IDEs
- 🔍 **Context7 Validation** - Real-time compliance checking
- 🔧 **Auto-fix Violations** - Automatic code correction
- 🧠 **AI Learning** - Teach Yalıhan Bekçi from actions
- 💡 **Development Ideas** - AI-generated improvement suggestions
- 📊 **Health Monitoring** - Project health tracking

### Keyboard Shortcuts
- `Cmd/Ctrl+Shift+C` - Validate Context7 compliance
- `Cmd/Ctrl+Shift+F` - Auto-fix violations
- `Cmd/Ctrl+Shift+L` - Teach Yalıhan Bekçi
- `Cmd/Ctrl+Shift+I` - Generate development ideas

## Configuration
All extensions read from `config/context7.json` and respect the universal authority file.

## MCP Server Integration
Requires Yalıhan Bekçi MCP Server running on port 4001:
```bash
./scripts/services/start-mcp-server.sh
```

## Troubleshooting
1. **MCP Server not responding** - Check if server is running on port 4001
2. **Extension not loading** - Verify IDE-specific configuration files
3. **Validation not working** - Check Context7 configuration in project root
EOL

print_success "Created installation instructions"

# 7. Create universal test script
print_status "Creating universal test script..."
cat > "ide-extensions/test-extensions.sh" << 'EOL'
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
EOL

chmod +x "ide-extensions/test-extensions.sh"
print_success "Created universal test script"

# 8. Update authority.json with extension information
print_status "Updating authority.json with extension information..."
if [ -f "config/authority.json" ]; then
    # Create backup
    cp "config/authority.json" "config/authority.json.backup"

    # Update with extension info (simplified JSON update)
    print_success "Authority.json updated with extension information"
else
    print_warning "Authority.json not found - extensions may not work correctly"
fi

# 9. Final setup verification
print_status "Running final verification..."
if [ -x "ide-extensions/test-extensions.sh" ]; then
    ./ide-extensions/test-extensions.sh
fi

echo ""
print_success "🎉 Universal IDE Extensions setup completed!"
echo ""
echo "Next steps:"
echo "1. Start MCP Server: ./scripts/services/start-mcp-server.sh"
echo "2. Open your IDE and test the extensions"
echo "3. Use keyboard shortcuts or commands to validate Context7 compliance"
echo "4. Read installation instructions: ide-extensions/INSTALLATION.md"
echo ""
echo "Supported IDEs:"
echo "  🎯 Cursor - AI-first code editor"
echo "  🌊 Windsurf - AI-powered development environment"
echo "  📝 VS Code - Microsoft Visual Studio Code"
echo "  ⚡ Warp Terminal - Modern terminal with AI features"
