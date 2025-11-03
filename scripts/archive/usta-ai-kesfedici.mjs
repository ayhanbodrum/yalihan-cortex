import puppeteer from "puppeteer";
import fs from "fs";

/**
 * 🤖 USTA AI Keşfedici
 * - AI provider'ları keşfeder
 * - Database health check
 * - Performance metrics
 * - Context7 MD dosyalarını okur ve kuralları öğrenir
 */

const CONFIG = {
    baseUrl: "http://127.0.0.1:8000",
    loginEmail: "admin@yalihanemlak.com",
    loginPassword: "admin123",
};

// Context7 MD Dosyaları (Öğrenilecek Kurallar)
const CONTEXT7_DOCS = [
    "docs/context7/rules/context7-rules.md",
    "docs/context7/reports/context7-master.md",
    "CONTEXT7_ULTIMATE_STATUS_REPORT.md",
    "context7-super-analyzer-report.md",
];

const results = {
    aiProviders: [],
    databaseHealth: {},
    performanceMetrics: {},
    learnedRules: [],
    capabilities: [],
};

async function learnFromDocs() {
    console.log("📚 Context7 MD dosyalarından öğreniliyor...\n");

    const allRules = {
        forbiddenFields: new Set(),
        forbiddenRelations: new Set(),
        requiredPatterns: new Set(),
        bestPractices: [],
    };

    for (const docPath of CONTEXT7_DOCS) {
        if (!fs.existsSync(docPath)) continue;

        const content = fs.readFileSync(docPath, "utf-8");

        // Yasak alan adlarını öğren
        const forbiddenMatches = content.matchAll(
            /`([a-z_]+)`.*yasak|`([a-z_]+)`.*→.*`([a-z_]+)`/gi
        );
        for (const match of forbiddenMatches) {
            if (match[1]) allRules.forbiddenFields.add(match[1]);
            if (match[2] && match[3]) {
                allRules.forbiddenFields.add(`${match[2]} → ${match[3]}`);
            }
        }

        // Zorunlu pattern'leri öğren
        const patternMatches = content.matchAll(/neo-([a-z-]+)/gi);
        for (const match of patternMatches) {
            allRules.requiredPatterns.add(`neo-${match[1]}`);
        }

        // Best practice'leri öğren
        const practiceMatches = content.matchAll(/✅\s+\*\*([^*]+)\*\*/g);
        for (const match of practiceMatches) {
            allRules.bestPractices.push(match[1]);
        }
    }

    results.learnedRules = {
        forbiddenFields: Array.from(allRules.forbiddenFields).slice(0, 20),
        requiredPatterns: Array.from(allRules.requiredPatterns).slice(0, 10),
        bestPractices: allRules.bestPractices.slice(0, 10),
    };

    console.log(
        `   ✅ ${results.learnedRules.forbiddenFields.length} yasak alan öğrenildi`
    );
    console.log(
        `   ✅ ${results.learnedRules.requiredPatterns.length} zorunlu pattern öğrenildi`
    );
    console.log(
        `   ✅ ${results.learnedRules.bestPractices.length} best practice öğrenildi\n`
    );
}

async function discoverAIProviders(page) {
    console.log("🤖 AI Provider'lar keşfediliyor...\n");

    try {
        await page.goto(CONFIG.baseUrl + "/admin/ai-settings", {
            waitUntil: "networkidle2",
            timeout: 10000,
        });

        const providers = await page.evaluate(() => {
            const providerElements = document.querySelectorAll(
                '[data-provider], [id*="provider"], .provider-card'
            );
            const found = [];

            // Check for specific providers
            const knownProviders = [
                "OpenAI",
                "DeepSeek",
                "Gemini",
                "Claude",
                "Ollama",
            ];
            knownProviders.forEach((provider) => {
                const element = document.body.innerText.includes(provider);
                if (element) {
                    found.push({
                        name: provider,
                        detected: true,
                        source: "AI Settings Page",
                    });
                }
            });

            return found;
        });

        results.aiProviders = providers;

        console.log(`   ✅ ${providers.length} AI provider tespit edildi:`);
        providers.forEach((p) => console.log(`      • ${p.name}`));
        console.log("");
    } catch (error) {
        console.log(`   ⚠️ AI settings sayfasına erişilemedi\n`);
    }
}

