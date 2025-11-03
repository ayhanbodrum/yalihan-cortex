@props([
    'property' => null,
    'image' => 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=400&h=250&fit=crop',
    'title' => 'Modern Villa',
    'location' => 'Yalıkavak, Bodrum',
    'price' => '₺8,500,000',
    'pricePeriod' => '',
    'beds' => 4,
    'baths' => 3,
    'area' => 250,
    'badge' => 'sale', // sale, rent, featured
    'badgeText' => 'Satılık',
    'isFavorite' => false,
    'showActions' => true,
])

@php
    $badgeClasses = [
        'sale' => 'bg-green-500 text-white',
        'rent' => 'bg-blue-500 text-white',
        'featured' => 'bg-yellow-500 text-white',
    ];

    $badgeClass = $badgeClasses[$badge] ?? 'bg-gray-500 text-white';
@endphp

<div
    class="property-card bg-white rounded-3xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-500 border border-gray-100 relative group transform hover:-translate-y-2">
    <!-- Property Image -->
    <div class="relative h-72 overflow-hidden">
        <img src="{{ $image }}" alt="{{ $title }}"
            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">

        <!-- Gradient Overlay -->
        <div
            class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500">
        </div>

        <!-- Badge -->
        <div
            class="absolute top-5 left-5 {{ $badgeClass }} px-4 py-2 rounded-2xl text-sm font-bold shadow-lg backdrop-blur-sm">
            {{ $badgeText }}
        </div>

        <!-- Favorite Button -->
        <div class="absolute top-5 right-5 w-12 h-12 bg-white/90 backdrop-blur-md rounded-2xl flex items-center justify-center cursor-pointer hover:bg-orange-500 hover:text-white transition-all duration-300 shadow-lg"
            onclick="toggleFavorite(this)">
            @if ($isFavorite)
                <span class="text-red-500 text-xl">❤️</span>
            @else
                <span class="text-gray-600 text-xl">🤍</span>
            @endif
        </div>

        <!-- Action Overlay -->
        @if ($showActions)
            <div
                class="absolute bottom-5 left-5 right-5 opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-500">
                <div class="flex gap-3">
                    <button
                        class="flex-1 bg-white/95 backdrop-blur-md p-3 rounded-2xl hover:bg-orange-500 hover:text-white transition-all duration-300 text-center shadow-lg font-medium"
                        onclick="openModal('virtualTour')" title="360° Sanal Tur">
                        <div class="text-lg mb-1">🔄</div>
                        <div class="text-xs">Sanal Tur</div>
                    </button>
                    <button
                        class="flex-1 bg-white/95 backdrop-blur-md p-3 rounded-2xl hover:bg-orange-500 hover:text-white transition-all duration-300 text-center shadow-lg font-medium"
                        onclick="openModal('gallery')" title="Fotoğraf Galerisi">
                        <div class="text-lg mb-1">📸</div>
                        <div class="text-xs">Galeri</div>
                    </button>
                    <button
                        class="flex-1 bg-white/95 backdrop-blur-md p-3 rounded-2xl hover:bg-orange-500 hover:text-white transition-all duration-300 text-center shadow-lg font-medium"
                        onclick="openModal('map')" title="Haritada Göster">
                        <div class="text-lg mb-1">🗺️</div>
                        <div class="text-xs">Harita</div>
                    </button>
                    <button
                        class="flex-1 bg-white/95 backdrop-blur-md p-3 rounded-2xl hover:bg-orange-500 hover:text-white transition-all duration-300 text-center shadow-lg font-medium"
                        onclick="shareProperty()" title="Paylaş">
                        <div class="text-lg mb-1">📤</div>
                        <div class="text-xs">Paylaş</div>
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Property Content -->
    <div class="p-8">
        <h3 class="text-2xl font-bold text-gray-900 mb-3 line-clamp-2 leading-tight">{{ $title }}</h3>
        <p class="text-gray-600 mb-6 flex items-center text-lg">
            <span class="text-orange-500 mr-2">📍</span> {{ $location }}
        </p>

        <!-- Property Details -->
        <div class="grid grid-cols-3 gap-6 mb-6">
            <div class="text-center p-4 bg-gray-50 rounded-2xl hover:bg-orange-50 transition-colors duration-300">
                <div class="text-3xl mb-2">🛏️</div>
                <div class="text-sm text-gray-500 uppercase tracking-wide font-medium">Yatak</div>
                <div class="font-bold text-gray-900 text-xl">{{ $beds }}</div>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-2xl hover:bg-orange-50 transition-colors duration-300">
                <div class="text-3xl mb-2">🚿</div>
                <div class="text-sm text-gray-500 uppercase tracking-wide font-medium">Banyo</div>
                <div class="font-bold text-gray-900 text-xl">{{ $baths }}</div>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-2xl hover:bg-orange-50 transition-colors duration-300">
                <div class="text-3xl mb-2">📐</div>
                <div class="text-sm text-gray-500 uppercase tracking-wide font-medium">m²</div>
                <div class="font-bold text-gray-900 text-xl">{{ $area }}</div>
            </div>
        </div>

        <!-- Price -->
        <div class="text-3xl font-bold text-orange-600 mb-6">
            <span class="price-display" data-price="{{ str_replace(['₺', ',', '.'], '', $price) }}">
                {{ $price }}
            </span>
            @if ($pricePeriod)
                <span class="text-xl text-gray-500 font-normal">{{ $pricePeriod }}</span>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3">
            <button
                class="flex-1 border-2 border-orange-500 text-orange-500 py-3 px-6 rounded-2xl hover:bg-orange-500 hover:text-white transition-all duration-300 font-semibold text-lg"
                onclick="openModal('propertyDetail')">
                Detayları Gör
            </button>
            <button
                class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 text-white py-3 px-6 rounded-2xl hover:from-orange-600 hover:to-orange-700 transition-all duration-300 font-semibold text-lg shadow-lg hover:shadow-xl"
                onclick="contactProperty()">
                İletişime Geç
            </button>
        </div>
    </div>
</div>

<script>
    function toggleFavorite(element) {
        const isFavorited = element.querySelector('span').textContent === '❤️';
        element.querySelector('span').textContent = isFavorited ? '🤍' : '❤️';
        element.querySelector('span').className = isFavorited ? 'text-gray-600' : 'text-red-500';

        // Toast notification
        showToast(isFavorited ? 'Favorilerden çıkarıldı' : 'Favorilere eklendi', 'success');
    }

    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
        }
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

    function contactProperty() {
        showToast('İletişim formu açılıyor...', 'success');
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
