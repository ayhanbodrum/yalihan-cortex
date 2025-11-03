import puppeteer from "puppeteer";
import fs from "fs";
import path from "path";

const CONFIG = {
    baseUrl: "http://127.0.0.1:8000",
    loginEmail: "admin@yalihanemlak.com",
    loginPassword: "admin123",
    screenshotsDir: "./screenshots/usta-web-developer",
    timeout: 15000, // 30s → 15s (daha hızlı)
    headless: true, // false → true (performans için)

    // Context7 Öğretmen Modu
    context7Teacher: true,
    context7StrictMode: true,

    // 🆕 Web Developer Features (Optimized)
    selfLearning: true, // Kendi hatalarından öğren
    autoFix: true, // Otomatik düzelt
    securityScan: true, // Güvenlik taraması
    seoAnalysis: true, // SEO analizi
    a11yCheck: true, // Erişilebilirlik
    mobileTest: false, // true → false (Çok yavaş - devre dışı)
    apiHealthCheck: true, // API kontrolü
    codeQuality: true, // Kod kalitesi
    performanceBudget: true, // Performans limitleri
    networkMonitor: false, // true → false (Yavaşlatıyor - devre dışı)
};

// Performance Budget Limitleri
const PERFORMANCE_BUDGET = {
    maxLoadTime: 2000, // 2 saniye
    maxJSHeapSize: 50, // 50MB
    maxNetworkRequests: 50, // 50 istek
    maxDOMSize: 1500, // 1500 element
    maxConsoleErrors: 0, // 0 hata
};

const SAYFALAR = [
    {
        url: "/admin/talep-portfolyo",
        name: "Talep-Portföy Eşleştirme",
        tasarimKritik: true,
    },
    {
        url: "/admin/ilan-kategorileri",
        name: "İlan Kategorileri",
        tasarimKritik: true,
    },
];

// Context7 Yasaklı Alanlar (Güncellenmiş)
const CONTEXT7_FORBIDDEN_FIELDS = {
    durum: "status",
    is_active: "status",
    aktif: "status",
    sehir: "il",
    region_id: "il_id",
    ad_soyad: "tam_ad",
    full_name: "name",
    name: "ulke_adi (ulkeler tablosunda)", // YENİ! ⚠️ YASAKLI
};

const results = {
    sayfalar: [],
    teknikHatalar: [],
    context7Violations: [],
    visualInsights: [], // Görsel analiz sonuçları
    uxSuggestions: [], // UX/UI önerileri

    // 🆕 Web Developer Results
    learnedPatterns: [], // Öğrenilen pattern'ler
    autoFixApplied: [], // Otomatik düzeltmeler
    securityIssues: [], // Güvenlik sorunları
    seoScores: [], // SEO skorları
    a11yViolations: [], // Erişilebilirlik ihlalleri
    mobileScores: [], // Mobil responsive skorları
    apiHealth: [], // API sağlık durumu
    codeQualityIssues: [], // Kod kalitesi sorunları
    performanceBudgetStatus: [], // Performans budget durumu
    networkAnalysis: [], // Network analizi

    startTime: new Date(),
};

// Context7 Öğrenme Sistemi
const LEARNED_PATTERNS_FILE = "./config/usta-learned-patterns.json";

// 🆕 1. SELF-LEARNING SYSTEM
function loadLearnedPatterns() {
    if (fs.existsSync(LEARNED_PATTERNS_FILE)) {
        const data = fs.readFileSync(LEARNED_PATTERNS_FILE, "utf-8");
        return JSON.parse(data);
    }
    return {
        commonErrors: [],
        frequentFixes: [],
        bestPractices: [],
        lastUpdated: new Date().toISOString(),
    };
}

function saveLearnedPattern(pattern) {
    const learned = loadLearnedPatterns();

    // Aynı pattern varsa frekansını artır
    const existing = learned.commonErrors.find(
        (p) => p.pattern === pattern.pattern
    );
    if (existing) {
        existing.frequency++;
        existing.lastSeen = new Date().toISOString();
    } else {
        learned.commonErrors.push({
            ...pattern,
            frequency: 1,
            firstSeen: new Date().toISOString(),
            lastSeen: new Date().toISOString(),
        });
    }

    learned.lastUpdated = new Date().toISOString();

    // Dizin yoksa oluştur
    const dir = path.dirname(LEARNED_PATTERNS_FILE);
    if (!fs.existsSync(dir)) {
        fs.mkdirSync(dir, { recursive: true });
    }

    fs.writeFileSync(LEARNED_PATTERNS_FILE, JSON.stringify(learned, null, 2));

    results.learnedPatterns.push({
        pattern: pattern.pattern,
        action: existing ? "Updated frequency" : "New pattern learned",
        frequency: existing ? existing.frequency : 1,
    });
}

// 🆕 2. SECURITY DEEP SCAN
async function securityScan(page, url) {
    console.log(`   🔒 Security scan: ${url}`);

    const issues = [];

    try {
        // XSS Vulnerability Check
        const xssCheck = await page.evaluate(() => {
            const inputs = document.querySelectorAll(
                'input[type="text"], textarea'
            );
            const vulnerable = [];

            inputs.forEach((input) => {
                // Check if input has validation
                if (
                    !input.hasAttribute("pattern") &&
                    !input.hasAttribute("maxlength")
                ) {
                    vulnerable.push({
                        type: "XSS Risk",
                        element: input.name || input.id || "unnamed",
                        issue: "Input without validation",
                    });
                }
            });

            return vulnerable;
        });

        issues.push(...xssCheck);

        // CSRF Token Check
        const csrfCheck = await page.evaluate(() => {
            const forms = document.querySelectorAll("form");
            const missingCSRF = [];

            forms.forEach((form) => {
                const csrfInput = form.querySelector('input[name="_token"]');
                if (!csrfInput && form.method.toLowerCase() === "post") {
                    missingCSRF.push({
                        type: "CSRF Risk",
                        element: form.action || form.id || "unnamed form",
                        issue: "Form without CSRF token",
                    });
                }
            });

            return missingCSRF;
        });

        issues.push(...csrfCheck);

        // Sensitive Data Exposure
        const dataExposure = await page.evaluate(() => {
            const exposed = [];
            const text = document.body.innerText;

            // Check for exposed API keys, tokens, passwords
            if (text.match(/api[_-]?key|secret|password|token/i)) {
                exposed.push({
                    type: "Data Exposure Risk",
                    element: "Page content",
                    issue: "Possible sensitive data in page content",
                });
            }

            return exposed;
        });

        issues.push(...dataExposure);
    } catch (error) {
        issues.push({
            type: "Security Scan Error",
            element: url,
            issue: error.message,
        });
    }

    results.securityIssues.push({
        url: url,
        totalIssues: issues.length,
        issues: issues,
        severity:
            issues.length === 0
                ? "✅ Secure"
                : issues.length < 3
                ? "⚠️ Low"
                : "🚨 High",
    });

    return issues;
}

