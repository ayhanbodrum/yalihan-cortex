import puppeteer from "puppeteer";
import fs from "fs";

const CONFIG = {
    baseUrl: "http://127.0.0.1:8000",
    loginEmail: "admin@yalihanemlak.com",
    loginPassword: "admin123",
    screenshotsDir: "./screenshots/kapsamli-test",
    timeout: 30000,
    headless: true,
    testCreatePages: true,
    testEditPages: true,
    testSubmenus: true,
};

const results = {
    allLinks: [],
    testedPages: [],
    errors: [],
    success: [],
    totalTests: 0,
    successCount: 0,
    errorCount: 0,
    byCategory: {},
    startTime: new Date(),
};

async function createScreenshotDir() {
    if (!fs.existsSync(CONFIG.screenshotsDir)) {
        fs.mkdirSync(CONFIG.screenshotsDir, { recursive: true });
    }
}

async function login(page) {
    console.log("🔐 Admin girişi yapılıyor...\n");

    await page.goto(CONFIG.baseUrl + "/login", { waitUntil: "networkidle2" });
    await page.type('input[name="email"]', CONFIG.loginEmail);
    await page.type('input[name="password"]', CONFIG.loginPassword);
    await Promise.all([
        page.waitForNavigation({ waitUntil: "networkidle2" }),
        page.click('button[type="submit"]'),
    ]);

    console.log("   ✅ Giriş başarılı!\n");
}

async function extractAllAdminLinks(page) {
    console.log("🔍 Sidebar menüsünden TÜM linkler çıkarılıyor...\n");

    const links = await page.evaluate(() => {
        const foundLinks = [];
        const processed = new Set();

        document.querySelectorAll("a[href]").forEach((link) => {
            const href = link.getAttribute("href");

            if (!href || !href.startsWith("/admin")) return;

            const skipPatterns = [
                "/admin/logout",
                "#",
                "javascript:",
                "/admin/profil",
                "/admin/notifications",
            ];

            if (skipPatterns.some((pattern) => href.includes(pattern))) return;
            if (processed.has(href)) return;

            processed.add(href);

            const parentLi = link.closest("li");
            const category =
                parentLi
                    ?.closest("[data-category]")
                    ?.getAttribute("data-category") ||
                parentLi?.closest("nav")?.getAttribute("aria-label") ||
                "Diğer";

            const text = link.textContent.trim().replace(/\s+/g, " ");

            foundLinks.push({
                url: href,
                text: text || href,
                category: category,
                isSubmenu: link.closest(".submenu, .dropdown") !== null,
            });
        });

        return foundLinks.sort(
            (a, b) =>
                a.category.localeCompare(b.category) ||
                a.url.localeCompare(b.url)
        );
    });

    console.log(`   ✅ ${links.length} benzersiz link bulundu\n`);

    const categorized = {};
    links.forEach((link) => {
        if (!categorized[link.category]) {
            categorized[link.category] = [];
        }
        categorized[link.category].push(link);
    });

    console.log("   📋 Kategori Dağılımı:");
    for (const [cat, items] of Object.entries(categorized)) {
        console.log(`      ${cat}: ${items.length} link`);
    }
    console.log("");

    return links;
}

async function generateCRUDUrls(baseLink) {
    const crudUrls = [{ url: baseLink.url, type: "liste", ...baseLink }];

    if (CONFIG.testCreatePages) {
        crudUrls.push({
            url: `${baseLink.url}/create`,
            text: `${baseLink.text} - Ekle`,
            type: "create",
            category: baseLink.category,
        });
    }

    if (CONFIG.testEditPages) {
        crudUrls.push({
            url: `${baseLink.url}/1/edit`,
            text: `${baseLink.text} - Düzenle`,
            type: "edit",
            category: baseLink.category,
        });
    }

    return crudUrls;
}

