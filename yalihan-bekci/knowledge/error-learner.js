/**
 * Error Learner - Hata Raporlarından Otomatik Öğrenir
 * docs/ klasöründeki hata raporlarını tarar ve kuralları çıkarır
 */

const fs = require("fs");
const path = require("path");

class ErrorLearner {
    constructor() {
        this.projectRoot = process.env.PROJECT_ROOT || process.cwd();
        this.errorsPath = path.join(__dirname, "learned-errors.json");
        this.errors = this.loadErrors();
    }

    loadErrors() {
        if (fs.existsSync(this.errorsPath)) {
            return JSON.parse(fs.readFileSync(this.errorsPath, "utf8"));
        }

        return {
            undefined_variables: {},
            missing_tables: {},
            context7_violations: {},
            common_errors: {},
            alpine_undefined: {},
            vite_directive_missing: {},
            vite_cache_issue: {},
            tailwind_errors: {},
            lastScan: null,
        };
    }

    saveErrors() {
        fs.writeFileSync(this.errorsPath, JSON.stringify(this.errors, null, 2));
    }

    /**
     * Tüm hata raporlarını tara
     */
    scanAllReports() {
        console.error("🔍 Hata raporları taranıyor...");

        const reportPaths = [
            "docs/admin/admin-detayli-test-raporu.md",
            "docs/admin/admin-kapsamli-test-raporu.md",
            "docs/context7/reports/context7-violations-log.md",
            "docs/reports/KRITIK_HATALAR_VE_ONLEMLER.md",
        ];

        let totalLearned = 0;

        reportPaths.forEach((reportPath) => {
            const fullPath = path.join(this.projectRoot, reportPath);
            if (fs.existsSync(fullPath)) {
                const learned = this.scanReport(fullPath);
                totalLearned += learned;
            }
        });

        this.errors.lastScan = new Date().toISOString();
        this.saveErrors();

        console.error(`📊 ${totalLearned} hata pattern öğrenildi`);

        return totalLearned;
    }

    /**
     * Tek raporu tara
     */
    scanReport(filePath) {
        const content = fs.readFileSync(filePath, "utf8");
        const fileName = path.basename(filePath);
        let learned = 0;

        // 1. Tanımsız değişken hatalarını öğren
        const undefinedVarRegex = /Tanımsız değişken:\s*\$(\w+)/g;
        let match;

        while ((match = undefinedVarRegex.exec(content)) !== null) {
            const varName = match[1];
            if (!this.errors.undefined_variables[varName]) {
                this.errors.undefined_variables[varName] = {
                    variable: `$${varName}`,
                    solution: `Controller'da $${varName} tanımlanmalı`,
                    sources: [],
                    occurrences: 0,
                };
            }
            this.errors.undefined_variables[varName].occurrences++;
            if (
                !this.errors.undefined_variables[varName].sources.includes(
                    fileName
                )
            ) {
                this.errors.undefined_variables[varName].sources.push(fileName);
            }
            learned++;
        }

        // 2. Eksik tablo hatalarını öğren
        const missingTableRegex = /Tablo eksik:\s*(\w+)/g;
        while ((match = missingTableRegex.exec(content)) !== null) {
            const tableName = match[1];
            if (!this.errors.missing_tables[tableName]) {
                this.errors.missing_tables[tableName] = {
                    table: tableName,
                    solution: `Migration oluştur: create_${tableName}_table`,
                    sources: [],
                };
            }
            if (
                !this.errors.missing_tables[tableName].sources.includes(
                    fileName
                )
            ) {
                this.errors.missing_tables[tableName].sources.push(fileName);
            }
            learned++;
        }

        // 3. Alpine.js undefined hatalarını öğren
        if (!this.errors.alpine_undefined) {
            this.errors.alpine_undefined = {};
        }

        const alpineRegex =
            /Alpine Expression Error:\s+(\w+)\s+is not defined/g;
        while ((match = alpineRegex.exec(content)) !== null) {
            const funcName = match[1];
            if (!this.errors.alpine_undefined[funcName]) {
                this.errors.alpine_undefined[funcName] = {
                    function: funcName,
                    solution: `window.${funcName} export edilmeli veya @vite direktifi blade'de eksik`,
                    fix: `@vite(['resources/js/admin/file.js']) blade'de @push('scripts') içine ekle`,
                    sources: [],
                    occurrences: 0,
                };
            }
            this.errors.alpine_undefined[funcName].occurrences++;
            if (
                !this.errors.alpine_undefined[funcName].sources.includes(
                    fileName
                )
            ) {
                this.errors.alpine_undefined[funcName].sources.push(fileName);
            }
            learned++;
        }

        // 4. @vite direktifi eksikliği
        if (!this.errors.vite_directive_missing) {
            this.errors.vite_directive_missing = {};
        }

        // Eğer çok sayıda Alpine undefined varsa, @vite eksik olabilir
        const alpineErrorCount = (
            content.match(/Alpine Expression Error/g) || []
        ).length;
        if (alpineErrorCount > 5) {
            const vitePattern = {
                pattern: "Multiple Alpine undefined errors (10+)",
                probable_cause: "@vite direktifi blade'de eksik",
                solution:
                    "Blade'de @push('scripts') içine @vite(['resources/js/admin/file.js']) ekle",
                detection_count: alpineErrorCount,
                source: fileName,
                severity: "critical",
            };

            this.errors.vite_directive_missing[fileName] = vitePattern;
            learned++;
        }

        // 5. Vite cache sorunları
        if (!this.errors.vite_cache_issue) {
            this.errors.vite_cache_issue = {};
        }

        if (
            content.includes("Eski modül yükleniyor") ||
            content.includes("Hard refresh yeterli değil")
        ) {
            this.errors.vite_cache_issue["cache_clear_needed"] = {
                pattern: "Vite cache eski modül yüklüyor",
                solution: "rm -rf node_modules/.vite && vite restart",
                occurrences:
                    (this.errors.vite_cache_issue["cache_clear_needed"]
                        ?.occurrences || 0) + 1,
                sources: [fileName],
            };
            learned++;
        }

        return learned;
    }

