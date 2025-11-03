@props([
    'title' => 'Modern Villa - Yalıkavak',
    'location' => 'Yalıkavak, Bodrum',
    'price' => '₺8,500,000',
    'pricePeriod' => null,
    'beds' => 4,
    'baths' => 3,
    'area' => 250,
    'badge' => 'sale',
    'badgeText' => 'Satılık',
    'isFavorite' => false,
    'images' => [],
    'description' => 'Bu harika villa, Bodrum\'un en prestijli bölgelerinden biri olan Yalıkavak\'ta yer almaktadır. Deniz manzaralı, modern tasarımı ve lüks özellikleri ile dikkat çeken bu emlak, aileler için ideal bir yaşam alanı sunmaktadır.',
    'features' => [],
    'agent' => null,
    'showMap' => true,
    'showVirtualTour' => true,
    'showGallery' => true,
    'showShare' => true,
    'class' => '',
])

@php
    $badgeClasses = [
        'sale' => 'bg-green-500 text-white',
        'rent' => 'bg-blue-500 text-white',
        'featured' => 'bg-yellow-500 text-white',
    ];

    $badgeClass = $badgeClasses[$badge] ?? 'bg-gray-500 text-white';

    $defaultImages = [
        'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800&h=600&fit=crop',
        'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800&h=600&fit=crop',
        'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?w=800&h=600&fit=crop',
        'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&h=600&fit=crop',
        'https://images.unsplash.com/photo-1600607687644-c7171b42498b?w=800&h=600&fit=crop',
    ];

    $propertyImages = !empty($images) ? $images : $defaultImages;

    $defaultFeatures = ['Havuz', 'Bahçe', 'Garaj', 'Balkon', 'Klima', 'Güvenlik', 'Asansör', 'Fitness'];

    $propertyFeatures = !empty($features) ? $features : $defaultFeatures;

    $defaultAgent = [
        'name' => 'Ahmet Yılmaz',
        'phone' => '0533 209 03 02',
        'email' => 'ahmet@yalihanemlak.com',
        'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop&crop=face',
        'rating' => 4.8,
        'properties' => 25,
    ];

    $propertyAgent = $agent ?? $defaultAgent;
@endphp

