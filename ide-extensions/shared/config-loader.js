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