    /**
     * En sık görülen hataları getir
     */
    getMostCommonErrors(limit = 10) {
        const allErrors = [];

        // Tanımsız değişkenler
        Object.entries(this.errors.undefined_variables).forEach(
            ([key, error]) => {
                allErrors.push({
                    type: "undefined_variable",
                    name: error.variable,
                    count: error.occurrences,
                    solution: error.solution,
                });
            }
        );

        // Sıklığa göre sırala
        allErrors.sort((a, b) => b.count - a.count);

        return allErrors.slice(0, limit);
    }

    /**
     * Rapor üret
     */
    generateReport() {
        return {
            summary: {
                undefined_variables: Object.keys(
                    this.errors.undefined_variables
                ).length,
                missing_tables: Object.keys(this.errors.missing_tables).length,
                context7_violations: Object.keys(
                    this.errors.context7_violations
                ).length,
                common_errors: Object.keys(this.errors.common_errors).length,
                alpine_undefined: Object.keys(this.errors.alpine_undefined)
                    .length,
                vite_directive_missing: Object.keys(
                    this.errors.vite_directive_missing
                ).length,
                vite_cache_issue: Object.keys(this.errors.vite_cache_issue)
                    .length,
                tailwind_errors: Object.keys(this.errors.tailwind_errors)
                    .length,
                lastScan: this.errors.lastScan,
            },
            most_common: this.getMostCommonErrors(10),
            details: this.errors,
        };
    }

    /**
     * Yeni hata manuelden ekle (bugün yaşanan hatalar için)
     */
    learnManualError(errorType, errorData) {
        if (!this.errors[errorType]) {
            this.errors[errorType] = {};
        }

        const key =
            errorData.name || errorData.pattern || Date.now().toString();
        this.errors[errorType][key] = {
            ...errorData,
            learnedAt: new Date().toISOString(),
            manual: true,
        };

        this.saveErrors();
        console.error(`✅ Manuel hata öğrenildi: ${errorType} → ${key}`);
    }
}

module.exports = new ErrorLearner();
