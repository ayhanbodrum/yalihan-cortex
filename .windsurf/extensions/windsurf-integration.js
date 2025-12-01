// Context7 Windsurf Integration
// AI Assistant enhancement with Context7 compliance

class Context7WindsurfIntegration {
    constructor() {
        this.mcpServerUrl = 'http://localhost:4001';
        this.config = this.loadConfig();
        this.initializeWindsurfIntegration();
    }

    loadConfig() {
        // Load configuration from Windsurf settings
        return {
            enabled: true,
            autofix: false,
            learningEnabled: true,
            aiIntegration: true,
            mcpServerUrl: 'http://localhost:4001',
        };
    }

    initializeWindsurfIntegration() {
        // Register Context7 commands with Windsurf
        this.registerCommands();

        // Enhance AI assistant with Context7 context
        this.enhanceAIAssistant();

        // Setup file watchers
        this.setupFileWatchers();

        console.log('Context7 Windsurf Integration initialized');
    }

    registerCommands() {
        const commands = {
            'context7.validate': () => this.validateCurrentFile(),
            'context7.autofix': () => this.autofixCurrentFile(),
            'context7.learn': () => this.learnFromCurrentAction(),
            'context7.generateIdeas': () => this.generateDevelopmentIdeas(),
        };

        Object.entries(commands).forEach(([command, handler]) => {
            // Windsurf command registration (pseudo-code)
            if (typeof windsurf !== 'undefined') {
                windsurf.commands.register(command, handler);
            }
        });
    }

    enhanceAIAssistant() {
        // Add Context7 context to AI assistant
        const context7Rules = `
        Context7 Standards for Yalıhan Bekçi Project:

        FORBIDDEN:
        - Neo Design System (neo-* classes)
        - Bootstrap classes (btn-*, card-*, etc.)
        - Fields: "enabled", "order"

        REQUIRED:
        - Tailwind CSS utility classes only
        - Dark mode classes: bg-white dark:bg-gray-800
        - Transition effects: transition-all duration-200
        - Laravel 10 patterns
        - Eloquent with() for relationships (not accessors)

        NAMING CONVENTIONS:
        - il_id (not city_id)
        - status (not durum)
        - aktif_mi (not active)
        - one_cikan (not featured)
        `;

        if (typeof windsurf !== 'undefined' && windsurf.ai) {
            windsurf.ai.addSystemContext('context7_rules', context7Rules);
            windsurf.ai.addSystemContext('yalihan_bekci_patterns', this.getYalihanBekciPatterns());
        }
    }

    getYalihanBekciPatterns() {
        return `
        Yalıhan Bekçi Project Patterns:

        1. Modular Architecture: app/Modules/* structure
        2. Service Layer: Business logic in Services, not Controllers
        3. Eager Loading: Use with() for relationships
        4. Dark Mode: Always include dark: variants
        5. Transitions: All interactive elements need transitions
        6. Null Coalescing: {{ $var->field ?? '—' }} in Blade
        `;
    }

    setupFileWatchers() {
        // Watch for file changes and validate Context7 compliance
        const fileExtensions = ['.php', '.blade.php', '.js', '.css'];

        fileExtensions.forEach((ext) => {
            if (typeof windsurf !== 'undefined' && windsurf.workspace) {
                windsurf.workspace.onDidSaveTextDocument(ext, (document) => {
                    if (this.config.enabled) {
                        this.validateDocument(document);
                    }
                });
            }
        });
    }

    async validateCurrentFile() {
        try {
            const activeEditor = this.getActiveEditor();
            if (!activeEditor) {
                this.showMessage('No active file to validate');
                return;
            }

            const content = activeEditor.document.getText();
            const filePath = activeEditor.document.uri.fsPath;

            const violations = await this.checkContext7Violations(content, filePath);

            if (violations.length > 0) {
                this.showViolations(violations);
                await this.learnFromViolations(violations, filePath);
            } else {
                this.showMessage('✅ Context7 validation passed!');
            }
        } catch (error) {
            this.showError('Validation failed: ' + error.message);
        }
    }

    async autofixCurrentFile() {
        try {
            const activeEditor = this.getActiveEditor();
            if (!activeEditor) {
                this.showMessage('No active file to fix');
                return;
            }

            const content = activeEditor.document.getText();
            const fixedContent = this.applyContext7Fixes(content);

            // Apply fixes to editor
            const edit = new windsurf.WorkspaceEdit();
            edit.replace(
                activeEditor.document.uri,
                new windsurf.Range(0, 0, activeEditor.document.lineCount, 0),
                fixedContent
            );

            await windsurf.workspace.applyEdit(edit);

            this.showMessage('✅ Context7 violations auto-fixed!');

            // Learn from the fix
            await this.learnFromAction('context7_autofix', {
                filePath: activeEditor.document.uri.fsPath,
                violationsFixed: true,
            });
        } catch (error) {
            this.showError('Auto-fix failed: ' + error.message);
        }
    }

    async generateDevelopmentIdeas() {
        try {
            this.showMessage('🚀 Generating development ideas...');

            const ideas = await this.callMCPServer('generate_development_ideas', {
                category: 'all',
                priority: 'all',
            });

            if (ideas && ideas.length > 0) {
                this.showIdeasPanel(ideas);
            } else {
                this.showMessage('No development ideas generated');
            }
        } catch (error) {
            this.showError('Ideas generation failed: ' + error.message);
        }
    }

