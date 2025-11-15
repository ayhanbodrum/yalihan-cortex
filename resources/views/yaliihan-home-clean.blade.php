@extends('layouts.frontend')

@section('title', 'Ana Sayfa - Yalıhan Emlak')

@section('content')
    <!-- Hero Section -->
<header class="absolute top-0 inset-x-0 z-30">
    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="Yalıhan Emlak" class="h-10 w-auto dark:hidden">
            <img src="{{ asset('images/logo.png') }}" alt="Yalıhan Emlak" class="h-10 w-auto hidden dark:block">
            <span class="text-white text-lg font-semibold tracking-wide drop-shadow-md">Yalıhan Emlak</span>
        </a>
        <nav class="hidden md:flex items-center gap-6 text-white font-medium text-sm">
            <a href="#featured" class="hover:text-amber-300 transition">Öne Çıkanlar</a>
            <a href="#yazlik" class="hover:text-amber-300 transition">Yazlıklar</a>
            <a href="#arsa" class="hover:text-amber-300 transition">Arsalar</a>
            <a href="#golden-visa" class="hover:text-amber-300 transition">Yurtdışı</a>
            <a href="{{ route('frontend.portfolio.index') }}" class="px-4 py-2 rounded-full bg-amber-500 text-slate-900 font-semibold shadow-md hover:bg-amber-400 transition">Portföy</a>
        </nav>
        <button class="md:hidden inline-flex items-center justify-center w-11 h-11 rounded-full bg-white/20 text-white backdrop-blur-sm border border-white/30">
            <span class="sr-only">Menü</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>
