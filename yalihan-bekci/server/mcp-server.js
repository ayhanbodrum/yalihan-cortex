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
                    description: 'Context7 kurallarına göre kod doğrular',
                    inputSchema: {
                        type: 'object',
                        properties: {
                            code: {
                                type: 'string',
                                description: 'Doğrulanacak kod',
                            },
                            filePath: {
                                type: 'string',
                                description: 'Dosya yolu',
                            },
                        },
                        required: ['code'],
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
        const rules = context7Rules.loadRules();
        const violations = context7Rules.checkCode(args.code, args.filePath || 'unknown');

        return {
            content: [
                {
                    type: 'text',
                    text: JSON.stringify(
                        {
                            success: true,
                            violations: violations,
                            count: violations.length,
                            passed: violations.length === 0,
                        },
                        null,
                        2
                    ),
                },
            ],
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
        // Auto-learn başlat
        if (process.env.AUTO_LEARN === 'true') {
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

        console.error('🛡️ Yalıhan Bekçi MCP hazır!');
    }
}

const server = new YalihanBekciMCP();
server.run().catch(console.error);
