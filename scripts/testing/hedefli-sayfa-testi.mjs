import puppeteer from "puppeteer";
import fs from "fs";

const CONFIG = {
    baseUrl: "http://127.0.0.1:8000",
    loginEmail: "admin@yalihanemlak.com",
    loginPassword: "admin123",
    screenshotsDir: "./screenshots/hedefli-test",
    timeout: 30000,
};

const HEDEF_SAYFALAR = [
    { url: "/admin/danisman/create", name: "Danışman Create" },
    { url: "/admin/notifications", name: "Bildirimler" },
    { url: "/admin/eslesmeler", name: "Eşleşmeler" },
    { url: "/admin/takim-yonetimi/takim", name: "Takım Yönetimi" },
    { url: "/admin/ilan-kategorileri", name: "İlan Kategorileri" },
    { url: "/admin/kisiler", name: "Kişiler" },
    { url: "/admin/ilanlar/stable-create", name: "İlan Stable Create" },
    { url: "/admin/crm", name: "CRM Dashboard" },
    { url: "/admin/harita", name: "Harita" },
    { url: "/admin/smart-calculator", name: "Smart Calculator" },
];

const results = {
    sayfalar: [],
    hatalar: [],
    tasarimSorunlari: [],
};

async function createDir() {
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

async function testSayfa(browser, sayfaInfo) {
    const page = await browser.newPage();
    await page.setViewport({ width: 1920, height: 1080 });

    const sonuc = {
        ...sayfaInfo,
        httpStatus: null,
        hatalar: [],
        tasarimSorunlari: [],
        screenshot: null,
        timestamp: new Date().toISOString(),
    };

    try {
        console.log(`📄 Test ediliyor: ${sayfaInfo.name}`);
        console.log(`   URL: ${sayfaInfo.url}`);

        const response = await page.goto(CONFIG.baseUrl + sayfaInfo.url, {
            waitUntil: "networkidle2",
            timeout: CONFIG.timeout,
        });

        sonuc.httpStatus = response.status();
        console.log(`   HTTP: ${sonuc.httpStatus}`);

        // Hata kontrolü
        const pageAnalysis = await page.evaluate(() => {
            const hatalar = [];
            const tasarimSorunlari = [];
            const bodyText = document.body.innerText;

            // Hata tespiti
            const errorPatterns = [
                { pattern: /Exception|Error|SQLSTATE/, tip: "PHP Exception" },
                {
                    pattern: /Undefined variable \$(\w+)/,
                    tip: "Undefined Variable",
                },
                {
                    pattern: /Table '.*?\.(\w+)' doesn't exist/,
                    tip: "Tablo Eksik",
                },
                { pattern: /Column '(\w+)' not found/, tip: "Kolon Eksik" },
                {
                    pattern: /to be implemented|hazır değil/i,
                    tip: "Implement Edilmemiş",
                },
                { pattern: /Method.*does not exist/, tip: "Method Bulunamadı" },
            ];

            errorPatterns.forEach(({ pattern, tip }) => {
                if (pattern.test(bodyText)) {
                    const match = bodyText.match(pattern);
                    hatalar.push({
                        tip: tip,
                        detay: match ? match[0] : tip,
                    });
                }
            });

            // Tasarım sorunları tespiti
            const elements = {
                buttons: document.querySelectorAll("button").length,
                inputs: document.querySelectorAll("input").length,
                tables: document.querySelectorAll("table").length,
                cards: document.querySelectorAll(".card, .neo-card").length,
            };

            // Tailwind/Neo Design check
            const hasTailwind =
                document.querySelector('[class*="bg-"]') !== null;
            const hasNeoClasses =
                document.querySelector('[class*="neo-"]') !== null;

            if (!hasTailwind && !hasNeoClasses) {
                tasarimSorunlari.push(
                    "Tailwind/Neo Design System kullanılmamış"
                );
            }

            // Responsive kontrol
            const hasResponsive =
                document.querySelector('[class*="md:"], [class*="lg:"]') !==
                null;
            if (!hasResponsive) {
                tasarimSorunlari.push("Responsive design eksik");
            }

            // Dark mode kontrol
            const hasDarkMode =
                document.querySelector('[class*="dark:"]') !== null;
            if (!hasDarkMode) {
                tasarimSorunlari.push("Dark mode desteği yok");
            }

            return {
                hatalar,
                tasarimSorunlari,
                elements,
                hasTailwind,
                hasNeoClasses,
            };
        });

        sonuc.hatalar = pageAnalysis.hatalar;
        sonuc.tasarimSorunlari = pageAnalysis.tasarimSorunlari;

        // Screenshot
        const screenshotName = `${sayfaInfo.name.replace(
            /\s/g,
            "-"
        )}-${Date.now()}.png`;
        const screenshotPath = `${CONFIG.screenshotsDir}/${screenshotName}`;
        await page.screenshot({ path: screenshotPath, fullPage: true });
        sonuc.screenshot = screenshotPath;

        if (sonuc.hatalar.length > 0) {
            console.log(`   ❌ ${sonuc.hatalar.length} hata bulundu`);
            sonuc.hatalar.forEach((h) =>
                console.log(`      - ${h.tip}: ${h.detay.substring(0, 50)}...`)
            );
            results.hatalar.push(sonuc);
        } else {
            console.log(`   ✅ Hata yok`);
        }

        if (sonuc.tasarimSorunlari.length > 0) {
            console.log(
                `   ⚠️  ${sonuc.tasarimSorunlari.length} tasarım sorunu`
            );
            sonuc.tasarimSorunlari.forEach((s) => console.log(`      - ${s}`));
            results.tasarimSorunlari.push(sonuc);
        }

        console.log(`   📸 Screenshot: ${screenshotPath}\n`);
    } catch (error) {
        sonuc.hatalar.push({ tip: "Bağlantı Hatası", detay: error.message });
        console.log(`   💥 Hata: ${error.message}\n`);
    }

    results.sayfalar.push(sonuc);
    await page.close();
    return sonuc;
}

async function generateReport() {
    let report = `# 🎯 Hedefli Sayfa Test Raporu

**Test Zamanı:** ${new Date().toLocaleString("tr-TR")}
**Toplam Sayfa:** ${results.sayfalar.length}
**Hatalı Sayfa:** ${results.hatalar.length}
**Tasarım Sorunu:** ${results.tasarimSorunlari.length}

---

## 📊 Sayfa Bazında Detaylar

`;

    results.sayfalar.forEach((s) => {
        const icon = s.hatalar.length === 0 ? "✅" : "❌";
        report += `### ${icon} ${s.name}\n\n`;
        report += `- **URL:** \`${s.url}\`\n`;
        report += `- **HTTP:** ${s.httpStatus || "N/A"}\n`;
        report += `- **Screenshot:** ${s.screenshot}\n\n`;

        if (s.hatalar.length > 0) {
            report += `**🐛 Hatalar:**\n\n`;
            s.hatalar.forEach((h) => {
                report += `- **${h.tip}:** ${h.detay}\n`;
                report += `  - **Çözüm:** ${generateSolution(h)}\n\n`;
            });
        }

        if (s.tasarimSorunlari.length > 0) {
            report += `**🎨 Tasarım Sorunları:**\n\n`;
            s.tasarimSorunlari.forEach((t) => {
                report += `- ${t}\n`;
            });
            report += `\n`;
        }

        report += `---\n\n`;
    });

    report += `## 🔧 Otomatik Çözüm Komutları\n\n`;
    report += `\`\`\`bash\n`;
    report += `# Hataları otomatik düzelt\n`;
    report += `php scripts/hedefli-hata-duzelt.php\n\n`;
    report += `# Cache temizle\n`;
    report += `php artisan cache:clear && php artisan view:clear\n\n`;
    report += `# Tekrar test\n`;
    report += `node scripts/hedefli-sayfa-testi.mjs\n`;
    report += `\`\`\`\n\n`;

    report += `---\n\n**Context7 Uyumlu:** ✅\n`;
    report += `**Tarih:** ${new Date().toLocaleString("tr-TR")}\n`;

    fs.writeFileSync("./hedefli-sayfa-test-raporu.md", report);
    console.log("\n📋 Rapor oluşturuldu: hedefli-sayfa-test-raporu.md\n");
}

function generateSolution(hata) {
    if (hata.tip === "Tablo Eksik") {
        const match = hata.detay.match(/Table '.*?\.(\w+)'/);
        const tableName = match ? match[1] : "unknown";
        return `php artisan make:migration create_${tableName}_table`;
    }

    if (hata.tip === "Undefined Variable") {
        const match = hata.detay.match(/\$(\w+)/);
        const varName = match ? match[1] : "unknown";
        return `Controller'da $${varName} değişkenini tanımla ve compact() ile gönder`;
    }

    if (hata.tip === "Implement Edilmemiş") {
        return "View dosyası oluştur veya controller metodunu implement et";
    }

    if (hata.tip === "Method Bulunamadı") {
        return "Controller'da ilgili metodu tanımla";
    }

    return "Manuel kontrol gerekli";
}

async function main() {
    console.log("\n🎯 Hedefli Sayfa Test Sistemi\n");
    console.log("═══════════════════════════════════════\n");
    console.log(`📋 Test edilecek sayfa: ${HEDEF_SAYFALAR.length}\n`);

    await createDir();

    const browser = await puppeteer.launch({
        headless: false, // Kullanıcı görebilsin
        args: [
            "--no-sandbox",
            "--disable-setuid-sandbox",
            "--window-size=1920,1080",
        ],
    });

    const page = await browser.newPage();
    await page.setViewport({ width: 1920, height: 1080 });

    await login(page);
    await page.close();

    for (const sayfaInfo of HEDEF_SAYFALAR) {
        await testSayfa(browser, sayfaInfo);
        await new Promise((resolve) => setTimeout(resolve, 1000)); // 1 saniye bekle
    }

    await browser.close();

    await generateReport();

    console.log("═══════════════════════════════════════");
    console.log("📊 ÖZET:\n");
    console.log(`✅ Toplam: ${results.sayfalar.length}`);
    console.log(`❌ Hatalı: ${results.hatalar.length}`);
    console.log(`⚠️  Tasarım Sorunu: ${results.tasarimSorunlari.length}`);
    console.log("\n✨ Test tamamlandı!\n");
}

main().catch((error) => {
    console.error("💥 Kritik hata:", error);
    process.exit(1);
});