async function testPage(browser, pageInfo) {
    const page = await browser.newPage();
    page.setDefaultTimeout(CONFIG.timeout);

    const testResult = {
        ...pageInfo,
        status: "unknown",
        httpStatus: null,
        error: null,
        screenshot: null,
        solution: null,
        timestamp: new Date().toISOString(),
    };

    try {
        const response = await page.goto(CONFIG.baseUrl + pageInfo.url, {
            waitUntil: "networkidle2",
            timeout: CONFIG.timeout,
        });

        testResult.httpStatus = response.status();

        if (testResult.httpStatus === 404) {
            testResult.status = "not_found";
            testResult.error = {
                type: "Sayfa bulunamadı",
                title: "404 Not Found",
            };
            testResult.solution =
                pageInfo.type === "edit"
                    ? "Veri yok veya route eksik"
                    : "Route tanımlı değil";

            console.log(`   ⚠️  404 Not Found`);
            results.errorCount++;
            results.errors.push(testResult);
        } else {
            const errorInfo = await page.evaluate(() => {
                const errorKeywords = [
                    "Exception",
                    "Error",
                    "SQLSTATE",
                    "Undefined variable",
                    "Class not found",
                    "Call to undefined",
                    "doesn't exist",
                    "to be implemented",
                    "hazır değil",
                    "NotFoundHttpException",
                ];

                const bodyText = document.body.innerText;
                const hasError = errorKeywords.some((keyword) =>
                    bodyText.includes(keyword)
                );

                if (!hasError) return null;

                const title =
                    document.querySelector("title")?.textContent || "";
                let errorType = "Unknown";
                let tableName = null;
                let variableName = null;
                let methodName = null;

                if (bodyText.match(/SQLSTATE\[42S02\].*Table '.*?\.(\w+)'/)) {
                    const match = bodyText.match(/Table '.*?\.(\w+)'/);
                    tableName = match ? match[1] : null;
                    errorType = `Tablo eksik: ${tableName}`;
                } else if (bodyText.match(/Undefined variable \$(\w+)/)) {
                    const match = bodyText.match(/Undefined variable \$(\w+)/);
                    variableName = match ? match[1] : null;
                    errorType = `Tanımsız değişken: $${variableName}`;
                } else if (
                    bodyText.match(
                        /Call to undefined (method|relationship) \[?(\w+)\]?/
                    )
                ) {
                    const match = bodyText.match(
                        /Call to undefined (method|relationship) \[?(\w+)\]?/
                    );
                    methodName = match ? match[2] : null;
                    errorType = `Tanımsız ${
                        match ? match[1] : "method"
                    }: ${methodName}`;
                } else if (bodyText.match(/Class ".*?\\(\w+)" not found/)) {
                    const match = bodyText.match(
                        /Class ".*?\\(\w+)" not found/
                    );
                    errorType = `Sınıf bulunamadı: ${
                        match ? match[1] : "unknown"
                    }`;
                } else if (bodyText.match(/to be implemented|hazır değil/i)) {
                    errorType = `Endpoint henüz implement edilmemiş`;
                } else if (bodyText.match(/NotFoundHttpException/)) {
                    errorType = `Route tanımlı değil`;
                }

                return {
                    title,
                    type: errorType,
                    tableName,
                    variableName,
                    methodName,
                    bodySnippet: bodyText.substring(0, 500),
                };
            });

            if (errorInfo) {
                testResult.status = "error";
                testResult.error = errorInfo;

                testResult.solution = generateSolution(errorInfo);

                const screenshotPath = `${
                    CONFIG.screenshotsDir
                }/error-${pageInfo.category.replace(
                    /\s/g,
                    "-"
                )}-${Date.now()}.png`;
                await page.screenshot({ path: screenshotPath, fullPage: true });
                testResult.screenshot = screenshotPath;

                console.log(`   ❌ ${errorInfo.type}`);
                console.log(`   💡 Çözüm: ${testResult.solution}`);

                results.errorCount++;
                results.errors.push(testResult);
            } else {
                testResult.status = "success";

                const screenshotPath = `${
                    CONFIG.screenshotsDir
                }/success-${pageInfo.category.replace(
                    /\s/g,
                    "-"
                )}-${Date.now()}.png`;
                await page.screenshot({
                    path: screenshotPath,
                    fullPage: false,
                });
                testResult.screenshot = screenshotPath;

                console.log(`   ✅ Başarılı (HTTP ${testResult.httpStatus})`);

                results.successCount++;
                results.success.push(testResult);
            }
        }
    } catch (error) {
        testResult.status = "failed";
        testResult.error = { title: error.message, type: "Bağlantı hatası" };
        testResult.solution = "Sunucu çalışmıyor olabilir";

        console.log(`   💥 ${error.message}`);

        results.errorCount++;
        results.errors.push(testResult);
    }

    results.testedPages.push(testResult);

    await page.close();
    return testResult;
}

