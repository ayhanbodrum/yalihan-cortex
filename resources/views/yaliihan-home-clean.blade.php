@extends('layouts.frontend')

@section('title', 'Ana Sayfa - Yalıhan Emlak')

@section('content')
    <!-- Hero Section -->
    <x-yaliihan.hero-section title="🏠 Yalıhan Emlak" subtitle="Bodrum'un en güzel emlakları burada!" :show-search="true" />

    <!-- AI Assistant CTA -->
    <section class="-mt-8 sm:-mt-12 relative z-10">
        <div class="container mx-auto px-4">
            <div class="bg-white dark:bg-gray-900 border border-blue-100 dark:border-blue-800/40 shadow-xl rounded-3xl p-6 sm:p-10 flex flex-col lg:flex-row items-center gap-8 overflow-hidden transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-transparent to-purple-50 dark:from-blue-900/40 dark:via-transparent dark:to-purple-900/30 pointer-events-none"></div>
                <div class="relative flex flex-col sm:flex-row items-center gap-6 w-full">
                    <div class="flex-shrink-0 w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-gradient-to-br from-blue-600 to-purple-600 text-white flex items-center justify-center shadow-lg">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 11c1.657 0 3-1.567 3-3.5S13.657 4 12 4 9 5.567 9 7.5 10.343 11 12 11zM9.5 13a4.5 4.5 0 00-4.5 4.5V19a1 1 0 001 1h10a1 1 0 001-1v-1.5A4.5 4.5 0 0012.5 13h-3z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7h3m-1.5-1.5V9" />
                        </svg>
                    </div>
                    <div class="flex-1 text-center lg:text-left">
                        <p class="inline-flex items-center gap-2 text-xs font-semibold tracking-widest uppercase text-blue-600 dark:text-blue-300 mb-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse"></span>
                            Yapay Zeka Destekli Danışman
                        </p>
                        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-3">Sanal Danışmanımızla 7/24 Sohbet Edin</h2>
                        <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300 max-w-2xl">Portföy önerileri alın, yatırım planınızı analiz ettirin ve sorularınıza saniyeler içinde yanıt alın. AI destekli danışmanımız Türkçe ve İngilizce olarak hizmet verir.</p>
                    </div>
                </div>
                <div class="relative flex flex-col sm:flex-row items-center gap-3">
                    <a href="{{ url('/ai/explore') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all duration-200 hover:shadow-lg active:scale-95 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                        Sohbete Başla
                        <i class="fas fa-arrow-right text-sm"></i>
                    </a>
                    <a href="{{ route('frontend.danismanlar.index') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 border border-blue-200 dark:border-blue-700 text-blue-600 dark:text-blue-300 font-semibold rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                        Danışmanları Gör
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Properties Grid -->
    <section class="py-20 bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 transition-colors duration-300">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-5xl font-bold text-gray-900 dark:text-white mb-6 transition-colors duration-300">Öne Çıkan İlanlar</h2>
                <p class="text-2xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto mb-8 transition-colors duration-300">En güzel emlak seçenekleri ile hayalinizdeki evi
                    bulun</p>
                <a href="{{ route('frontend.portfolio.index') }}"
                    class="inline-flex items-center px-8 py-4 bg-blue-600 dark:bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 active:scale-95 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                    <i class="fas fa-building mr-3"></i>
                    Tüm Portföyü Görüntüle
                    <i class="fas fa-arrow-right ml-3"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                <!-- Property 1 -->
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800 group">
                    <!-- Property Image -->
                    <div class="relative h-64 overflow-hidden rounded-t-xl">
                        <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=400&h=300&fit=crop"
                            alt="Modern Villa - Yalıkavak"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <!-- Gradient Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <!-- Badge -->
                        <div class="absolute top-4 left-4 bg-green-500 text-white px-3 py-1.5 rounded-full text-sm font-semibold shadow-lg">Satılık</div>
                        <!-- Favorite Button -->
                        <div class="absolute top-4 right-4 w-10 h-10 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm rounded-full flex items-center justify-center cursor-pointer hover:bg-red-500 hover:text-white transition-all duration-300 shadow-lg dark:text-gray-300"
                            onclick="toggleFavorite(this)">
                            <span class="text-gray-600 dark:text-gray-300 text-xl">🤍</span>
                        </div>
                        <!-- Action Overlay -->
                        <div class="absolute bottom-4 left-4 right-4 opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-500">
                            <div class="flex gap-2">
                                <button class="flex-1 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md p-2.5 rounded-xl hover:bg-blue-500 hover:text-white dark:hover:bg-blue-600 transition-all duration-300 text-center shadow-lg font-medium text-xs"
                                    onclick="openModal('virtualTour')">
                                    <div class="text-base mb-0.5">🔄</div>
                                    <div>Sanal Tur</div>
                                </button>
                                <button class="flex-1 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md p-2.5 rounded-xl hover:bg-blue-500 hover:text-white dark:hover:bg-blue-600 transition-all duration-300 text-center shadow-lg font-medium text-xs"
                                    onclick="openModal('gallery')">
                                    <div class="text-base mb-0.5">📸</div>
                                    <div>Galeri</div>
                                </button>
                                <button class="flex-1 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md p-2.5 rounded-xl hover:bg-blue-500 hover:text-white dark:hover:bg-blue-600 transition-all duration-300 text-center shadow-lg font-medium text-xs"
                                    onclick="openModal('map')">
                                    <div class="text-base mb-0.5">🗺️</div>
                                    <div>Harita</div>
                                </button>
                                <button class="flex-1 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md p-2.5 rounded-xl hover:bg-blue-500 hover:text-white dark:hover:bg-blue-600 transition-all duration-300 text-center shadow-lg font-medium text-xs"
                                    onclick="shareProperty()">
                                    <div class="text-base mb-0.5">📤</div>
                                    <div>Paylaş</div>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Property Content -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 line-clamp-2">Modern Villa - Yalıkavak</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-4 flex items-center text-sm">
                            <span class="text-blue-500 dark:text-blue-400 mr-2">📍</span> Yalıkavak, Bodrum
                        </p>
                        <!-- Property Details -->
                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                <div class="text-2xl mb-1">🛏️</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium">Yatak</div>
                                <div class="font-bold text-gray-900 dark:text-white text-lg">4</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                <div class="text-2xl mb-1">🚿</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium">Banyo</div>
                                <div class="font-bold text-gray-900 dark:text-white text-lg">3</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                <div class="text-2xl mb-1">📐</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium">m²</div>
                                <div class="font-bold text-gray-900 dark:text-white text-lg">250</div>
                            </div>
                        </div>
                        <!-- Price -->
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mb-4">₺8,500,000</div>
                        <!-- Action Buttons -->
                        <div class="flex gap-3">
                            <button class="flex-1 border-2 border-blue-500 dark:border-blue-400 text-blue-500 dark:text-blue-400 py-2.5 px-4 rounded-lg hover:bg-blue-500 hover:text-white dark:hover:bg-blue-600 dark:hover:text-white transition-all duration-300 font-semibold text-sm"
                                onclick="openModal('propertyDetail')">Detayları Gör</button>
                            <button class="flex-1 bg-blue-600 dark:bg-blue-500 text-white py-2.5 px-4 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg font-semibold text-sm"
                                onclick="contactProperty()">İletişime Geç</button>
                        </div>
                    </div>
                </div>

                <!-- Property 2 -->
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800 group">
                    <!-- Property Image -->
                    <div class="relative h-64 overflow-hidden rounded-t-xl">
                        <img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=400&h=300&fit=crop"
                            alt="Lüks Daire - Gümbet"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <!-- Gradient Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <!-- Badge -->
                        <div class="absolute top-4 left-4 bg-blue-500 text-white px-3 py-1.5 rounded-full text-sm font-semibold shadow-lg">Kiralık</div>
                        <!-- Favorite Button -->
                        <div class="absolute top-4 right-4 w-10 h-10 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm rounded-full flex items-center justify-center cursor-pointer hover:bg-red-500 hover:text-white transition-all duration-300 shadow-lg dark:text-gray-300"
                            onclick="toggleFavorite(this)">
                            <span class="text-gray-600 dark:text-gray-300 text-xl">🤍</span>
                        </div>
                        <!-- Action Overlay -->
                        <div class="absolute bottom-4 left-4 right-4 opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-500">
                            <div class="flex gap-2">
                                <button class="flex-1 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md p-2.5 rounded-xl hover:bg-blue-500 hover:text-white dark:hover:bg-blue-600 transition-all duration-300 text-center shadow-lg font-medium text-xs"
                                    onclick="openModal('virtualTour')">
                                    <div class="text-base mb-0.5">🔄</div>
                                    <div>Sanal Tur</div>
                                </button>
                                <button class="flex-1 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md p-2.5 rounded-xl hover:bg-blue-500 hover:text-white dark:hover:bg-blue-600 transition-all duration-300 text-center shadow-lg font-medium text-xs"
                                    onclick="openModal('gallery')">
                                    <div class="text-base mb-0.5">📸</div>
                                    <div>Galeri</div>
                                </button>
                                <button class="flex-1 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md p-2.5 rounded-xl hover:bg-blue-500 hover:text-white dark:hover:bg-blue-600 transition-all duration-300 text-center shadow-lg font-medium text-xs"
                                    onclick="openModal('map')">
                                    <div class="text-base mb-0.5">🗺️</div>
                                    <div>Harita</div>
                                </button>
                                <button class="flex-1 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md p-2.5 rounded-xl hover:bg-blue-500 hover:text-white dark:hover:bg-blue-600 transition-all duration-300 text-center shadow-lg font-medium text-xs"
                                    onclick="shareProperty()">
                                    <div class="text-base mb-0.5">📤</div>
                                    <div>Paylaş</div>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Property Content -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 line-clamp-2">Lüks Daire - Gümbet</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-4 flex items-center text-sm">
                            <span class="text-blue-500 dark:text-blue-400 mr-2">📍</span> Gümbet, Bodrum
                        </p>
                        <!-- Property Details -->
                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                <div class="text-2xl mb-1">🛏️</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium">Yatak</div>
                                <div class="font-bold text-gray-900 dark:text-white text-lg">2</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                <div class="text-2xl mb-1">🚿</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium">Banyo</div>
                                <div class="font-bold text-gray-900 dark:text-white text-lg">2</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                <div class="text-2xl mb-1">📐</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium">m²</div>
                                <div class="font-bold text-gray-900 dark:text-white text-lg">120</div>
                            </div>
                        </div>
                        <!-- Price -->
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mb-4">₺15,000 <span class="text-lg text-gray-500 dark:text-gray-400 font-normal">/ay</span></div>
                        <!-- Action Buttons -->
                        <div class="flex gap-3">
                            <button class="flex-1 border-2 border-blue-500 dark:border-blue-400 text-blue-500 dark:text-blue-400 py-2.5 px-4 rounded-lg hover:bg-blue-500 hover:text-white dark:hover:bg-blue-600 dark:hover:text-white transition-all duration-300 font-semibold text-sm"
                                onclick="openModal('propertyDetail')">Detayları Gör</button>
                            <button class="flex-1 bg-blue-600 dark:bg-blue-500 text-white py-2.5 px-4 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg font-semibold text-sm"
                                onclick="contactProperty()">İletişime Geç</button>
                        </div>
                    </div>
                </div>

                <!-- Property 3 -->
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800 group">
                    <!-- Property Image -->
                    <div class="relative h-64 overflow-hidden rounded-t-xl">
                        <img src="https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?w=400&h=300&fit=crop"
                            alt="Deniz Manzaralı Villa - Bitez"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <!-- Gradient Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <!-- Badge -->
                        <div class="absolute top-4 left-4 bg-purple-500 text-white px-3 py-1.5 rounded-full text-sm font-semibold shadow-lg">Öne Çıkan</div>
                        <!-- Favorite Button -->
                        <div class="absolute top-4 right-4 w-10 h-10 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm rounded-full flex items-center justify-center cursor-pointer hover:bg-red-500 hover:text-white transition-all duration-300 shadow-lg dark:text-gray-300"
                            onclick="toggleFavorite(this)">
                            <span class="text-gray-600 dark:text-gray-300 text-xl">🤍</span>
                        </div>
                        <!-- Action Overlay -->
                        <div class="absolute bottom-4 left-4 right-4 opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-500">
                            <div class="flex gap-2">
                                <button class="flex-1 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md p-2.5 rounded-xl hover:bg-blue-500 hover:text-white dark:hover:bg-blue-600 transition-all duration-300 text-center shadow-lg font-medium text-xs"
                                    onclick="openModal('virtualTour')">
                                    <div class="text-base mb-0.5">🔄</div>
                                    <div>Sanal Tur</div>
                                </button>
                                <button class="flex-1 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md p-2.5 rounded-xl hover:bg-blue-500 hover:text-white dark:hover:bg-blue-600 transition-all duration-300 text-center shadow-lg font-medium text-xs"
                                    onclick="openModal('gallery')">
                                    <div class="text-base mb-0.5">📸</div>
                                    <div>Galeri</div>
                                </button>
                                <button class="flex-1 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md p-2.5 rounded-xl hover:bg-blue-500 hover:text-white dark:hover:bg-blue-600 transition-all duration-300 text-center shadow-lg font-medium text-xs"
                                    onclick="openModal('map')">
                                    <div class="text-base mb-0.5">🗺️</div>
                                    <div>Harita</div>
                                </button>
                                <button class="flex-1 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md p-2.5 rounded-xl hover:bg-blue-500 hover:text-white dark:hover:bg-blue-600 transition-all duration-300 text-center shadow-lg font-medium text-xs"
                                    onclick="shareProperty()">
                                    <div class="text-base mb-0.5">📤</div>
                                    <div>Paylaş</div>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Property Content -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 line-clamp-2">Deniz Manzaralı Villa - Bitez</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-4 flex items-center text-sm">
                            <span class="text-blue-500 dark:text-blue-400 mr-2">📍</span> Bitez, Bodrum
                        </p>
                        <!-- Property Details -->
                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                <div class="text-2xl mb-1">🛏️</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium">Yatak</div>
                                <div class="font-bold text-gray-900 dark:text-white text-lg">5</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                <div class="text-2xl mb-1">🚿</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium">Banyo</div>
                                <div class="font-bold text-gray-900 dark:text-white text-lg">4</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                <div class="text-2xl mb-1">📐</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium">m²</div>
                                <div class="font-bold text-gray-900 dark:text-white text-lg">350</div>
                            </div>
                        </div>
                        <!-- Price -->
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mb-4">₺12,500,000</div>
                        <!-- Action Buttons -->
                        <div class="flex gap-3">
                            <button class="flex-1 border-2 border-blue-500 dark:border-blue-400 text-blue-500 dark:text-blue-400 py-2.5 px-4 rounded-lg hover:bg-blue-500 hover:text-white dark:hover:bg-blue-600 dark:hover:text-white transition-all duration-300 font-semibold text-sm"
                                onclick="openModal('propertyDetail')">Detayları Gör</button>
                            <button class="flex-1 bg-blue-600 dark:bg-blue-500 text-white py-2.5 px-4 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg font-semibold text-sm"
                                onclick="contactProperty()">İletişime Geç</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-white dark:bg-gray-800 transition-colors duration-300">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-5xl font-bold text-gray-900 dark:text-white mb-6 transition-colors duration-300">Neden Yalıhan Emlak?</h2>
                <p class="text-2xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto transition-colors duration-300">Profesyonel hizmet, güvenilir çözümler</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center transform hover:scale-105 transition-transform duration-300">
                    <div class="text-6xl mb-4">🏠</div>
                    <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4 transition-colors duration-300">Geniş Portföy</h3>
                    <p class="text-gray-600 dark:text-gray-300 transition-colors duration-300">Bodrum'un her bölgesinde binlerce emlak seçeneği</p>
                </div>
                <div class="text-center transform hover:scale-105 transition-transform duration-300">
                    <div class="text-6xl mb-4">🤝</div>
                    <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4 transition-colors duration-300">Güvenilir Hizmet</h3>
                    <p class="text-gray-600 dark:text-gray-300 transition-colors duration-300">20+ yıllık deneyim ve müşteri memnuniyeti</p>
                </div>
                <div class="text-center transform hover:scale-105 transition-transform duration-300">
                    <div class="text-6xl mb-4">📱</div>
                    <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4 transition-colors duration-300">Modern Teknoloji</h3>
                    <p class="text-gray-600 dark:text-gray-300 transition-colors duration-300">360° sanal tur, harita entegrasyonu ve daha fazlası</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-blue-600 dark:bg-blue-700 text-white transition-colors duration-300">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold mb-4">Hayalinizdeki Evi Bulun!</h2>
            <p class="text-xl mb-8 opacity-95">Uzman ekibimiz size yardımcı olmaya hazır</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button
                    class="bg-white text-blue-600 dark:bg-gray-800 dark:text-blue-400 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 dark:hover:bg-gray-700 active:scale-95 transition-all duration-200 shadow-lg hover:shadow-xl focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-600">
                    📞 Hemen Ara: 0533 209 03 02
                </button>
                <button
                    class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 dark:hover:bg-gray-800 dark:hover:text-blue-400 active:scale-95 transition-all duration-200 focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-600">
                    📧 İletişim Formu
                </button>
            </div>
        </div>
    </section>

    <!-- Modal Containers -->
    <div id="virtualTour" class="fixed inset-0 hidden items-center justify-center z-50">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('virtualTour')"></div>
        <div class="relative bg-white dark:bg-gray-900 rounded-3xl shadow-2xl max-w-3xl w-full mx-4 p-6 sm:p-8 overflow-y-auto max-h-[90vh]">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">360° Sanal Tur</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Portföyün sanal turuna göz atın.</p>
                </div>
                <button type="button" class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition-colors" onclick="closeModal('virtualTour')" aria-label="Kapat">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="aspect-video rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Sanal tur içeriği yakında eklenecek.</span>
            </div>
        </div>
    </div>

    <div id="gallery" class="fixed inset-0 hidden items-center justify-center z-50">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('gallery')"></div>
        <div class="relative bg-white dark:bg-gray-900 rounded-3xl shadow-2xl max-w-4xl w-full mx-4 p-6 sm:p-8 overflow-y-auto max-h-[90vh]">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Galeri</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Seçili portföye ait görseller.</p>
                </div>
                <button type="button" class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition-colors" onclick="closeModal('gallery')" aria-label="Kapat">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="aspect-video rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800"></div>
                <div class="aspect-video rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800"></div>
                <div class="aspect-video rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800"></div>
                <div class="aspect-video rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800"></div>
            </div>
        </div>
    </div>

    <div id="map" class="fixed inset-0 hidden items-center justify-center z-50">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('map')"></div>
        <div class="relative bg-white dark:bg-gray-900 rounded-3xl shadow-2xl max-w-3xl w-full mx-4 p-6 sm:p-8 overflow-hidden">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Lokasyon Haritası</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Portföyün bulunduğu konumu inceleyin.</p>
                </div>
                <button type="button" class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition-colors" onclick="closeModal('map')" aria-label="Kapat">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="rounded-2xl overflow-hidden h-72 bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Harita bileşeni yakında eklenecek.</span>
            </div>
        </div>
    </div>

    <div id="propertyDetail" class="fixed inset-0 hidden items-center justify-center z-50">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('propertyDetail')"></div>
        <div class="relative bg-white dark:bg-gray-900 rounded-3xl shadow-2xl max-w-4xl w-full mx-4 p-6 sm:p-8 overflow-y-auto max-h-[90vh]">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">İlan Detayları</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Ayrıntılı bilgi için portföy sayfasına gidin.</p>
                </div>
                <button type="button" class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition-colors" onclick="closeModal('propertyDetail')" aria-label="Kapat">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="space-y-4 text-gray-700 dark:text-gray-300 text-sm leading-relaxed">
                <p>Bu bölüm gerçek ilan içeriğiyle değiştirilecek. Şimdilik demo verileri görüntülenmektedir.</p>
                <ul class="list-disc list-inside space-y-2">
                    <li>Geniş salon ve panoramik manzara.</li>
                    <li>Özel havuz ve peyzajlı bahçe.</li>
                    <li>Güvenlik, otopark ve akıllı ev sistemi.</li>
                </ul>
                <a href="{{ route('frontend.ilanlar.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all duration-200">
                    Tüm İlanları Gör
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- JavaScript Functions -->
    <script>
        // ✅ CONTEXT7: Vanilla JS com error handling completo
        // Favorite Toggle Function - FIX: Null checks adicionados
        function toggleFavorite(element) {
            try {
                if (!element) {
                    console.error('Context7: toggleFavorite - element is null');
                    return;
                }

                const span = element.querySelector('span');
                if (!span) {
                    console.error('Context7: toggleFavorite - span not found');
                    return;
                }

                const isFavorited = span.textContent === '❤️';
                span.textContent = isFavorited ? '🤍' : '❤️';
                span.className = isFavorited ? 'text-gray-600 dark:text-gray-300 text-xl' : 'text-red-500 text-xl';

                // Toast notification
                if (typeof showToast === 'function') {
                    showToast(isFavorited ? 'Favorilerden çıkarıldı' : 'Favorilere eklendi', 'success');
                }

                console.log('Context7: Favorite toggled', isFavorited ? 'removed' : 'added');
            } catch (error) {
                console.error('Context7: toggleFavorite error', error);
            }
        }

        // Modal Functions - FIX: Null checks adicionados
        function openModal(modalId) {
            try {
                if (!modalId) {
                    console.error('Context7: openModal - modalId is null');
                    return;
                }

                const modal = document.getElementById(modalId);
                if (modal && modal.classList) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    modal.setAttribute('aria-hidden', 'false');
                    document.body.classList.add('overflow-hidden');
                    console.log('Context7: Modal opened', modalId);
                } else {
                    if (typeof showToast === 'function') {
                        showToast('İçerik hazırlanıyor', 'info');
                    }
                    console.warn('Context7: Modal not found', modalId);
                }
            } catch (error) {
                console.error('Context7: openModal error', error);
                if (typeof showToast === 'function') {
                    showToast('Modal açılırken hata oluştu', 'error');
                }
            }
        }

        function closeModal(modalId) {
            try {
                if (!modalId) {
                    console.error('Context7: closeModal - modalId is null');
                    return;
                }

                const modal = document.getElementById(modalId);
                if (modal && modal.classList) {
                    modal.classList.remove('flex');
                    modal.classList.add('hidden');
                    modal.setAttribute('aria-hidden', 'true');

                    const openModals = document.querySelectorAll('.fixed.inset-0.flex');
                    if (!openModals || openModals.length === 0) {
                        document.body.classList.remove('overflow-hidden');
                    }
                    console.log('Context7: Modal closed', modalId);
                }
            } catch (error) {
                console.error('Context7: closeModal error', error);
            }
        }

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                const activeModal = document.querySelector('.fixed.inset-0.flex');
                if (activeModal && activeModal.id) {
                    closeModal(activeModal.id);
                }
            }
        });

        // Share Property Function - FIX: API checks adicionados
        function shareProperty() {
            try {
                if (navigator.share) {
                    navigator.share({
                        title: 'Yalıhan Emlak - Modern Villa',
                        text: 'Bu harika emlakı inceleyin!',
                        url: window.location.href
                    }).then(() => {
                        console.log('Context7: Share successful');
                    }).catch(error => {
                        console.error('Context7: Share error', error);
                    });
                } else {
                    // Fallback for browsers that don't support Web Share API
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(window.location.href).then(() => {
                            if (typeof showToast === 'function') {
                                showToast('Paylaşım linki kopyalandı!', 'success');
                            }
                            console.log('Context7: Link copied to clipboard');
                        }).catch(error => {
                            console.error('Context7: Clipboard error', error);
                            if (typeof showToast === 'function') {
                                showToast('Link kopyalanamadı', 'error');
                            }
                        });
                    } else {
                        console.warn('Context7: Web Share API and Clipboard API not supported');
                        if (typeof showToast === 'function') {
                            showToast('Paylaşım desteklenmiyor', 'error');
                        }
                    }
                }
            } catch (error) {
                console.error('Context7: shareProperty error', error);
            }
        }

        // Contact Property Function - FIX: Safe route checking
        function contactProperty() {
            try {
                if (typeof showToast === 'function') {
                    showToast('İletişim formu açılıyor...', 'success');
                }

                // Redirect to contact page after toast
                setTimeout(() => {
                    try {
                        // ✅ FIX: Safe route checking without Blade error
                        @php
                            $contactRoute = null;
                            try {
                                $contactRoute = route('frontend.contact.index');
                            } catch (\Exception $e) {
                                // Route doesn't exist, use fallback
                            }
                        @endphp

                        const contactUrl = @json($contactRoute ?? '#contact');

                        if (contactUrl && contactUrl !== '#contact') {
                            window.location.href = contactUrl;
                            console.log('Context7: Redirecting to contact page');
                        } else {
                            console.log('Context7: Contact page not configured, scrolling to #contact');
                            const contactSection = document.getElementById('contact');
                            if (contactSection && contactSection.scrollIntoView) {
                                contactSection.scrollIntoView({ behavior: 'smooth' });
                            } else {
                                // Final fallback - just show message
                                if (typeof showToast === 'function') {
                                    showToast('İletişim sayfası yapım aşamasında', 'info');
                                }
                            }
                        }
                    } catch (error) {
                        console.error('Context7: Contact redirect error', error);
                    }
                }, 1000);

                console.log('Context7: Contact property initiated');
            } catch (error) {
                console.error('Context7: contactProperty error', error);
            }
        }

        // Toast Notification Function - FIX: Null checks e cleanup
        function showToast(message, type = 'success') {
            try {
                if (!message) {
                    console.warn('Context7: showToast - empty message');
                    return;
                }

                const toast = document.createElement('div');
                if (!toast) {
                    console.error('Context7: Failed to create toast element');
                    return;
                }

                const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
                const icon = type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️';

                toast.className =
                    `fixed top-4 right-4 ${bgColor} text-white rounded-2xl p-4 shadow-2xl z-50 transform translate-x-full transition-transform duration-300 max-w-sm`;
                toast.innerHTML = `
                    <div class="flex items-center">
                        <span class="text-2xl mr-3">${icon}</span>
                        <span class="font-medium">${message}</span>
                    </div>
                `;

                // Safe appendChild
                if (document.body) {
                    document.body.appendChild(toast);
                } else {
                    console.error('Context7: document.body not available');
                    return;
                }

                // Show toast
                setTimeout(() => {
                    if (toast && toast.classList) {
                        toast.classList.remove('translate-x-full');
                    }
                }, 100);

                // Hide toast after 3 seconds
                setTimeout(() => {
                    if (toast && toast.classList) {
                        toast.classList.add('translate-x-full');
                    }

                    setTimeout(() => {
                        if (document.body && document.body.contains(toast)) {
                            document.body.removeChild(toast);
                        }
                    }, 300);
                }, 3000);

                console.log('Context7: Toast shown', message, type);
            } catch (error) {
                console.error('Context7: showToast error', error);
            }
        }

        // Make showToast globally available
        window.showToast = showToast;

        // Smooth scroll for anchor links - FIX: Error handling eklendi
        document.addEventListener('DOMContentLoaded', function() {
            try {
                // Add smooth scrolling to all anchor links
                const anchorLinks = document.querySelectorAll('a[href^="#"]');
                if (!anchorLinks || anchorLinks.length === 0) {
                    console.log('Context7: No anchor links found');
                } else {
                    anchorLinks.forEach(link => {
                        if (!link) return;

                        link.addEventListener('click', function(e) {
                            try {
                                e.preventDefault();
                                const href = this.getAttribute('href');
                                if (!href || href.length <= 1) return;

                                const targetId = href.substring(1);
                                const targetElement = document.getElementById(targetId);

                                if (targetElement && targetElement.scrollIntoView) {
                                    targetElement.scrollIntoView({
                                        behavior: 'smooth',
                                        block: 'start'
                                    });
                                } else {
                                    console.warn('Context7: Target element not found', targetId);
                                }
                            } catch (error) {
                                console.error('Context7: Anchor scroll error', error);
                            }
                        });
                    });

                    console.log('Context7: Anchor links initialized', anchorLinks.length);
                }

                // Add intersection observer for animations
                try {
                    const observerOptions = {
                        threshold: 0.1,
                        rootMargin: '0px 0px -50px 0px'
                    };

                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            try {
                                if (entry && entry.isIntersecting && entry.target && entry.target.classList) {
                                    entry.target.classList.add('animate-fade-in');
                                }
                            } catch (error) {
                                console.error('Context7: Observer entry error', error);
                            }
                        });
                    }, observerOptions);

                    // Observe all property cards
                    const propertyCards = document.querySelectorAll('.rounded-xl.border');
                    if (propertyCards && propertyCards.length > 0) {
                        propertyCards.forEach(card => {
                            if (card && observer) {
                                observer.observe(card);
                            }
                        });
                        console.log('Context7: Property cards observer initialized', propertyCards.length);
                    }
                } catch (error) {
                    console.error('Context7: IntersectionObserver error', error);
                }
            } catch (error) {
                console.error('Context7: DOMContentLoaded main error', error);
            }
        });

        // Add CSS for animations - FIX: Safe DOM manipulation
        try {
            const style = document.createElement('style');
            if (style) {
                style.textContent = `
                    .animate-fade-in {
                        animation: fadeInUp 0.6s ease-out forwards;
                    }

                    @keyframes fadeInUp {
                        from {
                            opacity: 0;
                            transform: translateY(30px);
                        }
                        to {
                            opacity: 1;
                            transform: translateY(0);
                        }
                    }
                `;
                if (document.head) {
                    document.head.appendChild(style);
                    console.log('Context7: Animation styles added');
                }
            }
        } catch (error) {
            console.error('Context7: Style injection error', error);
        }
    </script>
@endsection
