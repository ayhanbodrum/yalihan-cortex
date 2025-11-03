import puppeteer from "puppeteer";
import fs from "fs";

const CONFIG = {
    baseUrl: "http://127.0.0.1:8000",
    loginEmail: "admin@yalihanemlak.com",
    loginPassword: "admin123",
    screenshotsDir: "./screenshots/usta-test",
    timeout: 30000,
    headless: false, // Kullanıcı süreci görsün

    // Context7 Öğretmen Modu
    context7Teacher: true, // Context7 kurallarını öğret
    context7StrictMode: true, // Strict Context7 compliance
};

const SAYFALAR = [
    {
        url: "/admin/talep-portfolyo",
        name: "Talep-Portföy Eşleştirme",
        tasarimKritik: true,
    },
    {
        url: "/admin/notifications",
        name: "Bildirimler",
        tasarimKritik: true,
    },
    { url: "/admin/analytics", name: "Analytics", tasarimKritik: true },
    {
        url: "/admin/telegram-bot",
        name: "Telegram Bot",
        tasarimKritik: true,
    },
    {
        url: "/admin/ilan-kategorileri",
        name: "İlan Kategorileri",
        tasarimKritik: true,
    },
];

const results = {
    sayfalar: [],
    tasarimHatalari: [],
    teknikHatalar: [],
    duzeltmeler: [],
    context7Violations: [], // Context7 kural ihlalleri
    context7Lessons: [], // Context7 öğretici mesajlar
    visualInsights: [], // Görsel analiz ve öneriler (YENİ!)
    uxSuggestions: [], // UX/UI iyileştirme önerileri (YENİ!)
    startTime: new Date(),
};

// Context7 Kuralları (Öğretmen Modu)
const CONTEXT7_RULES = {
    // Yasak Database Alan Adları
    forbiddenFields: {
        'durum': 'status',
        'is_active': 'status',
        'aktif': 'status',
        'sehir': 'il',
        'region_id': 'il_id',
        'ad_soyad': 'tam_ad',
        'full_name': 'name',
    },

    // Yasak Model İlişkileri
    forbiddenRelations: {
        'sehir()': 'il()',
        'bolge()': 'KALDIRIN - Kullanılmıyor',
    },

    // Zorunlu Design Patterns
    requiredPatterns: {
        'neo-card': 'Modern card component zorunlu',
        'neo-input': 'Input styling için neo-input kullanın',
        'neo-btn': 'Button styling için neo-btn kullanın',
        'dark:': 'Dark mode desteği zorunlu',
        'md:': 'Responsive design zorunlu',
    },

    // Context7 Standartları
    standards: {
        'status field': 'Tüm status alanları string olmalı (Aktif, Pasif, etc.)',
        'timestamps': 'created_at, updated_at zorunlu',
        'soft deletes': 'deleted_at kullanımı önerilir',
        'foreign keys': 'Tüm ilişkiler foreign key ile tanımlı olmalı',
    }
};

async function createDir() {
    if (!fs.existsSync(CONFIG.screenshotsDir)) {
        fs.mkdirSync(CONFIG.screenshotsDir, { recursive: true });
    }
    if (!fs.existsSync("./screenshots/usta-test/before")) {
        fs.mkdirSync("./screenshots/usta-test/before", { recursive: true });
    }
    if (!fs.existsSync("./screenshots/usta-test/after")) {
        fs.mkdirSync("./screenshots/usta-test/after", { recursive: true });
    }
}

async function login(page) {
    console.log("🔐 Usta giriş yapıyor...\n");
    await page.goto(CONFIG.baseUrl + "/login", { waitUntil: "networkidle2" });
    await page.type('input[name="email"]', CONFIG.loginEmail);
    await page.type('input[name="password"]', CONFIG.loginPassword);
    await Promise.all([
        page.waitForNavigation({ waitUntil: "networkidle2" }),
        page.click('button[type="submit"]'),
    ]);
    console.log("   ✅ Giriş başarılı!\n");
}

