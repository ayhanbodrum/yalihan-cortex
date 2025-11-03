import puppeteer from 'puppeteer';
import fs from 'fs';
import path from 'path';

const CONFIG = {
    baseUrl: 'http://127.0.0.1:8000',
    loginEmail: 'admin@yalihanemlak.com',
    loginPassword: 'admin123',
    screenshotsDir: './screenshots/admin-test',
    timeout: 30000,
    headless: true,
};

const results = {
    tested: [],
    errors: [],
    screenshots: [],
    totalLinks: 0,
    successCount: 0,
    errorCount: 0,
    startTime: new Date(),
};

async function createScreenshotDir() {
    if (!fs.existsSync(CONFIG.screenshotsDir)) {
        fs.mkdirSync(CONFIG.screenshotsDir, { recursive: true });
    }
}

async function login(page) {
    console.log('🔐 Admin girişi yapılıyor...');
    
    await page.goto(CONFIG.baseUrl + '/login', { waitUntil: 'networkidle2' });
    
    await page.type('input[name="email"]', CONFIG.loginEmail);
    await page.type('input[name="password"]', CONFIG.loginPassword);
    
    await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle2' }),
        page.click('button[type="submit"]'),
    ]);
    
    console.log('   ✅ Giriş başarılı!\n');
}

async function extractMenuLinks(page) {
    console.log('🔍 Menü linkleri keşfediliyor...');
    
    const links = await page.evaluate(() => {
        const menuLinks = [];
        const foundUrls = new Set();
        
        document.querySelectorAll('a[href]').forEach(link => {
            const href = link.getAttribute('href');
            
            if (href && href.startsWith('/admin') && !foundUrls.has(href)) {
                const skipPatterns = [
                    '/admin/logout',
                    '#',
                    'javascript:',
                ];
                
                if (!skipPatterns.some(pattern => href.includes(pattern))) {
                    foundUrls.add(href);
                    menuLinks.push({
                        url: href,
                        text: link.textContent.trim() || href,
                        className: link.className,
                    });
                }
            }
        });
        
        return menuLinks.sort((a, b) => a.url.localeCompare(b.url));
    });
    
    console.log(`   ✅ ${links.length} link bulundu\n`);
    
    if (links.length === 0) {
        console.log('   ⚠️  Link bulunamadı! Varsayılan listeden test yapılacak...\n');
        return [
            { url: '/admin/dashboard', text: 'Dashboard' },
            { url: '/admin/ilanlar', text: 'İlanlar' },
            { url: '/admin/kisiler', text: 'Kişiler' },
            { url: '/admin/danisman', text: 'Danışmanlar' },
            { url: '/admin/kullanicilar', text: 'Kullanıcılar' },
            { url: '/admin/ayarlar', text: 'Ayarlar' },
        ];
    }
    
    return links;
}

async function testPage(browser, url, linkText) {
    const page = await browser.newPage();
    
    page.setDefaultTimeout(CONFIG.timeout);
    
    const testResult = {
        url: url,
        linkText: linkText,
        status: 'unknown',
        httpStatus: null,
        error: null,
        screenshot: null,
        timestamp: new Date().toISOString(),
    };
    
    try {
        console.log(`📄 Test: ${url}`);
        console.log(`   Menü: "${linkText}"`);
        
        const response = await page.goto(CONFIG.baseUrl + url, {
            waitUntil: 'networkidle2',
            timeout: CONFIG.timeout,
        });
        
        testResult.httpStatus = response.status();
        
        const pageTitle = await page.title();
        
        const hasError = await page.evaluate(() => {
            const errorKeywords = [
                'Exception',
                'Error',
                'SQLSTATE',
                'Undefined variable',
                'Class not found',
                'syntax error',
                'Call to undefined',
            ];
            
            const bodyText = document.body.innerText;
            return errorKeywords.some(keyword => bodyText.includes(keyword));
        });
        
        if (hasError) {
            testResult.status = 'error';
            
            const errorDetails = await page.evaluate(() => {
                const errorTitle = document.querySelector('title')?.textContent || '';
                const errorBody = document.querySelector('.exception-message, .error-message')?.textContent || '';
                return { title: errorTitle, body: errorBody.substring(0, 200) };
            });
            
            testResult.error = errorDetails;
            
            const screenshotPath = `${CONFIG.screenshotsDir}/error-${Date.now()}.png`;
            await page.screenshot({ path: screenshotPath, fullPage: true });
            testResult.screenshot = screenshotPath;
            
            console.log(`   ❌ HATA BULUNDU!`);
            console.log(`   Error: ${errorDetails.title}`);
            console.log(`   Screenshot: ${screenshotPath}`);
            
            results.errorCount++;
            results.errors.push(testResult);
        } else {
            testResult.status = 'success';
            
            const screenshotPath = `${CONFIG.screenshotsDir}/success-${Date.now()}.png`;
            await page.screenshot({ path: screenshotPath, fullPage: false });
            testResult.screenshot = screenshotPath;
            
            console.log(`   ✅ Başarılı (HTTP ${testResult.httpStatus})`);
            console.log(`   Title: ${pageTitle}`);
            
            results.successCount++;
        }
        
    } catch (error) {
        testResult.status = 'failed';
        testResult.error = error.message;
        
        const screenshotPath = `${CONFIG.screenshotsDir}/failed-${Date.now()}.png`;
        try {
            await page.screenshot({ path: screenshotPath, fullPage: true });
            testResult.screenshot = screenshotPath;
        } catch {}
        
        console.log(`   💥 Test başarısız: ${error.message}`);
        
        results.errorCount++;
        results.errors.push(testResult);
    }
    
    results.tested.push(testResult);
    console.log('');
    
    await page.close();
    return testResult;
}