<div class="property-detail-page {{ $class }}">
    <!-- Property Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- Title & Location -->
                <div class="flex-1">
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">{{ $title }}</h1>
                    <div class="flex items-center gap-2 text-gray-600 mb-4">
                        <span class="text-orange-500">📍</span>
                        <span class="text-lg">{{ $location }}</span>
                    </div>

                    <!-- Price -->
                    <div class="text-3xl lg:text-4xl font-bold text-orange-600">
                        {{ $price }}
                        @if ($pricePeriod)
                            <span class="text-lg text-gray-500 font-normal">{{ $pricePeriod }}</span>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-3">
                    @if ($showVirtualTour)
                        <button class="btn btn-outline flex items-center gap-2" onclick="openVirtualTour()">
                            <span>🔄</span>
                            <span>360° Tur</span>
                        </button>
                    @endif

                    @if ($showGallery)
                        <button class="btn btn-outline flex items-center gap-2" onclick="openGallery()">
                            <span>📸</span>
                            <span>Galeri</span>
                        </button>
                    @endif

                    @if ($showMap)
                        <button class="btn btn-outline flex items-center gap-2" onclick="openMap()">
                            <span>🗺️</span>
                            <span>Harita</span>
                        </button>
                    @endif

                    @if ($showShare)
                        <button class="btn btn-outline flex items-center gap-2" onclick="shareProperty()">
                            <span>📤</span>
                            <span>Paylaş</span>
                        </button>
                    @endif

                    <button class="btn neo-btn neo-btn-primary flex items-center gap-2" onclick="toggleFavorite()">
                        <span id="favoriteIcon">{{ $isFavorite ? '❤️' : '🤍' }}</span>
                        <span>Favori</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Images & Details -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Image Gallery -->
                <div class="property-gallery">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Main Image -->
                        <div class="md:col-span-2">
                            <div class="relative h-96 rounded-2xl overflow-hidden">
                                <img id="mainImage" src="{{ $propertyImages[0] }}" alt="{{ $title }}"
                                    class="w-full h-full object-cover cursor-pointer transition-transform duration-300 hover:scale-105">
                                <div
                                    class="absolute top-4 left-4 {{ $badgeClass }} px-4 py-2 rounded-full text-sm font-semibold">
                                    {{ $badgeText }}
                                </div>
                                <div class="absolute top-4 right-4 bg-white bg-opacity-90 rounded-full p-2">
                                    <span class="text-sm font-medium">{{ count($propertyImages) }} Fotoğraf</span>
                                </div>
                            </div>
                        </div>

                        <!-- Thumbnail Images -->
                        @foreach (array_slice($propertyImages, 1, 4) as $index => $image)
                            <div class="relative h-24 rounded-lg overflow-hidden cursor-pointer hover:opacity-80 transition-opacity"
                                onclick="changeMainImage('{{ $image }}')">
                                <img src="{{ $image }}" alt="{{ $title }} - {{ $index + 2 }}"
                                    class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Property Details -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Emlak Detayları</h2>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                        <div class="text-center">
                            <div class="text-3xl mb-2">🛏️</div>
                            <div class="text-sm text-gray-500">Yatak Odası</div>
                            <div class="text-xl font-semibold text-gray-900">{{ $beds }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl mb-2">🚿</div>
                            <div class="text-sm text-gray-500">Banyo</div>
                            <div class="text-xl font-semibold text-gray-900">{{ $baths }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl mb-2">📐</div>
                            <div class="text-sm text-gray-500">Alan (m²)</div>
                            <div class="text-xl font-semibold text-gray-900">{{ $area }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl mb-2">🏠</div>
                            <div class="text-sm text-gray-500">Tip</div>
                            <div class="text-xl font-semibold text-gray-900">Villa</div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Açıklama</h3>
                        <p class="text-gray-600 leading-relaxed">{{ $description }}</p>
                    </div>

                    <!-- Features -->
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Özellikler</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach ($propertyFeatures as $feature)
                                <div class="flex items-center gap-2 text-gray-600">
                                    <span class="text-green-500">✓</span>
                                    <span>{{ $feature }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Map Section -->
                @if ($showMap)
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Konum</h3>
                        <x-yaliihan.map-component :center="[
                            'lat' => $mapCoordinates['lat'] ?? 37.4220656,
                            'lng' => $mapCoordinates['lng'] ?? -122.0840897,
                        ]" :zoom="15" :markers="[
                            [
                                'position' => [
                                    'lat' => $mapCoordinates['lat'] ?? 37.4220656,
                                    'lng' => $mapCoordinates['lng'] ?? -122.0840897,
                                ],
                                'title' => $title,
                                'content' => $location,
                                'icon' => null,
                            ],
                        ]" height="400px"
                            :show-traffic="true" :show-transit="true" :show-bicycling="true" class="property-map" />
                    </div>
                @endif
            </div>

            <!-- Right Column - Agent & Contact -->
            <div class="space-y-6">
                <!-- Agent Card -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Emlak Danışmanı</h3>

                    <div class="flex items-center gap-4 mb-4">
                        <img src="{{ $propertyAgent['avatar'] }}" alt="{{ $propertyAgent['name'] }}"
                            class="w-16 h-16 rounded-full object-cover">
                        <div>
                            <h4 class="font-semibold text-gray-900">{{ $propertyAgent['name'] }}</h4>
                            <div class="flex items-center gap-1 text-yellow-500">
                                @for ($i = 0; $i < 5; $i++)
                                    <span>{{ $i < floor($propertyAgent['rating']) ? '★' : '☆' }}</span>
                                @endfor
                                <span class="text-sm text-gray-500 ml-1">{{ $propertyAgent['rating'] }}</span>
                            </div>
                            <div class="text-sm text-gray-500">{{ $propertyAgent['properties'] }} İlan</div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <a href="tel:{{ $propertyAgent['phone'] }}"
                            class="w-full btn neo-btn neo-btn-primary flex items-center justify-center gap-2">
                            <span>📞</span>
                            <span>Ara</span>
                        </a>
                        <a href="mailto:{{ $propertyAgent['email'] }}"
                            class="w-full btn btn-outline flex items-center justify-center gap-2">
                            <span>✉️</span>
                            <span>E-posta</span>
                        </a>
                        <button class="w-full btn btn-outline flex items-center justify-center gap-2">
                            <span>💬</span>
                            <span>WhatsApp</span>
                        </button>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">İletişim Formu</h3>

                    <form class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ad Soyad</label>
                            <input type="text"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                placeholder="Adınız ve soyadınız">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">E-posta</label>
                            <input type="email"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                placeholder="E-posta adresiniz">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Telefon</label>
                            <input type="tel"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                placeholder="Telefon numaranız">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mesaj</label>
                            <textarea rows="4"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                placeholder="Mesajınızı yazın..."></textarea>
                        </div>

                        <button type="submit" class="w-full btn neo-btn neo-btn-primary">
                            Mesaj Gönder
                        </button>
                    </form>
                </div>

                <!-- Similar Properties -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Benzer İlanlar</h3>

                    <div class="space-y-4">
                        <div class="flex gap-3">
                            <img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=100&h=100&fit=crop"
                                alt="Similar Property" class="w-20 h-20 rounded-lg object-cover">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 text-sm">Lüks Daire - Gümbet</h4>
                                <p class="text-gray-500 text-sm">Gümbet, Bodrum</p>
                                <p class="text-orange-600 font-semibold">₺15,000/ay</p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <img src="https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?w=100&h=100&fit=crop"
                                alt="Similar Property" class="w-20 h-20 rounded-lg object-cover">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 text-sm">Deniz Manzaralı Villa</h4>
                                <p class="text-gray-500 text-sm">Bitez, Bodrum</p>
                                <p class="text-orange-600 font-semibold">₺12,500,000</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Image Gallery Functions
    function changeMainImage(imageSrc) {
        document.getElementById('mainImage').src = imageSrc;
    }

    // Action Functions
    function openVirtualTour() {
        showToast('360° Sanal Tur açılıyor...', 'success');
    }

    function openGallery() {
        showToast('Fotoğraf galerisi açılıyor...', 'success');
    }

    function openMap() {
        showToast('Harita açılıyor...', 'success');
    }

    function shareProperty() {
        if (navigator.share) {
            navigator.share({
                title: '{{ $title }} - Yalıhan Emlak',
                text: 'Bu harika emlakı inceleyin!',
                url: window.location.href
            });
        } else {
            showToast('Paylaşım linki kopyalandı!', 'success');
        }
    }

    function toggleFavorite() {
        const icon = document.getElementById('favoriteIcon');
        const isFavorited = icon.textContent === '❤️';

        icon.textContent = isFavorited ? '🤍' : '❤️';

        showToast(isFavorited ? 'Favorilerden çıkarıldı' : 'Favorilere eklendi', 'success');
    }

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
</script>

<style>
    .property-detail-page {
        min-height: 100vh;
        background-color: #f8fafc;
    }

    .property-gallery img {
        transition: all 0.3s ease;
    }

    .property-gallery img:hover {
        transform: scale(1.02);
    }

    .btn {
        @apply px-4 py-2 rounded-lg font-medium transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2;
    }

    .neo-btn neo-btn-primary {
        @apply bg-orange-500 text-white hover:bg-orange-600 focus:ring-orange-500;
    }

    .btn-outline {
        @apply border border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white focus:ring-orange-500;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .property-detail-page .grid {
            grid-template-columns: 1fr;
        }

        .property-detail-page .lg\\:col-span-2 {
            grid-column: 1;
        }

        .property-detail-page .lg\\:col-span-1 {
            grid-column: 1;
        }
    }
</style>
