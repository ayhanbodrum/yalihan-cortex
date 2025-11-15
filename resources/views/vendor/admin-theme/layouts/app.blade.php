<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() }}">

    <title>@yield('title', 'Admin Panel') | Yalıhan Emlak</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Vite Derlenmiş CSS -->
    @php
        $manifestPath = public_path('build/manifest.json');
        $manifest = file_exists($manifestPath) ? json_decode(file_get_contents($manifestPath), true) : [];
        $viteAsset = function ($src) use ($manifest) {
            if (isset($manifest[$src])) {
                return asset('build/' . $manifest[$src]['file']);
            }
            return null;
        };
    @endphp
    @if ($cssApp = $viteAsset('resources/css/app.css'))
        <link rel="stylesheet" href="{{ $cssApp }}" />
    @endif
    @if ($cssNeo = $viteAsset('resources/css/space-y-4.css'))
        <link rel="stylesheet" href="{{ $cssNeo }}" />
    @endif

    {{-- Removed broken CSS links (files don't exist):
         - quick-search.css
         - dynamic-form-fields.css
         - form-standards.css
         Using Vite build instead
    --}}

    <!-- Additional Styles -->
    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Modern Dark Sidebar Enhancements */
        .sidebar-dark {
            background: linear-gradient(180deg, #1f2937 0%, #111827 100%);
            color: #e5e7eb;
        }

        .sidebar-dark .sidebar-header-border {
            border-color: rgba(255, 255, 255, 0.06);
        }

        .sidebar-dark nav {
            scrollbar-width: thin;
        }

        .sidebar-dark nav::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-dark nav::-webkit-scrollbar-track {
            background: #1f2937;
        }

        .sidebar-dark nav::-webkit-scrollbar-thumb {
            background: #374151;
            border-radius: 4px;
        }

        .sidebar-dark a,
        .sidebar-dark button {
            color: #d1d5db;
        }

        .sidebar-dark a svg,
        .sidebar-dark button svg {
            stroke-width: 1.8;
        }

        .sidebar-dark .space-y-1>a,
        .sidebar-dark .space-y-1>div>a,
        .sidebar-dark .space-y-1>div>button,
        .sidebar-dark .space-y-1 [x-show] a {
            position: relative;
            border-radius: 0.65rem;
            font-size: 0.85rem;
            font-weight: 500;
            letter-spacing: .2px;
        }

        .sidebar-dark .space-y-1>a:hover,
        .sidebar-dark .space-y-1>div>a:hover,
        .sidebar-dark .space-y-1>div>button:hover,
        .sidebar-dark .space-y-1 [x-show] a:hover {
            background: rgba(255, 255, 255, 0.05) !important;
            color: #f97316 !important;
        }

        /* Active states override Tailwind light bg classes */
        .sidebar-dark a.bg-orange-100,
        .sidebar-dark a.dark\:bg-orange-900,
        .sidebar-dark button.bg-orange-100,
        .sidebar-dark button.dark\:bg-orange-900 {
            background: linear-gradient(90deg, rgba(249, 115, 22, 0.22) 0%, rgba(249, 115, 22, 0.05) 100%) !important;
            color: #f97316 !important;
            box-shadow: inset 2px 0 0 0 #f97316;
        }

        .sidebar-dark .submenu a.bg-orange-100,
        .sidebar-dark .submenu a.dark\:bg-orange-900 {
            box-shadow: none;
            inset: 0;
        }

        .sidebar-dark .submenu {
            border-left: 1px solid #374151;
            margin-left: .5rem;
            padding-left: .5rem;
        }

        .sidebar-dark .submenu a {
            border-radius: .5rem;
        }

        .sidebar-dark .submenu a:hover {
            background: rgba(255, 255, 255, 0.05) !important;
        }

        .sidebar-dark .section-title {
            margin: 1.25rem 0 .35rem;
            padding: 0 .75rem;
            font-size: .6rem;
            text-transform: uppercase;
            letter-spacing: .12em;
            font-weight: 600;
            color: #6b7280;
        }

        .sidebar-dark .divider {
            height: 1px;
            margin: .75rem .75rem;
            background: linear-gradient(90deg, transparent, #374151 40%, #374151 60%, transparent);
        }

        @media (max-width: 768px) {
            .sidebar-dark {
                backdrop-filter: blur(10px);
            }
        }
    </style>

    <!-- Legacy jQuery/Select2 removed - 2025-10-21 -->
    <!-- Use Context7 Live Search instead -->
    <script src="{{ asset('js/context7-live-search.js') }}"></script>
    <link href="{{ asset('css/context7-live-search.css') }}" rel="stylesheet">

    <!-- Leaflet.js for Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Alpine.js - Load only if not loaded in page -->
    @if (!isset($skipAlpineJs))
        <!-- Alpine.js Main Library -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Fallback global function so x-data="darkMode()" won't throw if evaluated early -->
        <script>
            // Provide a safe global factory for usages like x-data="darkMode()"
            // This prevents "darkMode is not defined" errors when Alpine evaluates expressions
            // before the Alpine.data registration runs in some page load orders.
            if (typeof window.darkMode === 'undefined') {
                window.darkMode = function() {
                    return {
                        darkMode: localStorage.getItem('darkMode') === 'true',
                        init() {
                            if (this.darkMode) {
                                document.body.classList.add('dark-mode');
                            }
                        },
                        toggleDarkMode() {
                            this.darkMode = !this.darkMode;
                            localStorage.setItem('darkMode', this.darkMode);
                            document.body.classList.toggle('dark-mode');
                        }
                    };
                };
            }
        </script>

        <!-- Alpine.js Plugin Registration -->
        <script>
            document.addEventListener('alpine:init', () => {
                // Alpine.js başarıyla yüklendi
                console.log('✅ Alpine.js başarıyla yüklendi');
            });
        </script>


        <!-- Alpine.js error handler for dashboard pages -->
        <script>
            // Global hata yakalayıcı - tanımlanmamış değişkenleri yakalar
            window.addEventListener('error', function(e) {
                if (e.message.includes('is not defined')) {
                    console.warn('⚠️ Tanımlanmamış değişken yakalandı:', e.message);
                    e.preventDefault();
                }
            });

            // Global değişkenler - hataları önlemek için
            window.activeTooltip = null;
            window.errors = {};
            window.formData = {};

            // İlan Ekleme 2.0 global değişkenleri
            window.autoSaveStatus = '';
            window.lastSavedAt = null;
            window.aiSuggestions = {
                title: '',
                description: '',
                isGenerating: false
            };
            window.isArsa = false;

            // Global fonksiyonlar
            window.formatCurrency = function(amount, currency = 'TRY') {
                if (!amount) return '0 ₺';
                return new Intl.NumberFormat('tr-TR', {
                    style: 'currency',
                    currency: currency
                }).format(amount);
            };

            // Arsa hesaplama fonksiyonları
            window.calculateLandUnitPrice = function() {
                const fiyat = parseFloat(document.getElementById('fiyat')?.value || 0);
                const alan = parseFloat(document.getElementById('alan_m2')?.value || 0);
                return alan > 0 ? fiyat / alan : 0;
            };

            window.calculateKAKS = function(arsaAlani, toplamInsaatAlani) {
                return arsaAlani > 0 ? toplamInsaatAlani / arsaAlani : 0;
            };

            window.calculateTAKS = function(arsaAlani, tabanAlani) {
                return arsaAlani > 0 ? tabanAlani / arsaAlani : 0;
            };

            window.convertM2ToDunum = function(alanM2) {
                return alanM2 / 1000;
            };

            window.convertDunumToM2 = function(alanDunum) {
                return alanDunum * 1000;
            };

            // Arsa Calculator Alpine.js bileşeni
            window.arsaCalculator = function() {
                return {
                    // Veriler
                    arsaAlani: 0,
                    imarDurumu: '',
                    kaks: 0,
                    taks: 0,
                    toplamFiyat: '',
                    metreFiyati: '',
                    yaziliFiyat: '',
                    maxInsaatAlani: 0,
                    maxTabanAlani: 0,
                    isValidArsa: true,

                    // İmar limitleri
                    imarLimits: {
                        konut: {
                            maxKaks: 1.5,
                            maxTaks: 0.3
                        },
                        ticari: {
                            maxKaks: 2.5,
                            maxTaks: 0.5
                        },
                        sanayi: {
                            maxKaks: 1.2,
                            maxTaks: 0.4
                        },
                        tarla: {
                            maxKaks: 0.0,
                            maxTaks: 0.0
                        },
                        bahce: {
                            maxKaks: 0.15,
                            maxTaks: 0.1
                        }
                    },

                    // Tüm hesaplamaları yap
                    calculateAll() {
                        this.calculateInsat();
                        this.calculateTaban();
                        this.calculateMetreFiyati();
                        this.validateArsa();
                    },

                    // İnşaat alanı hesapla
                    calculateInsat() {
                        this.maxInsaatAlani = this.arsaAlani * this.kaks;
                    },

                    // Taban alanı hesapla
                    calculateTaban() {
                        this.maxTabanAlani = this.arsaAlani * this.taks;
                    },

                    // Metrekare fiyatı hesapla
                    calculateMetreFiyati() {
                        const fiyat = parseFloat(this.toplamFiyat.replace(/[^\d]/g, ''));
                        if (this.arsaAlani > 0 && fiyat > 0) {
                            this.metreFiyati = (fiyat / this.arsaAlani).toFixed(2);
                        } else {
                            this.metreFiyati = '0.00';
                        }
                    },

                    // İmar limitlerini güncelle
                    updateImarLimits() {
                        const limit = this.imarLimits[this.imarDurumu];
                        if (limit) {
                            if (this.kaks > limit.maxKaks) this.kaks = limit.maxKaks;
                            if (this.taks > limit.maxTaks) this.taks = limit.maxTaks;
                        }
                        this.validateArsa();
                    },

                    // Arsa validasyonu
                    validateArsa() {
                        let isValid = true;

                        if (this.arsaAlani < 200) isValid = false;
                        if (this.kaks > 5.0) isValid = false;
                        if (this.taks > 1.0) isValid = false;
                        if (this.kaks < this.taks) isValid = false;

                        this.isValidArsa = isValid;
                    },

                    // Dönüm gösterimi
                    formatDonumDisplay() {
                        if (this.arsaAlani > 0) {
                            const donum = this.arsaAlani / 1000;
                            return `${this.arsaAlani} m² = ${donum.toFixed(2)} dönüm`;
                        }
                        return '';
                    },

                    // Sayı formatla
                    formatNumber(num) {
                        return new Intl.NumberFormat('tr-TR').format(num);
                    },

                    // Başlatma
                    init() {
                        this.calculateAll();
                    }
                };
            };

            // İlan form logic fonksiyonu
            window.ilanFormLogic = function() {
                return {
                    // Form verileri
                    formData: {
                        kategori_id: '',
                        ana_kategori_id: '',
                        yayin_tipi: '',
                        fiyat: '',
                        para_birimi: 'TRY',
                        metrekare: '',
                        secili_ozellikler: [],
                        baslik: '',
                        aciklama: '',
                        // Arsa alanları
                        alan_m2: '',
                        kaks: '',
                        taks: '',
                        imar_statusu: '',
                        ada_parsel: '',
                        // Yazlık alanları
                        max_misafir: '',
                        min_konaklama: '',
                        max_konaklama: '',
                        havuz_var: false,
                        havuz_boyut: '',
                        havuz_derinlik: '',
                        havuz_turu: '',
                        giris_saati: '',
                        cikis_saati: '',
                        dahil_hizmetler: [],
                        elektrik_dahil: '',
                        depozito: '',
                        hayvan_izni: '',
                        ozel_kurallar: '',
                        ozellikler: [],
                        emlak_turu: ''
                    },

                    // Hata mesajları
                    errors: {},

                    // Tooltip statusu
                    activeTooltip: null,

                    // Fiyat hesaplama
                    priceText: '',
                    m2PriceText: '',

                    // Çoklu fiyat alanları
                    multiPrices: {
                        gunluk: '',
                        haftalik: '',
                        aylik: '',
                        sezonluk: ''
                    },

                    // Arsa hesaplama fonksiyonları
                    calculateLandUnitPrice() {
                        if (!this.formData.fiyat || !this.formData.alan_m2) return 0;
                        const fiyat = parseFloat(this.formData.fiyat);
                        const alan = parseFloat(this.formData.alan_m2);
                        return alan > 0 ? fiyat / alan : 0;
                    },

                    calculateKAKS(arsaAlani, toplamInsaatAlani) {
                        return arsaAlani > 0 ? toplamInsaatAlani / arsaAlani : 0;
                    },

                    calculateTAKS(arsaAlani, tabanAlani) {
                        return arsaAlani > 0 ? tabanAlani / arsaAlani : 0;
                    },

                    convertM2ToDunum(alanM2) {
                        return alanM2 / 1000;
                    },

                    convertDunumToM2(alanDunum) {
                        return alanDunum * 1000;
                    },

                    // Canlı validasyon
                    liveValidate(field, value) {
                        // Basit validasyon kuralları
                        const rules = {
                            alan_m2: {
                                required: true,
                                min: 200,
                                max: 1000000,
                                pattern: /^[0-9]*\.?[0-9]*$/
                            }
                        };

                        if (rules[field]) {
                            const rule = rules[field];
                            let error = '';

                            if (rule.required && !value) {
                                error = 'Bu alan zorunludur';
                            } else if (rule.min && parseFloat(value) < rule.min) {
                                error = `Minimum değer ${rule.min} olmalıdır`;
                            } else if (rule.max && parseFloat(value) > rule.max) {
                                error = `Maksimum değer ${rule.max} olmalıdır`;
                            } else if (rule.pattern && !rule.pattern.test(value)) {
                                error = 'Geçersiz format';
                            }

                            if (error) {
                                this.errors[field] = error;
                            } else {
                                delete this.errors[field];
                            }
                        }
                    },

                    // Tooltip göster/gizle
                    showTooltip(tooltipName) {
                        this.activeTooltip = tooltipName;
                    },

                    hideTooltip() {
                        this.activeTooltip = null;
                    },

                    // Sayıyı Türkçe yazıya çevir
                    numberToTurkishWords(number) {
                        const ones = ['', 'bir', 'iki', 'üç', 'dört', 'beş', 'altı', 'yedi', 'sekiz', 'dokuz'];
                        const tens = ['', 'on', 'yirmi', 'otuz', 'kırk', 'elli', 'altmış', 'yetmiş', 'seksen', 'doksan'];

                        if (number === 0) return 'sıfır';
                        if (number < 10) return ones[number];
                        if (number < 100) {
                            const ten = Math.floor(number / 10);
                            const one = number % 10;
                            return tens[ten] + (one > 0 ? ' ' + ones[one] : '');
                        }
                        return number.toString();
                    },

                    // Form başlatma
                    init() {
                        console.log('İlan form logic başlatıldı');
                        this.updateProgress();
                    },

                    // Progress güncelleme
                    updateProgress() {
                        const requiredFields = document.querySelectorAll(
                            'input[required], select[required], textarea[required]');
                        const totalFields = requiredFields.length;
                        let filledFields = 0;

                        requiredFields.forEach(field => {
                            if (field.value.trim() !== '') {
                                filledFields++;
                            }
                        });

                        const progress = totalFields > 0 ? Math.round((filledFields / totalFields) * 100) : 0;

                        const progressBar = document.getElementById('progress-bar');
                        const progressText = document.getElementById('progress-text');

                        if (progressBar) progressBar.style.width = progress + '%';
                        if (progressText) progressText.textContent = progress + '%';
                    }
                };
            };

            document.addEventListener('alpine:init', () => {
                // Dashboard sayfaları için güvenli Alpine.js başlatıcısı
                const currentPath = window.location.pathname;

                // Güvenli global fonksiyonlar - hataları önlemek için
                window.featureList = window.featureList || function() {
                    return {
                        features: [],
                        selected: [],
                        init() {
                            console.log('featureList initialized');
                        }
                    };
                };

                window.init = window.init || function() {
                    console.log('init function called');
                };

                window.selected = window.selected || function() {
                    return {
                        items: [],
                        init() {
                            console.log('selected initialized');
                        }
                    };
                };

                window.isActive = window.isActive || function() {
                    return {
                        active: false,
                        init() {
                            console.log('isActive initialized');
                        }
                    };
                };

                // Alpine.js data tanımlamaları
                Alpine.data('featureList', () => ({
                    features: [],
                    selected: [],
                    init() {
                        console.log('Alpine featureList initialized');
                    }
                }));

                Alpine.data('selected', () => ({
                    items: [],
                    init() {
                        console.log('Alpine selected initialized');
                    }
                }));

                Alpine.data('isActive', () => ({
                    active: false,
                    init() {
                        console.log('Alpine isActive initialized');
                    }
                }));

                if (currentPath.includes('/admin/dashboard') || currentPath === '/admin') {
                    // Dashboard için ilanFormLogic tanımla (İlan Ekleme 2.0 uyumlu)
                    window.ilanFormLogic = function() {
                        return {
                            formData: {
                                kategori_id: '',
                                ana_kategori_id: '',
                                yayin_tipi: '',
                                fiyat: '',
                                para_birimi: 'TRY',
                                metrekare: '',
                                secili_ozellikler: [],
                                baslik: '',
                                aciklama: '',
                                owner_id: '',
                                alan_m2: '',
                                kaks: '',
                                taks: '',
                                imar_statusu: '',
                                ada_parsel: '',
                                max_misafir: '',
                                min_konaklama: '',
                                max_konaklama: '',
                                havuz_var: false,
                                havuz_boyut: '',
                                havuz_derinlik: '',
                                havuz_turu: '',
                                giris_saati: '',
                                cikis_saati: '',
                                dahil_hizmetler: [],
                                elektrik_dahil: '',
                                depozito: '',
                                hayvan_izni: '',
                                ozel_kurallar: '',
                                ozellikler: [],
                                emlak_turu: ''
                            },

                            // İlan Ekleme 2.0 değişkenleri
                            currentStage: 'owner',
                            stages: [],
                            autoSaveStatus: '',
                            lastSavedAt: null,
                            isSaving: false,
                            isOwnerSelected: false,
                            isFormLocked: true,
                            aiSuggestions: {
                                title: '',
                                description: '',
                                isGenerating: false
                            },
                            errors: {},
                            activeTooltip: null,
                            isArsa: false,

                            priceText: '',
                            m2PriceText: '',
                            // Çoklu fiyat alanları
                            multiPrices: {
                                gunluk: '',
                                haftalik: '',
                                aylik: '',
                                sezonluk: ''
                            },
                            // Yardımcı: tam sayıyı Türkçe yazıya çevir
                            numberToTurkishWords(number) {
                                if (number === 0) return 'Sıfır';
                                const ones = ['', 'Bir', 'İki', 'Üç', 'Dört', 'Beş', 'Altı', 'Yedi', 'Sekiz',
                                    'Dokuz'
                                ];
                                const tens = ['', 'On', 'Yirmi', 'Otuz', 'Kırk', 'Elli', 'Altmış', 'Yetmiş',
                                    'Seksen', 'Doksan'
                                ];
                                const thousands = ['', 'Bin', 'Milyon', 'Milyar'];
                                let words = '';
                                let groupIndex = 0;
                                while (number > 0) {
                                    let group = number % 1000;
                                    if (group !== 0) {
                                        let groupWords = '';
                                        let hundreds = Math.floor(group / 100);
                                        let remainder = group % 100;
                                        if (hundreds) {
                                            if (hundreds === 1) groupWords += 'Yüz';
                                            else groupWords += ones[hundreds] + ' Yüz';
                                        }
                                        if (remainder) {
                                            if (remainder < 10) groupWords += (groupWords ? ' ' : '') + ones[
                                                remainder];
                                            else if (remainder < 20) {
                                                const teenMap = {
                                                    10: 'On',
                                                    11: 'On Bir',
                                                    12: 'On İki',
                                                    13: 'On Üç',
                                                    14: 'On Dört',
                                                    15: 'On Beş',
                                                    16: 'On Altı',
                                                    17: 'On Yedi',
                                                    18: 'On Sekiz',
                                                    19: 'On Dokuz'
                                                };
                                                groupWords += (groupWords ? ' ' : '') + teenMap[remainder];
                                            } else {
                                                let tenVal = Math.floor(remainder / 10);
                                                let oneVal = remainder % 10;
                                                groupWords += (groupWords ? ' ' : '') + tens[tenVal];
                                                if (oneVal) groupWords += ' ' + ones[oneVal];
                                            }
                                        }
                                        if (groupIndex === 1 && group === 1) {
                                            // "Bir Bin" yerine sadece "Bin"
                                            groupWords = 'Bin';
                                        } else if (thousands[groupIndex]) {
                                            groupWords += ' ' + thousands[groupIndex];
                                        }
                                        words = groupWords + (words ? ' ' + words : '');
                                    }
                                    number = Math.floor(number / 1000);
                                    groupIndex++;
                                }
                                return words.trim();
                            },
                            updatePriceText() {
                                if (!this.formData.fiyat) {
                                    this.priceText = '';
                                    return;
                                }
                                const raw = parseInt(this.formData.fiyat.toString().replace(/[^\d]/g, ''));
                                if (!raw) {
                                    this.priceText = '';
                                    return;
                                }
                                const formatted = new Intl.NumberFormat('tr-TR').format(raw);
                                const words = this.numberToTurkishWords(raw) + ' Türk Lirası';
                                this.priceText = formatted + ' ₺ (' + words + ')';
                                if (this.formData.metrekare && parseInt(this.formData.metrekare) > 0) {
                                    const m2 = Math.round(raw / parseInt(this.formData.metrekare));
                                    this.m2PriceText = new Intl.NumberFormat('tr-TR').format(m2) + ' ₺';
                                } else {
                                    this.m2PriceText = '';
                                }
                            },
                            isArsa() {
                                return ['arsa', 'tarla', 'bahce'].includes((this.formData.emlak_turu || '')
                                    .toLowerCase());
                            },
                            init() {
                                this.$watch('formData.fiyat', () => this.updatePriceText());
                                this.$watch('formData.metrekare', () => this.updatePriceText());
                                this.$watch('formData.yayin_tipi', value => {
                                    if (!['gunluk_kiralik', 'sezonluk_kiralik', 'yazlik_kiralik'].includes(
                                            value)) {
                                        this.multiPrices = {
                                            gunluk: '',
                                            haftalik: '',
                                            aylik: '',
                                            sezonluk: ''
                                        };
                                    }
                                });
                                this.updatePriceText();
                            }
                        };
                    };

                    // Backward compatibility
                    window.wizardForm = window.ilanFormLogic;

                    console.log('Dashboard Alpine.js güvenli modda başlatıldı');
                }
            });
        </script>

        {{-- Dark Mode Alpine component --}}
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('darkMode', () => ({
                    darkMode: localStorage.getItem('darkMode') === 'true',
                    init() {
                        if (this.darkMode) {
                            document.body.classList.add('dark-mode');
                        }
                    },
                    toggleDarkMode() {
                        this.darkMode = !this.darkMode;
                        localStorage.setItem('darkMode', this.darkMode);
                        document.body.classList.toggle('dark-mode');
                    }
                }));
            });
        </script>
    @endif

    @yield('styles')
    @stack('styles')
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 admin-panel" x-data>
    <div x-data="{ sidebarOpen: false }">
        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-20 w-64 sidebar-dark border-r border-gray-700 transform transition-transform duration-300 ease-in-out md:translate-x-0 sidebar"
            :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }">
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between h-16 px-6 border-b sidebar-header-border">
                <a href="{{ route('admin.dashboard.index') }}" class="flex items-center">
                    <span class="text-xl font-bold text-primary">{{ config('company.name') }}</span>
                </a>
                <button @click="sidebarOpen = !sidebarOpen"
                    class="md:hidden text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Sidebar Links -->
            <nav class="flex flex-col h-[calc(100vh-4rem)] p-4 overflow-y-auto">
                <div class="space-y-1">
                    <div class="section-title">{{ ucfirst($userRole ?? 'user') }} Menüsü</div>

                    @if (isset($menuItems) && is_array($menuItems))
                        @foreach ($menuItems as $menuItem)
                            @if (app(\App\Services\MenuService::class)->hasPermission($menuItem['permission']))
                                @if (isset($menuItem['submenu']) && is_array($menuItem['submenu']))
                                    <!-- Submenu Parent -->
                                    <div class="menu-group">
                                        <div class="flex items-center px-4 py-2 rounded-lg transition-all duration-300 text-gray-700 dark:text-gray-300 cursor-pointer hover:!bg-orange-50 dark:hover:!bg-gray-700 hover:!text-orange-600 dark:hover:!text-orange-400"
                                            onclick="toggleSubmenu('submenu-{{ $loop->index }}')">
                                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                @if ($menuItem['icon'] === 'home')
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                                    </path>
                                                @elseif($menuItem['icon'] === 'users')
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                                    </path>
                                                @elseif($menuItem['icon'] === 'user-tie')
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                    </path>
                                                @elseif($menuItem['icon'] === 'address-book')
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                                    </path>
                                                @elseif($menuItem['icon'] === 'chart-bar')
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                                    </path>
                                                @elseif($menuItem['icon'] === 'cog')
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                                    </path>
                                                @elseif($menuItem['icon'] === 'envelope')
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                    </path>
                                                @elseif($menuItem['icon'] === 'user')
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                    </path>
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                                    </path>
                                                @endif
                                            </svg>
                                            <span>{{ $menuItem['name'] }}</span>
                                            <svg class="w-4 h-4 ml-auto transition-transform duration-200"
                                                id="arrow-{{ $loop->index }}" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>

                                        <!-- Submenu Items -->
                                        <div id="submenu-{{ $loop->index }}" class="hidden ml-4 mt-1 space-y-1">
                                            @foreach ($menuItem['submenu'] as $subItem)
                                                @if (app(\App\Services\MenuService::class)->hasPermission($subItem['permission']))
                                                    <a href="{{ $subItem['url'] }}"
                                                        class="flex items-center px-4 py-2 text-sm text-gray-600 dark:text-gray-400 rounded-lg hover:!bg-orange-50 dark:hover:!bg-gray-700 hover:!text-orange-600 dark:hover:!text-orange-400">
                                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            @if ($subItem['icon'] === 'users')
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                                                </path>
                                                            @elseif($subItem['icon'] === 'tasks')
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                                                </path>
                                                            @elseif($subItem['icon'] === 'chart-bar')
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                                                </path>
                                                            @endif
                                                        </svg>
                                                        <span>{{ $subItem['name'] }}</span>
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <!-- Regular Menu Item -->
                                    <a href="{{ $menuItem['url'] }}"
                                        class="flex items-center px-4 py-2 rounded-lg transition-all duration-300 {{ $menuItem['active'] ? 'bg-orange-100 dark:bg-gray-900 text-orange-800 dark:text-orange-200' : 'text-gray-700 dark:text-gray-300 hover:!bg-orange-50 dark:hover:!bg-gray-700 hover:!text-orange-600 dark:hover:!text-orange-400' }}">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            @if ($menuItem['icon'] === 'home')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                                </path>
                                            @elseif($menuItem['icon'] === 'users')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                                </path>
                                            @elseif($menuItem['icon'] === 'user-tie')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                </path>
                                            @elseif($menuItem['icon'] === 'address-book')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                                </path>
                                            @elseif($menuItem['icon'] === 'chart-bar')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                                </path>
                                            @elseif($menuItem['icon'] === 'cog')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                                </path>
                                            @elseif($menuItem['icon'] === 'envelope')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                </path>
                                            @elseif($menuItem['icon'] === 'user')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                </path>
                                            @elseif($menuItem['icon'] === 'magic')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                </path>
                                            @elseif($menuItem['icon'] === 'brain')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z">
                                                </path>
                                            @elseif($menuItem['icon'] === 'chart-line')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                                </path>
                                            @elseif($menuItem['icon'] === 'users-cog')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                                </path>
                                            @elseif($menuItem['icon'] === 'tasks')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                                </path>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                                </path>
                                            @endif
                                        </svg>
                                        <span>{{ $menuItem['name'] }}</span>
                                    </a>
                                @endif
                            @endif
                        @endforeach
                    @else
                        <!-- Fallback menu if menuItems not available -->
                        <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                            <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 19.5c-.77.833.192 2.5 1.732 2.5z">
                                </path>
                            </svg>
                            <p class="text-sm">Menü yükleniyor...</p>
                        </div>
                    @endif
                </div>

                <div class="mt-auto pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ url('/profile') }}"
                        class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Profilim</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="flex w-full items-center px-4 py-2 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                            <span>Çıkış Yap</span>
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="md:ml-64 min-h-screen">
            <!-- Top Navigation -->
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="flex items-center justify-between h-16 px-6">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="text-gray-500 hover:text-gray-700 focus:outline-none md:hidden">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <div class="flex items-center">
                        <h2 class="text-xl font-semibold">@yield('page-title', 'Dashboard')</h2>
                    </div>

                    <div class="flex items-center space-x-4" x-data="{
                        darkMode: localStorage.getItem('darkMode') === 'true',
                        toggleDarkMode() {
                            this.darkMode = !this.darkMode;
                            localStorage.setItem('darkMode', this.darkMode);
                            document.documentElement.classList.toggle('dark', this.darkMode);
                        }
                    }">
                        <button @click="toggleDarkMode" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                            <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                                </path>
                            </svg>
                            <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                        </button>
                        <!-- Dark Mode Toggle -->
                        <button type="button" x-data @click="document.documentElement.classList.toggle('dark')"
                            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 focus:outline-none">
                            <svg class="hidden dark:block w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                            <svg class="block dark:hidden w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                                </path>
                            </svg>
                        </button>

                        <!-- Profile Dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center focus:outline-none">
                                <div
                                    class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center text-white">
                                    {{ auth()->user() ? substr(auth()->user()->name, 0, 1) : 'U' }}
                                </div>
                                <span
                                    class="ml-2 text-sm font-medium hidden md:block">{{ auth()->user() ? auth()->user()->name : 'Kullanıcı' }}</span>
                                <svg :class="{ 'rotate-180': open }"
                                    class="w-4 h-4 ml-1 transition-transform hidden md:block" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false" x-cloak
                                class="absolute right-0 mt-2 w-48 py-2 bg-white dark:bg-gray-800 rounded-lg shadow-lg z-50 border border-gray-200 dark:border-gray-700">
                                <a href="{{ url('/profile') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Profil</a>
                                <a href="{{ route('ayarlar.index') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Ayarlar</a>
                                <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}" class="w-full">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        Çıkış Yap
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-6">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- SortableJS for Drag & Drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <!-- Vite Derlenmiş Global JS -->
    @if ($globalJs = $viteAsset('resources/js/admin/global.js'))
        <script type="module" src="{{ $globalJs }}"></script>
    @endif

    <!-- Custom Admin Scripts -->
    <script>
        // CSRF Token setup for AJAX
        window.Laravel = {!! json_encode(['csrfToken' => csrf_token()]) !!};

        

        // Güvenli global değişkenler - hataları önlemek için
        window.featureList = window.featureList || function() {
            return {
                features: [],
                selected: [],
                init() {
                    console.log('Global featureList initialized');
                }
            };
        };

        window.init = window.init || function() {
            console.log('Global init function called');
        };

        window.selected = window.selected || function() {
            return {
                items: [],
                init() {
                    console.log('Global selected initialized');
                }
            };
        };

        window.isActive = window.isActive || function() {
            return {
                active: false,
                init() {
                    console.log('Global isActive initialized');
                }
            };
        };

        // Safe Alpine.js error handler
        window.addEventListener('error', function(e) {
            if (e.message && e.message.includes('is not defined') && e.filename && e.filename.includes(
                    'cdn.min.js')) {
                console.warn('⚠️ Alpine.js variable not found:', e.message);
                // Silently handle Alpine.js variable errors in non-form pages
                e.preventDefault();
                return false;
            }
        });

        // Submenu toggle function
        window.toggleSubmenu = function(submenuId) {
            const submenu = document.getElementById(submenuId);
            const arrow = document.getElementById('arrow-' + submenuId.replace('submenu-', ''));

            if (submenu && arrow) {
                if (submenu.classList.contains('hidden')) {
                    submenu.classList.remove('hidden');
                    arrow.style.transform = 'rotate(180deg)';
                } else {
                    submenu.classList.add('hidden');
                    arrow.style.transform = 'rotate(0deg)';
                }
            }
        };

        // API Token Setup - Auto-save için
        window.setupApiToken = function() {
            const apiToken = localStorage.getItem('admin_api_token');
            if (!apiToken) {
                console.log('🔑 API Token bulunamadı. CSRF token kullanılacak.');
                return false;
            }
            console.log('✅ API Token bulundu. Bearer authentication kullanılacak.');
            return true;
        };

        // Alpine.js hazır olduğunda çalışacak
        document.addEventListener('alpine:init', () => {
            console.log('✅ Alpine.js initialized successfully');

            // API Token kontrolü
            window.setupApiToken();

            // Alpine.js data tanımlamaları
            if (typeof Alpine !== 'undefined') {
                Alpine.data('featureList', () => ({
                    features: [],
                    selected: [],
                    init() {
                        console.log('Alpine featureList initialized');
                    }
                }));

                Alpine.data('selected', () => ({
                    items: [],
                    init() {
                        console.log('Alpine selected initialized');
                    }
                }));

                Alpine.data('isActive', () => ({
                    active: false,
                    init() {
                        console.log('Alpine isActive initialized');
                    }
                }));
            }
        });
    </script>

    <!-- TODO: İlan form sihirbazı modülerleştirilecek; geçici olarak kaldırıldı -->

    @stack('scripts')
    @yield('scripts')

    <script src="{{ asset('js/admin/csrf-handler.js') }}"></script>
    <!-- Quick Search JavaScript -->
    <script src="{{ asset('js/admin/quick-search.js') }}"></script>

    <!-- TKGM Integration JavaScript -->
    <script src="{{ asset('js/admin/tkgm-integration.js') }}"></script>

    <!-- Pusher.js for Real-time Notifications -->
    @php($pusherKey = config('broadcasting.connections.pusher.key'))
    @if (!empty($pusherKey))
        <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

        <!-- Real-time Notifications JavaScript -->
        <script src="{{ asset('js/admin/real-time-notifications.js') }}"></script>

        <!-- Pusher Configuration -->
        <script>
            window.PUSHER_APP_KEY = '{{ $pusherKey }}';
            window.PUSHER_APP_CLUSTER = '{{ config('broadcasting.connections.pusher.options.cluster') }}';
        </script>
    @endif

</body>

</html>
