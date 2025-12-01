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