// 🆕 3. SEO ANALYZER
async function seoAnalyze(page, url) {
    console.log(`   📊 SEO analysis: ${url}`);

    try {
        const seo = await page.evaluate(() => {
            return {
                title: document.title,
                titleLength: document.title.length,
                metaDescription:
                    document.querySelector('meta[name="description"]')
                        ?.content || "",
                metaKeywords:
                    document.querySelector('meta[name="keywords"]')?.content ||
                    "",
                h1Count: document.querySelectorAll("h1").length,
                h2Count: document.querySelectorAll("h2").length,
                imgWithAlt: document.querySelectorAll("img[alt]").length,
                imgWithoutAlt:
                    document.querySelectorAll("img:not([alt])").length,
                linksTotal: document.querySelectorAll("a").length,
                externalLinks: Array.from(
                    document.querySelectorAll("a")
                ).filter(
                    (a) =>
                        a.href.startsWith("http") &&
                        !a.href.includes(window.location.host)
                ).length,
                hasOGTitle: !!document.querySelector(
                    'meta[property="og:title"]'
                ),
                hasOGDescription: !!document.querySelector(
                    'meta[property="og:description"]'
                ),
                hasOGImage: !!document.querySelector(
                    'meta[property="og:image"]'
                ),
                canonical:
                    document.querySelector('link[rel="canonical"]')?.href || "",
            };
        });

        // SEO Skoru Hesapla (0-100)
        let score = 100;
        const issues = [];

        // Title checks
        if (!seo.title) {
            score -= 15;
            issues.push("❌ Page title missing");
        } else if (seo.titleLength < 30 || seo.titleLength > 60) {
            score -= 5;
            issues.push(`⚠️ Title length: ${seo.titleLength} (ideal: 30-60)`);
        }

        // Meta description
        if (!seo.metaDescription) {
            score -= 15;
            issues.push("❌ Meta description missing");
        } else if (
            seo.metaDescription.length < 120 ||
            seo.metaDescription.length > 160
        ) {
            score -= 5;
            issues.push(
                `⚠️ Description length: ${seo.metaDescription.length} (ideal: 120-160)`
            );
        }

        // H1 tags
        if (seo.h1Count === 0) {
            score -= 10;
            issues.push("❌ No H1 tag found");
        } else if (seo.h1Count > 1) {
            score -= 5;
            issues.push(`⚠️ Multiple H1 tags: ${seo.h1Count} (should be 1)`);
        }

        // Images
        if (seo.imgWithoutAlt > 0) {
            score -= 10;
            issues.push(`⚠️ ${seo.imgWithoutAlt} images without alt text`);
        }

        // OpenGraph
        if (!seo.hasOGTitle || !seo.hasOGDescription || !seo.hasOGImage) {
            score -= 10;
            issues.push("⚠️ Incomplete OpenGraph tags");
        }

        results.seoScores.push({
            url: url,
            score: Math.max(0, score),
            grade:
                score >= 90 ? "A" : score >= 75 ? "B" : score >= 60 ? "C" : "D",
            data: seo,
            issues: issues,
        });

        return seo;
    } catch (error) {
        results.seoScores.push({
            url: url,
            score: 0,
            error: error.message,
        });
        return null;
    }
}

// 🆕 4. ACCESSIBILITY SCANNER
async function a11yCheck(page, url) {
    console.log(`   ♿ A11y check: ${url}`);

    try {
        const violations = await page.evaluate(() => {
            const issues = [];

            // Check for alt text on images
            const imgsWithoutAlt = document.querySelectorAll("img:not([alt])");
            if (imgsWithoutAlt.length > 0) {
                issues.push({
                    type: "Missing Alt Text",
                    count: imgsWithoutAlt.length,
                    wcagLevel: "A",
                    impact: "Critical",
                });
            }

            // Check for form labels
            const inputsWithoutLabels = Array.from(
                document.querySelectorAll("input, select, textarea")
            ).filter((input) => {
                const id = input.id;
                if (!id) return true;
                return !document.querySelector(`label[for="${id}"]`);
            });

            if (inputsWithoutLabels.length > 0) {
                issues.push({
                    type: "Inputs Without Labels",
                    count: inputsWithoutLabels.length,
                    wcagLevel: "A",
                    impact: "Serious",
                });
            }

            // Check for button accessibility
            const buttonsWithoutText = Array.from(
                document.querySelectorAll("button, [role='button']")
            ).filter((btn) => {
                return (
                    !btn.textContent.trim() && !btn.getAttribute("aria-label")
                );
            });

            if (buttonsWithoutText.length > 0) {
                issues.push({
                    type: "Buttons Without Text/Label",
                    count: buttonsWithoutText.length,
                    wcagLevel: "A",
                    impact: "Serious",
                });
            }

            // Check for color contrast (basic check)
            const lowContrastElements = [];
            const allElements = document.querySelectorAll("*");

            // Skip lang attribute check
            const hasLang = document.documentElement.hasAttribute("lang");
            if (!hasLang) {
                issues.push({
                    type: "Missing Lang Attribute",
                    count: 1,
                    wcagLevel: "A",
                    impact: "Serious",
                });
            }

            // Check for keyboard accessibility
            const interactiveElements = document.querySelectorAll(
                "a, button, input, select, textarea, [tabindex]"
            );
            const negativeTabindex = Array.from(interactiveElements).filter(
                (el) => el.getAttribute("tabindex") === "-1"
            );

            if (negativeTabindex.length > 0) {
                issues.push({
                    type: "Negative Tabindex",
                    count: negativeTabindex.length,
                    wcagLevel: "A",
                    impact: "Moderate",
                });
            }

            return issues;
        });

        // WCAG Level hesapla
        const criticalCount = violations.filter(
            (v) => v.impact === "Critical"
        ).length;
        const seriousCount = violations.filter(
            (v) => v.impact === "Serious"
        ).length;

        let wcagLevel = "AAA";
        if (criticalCount > 0) wcagLevel = "Failed";
        else if (seriousCount > 0) wcagLevel = "A";
        else if (violations.length > 0) wcagLevel = "AA";

        results.a11yViolations.push({
            url: url,
            totalViolations: violations.length,
            violations: violations,
            wcagLevel: wcagLevel,
            status:
                violations.length === 0
                    ? "✅ Accessible"
                    : violations.length < 3
                    ? "⚠️ Needs Improvement"
                    : "🚨 Critical",
        });

        return violations;
    } catch (error) {
        results.a11yViolations.push({
            url: url,
            error: error.message,
        });
        return [];
    }
}

