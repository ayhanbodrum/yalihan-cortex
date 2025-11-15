import puppeteer from 'puppeteer';
import fs from 'fs';

const CONFIG = {
    baseUrl: 'http://127.0.0.1:8000',
    loginEmail: 'admin@yalihanemlak.com',
    loginPassword: 'admin123',
    screenshotsDir: './screenshots/detayli-test',
    timeout: 30000,
    headless: true,
};

const TEST_PAGES = {
    'CRM': [
        { url: '/admin/kisiler', text: 'Kişiler Liste' },
        { url: '/admin/kisiler/create', text: 'Kişi Ekle' },
        { url: '/admin/danisman', text: 'Danışmanlar Liste' },
        { url: '/admin/danisman/create', text: 'Danışman Ekle' },
        { url: '/admin/talepler', text: 'Talepler Liste' },
        { url: '/admin/takim-yonetimi/takim', text: 'Takım Liste' },
        { url: '/admin/takim-yonetimi/gorevler', text: 'Görevler' },
    ],
    'İlan Yönetimi': [
        { url: '/admin/ilanlar', text: 'İlanlar Liste' },
        { url: '/admin/ilanlar/create', text: 'İlan Ekle' },
        { url: '/admin/ilan-kategorileri', text: 'İlan Kategorileri' },
        { url: '/admin/ozellikler', text: 'Özellikler' },
        { url: '/admin/ozellikler/kategoriler', text: 'Özellik Kategorileri' },
        { url: '/stable-create', text: 'Stable Create (İlan Ekleme)' },
    ],
};

const results = {
    byCategory: {},
    errors: [],
    totalTests: 0,
    successCount: 0,
    errorCount: 0,
};

async function createScreenshotDir() {
    if (!fs.existsSync(CONFIG.screenshotsDir)) {
        fs.mkdirSync(CONFIG.screenshotsDir, { recursive: true });
    }
}

async function login(page) {
    console.log('🔐 Admin girişi yapılıyor...\n');

    await page.goto(CONFIG.baseUrl + '/login', { waitUntil: 'networkidle2' });
    await page.type('input[name="email"]', CONFIG.loginEmail);
    await page.type('input[name="password"]', CONFIG.loginPassword);
    await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle2' }),
        page.click('button[type="submit"]'),
    ]);

    console.log('   ✅ Giriş başarılı!\n');
}

async function testPage(browser, url, linkText, category) {
    const page = await browser.newPage();
    page.setDefaultTimeout(CONFIG.timeout);

    const testResult = {
        url,
        linkText,
        category,
        status: 'unknown',
        httpStatus: null,
        error: null,
        screenshot: null,
        timestamp: new Date().toISOString(),
    };

    try {
        const response = await page.goto(CONFIG.baseUrl + url, {
            waitUntil: 'networkidle2',
            timeout: CONFIG.timeout,
        });

        testResult.httpStatus = response.status();

        const hasError = await page.evaluate(() => {
            const errorKeywords = [
                'Exception',
                'Error',
                'SQLSTATE',
                'Undefined variable',
                'Class not found',
                'syntax error',
                'Call to undefined',
                'doesn\'t exist',
            ];

            const bodyText = document.body.innerText;
            return errorKeywords.some(keyword => bodyText.includes(keyword));
        });

        if (hasError) {
            testResult.status = 'error';

            const errorDetails = await page.evaluate(() => {
                const errorTitle = document.querySelector('title')?.textContent || '';
                const errorBody = document.querySelector('.exception-message, .error-message')?.textContent || '';
                const bodyText = document.body.innerText;

                let errorType = 'Unknown';
                if (bodyText.includes('SQLSTATE[42S02]')) {
                    const match = bodyText.match(/Table '.*?\.(\w+)'/);
                    errorType = `Tablo eksik: ${match ? match[1] : 'unknown'}`;
                } else if (bodyText.includes('Undefined variable')) {
                    const match = bodyText.match(/Undefined variable \$(\w+)/);
                    errorType = `Tanımsız değişken: $${match ? match[1] : 'unknown'}`;
                } else if (bodyText.includes('Call to undefined')) {
                    const match = bodyText.match(/Call to undefined (\w+) (.*)/);
                    errorType = `Tanımsız ${match ? match[1] : 'method/function'}`;
                }

                return { title: errorTitle, body: errorBody.substring(0, 300), type: errorType };
            });

            testResult.error = errorDetails;

            const screenshotPath = `${CONFIG.screenshotsDir}/${category.replace(/\s/g, '-')}-error-${Date.now()}.png`;
            await page.screenshot({ path: screenshotPath, fullPage: true });
            testResult.screenshot = screenshotPath;

            console.log(`   ❌ HATA: ${errorDetails.type}`);
            console.log(`   📸 Screenshot: ${screenshotPath}`);

            results.errorCount++;
            results.errors.push(testResult);
        } else {
            testResult.status = 'success';

            const screenshotPath = `${CONFIG.screenshotsDir}/${category.replace(/\s/g, '-')}-success-${Date.now()}.png`;
            await page.screenshot({ path: screenshotPath, fullPage: false });
            testResult.screenshot = screenshotPath;

            console.log(`   ✅ Başarılı (HTTP ${testResult.httpStatus})`);

            results.successCount++;
        }

    } catch (error) {
        testResult.status = 'failed';
        testResult.error = { title: error.message, type: 'Connection Error' };

        console.log(`   💥 Bağlantı hatası: ${error.message}`);

        results.errorCount++;
        results.errors.push(testResult);
    }

    await page.close();
    return testResult;
}