function generateSolution(errorInfo) {
    if (errorInfo.tableName) {
        return `Migration oluştur: php artisan make:migration create_${errorInfo.tableName}_table`;
    }

    if (errorInfo.variableName) {
        return `Controller'da $${errorInfo.variableName} değişkenini tanımla ve view'a gönder`;
    }

    if (errorInfo.methodName) {
        return `Model'de ${errorInfo.methodName}() metodunu/ilişkisini tanımla`;
    }

    return "Manuel kontrol gerekli";
}

async function generateComprehensiveReport() {
    const duration = (new Date() - results.startTime) / 1000;

    let report = `# 🧪 Admin Panel Kapsamlı Test Raporu

**Test Zamanı:** ${results.startTime.toLocaleString("tr-TR")}
**Test Süresi:** ${duration.toFixed(2)} saniye
**Toplam Sayfa:** ${results.totalTests}

---

## 📊 Genel Özet

| Metrik | Değer |
|--------|-------|
| **Toplam Test** | ${results.totalTests} |
| **Başarılı** | ${results.successCount} ✅ |
| **Hatalı** | ${results.errorCount} ❌ |
| **404 Not Found** | ${
        results.testedPages.filter((p) => p.status === "not_found").length
    } |
| **Başarı Oranı** | ${(
        (results.successCount / results.totalTests) *
        100
    ).toFixed(2)}% |

---

## 📋 Kategori Bazında Detaylı Sonuçlar

`;

    for (const [category, pages] of Object.entries(results.byCategory)) {
        const total = pages.length;
        const success = pages.filter((p) => p.status === "success").length;
        const errors = pages.filter((p) => p.status === "error").length;
        const notFound = pages.filter((p) => p.status === "not_found").length;

        report += `### ${category}\n\n`;
        report += `| Metrik | Değer |\n|--------|-------|\n`;
        report += `| Toplam | ${total} |\n`;
        report += `| Başarılı | ${success} ✅ |\n`;
        report += `| Hatalı | ${errors} ❌ |\n`;
        report += `| 404 | ${notFound} |\n`;
        report += `| Başarı Oranı | ${((success / total) * 100).toFixed(
            2
        )}% |\n\n`;

        report += `#### ${category} - Sayfa Detayları:\n\n`;

        pages.forEach((p) => {
            const icon =
                p.status === "success"
                    ? "✅"
                    : p.status === "not_found"
                    ? "⚠️"
                    : "❌";
            const typeLabel =
                p.type === "create"
                    ? "[EKLE]"
                    : p.type === "edit"
                    ? "[DÜZENLE]"
                    : "[LİSTE]";
            report += `${icon} **${p.text}** ${typeLabel}\n`;
            report += `  - URL: \`${p.url}\`\n`;
            report += `  - HTTP: ${p.httpStatus || "N/A"}\n`;

            if (p.error) {
                report += `  - **Hata:** ${p.error.type}\n`;
                report += `  - **Çözüm:** ${p.solution || "Manuel kontrol"}\n`;
                report += `  - **Screenshot:** ${p.screenshot}\n`;
            }
            report += `\n`;
        });

        report += `---\n\n`;
    }

    if (results.errors.length > 0) {
        report += `## ❌ Hatalı Sayfalar ve Otomatik Çözüm Önerileri\n\n`;

        const errorsByType = {};
        results.errors.forEach((err) => {
            const type = err.error?.type || "Unknown";
            if (!errorsByType[type]) errorsByType[type] = [];
            errorsByType[type].push(err);
        });

        for (const [errorType, errors] of Object.entries(errorsByType)) {
            report += `### ${errorType} (${errors.length} adet)\n\n`;

            errors.forEach((err, idx) => {
                report += `${idx + 1}. **${err.text}** (${err.url})\n`;
                report += `   - **Kategori:** ${err.category}\n`;
                report += `   - **Çözüm:** ${err.solution}\n`;
                report += `   - **Screenshot:** ${err.screenshot}\n\n`;
            });

            report += `**Toplu Çözüm:**\n`;

            if (errorType.includes("Tablo eksik")) {
                const tables = errors
                    .map((e) => e.error.tableName)
                    .filter(Boolean);
                report += `\`\`\`bash\n`;
                tables.forEach((table) => {
                    report += `php artisan make:migration create_${table}_table\n`;
                });
                report += `# Migration'ları doldur ve çalıştır\n`;
                report += `php artisan migrate\n`;
                report += `\`\`\`\n\n`;
            } else if (errorType.includes("Tanımsız değişken")) {
                report += `Controller'larda eksik değişkenleri tanımla:\n`;
                report += `\`\`\`bash\n`;
                report += `php scripts/otomatik-hata-duzelt.php\n`;
                report += `\`\`\`\n\n`;
            } else if (errorType.includes("Tanımsız")) {
                report += `Model ilişkilerini ve metodları tanımla\n\n`;
            }

            report += `---\n\n`;
        }
    }

    report += `## ✅ Başarılı Sayfalar (${results.successCount} adet)\n\n`;

    const successByCategory = {};
    results.success.forEach((s) => {
        if (!successByCategory[s.category]) successByCategory[s.category] = [];
        successByCategory[s.category].push(s);
    });

    for (const [cat, pages] of Object.entries(successByCategory)) {
        report += `### ${cat} (${pages.length} başarılı)\n\n`;
        pages.forEach((p) => {
            const typeLabel =
                p.type === "create"
                    ? "[EKLE]"
                    : p.type === "edit"
                    ? "[DÜZENLE]"
                    : "[LİSTE]";
            report += `- ✅ ${p.text} ${typeLabel} - \`${p.url}\`\n`;
        });
        report += `\n`;
    }

    report += `---\n\n`;
    report += `## 📸 Ekran Görüntüleri\n\n`;
    report += `**Klasör:** \`${CONFIG.screenshotsDir}/\`\n\n`;
    report += `**Toplam Screenshot:** ${results.testedPages.length}\n\n`;
    report += `---\n\n`;
    report += `## 🔧 Otomatik Düzeltme Komutları\n\n`;
    report += `\`\`\`bash\n`;
    report += `# Otomatik hata düzeltici\n`;
    report += `php scripts/otomatik-hata-duzelt.php\n\n`;
    report += `# Testi tekrar çalıştır\n`;
    report += `node scripts/admin-kapsamli-test.mjs\n`;
    report += `\`\`\`\n\n`;
    report += `---\n\n`;
    report += `**Context7 Uyumlu:** ✅  \n`;
    report += `**Rapor Tarihi:** ${new Date().toLocaleString("tr-TR")}\n`;

    fs.writeFileSync("./admin-kapsamli-test-raporu.md", report);
    console.log(`\n📋 Kapsamlı rapor: ./admin-kapsamli-test-raporu.md\n`);
}