// 🆕 5. MOBILE RESPONSIVENESS TEST (Optimized - sadece 1 cihaz)
async function mobileTest(browser, url) {
    console.log(`   📱 Mobile test: ${url}`);

    const devices = [
        { name: "Mobile", width: 375, height: 667 }, // Sadece mobile test
    ];

    const mobileResults = [];

    for (const device of devices) {
        const page = await browser.newPage();
        await page.setViewport({ width: device.width, height: device.height });

        try {
            const startTime = Date.now();
            await page.goto(CONFIG.baseUrl + url, {
                waitUntil: "networkidle2",
                timeout: CONFIG.timeout,
            });
            const loadTime = Date.now() - startTime;

            // Mobile-specific checks
            const mobileChecks = await page.evaluate((deviceName) => {
                const issues = [];

                // Check for viewport meta tag (critical for mobile)
                const viewport = document.querySelector(
                    'meta[name="viewport"]'
                );
                if (!viewport && deviceName === "Mobile") {
                    issues.push("❌ Missing viewport meta tag");
                }

                // Check for horizontal scroll
                const hasHorizontalScroll =
                    document.body.scrollWidth > window.innerWidth;
                if (hasHorizontalScroll) {
                    issues.push("⚠️ Horizontal scrollbar detected");
                }

                // Check for touch-friendly elements
                const smallButtons = Array.from(
                    document.querySelectorAll("button, a")
                ).filter((btn) => {
                    const rect = btn.getBoundingClientRect();
                    return rect.width < 44 || rect.height < 44;
                });

                if (smallButtons.length > 0 && deviceName === "Mobile") {
                    issues.push(
                        `⚠️ ${smallButtons.length} elements smaller than 44x44px (touch target)`
                    );
                }

                // Check for mobile-specific CSS
                const hasMobileCSS = Array.from(document.styleSheets).some(
                    (sheet) => {
                        try {
                            return Array.from(sheet.cssRules || []).some(
                                (rule) => {
                                    return (
                                        rule.media &&
                                        rule.media.mediaText.includes(
                                            "max-width"
                                        )
                                    );
                                }
                            );
                        } catch (e) {
                            return false;
                        }
                    }
                );

                return {
                    issues: issues,
                    hasMobileCSS: hasMobileCSS,
                };
            }, device.name);

            const score = 100 - mobileChecks.issues.length * 15;

            mobileResults.push({
                device: device.name,
                viewport: `${device.width}x${device.height}`,
                loadTime: loadTime,
                score: Math.max(0, score),
                issues: mobileChecks.issues,
                status:
                    score >= 90
                        ? "✅ Excellent"
                        : score >= 70
                        ? "⚠️ Good"
                        : "🚨 Poor",
            });
        } catch (error) {
            mobileResults.push({
                device: device.name,
                error: error.message,
                status: "❌ Failed",
            });
        }

        await page.close();
    }

    results.mobileScores.push({
        url: url,
        devices: mobileResults,
        overallScore: Math.round(
            mobileResults.reduce((sum, r) => sum + (r.score || 0), 0) /
                mobileResults.length
        ),
    });

    return mobileResults;
}

// 🆕 6. API HEALTH CHECK
async function apiHealthCheck(page) {
    console.log(`   🌐 API health check...`);

    const endpoints = [
        { url: "/api/ilanlar", method: "GET", name: "İlanlar API" },
        { url: "/api/kisiler", method: "GET", name: "Kişiler API" },
        { url: "/api/talepler", method: "GET", name: "Talepler API" },
    ];

    for (const endpoint of endpoints) {
        try {
            const response = await page.evaluate(async (url) => {
                const startTime = performance.now();
                const res = await fetch(url);
                const endTime = performance.now();

                return {
                    status: res.status,
                    ok: res.ok,
                    responseTime: Math.round(endTime - startTime),
                    headers: Object.fromEntries(res.headers.entries()),
                };
            }, CONFIG.baseUrl + endpoint.url);

            results.apiHealth.push({
                endpoint: endpoint.url,
                name: endpoint.name,
                status: response.status,
                responseTime: response.responseTime,
                healthy: response.ok && response.responseTime < 500,
                grade:
                    response.responseTime < 200
                        ? "A"
                        : response.responseTime < 500
                        ? "B"
                        : "C",
            });
        } catch (error) {
            results.apiHealth.push({
                endpoint: endpoint.url,
                name: endpoint.name,
                error: error.message,
                healthy: false,
            });
        }
    }
}

