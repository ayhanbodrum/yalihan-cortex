/**
 * Context7 Rule Loader
 * Master MD dosyalarından yasaklı kuralları otomatik öğrenir
 */

const fs = require('fs');
const path = require('path');

class Context7RuleLoader {
    constructor() {
        this.projectRoot = path.join(__dirname, '../..');
        this.rules = {
            forbidden: {},
            required: {},
            patterns: [],
            lastLoaded: null,
        };

        this.masterDocs = [
            'docs/ai-training/02-CONTEXT7-RULES-SIMPLIFIED.md',
            'docs/context7/rules/context7-rules.md',
            'README.md',
            '.context7/authority.json',
        ];
    }

    /**
     * Tüm master dökümanları yükle ve kuralları çıkar
     */
    loadAllRules() {
        console.log('🔍 Context7 kuralları öğreniliyor...');

        this.masterDocs.forEach((docPath) => {
            const fullPath = path.join(this.projectRoot, docPath);
            if (fs.existsSync(fullPath)) {
                this.parseDocument(fullPath);
            }
        });

        this.rules.lastLoaded = new Date().toISOString();
        this.saveRules();

        console.log(`✅ ${Object.keys(this.rules.forbidden).length} yasaklı kural öğrenildi`);
        console.log(`✅ ${Object.keys(this.rules.required).length} zorunlu kural öğrenildi`);

        return this.rules;
    }

    /**
     * MD dosyasını parse et ve kuralları çıkar
     */
    parseDocument(filePath) {
        const content = fs.readFileSync(filePath, 'utf8');
        const fileName = path.basename(filePath);

        console.log(`📖 Okunuyor: ${fileName}`);

        // Yasaklı pattern'leri bul
        this.extractForbiddenPatterns(content, fileName);

        // Zorunlu pattern'leri bul
        this.extractRequiredPatterns(content, fileName);

        // Code example'lardan öğren
        this.extractCodeExamples(content, fileName);
    }

