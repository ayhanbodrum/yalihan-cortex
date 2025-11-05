@props([
    'properties' => [],
    'showFilters' => true,
    'showSort' => true,
    'showViewToggle' => true,
    'viewMode' => 'grid', // grid, list
    'pagination' => true,
    'class' => '',
])

@php
    $defaultProperties = [
        [
            'id' => 1,
            'title' => 'Modern Villa - Yalıkavak',
            'location' => 'Yalıkavak, Bodrum',
            'price' => '₺8,500,000',
            'pricePeriod' => null,
            'beds' => 4,
            'baths' => 3,
            'area' => 250,
            'badge' => 'sale',
            'badgeText' => 'Satılık',
            'image' => 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=400&h=250&fit=crop',
            'isFavorite' => false,
            'features' => ['Havuz', 'Bahçe', 'Garaj'],
            'agent' => 'Ahmet Yılmaz',
            'date' => '2 gün önce',
        ],
        [
            'id' => 2,
            'title' => 'Lüks Daire - Gümbet',
            'location' => 'Gümbet, Bodrum',
            'price' => '₺15,000',
            'pricePeriod' => '/ay',
            'beds' => 2,
            'baths' => 2,
            'area' => 120,
            'badge' => 'rent',
            'badgeText' => 'Kiralık',
            'image' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=400&h=250&fit=crop',
            'isFavorite' => true,
            'features' => ['Balkon', 'Klima', 'Güvenlik'],
            'agent' => 'Mehmet Kaya',
            'date' => '1 hafta önce',
        ],
        [
            'id' => 3,
            'title' => 'Deniz Manzaralı Villa - Bitez',
            'location' => 'Bitez, Bodrum',
            'price' => '₺12,500,000',
            'pricePeriod' => null,
            'beds' => 5,
            'baths' => 4,
            'area' => 350,
            'badge' => 'featured',
            'badgeText' => 'Öne Çıkan',
            'image' => 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?w=400&h=250&fit=crop',
            'isFavorite' => false,
            'features' => ['Havuz', 'Deniz Manzarası', 'Bahçe', 'Garaj'],
            'agent' => 'Ayşe Demir',
            'date' => '3 gün önce',
        ],
        [
            'id' => 4,
            'title' => 'Şehir Merkezi Daire',
            'location' => 'Bodrum Merkez',
            'price' => '₺25,000',
            'pricePeriod' => '/ay',
            'beds' => 3,
            'baths' => 2,
            'area' => 150,
            'badge' => 'rent',
            'badgeText' => 'Kiralık',
            'image' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=400&h=250&fit=crop',
            'isFavorite' => false,
            'features' => ['Asansör', 'Güvenlik', 'Fitness'],
            'agent' => 'Can Özkan',
            'date' => '5 gün önce',
        ],
        [
            'id' => 5,
            'title' => 'Türkbükü Villa',
            'location' => 'Türkbükü, Bodrum',
            'price' => '₺15,000,000',
            'pricePeriod' => null,
            'beds' => 6,
            'baths' => 5,
            'area' => 450,
            'badge' => 'sale',
            'badgeText' => 'Satılık',
            'image' => 'https://images.unsplash.com/photo-1600607687644-c7171b42498b?w=400&h=250&fit=crop',
            'isFavorite' => true,
            'features' => ['Havuz', 'Deniz Manzarası', 'Bahçe', 'Garaj', 'Fitness'],
            'agent' => 'Zeynep Arslan',
            'date' => '1 gün önce',
        ],
        [
            'id' => 6,
            'title' => 'Göltürkbükü Daire',
            'location' => 'Göltürkbükü, Bodrum',
            'price' => '₺18,000',
            'pricePeriod' => '/ay',
            'beds' => 2,
            'baths' => 1,
            'area' => 90,
            'badge' => 'rent',
            'badgeText' => 'Kiralık',
            'image' => 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?w=400&h=250&fit=crop',
            'isFavorite' => false,
            'features' => ['Balkon', 'Klima'],
            'agent' => 'Emre Yıldız',
            'date' => '4 gün önce',
        ],
    ];

    $propertyList = !empty($properties) ? $properties : $defaultProperties;
@endphp