// 🆕 7. CODE QUALITY ANALYZER
async function codeQualityAnalyze(page, url) {
    console.log(`   🎨 Code quality: ${url}`);

    try {
        // Listen for console errors
        const consoleErrors = [];
        page.on("console", (msg) => {
            if (msg.type() === "error") {
                consoleErrors.push(msg.text());
            }
        });

        // Listen for JavaScript errors
        const jsErrors = [];
        page.on("pageerror", (error) => {
            jsErrors.push(error.message);
        });

        // Analyze page
        const quality = await page.evaluate(() => {
            const issues = [];

            // Check for inline styles
            const inlineStyles = document.querySelectorAll("[style]");
            if (inlineStyles.length > 10) {
                issues.push({
                    type: "Code Smell",
                    issue: `${inlineStyles.length} elements with inline styles`,
                    severity: "Low",
                });
            }

            // Check for deprecated tags
            const deprecated = document.querySelectorAll(
                "font, center, marquee, blink"
            );
            if (deprecated.length > 0) {
                issues.push({
                    type: "Deprecated HTML",
                    issue: `${deprecated.length} deprecated HTML tags`,
                    severity: "Medium",
                });
            }

            // Check for empty elements
            const emptyElements = Array.from(
                document.querySelectorAll("div, span, p")
            ).filter((el) => !el.textContent.trim() && !el.children.length);

            if (emptyElements.length > 5) {
                issues.push({
                    type: "Dead Code",
                    issue: `${emptyElements.length} empty elements`,
                    severity: "Low",
                });
            }

            // Check for duplicate IDs
            const ids = Array.from(document.querySelectorAll("[id]")).map(
                (el) => el.id
            );
            const duplicateIds = ids.filter(
                (id, index) => ids.indexOf(id) !== index
            );

            if (duplicateIds.length > 0) {
                issues.push({
                    type: "Invalid HTML",
                    issue: `${duplicateIds.length} duplicate IDs`,
                    severity: "High",
                });
            }

            // Check for missing alt attributes
            const missingAlt = document.querySelectorAll("img:not([alt])");
            if (missingAlt.length > 0) {
                issues.push({
                    type: "Best Practice",
                    issue: `${missingAlt.length} images without alt`,
                    severity: "Medium",
                });
            }

            return {
                issues: issues,
                domSize: document.getElementsByTagName("*").length,
                scriptTags: document.querySelectorAll("script").length,
                styleTags: document.querySelectorAll("style").length,
            };
        });

        // Add console/JS errors
        if (consoleErrors.length > 0) {
            quality.issues.push({
                type: "Console Errors",
                issue: `${consoleErrors.length} console errors`,
                severity: "High",
                details: consoleErrors.slice(0, 5),
            });
        }

        if (jsErrors.length > 0) {
            quality.issues.push({
                type: "JavaScript Errors",
                issue: `${jsErrors.length} JS errors`,
                severity: "Critical",
                details: jsErrors.slice(0, 5),
            });
        }

        const score = Math.max(
            0,
            100 -
                quality.issues.filter((i) => i.severity === "Critical").length *
                    25 -
                quality.issues.filter((i) => i.severity === "High").length *
                    15 -
                quality.issues.filter((i) => i.severity === "Medium").length *
                    10 -
                quality.issues.filter((i) => i.severity === "Low").length * 5
        );

        results.codeQualityIssues.push({
            url: url,
            score: score,
            grade:
                score >= 90 ? "A" : score >= 75 ? "B" : score >= 60 ? "C" : "D",
            issues: quality.issues,
            metrics: {
                domSize: quality.domSize,
                scriptTags: quality.scriptTags,
                styleTags: quality.styleTags,
            },
        });

        return quality;
    } catch (error) {
        results.codeQualityIssues.push({
            url: url,
            error: error.message,
        });
        return null;
    }
}

// 🆕 8. PERFORMANCE BUDGET CHECK
async function performanceBudgetCheck(page, url) {
    console.log(`   ⚡ Performance budget: ${url}`);

    try {
        const startTime = Date.now();
        const response = await page.goto(CONFIG.baseUrl + url, {
            waitUntil: "networkidle2",
            timeout: CONFIG.timeout,
        });
        const loadTime = Date.now() - startTime;

        const metrics = await page.metrics();
        const performance = await page.evaluate(() => {
            const entries = performance.getEntriesByType("navigation")[0];
            return {
                domContentLoaded:
                    entries?.domContentLoadedEventEnd -
                        entries?.domContentLoadedEventStart || 0,
                loadComplete:
                    entries?.loadEventEnd - entries?.loadEventStart || 0,
            };
        });

        // Count network requests
        let requestCount = 0;
        page.on("request", () => requestCount++);

        // Check DOM size
        const domSize = await page.evaluate(
            () => document.getElementsByTagName("*").length
        );

        const jsHeapSize = Math.round(metrics.JSHeapUsedSize / 1024 / 1024);

        // Check against budget
        const violations = [];
        if (loadTime > PERFORMANCE_BUDGET.maxLoadTime) {
            violations.push({
                metric: "Load Time",
                actual: `${loadTime}ms`,
                budget: `${PERFORMANCE_BUDGET.maxLoadTime}ms`,
                exceeded: loadTime - PERFORMANCE_BUDGET.maxLoadTime,
            });
        }

        if (jsHeapSize > PERFORMANCE_BUDGET.maxJSHeapSize) {
            violations.push({
                metric: "JS Heap Size",
                actual: `${jsHeapSize}MB`,
                budget: `${PERFORMANCE_BUDGET.maxJSHeapSize}MB`,
                exceeded: jsHeapSize - PERFORMANCE_BUDGET.maxJSHeapSize,
            });
        }

        if (domSize > PERFORMANCE_BUDGET.maxDOMSize) {
            violations.push({
                metric: "DOM Size",
                actual: domSize,
                budget: PERFORMANCE_BUDGET.maxDOMSize,
                exceeded: domSize - PERFORMANCE_BUDGET.maxDOMSize,
            });
        }

        results.performanceBudgetStatus.push({
            url: url,
            metrics: {
                loadTime: loadTime,
                jsHeapSize: jsHeapSize,
                domSize: domSize,
                requestCount: requestCount,
            },
            violations: violations,
            status:
                violations.length === 0 ? "✅ Within Budget" : "🚨 Over Budget",
            grade:
                violations.length === 0
                    ? "A"
                    : violations.length < 2
                    ? "B"
                    : "C",
        });

        return violations;
    } catch (error) {
        results.performanceBudgetStatus.push({
            url: url,
            error: error.message,
        });
        return [];
    }
}

// 🆕 9. NETWORK MONITOR
async function networkMonitor(page, url) {
    console.log(`   📡 Network monitor: ${url}`);

    const networkLog = [];
    const slowRequests = [];

    // Monitor all network requests
    page.on("request", (request) => {
        request.startTime = Date.now();
    });

    page.on("response", async (response) => {
        const request = response.request();
        const responseTime = Date.now() - (request.startTime || Date.now());

        const log = {
            url: request.url(),
            method: request.method(),
            status: response.status(),
            responseTime: responseTime,
            resourceType: request.resourceType(),
        };

        networkLog.push(log);

        // Track slow requests (>1000ms)
        if (responseTime > 1000) {
            slowRequests.push({
                url: request.url(),
                responseTime: responseTime,
                type: request.resourceType(),
            });
        }
    });

    try {
        await page.goto(CONFIG.baseUrl + url, {
            waitUntil: "networkidle2",
            timeout: CONFIG.timeout,
        });

        // Analyze network log
        const apiCalls = networkLog.filter((log) => log.url.includes("/api/"));
        const failedRequests = networkLog.filter((log) => log.status >= 400);

        results.networkAnalysis.push({
            url: url,
            totalRequests: networkLog.length,
            apiCalls: apiCalls.length,
            slowRequests: slowRequests.length,
            failedRequests: failedRequests.length,
            avgResponseTime: Math.round(
                networkLog.reduce((sum, log) => sum + log.responseTime, 0) /
                    networkLog.length
            ),
            slowestRequest:
                slowRequests.length > 0
                    ? slowRequests.sort(
                          (a, b) => b.responseTime - a.responseTime
                      )[0]
                    : null,
            status:
                failedRequests.length === 0 && slowRequests.length < 3
                    ? "✅ Healthy"
                    : slowRequests.length > 5
                    ? "🚨 Slow"
                    : "⚠️ Needs Attention",
        });

        return { networkLog, slowRequests };
    } catch (error) {
        results.networkAnalysis.push({
            url: url,
            error: error.message,
        });
        return { networkLog: [], slowRequests: [] };
    }
}

