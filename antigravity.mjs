#!/usr/bin/env node

/**
 * Antigravity Universal IDE Detection & Configuration System
 * Supports: VS Code, Cursor, Windsurf, Warp, and more
 * Auto-configures Context7 environment for any IDE
 */

import fs from 'fs';
import path from 'path';
import process from 'process';
import { execSync, spawn } from 'child_process';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Color utilities for terminal output
const colors = {
    reset: '\x1b[0m',
    bright: '\x1b[1m',
    red: '\x1b[31m',
    green: '\x1b[32m',
    yellow: '\x1b[33m',
    blue: '\x1b[34m',
    magenta: '\x1b[35m',
    cyan: '\x1b[36m',
    white: '\x1b[37m',
};

function log(message, color = 'white') {
    console.log(`${colors[color]}${message}${colors.reset}`);
}

// IDE Detection System
class AntigravityDetector {
    constructor() {
        this.projectRoot = __dirname;
        this.config = this.loadConfig();
        this.detectedIDEs = [];
        this.activeIDE = null;
    }

    loadConfig() {
        try {
            const configPath = path.join(this.projectRoot, 'antigravity_config.json');
            return JSON.parse(fs.readFileSync(configPath, 'utf8'));
        } catch (error) {
            log(`⚠️  Config file not found: ${error.message}`, 'yellow');
            return this.getDefaultConfig();
        }
    }

    getDefaultConfig() {
        return {
            universalIDESupport: {
                enabled: true,
                autoDetect: true,
                autoConfig: true,
            },
            supportedIDEs: ['vscode', 'cursor', 'windsurf', 'warp'],
            context7Configuration: {
                enabled: true,
                dualSystem: true,
                autoValidation: true,
            },
        };
    }

    // Detect all installed IDEs
    detectIDEs() {
        log('🔍 Detecting installed IDEs...', 'cyan');

        const ideDetectors = {
            vscode: () => this.detectVSCode(),
            cursor: () => this.detectCursor(),
            windsurf: () => this.detectWindsurf(),
            warp: () => this.detectWarp(),
            sublimetext: () => this.detectSublime(),
            atom: () => this.detectAtom(),
            brackets: () => this.detectBrackets(),
        };

        for (const [ideName, detector] of Object.entries(ideDetectors)) {
            try {
                const ideInfo = detector();
                if (ideInfo.installed) {
                    this.detectedIDEs.push({ name: ideName, ...ideInfo });
                    log(`  ✅ ${ideName}: ${ideInfo.version || 'Detected'}`, 'green');
                }
            } catch (error) {
                log(`  ❌ ${ideName}: Not detected`, 'red');
            }
        }

        return this.detectedIDEs;
    }

    detectVSCode() {
        try {
            const version = execSync('code --version', { encoding: 'utf8', stdio: 'pipe' }).split(
                '\n'
            )[0];
            return { installed: true, version, executable: 'code' };
        } catch {
            return { installed: false };
        }
    }

    detectCursor() {
        try {
            const version = execSync('cursor --version', { encoding: 'utf8', stdio: 'pipe' }).split(
                '\n'
            )[0];
            return { installed: true, version, executable: 'cursor' };
        } catch {
            return { installed: false };
        }
    }

    detectWindsurf() {
        try {
            const version = execSync('windsurf --version', {
                encoding: 'utf8',
                stdio: 'pipe',
            }).split('\n')[0];
            return { installed: true, version, executable: 'windsurf' };
        } catch {
            return { installed: false };
        }
    }

    detectWarp() {
        try {
            // Warp is a terminal, check if it's available
            const result = execSync('which warp', { encoding: 'utf8', stdio: 'pipe' });
            return { installed: true, version: 'Terminal', executable: 'warp' };
        } catch {
            return { installed: false };
        }
    }

    detectSublime() {
        try {
            const version = execSync('subl --version', { encoding: 'utf8', stdio: 'pipe' });
            return { installed: true, version: version.trim(), executable: 'subl' };
        } catch {
            return { installed: false };
        }
    }