<div class="property-listing-page {{ $class }}">
    <!-- Header Section -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Emlak İlanları</h1>
                    <p class="text-gray-600">{{ count($propertyList) }} ilan bulundu</p>
                </div>

                @if ($showViewToggle)
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600">Görünüm:</span>
                        <button id="gridView"
                            class="p-2 rounded-lg {{ $viewMode === 'grid' ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-600' }}"
                            onclick="changeView('grid')">
                            ⊞
                        </button>
                        <button id="listView"
                            class="p-2 rounded-lg {{ $viewMode === 'list' ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-600' }}"
                            onclick="changeView('list')">
                            ☰
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Filters Sidebar -->
            @if ($showFilters)
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-8">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6">Filtreler</h3>

                        <!-- Search Form -->
                        <x-yaliihan.search-form :show-advanced="true" :show-sort="false" class="mb-6" />

                        <!-- Price Range -->
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-900 mb-3">Fiyat Aralığı</h4>
                            <div class="space-y-2">
                                <input type="number" placeholder="Min. Fiyat"
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                                <input type="number" placeholder="Max. Fiyat"
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                        </div>

                        <!-- Property Type -->
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-900 mb-3">Emlak Türü</h4>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox"
                                        class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                                    <span class="ml-2 text-gray-700">Villa</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox"
                                        class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                                    <span class="ml-2 text-gray-700">Daire</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox"
                                        class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                                    <span class="ml-2 text-gray-700">Arsa</span>
                                </label>
                            </div>
                        </div>

                        <!-- Features -->
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-900 mb-3">Özellikler</h4>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox"
                                        class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                                    <span class="ml-2 text-gray-700">Havuz</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox"
                                        class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                                    <span class="ml-2 text-gray-700">Bahçe</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox"
                                        class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                                    <span class="ml-2 text-gray-700">Garaj</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox"
                                        class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                                    <span class="ml-2 text-gray-700">Deniz Manzarası</span>
                                </label>
                            </div>
                        </div>

                        <button class="w-full btn inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg" onclick="applyFilters()">
                            Filtreleri Uygula
                        </button>
                    </div>
                </div>
            @endif

            <!-- Properties Grid/List -->
            <div class="{{ $showFilters ? 'lg:col-span-3' : 'lg:col-span-4' }}">
                <!-- Sort Bar -->
                @if ($showSort)
                    <div class="bg-white rounded-2xl shadow-lg p-4 mb-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-gray-700">Sırala:</span>
                                <select
                                    class="p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                                    <option value="default">Varsayılan</option>
                                    <option value="price_asc">Fiyat (Düşük → Yüksek)</option>
                                    <option value="price_desc">Fiyat (Yüksek → Düşük)</option>
                                    <option value="date_asc">Tarih (Eski → Yeni)</option>
                                    <option value="date_desc">Tarih (Yeni → Eski)</option>
                                    <option value="area_asc">Alan (Küçük → Büyük)</option>
                                    <option value="area_desc">Alan (Büyük → Küçük)</option>
                                </select>
                            </div>

                            <div class="text-sm text-gray-600">
                                {{ count($propertyList) }} ilan gösteriliyor
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Properties Grid/List -->
                <div id="propertiesContainer"
                    class="{{ $viewMode === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6' : 'space-y-6' }}">
                    @foreach ($propertyList as $property)
                        @if ($viewMode === 'grid')
                            <!-- Grid View -->
                            <x-yaliihan.property-card :image="$property['image']" :title="$property['title']" :location="$property['location']"
                                :price="$property['price']" :price-period="$property['pricePeriod']" :beds="$property['beds']" :baths="$property['baths']"
                                :area="$property['area']" :badge="$property['badge']" :badge-text="$property['badgeText']" :is-favorite="$property['isFavorite']" />
                        @else
                            <!-- List View -->
                            <div
                                class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                                <div class="flex flex-col md:flex-row">
                                    <!-- Image -->
                                    <div class="md:w-80 h-64 md:h-auto">
                                        <img src="{{ $property['image'] }}" alt="{{ $property['title'] }}"
                                            class="w-full h-full object-cover">
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-1 p-6">
                                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <span
                                                        class="px-3 py-1 rounded-full text-sm font-semibold {{ $property['badge'] === 'sale' ? 'bg-green-500 text-white' : ($property['badge'] === 'rent' ? 'bg-blue-500 text-white' : 'bg-yellow-500 text-white') }}">
                                                        {{ $property['badgeText'] }}
                                                    </span>
                                                    <span class="text-sm text-gray-500">{{ $property['date'] }}</span>
                                                </div>

                                                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                                    {{ $property['title'] }}</h3>
                                                <p class="text-gray-600 mb-4">📍 {{ $property['location'] }}</p>

                                                <div class="grid grid-cols-3 gap-4 mb-4">
                                                    <div class="text-center">
                                                        <div class="text-2xl mb-1">🛏️</div>
                                                        <div class="text-sm text-gray-500">Yatak</div>
                                                        <div class="font-semibold">{{ $property['beds'] }}</div>
                                                    </div>
                                                    <div class="text-center">
                                                        <div class="text-2xl mb-1">🚿</div>
                                                        <div class="text-sm text-gray-500">Banyo</div>
                                                        <div class="font-semibold">{{ $property['baths'] }}</div>
                                                    </div>
                                                    <div class="text-center">
                                                        <div class="text-2xl mb-1">📐</div>
                                                        <div class="text-sm text-gray-500">m²</div>
                                                        <div class="font-semibold">{{ $property['area'] }}</div>
                                                    </div>
                                                </div>

                                                <div class="flex flex-wrap gap-2 mb-4">
                                                    @foreach ($property['features'] as $feature)
                                                        <span
                                                            class="px-2 py-1 bg-gray-100 text-gray-700 text-sm rounded">{{ $feature }}</span>
                                                    @endforeach
                                                </div>

                                                <div class="text-sm text-gray-500">
                                                    Danışman: {{ $property['agent'] }}
                                                </div>
                                            </div>

                                            <div class="flex flex-col items-end gap-4">
                                                <div class="text-right">
                                                    <div class="text-2xl font-bold text-orange-600">
                                                        {{ $property['price'] }}
                                                        @if ($property['pricePeriod'])
                                                            <span
                                                                class="text-lg text-gray-500 font-normal">{{ $property['pricePeriod'] }}</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="flex gap-2">
                                                    <button
                                                        class="p-2 border border-orange-500 text-orange-500 rounded-lg hover:bg-orange-500 hover:text-white transition-colors"
                                                        onclick="toggleFavorite({{ $property['id'] }})">
                                                        {{ $property['isFavorite'] ? '❤️' : '🤍' }}
                                                    </button>
                                                    <button
                                                        class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                                                        Detay
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <!-- Pagination -->
                @if ($pagination)
                    <div class="mt-8 flex justify-center">
                        <nav class="flex items-center gap-2">
                            <button
                                class="px-4 py-2.5 border border-gray-300 rounded-lg hover:bg-orange-500 hover:text-white transition-colors">
                                ← Önceki
                            </button>
                            <button class="px-4 py-2.5 bg-orange-500 text-white rounded-lg">1</button>
                            <button
                                class="px-4 py-2.5 border border-gray-300 rounded-lg hover:bg-orange-500 hover:text-white transition-colors">2</button>
                            <button
                                class="px-4 py-2.5 border border-gray-300 rounded-lg hover:bg-orange-500 hover:text-white transition-colors">3</button>
                            <button
                                class="px-4 py-2.5 border border-gray-300 rounded-lg hover:bg-orange-500 hover:text-white transition-colors">
                                Sonraki →
                            </button>
                        </nav>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    // View Mode Functions
    function changeView(mode) {
        const container = document.getElementById('propertiesContainer');
        const gridBtn = document.getElementById('gridView');
        const listBtn = document.getElementById('listView');

        if (mode === 'grid') {
            container.className = 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6';
            gridBtn.className = 'p-2 rounded-lg bg-orange-500 text-white';
            listBtn.className = 'p-2 rounded-lg bg-gray-200 text-gray-600';
        } else {
            container.className = 'space-y-6';
            listBtn.className = 'p-2 rounded-lg bg-orange-500 text-white';
            gridBtn.className = 'p-2 rounded-lg bg-gray-200 text-gray-600';
        }

        // Store preference
        localStorage.setItem('propertyViewMode', mode);
    }

    // Filter Functions
    function applyFilters() {
        showToast('Filtreler uygulanıyor...', 'success');
        // Burada gerçek filtreleme işlemi yapılacak
    }

    // Favorite Functions
    function toggleFavorite(propertyId) {
        showToast('Favori statusu güncellendi', 'success');
        // Burada gerçek favori işlemi yapılacak
    }

    // Toast Notification
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 bg-white rounded-lg p-4 shadow-lg border-l-4 ${
        type === 'success' ? 'border-green-500' : 'border-red-500'
    } z-50 transform translate-x-full transition-transform duration-300`;
        toast.innerHTML = message;
        document.body.appendChild(toast);

        setTimeout(() => toast.classList.remove('translate-x-full'), 100);
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => document.body.removeChild(toast), 300);
        }, 3000);
    }

    // Load saved view mode
    document.addEventListener('DOMContentLoaded', function() {
        const savedViewMode = localStorage.getItem('propertyViewMode');
        if (savedViewMode) {
            changeView(savedViewMode);
        }
    });
</script>

<style>
    .property-listing-page {
        min-height: 100vh;
        background-color: #f8fafc;
    }

    .btn {
        @apply px-4 py-2 rounded-lg font-medium transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2;
    }

    .inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg {
        @apply bg-orange-500 text-white hover:bg-orange-600 focus:ring-orange-500;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .property-listing-page .lg\\:col-span-1 {
            grid-column: 1;
        }

        .property-listing-page .lg\\:col-span-3 {
            grid-column: 1;
        }

        .property-listing-page .lg\\:col-span-4 {
            grid-column: 1;
        }
    }
</style>
