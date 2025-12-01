#!/usr/bin/env node

/**
 * Yalıhan Bekçi MCP Server v2.0 - Optimized
 * MD Cleanup & AI Prompt Management Features
 * Context7 Compliance: 100%
 */

const { Server } = require('@modelcontextprotocol/sdk/server/index.js');
const { StdioServerTransport } = require('@modelcontextprotocol/sdk/server/stdio.js');
const {
    CallToolRequestSchema,
    ListToolsRequestSchema,
    ListResourcesRequestSchema,
    ReadResourceRequestSchema,
} = require('@modelcontextprotocol/sdk/types.js');

const context7Rules = require('../knowledge/context7-rule-loader');
const systemMemory = require('../knowledge/system-memory');
const knowledgeBase = require('../knowledge/knowledge-base');
const errorLearner = require('../knowledge/error-learner');
const fs = require('fs');
const path = require('path');

class YalihanBekciMCP {
    constructor() {
        this.server = new Server(
            {
                name: 'yalihan-bekci',
                version: '1.0.0',
            },
            {
                capabilities: {
                    tools: {},
                    resources: {},
                    prompts: {},
                },
            }
        );

        this.setupHandlers();
        this.setupErrorHandling();
    }

    setupHandlers() {
        // List available tools
        this.server.setRequestHandler(ListToolsRequestSchema, async () => ({
            tools: [
                {
                    name: 'context7_validate',
                    description:
                        'Context7 kurallarına göre kod doğrular - Geliştirilmiş: Akıllı pattern matching, otomatik öğrenme, iyileştirme önerileri',
                    inputSchema: {
                        type: 'object',
                        properties: {
                            code: {
                                type: 'string',
                                description: 'Doğrulanacak kod',
                            },
                            filePath: {
                                type: 'string',
                                description: 'Dosya yolu (context-aware detection için önemli)',
                            },
                            autoFix: {
                                type: 'boolean',
                                description: 'Otomatik düzeltme önerileri göster',
                                default: true,
                            },
                        },
                        required: ['code'],
                    },
                },
                {
                    name: 'context7_auto_fix',
                    description: 'Context7 ihlallerini otomatik düzeltir',
                    inputSchema: {
                        type: 'object',
                        properties: {
                            code: {
                                type: 'string',
                                description: 'Düzeltilecek kod',
                            },
                            filePath: {
                                type: 'string',
                                description: 'Dosya yolu',
                            },
                        },
                        required: ['code', 'filePath'],
                    },
                },
                {
                    name: 'context7_learn_pattern',
                    description: 'Yeni Context7 pattern öğrenir ve kaydeder',
                    inputSchema: {
                        type: 'object',
                        properties: {
                            pattern: {
                                type: 'string',
                                description: 'Yasaklı pattern',
                            },
                            reason: {
                                type: 'string',
                                description: 'Yasak olma nedeni',
                            },
                            suggestion: {
                                type: 'string',
                                description: 'Önerilen alternatif',
                            },
                        },
                        required: ['pattern', 'reason', 'suggestion'],
                    },
                },
                {
                    name: 'get_context7_rules',
                    description: 'Öğrenilmiş Context7 kurallarını getirir',
                    inputSchema: {
                        type: 'object',
                        properties: {},
                    },
                },
                {
                    name: 'check_pattern',
                    description: "Sık görülen hata pattern'lerini kontrol eder",
                    inputSchema: {
                        type: 'object',
                        properties: {
                            query: {
                                type: 'string',
                                description: 'Aranacak pattern',
                            },
                        },
                    },
                },
                {
                    name: 'get_system_structure',
                    description: 'Sistem yapısını getirir (model, controller sayıları)',
                    inputSchema: {
                        type: 'object',
                        properties: {},
                    },
                },
                {
                    name: 'get_learned_errors',
                    description: 'Test raporlarından öğrenilmiş hataları gösterir',
                    inputSchema: {
                        type: 'object',
                        properties: {
                            limit: {
                                type: 'number',
                                description: 'Kaç hata gösterilsin (default: 10)',
                            },
                        },
                    },
                },
                {
                    name: 'md_duplicate_detector',
                    description: 'MD dosyalarında duplicate kontrolü yapar',
                    inputSchema: {
                        type: 'object',
                        properties: {
                            path: {
                                type: 'string',
                                description: 'Kontrol edilecek dizin (default: current)',
                            },
                            excludePaths: {
                                type: 'array',
                                items: { type: 'string' },
                                description:
                                    'Hariç tutulacak dizinler (vendor, node_modules, archive)',
                            },
                        },
                    },
                },
                {
                    name: 'knowledge_consolidator',
                    description: 'Knowledge base dosyalarını birleştirir ve optimize eder',
                    inputSchema: {
                        type: 'object',
                        properties: {
                            category: {
                                type: 'string',
                                description:
                                    'Birleştirilecek kategori (ai, context7, stable-create)',
                            },
                            dryRun: {
                                type: 'boolean',
                                description: 'Sadece rapor oluştur, değişiklik yapma',
                            },
                        },
                    },
                },
                {
                    name: 'ai_prompt_manager',
                    description: 'AI prompt dosyalarını yönetir ve eksikleri tespit eder',
                    inputSchema: {
                        type: 'object',
                        properties: {
                            action: {
                                type: 'string',
                                enum: ['list', 'missing', 'create', 'validate'],
                                description: 'Yapılacak işlem',
                            },
                            promptType: {
                                type: 'string',
                                description: 'Prompt tipi (arsa, daire, villa, etc.)',
                            },
                        },
                        required: ['action'],
                    },
                },
            ],
        }));

        // Handle tool calls
        this.server.setRequestHandler(CallToolRequestSchema, async (request) => {
            const { name, arguments: args } = request.params;

            switch (name) {
                case 'context7_auto_fix':
                    return await this.autoFixCode(args);

                case 'context7_learn_pattern':
                    return await this.learnPattern(args);
                case 'context7_validate':
                    return await this.validateCode(args);

                case 'get_context7_rules':
                    return await this.getContext7Rules();

                case 'check_pattern':
                    return await this.checkPattern(args);

                case 'get_system_structure':
                    return await this.getSystemStructure();

                case 'get_learned_errors':
                    return await this.getLearnedErrors(args);

                case 'md_duplicate_detector':
                    return await this.detectMdDuplicates(args);

                case 'knowledge_consolidator':
                    return await this.consolidateKnowledge(args);

                case 'ai_prompt_manager':
                    return await this.manageAiPrompts(args);

                default:
                    throw new Error(`Unknown tool: ${name}`);
            }
        });

        // List available resources
        this.server.setRequestHandler(ListResourcesRequestSchema, async () => ({
            resources: [
                {
                    uri: 'context7://rules/forbidden',
                    name: 'Context7 Yasaklı Kurallar',
                    description: "Öğrenilmiş yasaklı pattern'ler",
                    mimeType: 'application/json',
                },
                {
                    uri: 'context7://rules/required',
                    name: 'Context7 Zorunlu Kurallar',
                    description: "Öğrenilmiş zorunlu pattern'ler",
                    mimeType: 'application/json',
                },
                {
                    uri: 'context7://system/structure',
                    name: 'Sistem Yapısı',
                    description: 'Model, Controller, View sayıları',
                    mimeType: 'application/json',
                },
                {
                    uri: 'context7://patterns/common',
                    name: 'Sık Hatalar',
                    description: "En sık görülen hata pattern'leri",
                    mimeType: 'application/json',
                },
                {
                    uri: 'context7://cleanup/md-duplicates',
                    name: 'MD Duplicate Analysis',
                    description: 'Markdown dosya duplicate analizi',
                    mimeType: 'application/json',
                },
                {
                    uri: 'context7://ai/prompt-status',
                    name: 'AI Prompt Status',
                    description: 'AI prompt dosyalarının durumu',
                    mimeType: 'application/json',
                },
                {
                    uri: 'context7://knowledge/consolidation',
                    name: 'Knowledge Consolidation',
                    description: 'Knowledge base birleştirme fırsatları',
                    mimeType: 'application/json',
                },
            ],
        }));

        // Read resource
        this.server.setRequestHandler(ReadResourceRequestSchema, async (request) => {
            const { uri } = request.params;

            switch (uri) {
                case 'context7://rules/forbidden':
                    return await this.getForbiddenRules();

                case 'context7://rules/required':
                    return await this.getRequiredRules();

                case 'context7://system/structure':
                    return await this.getSystemStructure();

                case 'context7://patterns/common':
                    return await this.getCommonPatterns();

                case 'context7://cleanup/md-duplicates':
                    return await this.detectMdDuplicates({});

                case 'context7://ai/prompt-status':
                    return await this.manageAiPrompts({ action: 'list' });

                case 'context7://knowledge/consolidation':
                    return await this.consolidateKnowledge({
                        category: 'all',
                        dryRun: true,
                    });

                default:
                    throw new Error(`Unknown resource: ${uri}`);
            }
        });
    }