    detectAtom() {
        try {
            const version = execSync('atom --version', { encoding: 'utf8', stdio: 'pipe' }).split(
                '\n'
            )[0];
            return { installed: true, version, executable: 'atom' };
        } catch {
            return { installed: false };
        }
    }

    detectBrackets() {
        try {
            const version = execSync('brackets --version', { encoding: 'utf8', stdio: 'pipe' });
            return { installed: true, version: version.trim(), executable: 'brackets' };
        } catch {
            return { installed: false };
        }
    }

    // Detect currently active IDE
    detectActiveIDE() {
        log('🎯 Detecting active IDE...', 'cyan');

        try {
            // Check running processes
            const processes = execSync(
                'ps aux | grep -E "(code|cursor|windsurf|atom|subl)" | grep -v grep',
                { encoding: 'utf8', stdio: 'pipe' }
            );

            const lines = processes.split('\n').filter((line) => line.trim());

            for (const line of lines) {
                if (
                    line.includes('/Applications/Visual Studio Code.app') ||
                    line.includes('code --')
                ) {
                    this.activeIDE = 'vscode';
                    break;
                }
                if (line.includes('/Applications/Cursor.app') || line.includes('cursor --')) {
                    this.activeIDE = 'cursor';
                    break;
                }
                if (line.includes('/Applications/Windsurf.app') || line.includes('windsurf --')) {
                    this.activeIDE = 'windsurf';
                    break;
                }
                if (line.includes('/Applications/Sublime Text.app') || line.includes('subl --')) {
                    this.activeIDE = 'sublimetext';
                    break;
                }
                if (line.includes('/Applications/Atom.app') || line.includes('atom --')) {
                    this.activeIDE = 'atom';
                    break;
                }
            }

            if (this.activeIDE) {
                log(`  🎯 Active IDE: ${this.activeIDE}`, 'green');
            } else {
                log('  ℹ️  No active IDE detected', 'yellow');
            }
        } catch (error) {
            log('  ⚠️  Could not detect active IDE', 'yellow');
        }

        return this.activeIDE;
    }

    // Configure specific IDE for Context7
    configureForIDE(ideName) {
        log(`🔧 Configuring ${ideName} for Context7...`, 'cyan');

        const configurations = {
            vscode: () => this.configureVSCode(),
            cursor: () => this.configureCursor(),
            windsurf: () => this.configureWindsurf(),
            warp: () => this.configureWarp(),
            sublimetext: () => this.configureSublime(),
            atom: () => this.configureAtom(),
        };

        if (configurations[ideName]) {
            try {
                configurations[ideName]();
                log(`  ✅ ${ideName} configured successfully`, 'green');
                return true;
            } catch (error) {
                log(`  ❌ Failed to configure ${ideName}: ${error.message}`, 'red');
                return false;
            }
        } else {
            log(`  ⚠️  Configuration for ${ideName} not available`, 'yellow');
            return false;
        }
    }

    configureVSCode() {
        const vscodePath = path.join(process.env.HOME, '.vscode');
        const settingsPath = path.join(vscodePath, 'settings.json');

        if (!fs.existsSync(vscodePath)) {
            fs.mkdirSync(vscodePath, { recursive: true });
        }

        // Read existing settings or create new
        let settings = {};
        if (fs.existsSync(settingsPath)) {
            settings = JSON.parse(fs.readFileSync(settingsPath, 'utf8'));
        }

        // Add Context7 specific settings
        settings['github.copilot.enable'] = true;
        settings['github.copilot.chat.enable'] = true;
        settings['files.associations'] = {
            ...settings['files.associations'],
            '*.context7': 'json',
            'antigravity_config.json': 'jsonc',
        };

        fs.writeFileSync(settingsPath, JSON.stringify(settings, null, 2));
    }