async function generateReport() {
    const duration = (new Date() - results.startTime) / 1000;
    
    const report = `
# 🧪 Admin Panel Otomatik Test Raporu

**Test Zamanı:** ${results.startTime.toLocaleString('tr-TR')}  
**Süre:** ${duration.toFixed(2)} saniye  
**Tarayıcı:** Puppeteer (Headless Chrome)

---

## 📊 Test Özeti

| Metrik | Değer |
|--------|-------|
| **Toplam Link** | ${results.totalLinks} |
| **Başarılı** | ${results.successCount} ✅ |
| **Hatalı** | ${results.errorCount} ❌ |
| **Başarı Oranı** | ${((results.successCount / results.totalLinks) * 100).toFixed(2)}% |

---

## ${results.errorCount > 0 ? '❌' : '✅'} Bulunan Hatalar

${results.errors.length > 0 ? results.errors.map(e => `
### ${e.linkText} (${e.url})

- **HTTP Status:** ${e.httpStatus || 'N/A'}
- **Hata:** ${e.error?.title || e.error || 'Bilinmeyen hata'}
- **Screenshot:** ${e.screenshot}
- **Zaman:** ${e.timestamp}

${e.error?.body ? '**Detay:**\n```\n' + e.error.body + '\n```\n' : ''}
`).join('\n---\n') : '_Hata bulunamadı! 🎉_'}

---

## ✅ Başarılı Testler

${results.tested.filter(t => t.status === 'success').map(t => `
- ✅ **${t.linkText}** (${t.url}) - HTTP ${t.httpStatus}
`).join('\n')}

---

## 📸 Ekran Görüntüleri

Tüm ekran görüntüleri: \`${CONFIG.screenshotsDir}/\`

**Toplam:** ${results.tested.length} screenshot

---

**Context7 Uyumlu:** ✅  
**Otomatik Test:** ✅  
**Rapor Tarihi:** ${new Date().toLocaleString('tr-TR')}
`;

    const reportPath = './admin-test-report.md';
    fs.writeFileSync(reportPath, report);
    console.log(`\n📋 Detaylı rapor: ${reportPath}\n`);
}

async function main() {
    console.log('\n🤖 Admin Panel Otomatik Crawler Başlatılıyor...\n');
    console.log('═════════════════════════════════════════════════\n');
    
    await createScreenshotDir();
    
    const browser = await puppeteer.launch({
        headless: CONFIG.headless,
        args: ['--no-sandbox', '--disable-setuid-sandbox'],
    });
    
    const page = await browser.newPage();
    
    await page.setViewport({ width: 1920, height: 1080 });
    
    await login(page);
    
    await page.goto(CONFIG.baseUrl + '/admin', { waitUntil: 'networkidle2' });
    
    const menuLinks = await extractMenuLinks(page);
    results.totalLinks = menuLinks.length;
    
    console.log('🚀 Test başlatılıyor...\n');
    console.log('═════════════════════════════════════════════════\n');
    
    for (const link of menuLinks) {
        await testPage(browser, link.url, link.text);
        
        await new Promise(resolve => setTimeout(resolve, 500));
    }
    
    await browser.close();
    
    console.log('═════════════════════════════════════════════════\n');
    console.log('📊 TEST TAMAMLANDI!\n');
    console.log(`Toplam: ${results.totalLinks}`);
    console.log(`Başarılı: ${results.successCount} ✅`);
    console.log(`Hatalı: ${results.errorCount} ❌`);
    console.log(`Başarı Oranı: ${((results.successCount / results.totalLinks) * 100).toFixed(2)}%\n`);
    
    await generateReport();
    
    console.log('✨ Tüm işlemler tamamlandı!\n');
    
    process.exit(results.errorCount > 0 ? 1 : 0);
}

main().catch(error => {
    console.error('💥 Kritik hata:', error);
    process.exit(1);
});