    async validateCode(args) {
        try {
            const rules = context7Rules.loadRules();
            const violations = context7Rules.checkCode(args.code, args.filePath || 'unknown');

            // Super uyarı modu kontrolü
            const verbose = process.env.VERBOSE === 'true' || process.env.VERBOSE === '1';

            // Geliştirilmiş violation detayları ekle
            const enhancedViolations = this.enhanceViolations(
                violations,
                args.code,
                args.filePath,
                verbose
            );

            // İyileştirme önerileri ekle
            const suggestions = this.generateImprovementSuggestions(
                enhancedViolations,
                args.code,
                args.filePath
            );

            // Violation kategorilerine ayır
            const categorized = this.categorizeViolations(enhancedViolations);

            // Öğrenme: Yeni pattern'ler varsa kaydet
            if (enhancedViolations.length > 0) {
                this.learnFromViolations(enhancedViolations, args.filePath);
            }

            return {
                content: [
                    {
                        type: 'text',
                        text: JSON.stringify(
                            {
                                success: true,
                                violations: enhancedViolations,
                                count: enhancedViolations.length,
                                passed: enhancedViolations.length === 0,
                                categorized: categorized,
                                suggestions: suggestions,
                                autoFixable: enhancedViolations.filter((v) => v.autoFix).length,
                                summary: this.generateSummary(enhancedViolations, categorized),
                                filePath: args.filePath || 'unknown',
                                fileUrl: args.filePath ? this.generateFileUrl(args.filePath) : null,
                            },
                            null,
                            2
                        ),
                    },
                ],
            };
        } catch (error) {
            return {
                content: [
                    {
                        type: 'text',
                        text: JSON.stringify({
                            success: false,
                            error: error.message,
                            violations: [],
                            count: 0,
                        }),
                    },
                ],
            };
        }
    }

