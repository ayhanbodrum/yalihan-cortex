/**
 * TestSprite MCP - Ana Sunucu
 *
 * Bu dosya, TestSprite MCP'nin ana sunucu bileşenidir.
 * Migration ve Seeder testlerini koordine eder, sonuçları bilgi tabanında saklar
 * ve raporlama motorunu kullanarak sonuçları görselleştirir.
 */

const express = require('express');
const bodyParser = require('body-parser');
const fs = require('fs');
const path = require('path');

// TestSprite MCP bileşenleri
const migrationCollector = require('../collectors/migration-collector');
const seederCollector = require('../collectors/seeder-collector');
const knowledgeBase = require('../knowledge/knowledge-base');
const reportingEngine = require('../reporting/reporting-engine');
const context7RuleLoader = require('../knowledge/context7-rule-loader');
const systemMemory = require('../knowledge/system-memory');
const techUpdater = require('../knowledge/tech-updater');

class TestSpriteMCP {
    constructor() {
        this.app = express();
        this.port = process.env.BEKCI_PORT || process.env.GUARDIAN_PORT || 3334;
        this.configPath = path.join(__dirname, '../../.context7/testsprite.config.json');
        this.config = this.loadConfig();

        this.setupServer();
    }

    loadConfig() {
        try {
            if (fs.existsSync(this.configPath)) {
                return JSON.parse(fs.readFileSync(this.configPath, 'utf8'));
            }
        } catch (error) {
            console.error('Yapılandırma dosyası yüklenirken hata oluştu:', error);
        }

        // Varsayılan yapılandırma
        return {
            autoCorrect: false,
            reportPath: path.join(__dirname, '../../reports/testsprite'),
            notifyOnError: true,
            testInterval: 3600000, // 1 saat
            rules: {
                migrations: {
                    enforceModuleStructure: true,
                    checkSyntax: true,
                    validateSemanticVersioning: true,
                },
                seeders: {
                    enforceModuleStructure: true,
                    checkSyntax: true,
                    validateDependencies: true,
                },
                codeStandards: {
                    enforcePSR12: true,
                    checkVueCompositionAPI: true,
                    validateBladeStrictMode: true,
                },
                security: {
                    checkEnvFiles: true,
                    validateApiKeys: true,
                    enforceEncryption: true,
                },
            },
        };
    }

    setupServer() {
        this.app.use(bodyParser.json());

        // Ana endpoint
        this.app.get('/', (req, res) => {
            res.json({
                name: 'Yalıhan Bekçi',
                description: 'Context7 Otomatik Öğrenme ve Koruma Sistemi',
                emoji: '🛡️',
                version: '1.0.0',
                status: 'running',
                features: [
                    'Context7 Kural Öğrenme',
                    'Sistem Yapısı Analizi',
                    'Pattern Tanıma',
                    'Otomatik Doğrulama',
                ],
            });
        });

        // Test başlatma endpointi
        this.app.post('/run-tests', async (req, res) => {
            try {
                const testResults = await this.runTests(req.body);
                res.json(testResults);
            } catch (error) {
                res.status(500).json({ error: error.message });
            }
        });

        // Bilgi tabanı sorgulama endpointi
        this.app.get('/knowledge', (req, res) => {
            const query = req.query.q || '';
            const results = knowledgeBase.search(query);
            res.json(results);
        });

        // Rapor oluşturma endpointi
        this.app.get('/reports', (req, res) => {
            const reportType = req.query.type || 'summary';
            const report = reportingEngine.generateReport(reportType);
            res.json(report);
        });

        // Context7 kurallarını getir (MCP için)
        this.app.get('/context7/rules', (req, res) => {
            const rules = context7RuleLoader.loadRules();
            res.json({
                success: true,
                rules: {
                    forbidden: Object.keys(rules.forbidden).length,
                    required: Object.keys(rules.required).length,
                    forbidden_list: Object.keys(rules.forbidden),
                    required_list: Object.keys(rules.required),
                },
            });
        });

        // Kod validate et (MCP için)
        this.app.post('/context7/validate', (req, res) => {
            const { code, filePath } = req.body;
            const violations = context7RuleLoader.checkCode(code, filePath);
            res.json({
                success: true,
                violations: violations,
                count: violations.length,
                passed: violations.length === 0,
            });
        });

        // Pattern'leri getir (MCP için)
        this.app.get('/patterns/common', (req, res) => {
            const patterns = knowledgeBase.getCommonPatterns(10);
            res.json({
                success: true,
                patterns: patterns,
            });
        });

        // Sistem durumunu getir (MCP için)
        this.app.get('/system/status', (req, res) => {
            const status = systemMemory.getSystemStatus();
            res.json({
                success: true,
                status: status,
            });
        });

        // Son işlemi getir (MCP için)
        this.app.get('/system/last-operation', (req, res) => {
            const lastOp = systemMemory.getLastOperation();
            res.json({
                success: true,
                lastOperation: lastOp,
            });
        });

        // Sistem yapısını öğren (MCP için)
        this.app.post('/system/learn', (req, res) => {
            const structure = systemMemory.learnSystemStructure();
            res.json({
                success: true,
                structure: structure,
            });
        });

        // Teknoloji güncellemelerini kontrol et (MCP için)
        this.app.get('/tech/updates', async (req, res) => {
            try {
                const updates = await techUpdater.checkForUpdates();
                const report = techUpdater.generateReport();
                res.json({
                    success: true,
                    ...report,
                });
            } catch (error) {
                res.status(500).json({
                    success: false,
                    error: error.message,
                });
            }
        });

        // Kullanılan teknolojileri göster (MCP için)
        this.app.get('/tech/stack', async (req, res) => {
            await techUpdater.detectTechnologies();
            res.json({
                success: true,
                technologies: techUpdater.technologies,
            });
        });
    }

