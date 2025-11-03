/**
 * Context7 Validator
 * Kod dosyalarını Context7 kurallarına göre validate eder
 */

const fs = require('fs');
const path = require('path');
const context7Rules = require('../knowledge/context7-rule-loader');

class Context7Validator {
  constructor() {
    this.rules = null;
  }

  /**
   * Kuralları yükle
   */
  async loadRules() {
    if (!this.rules) {
      this.rules = context7Rules.loadRules();
    }
    return this.rules;
  }

  /**
   * Tüm PHP dosyalarını validate et
   */
  async validateAll() {
    await this.loadRules();

    const results = {
      total: 0,
      violations: [],
      passed: 0
    };

    // Scan edilecek dizinler
    const directories = [
      'app/Models',
      'app/Http/Controllers',
      'database/migrations',
      'database/seeders',
      'resources/views'
    ];

    for (const dir of directories) {
      const fullPath = path.join(process.env.PROJECT_ROOT || process.cwd(), dir);
      if (fs.existsSync(fullPath)) {
        await this.scanDirectory(fullPath, results);
      }
    }

    results.passed = results.total - results.violations.length;

    return results;
  }

  /**
   * Dizini tarar
   */
  async scanDirectory(dirPath, results) {
    const files = fs.readdirSync(dirPath, { recursive: true });

    for (const file of files) {
      const filePath = path.join(dirPath, file);

      if (fs.statSync(filePath).isFile()) {
        const ext = path.extname(filePath);
        if (['.php', '.blade.php'].includes(ext)) {
          results.total++;
          await this.validateFile(filePath, results);
        }
      }
    }
  }

  /**
   * Tek dosyayı validate et
   */
  async validateFile(filePath, results) {
    const content = fs.readFileSync(filePath, 'utf8');
    const violations = context7Rules.checkCode(content, filePath);

    if (violations.length > 0) {
      results.violations.push({
        file: filePath,
        violations: violations
      });
    }
  }

  /**
   * Specific pattern kontrolü
   */
  checkPattern(code, pattern) {
    const regex = new RegExp(`\\b${pattern}\\b`, 'gi');
    return regex.test(code);
  }

  /**
   * Auto-fix önerileri
   */
  generateAutoFix(violation) {
    const fixes = {
      'durum': { replace: 'status', type: 'field_rename' },
      'is_active': { replace: 'status', type: 'field_rename' },
      'sehir': { replace: 'il', type: 'field_rename' },
      'sehir_id': { replace: 'il_id', type: 'field_rename' },
      'btn-': { replace: 'neo-btn', type: 'css_class' },
      'card-': { replace: 'neo-card', type: 'css_class' }
    };

    return fixes[violation.rule] || null;
  }
}

module.exports = new Context7Validator();