    configureCursor() {
        // Cursor uses VS Code compatible settings
        const cursorPath = path.join(process.env.HOME, 'Library/Application Support/Cursor/User');
        const settingsPath = path.join(cursorPath, 'settings.json');

        if (!fs.existsSync(cursorPath)) {
            fs.mkdirSync(cursorPath, { recursive: true });
        }

        let settings = {};
        if (fs.existsSync(settingsPath)) {
            settings = JSON.parse(fs.readFileSync(settingsPath, 'utf8'));
        }

        settings['cursor.ai.enable'] = true;
        settings['cursor.ai.chat.enable'] = true;
        settings['files.associations'] = {
            ...settings['files.associations'],
            '*.context7': 'json',
            'antigravity_config.json': 'jsonc',
        };

        fs.writeFileSync(settingsPath, JSON.stringify(settings, null, 2));
    }

    configureWindsurf() {
        const windsurfPath = path.join(
            process.env.HOME,
            'Library/Application Support/Windsurf/User'
        );
        const settingsPath = path.join(windsurfPath, 'settings.json');

        if (!fs.existsSync(windsurfPath)) {
            fs.mkdirSync(windsurfPath, { recursive: true });
        }

        let settings = {};
        if (fs.existsSync(settingsPath)) {
            settings = JSON.parse(fs.readFileSync(settingsPath, 'utf8'));
        }

        settings['windsurf.ai.enable'] = true;
        settings['files.associations'] = {
            ...settings['files.associations'],
            '*.context7': 'json',
            'antigravity_config.json': 'jsonc',
        };

        fs.writeFileSync(settingsPath, JSON.stringify(settings, null, 2));
    }

    configureWarp() {
        // Warp is a terminal, configure shell integration
        const warpPath = path.join(process.env.HOME, '.warp');
        const configPath = path.join(warpPath, 'warp.yaml');

        if (!fs.existsSync(warpPath)) {
            fs.mkdirSync(warpPath, { recursive: true });
        }

        const warpConfig = `
# Warp Terminal Configuration for Context7
context7:
  enabled: true
  project_root: ${this.projectRoot}
  mcp_servers:
    yalihan_bekci: "http://localhost:4001"
    context7_validator: "http://localhost:4002"
`;

        fs.writeFileSync(configPath, warpConfig);
    }

    configureSublime() {
        const sublimePath = path.join(
            process.env.HOME,
            'Library/Application Support/Sublime Text/Packages/User'
        );
        const settingsPath = path.join(sublimePath, 'Preferences.sublime-settings');

        if (!fs.existsSync(sublimePath)) {
            fs.mkdirSync(sublimePath, { recursive: true });
        }

        let settings = {};
        if (fs.existsSync(settingsPath)) {
            settings = JSON.parse(fs.readFileSync(settingsPath, 'utf8'));
        }

        settings.file_associations = {
            ...settings.file_associations,
            context7: 'json',
            'antigravity_config.json': 'json',
        };

        fs.writeFileSync(settingsPath, JSON.stringify(settings, null, 2));
    }

    configureAtom() {
        const atomPath = path.join(process.env.HOME, '.atom');
        const configPath = path.join(atomPath, 'config.cson');

        // Atom uses CSON format
        const atomConfig = `
"*":
  core:
    projectHome: "${this.projectRoot}"
  editor:
    fileAssociations:
      "*.context7": "source.json"
      "antigravity_config.json": "source.json"
`;

        if (!fs.existsSync(atomPath)) {
            fs.mkdirSync(atomPath, { recursive: true });
        }

        fs.writeFileSync(configPath, atomConfig);
    }

    // Start MCP Servers for Context7
    startMCPServers() {
        log('🚀 Starting MCP Servers...', 'cyan');

        const servers = [
            {
                name: 'Yalıhan Bekçi',
                script: path.join(this.projectRoot, 'mcp-servers/yalihan-bekci-mcp.js'),
                port: 4001,
            },
            {
                name: 'Context7 Validator',
                script: path.join(this.projectRoot, 'mcp-servers/context7-validator-mcp.js'),
                port: 4002,
            },
        ];

        for (const server of servers) {
            if (fs.existsSync(server.script)) {
                try {
                    spawn('node', [server.script], {
                        detached: true,
                        stdio: 'ignore',
                    });
                    log(`  ✅ ${server.name} started on port ${server.port}`, 'green');
                } catch (error) {
                    log(`  ❌ Failed to start ${server.name}: ${error.message}`, 'red');
                }
            } else {
                log(`  ⚠️  ${server.name} script not found: ${server.script}`, 'yellow');
            }
        }
    }

