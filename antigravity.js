#!/usr/bin/env node

/**
 * Antigravity Universal IDE Detection & Configuration
 * Automatically detects and configures Context7 environment for any IDE
 */

const fs = require('fs');
const path = require('path');
const { exec, spawn } = require('child_process');
const os = require('os');

class AntigravitySystem {
    constructor() {
        this.projectRoot = process.cwd();
        this.configFile = path.join(this.projectRoot, 'antigravity_config.json');
        this.config = this.loadConfig();
        this.detectedIDEs = [];
        this.activeIDE = null;
    }

    loadConfig() {
        try {
            return JSON.parse(fs.readFileSync(this.configFile, 'utf8'));
        } catch (error) {
            console.log('⚠️  Antigravity config not found. Using defaults.');
            return this.getDefaultConfig();
        }
    }

    getDefaultConfig() {
        return {
            name: 'Context7 Universal Environment',
            universalIDESupport: {
                supportedIDEs: ['vscode', 'cursor', 'windsurf', 'warp'],
                defaultIDE: 'vscode',
            },
            commands: {},
            mcpServers: {},
        };
    }

    async detectIDEs() {
        console.log('🔍 Detecting available IDEs...');

        const ideDetectors = {
            vscode: () => this.checkCommand('code'),
            cursor: () => this.checkCommand('cursor'),
            windsurf: () =>
                this.checkCommand('windsurf') || this.checkPath('/Applications/Windsurf.app'),
            warp: () => this.checkCommand('warp') || this.checkPath('/Applications/Warp.app'),
            sublime: () => this.checkCommand('subl'),
            vim: () => this.checkCommand('vim'),
            emacs: () => this.checkCommand('emacs'),
        };

        for (const [ide, detector] of Object.entries(ideDetectors)) {
            try {
                if (await detector()) {
                    this.detectedIDEs.push(ide);
                    console.log(`✅ Found ${ide.toUpperCase()}`);
                }
            } catch (error) {
                // IDE not found, continue
            }
        }

        if (this.detectedIDEs.length === 0) {
            console.log('❌ No supported IDEs detected.');
            return false;
        }

        console.log(`🎯 Detected IDEs: ${this.detectedIDEs.join(', ')}`);
        return true;
    }

    async checkCommand(command) {
        return new Promise((resolve) => {
            exec(`which ${command}`, (error) => {
                resolve(!error);
            });
        });
    }

    checkPath(appPath) {
        try {
            return fs.existsSync(appPath);
        } catch {
            return false;
        }
    }

    detectActiveIDE() {
        // Check environment variables
        const ideEnvs = {
            VSCODE_PID: 'vscode',
            CURSOR_USER_DATA_DIR: 'cursor',
            WINDSURF_DEV: 'windsurf',
            WARP_IS_LOCAL_SHELL_SESSION: 'warp',
        };

        for (const [envVar, ide] of Object.entries(ideEnvs)) {
            if (process.env[envVar]) {
                this.activeIDE = ide;
                console.log(`🎯 Active IDE detected: ${ide.toUpperCase()}`);
                return ide;
            }
        }

        // Check running processes
        return new Promise((resolve) => {
            const platform = os.platform();
            let command;

            if (platform === 'darwin') {
                command = 'ps -ax';
            } else if (platform === 'linux') {
                command = 'ps aux';
            } else {
                command = 'tasklist';
            }

            exec(command, (error, stdout) => {
                if (error) {
                    resolve(this.detectedIDEs[0] || 'vscode');
                    return;
                }

                const processes = stdout.toLowerCase();

                if (processes.includes('cursor')) {
                    this.activeIDE = 'cursor';
                } else if (processes.includes('windsurf')) {
                    this.activeIDE = 'windsurf';
                } else if (processes.includes('code')) {
                    this.activeIDE = 'vscode';
                } else if (processes.includes('warp')) {
                    this.activeIDE = 'warp';
                } else {
                    this.activeIDE = this.detectedIDEs[0] || 'vscode';
                }

                console.log(`🎯 Active IDE: ${this.activeIDE.toUpperCase()}`);
                resolve(this.activeIDE);
            });
        });
    }

    async configureForIDE(ide) {
        console.log(`⚙️  Configuring for ${ide.toUpperCase()}...`);

        const ideConfig = this.config.ideSpecificConfigurations?.[ide] || {};

        switch (ide) {
            case 'vscode':
                await this.configureVSCode(ideConfig);
                break;
            case 'cursor':
                await this.configureCursor(ideConfig);
                break;
            case 'windsurf':
                await this.configureWindsurf(ideConfig);
                break;
            case 'warp':
                await this.configureWarp(ideConfig);
                break;
            default:
                await this.configureGeneric(ide, ideConfig);
        }
    }

    async configureVSCode(config) {
        // Configure VS Code settings
        const settingsPath = this.getVSCodeSettingsPath();
        const workspaceSettings = {
            'emmet.includeLanguages': {
                blade: 'html',
            },
            'php.validate.enable': true,
            'editor.formatOnSave': true,
            'tailwindCSS.includeLanguages': {
                blade: 'html',
            },
            'context7.enabled': true,
            'context7.strictMode': true,
            ...config.settings,
        };

        await this.writeSettings(settingsPath, workspaceSettings);

        // Configure tasks
        await this.configureVSCodeTasks(config.tasks || []);

        console.log('✅ VS Code configured');
    }