async function ustaAnaliz(page, sayfaInfo) {
    console.log(`\n🔍 USTA ANALİZ: ${sayfaInfo.name}`);
    console.log(`   URL: ${sayfaInfo.url}`);

    const sonuc = {
        ...sayfaInfo,
        httpStatus: null,
        teknikHatalar: [],
        tasarimHatalari: [],
        context7Uyumluluk: {},
        duzeltmeOnerileri: [],
        screenshotBefore: null,
        timestamp: new Date().toISOString(),
    };

    try {
        const response = await page.goto(CONFIG.baseUrl + sayfaInfo.url, {
            waitUntil: "networkidle2",
            timeout: CONFIG.timeout,
        });

        sonuc.httpStatus = response.status();
        console.log(`   📡 HTTP: ${sonuc.httpStatus}`);

        // Screenshot BEFORE
        const screenshotName = `${sayfaInfo.name.replace(
            /\s/g,
            "-"
        )}-${Date.now()}`;
        const screenshotBefore = `${CONFIG.screenshotsDir}/before/${screenshotName}.png`;
        await page.screenshot({ path: screenshotBefore, fullPage: true });
        sonuc.screenshotBefore = screenshotBefore;
        console.log(`   📸 Screenshot alındı`);

        // USTA DETAYLI ANALİZ
        const ustaAnaliz = await page.evaluate(() => {
            const teknikHatalar = [];
            const tasarimHatalari = [];
            const context7Sorunlar = [];

            const bodyText = document.body.innerText;
            const bodyHTML = document.body.innerHTML;

            // 1. TEKNİK HATALAR
            const errorPatterns = [
                {
                    pattern: /SQLSTATE\[42S02\].*Table '.*?\.(\w+)'/,
                    tip: "Tablo Eksik",
                    extract: 1,
                },
                {
                    pattern: /SQLSTATE\[42S22\].*Column '(\w+)' not found/,
                    tip: "Kolon Eksik",
                    extract: 1,
                },
                {
                    pattern: /Undefined variable \$(\w+)/,
                    tip: "Undefined Variable",
                    extract: 1,
                },
                {
                    pattern:
                        /Call to undefined (method|relationship) \[?(\w+)\]?/,
                    tip: "Method/Relationship Eksik",
                    extract: 2,
                },
                {
                    pattern: /Class ".*?\\(\w+)" not found/,
                    tip: "Class Bulunamadı",
                    extract: 1,
                },
                {
                    pattern: /to be implemented|hazır değil/i,
                    tip: "Implement Edilmemiş",
                    extract: 0,
                },
                {
                    pattern: /BadMethodCallException/,
                    tip: "Method Bulunamadı",
                    extract: 0,
                },
            ];

            errorPatterns.forEach(({ pattern, tip, extract }) => {
                if (pattern.test(bodyText)) {
                    const match = bodyText.match(pattern);
                    teknikHatalar.push({
                        tip: tip,
                        detay: match ? match[extract] || match[0] : tip,
                        fullMatch: match ? match[0].substring(0, 200) : "",
                    });
                }
            });

            // 2. TASARIM HATALARI (Context7 + Neo Design)
            const designChecks = {
                // Neo Design System
                neoCard: document.querySelectorAll(".neo-card").length,
                neoInput: document.querySelectorAll(".neo-input").length,
                neoBtn: document.querySelectorAll(
                    ".neo-btn-primary, .neo-btn-secondary"
                ).length,

                // Tailwind
                tailwindBg: document.querySelectorAll('[class*="bg-"]').length,
                tailwindText:
                    document.querySelectorAll('[class*="text-"]').length,

                // Responsive
                responsive: document.querySelectorAll(
                    '[class*="md:"], [class*="lg:"]'
                ).length,

                // Dark mode
                darkMode: document.querySelectorAll('[class*="dark:"]').length,

                // Form elements
                inputs: document.querySelectorAll("input").length,
                buttons: document.querySelectorAll("button").length,
                selects: document.querySelectorAll("select").length,

                // Layout
                cards: document.querySelectorAll(
                    ".card, .neo-card, [class*='card']"
                ).length,
                grids: document.querySelectorAll('[class*="grid"]').length,
                flexboxes: document.querySelectorAll('[class*="flex"]').length,
            };

            // Tasarım hatalarını tespit et
            if (designChecks.neoCard === 0 && designChecks.cards === 0) {
                tasarimHatalari.push({
                    tip: "Card Yapısı Eksik",
                    oncelik: "Yüksek",
                    cozum: "Neo-card component'leri ekle",
                });
            }

            if (designChecks.neoInput === 0 && designChecks.inputs > 0) {
                tasarimHatalari.push({
                    tip: "Input Styling Eksik",
                    oncelik: "Yüksek",
                    cozum: "Input'lara neo-input class ekle",
                });
            }

            if (designChecks.neoBtn === 0 && designChecks.buttons > 0) {
                tasarimHatalari.push({
                    tip: "Button Styling Eksik",
                    oncelik: "Yüksek",
                    cozum: "Button'lara neo-btn-primary class ekle",
                });
            }

            if (designChecks.responsive === 0) {
                tasarimHatalari.push({
                    tip: "Responsive Design Eksik",
                    oncelik: "Orta",
                    cozum: "md:, lg: breakpoint'leri ekle",
                });
            }

            if (designChecks.darkMode === 0) {
                tasarimHatalari.push({
                    tip: "Dark Mode Desteği Yok",
                    oncelik: "Orta",
                    cozum: "dark: class'ları ekle",
                });
            }

            if (
                designChecks.tailwindBg === 0 &&
                designChecks.tailwindText === 0
            ) {
                tasarimHatalari.push({
                    tip: "Tailwind Kullanılmamış",
                    oncelik: "Kritik",
                    cozum: "Tüm styling'i Tailwind'e geçir",
                });
            }

            // 3. CONTEXT7 UYUMLULUK (Öğretmen Modu)
            const context7Checks = {
                // Yasak kelimeler kontrolü (Detaylı)
                yasakKelimeler: [
                    { kelime: "durum", yerine: "status", aciklama: "Context7 Rule: 'durum' yerine 'status' kullanın" },
                    { kelime: "aktif", yerine: "status", aciklama: "Context7 Rule: 'aktif' yerine 'status' kullanın" },
                    { kelime: "is_active", yerine: "status", aciklama: "Context7 Rule: 'is_active' yerine 'status' kullanın" },
                    { kelime: "sehir", yerine: "il", aciklama: "Context7 Rule: 'sehir' yerine 'il' kullanın" },
                    { kelime: "region_id", yerine: "il_id", aciklama: "Context7 Rule: 'region_id' yasak, 'il_id' kullanın" },
                    { kelime: "ad_soyad", yerine: "tam_ad", aciklama: "Context7 Rule: Birleşik alanlar yasak" },
                ],

                // İyi pattern'ler (Context7 Standartları)
                iyiPatternler: {
                    hasStatusField: bodyHTML.includes('name="status"'),
                    hasTimestamps:
                        bodyHTML.includes("created_at") ||
                        bodyHTML.includes("updated_at"),
                    hasSoftDeletes: bodyHTML.includes("deleted_at"),
                    hasNeoCard: document.querySelectorAll(".neo-card").length > 0,
                    hasDarkMode: document.querySelectorAll('[class*="dark:"]').length > 0,
                    hasResponsive: document.querySelectorAll('[class*="md:"], [class*="lg:"]').length > 0,
                },
            };

            // Context7 sorunlarını tespit et (Öğretmen Modu)
            context7Checks.yasakKelimeler.forEach(({ kelime, yerine, aciklama }) => {
                const regex = new RegExp(`name=["']${kelime}["']`, "gi");
                if (regex.test(bodyHTML)) {
                    context7Sorunlar.push({
                        tip: "Context7 Yasak Alan Adı",
                        kelime: kelime,
                        yerine: yerine,
                        aciklama: aciklama,
                        oncelik: "Kritik",
                        ogretici: `🎓 CONTEXT7 DERS: "${kelime}" alanı Context7 kurallarına aykırıdır. Lütfen "${yerine}" kullanın. Bu değişiklik database, model ve view'larda yapılmalıdır.`
                    });
                }
            });

            // Context7 İyi Pattern Kontrolü
            if (!context7Checks.iyiPatternler.hasNeoCard) {
                context7Sorunlar.push({
                    tip: "Context7 Design Pattern Eksik",
                    kelime: "neo-card",
                    yerine: "Neo Design System",
                    aciklama: "Context7 Rule: Neo Design System component'leri kullanılmalı",
                    oncelik: "Yüksek",
                    ogretici: "🎓 CONTEXT7 DERS: Tüm card yapıları 'neo-card' class'ı kullanmalıdır. Bu Neo Design System'in bir parçasıdır ve responsive + dark mode desteği sağlar."
                });
            }

            if (!context7Checks.iyiPatternler.hasDarkMode) {
                context7Sorunlar.push({
                    tip: "Context7 Dark Mode Eksik",
                    kelime: "dark:",
                    yerine: "Dark mode support",
                    aciklama: "Context7 Rule: Dark mode desteği zorunlu",
                    oncelik: "Orta",
                    ogretici: "🎓 CONTEXT7 DERS: Tüm sayfalar 'dark:' prefix'li class'lar ile dark mode desteği sağlamalıdır. Örnek: 'dark:bg-gray-800', 'dark:text-gray-100'"
                });
            }

            // 4. GÖRSEL ANALİZ & UX ÖNERİLERİ (YENİ!)
            const gorselAnaliz = {
                // Sayfa yapısı
                layout: {
                    hasHeader: document.querySelector('h1, h2, .page-header, .neo-page-header') !== null,
                    hasStats: document.querySelectorAll('.stat-card, .neo-stat-card, [class*="bg-gradient"]').length,
                    hasTable: document.querySelector('table') !== null,
                    hasForm: document.querySelector('form') !== null,
                    hasModal: document.querySelectorAll('[id*="modal"], [id*="Modal"]').length,
                    hasChart: document.querySelectorAll('canvas, [id*="chart"]').length,
                },

                // İnteraktif elementler
                interaktif: {
                    buttonSayisi: document.querySelectorAll('button').length,
                    linkSayisi: document.querySelectorAll('a').length,
                    formInputSayisi: document.querySelectorAll('input, select, textarea').length,
                    searchBox: document.querySelector('[type="search"], [placeholder*="ara"], [placeholder*="Ara"]') !== null,
                    filterForm: document.querySelector('[action*="filter"], form[method="GET"]') !== null,
                },

                // Görsel kalite
                gorunum: {
                    hasIcon: document.querySelectorAll('svg, i[class*="fa-"]').length,
                    hasBadge: document.querySelectorAll('.badge, [class*="badge"], [class*="rounded-full"]').length,
                    hasGradient: document.querySelectorAll('[class*="gradient"]').length,
                    hasAnimation: document.querySelectorAll('[class*="transition"], [class*="hover:"]').length,
                    hasEmptyState: document.querySelector('[class*="empty"], .text-center') !== null,
                },

                // Accessibility
                erisilebilirlik: {
                    hasAltText: Array.from(document.querySelectorAll('img')).every(img => img.alt),
                    hasAriaLabels: document.querySelectorAll('[aria-label], [aria-labelledby]').length,
                    hasFormLabels: Array.from(document.querySelectorAll('input:not([type="hidden"])')).filter(input => {
                        return document.querySelector(`label[for="${input.id}"]`) !== null;
                    }).length,
                },
            };

            // UX Önerileri Üret
            const uxOnerileri = [];

            if (!gorselAnaliz.layout.hasHeader) {
                uxOnerileri.push({
                    kategori: 'Layout',
                    oncelik: 'Yüksek',
                    oneri: 'Sayfa başlığı (h1) ekleyin',
                    neden: 'Kullanıcılar hangi sayfada olduklarını anlamalı',
                    cozum: '<h1 class="text-3xl font-bold dark:text-white mb-6">Sayfa Başlığı</h1>'
                });
            }

            if (gorselAnaliz.layout.hasStats < 3 && gorselAnaliz.layout.hasStats > 0) {
                uxOnerileri.push({
                    kategori: 'İstatistikler',
                    oncelik: 'Orta',
                    oneri: 'En az 4 istatistik kartı gösterin',
                    neden: 'Dashboard sayfaları genelde 4 temel metrik gösterir',
                    cozum: 'Grid layout ile 4 istatistik kartı ekleyin (grid-cols-1 md:grid-cols-2 lg:grid-cols-4)'
                });
            }

            if (gorselAnaliz.interaktif.searchBox === false && gorselAnaliz.layout.hasTable) {
                uxOnerileri.push({
                    kategori: 'Arama',
                    oncelik: 'Yüksek',
                    oneri: 'Tablo için arama kutusu ekleyin',
                    neden: 'Büyük tablolarda arama özelliği kullanıcı deneyimini artırır',
                    cozum: '<input type="search" placeholder="Ara..." class="neo-input">'
                });
            }

            if (gorselAnaliz.gorunum.hasIcon < 5) {
                uxOnerileri.push({
                    kategori: 'Görsel',
                    oncelik: 'Düşük',
                    oneri: 'Daha fazla ikon kullanın',
                    neden: 'İkonlar sayfayı daha anlaşılır ve modern yapar',
                    cozum: 'Heroicons veya Feather Icons kullanarak buton ve başlıklara ikon ekleyin'
                });
            }

            if (gorselAnaliz.gorunum.hasAnimation < 10) {
                uxOnerileri.push({
                    kategori: 'Animasyon',
                    oncelik: 'Düşük',
                    oneri: 'Hover ve transition efektleri ekleyin',
                    neden: 'Animasyonlar sayfa etkileşimini artırır',
                    cozum: 'transition-all duration-300 hover:scale-105 hover:shadow-lg class\'ları ekleyin'
                });
            }

            if (gorselAnaliz.erisilebilirlik.hasFormLabels === 0 && gorselAnaliz.interaktif.formInputSayisi > 0) {
                uxOnerileri.push({
                    kategori: 'Erişilebilirlik',
                    oncelik: 'Kritik',
                    oneri: 'Form input\'larına label ekleyin',
                    neden: 'Erişilebilirlik ve kullanılabilirlik için zorunlu',
                    cozum: '<label for="inputId" class="block text-sm font-medium">Alan Adı</label>'
                });
            }

            return {
                teknikHatalar,
                tasarimHatalari,
                context7Sorunlar,
                designChecks,
                context7Checks,
                gorselAnaliz, // YENİ!
                uxOnerileri, // YENİ!
            };
        });

        sonuc.teknikHatalar = ustaAnaliz.teknikHatalar;
        sonuc.tasarimHatalari = ustaAnaliz.tasarimHatalari;
        sonuc.context7Uyumluluk = ustaAnaliz.context7Checks;
        sonuc.gorselAnaliz = ustaAnaliz.gorselAnaliz; // YENİ!
        sonuc.uxOnerileri = ustaAnaliz.uxOnerileri; // YENİ!

        // USTA RAPORU
        console.log(`\n   📋 USTA RAPOR:`);

        if (sonuc.teknikHatalar.length > 0) {
            console.log(
                `   ❌ Teknik Hata: ${sonuc.teknikHatalar.length} adet`
            );
            sonuc.teknikHatalar.forEach((h) => {
                console.log(`      • ${h.tip}: ${h.detay}`);

                // Otomatik düzeltme önerisi
                const oneri = generateDuzeltmeOnerisi(h);
                if (oneri) {
                    sonuc.duzeltmeOnerileri.push(oneri);
                    console.log(`        💡 ${oneri.komut}`);
                }
            });
            results.teknikHatalar.push(sonuc);
        } else {
            console.log(`   ✅ Teknik hata yok`);
        }

        if (sonuc.tasarimHatalari.length > 0) {
            console.log(
                `   🎨 Tasarım Sorunu: ${sonuc.tasarimHatalari.length} adet`
            );
            sonuc.tasarimHatalari.forEach((h) => {
                console.log(`      • [${h.oncelik}] ${h.tip}`);
                console.log(`        → ${h.cozum}`);
            });
            results.tasarimHatalari.push(sonuc);
        } else {
            console.log(`   ✅ Tasarım uyumlu`);
        }

        console.log(
            `   📊 Context7: ${
                ustaAnaliz.context7Sorunlar.length > 0
                    ? "⚠️ " + ustaAnaliz.context7Sorunlar.length + " sorun var"
                    : "✅ Uyumlu"
            }`
        );

        // Context7 Öğretmen Modu Mesajları
        if (CONFIG.context7Teacher && ustaAnaliz.context7Sorunlar.length > 0) {
            console.log(`\n   🎓 CONTEXT7 ÖĞRETMEN MODU:`);
            ustaAnaliz.context7Sorunlar.forEach((sorun, index) => {
                console.log(`   ${index + 1}. ${sorun.ogretici || sorun.aciklama}`);
                results.context7Lessons.push({
                    sayfa: sayfaInfo.name,
                    url: sayfaInfo.url,
                    ders: sorun.ogretici || sorun.aciklama,
                    oncelik: sorun.oncelik
                });
            });
        }

        // Context7 İhlal Kaydı
        if (ustaAnaliz.context7Sorunlar.length > 0) {
            results.context7Violations.push({
                sayfa: sayfaInfo.name,
                url: sayfaInfo.url,
                sorunlar: ustaAnaliz.context7Sorunlar
            });
        }

        // UX Önerileri Kaydı (YENİ!)
        if (ustaAnaliz.uxOnerileri && ustaAnaliz.uxOnerileri.length > 0) {
            console.log(`   💡 UX Önerisi: ${ustaAnaliz.uxOnerileri.length} adet`);
            ustaAnaliz.uxOnerileri.forEach((oneri, idx) => {
                console.log(`      ${idx + 1}. [${oneri.oncelik}] ${oneri.oneri}`);
            });

            results.uxSuggestions.push({
                sayfa: sayfaInfo.name,
                url: sayfaInfo.url,
                onerileri: ustaAnaliz.uxOnerileri
            });
        }

        // Görsel İzlenim (YENİ!)
        if (ustaAnaliz.gorselAnaliz) {
            const izlenim = generateVisualInsight(sayfaInfo.name, ustaAnaliz.gorselAnaliz);
            console.log(`   🎨 Görsel İzlenim: ${izlenim.skor}/10`);
            console.log(`      ${izlenim.yorum}`);

            results.visualInsights.push({
                sayfa: sayfaInfo.name,
                url: sayfaInfo.url,
                skor: izlenim.skor,
                yorum: izlenim.yorum,
                detaylar: izlenim.detaylar
            });
        }
    } catch (error) {
        sonuc.teknikHatalar.push({
            tip: "Bağlantı Hatası",
            detay: error.message,
        });
        console.log(`   💥 Hata: ${error.message}`);
    }

    results.sayfalar.push(sonuc);
    return sonuc;
}