    // Setup Git Hooks for Context7
    setupGitHooks() {
        log('🪝 Setting up Git hooks...', 'cyan');

        const hooksDir = path.join(this.projectRoot, '.git/hooks');
        if (!fs.existsSync(hooksDir)) {
            log('  ⚠️  .git/hooks directory not found', 'yellow');
            return;
        }

        // Pre-commit hook for Context7 validation
        const preCommitHook = `#!/bin/sh
# Context7 Pre-commit Hook
echo "🔍 Running Context7 validation..."
php artisan context7:validate-migration --staged
if [ $? -ne 0 ]; then
    echo "❌ Context7 validation failed. Commit blocked."
    exit 1
fi
echo "✅ Context7 validation passed."
`;

        const preCommitPath = path.join(hooksDir, 'pre-commit');
        fs.writeFileSync(preCommitPath, preCommitHook);
        fs.chmodSync(preCommitPath, '755');

        log('  ✅ Git hooks configured', 'green');
    }

    // Main execution
    run() {
        log('🌟 Antigravity Universal IDE System Starting...', 'magenta');
        log('='.repeat(50), 'magenta');

        // Detect all IDEs
        this.detectIDEs();

        // Detect active IDE
        this.detectActiveIDE();

        // Configure all detected IDEs
        log('\n🔧 Configuring detected IDEs...', 'cyan');
        for (const ide of this.detectedIDEs) {
            this.configureForIDE(ide.name);
        }

        // Start MCP Servers
        log('\n🚀 Starting Context7 ecosystem...', 'cyan');
        this.startMCPServers();

        // Setup Git Hooks
        this.setupGitHooks();

        // Summary
        log('\n📊 Summary:', 'bright');
        log(`  Detected IDEs: ${this.detectedIDEs.length}`, 'white');
        log(`  Active IDE: ${this.activeIDE || 'None'}`, 'white');
        log(
            `  Context7 Status: ${this.config.context7Configuration?.enabled ? 'Enabled' : 'Disabled'}`,
            'white'
        );

        log('\n✨ Antigravity system ready!', 'green');
        log('All IDEs are now Context7 compatible.', 'green');
    }
}

// CLI Interface
function main() {
    const args = process.argv.slice(2);
    const detector = new AntigravityDetector();

    if (args.includes('--help') || args.includes('-h')) {
        log('Antigravity Universal IDE Detection & Configuration System', 'bright');
        log('\nUsage:', 'white');
        log(
            '  node antigravity.mjs              # Full system detection and configuration',
            'cyan'
        );
        log('  node antigravity.mjs --detect     # Only detect IDEs', 'cyan');
        log('  node antigravity.mjs --active     # Only detect active IDE', 'cyan');
        log('  node antigravity.mjs --config     # Only configure IDEs', 'cyan');
        log('  node antigravity.mjs --mcp        # Only start MCP servers', 'cyan');
        log('  node antigravity.mjs --hooks      # Only setup Git hooks', 'cyan');
        return;
    }

    if (args.includes('--detect')) {
        detector.detectIDEs();
        return;
    }

    if (args.includes('--active')) {
        detector.detectActiveIDE();
        return;
    }

    if (args.includes('--config')) {
        detector.detectIDEs();
        for (const ide of detector.detectedIDEs) {
            detector.configureForIDE(ide.name);
        }
        return;
    }

    if (args.includes('--mcp')) {
        detector.startMCPServers();
        return;
    }

    if (args.includes('--hooks')) {
        detector.setupGitHooks();
        return;
    }

    // Default: Run full system
    detector.run();
}

main();