    async configureCursor(config) {
        // Cursor-specific configuration
        const cursorConfig = {
            aiIntegration: true,
            context7Rules: true,
            customPrompts: {
                codeReview:
                    'Review this code following Context7 standards and Laravel best practices',
                optimize: 'Optimize for performance following Context7 compliance',
                refactor: 'Refactor following Context7 naming conventions',
            },
            ...config,
        };

        // Write cursor-specific config
        const configPath = path.join(this.projectRoot, '.cursor', 'config.json');
        await this.ensureDir(path.dirname(configPath));
        fs.writeFileSync(configPath, JSON.stringify(cursorConfig, null, 2));

        console.log('✅ Cursor configured');
    }

    async configureWindsurf(config) {
        // Windsurf configuration
        const windsurfConfig = {
            aiCapabilities: {
                codeGeneration: true,
                realTimeAnalysis: true,
                context7Compliance: true,
            },
            customCommands: [
                {
                    name: 'Context7 Review',
                    command: 'php artisan ai:code-review --file=${activeFile}',
                    shortcut: 'Ctrl+Shift+R',
                },
                {
                    name: 'Generate Development Ideas',
                    command: 'php artisan ideas:generate --interactive',
                    shortcut: 'Ctrl+Shift+I',
                },
            ],
            ...config,
        };

        const configPath = path.join(this.projectRoot, '.windsurf', 'config.json');
        await this.ensureDir(path.dirname(configPath));
        fs.writeFileSync(configPath, JSON.stringify(windsurfConfig, null, 2));

        console.log('✅ Windsurf configured');
    }

    async configureWarp(config) {
        // Warp terminal configuration
        const warpConfig = {
            terminalIntegration: {
                aiCommands: true,
                autoComplete: 'context7-aware',
                aliases: {
                    cr: 'php artisan ai:code-review',
                    ideas: 'php artisan ideas:generate',
                    velocity: 'php artisan analytics:velocity-report',
                    learn: 'php artisan bekci:learn',
                    c7: 'php artisan context7:validate-migration',
                },
            },
            ...config,
        };

        // Create warp config
        const configPath = path.join(this.projectRoot, '.warp', 'config.json');
        await this.ensureDir(path.dirname(configPath));
        fs.writeFileSync(configPath, JSON.stringify(warpConfig, null, 2));

        // Add aliases to shell
        await this.addShellAliases(warpConfig.terminalIntegration.aliases);

        console.log('✅ Warp configured');
    }

    async configureGeneric(ide, config) {
        console.log(`⚙️  Setting up generic configuration for ${ide}`);

        // Create generic IDE config
        const genericConfig = {
            ide: ide,
            context7: {
                enabled: true,
                strictMode: true,
                autoValidation: true,
            },
            commands: this.config.commands,
            shortcuts: this.config.shortcuts,
            ...config,
        };

        const configPath = path.join(this.projectRoot, `.${ide}`, 'context7-config.json');
        await this.ensureDir(path.dirname(configPath));
        fs.writeFileSync(configPath, JSON.stringify(genericConfig, null, 2));

        console.log(`✅ ${ide} configured`);
    }

    async startMCPServers() {
        console.log('🚀 Starting MCP servers...');

        const servers = this.config.mcpServers || {};

        for (const [name, serverConfig] of Object.entries(servers)) {
            if (serverConfig.autoStart) {
                try {
                    await this.startMCPServer(name, serverConfig);
                    console.log(`✅ ${name} MCP server started`);
                } catch (error) {
                    console.log(`❌ Failed to start ${name}: ${error.message}`);
                }
            }
        }
    }

    async startMCPServer(name, config) {
        return new Promise((resolve, reject) => {
            const serverProcess = spawn('node', [config.command], {
                cwd: this.projectRoot,
                stdio: ['ignore', 'pipe', 'pipe'],
                env: { ...process.env, PORT: config.port },
            });

            serverProcess.stdout.on('data', (data) => {
                console.log(`[${name}] ${data.toString().trim()}`);
            });

            serverProcess.stderr.on('data', (data) => {
                console.log(`[${name}] ERROR: ${data.toString().trim()}`);
            });

            // Give server time to start
            setTimeout(() => {
                if (!serverProcess.killed) {
                    resolve(serverProcess);
                } else {
                    reject(new Error('Server failed to start'));
                }
            }, 2000);
        });
    }

    async setupGitHooks() {
        console.log('🔗 Setting up Git hooks...');

        const hooks = this.config.gitIntegration?.hooks || {};
        const hooksDir = path.join(this.projectRoot, '.git', 'hooks');

        for (const [hookName, commands] of Object.entries(hooks)) {
            const hookPath = path.join(hooksDir, hookName);
            const hookContent = this.generateHookScript(commands);

            fs.writeFileSync(hookPath, hookContent, { mode: 0o755 });
            console.log(`✅ ${hookName} hook configured`);
        }
    }

