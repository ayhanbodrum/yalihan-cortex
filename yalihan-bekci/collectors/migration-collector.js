/**
 * TestSprite MCP - Migration Test Toplayıcısı
 *
 * Bu modül, Laravel migration dosyalarını analiz eder ve
 * Context7 kurallarına uygunluğunu kontrol eder.
 */

const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

class MigrationCollector {
    constructor() {
        this.modulesPath = path.join(process.cwd(), 'app/Modules');
        this.databasePath = path.join(process.cwd(), 'database/migrations');
    }

    /**
     * Tüm migration testlerini çalıştırır
     * @param {Object} rules Test kuralları
     * @returns {Object} Test sonuçları
     */
    async runTests(rules) {
        console.log('Migration testleri başlatılıyor...');

        const migrations = this.findAllMigrations();
        const results = {
            total: migrations.length,
            passed: 0,
            errors: [],
            warnings: [],
            details: [],
        };

        for (const migration of migrations) {
            const testResult = await this.testMigration(migration, rules);
            results.details.push(testResult);

            if (testResult.status === 'passed') {
                results.passed++;
            } else if (testResult.status === 'error') {
                results.errors.push(testResult);
            } else if (testResult.status === 'warning') {
                results.warnings.push(testResult);
            }
        }

        return results;
    }

    /**
     * Tüm migration dosyalarını bulur
     * @returns {Array} Migration dosyaları listesi
     */
    findAllMigrations() {
        const migrations = [];

        // Ana migrations dizinindeki dosyaları bul
        if (fs.existsSync(this.databasePath)) {
            const files = fs.readdirSync(this.databasePath);
            files.forEach((file) => {
                if (file.endsWith('.php')) {
                    migrations.push({
                        path: path.join(this.databasePath, file),
                        name: file,
                        module: null,
                    });
                }
            });
        }

        // Modül migrations dizinlerindeki dosyaları bul
        if (fs.existsSync(this.modulesPath)) {
            const modules = fs.readdirSync(this.modulesPath);
            modules.forEach((module) => {
                const moduleMigrationsPath = path.join(
                    this.modulesPath,
                    module,
                    'Database/Migrations'
                );
                if (fs.existsSync(moduleMigrationsPath)) {
                    const files = fs.readdirSync(moduleMigrationsPath);
                    files.forEach((file) => {
                        if (file.endsWith('.php')) {
                            migrations.push({
                                path: path.join(moduleMigrationsPath, file),
                                name: file,
                                module: module,
                            });
                        }
                    });
                }
            });
        }

        return migrations;
    }

    /**
     * Bir migration dosyasını test eder
     * @param {Object} migration Migration dosyası bilgileri
     * @param {Object} rules Test kuralları
     * @returns {Object} Test sonucu
     */
    async testMigration(migration, rules) {
        const result = {
            file: migration.name,
            module: migration.module,
            path: migration.path,
            status: 'passed',
            errors: [],
            warnings: [],
        };

        // Dosya içeriğini oku
        const content = fs.readFileSync(migration.path, 'utf8');

        // Modül yapısını kontrol et
        if (rules.enforceModuleStructure && migration.module) {
            const moduleNameInFile = this.extractModuleNameFromContent(content);
            if (moduleNameInFile && moduleNameInFile !== migration.module) {
                result.errors.push(
                    `Migration dosyası ${migration.module} modülünde ama içerikte ${moduleNameInFile} modülü kullanılmış`
                );
                result.status = 'error';
            }
        }

        // Sözdizimini kontrol et
        if (rules.checkSyntax) {
            const syntaxErrors = this.checkSyntax(migration.path);
            if (syntaxErrors.length > 0) {
                result.errors.push(...syntaxErrors);
                result.status = 'error';
            }
        }

        // Semantic versiyonlamayı doğrula
        if (rules.validateSemanticVersioning) {
            const versionErrors = this.validateVersion(migration.name);
            if (versionErrors.length > 0) {
                result.errors.push(...versionErrors);
                result.status = 'error';
            }
        }

        return result;
    }

    /**
     * Migration içeriğinden modül adını çıkarır
     * @param {string} content Migration dosyası içeriği
     * @returns {string|null} Modül adı
     */
    extractModuleNameFromContent(content) {
        const namespaceMatch = content.match(/namespace\s+App\\Modules\\([a-zA-Z0-9_]+)/);
        if (namespaceMatch && namespaceMatch[1]) {
            return namespaceMatch[1];
        }
        return null;
    }

    /**
     * PHP sözdizimi kontrolü yapar
     * @param {string} filePath Dosya yolu
     * @returns {Array} Sözdizimi hataları
     */
    checkSyntax(filePath) {
        try {
            execSync(`php -l "${filePath}"`, { stdio: 'pipe' });
            return [];
        } catch (error) {
            return [`Sözdizimi hatası: ${error.message}`];
        }
    }

    /**
     * Migration dosya adının semantic versiyonlama kurallarına uygunluğunu kontrol eder
     * @param {string} fileName Dosya adı
     * @returns {Array} Versiyon hataları
     */
    validateVersion(fileName) {
        const errors = [];

        // YYYY_MM_DD_HHMMSS formatını kontrol et
        const versionMatch = fileName.match(/^(\d{4}_\d{2}_\d{2}_\d{6})_/);
        if (!versionMatch) {
            errors.push(
                `Migration dosya adı '${fileName}' YYYY_MM_DD_HHMMSS formatına uygun değil`
            );
        }

        return errors;
    }
}

module.exports = new MigrationCollector();