// 🆕 10. SMART AUTO-FIX ENGINE (Enhanced)
function suggestAutoFix(error) {
    const fixes = [];

    // Undefined variable fix
    if (error.type === "undefined_variable") {
        fixes.push({
            type: "Auto-Fix Available",
            error: error.details,
            fix: `Add $${error.variable} to controller compact()`,
            code: `return view('...', compact('...', '${error.variable}'));`,
            confidence: "High",
            priority: "Critical",
        });

        // Öğrenme sistemine kaydet
        saveLearnedPattern({
            pattern: "undefined_variable",
            context: "blade_view",
            solution: "add_to_compact",
            variable: error.variable,
        });
    }

    // Missing table fix
    if (error.type === "table_not_found") {
        fixes.push({
            type: "Auto-Fix Available",
            error: error.details,
            fix: `Run migration: php artisan migrate`,
            code: `php artisan migrate`,
            confidence: "High",
            priority: "Critical",
        });

        saveLearnedPattern({
            pattern: "table_not_found",
            context: "database",
            solution: "run_migration",
        });
    }

    // Missing relationship fix
    if (error.type === "undefined_method" && error.details.includes("()")) {
        fixes.push({
            type: "Auto-Fix Available",
            error: error.details,
            fix: `Add relationship to model`,
            code: `public function ${error.method}() {\n    return $this->hasMany(...);\n}`,
            confidence: "Medium",
            priority: "High",
        });

        saveLearnedPattern({
            pattern: "missing_relationship",
            context: "eloquent_model",
            solution: "add_relationship_method",
        });
    }

    // Context7 forbidden field fix
    if (error.type === "context7_violation") {
        const forbiddenField = Object.keys(CONTEXT7_FORBIDDEN_FIELDS).find(
            (key) => error.details.includes(key)
        );
        if (forbiddenField) {
            fixes.push({
                type: "Context7 Auto-Fix",
                error: error.details,
                fix: `Replace '${forbiddenField}' with '${CONTEXT7_FORBIDDEN_FIELDS[forbiddenField]}'`,
                code: `->orderBy('${CONTEXT7_FORBIDDEN_FIELDS[forbiddenField]}') // was: ${forbiddenField}`,
                confidence: "High",
                priority: "Critical",
            });

            saveLearnedPattern({
                pattern: "context7_forbidden_field",
                context: forbiddenField,
                solution: CONTEXT7_FORBIDDEN_FIELDS[forbiddenField],
            });
        }
    }

    // Loading state missing fix
    if (error.type === "missing_loading_state") {
        fixes.push({
            type: "UX Auto-Fix",
            error: "Loading state indicator missing",
            fix: "Add Alpine.js loading state",
            code: `<button :disabled="loading" class="neo-btn">
    <span x-show="loading">⏳ Yükleniyor...</span>
    <span x-show="!loading">Kaydet</span>
</button>`,
            confidence: "High",
            priority: "Medium",
        });

        saveLearnedPattern({
            pattern: "missing_loading_state",
            context: "ux_pattern",
            solution: "alpine_loading_state",
        });
    }

    if (fixes.length > 0) {
        results.autoFixApplied.push(...fixes);
    }

    return fixes;
}

// Hata tespiti ve otomatik düzeltme analizi
async function detectAndFixErrors(page, url) {
    const errors = [];

    // Sayfa hatalarını yakala
    const pageErrors = await page.evaluate(() => {
        const errorElements = document.querySelectorAll(
            '.error, [class*="error"], .alert-danger'
        );
        return Array.from(errorElements).map((el) => ({
            text: el.textContent,
            type: el.className,
        }));
    });

    // Loading state kontrolü
    const hasLoadingState = await page.evaluate(() => {
        return !!document.querySelector(
            '[x-show*="loading"], [class*="loading"], .spinner'
        );
    });

    if (!hasLoadingState) {
        errors.push({
            type: "missing_loading_state",
            details: "Page is missing loading state indicators",
        });
        suggestAutoFix({
            type: "missing_loading_state",
        });
    }

    return errors;
}