    generateHookScript(commands) {
        const commandLines = commands.map((cmd) => `echo "Running: ${cmd}" && ${cmd}`).join('\n');

        return `#!/bin/sh
# Context7 Git Hook - Auto-generated by Antigravity

echo "🔍 Running Context7 checks..."

${commandLines}

echo "✅ Context7 checks completed"
`;
    }

    async addShellAliases(aliases) {
        const shellrcFile = path.join(os.homedir(), '.zshrc');

        if (fs.existsSync(shellrcFile)) {
            const content = fs.readFileSync(shellrcFile, 'utf8');
            let newContent = content;

            for (const [alias, command] of Object.entries(aliases)) {
                const aliasLine = `alias ${alias}="${command}"`;
                if (!content.includes(aliasLine)) {
                    newContent += `\n# Context7 alias\n${aliasLine}\n`;
                }
            }

            if (newContent !== content) {
                fs.writeFileSync(shellrcFile, newContent);
                console.log('📝 Shell aliases added to .zshrc');
            }
        }
    }

    getVSCodeSettingsPath() {
        return path.join(this.projectRoot, '.vscode', 'settings.json');
    }

    async configureVSCodeTasks(tasks) {
        const tasksPath = path.join(this.projectRoot, '.vscode', 'tasks.json');

        const defaultTasks = [
            {
                label: 'AI Code Review - Staged',
                type: 'shell',
                command: 'php',
                args: ['artisan', 'ai:code-review', '--staged', '--format=table'],
                group: 'test',
                problemMatcher: [],
            },
            {
                label: 'Generate Development Ideas',
                type: 'shell',
                command: 'php',
                args: ['artisan', 'ideas:generate', '--interactive'],
                group: 'build',
                problemMatcher: [],
            },
            {
                label: 'Velocity Report',
                type: 'shell',
                command: 'php',
                args: ['artisan', 'analytics:velocity-report', '--format=table'],
                group: 'test',
                problemMatcher: [],
            },
        ];

        const taskConfig = {
            version: '2.0.0',
            tasks: [...defaultTasks, ...tasks],
        };

        await this.ensureDir(path.dirname(tasksPath));
        fs.writeFileSync(tasksPath, JSON.stringify(taskConfig, null, 2));
    }

    async writeSettings(settingsPath, settings) {
        let existingSettings = {};

        if (fs.existsSync(settingsPath)) {
            try {
                existingSettings = JSON.parse(fs.readFileSync(settingsPath, 'utf8'));
            } catch (error) {
                console.log('⚠️  Could not parse existing settings, using defaults');
            }
        }

        const mergedSettings = { ...existingSettings, ...settings };

        await this.ensureDir(path.dirname(settingsPath));
        fs.writeFileSync(settingsPath, JSON.stringify(mergedSettings, null, 2));
    }

    async ensureDir(dir) {
        if (!fs.existsSync(dir)) {
            fs.mkdirSync(dir, { recursive: true });
        }
    }

    async showStatus() {
        console.log('\n📊 CONTEXT7 ENVIRONMENT STATUS');
        console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        console.log(`🎯 Active IDE: ${this.activeIDE?.toUpperCase() || 'Not detected'}`);
        console.log(`🔧 Detected IDEs: ${this.detectedIDEs.join(', ') || 'None'}`);
        console.log(`📁 Project: ${this.config.name}`);

        if (this.config.mcpServers) {
            console.log('\n🚀 MCP Servers:');
            Object.keys(this.config.mcpServers).forEach((server) => {
                console.log(`   • ${server}`);
            });
        }

        if (this.config.commands) {
            console.log('\n⚡ Available Commands:');
            Object.entries(this.config.shortcuts || {})
                .slice(0, 5)
                .forEach(([key, cmd]) => {
                    console.log(`   • ${key}: ${cmd}`);
                });
        }

        console.log('\n🌐 Universal IDE Features: ✅ Active');
        console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }

    async run() {
        console.log('🌌 ANTIGRAVITY - Universal Context7 Environment');
        console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // Detect IDEs
        const hasIDEs = await this.detectIDEs();
        if (!hasIDEs) {
            console.log(
                '❌ No supported IDEs found. Please install VS Code, Cursor, Windsurf, or Warp.'
            );
            return;
        }

        // Detect active IDE
        await this.detectActiveIDE();

        // Configure for all detected IDEs
        for (const ide of this.detectedIDEs) {
            await this.configureForIDE(ide);
        }

        // Start MCP servers
        await this.startMCPServers();

        // Setup Git hooks
        await this.setupGitHooks();

        // Show final status
        await this.showStatus();

        console.log(
            '\n✨ Antigravity configuration complete! Universal AI coding environment is ready.'
        );
        console.log('💡 Try: F1 (Code Review), F2 (Ideas), F3 (Analytics), F4 (Context7)');
    }
}

// Run Antigravity if called directly
if (require.main === module) {
    const system = new AntigravitySystem();
    system.run().catch(console.error);
}

module.exports = AntigravitySystem;