    async runTests(options = {}) {
        console.log('TestSprite MCP testleri başlatılıyor...');

        const results = {
            migrations: await migrationCollector.runTests(this.config.rules.migrations),
            seeders: await seederCollector.runTests(this.config.rules.seeders),
            timestamp: new Date().toISOString(),
        };

        // Sonuçları bilgi tabanına kaydet
        knowledgeBase.storeResults(results);

        // Hata varsa bildirim gönder
        if (
            this.config.notifyOnError &&
            (results.migrations.errors.length > 0 || results.seeders.errors.length > 0)
        ) {
            this.sendNotification(results);
        }

        // Rapor oluştur
        const report = reportingEngine.generateReport('detailed', results);

        return {
            summary: {
                totalTests: results.migrations.total + results.seeders.total,
                passedTests: results.migrations.passed + results.seeders.passed,
                failedTests: results.migrations.errors.length + results.seeders.errors.length,
                successRate:
                    (
                        ((results.migrations.passed + results.seeders.passed) /
                            (results.migrations.total + results.seeders.total)) *
                        100
                    ).toFixed(2) + '%',
            },
            reportUrl: `/reports?id=${report.id}`,
        };
    }

    sendNotification(results) {
        // Bildirim gönderme işlemi burada gerçekleştirilecek
        console.log('Hata bildirimi gönderiliyor...');
        // Örneğin: Email, Slack, Discord vb.
    }

    start() {
        this.app.listen(this.port, () => {
            console.log('╔══════════════════════════════════════════╗');
            console.log('║   🛡️  YALİHAN BEKÇİ BAŞLATILDI        ║');
            console.log('╚══════════════════════════════════════════╝');
            console.log(`📍 Port: ${this.port}`);
            console.log('🧠 Otomatik Öğrenme: AKTİF');
            console.log('🎯 Context7 Koruması: AKTİF');
            console.log('⚡ Kodunuzu korumaya hazır!');
            console.log('');
        });

        // Context7 kurallarını otomatik öğren
        if (process.env.AUTO_LEARN === 'true') {
            console.log('🧠 Context7 kuralları öğreniliyor...');
            context7RuleLoader.autoLearn();
            systemMemory.autoLearn();
            techUpdater.autoCheck();
        }

        // Periyodik test çalıştırma
        if (this.config.testInterval > 0) {
            setInterval(() => {
                this.runTests().catch((error) => {
                    console.error('Periyodik test çalıştırılırken hata oluştu:', error);
                });
            }, this.config.testInterval);
        }
    }
}

// Sunucuyu başlat
if (require.main === module) {
    const server = new TestSpriteMCP();
    server.start();
}

module.exports = TestSpriteMCP;
