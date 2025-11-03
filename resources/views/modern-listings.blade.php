@extends('layouts.frontend')

@section('title', 'İlanlar')
@section('description', 'Bodrum\'da satılık ve kiralık ev, villa, arsa ilanları')

@section('content')
    <!-- Page Header -->
    <section class="relative py-32 bg-gradient-to-br from-blue-600 via-purple-600 to-pink-600">
        <div class="absolute inset-0 bg-black/30"></div>
        <div class="container mx-auto px-4 lg:px-8 relative z-10">
            <div class="text-center text-white">
                <h1 class="text-5xl lg:text-6xl font-bold heading-font mb-4">Emlak İlanları</h1>
                <p class="text-xl text-white/90">Bodrum'da hayalinizdeki evi keşfedin</p>

                <!-- Breadcrumb -->
                <div class="flex items-center justify-center space-x-2 mt-8">
                    <a href="/" class="text-white/70 hover:text-white transition-colors">Ana Sayfa</a>
                    <span class="text-white/50">/</span>
                    <span class="text-white">İlanlar</span>
                </div>
            </div>
        </div>

        <!-- Decorative Wave -->
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M0 120L60 110C120 100 240 80 360 70C480 60 600 60 720 65C840 70 960 80 1080 85C1200 90 1320 90 1380 90L1440 90V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z"
                    fill="#F9FAFB" />
            </svg>
        </div>
    </section>

    <!-- Filters & Listings -->
    <section class="py-12 bg-gray-50">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="grid lg:grid-cols-4 gap-8">
                <!-- Sidebar Filters -->
                <div class="lg:col-span-1">
                    <div class="sticky top-24">
                        <!-- Filter Card -->
                        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl font-bold">Filtreler</h3>
                                <button class="text-sm text-blue-600 hover:text-blue-700">Temizle</button>
                            </div>

                            <!-- Search Input -->
                            <div class="mb-6">
                                <div class="relative">
                                    <input type="text" placeholder="Arama yapın..."
                                        class="w-full px-4 py-3 pl-10 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all">
                                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                </div>
                            </div>

                            <!-- Category Filter -->
                            <div class="mb-6">
                                <h4 class="font-semibold mb-3">İlan Tipi</h4>
                                <div class="space-y-2">
                                    <label class="flex items-center space-x-3 cursor-pointer">
                                        <input type="radio" name="type" value="all" checked
                                            class="w-4 h-4 text-blue-600">
                                        <span class="text-gray-700">Tümü</span>
                                    </label>
                                    <label class="flex items-center space-x-3 cursor-pointer">
                                        <input type="radio" name="type" value="sale" class="w-4 h-4 text-blue-600">
                                        <span class="text-gray-700">Satılık</span>
                                    </label>
                                    <label class="flex items-center space-x-3 cursor-pointer">
                                        <input type="radio" name="type" value="rent" class="w-4 h-4 text-blue-600">
                                        <span class="text-gray-700">Kiralık</span>
                                    </label>
                                    <label class="flex items-center space-x-3 cursor-pointer">
                                        <input type="radio" name="type" value="daily" class="w-4 h-4 text-blue-600">
                                        <span class="text-gray-700">Günlük Kiralık</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Property Type -->
                            <div class="mb-6">
                                <h4 class="font-semibold mb-3">Emlak Tipi</h4>
                                <div class="space-y-2">
                                    <label class="flex items-center space-x-3 cursor-pointer">
                                        <input type="checkbox" class="w-4 h-4 text-blue-600 rounded">
                                        <span class="text-gray-700">Daire</span>
                                        <span class="ml-auto text-xs text-gray-500">(124)</span>
                                    </label>
                                    <label class="flex items-center space-x-3 cursor-pointer">
                                        <input type="checkbox" class="w-4 h-4 text-blue-600 rounded">
                                        <span class="text-gray-700">Villa</span>
                                        <span class="ml-auto text-xs text-gray-500">(87)</span>
                                    </label>
                                    <label class="flex items-center space-x-3 cursor-pointer">
                                        <input type="checkbox" class="w-4 h-4 text-blue-600 rounded">
                                        <span class="text-gray-700">Arsa</span>
                                        <span class="ml-auto text-xs text-gray-500">(45)</span>
                                    </label>
                                    <label class="flex items-center space-x-3 cursor-pointer">
                                        <input type="checkbox" class="w-4 h-4 text-blue-600 rounded">
                                        <span class="text-gray-700">İşyeri</span>
                                        <span class="ml-auto text-xs text-gray-500">(32)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Price Range -->
                            <div class="mb-6">
                                <h4 class="font-semibold mb-3">Fiyat Aralığı</h4>
                                <div class="space-y-3">
                                    <input type="number" placeholder="Min Fiyat"
                                        class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <input type="number" placeholder="Max Fiyat"
                                        class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                </div>

                                <!-- Price Range Slider -->
                                <div class="mt-4">
                                    <div class="relative h-2 bg-gray-200 rounded-full">
                                        <div class="absolute left-0 right-0 h-2 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full"
                                            style="left: 20%; right: 30%;"></div>
                                        <div class="absolute left-0 w-4 h-4 bg-white border-2 border-blue-500 rounded-full shadow-lg"
                                            style="left: 20%; top: -4px;"></div>
                                        <div class="absolute right-0 w-4 h-4 bg-white border-2 border-purple-500 rounded-full shadow-lg"
                                            style="right: 30%; top: -4px;"></div>
                                    </div>
                                    <div class="flex justify-between mt-2 text-xs text-gray-500">
                                        <span>₺0</span>
                                        <span>₺50M</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Room Count -->
                            <div class="mb-6">
                                <h4 class="font-semibold mb-3">Oda Sayısı</h4>
                                <div class="grid grid-cols-3 gap-2">
                                    <button
                                        class="px-4 py-2.5 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors text-sm">1+1</button>
                                    <button
                                        class="px-4 py-2.5 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors text-sm">2+1</button>
                                    <button
                                        class="px-4 py-2.5 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors text-sm">3+1</button>
                                    <button
                                        class="px-4 py-2.5 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors text-sm">4+1</button>
                                    <button
                                        class="px-4 py-2.5 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors text-sm">5+1</button>
                                    <button
                                        class="px-4 py-2.5 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors text-sm">6+</button>
                                </div>
                            </div>

                            <!-- Area Range -->
                            <div class="mb-6">
                                <h4 class="font-semibold mb-3">Metrekare</h4>
                                <div class="grid grid-cols-2 gap-3">
                                    <input type="number" placeholder="Min m²"
                                        class="px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm">
                                    <input type="number" placeholder="Max m²"
                                        class="px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm">
                                </div>
                            </div>

                            <!-- Features -->
                            <div class="mb-6">
                                <h4 class="font-semibold mb-3">Özellikler</h4>
                                <div class="space-y-2">
                                    <label class="flex items-center space-x-3 cursor-pointer">
                                        <input type="checkbox" class="w-4 h-4 text-blue-600 rounded">
                                        <span class="text-gray-700 text-sm">Deniz Manzaralı</span>
                                    </label>
                                    <label class="flex items-center space-x-3 cursor-pointer">
                                        <input type="checkbox" class="w-4 h-4 text-blue-600 rounded">
                                        <span class="text-gray-700 text-sm">Havuzlu</span>
                                    </label>
                                    <label class="flex items-center space-x-3 cursor-pointer">
                                        <input type="checkbox" class="w-4 h-4 text-blue-600 rounded">
                                        <span class="text-gray-700 text-sm">Bahçeli</span>
                                    </label>
                                    <label class="flex items-center space-x-3 cursor-pointer">
                                        <input type="checkbox" class="w-4 h-4 text-blue-600 rounded">
                                        <span class="text-gray-700 text-sm">Otoparklı</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Apply Button -->
                            <button
                                class="w-full py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                                Filtreleri Uygula
                            </button>
                        </div>

                        <!-- Help Card -->
                        <div class="bg-gradient-to-br from-blue-100 to-purple-100 rounded-2xl p-6">
                            <div class="text-center">
                                <i class="fas fa-headset text-4xl text-blue-600 mb-4"></i>
                                <h4 class="font-bold mb-2">Yardıma mı ihtiyacınız var?</h4>
                                <p class="text-sm text-gray-600 mb-4">Uzman ekibimiz size yardımcı olmaktan mutluluk duyar
                                </p>
                                <a href="tel:05332090302"
                                    class="inline-flex items-center space-x-2 px-4 py-2 bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                                    <i class="fas fa-phone text-blue-600"></i>
                                    <span class="font-semibold">0533 209 03 02</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Listings Grid -->
                <div class="lg:col-span-3">
                    <!-- Toolbar -->
                    <div class="bg-white rounded-2xl shadow-lg p-4 mb-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex items-center space-x-4">
                                <span class="text-gray-600">Toplam <strong>248</strong> ilan bulundu</span>
                            </div>

                            <div class="flex items-center space-x-4">
                                <!-- View Mode -->
                                <div class="flex items-center space-x-2 bg-gray-100 rounded-lg p-1">
                                    <button class="px-3 py-1.5 bg-white rounded text-gray-700 shadow-sm">
                                        <i class="fas fa-th-large"></i>
                                    </button>
                                    <button class="px-3 py-1.5 text-gray-500 hover:text-gray-700">
                                        <i class="fas fa-th-list"></i>
                                    </button>
                                    <button class="px-3 py-1.5 text-gray-500 hover:text-gray-700">
                                        <i class="fas fa-map-marked-alt"></i>
                                    </button>
                                </div>

                                <!-- Sort -->
                                <select
                                    class="px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <option>Sıralama</option>
                                    <option>Fiyat (Düşük-Yüksek)</option>
                                    <option>Fiyat (Yüksek-Düşük)</option>
                                    <option>Tarih (Yeni-Eski)</option>
                                    <option>Tarih (Eski-Yeni)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Listings Grid -->
                    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @for ($i = 1; $i <= 12; $i++)
                            <div class="group cursor-pointer" x-data="{ liked: false, imageIndex: 0, images: [1, 2, 3, 4] }">
                                <div
                                    class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300">
                                    <!-- Image Carousel -->
                                    <div class="relative h-64 overflow-hidden">
                                        <!-- Images -->
                                        <div class="relative h-full">
                                            <img src="https://images.unsplash.com/photo-{{ 1600000000000 + $i * 1000000 }}-9991f1c4c750?w=600"
                                                alt="Property {{ $i }}" class="w-full h-full object-cover">

                                            <!-- Image Navigation Dots -->
                                            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-2">
                                                <template x-for="(img, index) in images" :key="index">
                                                    <button @click="imageIndex = index"
                                                        :class="{
                                                            'bg-white': imageIndex ===
                                                                index,
                                                            'bg-white/50': imageIndex !== index
                                                        }"
                                                        class="w-2 h-2 rounded-full transition-all"></button>
                                                </template>
                                            </div>
                                        </div>

                                        <!-- Overlay Gradient -->
                                        <div
                                            class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent pointer-events-none">
                                        </div>

                                        <!-- Top Badges -->
                                        <div class="absolute top-4 left-4 flex flex-wrap gap-2">
                                            @if ($i <= 3)
                                                <span
                                                    class="px-3 py-1 bg-red-500 text-white text-xs font-semibold rounded-full">YENİ</span>
                                            @endif
                                            @if ($i % 3 == 0)
                                                <span
                                                    class="px-3 py-1 bg-green-500 text-white text-xs font-semibold rounded-full">FIRSSAT</span>
                                            @endif
                                            <span
                                                class="px-3 py-1 bg-white/90 backdrop-blur text-gray-800 text-xs font-semibold rounded-full">
                                                {{ ['SATILIK', 'KİRALIK', 'GÜNLÜK'][$i % 3] }}
                                            </span>
                                        </div>

                                        <!-- Actions -->
                                        <div class="absolute top-4 right-4 flex flex-col space-y-2">
                                            <button @click="liked = !liked"
                                                :class="{ 'bg-red-500 text-white': liked, 'bg-white/90 text-gray-700': !liked }"
                                                class="w-10 h-10 backdrop-blur rounded-full flex items-center justify-center hover:scale-110 transition-all">
                                                <i class="fas fa-heart"></i>
                                            </button>
                                            <button
                                                class="w-10 h-10 bg-white/90 backdrop-blur text-gray-700 rounded-full flex items-center justify-center hover:scale-110 transition-all">
                                                <i class="fas fa-share-alt"></i>
                                            </button>
                                            <button
                                                class="w-10 h-10 bg-white/90 backdrop-blur text-gray-700 rounded-full flex items-center justify-center hover:scale-110 transition-all">
                                                <i class="fas fa-expand"></i>
                                            </button>
                                        </div>

                                        <!-- Price Badge -->
                                        <div class="absolute bottom-4 left-4">
                                            <div class="text-2xl font-bold text-white">
                                                ₺{{ number_format(1000000 + $i * 500000, 0, ',', '.') }}</div>
                                            @if ($i % 3 == 1)
                                                <div class="text-sm text-white/90">Aylık</div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="p-6">
                                        <!-- Title -->
                                        <h3
                                            class="text-xl font-bold mb-2 group-hover:text-blue-600 transition-colors line-clamp-1">
                                            {{ [
                                                'Deniz Manzaralı Lüks Villa',
                                                'Modern Daire',
                                                'Müstakil Ev',
                                                'Rezidans',
                                                'Taş Villa',
                                                'Yazlık Villa',
                                                'Merkezi Daire',
                                                'Bahçeli Ev',
                                                'Penthouse',
                                                'Stüdyo Daire',
                                                'Triplex Villa',
                                                'Dubleks Daire',
                                            ][$i - 1] }}
                                        </h3>

                                        <!-- Location -->
                                        <div class="flex items-center text-gray-600 text-sm mb-4">
                                            <i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>
                                            {{ [
                                                'Yalıkavak',
                                                'Türkbükü',
                                                'Bodrum Merkez',
                                                'Gümüşlük',
                                                'Bitez',
                                                'Gündoğan',
                                                'Göltürkbükü',
                                                'Torba',
                                                'Güvercinlik',
                                                'Ortakent',
                                                'Akyarlar',
                                                'Turgutreis',
                                            ][$i - 1] }},
                                            Bodrum
                                        </div>

                                        <!-- Features Grid -->
                                        <div class="grid grid-cols-4 gap-3 py-4 border-t border-gray-100">
                                            <div class="text-center">
                                                <i class="fas fa-bed text-gray-400 text-sm mb-1"></i>
                                                <div class="text-sm font-semibold">{{ 2 + ($i % 4) }}+1</div>
                                            </div>
                                            <div class="text-center">
                                                <i class="fas fa-bath text-gray-400 text-sm mb-1"></i>
                                                <div class="text-sm font-semibold">{{ 1 + ($i % 3) }}</div>
                                            </div>
                                            <div class="text-center">
                                                <i class="fas fa-expand-arrows-alt text-gray-400 text-sm mb-1"></i>
                                                <div class="text-sm font-semibold">{{ 100 + $i * 20 }}m²</div>
                                            </div>
                                            <div class="text-center">
                                                <i class="fas fa-calendar text-gray-400 text-sm mb-1"></i>
                                                <div class="text-sm font-semibold">{{ 2020 + ($i % 5) }}</div>
                                            </div>
                                        </div>

                                        <!-- Tags -->
                                        <div class="flex flex-wrap gap-2 mb-4">
                                            @if ($i % 2 == 0)
                                                <span
                                                    class="px-2 py-1 bg-blue-100 text-blue-600 text-xs rounded-full">Deniz
                                                    Manzaralı</span>
                                            @endif
                                            @if ($i % 3 == 0)
                                                <span
                                                    class="px-2 py-1 bg-green-100 text-green-600 text-xs rounded-full">Havuzlu</span>
                                            @endif
                                            @if ($i % 4 == 0)
                                                <span
                                                    class="px-2 py-1 bg-purple-100 text-purple-600 text-xs rounded-full">Bahçeli</span>
                                            @endif
                                        </div>

                                        <!-- Footer Actions -->
                                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                            <div class="flex items-center space-x-2">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode('Agent ' . $i) }}&background=667eea&color=fff"
                                                    alt="Agent" class="w-8 h-8 rounded-full">
                                                <div>
                                                    <div class="text-xs font-semibold">
                                                        {{ ['Ahmet Y.', 'Mehmet K.', 'Ayşe D.', 'Fatma S.', 'Ali B.', 'Veli Ç.'][$i % 6] }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">Danışman</div>
                                                </div>
                                            </div>

                                            <div class="flex space-x-2">
                                                <a href="#"
                                                    class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white text-sm font-semibold rounded-lg hover:shadow-lg transform hover:scale-105 transition-all">
                                                    Detaylar
                                                </a>
                                                <button
                                                    class="px-4 py-2.5 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                                                    <i class="fab fa-whatsapp"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>

                    <!-- Pagination -->
                    <div class="mt-12 flex justify-center">
                        <nav class="flex items-center space-x-2">
                            <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button
                                class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg">1</button>
                            <button
                                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">2</button>
                            <button
                                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">3</button>
                            <span class="px-2">...</span>
                            <button
                                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">21</button>
                            <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter CTA -->
    <section class="py-16 bg-gradient-to-br from-blue-600 via-purple-600 to-pink-600">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center text-white">
                <h2 class="text-3xl font-bold mb-4">Yeni İlanlardan Haberdar Olun</h2>
                <p class="text-lg text-white/90 mb-8">Aradığınız kriterlere uygun ilanlar eklendiğinde size haber verelim
                </p>

                <div class="max-w-md mx-auto">
                    <div class="flex gap-3">
                        <input type="email" placeholder="E-posta adresiniz"
                            class="flex-1 px-6 py-3 rounded-xl text-gray-900 placeholder-gray-500 focus:ring-4 focus:ring-white/30">
                        <button
                            class="px-8 py-3 bg-white text-gray-900 font-semibold rounded-xl hover:shadow-lg transform hover:scale-105 transition-all">
                            Abone Ol
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Filter interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Price range slider logic
            const priceSlider = document.querySelector('.price-slider');
            // Add slider functionality here

            // Filter toggle for mobile
            const filterToggle = document.querySelector('.filter-toggle');
            // Add mobile filter toggle here
        });
    </script>
@endpush
