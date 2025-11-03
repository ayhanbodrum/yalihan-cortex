/**
 * TestSprite MCP - Raporlama Motoru
 * 
 * Bu modül, test sonuçlarını raporlar ve görselleştirir.
 * Markdown ve HTML formatlarında raporlar oluşturur.
 */

const fs = require('fs');
const path = require('path');
const knowledgeBase = require('../knowledge/knowledge-base');

class ReportingEngine {
  constructor() {
    this.reportsPath = path.join(process.cwd(), 'reports/testsprite');
    this.ensureDirectoryExists(this.reportsPath);
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
   * Rapor oluşturur
   * @param {string} type Rapor türü (summary, detailed, changelog)
   * @param {Object} results Test sonuçları (belirtilmezse son sonuçlar kullanılır)
   * @returns {Object} Rapor bilgileri
   */
  generateReport(type = 'summary', results = null) {
    // Sonuçlar belirtilmemişse son sonuçları kullan
    if (!results) {
      const recentResults = knowledgeBase.getRecentResults(1);
      if (recentResults.length === 0) {
        return { error: 'Henüz test sonucu bulunmuyor' };
      }
      results = recentResults[0];
    }

    const reportId = `report-${Date.now()}`;
    const reportPath = path.join(this.reportsPath, `${reportId}.md`);
    
    let content = '';
    
    switch (type) {
      case 'summary':
        content = this.generateSummaryReport(results);
        break;
      case 'detailed':
        content = this.generateDetailedReport(results);
        break;
      case 'changelog':
        content = this.generateChangelogReport(results);
        break;
      default:
        content = this.generateSummaryReport(results);
    }
    
    fs.writeFileSync(reportPath, content);
    
    return {
      id: reportId,
      path: reportPath,
      type,
      timestamp: new Date().toISOString()
    };
  }

  /**
   * Özet rapor oluşturur
   * @param {Object} results Test sonuçları
   * @returns {string} Rapor içeriği
   */
  generateSummaryReport(results) {
    const totalTests = results.migrations.total + results.seeders.total;
    const passedTests = results.migrations.passed + results.seeders.passed;
    const failedTests = results.migrations.errors.length + results.seeders.errors.length;
    const successRate = totalTests > 0 ? ((passedTests / totalTests) * 100).toFixed(2) : '0.00';
    
    return `# TestSprite MCP - Özet Rapor

## Test Sonuçları (${new Date(results.timestamp).toLocaleString('tr-TR')})

- **Toplam Test**: ${totalTests}
- **Başarılı**: ${passedTests}
- **Başarısız**: ${failedTests}
- **Başarı Oranı**: %${successRate}

## Migration Testleri

- Toplam: ${results.migrations.total}
- Başarılı: ${results.migrations.passed}
- Başarısız: ${results.migrations.errors.length}

## Seeder Testleri

- Toplam: ${results.seeders.total}
- Başarılı: ${results.seeders.passed}
- Başarısız: ${results.seeders.errors.length}

${this.generateErrorSummary(results)}

---
*Bu rapor TestSprite MCP tarafından otomatik olarak oluşturulmuştur.*
`;
  }

  /**
   * Detaylı rapor oluşturur
   * @param {Object} results Test sonuçları
   * @returns {string} Rapor içeriği
   */
  generateDetailedReport(results) {
    const totalTests = results.migrations.total + results.seeders.total;
    const passedTests = results.migrations.passed + results.seeders.passed;
    const failedTests = results.migrations.errors.length + results.seeders.errors.length;
    const successRate = totalTests > 0 ? ((passedTests / totalTests) * 100).toFixed(2) : '0.00';
    
    let report = `# TestSprite MCP - Detaylı Rapor

## Test Sonuçları (${new Date(results.timestamp).toLocaleString('tr-TR')})

- **Toplam Test**: ${totalTests}
- **Başarılı**: ${passedTests}
- **Başarısız**: ${failedTests}
- **Başarı Oranı**: %${successRate}

## Migration Testleri

### Özet
- Toplam: ${results.migrations.total}
- Başarılı: ${results.migrations.passed}
- Başarısız: ${results.migrations.errors.length}

### Hatalar
`;

    if (results.migrations.errors.length === 0) {
      report += '- Hata bulunamadı\n';
    } else {
      results.migrations.errors.forEach((error, index) => {
        report += `#### ${index + 1}. ${error.file}\n`;
        report += `- **Modül**: ${error.module || 'Ana Uygulama'}\n`;
        report += `- **Durum**: Başarısız\n`;
        report += `- **Hatalar**:\n`;
        error.errors.forEach(err => {
          report += `  - ${err}\n`;
        });
        report += '\n';
      });
    }

    report += `\n## Seeder Testleri

### Özet
- Toplam: ${results.seeders.total}
- Başarılı: ${results.seeders.passed}
- Başarısız: ${results.seeders.errors.length}

### Hatalar
`;

    if (results.seeders.errors.length === 0) {
      report += '- Hata bulunamadı\n';
    } else {
      results.seeders.errors.forEach((error, index) => {
        report += `#### ${index + 1}. ${error.file}\n`;
        report += `- **Modül**: ${error.module || 'Ana Uygulama'}\n`;
        report += `- **Durum**: Başarısız\n`;
        report += `- **Hatalar**:\n`;
        error.errors.forEach(err => {
          report += `  - ${err}\n`;
        });
        report += '\n';
      });
    }

    report += `\n## Öneriler

${this.generateRecommendations(results)}

## Sık Karşılaşılan Hatalar

${this.generateCommonPatterns()}

---
*Bu rapor TestSprite MCP tarafından otomatik olarak oluşturulmuştur.*
`;

    return report;
  }

  /**
   * Changelog raporu oluşturur
   * @param {Object} results Test sonuçları
   * @returns {string} Rapor içeriği
   */
  generateChangelogReport(results) {
    // Son iki test sonucunu al
    const recentResults = knowledgeBase.getRecentResults(2);
    if (recentResults.length < 2) {
      return `# TestSprite MCP - Değişiklik Raporu

Karşılaştırma için yeterli test sonucu bulunmuyor. En az iki test sonucu gereklidir.

---
*Bu rapor TestSprite MCP tarafından otomatik olarak oluşturulmuştur.*
`;
    }

    const currentResults = recentResults[1]; // En son sonuç
    const previousResults = recentResults[0]; // Bir önceki sonuç
    
    const currentDate = new Date(currentResults.timestamp).toLocaleString('tr-TR');
    const previousDate = new Date(previousResults.timestamp).toLocaleString('tr-TR');
    
    // Yeni hatalar
    const newMigrationErrors = this.findNewErrors(
      currentResults.migrations.errors,
      previousResults.migrations.errors
    );
    
    const newSeederErrors = this.findNewErrors(
      currentResults.seeders.errors,
      previousResults.seeders.errors
    );
    
    // Çözülen hatalar
    const resolvedMigrationErrors = this.findResolvedErrors(
      previousResults.migrations.errors,
      currentResults.migrations.errors
    );
    
    const resolvedSeederErrors = this.findResolvedErrors(
      previousResults.seeders.errors,
      currentResults.seeders.errors
    );
    
    return `# TestSprite MCP - Değişiklik Raporu

## Karşılaştırma

- **Önceki Test**: ${previousDate}
- **Güncel Test**: ${currentDate}

## Yeni Hatalar

### Migration Hataları

${newMigrationErrors.length === 0 ? '- Yeni hata bulunmuyor\n' : this.formatErrorList(newMigrationErrors)}

### Seeder Hataları

${newSeederErrors.length === 0 ? '- Yeni hata bulunmuyor\n' : this.formatErrorList(newSeederErrors)}

## Çözülen Hatalar

### Migration Hataları

${resolvedMigrationErrors.length === 0 ? '- Çözülen hata bulunmuyor\n' : this.formatErrorList(resolvedMigrationErrors)}

### Seeder Hataları

${resolvedSeederErrors.length === 0 ? '- Çözülen hata bulunmuyor\n' : this.formatErrorList(resolvedSeederErrors)}

## Özet

- **Yeni Hatalar**: ${newMigrationErrors.length + newSeederErrors.length}
- **Çözülen Hatalar**: ${resolvedMigrationErrors.length + resolvedSeederErrors.length}
- **Net Değişim**: ${(resolvedMigrationErrors.length + resolvedSeederErrors.length) - (newMigrationErrors.length + newSeederErrors.length)}

---
*Bu rapor TestSprite MCP tarafından otomatik olarak oluşturulmuştur.*
`;
  }

  /**
   * Hata özetini oluşturur
   * @param {Object} results Test sonuçları
   * @returns {string} Hata özeti
   */
  generateErrorSummary(results) {
    const migrationErrors = results.migrations.errors.length;
    const seederErrors = results.seeders.errors.length;
    
    if (migrationErrors === 0 && seederErrors === 0) {
      return '## Hatalar\n\nHiç hata bulunamadı. Tüm testler başarılı!';
    }
    
    let summary = '## Hatalar\n\n';
    
    if (migrationErrors > 0) {
      summary += `### Migration Hataları (${migrationErrors})\n\n`;
      results.migrations.errors.slice(0, 3).forEach((error, index) => {
        summary += `${index + 1}. **${error.file}**: ${error.errors[0]}\n`;
      });
      
      if (migrationErrors > 3) {
        summary += `... ve ${migrationErrors - 3} hata daha\n`;
      }
      
      summary += '\n';
    }
    
    if (seederErrors > 0) {
      summary += `### Seeder Hataları (${seederErrors})\n\n`;
      results.seeders.errors.slice(0, 3).forEach((error, index) => {
        summary += `${index + 1}. **${error.file}**: ${error.errors[0]}\n`;
      });
      
      if (seederErrors > 3) {
        summary += `... ve ${seederErrors - 3} hata daha\n`;
      }
      
      summary += '\n';
    }
    
    return summary;
  }

  /**
   * Öneriler oluşturur
   * @param {Object} results Test sonuçları
   * @returns {string} Öneriler
   */
  generateRecommendations(results) {
    let recommendations = '';
    
    // Migration önerileri
    if (results.migrations.errors.length > 0) {
      recommendations += '### Migration Önerileri\n\n';
      
      // Modül yapısı hataları
      const moduleErrors = results.migrations.errors.filter(error => 
        error.errors.some(err => err.includes('modül'))
      );
      
      if (moduleErrors.length > 0) {
        recommendations += '- **Modül Yapısı**: Migration dosyalarının doğru modül dizinlerinde olduğundan ve namespace\'lerin doğru olduğundan emin olun.\n';
      }
      
      // Sözdizimi hataları
      const syntaxErrors = results.migrations.errors.filter(error => 
        error.errors.some(err => err.includes('Sözdizimi'))
      );
      
      if (syntaxErrors.length > 0) {
        recommendations += '- **Sözdizimi**: PHP sözdizimi hatalarını düzeltin. Eksik parantezler, noktalı virgüller veya diğer sözdizimi hataları olabilir.\n';
      }
      
      // Versiyon hataları
      const versionErrors = results.migrations.errors.filter(error => 
        error.errors.some(err => err.includes('versiyon') || err.includes('format'))
      );
      
      if (versionErrors.length > 0) {
        recommendations += '- **Versiyon Formatı**: Migration dosya adlarının YYYY_MM_DD_HHMMSS formatına uygun olduğundan emin olun.\n';
      }
      
      recommendations += '\n';
    }
    
    // Seeder önerileri
    if (results.seeders.errors.length > 0) {
      recommendations += '### Seeder Önerileri\n\n';
      
      // Modül yapısı hataları
      const moduleErrors = results.seeders.errors.filter(error => 
        error.errors.some(err => err.includes('modül'))
      );
      
      if (moduleErrors.length > 0) {
        recommendations += '- **Modül Yapısı**: Seeder dosyalarının doğru modül dizinlerinde olduğundan ve namespace\'lerin doğru olduğundan emin olun.\n';
      }
      
      // Sözdizimi hataları
      const syntaxErrors = results.seeders.errors.filter(error => 
        error.errors.some(err => err.includes('Sözdizimi'))
      );
      
      if (syntaxErrors.length > 0) {
        recommendations += '- **Sözdizimi**: PHP sözdizimi hatalarını düzeltin. Eksik parantezler, noktalı virgüller veya diğer sözdizimi hataları olabilir.\n';
      }
      
      // Bağımlılık hataları
      const dependencyErrors = results.seeders.errors.filter(error => 
        error.errors.some(err => err.includes('Bağımlı'))
      );
      
      if (dependencyErrors.length > 0) {
        recommendations += '- **Bağımlılıklar**: Seeder\'ların bağımlı olduğu diğer seeder\'ların varlığını kontrol edin.\n';
      }
      
      // afterLastBatch uyarıları
      const afterLastBatchWarnings = results.seeders.errors.filter(error => 
        error.errors.some(err => err.includes('afterLastBatch'))
      );
      
      if (afterLastBatchWarnings.length > 0) {
        recommendations += '- **afterLastBatch**: Migration sonrası otomatik çalıştırma için seeder\'lara afterLastBatch metodu ekleyin.\n';
      }
      
      recommendations += '\n';
    }
    
    if (recommendations === '') {
      recommendations = '- Tüm testler başarılı! Şu anda özel bir öneri bulunmuyor.\n';
    }
    
    return recommendations;
  }

  /**
   * Sık karşılaşılan hata desenlerini oluşturur
   * @returns {string} Hata desenleri
   */
  generateCommonPatterns() {
    const commonPatterns = knowledgeBase.getCommonPatterns();
    let patterns = '';
    
    // Migration desenleri
    if (commonPatterns.migrations.length > 0) {
      patterns += '### Migration Hataları\n\n';
      commonPatterns.migrations.forEach((pattern, index) => {
        patterns += `${index + 1}. **${pattern.file}**: ${pattern.error}\n`;
        patterns += `   - Görülme sayısı: ${pattern.occurrences}\n`;
        patterns += `   - Son görülme: ${new Date(pattern.lastSeen).toLocaleString('tr-TR')}\n\n`;
      });
    }
    
    // Seeder desenleri
    if (commonPatterns.seeders.length > 0) {
      patterns += '### Seeder Hataları\n\n';
      commonPatterns.seeders.forEach((pattern, index) => {
        patterns += `${index + 1}. **${pattern.file}**: ${pattern.error}\n`;
        patterns += `   - Görülme sayısı: ${pattern.occurrences}\n`;
        patterns += `   - Son görülme: ${new Date(pattern.lastSeen).toLocaleString('tr-TR')}\n\n`;
      });
    }
    
    if (patterns === '') {
      patterns = '- Henüz yeterli veri bulunmuyor.\n';
    }
    
    return patterns;
  }

  /**
   * Yeni hataları bulur
   * @param {Array} currentErrors Güncel hatalar
   * @param {Array} previousErrors Önceki hatalar
   * @returns {Array} Yeni hatalar
   */
  findNewErrors(currentErrors, previousErrors) {
    return currentErrors.filter(currentError => {
      return !previousErrors.some(prevError => 
        prevError.file === currentError.file && 
        prevError.errors[0] === currentError.errors[0]
      );
    });
  }

  /**
   * Çözülen hataları bulur
   * @param {Array} previousErrors Önceki hatalar
   * @param {Array} currentErrors Güncel hatalar
   * @returns {Array} Çözülen hatalar
   */
  findResolvedErrors(previousErrors, currentErrors) {
    return previousErrors.filter(prevError => {
      return !currentErrors.some(currentError => 
        currentError.file === prevError.file && 
        currentError.errors[0] === prevError.errors[0]
      );
    });
  }

  /**
   * Hata listesini formatlar
   * @param {Array} errors Hatalar
   * @returns {string} Formatlanmış hata listesi
   */
  formatErrorList(errors) {
    if (errors.length === 0) {
      return '- Hata bulunamadı\n';
    }
    
    let list = '';
    errors.forEach((error, index) => {
      list += `#### ${index + 1}. ${error.file}\n`;
      list += `- **Modül**: ${error.module || 'Ana Uygulama'}\n`;
      list += `- **Hatalar**:\n`;
      error.errors.forEach(err => {
        list += `  - ${err}\n`;
      });
      list += '\n';
    });
    
    return list;
  }
}

module.exports = new ReportingEngine();