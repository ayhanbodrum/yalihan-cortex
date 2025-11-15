<!DOCTYPE html>
<html lang="tr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Context7 Location Defaults -->
    <meta name="location-default-latitude" content="{{ config('location.map.default_latitude') }}">
    <meta name="location-default-longitude" content="{{ config('location.map.default_longitude') }}">
    <meta name="location-default-zoom" content="{{ config('location.map.default_zoom') }}">
    <title>@yield('title', 'Yalıhan Emlak - Gayrimenkul')</title>

    <!-- Vite CSS (Development & Production) -->
    @vite(['resources/css/app.css'])

    <!-- Tailwind CDN Fallback (if Vite fails in development) -->
    @env('local')
        <script>
            // Check if Vite dev server is running, if not use CDN
            window.addEventListener('load', function() {
                setTimeout(function() {
                    const testEl = document.createElement('div');
                    testEl.className = 'bg-blue-500';
                    testEl.style.position = 'absolute';
                    testEl.style.left = '-9999px';
                    document.body.appendChild(testEl);
                    const bgColor = window.getComputedStyle(testEl).backgroundColor;
                    document.body.removeChild(testEl);

                    // If Tailwind is not working (bg-blue-500 doesn't apply), load CDN
                    if (bgColor === 'rgba(0, 0, 0, 0)' || bgColor === 'transparent' || !bgColor.includes('rgb')) {
                        console.warn('Tailwind not detected, loading CDN fallback...');
                        if (!window.tailwind) {
                            const script = document.createElement('script');
                            script.src = 'https://cdn.tailwindcss.com';
                            script.onload = function() {
                                if (window.tailwind) {
                                    tailwind.config = {
                                        darkMode: 'class',
                                        theme: {
                                            extend: {
                                                colors: {
                                                    primary: '#3b82f6',
                                                    secondary: '#1e40af',
                                                    accent: '#f59e0b',
                                                }
                                            }
                                        }
                                    };
                                    location.reload();
                                }
                            };
                            document.head.appendChild(script);
                        }
                    }
                }, 500);
            });
        </script>
    @endenv

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="{{ asset('css/advanced-leaflet.css') }}">
    @stack('styles')

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
        }

        /* Smooth transitions for dark mode */
        * {
            transition-property: background-color, border-color, color, fill, stroke;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 200ms;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-gradient-to-b from-gray-50 via-white to-gray-100 dark:from-gray-900 dark:via-gray-950 dark:to-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300" data-locale-endpoint="{{ route('preferences.locale') }}" data-currency-endpoint="{{ route('preferences.currency') }}">
    <!-- Global Topbar -->
    <x-frontend.global.topbar />

    <!-- Navigation -->
    <nav class="bg-white/95 dark:bg-gray-900/95 backdrop-blur-lg shadow-md sticky top-0 z-50 border-b border-gray-200/70 dark:border-gray-800/70 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center group">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 via-indigo-500 to-purple-600 dark:from-blue-600 dark:via-indigo-600 dark:to-purple-700 rounded-xl flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300 shadow-lg hover:shadow-xl">
                        <i class="fas fa-building text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white transition-colors duration-300">Yalıhan Emlak</h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400 transition-colors duration-300">Bodrum'da Güvenilir Emlak</p>
                    </div>
                </a>

                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="text-gray-700 dark:text-purple-300 hover:text-blue-600 dark:hover:text-purple-200 font-medium transition-colors duration-200 relative group">
                        Ana Sayfa
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-600 dark:bg-purple-300 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="{{ route('frontend.portfolio.index') }}" class="text-gray-700 dark:text-purple-300 hover:text-blue-600 dark:hover:text-purple-200 font-medium transition-colors duration-200 relative group">
                        Portföy
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-600 dark:bg-purple-300 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="{{ route('ilanlar.index') }}" class="text-gray-700 dark:text-purple-300 hover:text-blue-600 dark:hover:text-purple-200 font-medium transition-colors duration-200 relative group">
                        İlanlar
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-600 dark:bg-purple-300 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="{{ route('ilanlar.international') }}" class="text-gray-700 dark:text-purple-300 hover:text-blue-600 dark:hover:text-purple-200 font-medium transition-colors duration-200 relative group">
                        Uluslararası
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-600 dark:bg-purple-300 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="{{ route('frontend.danismanlar.index') }}" class="text-gray-700 dark:text-purple-300 hover:text-blue-600 dark:hover:text-purple-200 font-medium transition-colors duration-200 relative group">
                        Danışmanlar
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-600 dark:bg-purple-300 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="{{ route('contact') }}" class="text-gray-700 dark:text-purple-300 hover:text-blue-600 dark:hover:text-purple-200 font-medium transition-colors duration-200 relative group">
                        İletişim
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-600 dark:bg-purple-300 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                </div>

                <!-- Right Side Actions -->
                <div class="flex items-center space-x-4">
                    <!-- Dark Mode Toggle -->
                    <button onclick="toggleDarkMode()" class="p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800" aria-label="Dark mode toggle">
                        <i class="fas fa-moon dark:hidden"></i>
                        <i class="fas fa-sun hidden dark:inline"></i>
                    </button>

                    <!-- Auth Buttons -->
                    <div class="flex gap-2">
                        @auth
                            <a href="{{ route('admin.dashboard.index') }}" class="inline-flex items-center px-4 py-2 border-2 border-blue-600 dark:border-blue-500 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/30 active:scale-95 transition-all duration-200 font-medium shadow-md hover:shadow-lg focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                                <i class="fas fa-user mr-2"></i>Panel
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 border-2 border-blue-600 dark:border-blue-500 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/30 active:scale-95 transition-all duration-200 font-medium shadow-md hover:shadow-lg focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                                <i class="fas fa-sign-in-alt mr-2"></i>Giriş
                            </a>
                        @endauth
                    </div>

                    <!-- Mobile Menu Button -->
                    <button onclick="toggleMobileMenu()" class="lg:hidden p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800" aria-label="Menü">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobileMenu" class="hidden lg:hidden border-t border-gray-200/70 dark:border-gray-800/70 py-4">
                <div class="flex flex-col space-y-2">
                    <a href="{{ route('home') }}" class="px-4 py-2 text-gray-700 dark:text-purple-300 hover:bg-blue-50 dark:hover:bg-gray-800 hover:text-blue-600 dark:hover:text-purple-200 transition-colors duration-200 rounded-lg">
                        Ana Sayfa
                    </a>
                    <a href="{{ route('frontend.portfolio.index') }}" class="px-4 py-2 text-gray-700 dark:text-purple-300 hover:bg-blue-50 dark:hover:bg-gray-800 hover:text-blue-600 dark:hover:text-purple-200 transition-colors duration-200 rounded-lg">
                        Portföy
                    </a>
                    <a href="{{ route('ilanlar.index') }}" class="px-4 py-2 text-gray-700 dark:text-purple-300 hover:bg-blue-50 dark:hover:bg-gray-800 hover:text-blue-600 dark:hover:text-purple-200 transition-colors duration-200 rounded-lg">
                        İlanlar
                    </a>
                    <a href="{{ route('ilanlar.international') }}" class="px-4 py-2 text-gray-700 dark:text-purple-300 hover:bg-blue-50 dark:hover:bg-gray-800 hover:text-blue-600 dark:hover:text-purple-200 transition-colors duration-200 rounded-lg">
                        Uluslararası
                    </a>
                    <a href="{{ route('frontend.danismanlar.index') }}" class="px-4 py-2 text-gray-700 dark:text-purple-300 hover:bg-blue-50 dark:hover:bg-gray-800 hover:text-blue-600 dark:hover:text-purple-200 transition-colors duration-200 rounded-lg">
                        Danışmanlar
                    </a>
                    <a href="{{ route('contact') }}" class="px-4 py-2 text-gray-700 dark:text-purple-300 hover:bg-blue-50 dark:hover:bg-gray-800 hover:text-blue-600 dark:hover:text-purple-200 transition-colors duration-200 rounded-lg">
                        İletişim
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 dark:bg-gray-950 text-white transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
                <!-- Company Info -->
                <div>
                    <h5 class="text-xl font-bold mb-4 text-white">Yalıhan Emlak</h5>
                    <p class="text-gray-400 mb-4 leading-relaxed">
                        Türkiye'nin önde gelen gayrimenkul danışmanlık firması.
                        Güvenilir, profesyonel ve müşteri odaklı hizmet anlayışımızla yanınızdayız.
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200 transform hover:scale-110" aria-label="Facebook">
                            <i class="fab fa-facebook-f text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200 transform hover:scale-110" aria-label="Twitter">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200 transform hover:scale-110" aria-label="Instagram">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200 transform hover:scale-110" aria-label="LinkedIn">
                            <i class="fab fa-linkedin-in text-xl"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h6 class="text-lg font-bold mb-4 text-white">Hızlı Linkler</h6>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition-colors duration-200">Ana Sayfa</a></li>
                        <li><a href="{{ route('frontend.portfolio.index') }}" class="text-gray-400 hover:text-white transition-colors duration-200">Portföy</a></li>
                        <li><a href="{{ route('ilanlar.index') }}" class="text-gray-400 hover:text-white transition-colors duration-200">İlanlar</a></li>
                        <li><a href="{{ route('frontend.danismanlar.index') }}" class="text-gray-400 hover:text-white transition-colors duration-200">Danışmanlar</a></li>
                        <li><a href="{{ route('contact') }}" class="text-gray-400 hover:text-white transition-colors duration-200">İletişim</a></li>
                    </ul>
                </div>

                <!-- Services -->
                <div>
                    <h6 class="text-lg font-bold mb-4 text-white">Hizmetler</h6>
                    <ul class="space-y-2">
                        <li><a href="{{ route('ilanlar.index', ['islem_tipi' => 'satis', 'kategori_slug' => 'konut']) }}" class="text-gray-400 hover:text-white transition-colors duration-200">Satılık Evler</a></li>
                        <li><a href="{{ route('ilanlar.index', ['islem_tipi' => 'kiralama', 'kategori_slug' => 'konut']) }}" class="text-gray-400 hover:text-white transition-colors duration-200">Kiralık Evler</a></li>
                        <li><a href="{{ route('ilanlar.index', ['kategori_slug' => 'arsa']) }}" class="text-gray-400 hover:text-white transition-colors duration-200">Arsa</a></li>
                        <li><a href="{{ route('ilanlar.index', ['kategori_slug' => 'isyeri']) }}" class="text-gray-400 hover:text-white transition-colors duration-200">İşyeri</a></li>
                        <li><a href="{{ route('contact') }}" class="text-gray-400 hover:text-white transition-colors duration-200">Yatırım Danışmanlığı</a></li>
                        <li><a href="{{ route('frontend.danismanlar.index') }}" class="text-gray-400 hover:text-white transition-colors duration-200">Danışmanlarımız</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h6 class="text-lg font-bold mb-4 text-white">İletişim</h6>
                    <div class="space-y-3">
                        <div class="flex items-center text-gray-400">
                            <i class="fas fa-map-marker-alt mr-3 text-blue-400"></i>
                            <span>Yalıkavak, Bodrum, Muğla</span>
                        </div>
                        <div class="flex items-center text-gray-400">
                            <i class="fas fa-phone mr-3 text-blue-400"></i>
                            <span>+90 252 123 45 67</span>
                        </div>
                        <div class="flex items-center text-gray-400">
                            <i class="fas fa-envelope mr-3 text-blue-400"></i>
                            <span>info@yalihanemlak.com</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <hr class="border-gray-800 dark:border-gray-700 my-8">

            <!-- Copyright -->
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 mb-4 md:mb-0">
                    &copy; {{ date('Y') }} Yalıhan Emlak. Tüm hakları saklıdır.
                </p>
                <div class="flex gap-4">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Gizlilik Politikası</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Kullanım Şartları</a>
                </div>
            </div>
        </div>
    </footer>

    @if (config('location.google_maps.enabled') && config('location.google_maps.api_key'))
        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('location.google_maps.api_key') }}&libraries={{ config('location.google_maps.libraries', 'places') }}" async defer></script>
    @endif
    <script src="{{ asset('js/context7-location-adapter.js') }}" defer></script>

    <!-- Vanilla JS for Mobile Menu & Dark Mode -->
    <script>
        // ✅ CONTEXT7: Vanilla JS - Error handling eklendi
        // Mobile Menu Toggle
        function toggleMobileMenu() {
            try {
                const menu = document.getElementById('mobileMenu');
                if (menu) {
                    menu.classList.toggle('hidden');
                }
            } catch (error) {
                console.error('Context7: Mobile menu toggle error', error);
            }
        }

        // Dark Mode Toggle - FIX: localStorage boolean sorunu düzeltildi
        function toggleDarkMode() {
            try {
                const html = document.documentElement;
                const isDark = html.classList.toggle('dark');
                // ✅ FIX: Boolean değer doğrudan kaydediliyor
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                console.log('Context7: Dark mode toggled', isDark ? 'dark' : 'light');
            } catch (error) {
                console.error('Context7: Dark mode toggle error', error);
            }
        }

        // Initialize dark mode from localStorage - FIX: Tema yüklemesi düzeltildi
        (function() {
            try {
                // ✅ FIX: Önce localStorage'dan tema kontrol et
                const savedTheme = localStorage.getItem('theme');
                // ✅ FIX: Sistem tercihini de kontrol et
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                // ✅ FIX: Kaydedilmiş tema yoksa, sistem tercihini kullan
                const isDark = savedTheme === 'dark' || (!savedTheme && prefersDark);

                if (isDark) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }

                console.log('Context7: Dark mode initialized', isDark ? 'dark' : 'light');
            } catch (error) {
                console.error('Context7: Dark mode initialization error', error);
            }
        })();

        // Listen for system theme changes
        try {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                if (!localStorage.getItem('theme')) {
                    if (e.matches) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                }
            });
        } catch (error) {
            console.error('Context7: System theme listener error', error);
        }

        document.addEventListener('DOMContentLoaded', () => {
            try {
                const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = tokenMeta ? tokenMeta.getAttribute('content') : null;
                const localeEndpoint = document.body.dataset.localeEndpoint || null;
                const currencyEndpoint = document.body.dataset.currencyEndpoint || null;

                const postPreference = (endpoint, payload) => {
                    if (!endpoint || !csrfToken) {
                        console.warn('Context7: Preference endpoint or CSRF token missing');
                        return;
                    }

                    fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify(payload),
                    })
                        .then((response) => {
                            if (!response.ok) {
                                return response.json().then((data) => Promise.reject(data));
                            }
                            return response.json().catch(() => ({}));
                        })
                        .then(() => {
                            window.location.reload();
                        })
                        .catch((error) => {
                            console.error('Context7: Preference update failed', error);
                        });
                };

                document.querySelectorAll('[data-preference-locale]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const locale = button.getAttribute('data-preference-locale');
                        postPreference(localeEndpoint, { locale });
                    });
                });

                document.querySelectorAll('[data-preference-currency]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const currency = button.getAttribute('data-preference-currency');
                        postPreference(currencyEndpoint, { currency });
                    });
                });

                const mobileCurrencySelect = document.querySelector('[data-preference-currency-select]');
                if (mobileCurrencySelect) {
                    mobileCurrencySelect.addEventListener('change', (event) => {
                        postPreference(currencyEndpoint, { currency: event.target.value });
                    });
                }

                // AI Guide Button Handler
                document.querySelectorAll('[data-ai-guide-endpoint]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const endpoint = button.getAttribute('data-ai-guide-endpoint');
                        if (endpoint) {
                            window.location.href = endpoint;
                        }
                    });
                });

                // Property AI Analyze Button Handler
                document.querySelectorAll('[data-ai-analyze]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const propertyId = button.getAttribute('data-ai-analyze');
                        if (propertyId) {
                            // Redirect to AI explore with property context
                            const aiEndpoint = '{{ route("ai.explore") }}';
                            window.location.href = `${aiEndpoint}?property_id=${propertyId}`;
                        }
                    });
                });
            } catch (error) {
                console.error('Context7: Preference initialization error', error);
            }
        });
    </script>

    <!-- Custom Scripts -->
    @stack('scripts')
</body>
</html>
