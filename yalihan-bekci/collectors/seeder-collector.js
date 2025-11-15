/**
 * TestSprite MCP - Seeder Test Toplayıcısı
 *
 * Bu modül, Laravel seeder dosyalarını analiz eder ve
 * Context7 kurallarına uygunluğunu kontrol eder.
 */

const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

class SeederCollector {
    constructor() {
        this.modulesPath = path.join(process.cwd(), 'app/Modules');
        this.databasePath = path.join(process.cwd(), 'database/seeders');
    }

    /**
     * Tüm seeder testlerini çalıştırır
     * @param {Object} rules Test kuralları
     * @returns {Object} Test sonuçları
     */
    async runTests(rules) {
        console.log('Seeder testleri başlatılıyor...');

        const seeders = this.findAllSeeders();
        const results = {
            total: seeders.length,
            passed: 0,
            errors: [],
            warnings: [],
            details: [],
        };

        for (const seeder of seeders) {
            const testResult = await this.testSeeder(seeder, rules);
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
     * Tüm seeder dosyalarını bulur
     * @returns {Array} Seeder dosyaları listesi
     */
    findAllSeeders() {
        const seeders = [];

        // Ana seeders dizinindeki dosyaları bul
        if (fs.existsSync(this.databasePath)) {
            const files = fs.readdirSync(this.databasePath);
            files.forEach((file) => {
                if (file.endsWith('.php')) {
                    seeders.push({
                        path: path.join(this.databasePath, file),
                        name: file,
                        module: null,
                    });
                }
            });
        }

        // Modül seeders dizinlerindeki dosyaları bul
        if (fs.existsSync(this.modulesPath)) {
            const modules = fs.readdirSync(this.modulesPath);
            modules.forEach((module) => {
                const moduleSeederPath = path.join(this.modulesPath, module, 'Database/Seeders');
                if (fs.existsSync(moduleSeederPath)) {
                    const files = fs.readdirSync(moduleSeederPath);
                    files.forEach((file) => {
                        if (file.endsWith('.php')) {
                            seeders.push({
                                path: path.join(moduleSeederPath, file),
                                name: file,
                                module: module,
                            });
                        }
                    });
                }
            });
        }

        return seeders;
    }

    /**
     * Bir seeder dosyasını test eder
     * @param {Object} seeder Seeder dosyası bilgileri
     * @param {Object} rules Test kuralları
     * @returns {Object} Test sonucu
     */
    async testSeeder(seeder, rules) {
        const result = {
            file: seeder.name,
            module: seeder.module,
            path: seeder.path,
            status: 'passed',
            errors: [],
            warnings: [],
        };

        // Dosya içeriğini oku
        const content = fs.readFileSync(seeder.path, 'utf8');

        // Modül yapısını kontrol et
        if (rules.enforceModuleStructure && seeder.module) {
            const moduleNameInFile = this.extractModuleNameFromContent(content);
            if (moduleNameInFile && moduleNameInFile !== seeder.module) {
                result.errors.push(
                    `Seeder dosyası ${seeder.module} modülünde ama içerikte ${moduleNameInFile} modülü kullanılmış`
                );
                result.status = 'error';
            }
        }

        // Sözdizimini kontrol et
        if (rules.checkSyntax) {
            const syntaxErrors = this.checkSyntax(seeder.path);
            if (syntaxErrors.length > 0) {
                result.errors.push(...syntaxErrors);
                result.status = 'error';
            }
        }

        // Bağımlılıkları doğrula
        if (rules.validateDependencies) {
            const dependencyErrors = this.validateDependencies(content);
            if (dependencyErrors.length > 0) {
                result.errors.push(...dependencyErrors);
                result.status = 'error';
            }
        }

        // afterLastBatch metodunu kontrol et
        if (content.indexOf('afterLastBatch') === -1) {
            result.warnings.push(
                'Seeder dosyasında afterLastBatch metodu bulunamadı. Migration sonrası otomatik çalıştırma için bu metod gereklidir.'
            );
            if (result.status !== 'error') {
                result.status = 'warning';
            }
        }

        return result;
    }

    /**
     * Seeder içeriğinden modül adını çıkarır
     * @param {string} content Seeder dosyası içeriği
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
     * Seeder bağımlılıklarını kontrol eder
     * @param {string} content Seeder dosyası içeriği
     * @returns {Array} Bağımlılık hataları
     */
    validateDependencies(content) {
        const errors = [];

        // Diğer seeder'lara bağımlılıkları kontrol et
        const dependsOnMatch = content.match(/\$this->call\(\s*([a-zA-Z0-9_\\]+)::class\s*\)/g);
        if (dependsOnMatch) {
            for (const match of dependsOnMatch) {
                const seederClass = match.match(
                    /\$this->call\(\s*([a-zA-Z0-9_\\]+)::class\s*\)/
                )[1];

                // Bağımlı seeder'ın varlığını kontrol et
                try {
                    const seederExists = this.checkSeederExists(seederClass);
                    if (!seederExists) {
                        errors.push(`Bağımlı seeder bulunamadı: ${seederClass}`);
                    }
                } catch (error) {
                    errors.push(`Bağımlılık kontrolü sırasında hata: ${error.message}`);
                }
            }
        }

        return errors;
    }

    /**
     * Seeder sınıfının varlığını kontrol eder
     * @param {string} seederClass Seeder sınıf adı
     * @returns {boolean} Seeder varsa true, yoksa false
     */
    checkSeederExists(seederClass) {
        // Tam yolu olmayan seeder sınıfları için
        if (!seederClass.includes('\\')) {
            return fs.existsSync(path.join(this.databasePath, `${seederClass}.php`));
        }

        // Modül içindeki seeder'lar için
        const moduleMatch = seederClass.match(
            /App\\Modules\\([a-zA-Z0-9_]+)\\Database\\Seeders\\([a-zA-Z0-9_]+)/
        );
        if (moduleMatch) {
            const [, moduleName, className] = moduleMatch;
            return fs.existsSync(
                path.join(this.modulesPath, moduleName, 'Database/Seeders', `${className}.php`)
            );
        }

        // Diğer namespace'ler için
        return false;
    }
}

module.exports = new SeederCollector();