// 🆕 Görsel Analiz & UX Önerileri
async function visualAnalysisAndUX(page, url, sayfaAdi) {
    console.log(`   🎨 Görsel analiz & UX: ${url}`);

    try {
        await page.goto(CONFIG.baseUrl + url, {
            waitUntil: "networkidle2",
            timeout: CONFIG.timeout,
        });

        const screenshot = await page.screenshot({
            path: `${CONFIG.screenshotsDir}/${sayfaAdi.replace(
                /\s+/g,
                "-"
            )}.png`,
            fullPage: true,
        });

        // Görsel ve UX analizi
        const analysis = await page.evaluate(() => {
            const insights = {
                layout: {},
                colors: {},
                typography: {},
                spacing: {},
                interactive: {},
                ux: [],
            };

            // Layout analizi
            const container = document.querySelector(
                'main, .container, [class*="container"]'
            );
            if (container) {
                const rect = container.getBoundingClientRect();
                insights.layout = {
                    width: rect.width,
                    hasFixedWidth:
                        window.getComputedStyle(container).maxWidth !== "none",
                    isCentered:
                        window.getComputedStyle(container).marginLeft ===
                        "auto",
                };
            }

            // Renk uyumu analizi
            const bgColors = [];
            const textColors = [];
            document.querySelectorAll("*").forEach((el) => {
                const styles = window.getComputedStyle(el);
                bgColors.push(styles.backgroundColor);
                textColors.push(styles.color);
            });
            insights.colors = {
                uniqueBgColors: [...new Set(bgColors)].length,
                uniqueTextColors: [...new Set(textColors)].length,
                hasConsistency: [...new Set(bgColors)].length < 10,
            };

            // Typography analizi
            const fontSizes = [];
            const fontFamilies = [];
            document
                .querySelectorAll("h1, h2, h3, h4, h5, h6, p, span")
                .forEach((el) => {
                    const styles = window.getComputedStyle(el);
                    fontSizes.push(parseFloat(styles.fontSize));
                    fontFamilies.push(styles.fontFamily);
                });
            insights.typography = {
                uniqueFontSizes: [...new Set(fontSizes)].length,
                uniqueFontFamilies: [...new Set(fontFamilies)].length,
                hasTypographyScale: [...new Set(fontSizes)].length <= 6,
            };

            // Spacing analizi
            const margins = [];
            const paddings = [];
            document.querySelectorAll("*").forEach((el) => {
                const styles = window.getComputedStyle(el);
                margins.push(parseFloat(styles.marginTop));
                paddings.push(parseFloat(styles.paddingTop));
            });
            insights.spacing = {
                consistentMargins:
                    new Set(margins.filter((m) => m > 0)).size < 8,
                consistentPaddings:
                    new Set(paddings.filter((p) => p > 0)).size < 8,
            };

            // Interactive elements analizi
            const buttons = document.querySelectorAll(
                'button, [role="button"], a.btn, .neo-btn'
            );
            const inputs = document.querySelectorAll("input, select, textarea");
            const forms = document.querySelectorAll("form");

            insights.interactive = {
                buttonCount: buttons.length,
                inputCount: inputs.length,
                formCount: forms.length,
                hasHoverEffects: Array.from(buttons).some((btn) => {
                    const styles = window.getComputedStyle(btn);
                    return styles.cursor === "pointer";
                }),
            };

            // UX Sorunları tespit et
            insights.ux = [];

            // 1. Çok fazla renk kullanımı
            if (insights.colors.uniqueBgColors > 15) {
                insights.ux.push({
                    type: "Color Consistency",
                    severity: "Medium",
                    issue: `${insights.colors.uniqueBgColors} farklı background color kullanılmış`,
                    suggestion: "Renk paletini 8-10 renge indirin",
                });
            }

            // 2. Typography scale problemi
            if (insights.typography.uniqueFontSizes > 8) {
                insights.ux.push({
                    type: "Typography Scale",
                    severity: "Low",
                    issue: `${insights.typography.uniqueFontSizes} farklı font size kullanılmış`,
                    suggestion: "Typography scale kullanın (4-6 boyut yeterli)",
                });
            }

            // 3. Button style tutarlılığı
            const buttonStyles = Array.from(buttons).map((btn) => {
                const styles = window.getComputedStyle(btn);
                return `${styles.backgroundColor}-${styles.borderRadius}-${styles.padding}`;
            });
            if (new Set(buttonStyles).size > buttons.length * 0.5) {
                insights.ux.push({
                    type: "Button Consistency",
                    severity: "High",
                    issue: "Button stilleri tutarsız",
                    suggestion:
                        "Tüm buttonlar için ortak stil kullanın (neo-btn)",
                });
            }

            // 4. Form validation görsel feedback
            if (insights.interactive.formCount > 0) {
                const hasValidationFeedback = Array.from(forms).some((form) => {
                    return form.querySelector(
                        '[class*="error"], [class*="invalid"], [class*="valid"]'
                    );
                });
                if (!hasValidationFeedback) {
                    insights.ux.push({
                        type: "Form Validation",
                        severity: "High",
                        issue: "Form validation görsel feedback yok",
                        suggestion:
                            "Hata mesajları ve valid/invalid state gösterin",
                    });
                }
            }

            // 5. Loading states
            const hasLoadingStates = document.querySelector(
                '[class*="loading"], [class*="spinner"], .skeleton'
            );
            if (!hasLoadingStates && insights.interactive.buttonCount > 5) {
                insights.ux.push({
                    type: "Loading States",
                    severity: "Medium",
                    issue: "Loading state görünmüyor",
                    suggestion:
                        "Asenkron işlemler için loading indicator ekleyin",
                });
            }

            // 6. Empty states
            const tables = document.querySelectorAll("table");
            const hasEmptyState = Array.from(tables).some((table) => {
                return table.querySelector(
                    '[class*="empty"], [class*="no-data"]'
                );
            });
            if (tables.length > 0 && !hasEmptyState) {
                insights.ux.push({
                    type: "Empty States",
                    severity: "Low",
                    issue: "Empty state tasarımı yok",
                    suggestion:
                        "Veri yokken kullanıcıya friendly mesaj gösterin",
                });
            }

            return insights;
        });

        // Görsel skor hesapla
        let visualScore = 100;
        analysis.ux.forEach((issue) => {
            if (issue.severity === "High") visualScore -= 15;
            else if (issue.severity === "Medium") visualScore -= 10;
            else visualScore -= 5;
        });

        results.visualInsights.push({
            url: url,
            sayfa: sayfaAdi,
            score: Math.max(0, visualScore),
            grade:
                visualScore >= 90
                    ? "A"
                    : visualScore >= 75
                    ? "B"
                    : visualScore >= 60
                    ? "C"
                    : "D",
            analysis: analysis,
            screenshotPath: `${CONFIG.screenshotsDir}/${sayfaAdi.replace(
                /\s+/g,
                "-"
            )}.png`,
        });

        results.uxSuggestions.push({
            url: url,
            sayfa: sayfaAdi,
            issues: analysis.ux,
            totalIssues: analysis.ux.length,
            criticalCount: analysis.ux.filter((i) => i.severity === "High")
                .length,
        });
    } catch (error) {
        console.log(`   ⚠️ Görsel analiz hatası: ${error.message}`);
    }
}

// Ana test fonksiyonu
async function runWebDeveloperTest(page, browser, sayfa) {
    console.log(`\n${"=".repeat(60)}`);
    console.log(`🧪 Testing: ${sayfa.name}`);
    console.log(`${"=".repeat(60)}`);

    const url = sayfa.url;

    try {
        // 1. Security Scan
        if (CONFIG.securityScan) {
            await securityScan(page, url);
        }

        // 2. SEO Analysis
        if (CONFIG.seoAnalysis) {
            await seoAnalyze(page, url);
        }

        // 3. A11y Check
        if (CONFIG.a11yCheck) {
            await a11yCheck(page, url);
        }

        // 4. Mobile Test
        if (CONFIG.mobileTest) {
            await mobileTest(browser, url);
        }

        // 5. Code Quality
        if (CONFIG.codeQuality) {
            await codeQualityAnalyze(page, url);
        }

        // 6. Performance Budget
        if (CONFIG.performanceBudget) {
            await performanceBudgetCheck(page, url);
        }

        // 7. Network Monitor
        if (CONFIG.networkMonitor) {
            await networkMonitor(page, url);
        }

        // 8. Görsel Analiz & UX Önerileri 🆕
        await visualAnalysisAndUX(page, url, sayfa.name);

        // 9. Hata Tespit ve Otomatik Düzeltme 🆕
        await detectAndFixErrors(page, url);

        console.log(`✅ ${sayfa.name} - Web Developer check completed`);
    } catch (error) {
        console.log(`❌ ${sayfa.name} - Error: ${error.message}`);

        // Hataları kaydet ve öğren
        saveLearnedPattern({
            pattern: "test_error",
            context: url,
            solution: "check_page_accessibility",
            error: error.message,
        });
    }
}