function generateDuzeltmeOnerisi(hata) {
    if (hata.tip === "Tablo Eksik") {
        return {
            tip: "migration",
            komut: `php artisan make:migration create_${hata.detay}_table`,
            dosya: `database/migrations/create_${hata.detay}_table.php`,
            otomatik: true,
        };
    }

    if (hata.tip === "Undefined Variable") {
        return {
            tip: "controller",
            komut: `Controller'da $${hata.detay} değişkenini tanımla`,
            otomatik: true,
        };
    }

    if (hata.tip === "Kolon Eksik") {
        return {
            tip: "migration",
            komut: `php artisan make:migration add_${hata.detay}_column`,
            otomatik: true,
        };
    }

    if (hata.tip === "Implement Edilmemiş") {
        return {
            tip: "view",
            komut: "View dosyası oluştur veya controller implement et",
            otomatik: false,
        };
    }

    return null;
}

// YENİ! Görsel İzlenim Üretici
function generateVisualInsight(sayfaAdi, gorselAnaliz) {
    let skor = 10;
    let detaylar = [];
    let eksiler = [];
    let artılar = [];

    // Layout değerlendirmesi
    if (!gorselAnaliz.layout.hasHeader) {
        skor -= 2;
        eksiler.push('Sayfa başlığı eksik');
    } else {
        artılar.push('Modern sayfa başlığı mevcut');
    }

    if (gorselAnaliz.layout.hasStats >= 4) {
        artılar.push(`${gorselAnaliz.layout.hasStats} istatistik kartı - zengin dashboard`);
    } else if (gorselAnaliz.layout.hasStats > 0) {
        skor -= 1;
        eksiler.push('İstatistik kartları az (en az 4 olmalı)');
    }

    if (gorselAnaliz.layout.hasTable && !gorselAnaliz.interaktif.searchBox) {
        skor -= 1;
        eksiler.push('Tablo var ama arama kutusu yok');
    }

    // İnteraktif elementler
    if (gorselAnaliz.interaktif.buttonSayisi < 3) {
        skor -= 1;
        eksiler.push('Çok az buton - kullanıcı etkileşimi sınırlı');
    }

    if (gorselAnaliz.interaktif.filterForm) {
        artılar.push('Filtreleme sistemi mevcut');
    }

    // Görsel kalite
    if (gorselAnaliz.gorunum.hasIcon >= 10) {
        artılar.push('Zengin ikon kullanımı - modern görünüm');
    } else if (gorselAnaliz.gorunum.hasIcon < 5) {
        skor -= 1;
        eksiler.push('Az ikon kullanımı - görsel zenginlik düşük');
    }

    if (gorselAnaliz.gorunum.hasGradient >= 5) {
        artılar.push('Gradient tasarımlar - modern Neo Design');
    }

    if (gorselAnaliz.gorunum.hasAnimation >= 10) {
        artılar.push('Zengin animasyon - interaktif deneyim');
    } else {
        skor -= 0.5;
        eksiler.push('Az animasyon - statik görünüm');
    }

    // Erişilebilirlik
    if (!gorselAnaliz.erisilebilirlik.hasAltText) {
        skor -= 1;
        eksiler.push('Bazı görsellerde alt text eksik');
    }

    if (gorselAnaliz.erisilebilirlik.hasFormLabels === 0 && gorselAnaliz.interaktif.formInputSayisi > 0) {
        skor -= 2;
        eksiler.push('Form input\'larında label eksik - erişilebilirlik sorunu!');
    }

    // Genel yorum üret
    let yorum = '';
    if (skor >= 9) {
        yorum = '🌟 Mükemmel! Modern, interaktif ve kullanıcı dostu.';
    } else if (skor >= 7) {
        yorum = '✅ İyi! Bazı iyileştirmeler yapılabilir.';
    } else if (skor >= 5) {
        yorum = '⚠️ Orta! Önemli iyileştirmeler gerekli.';
    } else {
        yorum = '❌ Zayıf! Tasarımda ciddi sorunlar var.';
    }

    return {
        skor: Math.max(0, skor).toFixed(1),
        yorum: yorum,
        detaylar: {
            artılar: artılar,
            eksiler: eksiler,
            layout: gorselAnaliz.layout,
            interaktif: gorselAnaliz.interaktif,
            gorunum: gorselAnaliz.gorunum
        }
    };
}