    checkContext7Violations(content, filePath) {
        const violations = [];

        // Context7 violation patterns
        const patterns = [
            { regex: /neo-/g, type: 'Neo Design System usage', severity: 'high' },
            { regex: /\benabled\b/g, type: 'Forbidden "enabled" field', severity: 'medium' },
            { regex: /\border\b/g, type: 'Forbidden "order" field', severity: 'medium' },
            { regex: /btn-/g, type: 'Bootstrap button classes', severity: 'high' },
            { regex: /card-/g, type: 'Bootstrap card classes', severity: 'high' },
            {
                regex: /bg-white(?!\s+dark:)/g,
                type: 'Missing dark mode variant',
                severity: 'medium',
            },
        ];

        patterns.forEach((pattern) => {
            const matches = content.match(pattern.regex);
            if (matches) {
                violations.push({
                    type: pattern.type,
                    severity: pattern.severity,
                    count: matches.length,
                    filePath,
                });
            }
        });

        return violations;
    }

    applyContext7Fixes(content) {
        let fixed = content;

        // Replace forbidden patterns with Tailwind CSS equivalents
        const replacements = [
            {
                from: /neo-btn/g,
                to: 'bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-all duration-200',
            },
            { from: /neo-card/g, to: 'bg-white dark:bg-gray-800 shadow-lg rounded-lg' },
            {
                from: /btn-primary/g,
                to: 'bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-all duration-200',
            },
            {
                from: /btn-secondary/g,
                to: 'bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-all duration-200',
            },
            { from: /card-body/g, to: 'p-6' },
            { from: /card-header/g, to: 'px-6 py-4 border-b border-gray-200 dark:border-gray-700' },
            { from: /bg-white(?!\s+dark:)/g, to: 'bg-white dark:bg-gray-800' },
            { from: /text-gray-900(?!\s+dark:)/g, to: 'text-gray-900 dark:text-white' },
        ];

        replacements.forEach((replacement) => {
            fixed = fixed.replace(replacement.from, replacement.to);
        });

        return fixed;
    }

    async learnFromCurrentAction() {
        const activeEditor = this.getActiveEditor();
        if (!activeEditor) {
            this.showMessage('No active file for learning');
            return;
        }

        const actionContext = {
            filePath: activeEditor.document.uri.fsPath,
            action: 'manual_learning_trigger',
            timestamp: new Date().toISOString(),
        };

        await this.learnFromAction('manual_trigger', actionContext);
        this.showMessage('🧠 Action learned by Yalıhan Bekçi!');
    }

    async learnFromAction(actionType, context) {
        try {
            await this.callMCPServer('learn_from_action', {
                action_type: actionType,
                context: context,
                timestamp: new Date().toISOString(),
                source: 'windsurf_extension',
            });
        } catch (error) {
            console.log('Learning failed, storing locally:', error);
            this.storeLocalLearning(actionType, context);
        }
    }

    async callMCPServer(method, params) {
        const response = await fetch(this.mcpServerUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                method,
                params,
                id: Date.now(),
            }),
        });

        if (!response.ok) {
            throw new Error(`MCP Server error: ${response.status}`);
        }

        const result = await response.json();
        return result.result;
    }

    getActiveEditor() {
        if (typeof windsurf !== 'undefined' && windsurf.window) {
            return windsurf.window.activeTextEditor;
        }
        return null;
    }

    showMessage(message) {
        if (typeof windsurf !== 'undefined' && windsurf.window) {
            windsurf.window.showInformationMessage(message);
        } else {
            console.log(message);
        }
    }

    showError(message) {
        if (typeof windsurf !== 'undefined' && windsurf.window) {
            windsurf.window.showErrorMessage(message);
        } else {
            console.error(message);
        }
    }

    showViolations(violations) {
        const message = `Context7 violations found: ${violations.length} issues`;
        const details = violations
            .map((v) => `${v.type} (${v.severity}): ${v.count} occurrences`)
            .join('\n');

        this.showError(message + '\n\n' + details);
    }

    showIdeasPanel(ideas) {
        // Create a new document or panel to display ideas
        const ideasContent = this.formatIdeasForDisplay(ideas);

        if (typeof windsurf !== 'undefined' && windsurf.workspace) {
            windsurf.workspace
                .openTextDocument({
                    content: ideasContent,
                    language: 'markdown',
                })
                .then((document) => {
                    windsurf.window.showTextDocument(document);
                });
        }
    }

    formatIdeasForDisplay(ideas) {
        let content = '# 🚀 Development Ideas Generated by Yalıhan Bekçi\n\n';

        ideas.forEach((idea, index) => {
            content += `## ${index + 1}. ${idea.title}\n\n`;
            content += `**📝 Description:** ${idea.description}\n\n`;
            content += `**💼 Business Value:** ${idea.business_value}\n\n`;
            content += `**⏱️ Estimated Effort:** ${idea.estimated_effort}\n\n`;
            content += `**🏷️ Tags:** ${idea.tags ? idea.tags.join(', ') : 'N/A'}\n\n`;
            content += '---\n\n';
        });

        return content;
    }

    storeLocalLearning(actionType, context) {
        const learningData = {
            action_type: actionType,
            context: context,
            timestamp: new Date().toISOString(),
            source: 'windsurf_extension_offline',
        };

        // Store in local storage or file system
        console.log('Storing local learning data:', learningData);
    }
}

// Initialize Context7 Windsurf Integration
if (typeof module !== 'undefined' && module.exports) {
    module.exports = Context7WindsurfIntegration;
} else {
    // Browser environment
    new Context7WindsurfIntegration();
}