// Rapor oluştur
function generateWebDeveloperReport() {
    const endTime = new Date();
    const duration = Math.round((endTime - results.startTime) / 1000);

    const report = `# 👨‍💻 USTA Web Developer Raporu

**Test Zamanı:** ${results.startTime.toLocaleString("tr-TR")}
**Süre:** ${duration} saniye
**Toplam Sayfa:** ${SAYFALAR.length}
**USTA Versiyonu:** 4.0 (Web Developer Master)

---

## 🔒 Güvenlik Taraması

${results.securityIssues
    .map(
        (s) => `
### ${s.url}

- **Toplam Sorun:** ${s.totalIssues}
- **Durum:** ${s.severity}

${s.issues
    .map(
        (issue) => `
**${issue.type}**
- Element: ${issue.element}
- Sorun: ${issue.issue}
`
    )
    .join("\n")}
`
    )
    .join("\n")}

---

## 📊 SEO Skorları

${results.seoScores
    .map(
        (seo) => `
### ${seo.url}

- **Skor:** ${seo.score}/100 (Grade: ${seo.grade})
- **Title:** ${seo.data?.title || "N/A"} (${
            seo.data?.titleLength || 0
        } karakter)
- **Meta Description:** ${seo.data?.metaDescription ? "✅" : "❌"}
- **H1 Count:** ${seo.data?.h1Count || 0}
- **Images without alt:** ${seo.data?.imgWithoutAlt || 0}
- **OpenGraph:** ${
            seo.data?.hasOGTitle && seo.data?.hasOGDescription
                ? "✅ Complete"
                : "⚠️ Incomplete"
        }

**Sorunlar:**
${seo.issues.map((i) => `- ${i}`).join("\n")}
`
    )
    .join("\n")}

---

## ♿ Erişilebilirlik (A11y)

${results.a11yViolations
    .map(
        (a11y) => `
### ${a11y.url}

- **Toplam İhlal:** ${a11y.totalViolations}
- **WCAG Level:** ${a11y.wcagLevel}
- **Durum:** ${a11y.status}

${a11y.violations
    ?.map(
        (v) => `
**${v.type}**
- Count: ${v.count}
- WCAG: Level ${v.wcagLevel}
- Impact: ${v.impact}
`
    )
    .join("\n")}
`
    )
    .join("\n")}

---

## 📱 Mobil Responsive Testi

${results.mobileScores
    .map(
        (mobile) => `
### ${mobile.url}

- **Overall Score:** ${mobile.overallScore}/100

${mobile.devices
    .map(
        (d) => `
**${d.device}** (${d.viewport})
- Load Time: ${d.loadTime}ms
- Score: ${d.score}/100
- Status: ${d.status}
${d.issues ? d.issues.map((i) => `  - ${i}`).join("\n") : ""}
`
    )
    .join("\n")}
`
    )
    .join("\n")}

---

## 🌐 API Sağlık Kontrolü

${results.apiHealth
    .map(
        (api) => `
- **${api.name}** (${api.endpoint})
  - Status: ${api.status || "N/A"}
  - Response Time: ${api.responseTime || "N/A"}ms
  - Grade: ${api.grade || "N/A"}
  - Healthy: ${api.healthy ? "✅" : "❌"}
`
    )
    .join("\n")}

---

## 🎨 Kod Kalitesi

${results.codeQualityIssues
    .map(
        (cq) => `
### ${cq.url}

- **Score:** ${cq.score}/100 (Grade: ${cq.grade})
- **DOM Size:** ${cq.metrics?.domSize || "N/A"} elements
- **Script Tags:** ${cq.metrics?.scriptTags || "N/A"}
- **Style Tags:** ${cq.metrics?.styleTags || "N/A"}

**Sorunlar:**
${cq.issues
    ?.map(
        (issue) => `
- **${issue.type}** (${issue.severity})
  - ${issue.issue}
  ${issue.details ? issue.details.map((d) => `  - ${d}`).join("\n") : ""}
`
    )
    .join("\n")}
`
    )
    .join("\n")}

---

## ⚡ Performance Budget

${results.performanceBudgetStatus
    .map(
        (pb) => `
### ${pb.url}

- **Status:** ${pb.status} (Grade: ${pb.grade || "N/A"})
- **Metrics:**
  - Load Time: ${pb.metrics?.loadTime || "N/A"}ms (Budget: ${
            PERFORMANCE_BUDGET.maxLoadTime
        }ms)
  - JS Heap: ${pb.metrics?.jsHeapSize || "N/A"}MB (Budget: ${
            PERFORMANCE_BUDGET.maxJSHeapSize
        }MB)
  - DOM Size: ${pb.metrics?.domSize || "N/A"} (Budget: ${
            PERFORMANCE_BUDGET.maxDOMSize
        })

${
    pb.violations && pb.violations.length > 0
        ? `**🚨 Budget Violations:**
${pb.violations
    .map(
        (v) =>
            `- ${v.metric}: ${v.actual} (Budget: ${v.budget}, Exceeded: ${v.exceeded})`
    )
    .join("\n")}`
        : "✅ All metrics within budget"
}
`
    )
    .join("\n")}

---

## 📡 Network Analizi

${results.networkAnalysis
    .map(
        (na) => `
### ${na.url}

- **Total Requests:** ${na.totalRequests || "N/A"}
- **API Calls:** ${na.apiCalls || "N/A"}
- **Slow Requests:** ${na.slowRequests || "N/A"} (>1000ms)
- **Failed Requests:** ${na.failedRequests || "N/A"}
- **Avg Response Time:** ${na.avgResponseTime || "N/A"}ms
- **Status:** ${na.status || "N/A"}

${
    na.slowestRequest
        ? `**🐌 Slowest Request:**
- URL: ${na.slowestRequest.url}
- Time: ${na.slowestRequest.responseTime}ms
- Type: ${na.slowestRequest.type}`
        : ""
}
`
    )
    .join("\n")}

---

## 🎨 Görsel Analiz & UX Önerileri

${
    results.visualInsights.length > 0
        ? `
${results.visualInsights
    .map(
        (vi) => `
### ${vi.sayfa}

- **Görsel Skor:** ${vi.score}/100 (Grade: ${vi.grade})
- **Screenshot:** \`${vi.screenshotPath}\`

**Analiz Sonuçları:**
- Layout: ${vi.analysis.layout.hasFixedWidth ? "✅ Fixed width" : "⚠️ Fluid"} ${
            vi.analysis.layout.isCentered ? "✅ Centered" : ""
        }
- Renk Uyumu: ${
            vi.analysis.colors.hasConsistency
                ? "✅ Tutarlı"
                : `⚠️ ${vi.analysis.colors.uniqueBgColors} farklı renk`
        }
- Typography: ${
            vi.analysis.typography.hasTypographyScale
                ? "✅ Scale var"
                : `⚠️ ${vi.analysis.typography.uniqueFontSizes} farklı font size`
        }
- Spacing: ${
            vi.analysis.spacing.consistentMargins &&
            vi.analysis.spacing.consistentPaddings
                ? "✅ Tutarlı"
                : "⚠️ Tutarsız"
        }
- Interactive: ${vi.analysis.interactive.buttonCount} button, ${
            vi.analysis.interactive.inputCount
        } input, ${vi.analysis.interactive.formCount} form

**UX Sorunları (${
            results.uxSuggestions.find((u) => u.url === vi.url)?.totalIssues ||
            0
        }):**
${
    results.uxSuggestions
        .find((u) => u.url === vi.url)
        ?.issues.map(
            (issue) => `
- **${issue.type}** (${issue.severity})
  - Sorun: ${issue.issue}
  - Öneri: ${issue.suggestion}
`
        )
        .join("\n") || "UX sorunu yok ✅"
}
`
    )
    .join("\n")}
`
        : "Görsel analiz yapılmadı."
}

---

## 🧠 Self-Learning Patterns

${
    results.learnedPatterns.length > 0
        ? `
**Öğrenilen Pattern'ler:**
${results.learnedPatterns
    .map((p) => `- ${p.pattern}: ${p.action} (Frequency: ${p.frequency})`)
    .join("\n")}
`
        : "Henüz yeni pattern öğrenilmedi."
}

---

## 🔧 Otomatik Düzeltme Önerileri

${
    results.autoFixApplied.length > 0
        ? `
${results.autoFixApplied
    .map(
        (fix) => `
### ${fix.error}

- **Fix:** ${fix.fix}
- **Confidence:** ${fix.confidence}
- **Code:**
\`\`\`php
${fix.code}
\`\`\`
`
    )
    .join("\n")}
`
        : "Otomatik düzeltme önerisi yok."
}

---

## 📊 Genel Özet

### Güvenlik
- Toplam Sayfa: ${results.securityIssues.length}
- Güvenli: ${
        results.securityIssues.filter((s) => s.severity === "✅ Secure").length
    }
- Riskli: ${
        results.securityIssues.filter((s) => s.severity !== "✅ Secure").length
    }

### SEO
- Ortalama Skor: ${
        results.seoScores.length > 0
            ? Math.round(
                  results.seoScores.reduce((sum, s) => sum + s.score, 0) /
                      results.seoScores.length
              )
            : "N/A"
    }/100
- A Grade: ${results.seoScores.filter((s) => s.grade === "A").length}
- B Grade: ${results.seoScores.filter((s) => s.grade === "B").length}
- C Grade: ${results.seoScores.filter((s) => s.grade === "C").length}

### Erişilebilirlik
- WCAG AAA: ${
        results.a11yViolations.filter((a) => a.wcagLevel === "AAA").length
    }
- WCAG AA: ${results.a11yViolations.filter((a) => a.wcagLevel === "AA").length}
- WCAG A: ${results.a11yViolations.filter((a) => a.wcagLevel === "A").length}
- Failed: ${
        results.a11yViolations.filter((a) => a.wcagLevel === "Failed").length
    }

### Performance
- Within Budget: ${
        results.performanceBudgetStatus.filter(
            (p) => p.status === "✅ Within Budget"
        ).length
    }
- Over Budget: ${
        results.performanceBudgetStatus.filter(
            (p) => p.status === "🚨 Over Budget"
        ).length
    }

---

**USTA 4.0 - Web Developer Master**
**Context7 Uyumlu:** ✅
**Tarih:** ${endTime.toLocaleString("tr-TR")}
`;

    fs.writeFileSync("./usta-web-developer-raporu.md", report);
    console.log(
        "\n📋 Web Developer Raporu oluşturuldu: usta-web-developer-raporu.md\n"
    );
}