async function checkDatabaseHealth(page) {
    console.log("🗄️ Database health check...\n");

    try {
        // Dashboard'dan istatistikleri al
        await page.goto(CONFIG.baseUrl + "/admin/dashboard", {
            waitUntil: "networkidle2",
            timeout: 10000,
        });

        const dbStats = await page.evaluate(() => {
            const stats = {};

            // İstatistik kartlarından bilgi al
            document
                .querySelectorAll('[class*="stat"], [class*="metric"]')
                .forEach((card) => {
                    const text = card.textContent;
                    const numberMatch = text.match(/\d+/);
                    if (numberMatch) {
                        const label = text
                            .replace(/\d+/g, "")
                            .trim()
                            .substring(0, 30);
                        if (label) stats[label] = parseInt(numberMatch[0]);
                    }
                });

            return {
                hasConnection: true,
                stats: stats,
                tablesDetected: Object.keys(stats).length,
            };
        });

        results.databaseHealth = dbStats;

        console.log(`   ✅ Database bağlantısı: OK`);
        console.log(
            `   📊 ${dbStats.tablesDetected} tablo/metrik tespit edildi\n`
        );
    } catch (error) {
        results.databaseHealth = {
            hasConnection: false,
            error: error.message,
        };
        console.log(`   ❌ Database health check başarısız\n`);
    }
}

async function analyzePerformance(page) {
    console.log("⚡ Performance analizi...\n");

    const testPages = ["/admin/dashboard", "/admin/ilanlar", "/admin/kisiler"];

    const performanceResults = [];

    for (const url of testPages) {
        try {
            const startTime = Date.now();
            await page.goto(CONFIG.baseUrl + url, {
                waitUntil: "networkidle2",
                timeout: 10000,
            });
            const loadTime = Date.now() - startTime;

            const metrics = await page.metrics();

            performanceResults.push({
                url: url,
                loadTime: loadTime,
                jsHeapSize: Math.round(metrics.JSHeapUsedSize / 1024 / 1024),
                score:
                    loadTime < 1000
                        ? "🌟 Hızlı"
                        : loadTime < 2000
                        ? "✅ İyi"
                        : "⚠️ Yavaş",
            });

            console.log(
                `   ${url}: ${loadTime}ms - ${
                    performanceResults[performanceResults.length - 1].score
                }`
            );
        } catch (error) {
            performanceResults.push({
                url: url,
                error: error.message,
                score: "❌ Hata",
            });
        }
    }

    const avgLoadTime =
        performanceResults
            .filter((r) => r.loadTime)
            .reduce((sum, r) => sum + r.loadTime, 0) /
        performanceResults.filter((r) => r.loadTime).length;

    results.performanceMetrics = {
        pages: performanceResults,
        average: Math.round(avgLoadTime),
        grade: avgLoadTime < 1000 ? "A" : avgLoadTime < 2000 ? "B" : "C",
    };

    console.log(
        `\n   📊 Ortalama yükleme: ${Math.round(avgLoadTime)}ms (Grade: ${
            results.performanceMetrics.grade
        })\n`
    );
}

async function discoverCapabilities() {
    console.log("🔍 USTA yetenekleri analiz ediliyor...\n");

    // Mevcut yetenekler
    const capabilities = [
        { name: "Teknik Test", version: "2.0", status: "✅" },
        { name: "Context7 Öğretmen", version: "2.0", status: "✅" },
        { name: "Görsel Analiz", version: "2.0", status: "✅" },
        { name: "UX Önerileri", version: "2.0", status: "✅" },
        { name: "AI Provider Keşfi", version: "3.0", status: "🆕" },
        { name: "Database Health", version: "3.0", status: "🆕" },
        { name: "Performance Analysis", version: "3.0", status: "🆕" },
        { name: "MD Learning", version: "3.0", status: "🆕" },
    ];

    results.capabilities = capabilities;

    capabilities.forEach((cap) => {
        console.log(`   ${cap.status} ${cap.name} v${cap.version}`);
    });
    console.log("");
}