</header>

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

            @php
                $properties = $featuredProperties ?? collect();
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                @forelse($properties as $property)
                    <x-yaliihan.property-card :property="$property" />
                @empty
                    <div class="col-span-full flex flex-col items-center justify-center rounded-3xl border border-dashed border-blue-300 dark:border-blue-700/60 bg-white dark:bg-gray-900 p-10 text-center shadow-sm">
                        <div class="text-4xl mb-4">🏗️</div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Yakında Harika Portföyler Burada</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 max-w-md">Şu anda yayınlanmış portföy bulunmuyor. En güncel ilanlarımızı yakında burada görebilirsiniz.</p>
                    </div>
                @endforelse
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
                <div class="text-center p-8 bg-gray-50 dark:bg-gray-900/40 rounded-3xl border border-gray-200 dark:border-gray-700 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <div class="text-4xl font-black text-blue-600 dark:text-blue-400 mb-3">
                        {{ number_format($stats['active_listings'] ?? 0) }}+
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2 transition-colors duration-300">Aktif Portföy</h3>
                    <p class="text-gray-600 dark:text-gray-300 transition-colors duration-300">Güncel, doğrulanmış ve yayında olan ilanlarımızla hayalinizdeki evi keşfedin.</p>
                </div>
                <div class="text-center p-8 bg-gray-50 dark:bg-gray-900/40 rounded-3xl border border-gray-200 dark:border-gray-700 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <div class="text-4xl font-black text-blue-600 dark:text-blue-400 mb-3">
                        {{ $stats['experience_years'] ?? 0 }}+
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2 transition-colors duration-300">Yıllık Deneyim</h3>
                    <p class="text-gray-600 dark:text-gray-300 transition-colors duration-300">Bölgeyi bilen danışmanlarımızla süreç boyunca yanınızdayız.</p>
                </div>
                <div class="text-center p-8 bg-gray-50 dark:bg-gray-900/40 rounded-3xl border border-gray-200 dark:border-gray-700 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <div class="text-4xl font-black text-blue-600 dark:text-blue-400 mb-3">
                        {{ number_format($stats['happy_customers'] ?? 0) }}+
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2 transition-colors duration-300">Mutlu Müşteri</h3>
                    <p class="text-gray-600 dark:text-gray-300 transition-colors duration-300">Referanslarımızla büyüyen portföyümüz; güvenilir yatırım ortağınız.</p>
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
                <a href="tel:+905332090302"
                    class="inline-flex items-center justify-center bg-white text-blue-600 dark:bg-gray-800 dark:text-blue-400 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 dark:hover:bg-gray-700 active:scale-95 transition-all duration-200 shadow-lg hover:shadow-xl focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-600">
                    📞 Hemen Ara: <span class="font-bold">0533 209 03 02</span>
                </a>
                <a href="{{ url('/iletisim') }}"
                    class="inline-flex items-center justify-center border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 dark:hover:bg-gray-800 dark:hover:text-blue-400 active:scale-95 transition-all duration-200 focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-600">
                    📧 İletişim Formu
                </a>
            </div>
        </div>
    </section>

    <!-- Modal Containers -->
    <div id="virtualTour" class="fixed inset-0 hidden items-center justify-center z-50" role="dialog" aria-modal="true" aria-labelledby="virtualTourTitle">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('virtualTour')"></div>
        <div class="relative bg-white dark:bg-gray-900 rounded-3xl shadow-2xl max-w-3xl w-full mx-4 p-6 sm:p-8 overflow-y-auto max-h-[90vh]">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <h3 id="virtualTourTitle" class="text-2xl font-bold text-gray-900 dark:text-white">360° Sanal Tur</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Portföyün sanal turuna göz atın.</p>
                </div>
                <button type="button" class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition-colors" onclick="closeModal('virtualTour')" aria-label="Kapat">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="aspect-video rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800 relative">
                <iframe data-role="virtual-tour-iframe" title="Portföy Sanal Tur" loading="lazy" allowfullscreen class="w-full h-full hidden"></iframe>
                <div data-role="virtual-tour-placeholder" class="absolute inset-0 flex flex-col items-center justify-center gap-3 text-gray-500 dark:text-gray-400 text-sm">
                    <span class="text-4xl">🎥</span>
                    <p>Sanal tur içerikleri yakında eklenecek.</p>
                </div>
            </div>
        </div>
    </div>

    <div id="gallery" class="fixed inset-0 hidden items-center justify-center z-50" role="dialog" aria-modal="true" aria-labelledby="galleryTitle">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('gallery')"></div>
        <div class="relative bg-white dark:bg-gray-900 rounded-3xl shadow-2xl max-w-4xl w-full mx-4 p-6 sm:p-8 overflow-y-auto max-h-[90vh]">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <h3 id="galleryTitle" class="text-2xl font-bold text-gray-900 dark:text-white">Galeri</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Seçili portföye ait görseller.</p>
                </div>
                <button type="button" class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition-colors" onclick="closeModal('gallery')" aria-label="Kapat">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div data-role="gallery-grid" class="grid grid-cols-1 sm:grid-cols-2 gap-4"></div>
            <div data-role="gallery-placeholder" class="flex flex-col items-center justify-center gap-3 py-10 text-gray-500 dark:text-gray-400 text-sm">
                <span class="text-4xl">🖼️</span>
                <p>Bu portföy için henüz galeri görseli bulunamadı.</p>
            </div>
        </div>
    </div>

    <div id="map" class="fixed inset-0 hidden items-center justify-center z-50" role="dialog" aria-modal="true" aria-labelledby="mapTitle">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('map')"></div>
        <div class="relative bg-white dark:bg-gray-900 rounded-3xl shadow-2xl max-w-3xl w-full mx-4 p-6 sm:p-8 overflow-hidden">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <h3 id="mapTitle" class="text-2xl font-bold text-gray-900 dark:text-white">Lokasyon Haritası</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400" data-role="map-address">Portföyün bulunduğu konumu inceleyin.</p>
                </div>
                <button type="button" class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition-colors" onclick="closeModal('map')" aria-label="Kapat">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800">
                <iframe data-role="map-frame" title="Portföy Haritası" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="w-full h-72 hidden"></iframe>
                <div data-role="map-placeholder" class="flex flex-col items-center justify-center gap-3 py-10 text-gray-500 dark:text-gray-400 text-sm">
                    <span class="text-4xl">🗺️</span>
                    <p>Konum bilgisi bulunamadı. Lütfen danışmanımızla iletişime geçin.</p>
                </div>
            </div>
        </div>
    </div>

    <div id="propertyDetail" class="fixed inset-0 hidden items-center justify-center z-50" role="dialog" aria-modal="true" aria-labelledby="propertyDetailTitle">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('propertyDetail')"></div>
        <div class="relative bg-white dark:bg-gray-900 rounded-3xl shadow-2xl max-w-4xl w-full mx-4 p-6 sm:p-8 overflow-y-auto max-h-[90vh]">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <h3 id="propertyDetailTitle" class="text-2xl font-bold text-gray-900 dark:text-white">İlan Detayları</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Ayrıntılı bilgi için portföy sayfasına gidin.</p>
                </div>
                <button type="button" class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition-colors" onclick="closeModal('propertyDetail')" aria-label="Kapat">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="space-y-6 text-gray-700 dark:text-gray-300 text-sm leading-relaxed">
                <div>
                    <p data-role="detail-price" class="text-2xl font-semibold text-blue-600 dark:text-blue-400 mb-1"></p>
                    <p data-role="detail-location" class="text-sm text-gray-500 dark:text-gray-400">Lokasyon bilgisi yakında eklenecek.</p>
                </div>
                <p data-role="detail-description" class="text-base">Bu portföy için açıklama hazırlanıyor. Danışmanlarımız en kısa sürede içeriği güncelleyecek.</p>
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Öne Çıkan Özellikler</h4>
                    <ul data-role="detail-features" class="grid grid-cols-1 sm:grid-cols-2 gap-3"></ul>
                    <p data-role="detail-features-placeholder" class="text-sm text-gray-500 dark:text-gray-400">Öne çıkan özellikler yakında burada listelenecek.</p>
                </div>
                <a data-role="detail-link" href="{{ url('/portfolio') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all duration-200" target="_blank" rel="noopener">
                    Detay Sayfasına Git
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- JavaScript Functions -->
    <script>
        (function () {
            const BODY = document.body;

            const parsePayload = (payload) => {
                if (typeof payload === 'string') {
                    try {
                        return JSON.parse(payload);
                    } catch (error) {
                        return payload;
                    }
                }
                return payload;
            };

            const formatVirtualTourUrl = (url) => {
                if (!url || typeof url !== 'string') {
                    return '';
                }

                const trimmed = url.trim();
                if (trimmed.includes('watch?v=')) {
                    return trimmed.replace('watch?v=', 'embed/');
                }
                if (trimmed.includes('youtu.be/')) {
                    return trimmed.replace('youtu.be/', 'youtube.com/embed/');
                }
                return trimmed;
            };

            const showModal = (modal) => {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                modal.setAttribute('aria-hidden', 'false');
                BODY.classList.add('overflow-hidden');
            };

            const hideModal = (modal) => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
                modal.setAttribute('aria-hidden', 'true');

                const openModals = document.querySelectorAll('.fixed.inset-0.flex');
                if (openModals.length === 0) {
                    BODY.classList.remove('overflow-hidden');
                }
            };

            const resetVirtualTourModal = (modal) => {
                const iframe = modal.querySelector('[data-role="virtual-tour-iframe"]');
                const placeholder = modal.querySelector('[data-role="virtual-tour-placeholder"]');
                if (iframe) {
                    iframe.src = '';
                    iframe.classList.add('hidden');
                }
                placeholder?.classList.remove('hidden');
            };

            const resetGalleryModal = (modal) => {
                const grid = modal.querySelector('[data-role="gallery-grid"]');
                const placeholder = modal.querySelector('[data-role="gallery-placeholder"]');
                if (grid) {
                    grid.innerHTML = '';
                }
                placeholder?.classList.remove('hidden');
            };

            const resetMapModal = (modal) => {
                const frame = modal.querySelector('[data-role="map-frame"]');
                const placeholder = modal.querySelector('[data-role="map-placeholder"]');
                if (frame) {
                    frame.src = '';
                    frame.classList.add('hidden');
                }
                placeholder?.classList.remove('hidden');
            };

            const resetPropertyDetailModal = (modal) => {
                modal.querySelector('#propertyDetailTitle').textContent = 'İlan Detayları';
                const price = modal.querySelector('[data-role="detail-price"]');
                const location = modal.querySelector('[data-role="detail-location"]');
                const description = modal.querySelector('[data-role="detail-description"]');
                const features = modal.querySelector('[data-role="detail-features"]');
                const featuresPlaceholder = modal.querySelector('[data-role="detail-features-placeholder"]');
                const link = modal.querySelector('[data-role="detail-link"]');

                if (price) price.textContent = '';
                if (location) location.textContent = 'Lokasyon bilgisi yakında eklenecek.';
                if (description) description.textContent = 'Bu portföy için açıklama hazırlanıyor. Danışmanlarımız en kısa sürede içeriği güncelleyecek.';
                if (features) features.innerHTML = '';
                if (featuresPlaceholder) featuresPlaceholder.classList.remove('hidden');
                if (link) {
                    link.classList.remove('hidden');
                    link.setAttribute('href', '{{ url('/portfolio') }}');
                }
            };

            const updateVirtualTourModal = (modal, payload) => {
                const iframe = modal.querySelector('[data-role="virtual-tour-iframe"]');
                const placeholder = modal.querySelector('[data-role="virtual-tour-placeholder"]');
                const url = typeof payload === 'string' ? payload : payload?.url ?? '';
                const embedUrl = formatVirtualTourUrl(url);

                if (embedUrl && iframe) {
                    iframe.src = embedUrl;
                    iframe.classList.remove('hidden');
                    placeholder?.classList.add('hidden');
                } else {
                    placeholder?.classList.remove('hidden');
                    iframe?.classList.add('hidden');
                }
            };

            const updateGalleryModal = (modal, payload) => {
                const grid = modal.querySelector('[data-role="gallery-grid"]');
                const placeholder = modal.querySelector('[data-role="gallery-placeholder"]');
                const images = Array.isArray(payload) ? payload : Array.isArray(payload?.gallery) ? payload.gallery : [];

                if (!grid) {
                    return;
                }

                grid.innerHTML = '';

                if (images.length === 0) {
                    placeholder?.classList.remove('hidden');
                    return;
                }

                placeholder?.classList.add('hidden');

                images.forEach((image) => {
                    if (!image?.url) {
                        return;
                    }

                    const figure = document.createElement('figure');
                    figure.className = 'relative overflow-hidden rounded-2xl bg-gray-100 dark:bg-gray-800';

                    const img = document.createElement('img');
                    img.src = image.url;
                    img.alt = image.alt || 'Portföy görseli';
                    img.loading = 'lazy';
                    img.className = 'w-full h-full object-cover';

                    figure.appendChild(img);
                    grid.appendChild(figure);
                });
            };

            const updateMapModal = (modal, payload) => {
                const frame = modal.querySelector('[data-role="map-frame"]');
                const placeholder = modal.querySelector('[data-role="map-placeholder"]');
                const address = modal.querySelector('[data-role="map-address"]');

                const data = typeof payload === 'object' && !Array.isArray(payload) ? payload : {};
                const lat = data?.lat ?? data?.latitude;
                const lng = data?.lng ?? data?.longitude;
                const locationText = data?.content || data?.title || 'Konum bilgisi yakında eklenecek.';

                if (address) {
                    address.textContent = locationText;
                }

                if (lat && lng && frame) {
                    const src = `https://www.google.com/maps?q=${lat},${lng}&hl=tr&z=15&output=embed`;
                    frame.src = src;
                    frame.classList.remove('hidden');
                    placeholder?.classList.add('hidden');
                } else {
                    placeholder?.classList.remove('hidden');
                    frame?.classList.add('hidden');
                }
            };

            const updatePropertyDetailModal = (modal, payload) => {
                const data = typeof payload === 'object' && !Array.isArray(payload) ? payload : {};
                const title = data?.title || 'İlan Detayları';
                const price = modal.querySelector('[data-role="detail-price"]');
                const location = modal.querySelector('[data-role="detail-location"]');
                const description = modal.querySelector('[data-role="detail-description"]');
                const features = modal.querySelector('[data-role="detail-features"]');
                const featuresPlaceholder = modal.querySelector('[data-role="detail-features-placeholder"]');
                const link = modal.querySelector('[data-role="detail-link"]');

                modal.querySelector('#propertyDetailTitle').textContent = title;
                if (price) price.textContent = data?.price || '';
                if (location) location.textContent = data?.location || 'Lokasyon bilgisi yakında eklenecek.';
                if (description) description.textContent = data?.description || 'Bu portföy için açıklama hazırlanıyor. Danışmanlarımız en kısa sürede içeriği güncelleyecek.';

                if (features) {
                    features.innerHTML = '';
                    const featureList = Array.isArray(data?.features) ? data.features : [];
                    if (featureList.length === 0) {
                        featuresPlaceholder?.classList.remove('hidden');
                    } else {
                        featuresPlaceholder?.classList.add('hidden');
                        featureList.forEach((feature) => {
                            if (!feature?.label || !feature?.value) {
                                return;
                            }
                            const item = document.createElement('li');
                            item.className = 'rounded-2xl border border-gray-200 dark:border-gray-700 px-4 py-3 flex items-center justify-between text-sm';
                            item.innerHTML = `<span class="text-gray-500 dark:text-gray-400">${feature.label}</span><span class="font-semibold text-gray-900 dark:text-white">${feature.value}</span>`;
                            features.appendChild(item);
                        });
                    }
                }

                if (link) {
                    if (data?.link) {
                        link.setAttribute('href', data.link);
                        link.classList.remove('hidden');
                    } else {
                        link.classList.add('hidden');
                    }
                }
            };

            window.openYaliihanModal = function (modalId, payload) {
                const modal = document.getElementById(modalId);
                if (!modal) {
                    return;
                }

                const data = parsePayload(payload);

                switch (modalId) {
                    case 'virtualTour':
                        updateVirtualTourModal(modal, data);
                        break;
                    case 'gallery':
                        updateGalleryModal(modal, data);
                        break;
                    case 'map':
                        updateMapModal(modal, data);
                        break;
                    case 'propertyDetail':
                        updatePropertyDetailModal(modal, data);
                        break;
                }

                showModal(modal);
            };

            window.closeModal = function (modalId) {
                const modal = document.getElementById(modalId);
                if (!modal) {
                    return;
                }

                switch (modalId) {
                    case 'virtualTour':
                        resetVirtualTourModal(modal);
                        break;
                    case 'gallery':
                        resetGalleryModal(modal);
                        break;
                    case 'map':
                        resetMapModal(modal);
                        break;
                    case 'propertyDetail':
                        resetPropertyDetailModal(modal);
                        break;
                }

                hideModal(modal);
            };

            const showToast = (message, type = 'success') => {
                if (!message) {
                    return;
                }

                const toast = document.createElement('div');
                const background = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
                const icon = type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️';

                toast.className = `fixed top-4 right-4 ${background} text-white rounded-2xl p-4 shadow-2xl z-50 transform translate-x-full transition-transform duration-300 max-w-sm`;
                toast.innerHTML = `<div class="flex items-center"><span class="text-2xl mr-3">${icon}</span><span class="font-medium">${message}</span></div>`;

                    document.body.appendChild(toast);

                setTimeout(() => {
                        toast.classList.remove('translate-x-full');
                }, 100);

                setTimeout(() => {
                        toast.classList.add('translate-x-full');
                    setTimeout(() => {
                        if (document.body.contains(toast)) {
                            document.body.removeChild(toast);
                        }
                    }, 300);
                }, 3000);
            };

            window.showToast = showToast;

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    const activeModal = document.querySelector('.fixed.inset-0.flex');
                    if (activeModal?.id) {
                        window.closeModal(activeModal.id);
                    }
                }
            });

            document.addEventListener('DOMContentLoaded', () => {
                const anchorLinks = document.querySelectorAll('a[href^="#"]');
                anchorLinks.forEach((link) => {
                    link.addEventListener('click', (event) => {
                        const href = link.getAttribute('href');
                        if (!href || href.length <= 1) {
                            return;
                        }
                                const targetId = href.substring(1);
                        const target = document.getElementById(targetId);
                        if (!target) {
                            return;
                        }
                        event.preventDefault();
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    });
                });

                try {
                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach((entry) => {
                            if (entry.isIntersecting) {
                                    entry.target.classList.add('animate-fade-in');
                                observer.unobserve(entry.target);
                            }
                        });
                    }, {
                        threshold: 0.15,
                        rootMargin: '0px 0px -40px 0px'
                    });

                    document.querySelectorAll('.property-card').forEach((card) => observer.observe(card));
                } catch (error) {
                    console.error('IntersectionObserver error', error);
                }

            const style = document.createElement('style');
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
                    document.head.appendChild(style);
            });
        })();
    </script>
@endsection