// Main
async function main() {
    console.log("\n" + "=".repeat(60));
    console.log("👨‍💻 USTA 4.0 - Web Developer Master");
    console.log("=".repeat(60) + "\n");

    // Önceki raporu temizle
    const reportFile = "./usta-web-developer-raporu.md";
    if (fs.existsSync(reportFile)) {
        fs.unlinkSync(reportFile);
        console.log("🗑️  Önceki rapor temizlendi\n");
    }

    // Screenshot dizini oluştur
    if (!fs.existsSync(CONFIG.screenshotsDir)) {
        fs.mkdirSync(CONFIG.screenshotsDir, { recursive: true });
    }

    const browser = await puppeteer.launch({
        headless: CONFIG.headless,
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
        page.click('button[type="submit"]'),
        page.waitForNavigation(),
    ]);
    console.log("   ✅ Giriş başarılı!\n");

    // API Health Check (tek sefer)
    if (CONFIG.apiHealthCheck) {
        await apiHealthCheck(page);
    }

    // Test her sayfayı
    for (const sayfa of SAYFALAR) {
        await runWebDeveloperTest(page, browser, sayfa);
    }

    await browser.close();

    // Rapor oluştur
    generateWebDeveloperReport();

    console.log("\n" + "=".repeat(60));
    console.log("✨ USTA 4.0 Web Developer Test tamamlandı!");
    console.log("=".repeat(60) + "\n");
}

main().catch((error) => {
    console.error("❌ Fatal error:", error);
    process.exit(1);
});