    /**
     * Violation'ları geliştir - dosya yolu, satır numarası, context ekle
     * @param {boolean} verbose - Super uyarı modu (detaylı bilgi)
     */
    enhanceViolations(violations, code, filePath, verbose = false) {
        if (!code || !filePath) {
            return violations;
        }

        const lines = code.split('\n');

        return violations.map((violation) => {
            const enhanced = { ...violation };

            // Satır numarasını bul
            if (!enhanced.line && violation.code) {
                const lineIndex = lines.findIndex(
                    (line) => line.includes(violation.code.trim()) || line.includes(violation.rule)
                );
                if (lineIndex !== -1) {
                    enhanced.line = lineIndex + 1;
                }
            }

            // Dosya yolu ekle
            enhanced.filePath = filePath;
            enhanced.fileName = filePath.split('/').pop();
            enhanced.relativePath = filePath
                .replace(process.env.PROJECT_ROOT || '', '')
                .replace(/^\//, '');

            // Context ekle (satır öncesi ve sonrası)
            if (enhanced.line) {
                const lineNum = enhanced.line - 1;
                enhanced.context = {
                    before: lineNum > 0 ? lines[lineNum - 1].trim() : null,
                    current: lines[lineNum]?.trim() || violation.code,
                    after: lineNum < lines.length - 1 ? lines[lineNum + 1].trim() : null,
                };
            }

            // Dosya açma URL'i
            enhanced.fileUrl = this.generateFileUrl(filePath, enhanced.line);

            // Daha detaylı mesaj (super mode'da daha fazla detay)
            enhanced.detailedMessage = this.generateDetailedMessage(enhanced, verbose);

            // Super mode'da ekstra bilgiler
            if (verbose) {
                enhanced.superMode = true;
                enhanced.timestamp = new Date().toISOString();
                enhanced.fileSize = code.length;
                enhanced.lineCount = lines.length;
                enhanced.violationDensity = violations.length / lines.length;
            }

            return enhanced;
        });
    }

    /**
     * Dosya açma URL'i oluştur
     */
    generateFileUrl(filePath, lineNumber = null) {
        const relativePath = filePath
            .replace(process.env.PROJECT_ROOT || '', '')
            .replace(/^\//, '');
        let url = `file://${relativePath}`;

        if (lineNumber) {
            url += `#L${lineNumber}`;
        }

        return url;
    }

    /**
     * Detaylı hata mesajı oluştur
     * @param {boolean} verbose - Super uyarı modu
     */
    generateDetailedMessage(violation, verbose = false) {
        const parts = [];

        parts.push(`❌ Context7 İhlali: ${violation.rule || 'Bilinmeyen kural'}`);

        if (violation.fileName) {
            parts.push(`📁 Dosya: ${violation.fileName}`);
        }

        if (violation.relativePath && verbose) {
            parts.push(`📂 Yol: ${violation.relativePath}`);
        }

        if (violation.line) {
            parts.push(`📍 Satır: ${violation.line}`);
        }

        if (violation.column && verbose) {
            parts.push(`📍 Kolon: ${violation.column}`);
        }

        if (violation.suggestion) {
            parts.push(`💡 Öneri: ${violation.suggestion}`);
        }

        if (violation.severity) {
            const severityEmoji = {
                critical: '🔴',
                high: '🟠',
                medium: '🟡',
                low: '🟢',
            };
            parts.push(`${severityEmoji[violation.severity] || '⚪'} Önem: ${violation.severity}`);
        }

        if (violation.context?.current) {
            const codePreview = verbose
                ? violation.context.current
                : violation.context.current.substring(0, 100);
            parts.push(`📝 Kod: ${codePreview}`);
        }

        // Super mode'da ekstra context
        if (verbose && violation.context) {
            if (violation.context.before) {
                parts.push(`⬆️ Önceki satır: ${violation.context.before.substring(0, 60)}`);
            }
            if (violation.context.after) {
                parts.push(`⬇️ Sonraki satır: ${violation.context.after.substring(0, 60)}`);
            }
        }

        if (verbose && violation.fileUrl) {
            parts.push(`🔗 Dosya: ${violation.fileUrl}`);
        }

        return parts.join('\n');
    }

    /**
     * Violation'ları kategorize et
     */
    categorizeViolations(violations) {
        const categories = {
            critical: [],
            high: [],
            medium: [],
            low: [],
        };

        violations.forEach((v) => {
            const severity = v.severity || 'medium';
            if (categories[severity]) {
                categories[severity].push(v);
            } else {
                categories.medium.push(v);
            }
        });

        return categories;
    }

    /**
     * İyileştirme önerileri oluştur - Geliştirilmiş detaylı öneriler
     */
    generateImprovementSuggestions(violations, code, filePath) {
        const suggestions = [];

        if (violations.length === 0) {
            return [
                {
                    type: 'success',
                    message: '✅ Kod Context7 standartlarına uygun!',
                    icon: '✅',
                },
            ];
        }

        // En sık görülen hatalar
        const commonIssues = this.findCommonIssues(violations);
        commonIssues.forEach((issue) => {
            suggestions.push({
                type: 'warning',
                icon: '⚠️',
                message: `${issue.rule} kullanımı tespit edildi (${issue.count} kez)`,
                suggestion: issue.suggestion,
                autoFix: issue.autoFix,
                examples: issue.examples || [],
            });
        });

        // Kritik ihlaller için özel uyarı
        const criticalViolations = violations.filter((v) => v.severity === 'critical');
        if (criticalViolations.length > 0) {
            suggestions.push({
                type: 'error',
                icon: '🔴',
                message: `${criticalViolations.length} kritik ihlal bulundu - Öncelikli düzeltme gerekli!`,
                files: [...new Set(criticalViolations.map((v) => v.fileName || v.filePath))],
            });
        }

        // Dosya tipine özel öneriler
        const fileType = filePath ? filePath.split('.').pop() : 'unknown';
        if (fileType === 'blade.php') {
            suggestions.push({
                type: 'info',
                icon: '💡',
                message: "Blade dosyası: Tailwind CSS + dark mode variant'ları kontrol edin",
                checklist: [
                    'bg-white dark:bg-gray-800 kullanıldı mı?',
                    'text-gray-900 dark:text-white kullanıldı mı?',
                    'transition-all duration-200 eklendi mi?',
                ],
            });
        }

        // Otomatik düzeltilebilir ihlaller
        const autoFixable = violations.filter((v) => v.autoFix);
        if (autoFixable.length > 0) {
            suggestions.push({
                type: 'info',
                icon: '🔧',
                message: `${autoFixable.length} ihlal otomatik düzeltilebilir`,
                action: "context7_auto_fix tool'unu kullanabilirsiniz",
            });
        }

        return suggestions;
    }

    /**
     * Yaygın sorunları bul - Geliştirilmiş örneklerle
     */
    findCommonIssues(violations) {
        const issueMap = new Map();

        violations.forEach((v) => {
            const key = v.rule;
            if (!issueMap.has(key)) {
                issueMap.set(key, {
                    rule: key,
                    count: 0,
                    suggestion: v.suggestion,
                    autoFix: v.autoFix ? true : false,
                    examples: [],
                    files: new Set(),
                });
            }
            const issue = issueMap.get(key);
            issue.count++;

            // Örnek ekle (max 3)
            if (issue.examples.length < 3 && v.code) {
                issue.examples.push({
                    code: v.code.substring(0, 80),
                    line: v.line,
                    file: v.fileName || v.filePath,
                });
            }

            // Dosya ekle
            if (v.fileName) {
                issue.files.add(v.fileName);
            }
        });

        return Array.from(issueMap.values())
            .map((issue) => ({
                ...issue,
                files: Array.from(issue.files),
            }))
            .sort((a, b) => b.count - a.count);
    }

    /**
     * Violation'lardan öğren
     */
    learnFromViolations(violations, filePath) {
        violations.forEach((v) => {
            // Yeni pattern öğren
            if (v.autoFix) {
                errorLearner.learnPattern({
                    pattern: v.rule,
                    context: v.context,
                    fix: v.autoFix,
                    file: filePath,
                });
            }
        });
    }

    /**
     * Özet oluştur - Geliştirilmiş detaylı özet
     */
    generateSummary(violations, categorized) {
        if (violations.length === 0) {
            return {
                status: 'passed',
                message: '✅ Tüm kontroller geçti!',
                details: {
                    total: 0,
                    bySeverity: {},
                    byFile: {},
                    autoFixable: 0,
                },
            };
        }

        const criticalCount = categorized.critical.length;
        const highCount = categorized.high.length;
        const mediumCount = categorized.medium.length;
        const lowCount = categorized.low.length;
        const autoFixableCount = violations.filter((v) => v.autoFix).length;

        // Dosya bazında grupla
        const byFile = {};
        violations.forEach((v) => {
            const file = v.fileName || v.filePath || 'unknown';
            if (!byFile[file]) {
                byFile[file] = {
                    count: 0,
                    critical: 0,
                    high: 0,
                    medium: 0,
                    low: 0,
                    violations: [],
                };
            }
            byFile[file].count++;
            byFile[file][v.severity || 'medium']++;
            byFile[file].violations.push({
                rule: v.rule,
                line: v.line,
                severity: v.severity,
            });
        });

        // En çok ihlal olan dosyalar
        const topFiles = Object.entries(byFile)
            .sort((a, b) => b[1].count - a[1].count)
            .slice(0, 5)
            .map(([file, data]) => ({
                file,
                count: data.count,
                critical: data.critical,
            }));

        return {
            status: criticalCount > 0 ? 'failed' : highCount > 0 ? 'warning' : 'info',
            message: `${violations.length} ihlal bulundu (${criticalCount} kritik, ${highCount} yüksek, ${mediumCount} orta, ${lowCount} düşük, ${autoFixableCount} otomatik düzeltilebilir)`,
            priority: criticalCount > 0 ? 'high' : highCount > 0 ? 'medium' : 'low',
            details: {
                total: violations.length,
                bySeverity: {
                    critical: criticalCount,
                    high: highCount,
                    medium: mediumCount,
                    low: lowCount,
                },
                byFile: byFile,
                topFiles: topFiles,
                autoFixable: autoFixableCount,
                autoFixRate:
                    violations.length > 0
                        ? Math.round((autoFixableCount / violations.length) * 100)
                        : 0,
            },
        };
    }

    async getContext7Rules() {
        const rules = context7Rules.loadRules();

        return {
            content: [
                {
                    type: 'text',
                    text: JSON.stringify(
                        {
                            forbidden: Object.keys(rules.forbidden),
                            required: Object.keys(rules.required),
                            count: {
                                forbidden: Object.keys(rules.forbidden).length,
                                required: Object.keys(rules.required).length,
                            },
                        },
                        null,
                        2
                    ),
                },
            ],
        };
    }

    /**
     * Otomatik düzeltme yap
     */
    async autoFixCode(args) {
        try {
            const { code, filePath } = args;
            const violations = context7Rules.checkCode(code, filePath || 'unknown');

            let fixedCode = code;
            const fixes = [];

            violations.forEach((v) => {
                if (v.autoFix) {
                    fixedCode = fixedCode.replace(v.code, v.autoFix.fixed);
                    fixes.push({
                        rule: v.rule,
                        line: v.line,
                        original: v.code,
                        fixed: v.autoFix.fixed,
                    });
                }
            });

            return {
                content: [
                    {
                        type: 'text',
                        text: JSON.stringify(
                            {
                                success: true,
                                fixed: fixedCode !== code,
                                fixesApplied: fixes.length,
                                fixes: fixes,
                                fixedCode: fixedCode !== code ? fixedCode : null,
                            },
                            null,
                            2
                        ),
                    },
                ],
            };
        } catch (error) {
            return {
                content: [
                    {
                        type: 'text',
                        text: JSON.stringify({
                            success: false,
                            error: error.message,
                        }),
                    },
                ],
            };
        }
    }

    /**
     * Yeni pattern öğren
     */
    async learnPattern(args) {
        try {
            const { pattern, reason, suggestion } = args;
            const added = context7Rules.addForbiddenPattern(pattern, reason, 'user_input');

            return {
                content: [
                    {
                        type: 'text',
                        text: JSON.stringify(
                            {
                                success: true,
                                pattern: pattern,
                                added: added,
                                message: added
                                    ? `✅ Yeni pattern öğrenildi: ${pattern}`
                                    : `ℹ️ Pattern zaten mevcut: ${pattern}`,
                            },
                            null,
                            2
                        ),
                    },
                ],
            };
        } catch (error) {
            return {
                content: [
                    {
                        type: 'text',
                        text: JSON.stringify({
                            success: false,
                            error: error.message,
                        }),
                    },
                ],
            };
        }
    }

    async checkPattern(args) {
        const results = knowledgeBase.search(args.query || '');

        return {
            content: [
                {
                    type: 'text',
                    text: JSON.stringify(results, null, 2),
                },
            ],
        };
    }

    async getSystemStructure() {
        const status = systemMemory.getSystemStatus();

        return {
            content: [
                {
                    type: 'text',
                    text: JSON.stringify(status.systemStructure, null, 2),
                },
            ],
        };
    }

    async getLearnedErrors(args) {
        const limit = args?.limit || 10;
        const report = errorLearner.generateReport();

        return {
            content: [
                {
                    type: 'text',
                    text: JSON.stringify(
                        {
                            summary: report.summary,
                            most_common: report.most_common.slice(0, limit),
                        },
                        null,
                        2
                    ),
                },
            ],
        };
    }

    async getForbiddenRules() {
        const rules = context7Rules.loadRules();
        return {
            contents: [
                {
                    uri: 'context7://rules/forbidden',
                    mimeType: 'application/json',
                    text: JSON.stringify(rules.forbidden, null, 2),
                },
            ],
        };
    }

    async getRequiredRules() {
        const rules = context7Rules.loadRules();
        return {
            contents: [
                {
                    uri: 'context7://rules/required',
                    mimeType: 'application/json',
                    text: JSON.stringify(rules.required, null, 2),
                },
            ],
        };
    }

    async getCommonPatterns() {
        const patterns = knowledgeBase.getCommonPatterns(10);
        return {
            contents: [
                {
                    uri: 'context7://patterns/common',
                    mimeType: 'application/json',
                    text: JSON.stringify(patterns, null, 2),
                },
            ],
        };
    }

    async learnFromStableCreate() {
        console.error('🔍 stable-create sayfası öğreniliyor...');
        const stableCreatePath = path.join(
            process.env.PROJECT_ROOT,
            'resources/views/admin/ilanlar/stable-create.blade.php'
        );

        if (fs.existsSync(stableCreatePath)) {
            const content = fs.readFileSync(stableCreatePath, 'utf8');
            // Yasaklı pattern'leri tespit et
            const violations = context7Rules.checkCode(content, stableCreatePath);

            // Yeni kuralları öğren
            violations.forEach((v) => {
                context7Rules.addForbiddenPattern(v.pattern, v.reason, 'stable-create');
            });

            console.error(`✅ ${violations.length} yeni kural öğrenildi (stable-create)`);
        }
    }

    async learnFromDocs() {
        console.error('📚 docs/ klasörü öğreniliyor...');
        const docsPath = path.join(process.env.PROJECT_ROOT, 'docs');

        // Hata raporlarını oku
        const reportFiles = [
            'admin/admin-detayli-test-raporu.md',
            'admin/admin-kapsamli-test-raporu.md',
            'context7/reports/context7-violations-log.md',
        ];

        let learnedCount = 0;
        reportFiles.forEach((file) => {
            const filePath = path.join(docsPath, file);
            if (fs.existsSync(filePath)) {
                const content = fs.readFileSync(filePath, 'utf8');

                // Tanımsız değişken hatalarını öğren
                const undefinedVars = content.match(/Tanımsız değişken: \$(\w+)/g);
                if (undefinedVars) {
                    undefinedVars.forEach((match) => {
                        const varName = match.match(/\$(\w+)/)[1];
                        context7Rules.addRequiredPattern(
                            `Değişken kontrolü: $${varName}`,
                            `Controller'da $${varName} tanımlanmalı`,
                            file
                        );
                        learnedCount++;
                    });
                }

                // Context7 ihlallerini öğren
                const violations = content.match(/❌.*→.*/g);
                if (violations) {
                    violations.forEach((v) => {
                        const parts = v.match(/❌\s*(\w+).*→\s*(\w+)/);
                        if (parts && parts[1]) {
                            context7Rules.addForbiddenPattern(
                                parts[1],
                                parts[2] ? `${parts[2]} kullan` : 'Context7 yasak',
                                file
                            );
                            learnedCount++;
                        }
                    });
                }
            }
        });

        console.error(`✅ ${learnedCount} yeni kural öğrenildi (docs/)`);
    }

    // 🧹 MD Duplicate Detector
    async detectMdDuplicates(args) {
        const searchPath = args.path || process.env.PROJECT_ROOT;
        const excludePaths = args.excludePaths || [
            'vendor',
            'node_modules',
            'archive',
            '.context7/backups',
        ];

        const { execSync } = require('child_process');

        try {
            // Exclude paths'leri build et
            const excludeArgs = excludePaths.map((p) => `-not -path "./${p}/*"`).join(' ');

            // MD dosyalarını bul ve basename'lerini say
            const command = `find ${searchPath} -name "*.md" ${excludeArgs} -exec basename {} \\; | sort | uniq -c | sort -nr`;
            const output = execSync(command, { encoding: 'utf8' });

            const duplicates = [];
            const lines = output.trim().split('\n');

            lines.forEach((line) => {
                const match = line.trim().match(/(\d+)\s+(.+)/);
                if (match && parseInt(match[1]) > 1) {
                    duplicates.push({
                        filename: match[2],
                        count: parseInt(match[1]),
                        severity:
                            parseInt(match[1]) > 5
                                ? 'high'
                                : parseInt(match[1]) > 2
                                  ? 'medium'
                                  : 'low',
                    });
                }
            });

            return {
                content: [
                    {
                        type: 'text',
                        text: JSON.stringify(
                            {
                                total_duplicates: duplicates.length,
                                critical_duplicates: duplicates.filter(
                                    (d) => d.severity === 'high'
                                ),
                                duplicates: duplicates.slice(0, 20), // Top 20
                                recommendation:
                                    duplicates.length > 10
                                        ? 'Urgent cleanup needed'
                                        : 'Minor cleanup recommended',
                            },
                            null,
                            2
                        ),
                    },
                ],
            };
        } catch (error) {
            return {
                content: [
                    {
                        type: 'text',
                        text: `Error detecting duplicates: ${error.message}`,
                    },
                ],
            };
        }
    }

    // 🔧 Knowledge Consolidator
    async consolidateKnowledge(args) {
        const category = args.category || 'all';
        const dryRun = args.dryRun !== false;

        const consolidationMap = {
            ai: ['yalihan-bekci/knowledge/ai-*.json', 'docs/ai-training/', 'docs/context7/AI-*.md'],
            context7: ['yalihan-bekci/knowledge/context7-*.json', 'docs/context7/', '.context7/'],
            'stable-create': [
                'yalihan-bekci/knowledge/stable-create-*.json',
                'yalihan-bekci/knowledge/stable-create-*.md',
            ],
        };

        const results = {
            category,
            dry_run: dryRun,
            files_analyzed: 0,
            consolidation_opportunities: [],
            size_savings_potential: '0KB',
        };

        // Kategori bazında analiz yap
        const patterns = consolidationMap[category] || [];

        patterns.forEach((pattern) => {
            try {
                const { execSync } = require('child_process');
                const files = execSync(`find . -path "${pattern}" -type f`, {
                    encoding: 'utf8',
                })
                    .trim()
                    .split('\n')
                    .filter((f) => f);

                results.files_analyzed += files.length;

                if (files.length > 1) {
                    results.consolidation_opportunities.push({
                        pattern,
                        files: files.slice(0, 5), // İlk 5'i göster
                        total_files: files.length,
                        suggested_action: `Merge ${files.length} files into master reference`,
                    });
                }
            } catch (error) {
                // Pattern match etmezse devam et
            }
        });

        return {
            content: [
                {
                    type: 'text',
                    text: JSON.stringify(results, null, 2),
                },
            ],
        };
    }

    // 🤖 AI Prompt Manager
    async manageAiPrompts(args) {
        const action = args.action;
        const promptType = args.promptType;

        const expectedPrompts = [
            'arsa-aciklama-olustur.prompt.md',
            'arsa-baslik-olustur.prompt.md',
            'daire-aciklama-olustur.prompt.md',
            'daire-baslik-olustur.prompt.md',
            'villa-aciklama-olustur.prompt.md',
            'villa-baslik-olustur.prompt.md',
            'yazlik-aciklama-olustur.prompt.md',
            'yazlik-baslik-olustur.prompt.md',
            'isyeri-aciklama-olustur.prompt.md',
            'isyeri-baslik-olustur.prompt.md',
            'kategori-aciklama-olustur.prompt.md',
            'kategori-seo-optimizasyon.prompt.md',
            'kategori-akilli-oneriler.prompt.md',
            'danisman-performans-analizi.prompt.md',
            'danisman-raporu.prompt.md',
            'danisman-oneri-sistemi.prompt.md',
            'talep-eslesme.prompt.md',
            'talep-analizi.prompt.md',
        ];

        const promptsDir = path.join(process.env.PROJECT_ROOT, 'ai/prompts');
        const existingPrompts = [];

        if (fs.existsSync(promptsDir)) {
            fs.readdirSync(promptsDir).forEach((file) => {
                if (file.endsWith('.prompt.md')) {
                    existingPrompts.push(file);
                }
            });
        }

        const results = {
            action,
            total_expected: expectedPrompts.length,
            total_existing: existingPrompts.length,
            missing_prompts: expectedPrompts.filter((p) => !existingPrompts.includes(p)),
            existing_prompts: existingPrompts,
            completion_rate: `${Math.round(
                (existingPrompts.length / expectedPrompts.length) * 100
            )}%`,
        };

        if (action === 'create' && promptType) {
            const promptFile = `${promptType}-aciklama-olustur.prompt.md`;
            if (!existingPrompts.includes(promptFile)) {
                const template = `# ${
                    promptType.charAt(0).toUpperCase() + promptType.slice(1)
                } Açıklama Oluşturucu

**Version:** 1.0.0
**Category:** ${promptType}
**Type:** aciklama
**Priority:** high
**Last Updated:** ${new Date().toISOString().split('T')[0]}

---

## 🎯 **Görev**

${
    promptType.charAt(0).toUpperCase() + promptType.slice(1)
} türü emlak ilanları için SEO uyumlu açıklamalar oluştur.

---

## 📥 **Giriş Parametreleri**

### **Zorunlu Parametreler:**
- **title:** string - İlan başlığı
- **location:** object - Konum bilgileri
- **price:** number - Fiyat bilgisi
- **features:** array - Özellikler listesi

### **Opsiyonel Parametreler:**
- **tone:** string - Ton (seo, kurumsal, hizli_satis, luks)
- **target_length:** number - Hedef karakter sayısı (150-300)

---

## 📤 **Çıktı Formatı**

\`\`\`json
{
  "variants": [
    "SEO optimized description 1",
    "SEO optimized description 2",
    "SEO optimized description 3"
  ],
  "metadata": {
    "character_count": 245,
    "seo_score": 85,
    "readability": "easy"
  }
}
\`\`\`

---

## 🎯 **Context7 Kuralları**

- ✅ Turkish language optimization
- ✅ Real estate terminology
- ✅ Location-based keywords
- ✅ Price point positioning
- ✅ Feature highlighting
`;

                if (!args.dryRun) {
                    fs.mkdirSync(promptsDir, { recursive: true });
                    fs.writeFileSync(path.join(promptsDir, promptFile), template);
                    results.created_file = promptFile;
                }
            }
        }

        return {
            content: [
                {
                    type: 'text',
                    text: JSON.stringify(results, null, 2),
                },
            ],
        };
    }

    setupErrorHandling() {
        this.server.onerror = (error) => {
            console.error('[MCP Error]', error);
        };

        process.on('SIGINT', async () => {
            await this.server.close();
            process.exit(0);
        });
    }

    async run() {
        // Super uyarı modu kontrolü
        const autoLearn = process.env.AUTO_LEARN === 'true';
        const verbose = process.env.VERBOSE === 'true' || process.env.VERBOSE === '1';
        const superMode = autoLearn && verbose;

        if (superMode) {
            console.error('🚀 SUPER UYARI MODU AKTİF!');
            console.error('   ✅ AUTO_LEARN: Açık');
            console.error('   ✅ VERBOSE: Açık');
            console.error('   📊 Detaylı uyarılar ve öğrenme aktif');
        } else if (autoLearn) {
            console.error('🧠 AUTO_LEARN modu aktif (Normal öğrenme)');
        } else if (verbose) {
            console.error('📊 VERBOSE modu aktif (Detaylı uyarılar)');
        }

        // Auto-learn başlat
        if (autoLearn) {
            console.error('🧠 Context7 kuralları öğreniliyor...');
            context7Rules.loadAllRules();
            systemMemory.learnSystemStructure();

            // stable-create ve docs'tan öğren
            await this.learnFromStableCreate();
            await this.learnFromDocs();

            // Hata raporlarından öğren
            errorLearner.scanAllReports();
        }

        const transport = new StdioServerTransport();
        await this.server.connect(transport);

        if (superMode) {
            console.error('🛡️ Yalıhan Bekçi MCP hazır! (SUPER MODE)');
        } else {
            console.error('🛡️ Yalıhan Bekçi MCP hazır!');
        }
    }
}

const server = new YalihanBekciMCP();
server.run().catch(console.error);
