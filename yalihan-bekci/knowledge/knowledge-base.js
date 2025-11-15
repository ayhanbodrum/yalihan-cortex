/**
 * TestSprite MCP - Bilgi Tabanı
 *
 * Bu modül, test sonuçlarını saklar ve sorgular.
 * Geçmiş test sonuçlarını analiz ederek hata desenlerini öğrenir.
 */

const fs = require('fs');
const path = require('path');

class KnowledgeBase {
    constructor() {
        this.dbPath = path.join(__dirname, '../../reports/testsprite/knowledge');
        this.ensureDirectoryExists(this.dbPath);

        this.resultsPath = path.join(this.dbPath, 'results.json');
        this.patternsPath = path.join(this.dbPath, 'patterns.json');

        this.results = this.loadResults();
        this.patterns = this.loadPatterns();
    }

    /**
     * Dizinin varlığını kontrol eder, yoksa oluşturur
     * @param {string} dirPath Dizin yolu
     */
    ensureDirectoryExists(dirPath) {
        if (!fs.existsSync(dirPath)) {
            fs.mkdirSync(dirPath, { recursive: true });
        }
    }

    /**
     * Test sonuçlarını yükler
     * @returns {Array} Test sonuçları
     */
    loadResults() {
        try {
            if (fs.existsSync(this.resultsPath)) {
                return JSON.parse(fs.readFileSync(this.resultsPath, 'utf8'));
            }
        } catch (error) {
            console.error('Test sonuçları yüklenirken hata oluştu:', error);
        }
        return [];
    }

    /**
     * Hata desenlerini yükler
     * @returns {Object} Hata desenleri
     */
    loadPatterns() {
        try {
            if (fs.existsSync(this.patternsPath)) {
                return JSON.parse(fs.readFileSync(this.patternsPath, 'utf8'));
            }
        } catch (error) {
            console.error('Hata desenleri yüklenirken hata oluştu:', error);
        }
        return {
            migrations: {},
            seeders: {},
            lastUpdated: null,
        };
    }

    /**
     * Test sonuçlarını kaydeder
     * @param {Object} results Test sonuçları
     */
    storeResults(results) {
        // Sonuçları diziye ekle
        this.results.push({
            ...results,
            id: `test-${Date.now()}`,
            timestamp: new Date().toISOString(),
        });

        // Maksimum 100 sonuç sakla
        if (this.results.length > 100) {
            this.results = this.results.slice(-100);
        }

        // Sonuçları dosyaya kaydet
        fs.writeFileSync(this.resultsPath, JSON.stringify(this.results, null, 2));

        // Hata desenlerini güncelle
        this.updatePatterns(results);
    }

    /**
     * Hata desenlerini günceller
     * @param {Object} results Test sonuçları
     */
    updatePatterns(results) {
        // Migration hatalarını işle
        if (results.migrations && results.migrations.errors) {
            results.migrations.errors.forEach((error) => {
                const key = `${error.file}:${error.errors[0]}`;
                if (!this.patterns.migrations[key]) {
                    this.patterns.migrations[key] = {
                        file: error.file,
                        module: error.module,
                        error: error.errors[0],
                        occurrences: 0,
                        firstSeen: new Date().toISOString(),
                        lastSeen: null,
                    };
                }

                this.patterns.migrations[key].occurrences++;
                this.patterns.migrations[key].lastSeen = new Date().toISOString();
            });
        }

        // Seeder hatalarını işle
        if (results.seeders && results.seeders.errors) {
            results.seeders.errors.forEach((error) => {
                const key = `${error.file}:${error.errors[0]}`;
                if (!this.patterns.seeders[key]) {
                    this.patterns.seeders[key] = {
                        file: error.file,
                        module: error.module,
                        error: error.errors[0],
                        occurrences: 0,
                        firstSeen: new Date().toISOString(),
                        lastSeen: null,
                    };
                }

                this.patterns.seeders[key].occurrences++;
                this.patterns.seeders[key].lastSeen = new Date().toISOString();
            });
        }

        this.patterns.lastUpdated = new Date().toISOString();

        // Desenleri dosyaya kaydet
        fs.writeFileSync(this.patternsPath, JSON.stringify(this.patterns, null, 2));
    }

    /**
     * Bilgi tabanında arama yapar
     * @param {string} query Arama sorgusu
     * @returns {Object} Arama sonuçları
     */
    search(query) {
        if (!query) {
            return {
                results: [],
                patterns: [],
            };
        }

        const queryLower = query.toLowerCase();

        // Test sonuçlarında ara
        const resultMatches = this.results
            .filter((result) => {
                // Timestamp'te ara
                if (result.timestamp.toLowerCase().includes(queryLower)) {
                    return true;
                }

                // Migration hatalarında ara
                if (result.migrations && result.migrations.errors) {
                    for (const error of result.migrations.errors) {
                        if (
                            error.file.toLowerCase().includes(queryLower) ||
                            error.errors.some((e) => e.toLowerCase().includes(queryLower))
                        ) {
                            return true;
                        }
                    }
                }

                // Seeder hatalarında ara
                if (result.seeders && result.seeders.errors) {
                    for (const error of result.seeders.errors) {
                        if (
                            error.file.toLowerCase().includes(queryLower) ||
                            error.errors.some((e) => e.toLowerCase().includes(queryLower))
                        ) {
                            return true;
                        }
                    }
                }

                return false;
            })
            .slice(0, 10); // En fazla 10 sonuç döndür

        // Hata desenlerinde ara
        const patternMatches = {
            migrations: Object.values(this.patterns.migrations)
                .filter(
                    (pattern) =>
                        pattern.file.toLowerCase().includes(queryLower) ||
                        pattern.error.toLowerCase().includes(queryLower) ||
                        (pattern.module && pattern.module.toLowerCase().includes(queryLower))
                )
                .slice(0, 5),
            seeders: Object.values(this.patterns.seeders)
                .filter(
                    (pattern) =>
                        pattern.file.toLowerCase().includes(queryLower) ||
                        pattern.error.toLowerCase().includes(queryLower) ||
                        (pattern.module && pattern.module.toLowerCase().includes(queryLower))
                )
                .slice(0, 5),
        };

        return {
            results: resultMatches,
            patterns: [...patternMatches.migrations, ...patternMatches.seeders],
        };
    }

    /**
     * Son test sonuçlarını getirir
     * @param {number} limit Sonuç sayısı
     * @returns {Array} Test sonuçları
     */
    getRecentResults(limit = 10) {
        return this.results.slice(-limit);
    }

    /**
     * En sık karşılaşılan hata desenlerini getirir
     * @param {number} limit Desen sayısı
     * @returns {Object} Hata desenleri
     */
    getCommonPatterns(limit = 5) {
        const migrationPatterns = Object.values(this.patterns.migrations)
            .sort((a, b) => b.occurrences - a.occurrences)
            .slice(0, limit);

        const seederPatterns = Object.values(this.patterns.seeders)
            .sort((a, b) => b.occurrences - a.occurrences)
            .slice(0, limit);

        return {
            migrations: migrationPatterns,
            seeders: seederPatterns,
        };
    }
}

module.exports = new KnowledgeBase();