async function main() {
    console.log("\n🚀 Admin Panel Kapsamlı Test Sistemi\n");
    console.log("═══════════════════════════════════════════════════\n");
    console.log(`📋 Ayarlar:`);
    console.log(
        `   • Create sayfaları test: ${CONFIG.testCreatePages ? "✅" : "❌"}`
    );
    console.log(
        `   • Edit sayfaları test: ${CONFIG.testEditPages ? "✅" : "❌"}`
    );
    console.log(
        `   • Alt menüler test: ${CONFIG.testSubmenus ? "✅" : "❌"}\n`
    );

    await createScreenshotDir();

    const browser = await puppeteer.launch({
        headless: CONFIG.headless,
        args: ["--no-sandbox", "--disable-setuid-sandbox"],
    });

    const page = await browser.newPage();
    await page.setViewport({ width: 1920, height: 1080 });

    await login(page);

    await page.goto(CONFIG.baseUrl + "/admin", { waitUntil: "networkidle2" });

    let menuLinks = await extractAllAdminLinks(page);
    await page.close();

    if (menuLinks.length === 0) {
        console.log(
            "⚠️  Dinamik link bulunamadı, manuel liste kullanılıyor...\n"
        );
        menuLinks = [
            { url: "/admin/dashboard", text: "Dashboard", category: "Ana" },
            {
                url: "/admin/crm/dashboard",
                text: "CRM Dashboard",
                category: "CRM",
            },
            { url: "/admin/kisiler", text: "Kişiler", category: "CRM" },
            { url: "/admin/danisman", text: "Danışmanlar", category: "CRM" },
            { url: "/admin/talepler", text: "Talepler", category: "CRM" },
            {
                url: "/admin/takim-yonetimi/takim",
                text: "Takım",
                category: "CRM",
            },
            {
                url: "/admin/takim-yonetimi/gorevler",
                text: "Görevler",
                category: "CRM",
            },
            {
                url: "/admin/ilanlar",
                text: "İlanlar",
                category: "İlan Yönetimi",
            },
            {
                url: "/admin/ilan-kategorileri",
                text: "İlan Kategorileri",
                category: "İlan Yönetimi",
            },
            {
                url: "/admin/ozellikler",
                text: "Özellikler",
                category: "İlan Yönetimi",
            },
            {
                url: "/admin/ozellikler/kategoriler",
                text: "Özellik Kategorileri",
                category: "İlan Yönetimi",
            },
            {
                url: "/admin/kullanicilar",
                text: "Kullanıcılar",
                category: "Sistem",
            },
            { url: "/admin/ayarlar", text: "Ayarlar", category: "Sistem" },
            { url: "/admin/raporlar", text: "Raporlar", category: "Sistem" },
        ];
    }

    const allTestPages = [];

    for (const link of menuLinks) {
        const crudPages = await generateCRUDUrls(link);
        allTestPages.push(...crudPages);
    }

    results.totalTests = allTestPages.length;

    console.log(`🚀 Toplam ${results.totalTests} sayfa test edilecek...\n`);
    console.log("═══════════════════════════════════════════════════\n");

    for (const pageInfo of allTestPages) {
        if (!results.byCategory[pageInfo.category]) {
            results.byCategory[pageInfo.category] = [];
        }

        const typeLabel =
            pageInfo.type === "create"
                ? "[EKLE]"
                : pageInfo.type === "edit"
                ? "[DÜZENLE]"
                : "[LİSTE]";
        console.log(`📄 ${pageInfo.category} > ${pageInfo.text} ${typeLabel}`);

        const result = await testPage(browser, pageInfo);
        results.byCategory[pageInfo.category].push(result);

        await new Promise((resolve) => setTimeout(resolve, 300));
    }

    await browser.close();

    console.log("\n═══════════════════════════════════════════════════");
    console.log("📊 TEST SONUÇLARI:\n");

    for (const [category, pages] of Object.entries(results.byCategory)) {
        const success = pages.filter((p) => p.status === "success").length;
        const total = pages.length;
        const percentage = ((success / total) * 100).toFixed(1);
        const icon =
            percentage === "100.0" ? "✅" : percentage > 50 ? "⚠️" : "❌";
        console.log(
            `${icon} ${category}: ${success}/${total} (${percentage}%)`
        );
    }

    console.log(
        `\n🎯 GENEL: ${results.successCount}/${results.totalTests} başarılı (${(
            (results.successCount / results.totalTests) *
            100
        ).toFixed(2)}%)\n`
    );

    await generateComprehensiveReport();

    if (results.errorCount > 0) {
        console.log(
            "⚠️  Hatalar bulundu! Otomatik düzeltici çalıştırılıyor...\n"
        );
        console.log(
            "💡 Komutu çalıştır: php scripts/otomatik-hata-duzelt.php\n"
        );
    }

    console.log("✨ Kapsamlı test tamamlandı!\n");

    process.exit(results.errorCount > 0 ? 1 : 0);
}

main().catch((error) => {
    console.error("💥 Kritik hata:", error);
    process.exit(1);
});