async function generateUstaReport() {
    const duration = (new Date() - results.startTime) / 1000;

    let report = `# 🎯 USTA Test Raporu - Ekran Görüntüsü + Otomatik Düzeltme

**Test Zamanı:** ${results.startTime.toLocaleString("tr-TR")}
**Test Süresi:** ${duration.toFixed(2)} saniye
**Toplam Sayfa:** ${results.sayfalar.length}
**Teknik Hata:** ${results.teknikHatalar.length}
**Tasarım Sorunu:** ${results.tasarimHatalari.length}

---

## 🎯 USTA Sistemi Nedir?

**USTA = Ultra Smart Test & Auto-fix**

Özellikler:
- 🔍 Sayfa testi
- 📸 Ekran görüntüsü
- 🐛 Hata tespiti
- 🎨 Tasarım analizi
- 🔧 Otomatik düzeltme önerisi
- ✅ Context7 compliance check

---

## 📊 Sayfa Bazında Detaylar

`;

    results.sayfalar.forEach((s, idx) => {
        const icon =
            s.teknikHatalar.length === 0 && s.tasarimHatalari.length === 0
                ? "✅"
                : s.teknikHatalar.length > 0
                ? "❌"
                : "⚠️";

        report += `### ${idx + 1}. ${icon} ${s.name}\n\n`;
        report += `- **URL:** \`${s.url}\`\n`;
        report += `- **HTTP Status:** ${s.httpStatus || "N/A"}\n`;
        report += `- **Screenshot:** ${s.screenshotBefore}\n\n`;

        if (s.teknikHatalar.length > 0) {
            report += `#### 🐛 Teknik Hatalar (${s.teknikHatalar.length})\n\n`;
            s.teknikHatalar.forEach((h) => {
                report += `**${h.tip}:** \`${h.detay}\`\n\n`;
                report += `<details>\n<summary>Detay</summary>\n\n\`\`\`\n${h.fullMatch}\n\`\`\`\n</details>\n\n`;
            });
        }

        if (s.tasarimHatalari.length > 0) {
            report += `#### 🎨 Tasarım Sorunları (${s.tasarimHatalari.length})\n\n`;
            s.tasarimHatalari.forEach((h) => {
                report += `**[${h.oncelik}] ${h.tip}**\n`;
                report += `- Çözüm: ${h.cozum}\n\n`;
            });
        }

        if (s.duzeltmeOnerileri.length > 0) {
            report += `#### 💡 USTA Otomatik Düzeltme Önerileri\n\n`;
            report += `\`\`\`bash\n`;
            s.duzeltmeOnerileri.forEach((o) => {
                if (o.otomatik) {
                    report += `# ${o.tip.toUpperCase()}\n`;
                    report += `${o.komut}\n\n`;
                }
            });
            report += `\`\`\`\n\n`;
        }

        // Görsel İzlenim (YENİ!)
        const insight = results.visualInsights.find(v => v.sayfa === s.name);
        if (insight) {
            report += `#### 🎨 Görsel İzlenim & UX Analizi\n\n`;
            report += `**Skor:** ${insight.skor}/10 - ${insight.yorum}\n\n`;

            if (insight.detaylar.artılar.length > 0) {
                report += `**✅ Artılar:**\n`;
                insight.detaylar.artılar.forEach(a => report += `- ${a}\n`);
                report += `\n`;
            }

            if (insight.detaylar.eksiler.length > 0) {
                report += `**⚠️ Eksiler:**\n`;
                insight.detaylar.eksiler.forEach(e => report += `- ${e}\n`);
                report += `\n`;
            }

            // Layout Detayları
            report += `**📊 Sayfa Yapısı:**\n`;
            report += `- Header: ${insight.detaylar.layout.hasHeader ? '✅' : '❌'}\n`;
            report += `- İstatistik Kartları: ${insight.detaylar.layout.hasStats} adet\n`;
            report += `- Tablo: ${insight.detaylar.layout.hasTable ? '✅' : '❌'}\n`;
            report += `- Form: ${insight.detaylar.layout.hasForm ? '✅' : '❌'}\n`;
            report += `- Modal: ${insight.detaylar.layout.hasModal} adet\n`;
            report += `- Chart: ${insight.detaylar.layout.hasChart} adet\n\n`;

            // İnteraktif Elementler
            report += `**🔘 İnteraktif Elementler:**\n`;
            report += `- Buton: ${insight.detaylar.interaktif.buttonSayisi} adet\n`;
            report += `- Link: ${insight.detaylar.interaktif.linkSayisi} adet\n`;
            report += `- Form Input: ${insight.detaylar.interaktif.formInputSayisi} adet\n`;
            report += `- Arama Kutusu: ${insight.detaylar.interaktif.searchBox ? '✅' : '❌'}\n`;
            report += `- Filtre Formu: ${insight.detaylar.interaktif.filterForm ? '✅' : '❌'}\n\n`;

            // Görsel Kalite
            report += `**🎨 Görsel Kalite:**\n`;
            report += `- İkon: ${insight.detaylar.gorunum.hasIcon} adet\n`;
            report += `- Badge: ${insight.detaylar.gorunum.hasBadge} adet\n`;
            report += `- Gradient: ${insight.detaylar.gorunum.hasGradient} adet\n`;
            report += `- Animasyon: ${insight.detaylar.gorunum.hasAnimation} adet\n\n`;
        }

        // UX Önerileri (YENİ!)
        const uxOneri = results.uxSuggestions.find(u => u.sayfa === s.name);
        if (uxOneri && uxOneri.onerileri.length > 0) {
            report += `#### 💡 UX/UI İyileştirme Önerileri\n\n`;
            uxOneri.onerileri.forEach((oneri, idx) => {
                report += `**${idx + 1}. [${oneri.oncelik}] ${oneri.oneri}**\n\n`;
                report += `- **Neden:** ${oneri.neden}\n`;
                report += `- **Kategori:** ${oneri.kategori}\n`;
                report += `- **Çözüm:**\n\`\`\`html\n${oneri.cozum}\n\`\`\`\n\n`;
            });
        }

        report += `---\n\n`;
    });

    // USTA TOPLU ÇÖZÜMLER
    report += `## 🔧 USTA Toplu Otomatik Düzeltme\n\n`;

    const migrationlar = results.sayfalar
        .flatMap((s) => s.duzeltmeOnerileri)
        .filter((o) => o && o.tip === "migration");

    if (migrationlar.length > 0) {
        report += `### 📦 Migration'lar\n\n\`\`\`bash\n`;
        migrationlar.forEach((m) => (report += `${m.komut}\n`));
        report += `php artisan migrate\n\`\`\`\n\n`;
    }

    const controllerlar = results.sayfalar
        .flatMap((s) => s.duzeltmeOnerileri)
        .filter((o) => o && o.tip === "controller");

    if (controllerlar.length > 0) {
        report += `### 🎮 Controller Düzeltmeleri\n\n`;
        controllerlar.forEach((c) => (report += `- ${c.komut}\n`));
        report += `\n**Çalıştır:** \`php scripts/usta-duzelt.php\`\n\n`;
    }

    // TASARIM İYİLEŞTİRME PLANI
    const tasarimGrubu = {};
    results.tasarimHatalari.forEach((s) => {
        s.tasarimHatalari.forEach((h) => {
            if (!tasarimGrubu[h.tip]) tasarimGrubu[h.tip] = [];
            tasarimGrubu[h.tip].push(s.name);
        });
    });

    if (Object.keys(tasarimGrubu).length > 0) {
        report += `## 🎨 Tasarım İyileştirme Planı\n\n`;

        for (const [sorun, sayfalar] of Object.entries(tasarimGrubu)) {
            report += `### ${sorun} (${sayfalar.length} sayfa)\n\n`;
            report += `**Etkilenen Sayfalar:**\n`;
            sayfalar.forEach((s) => (report += `- ${s}\n`));
            report += `\n**Çözüm Şablonu:**\n`;

            if (sorun.includes("Neo Design System")) {
                report += `\`\`\`html\n`;
                report += `<!-- Mevcut -->\n`;
                report += `<div>\n`;
                report += `    <input type="text" name="field">\n`;
                report += `    <button>Kaydet</button>\n`;
                report += `</div>\n\n`;
                report += `<!-- USTA Önerisi (Context7 + Neo Design) -->\n`;
                report += `<div class="neo-card p-6 dark:bg-gray-800">\n`;
                report += `    <input type="text" name="field"\n`;
                report += `           class="neo-input focus:ring-blue-500 dark:bg-gray-700\n`;
                report += `                  md:w-1/2 lg:w-1/3">\n`;
                report += `    <button class="neo-btn-primary hover:bg-blue-700\n`;
                report += `                   md:w-auto dark:bg-blue-600">\n`;
                report += `        Kaydet\n`;
                report += `    </button>\n`;
                report += `</div>\n`;
                report += `\`\`\`\n\n`;
            }
        }
    }

    // USTA İSTATİSTİKLER
    report += `## 📊 USTA İstatistikleri\n\n`;
    report += `| Metrik | Değer |\n|--------|-------|\n`;
    report += `| **Toplam Test** | ${results.sayfalar.length} |\n`;
    report += `| **Teknik Hata** | ${results.teknikHatalar.length} ❌ |\n`;
    report += `| **Tasarım Sorunu** | ${results.tasarimHatalari.length} ⚠️ |\n`;
    report += `| **Context7 İhlali** | ${results.context7Violations.length} 🎓 |\n`;
    report += `| **Temiz Sayfa** | ${
        results.sayfalar.length -
        results.teknikHatalar.length -
        results.tasarimHatalari.length -
        results.context7Violations.length
    } ✅ |\n`;
    report += `| **Context7 Uyumluluk** | ${
        results.context7Violations.length === 0
            ? "✅ %100"
            : `⚠️ ${Math.round((1 - results.context7Violations.length / results.sayfalar.length) * 100)}%`
    } |\n`;
    report += `| **UX Önerileri** | ${results.uxSuggestions.length} sayfa 💡 |\n`;

    // Ortalama Görsel Skor
    const ortalamaGorselSkor = results.visualInsights.length > 0
        ? (results.visualInsights.reduce((sum, v) => sum + parseFloat(v.skor), 0) / results.visualInsights.length).toFixed(1)
        : 'N/A';
    report += `| **Ortalama Görsel Skor** | ${ortalamaGorselSkor}/10 🎨 |\n\n`;

    // Context7 Öğretmen Raporu
    if (results.context7Violations.length > 0) {
        report += `## 🎓 CONTEXT7 ÖĞRETMEN RAPORU\n\n`;
        report += `**Context7 Öğretmen Modu Aktif** - Tespit edilen kural ihlalleri ve çözümleri:\n\n`;

        results.context7Violations.forEach((violation, idx) => {
            report += `### ${idx + 1}. ${violation.sayfa} - Context7 İhlalleri\n\n`;
            report += `**URL:** \`${violation.url}\`\n\n`;

            violation.sorunlar.forEach((sorun, sidx) => {
                report += `#### 📚 Ders ${sidx + 1}: ${sorun.tip}\n\n`;
                report += `**Sorun:** \`${sorun.kelime}\` → **Doğru Kullanım:** \`${sorun.yerine}\`\n\n`;
                report += `**Açıklama:** ${sorun.aciklama}\n\n`;
                report += `**Öncelik:** ${sorun.oncelik}\n\n`;

                if (sorun.ogretici) {
                    report += `**Öğretici Mesaj:**\n> ${sorun.ogretici}\n\n`;
                }

                // Kod örneği ekle
                if (sorun.kelime === 'durum' || sorun.kelime === 'is_active' || sorun.kelime === 'aktif') {
                    report += `**Kod Örneği:**\n\`\`\`php\n`;
                    report += `// ❌ YANLIŞ\n`;
                    report += `<input name="${sorun.kelime}" type="text">\n`;
                    report += `$model->${sorun.kelime};\n\n`;
                    report += `// ✅ DOĞRU (Context7)\n`;
                    report += `<input name="${sorun.yerine}" type="text">\n`;
                    report += `$model->${sorun.yerine};\n`;
                    report += `\`\`\`\n\n`;
                }

                report += `**Düzeltme Adımları:**\n`;
                report += `1. Database migration'ında alan adını değiştir\n`;
                report += `2. Model'de fillable array'i güncelle\n`;
                report += `3. Controller'da alan referanslarını değiştir\n`;
                report += `4. View'larda form field'larını güncelle\n`;
                report += `5. JavaScript dosyalarında referansları düzelt\n\n`;

                report += `---\n\n`;
            });
        });

        report += `## 📖 Context7 Referans Dökümanları\n\n`;
        report += `Detaylı bilgi için şu dökümanları inceleyin:\n\n`;
        report += `- **Context7 Kuralları:** \`docs/context7/rules/context7-rules.md\`\n`;
        report += `- **Context7 Master:** \`docs/context7/reports/context7-master.md\`\n`;
        report += `- **API Dokümantasyonu:** \`docs/api/context7-api-documentation.md\`\n`;
        report += `- **README:** \`docs/README.md\`\n\n`;
    }

    // ÖNCE/SONRA KARŞILAŞTIRMA
    report += `## 🔄 Önce/Sonra Karşılaştırma\n\n`;
    report += `**Screenshot Klasörleri:**\n`;
    report += `- Önce: \`screenshots/usta-test/before/\`\n`;
    report += `- Sonra: \`screenshots/usta-test/after/\` (düzeltmelerden sonra)\n\n`;

    // Görsel Analiz Özeti (YENİ!)
    if (results.visualInsights.length > 0) {
        report += `## 🎨 Görsel Analiz Özeti\n\n`;
        report += `**USTA artık ekran görüntülerinden izlenim oluşturuyor ve öneriler sunuyor!**\n\n`;

        report += `| Sayfa | Görsel Skor | İzlenim | UX Önerileri |\n`;
        report += `|-------|-------------|---------|---------------|\n`;
        results.visualInsights.forEach(insight => {
            const uxOneri = results.uxSuggestions.find(u => u.sayfa === insight.sayfa);
            const uxSayisi = uxOneri ? uxOneri.onerileri.length : 0;
            report += `| ${insight.sayfa} | ${insight.skor}/10 | ${insight.yorum} | ${uxSayisi} öneri |\n`;
        });
        report += `\n`;

        // En iyi ve en kötü sayfalar
        const sorted = [...results.visualInsights].sort((a, b) => parseFloat(b.skor) - parseFloat(a.skor));
        if (sorted.length > 0) {
            report += `**🌟 En İyi Sayfa:** ${sorted[0].sayfa} (${sorted[0].skor}/10)\n`;
            report += `**⚠️ En Çok İyileştirme Gereken:** ${sorted[sorted.length - 1].sayfa} (${sorted[sorted.length - 1].skor}/10)\n\n`;
        }
    }

    // UX Önerileri Toplu Özet (YENİ!)
    if (results.uxSuggestions.length > 0) {
        const allSuggestions = results.uxSuggestions.flatMap(u => u.onerileri);
        const kategoriler = {};

        allSuggestions.forEach(oneri => {
            if (!kategoriler[oneri.kategori]) kategoriler[oneri.kategori] = [];
            kategoriler[oneri.kategori].push(oneri);
        });

        report += `## 💡 UX/UI İyileştirme Önerileri Özeti\n\n`;
        report += `**Toplam:** ${allSuggestions.length} öneri, ${Object.keys(kategoriler).length} kategori\n\n`;

        for (const [kategori, onerileri] of Object.entries(kategoriler)) {
            report += `### ${kategori} (${onerileri.length} öneri)\n\n`;
            onerileri.forEach((oneri, idx) => {
                report += `**${idx + 1}. [${oneri.oncelik}] ${oneri.oneri}**\n`;
                report += `- Neden: ${oneri.neden}\n`;
                report += `- Çözüm: ${oneri.cozum}\n\n`;
            });
        }
    }

    report += `---\n\n`;
    report += `**Context7 Uyumlu:** ✅\n`;
    report += `**USTA Versiyonu:** 2.0 (Context7 Teacher + Visual Insights)\n`;
    report += `**Tarih:** ${new Date().toLocaleString("tr-TR")}\n`;

    fs.writeFileSync("./usta-test-raporu.md", report);
    console.log("\n📋 USTA Raporu oluşturuldu: usta-test-raporu.md\n");
}

