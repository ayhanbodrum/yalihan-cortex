@props([
    'class' => '',
])

<nav
    class="yaliihan-nav bg-white shadow-lg fixed w-full z-50 backdrop-filter backdrop-blur-lg bg-opacity-95 {{ $class }}">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center h-20">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center group">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                        <span class="text-white font-bold text-xl">Y</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Yalıhan Emlak</h1>
                        <p class="text-sm text-gray-600">Bodrum'da Güvenilir Emlak</p>
                    </div>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden lg:flex items-center space-x-8">
                <a href="{{ route('home') }}"
                    class="nav-link text-gray-700 hover:text-orange-600 font-medium transition-colors">
                    Ana Sayfa
                </a>
                <div class="relative group">
                    <a href="{{ route('yalihan.properties') }}"
                        class="nav-link text-gray-700 hover:text-orange-600 font-medium transition-colors flex items-center">
                        İlanlar
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </a>
                    <!-- Dropdown Menu -->
                    <div
                        class="absolute top-full left-0 mt-2 w-48 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                        <a href="{{ route('yalihan.properties') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                            Tüm İlanlar
                        </a>
                        <a href="{{ route('yalihan.properties', ['ilan_turu' => 'satilik']) }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                            Satılık
                        </a>
                        <a href="{{ route('yalihan.properties', ['ilan_turu' => 'kiralik']) }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                            Kiralık
                        </a>
                    </div>
                </div>
                <a href="{{ route('yalihan.contact') }}"
                    class="nav-link text-gray-700 hover:text-orange-600 font-medium transition-colors">
                    Hakkımızda
                </a>
                <a href="{{ route('yalihan.contact') }}"
                    class="nav-link text-gray-700 hover:text-orange-600 font-medium transition-colors">
                    İletişim
                </a>
            </div>

            <!-- Right Side Actions -->
            <div class="flex items-center space-x-2">
                <!-- Language & Currency Selector -->
                <x-yaliihan.language-currency-selector :current-language="app()->getLocale()" :current-currency="'TRY'" :show-language="true"
                    :show-currency="true" class="hidden sm:flex" />

                <!-- Search Icon -->
                <button class="p-2 text-gray-600 hover:text-orange-600 transition-colors" title="Arama">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>

                <!-- Dark Mode Toggle -->
                <button class="p-2 text-gray-600 hover:text-orange-600 transition-colors" title="Dark Mode">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                        </path>
                    </svg>
                </button>

                <!-- CTA Button -->
                <a href="{{ route('yalihan.contact') }}"
                    class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-2 rounded-lg font-semibold hover:from-orange-600 hover:to-orange-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                    İlan Ver
                </a>

                <!-- Mobile Menu Button -->
                <button class="lg:hidden p-2 text-gray-600 hover:text-orange-600 transition-colors" x-data
                    @click="$dispatch('toggle-mobile-menu')">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="lg:hidden border-t border-gray-200 py-4" x-data="{ open: false }"
            @toggle-mobile-menu.window="open = !open" x-show="open" x-transition>
            <div class="space-y-2">
                <a href="{{ route('home') }}"
                    class="block px-4 py-2 text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                    Ana Sayfa
                </a>
                <a href="{{ route('yalihan.properties') }}"
                    class="block px-4 py-2 text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                    İlanlar
                </a>
                <a href="{{ route('yalihan.contact') }}"
                    class="block px-4 py-2 text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                    Hakkımızda
                </a>
                <a href="{{ route('yalihan.contact') }}"
                    class="block px-4 py-2 text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                    İletişim
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Spacer for fixed navigation -->
<div class="h-20"></div>

<style>
    .yaliihan-nav {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.9) 100%);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(255, 165, 0, 0.1);
    }

    .nav-link {
        position: relative;
        padding: 0.5rem 0;
    }

    .nav-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: linear-gradient(90deg, #f97316, #ea580c);
        transition: width 0.3s ease;
    }

    .nav-link:hover::after {
        width: 100%;
    }

    /* Mobile menu animation */
    [x-cloak] {
        display: none !important;
    }
</style>