    /**
     * Yasaklı pattern'leri çıkar
     */
    extractForbiddenPatterns(content, source) {
        // Pattern: ❌ YASAK veya FORBIDDEN
        const forbiddenRegex = /[❌🚫]\s*(?:YASAK|FORBIDDEN|NOT|ASLA).*?['"`]([^'"`]+)['"`]/gi;
        let match;

        while ((match = forbiddenRegex.exec(content)) !== null) {
            const forbidden = match[1].trim();

            if (!this.rules.forbidden[forbidden]) {
                this.rules.forbidden[forbidden] = {
                    value: forbidden,
                    reason: 'Context7 yasak',
                    sources: [],
                    severity: 'critical',
                };
            }

            if (!this.rules.forbidden[forbidden].sources.includes(source)) {
                this.rules.forbidden[forbidden].sources.push(source);
            }
        }

        // Specific patterns
        const specificPatterns = {
            durum: 'status kullan',
            is_active: 'status kullan',
            aktif: 'active kullan',
            sehir: 'il kullan',
            sehir_id: 'il_id kullan',
            ad_soyad: 'tam_ad accessor kullan',
            full_name: 'name kullan',
            'btn-': 'neo-btn kullan (Neo Design)',
            'card-': 'neo-card kullan (Neo Design)',
            'form-control': 'neo-input kullan',
        };

        Object.entries(specificPatterns).forEach(([forbidden, suggestion]) => {
            if (content.includes(forbidden)) {
                if (!this.rules.forbidden[forbidden]) {
                    this.rules.forbidden[forbidden] = {
                        value: forbidden,
                        reason: suggestion,
                        sources: [source],
                        severity: 'critical',
                    };
                }
            }
        });
    }

    /**
     * Zorunlu pattern'leri çıkar
     */
    extractRequiredPatterns(content, source) {
        // Pattern: ✅ DOĞRU veya REQUIRED
        const requiredRegex = /[✅✔️]\s*(?:DOĞRU|REQUIRED|ZORUNLU).*?['"`]([^'"`]+)['"`]/gi;
        let match;

        while ((match = requiredRegex.exec(content)) !== null) {
            const required = match[1].trim();

            if (!this.rules.required[required]) {
                this.rules.required[required] = {
                    value: required,
                    reason: 'Context7 zorunlu',
                    sources: [],
                    severity: 'required',
                };
            }

            if (!this.rules.required[required].sources.includes(source)) {
                this.rules.required[required].sources.push(source);
            }
        }
    }

    /**
     * Code example'lardan öğren
     */
    extractCodeExamples(content, source) {
        // PHP code blocks
        const codeBlockRegex = /```php\n(.*?)\n```/gs;
        let match;

        while ((match = codeBlockRegex.exec(content)) !== null) {
            const code = match[1];

            // Yasaklı kullanımları tespit et
            Object.keys(this.rules.forbidden).forEach((forbidden) => {
                if (code.includes(forbidden)) {
                    // Pattern olarak kaydet
                    this.rules.patterns.push({
                        type: 'code_violation',
                        forbidden: forbidden,
                        context: code.substring(0, 100),
                        source: source,
                    });
                }
            });
        }
    }

    /**
     * Kuralları kaydet
     */
    saveRules() {
        const rulesPath = path.join(__dirname, 'context7-rules.json');
        fs.writeFileSync(rulesPath, JSON.stringify(this.rules, null, 2));
        console.log(`💾 Kurallar kaydedildi: ${rulesPath}`);
    }

    /**
     * Kuralları yükle (cache'den)
     */
    loadRules() {
        const rulesPath = path.join(__dirname, 'context7-rules.json');
        if (fs.existsSync(rulesPath)) {
            this.rules = JSON.parse(fs.readFileSync(rulesPath, 'utf8'));
            return this.rules;
        }
        return this.loadAllRules();
    }

    /**
     * Kod içinde yasaklı kullanım var mı kontrol et
     * Geliştirilmiş: Daha akıllı pattern matching, context-aware detection
     */
    checkCode(code, filePath) {
        const violations = [];
        const lines = code.split('\n');
        const fileType = this.detectFileType(filePath);

        // Context-aware exclusion patterns (false positive önleme)
        const exclusionPatterns = {
            php: [
                /\/\/.*/, // Comments
                /\/\*[\s\S]*?\*\//, // Multi-line comments
                /['"`].*['"`]/, // Strings (handled separately)
                /->orderBy\(/, // Laravel orderBy() method (allowed)
                /orderByDesc\(/, // Laravel orderByDesc() method (allowed)
                /orderByAsc\(/, // Laravel orderByAsc() method (allowed)
                /@order/, // PHP annotations
            ],
            blade: [
                /@.*/, // Blade directives
                /{{.*}}/, // Blade echo
                /{!!.*!!}/, // Blade raw echo
                /<!--.*-->/, // HTML comments
            ],
            js: [
                /\/\/.*/, // Comments
                /\/\*[\s\S]*?\*\//, // Multi-line comments
                /['"`].*['"`]/, // Strings
                /\.orderBy\(/, // Method calls
            ],
        };

        Object.entries(this.rules.forbidden).forEach(([forbidden, rule]) => {
            // Daha akıllı regex: word boundary + context-aware
            const regex = this.buildSmartRegex(forbidden, fileType);
            
            lines.forEach((line, index) => {
                // Exclusion check
                if (this.isExcluded(line, exclusionPatterns[fileType] || [])) {
                    return;
                }

                // Context-aware violation check
                const match = regex.exec(line);
                if (match) {
                    // False positive kontrolü
                    if (this.isFalsePositive(line, forbidden, fileType)) {
                        return;
                    }

                    violations.push({
                        rule: forbidden,
                        line: index + 1,
                        column: match.index + 1,
                        code: line.trim(),
                        suggestion: rule.reason,
                        severity: rule.severity,
                        file: filePath,
                        context: this.getContext(lines, index),
                        autoFix: this.generateAutoFix(line, forbidden, rule),
                    });
                }
            });
        });

        // Duplicate violation'ları temizle (aynı satır, farklı pattern)
        return this.deduplicateViolations(violations);
    }

    /**
     * Dosya tipini tespit et
     */
    detectFileType(filePath) {
        const ext = filePath.split('.').pop()?.toLowerCase();
        if (ext === 'php') return 'php';
        if (ext === 'blade.php' || filePath.includes('.blade.')) return 'blade';
        if (ext === 'js' || ext === 'mjs') return 'js';
        if (ext === 'vue') return 'vue';
        return 'unknown';
    }

    /**
     * Akıllı regex oluştur (context-aware)
     */
    buildSmartRegex(pattern, fileType) {
        // Özel pattern'ler için özel regex
        const specialPatterns = {
            'order': /(?:^|\s|['"`])\border\b(?:\s*[=:]\s*|['"`]|$)/gi, // 'order' field, not orderBy()
            'durum': /(?:^|\s|['"`])\bdurum\b(?:\s*[=:]\s*|['"`]|$)/gi, // 'durum' field
            'aktif': /(?:^|\s|['"`])\baktif\b(?:\s*[=:]\s*|['"`]|$)/gi, // 'aktif' field
            'musteri': /(?:^|\s|['"`])\bmusteri\b(?:\s*[=:]\s*|['"`]|$)/gi, // 'musteri' field
            'sehir': /(?:^|\s|['"`])\bsehir\b(?:\s*[=:]\s*|['"`]|$)/gi, // 'sehir' field
        };

        if (specialPatterns[pattern]) {
            return specialPatterns[pattern];
        }

        // Genel pattern için word boundary
        return new RegExp(`\\b${pattern.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}\\b`, 'gi');
    }

    /**
     * Exclusion pattern kontrolü
     */
    isExcluded(line, patterns) {
        return patterns.some(pattern => pattern.test(line));
    }

    /**
     * False positive kontrolü
     */
    isFalsePositive(line, forbidden, fileType) {
        const falsePositivePatterns = {
            'order': [
                /orderBy\(/, // Laravel query builder
                /orderByDesc\(/,
                /orderByAsc\(/,
                /ORDER BY/, // SQL (uppercase)
                /@order/, // PHP attribute
                /\/\/.*order/, // Comment
            ],
            'durum': [
                /\/\/.*durum/, // Comment
                /['"`].*durum.*['"`]/, // String literal (display text)
            ],
            'aktif': [
                /\/\/.*aktif/, // Comment
                /['"`].*aktif.*['"`]/, // String literal
            ],
        };

        const patterns = falsePositivePatterns[forbidden] || [];
        return patterns.some(pattern => pattern.test(line));
    }

    /**
     * Context bilgisi al (öncesi/sonrası satırlar)
     */
    getContext(lines, index, contextSize = 2) {
        const start = Math.max(0, index - contextSize);
        const end = Math.min(lines.length, index + contextSize + 1);
        return {
            before: lines.slice(start, index).map((l, i) => ({
                line: start + i + 1,
                code: l.trim(),
            })),
            current: {
                line: index + 1,
                code: lines[index].trim(),
            },
            after: lines.slice(index + 1, end).map((l, i) => ({
                line: index + i + 2,
                code: l.trim(),
            })),
        };
    }

    /**
     * Otomatik düzeltme önerisi oluştur
     */
    generateAutoFix(line, forbidden, rule) {
        const fixes = {
            'order': line.replace(/\border\b/gi, 'display_order'),
            'durum': line.replace(/\bdurum\b/gi, 'status'),
            'aktif': line.replace(/\baktif\b/gi, 'status'),
            'musteri': line.replace(/\bmusteri\b/gi, 'kisi'),
            'sehir': line.replace(/\bsehir\b/gi, 'il'),
        };

        const fixed = fixes[forbidden];
        if (fixed && fixed !== line) {
            return {
                original: line,
                fixed: fixed,
                confidence: 0.9,
            };
        }

        return null;
    }

    /**
     * Duplicate violation'ları temizle
     */
    deduplicateViolations(violations) {
        const seen = new Map();
        return violations.filter(v => {
            const key = `${v.file}:${v.line}:${v.rule}`;
            if (seen.has(key)) {
                return false;
            }
            seen.set(key, true);
            return true;
        });
    }

    /**
     * Yeni yasaklı pattern ekle
     */
    addForbiddenPattern(pattern, reason, source) {
        if (!this.rules.forbidden[pattern]) {
            this.rules.forbidden[pattern] = {
                value: pattern,
                reason: reason,
                sources: [source],
                severity: 'critical',
            };
            this.saveRules();
            return true;
        }

        // Zaten var, sadece source ekle
        if (!this.rules.forbidden[pattern].sources.includes(source)) {
            this.rules.forbidden[pattern].sources.push(source);
            this.saveRules();
        }
        return false;
    }

    /**
     * Yeni zorunlu pattern ekle
     */
    addRequiredPattern(pattern, reason, source) {
        if (!this.rules.required[pattern]) {
            this.rules.required[pattern] = {
                value: pattern,
                reason: reason,
                sources: [source],
                severity: 'warning',
            };
            this.saveRules();
            return true;
        }

        if (!this.rules.required[pattern].sources.includes(source)) {
            this.rules.required[pattern].sources.push(source);
            this.saveRules();
        }
        return false;
    }

    /**
     * Otomatik öğrenme modu
     */
    async autoLearn() {
        console.log('🧠 Otomatik öğrenme başlıyor...');

        // Her saat kuralları yeniden yükle
        setInterval(
            () => {
                console.log('🔄 Kurallar güncelleniyor...');
                this.loadAllRules();
            },
            60 * 60 * 1000
        ); // 1 saat

        // İlk yükleme
        this.loadAllRules();
    }
}

module.exports = new Context7RuleLoader();