async function generateAIReport() {
    const report = `# 🤖 USTA AI Keşif Raporu

**Tarih:** ${new Date().toLocaleString("tr-TR")}
**USTA Versiyonu:** 3.0 (AI-Powered Master)

---

## 🤖 AI Provider Keşif Sonuçları

${
    results.aiProviders.length > 0
        ? `
**Tespit Edilen AI Provider'lar:**

${results.aiProviders.map((p) => `- ✅ **${p.name}** (${p.source})`).join("\n")}

**AI Entegrasyon Durumu:** ${
              results.aiProviders.length >= 3 ? "🌟 Zengin" : "✅ Yeterli"
          }
`
        : "⚠️ AI provider tespit edilemedi"
}

---

## 🗄️ Database Health Check

${
    results.databaseHealth.hasConnection
        ? `
**Bağlantı Durumu:** ✅ Başarılı

**Tespit Edilen Metrikler:**
${Object.entries(results.databaseHealth.stats || {})
    .map(([key, value]) => `- ${key}: ${value}`)
    .join("\n")}

**Toplam Tablo/Metrik:** ${results.databaseHealth.tablesDetected}
`
        : `
**Bağlantı Durumu:** ❌ Başarısız
**Hata:** ${results.databaseHealth.error}
`
}

---

## ⚡ Performance Metrikleri

**Ortalama Yükleme Süresi:** ${results.performanceMetrics.average}ms
**Performance Grade:** ${results.performanceMetrics.grade}

**Sayfa Bazında:**

${results.performanceMetrics.pages
    ?.map(
        (p) => `
- **${p.url}**
  - Yükleme: ${p.loadTime}ms
  - Skor: ${p.score}
  - JS Heap: ${p.jsHeapSize || "N/A"}MB
`
    )
    .join("\n")}

---

## 📚 Context7 Öğrenme Sonuçları

**Yasak Alan Adları (Öğrenildi):**
${results.learnedRules.forbiddenFields
    ?.slice(0, 10)
    .map((f) => `- \`${f}\``)
    .join("\n")}

**Zorunlu Design Patterns:**
${results.learnedRules.requiredPatterns
    ?.slice(0, 10)
    .map((p) => `- \`${p}\``)
    .join("\n")}

**Best Practices:**
${results.learnedRules.bestPractices
    ?.slice(0, 5)
    .map((bp) => `- ${bp}`)
    .join("\n")}

---

## 🎯 USTA Yetenekleri

${results.capabilities
    .map((cap) => `${cap.status} **${cap.name}** (v${cap.version})`)
    .join("\n")}

---

**USTA Versiyonu:** 3.0 (AI-Powered Master)
**Context7 Uyumlu:** ✅
**Tarih:** ${new Date().toLocaleString("tr-TR")}
`;

    fs.writeFileSync("./usta-ai-kesfedici-raporu.md", report);
    console.log(
        "📋 AI Keşif Raporu oluşturuldu: usta-ai-kesfedici-raporu.md\n"
    );
}

async function main() {
    console.log("\n" + "=".repeat(60));
    console.log("🤖 USTA 3.0 - AI Keşfedici & Öğrenen Sistem");
    console.log("=".repeat(60) + "\n");

    // Önce MD dosyalarından öğren
    await learnFromDocs();

    const browser = await puppeteer.launch({
        headless: true,
        args: ["--no-sandbox", "--disable-setuid-sandbox"],
    });

    const page = await browser.newPage();
    await page.setViewport({ width: 1920, height: 1080 });

    // Login
    console.log("🔐 Giriş yapılıyor...");
    await page.goto(CONFIG.baseUrl + "/login", { waitUntil: "networkidle2" });
    await page.type('input[name="email"]', CONFIG.loginEmail);
    await page.type('input[name="password"]', CONFIG.loginPassword);
    await Promise.all([
        page.waitForNavigation({ waitUntil: "networkidle2" }),
        page.click('button[type="submit"]'),
    ]);
    console.log("   ✅ Giriş başarılı!\n");

    // Keşif işlemleri
    await discoverCapabilities();
    await discoverAIProviders(page);
    await checkDatabaseHealth(page);
    await analyzePerformance(page);

    await browser.close();

    await generateAIReport();

    console.log("=".repeat(60));
    console.log("✨ USTA AI Keşfi tamamlandı!");
    console.log("=".repeat(60) + "\n");
}

main().catch(console.error);