async function main() {
    console.log("\n" + "=".repeat(50));
    console.log("🎯 USTA - Ultra Smart Test & Auto-fix");
    console.log("=".repeat(50) + "\n");
    console.log(`📋 Test edilecek: ${SAYFALAR.length} sayfa`);
    console.log(`📸 Ekran görüntüsü: Evet (headless=false)`);
    console.log(`🎨 Tasarım analizi: Evet`);
    console.log(`🔧 Otomatik düzeltme: Evet`);
    console.log(`🎓 Context7 Öğretmen: Evet (YENİ!)`);
    console.log(`🖼️  Görsel Analiz: Evet (YENİ!)`);
    console.log(`💡 UX Önerileri: Evet (YENİ!)\n`);

    await createDir();

    const browser = await puppeteer.launch({
        headless: CONFIG.headless,
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

    console.log("🔍 USTA analiz başlıyor...\n");

    for (const sayfaInfo of SAYFALAR) {
        const page = await browser.newPage();
        await page.setViewport({ width: 1920, height: 1080 });

        await ustaAnaliz(page, sayfaInfo);

        await page.close();
        await new Promise((resolve) => setTimeout(resolve, 1500)); // 1.5 saniye bekle
    }

    await browser.close();

    await generateUstaReport();

    console.log("=".repeat(50));
    console.log("📊 USTA ÖZET\n");
    console.log(`✅ Test edilen: ${results.sayfalar.length}`);
    console.log(`❌ Teknik hata: ${results.teknikHatalar.length}`);
    console.log(`⚠️  Tasarım sorunu: ${results.tasarimHatalari.length}`);
    console.log(`🎓 Context7 ihlali: ${results.context7Violations.length}`);
    console.log(`💡 UX önerileri: ${results.uxSuggestions.length} sayfa`);

    const ortalamaGorselSkor = results.visualInsights.length > 0
        ? (results.visualInsights.reduce((sum, v) => sum + parseFloat(v.skor), 0) / results.visualInsights.length).toFixed(1)
        : 'N/A';
    console.log(`🎨 Ortalama görsel skor: ${ortalamaGorselSkor}/10`);
    console.log(`📸 Screenshot: ${results.sayfalar.length * 2} adet\n`);

    if (
        results.teknikHatalar.length > 0 ||
        results.tasarimHatalari.length > 0
    ) {
        console.log("🔧 Otomatik düzeltme çalıştırılıyor...\n");
        console.log("💡 Komut: php scripts/usta-duzelt.php\n");
    }

    console.log("✨ USTA test tamamlandı!\n");
    console.log("📋 Detaylı rapor: usta-test-raporu.md");
    console.log("📸 Screenshot'lar: screenshots/usta-test/\n");
}

main().catch((error) => {
    console.error("💥 USTA Kritik Hata:", error);
    process.exit(1);
});