async function generateDetailedReport() {
    let report = `# 🧪 Admin Panel Detaylı Test Raporu

**Test Zamanı:** ${new Date().toLocaleString('tr-TR')}
**Toplam Kategori:** ${Object.keys(TEST_PAGES).length}
**Toplam Sayfa:** ${results.totalTests}

---

## 📊 Genel Özet

| Metrik | Değer |
|--------|-------|
| **Toplam Test** | ${results.totalTests} |
| **Başarılı** | ${results.successCount} ✅ |
| **Hatalı** | ${results.errorCount} ❌ |
| **Başarı Oranı** | ${((results.successCount / results.totalTests) * 100).toFixed(2)}% |

---

## 📋 Kategori Bazında Sonuçlar

`;

    for (const [category, pages] of Object.entries(results.byCategory)) {
        const categoryTests = pages.length;
        const categorySuccess = pages.filter(p => p.status === 'success').length;
        const categoryErrors = pages.filter(p => p.status === 'error').length;

        report += `### ${category}\n\n`;
        report += `- **Toplam:** ${categoryTests}\n`;
        report += `- **Başarılı:** ${categorySuccess} ✅\n`;
        report += `- **Hatalı:** ${categoryErrors} ❌\n`;
        report += `- **Oran:** ${((categorySuccess / categoryTests) * 100).toFixed(2)}%\n\n`;

        report += `#### Detaylar:\n\n`;
        pages.forEach(p => {
            const icon = p.status === 'success' ? '✅' : '❌';
            report += `${icon} **${p.linkText}** (${p.url})\n`;
            if (p.error) {
                report += `  - **Hata:** ${p.error.type}\n`;
                report += `  - **Screenshot:** ${p.screenshot}\n`;
            }
            report += `\n`;
        });

        report += `---\n\n`;
    }

    if (results.errors.length > 0) {
        report += `## ❌ Hata Detayları ve Çözümler\n\n`;

        results.errors.forEach((err, idx) => {
            report += `### ${idx + 1}. ${err.linkText} (${err.url})\n\n`;
            report += `- **Kategori:** ${err.category}\n`;
            report += `- **Hata Tipi:** ${err.error.type}\n`;
            report += `- **HTTP Status:** ${err.httpStatus || 'N/A'}\n`;
            report += `- **Screenshot:** ${err.screenshot}\n\n`;

            if (err.error.type.includes('Tablo eksik')) {
                const tableName = err.error.type.split(': ')[1];
                report += `**Çözüm:**\n`;
                report += `\`\`\`bash\n`;
                report += `php artisan make:migration create_${tableName}_table\n`;
                report += `# Migration'ı doldur ve çalıştır\n`;
                report += `php artisan migrate\n`;
                report += `\`\`\`\n\n`;
            } else if (err.error.type.includes('Tanımsız değişken')) {
                report += `**Çözüm:**\n`;
                report += `Controller'da değişkeni tanımla veya view'a gönder\n\n`;
            }

            report += `---\n\n`;
        });
    }

    report += `## 📸 Ekran Görüntüleri\n\n`;
    report += `Tüm ekran görüntüleri: \`${CONFIG.screenshotsDir}/\`\n\n`;
    report += `**Toplam:** ${results.totalTests} screenshot\n\n`;
    report += `---\n\n`;
    report += `**Context7 Uyumlu:** ✅  \n`;
    report += `**Rapor Tarihi:** ${new Date().toLocaleString('tr-TR')}\n`;

    fs.writeFileSync('./admin-detayli-test-raporu.md', report);
    console.log(`\n📋 Detaylı rapor: ./admin-detayli-test-raporu.md\n`);
}

async function main() {
    console.log('\n🔍 Admin Panel Detaylı Test Başlatılıyor...\n');
    console.log('═══════════════════════════════════════════════════\n');

    await createScreenshotDir();

    const browser = await puppeteer.launch({
        headless: CONFIG.headless,
        args: ['--no-sandbox', '--disable-setuid-sandbox'],
    });

    const page = await browser.newPage();
    await page.setViewport({ width: 1920, height: 1080 });

    await login(page);
    await page.close();

    for (const [category, pages] of Object.entries(TEST_PAGES)) {
        console.log(`\n📦 ${category} Kategorisi Test Ediliyor...\n`);
        console.log('─────────────────────────────────────────────────\n');

        results.byCategory[category] = [];

        for (const pageInfo of pages) {
            results.totalTests++;
            console.log(`📄 ${pageInfo.text}`);

            const result = await testPage(browser, pageInfo.url, pageInfo.text, category);
            results.byCategory[category].push(result);

            await new Promise(resolve => setTimeout(resolve, 500));
        }
    }

    await browser.close();

    console.log('\n═══════════════════════════════════════════════════');
    console.log('📊 TEST TAMAMLANDI!\n');

    for (const [category, pages] of Object.entries(results.byCategory)) {
        const success = pages.filter(p => p.status === 'success').length;
        const total = pages.length;
        console.log(`${category}: ${success}/${total} başarılı`);
    }

    console.log(`\nGenel: ${results.successCount}/${results.totalTests} başarılı (${((results.successCount / results.totalTests) * 100).toFixed(2)}%)\n`);

    await generateDetailedReport();

    console.log('✨ Test tamamlandı!\n');

    process.exit(results.errorCount > 0 ? 1 : 0);
}

main().catch(error => {
    console.error('💥 Kritik hata:', error);
    process.exit(1);
});
