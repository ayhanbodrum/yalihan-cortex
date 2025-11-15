@extends('layouts.frontend')

@section('title', 'AI Portföy Keşfi - Yalıhan Emlak')

@section('content')
    <div class="bg-gray-50 dark:bg-gray-900 min-h-screen">
        {{-- Hero Section --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 dark:from-blue-700 dark:via-indigo-700 dark:to-purple-700">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28">
                <div class="flex flex-col lg:flex-row items-center gap-10">
                    <div class="flex-1 text-center lg:text-left">
                        <span class="inline-flex items-center px-4 py-1.5 text-sm font-semibold text-white bg-white/10 backdrop-blur rounded-full mb-5">
                            <i class="fas fa-robot mr-2"></i>Yapay Zeka Destekli Analiz
                        </span>
                        <h1 class="text-4xl sm:text-5xl font-bold text-white mb-6">
                            AI Portföy Keşfi ile yeni yatırım fırsatlarını keşfedin
                        </h1>
                        <p class="text-lg sm:text-xl text-white/90 mb-8 max-w-2xl">
                            Yalıhan Emlak yapay zeka motoru, taleplerinizi ve hedeflerinizi analiz ederek size özel gayrimenkul portföyleri oluşturur. Farklı senaryoları deneyimleyin, yatırımlarınızı optimize edin.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="#explore"
                               class="inline-flex items-center justify-center px-6 py-3 bg-white text-blue-700 font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-blue-300">
                                <i class="fas fa-compass mr-2"></i>Keşfetmeye Başla
                            </a>
                            <a href="#how-it-works"
                               class="inline-flex items-center justify-center px-6 py-3 border border-white/70 text-white font-semibold rounded-lg hover:bg-white/10 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-white/40">
                                Nasıl Çalışır?
                            </a>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl p-6 sm:p-8">
                            <h2 class="text-xl font-semibold text-white mb-4">Hızlı Analiz Parametreleri</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="bg-white/10 rounded-2xl p-4">
                                    <p class="text-sm text-white/80 mb-1">Bütçe Aralığı</p>
                                    <p class="text-lg font-semibold text-white">3.000.000 ₺ - 12.000.000 ₺</p>
                                </div>
                                <div class="bg-white/10 rounded-2xl p-4">
                                    <p class="text-sm text-white/80 mb-1">Tercih Edilen Lokasyon</p>
                                    <p class="text-lg font-semibold text-white">İstanbul, İzmir, Muğla</p>
                                </div>
                                <div class="bg-white/10 rounded-2xl p-4">
                                    <p class="text-sm text-white/80 mb-1">Yatırım Amacı</p>
                                    <p class="text-lg font-semibold text-white">Uzun Vadeli Kiralama</p>
                                </div>
                                <div class="bg-white/10 rounded-2xl p-4">
                                    <p class="text-sm text-white/80 mb-1">Risk Profili</p>
                                    <p class="text-lg font-semibold text-white">Dengeli</p>
                                </div>
                            </div>
                            <div class="mt-6 text-sm text-white/70">
                                <i class="fas fa-info-circle mr-2"></i>Parametreleri değiştirerek yapay zekanın yeni kombinasyonlar önermesini sağlayabilirsiniz.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- How it works --}}
        <section id="how-it-works" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Süreç Nasıl İşliyor?</h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 mt-3 max-w-2xl mx-auto">
                    AI Portföy Keşfi sistemi; talebinizi anlamak, alternatifleri değerlendirmek ve en uygun portföyü önermek için 3 adımlı bir süreç izler.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 shadow-sm hover:shadow-lg transition-all duration-200">
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 font-semibold mb-5">1</span>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Talebi Anla</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Yatırım hedeflerinizi, bütçe aralıklarınızı ve tercih ettiğiniz lokasyonları sisteme tanımlayın. Yapay zeka motoru talebinizi ayrıntılı olarak analiz eder.
                    </p>
                </div>
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 shadow-sm hover:shadow-lg transition-all duration-200">
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 font-semibold mb-5">2</span>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Alternatifleri Skorla</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Sistem; portföy havuzundaki ilanları risk, getiri, likidite ve büyüme potansiyeli gibi metriklere göre skorlayarak en uygun eşleşmeleri belirler.
                    </p>
                </div>
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 shadow-sm hover:shadow-lg transition-all duration-200">
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 font-semibold mb-5">3</span>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Portföyü Karşılaştır</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Seçilen portföyleri yan yana karşılaştırın, danışmanlarımızdan canlı destek alın ve yatırım planınızı optimize edin.
                    </p>
                </div>
            </div>
        </section>

        {{-- Explore Section --}}
        <section id="explore" class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="flex flex-col lg:flex-row gap-8 items-start">
                    <div class="lg:w-1/3 space-y-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Senaryonu Oluştur</h2>
                        <p class="text-gray-600 dark:text-gray-400">
                            Farklı yatırım senaryolarını simüle ederek, yapay zekanın önerdiği portföyleri canlı olarak takip edebilirsiniz. Filtreleri güncelledikçe önerileriniz gerçek zamanlı yenilenir.
                        </p>
                        <ul class="space-y-3 text-gray-700 dark:text-gray-300">
                            <li class="flex items-start gap-3">
                                <span class="mt-1 text-blue-500"><i class="fas fa-check-circle"></i></span>
                                <span>Bütçe, lokasyon, yatırım türü ve risk profili gibi parametreleri düzenleyin.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="mt-1 text-blue-500"><i class="fas fa-check-circle"></i></span>
                                <span>AI motoru ile eşleşen portföyler için tahmini getiri ve risk skorlarını inceleyin.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="mt-1 text-blue-500"><i class="fas fa-check-circle"></i></span>
                                <span>Danışmanlarımızla paylaşarak kişiselleştirilmiş yatırım stratejisi oluşturun.</span>
                            </li>
                        </ul>
                    </div>
                    <div class="lg:flex-1 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm p-6">
                        <form action="#" method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="budget-min" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Minimum Bütçe</label>
                                <input type="number" id="budget-min" name="budget_min" min="0"
                                       class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200"
                                       placeholder="2.000.000">
                            </div>
                            <div>
                                <label for="budget-max" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Maksimum Bütçe</label>
                                <input type="number" id="budget-max" name="budget_max" min="0"
                                       class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200"
                                       placeholder="15.000.000">
                            </div>
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tercih Edilen Şehir</label>
                                <select id="city" name="city" style="color-scheme: light dark;"
                                        class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
                                    <option value="">Tüm Şehirler</option>
                                    <option value="istanbul">İstanbul</option>
                                    <option value="izmir">İzmir</option>
                                    <option value="antalya">Antalya</option>
                                    <option value="muğla">Muğla</option>
                                </select>
                            </div>
                            <div>
                                <label for="scenario" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Yatırım Senaryosu</label>
                                <select id="scenario" name="scenario" style="color-scheme: light dark;"
                                        class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
                                    <option value="premium">Premium Konut Portföyü</option>
                                    <option value="kiralama">Kiralama Getirisi Odaklı</option>
                                    <option value="degerlenme">Değerlenme Potansiyelli</option>
                                    <option value="karma">Karma Portföy</option>
                                </select>
                            </div>
                            <div>
                                <label for="risk" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Risk Seviyesi</label>
                                <select id="risk" name="risk" style="color-scheme: light dark;"
                                        class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
                                    <option value="dusuk">Düşük</option>
                                    <option value="orta">Orta</option>
                                    <option value="yuksek">Yüksek</option>
                                </select>
                            </div>
                            <div>
                                <label for="timeline" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Zaman Ufku</label>
                                <select id="timeline" name="timeline" style="color-scheme: light dark;"
                                        class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
                                    <option value="kisa">Kısa Vadeli (0-3 yıl)</option>
                                    <option value="orta">Orta Vadeli (3-7 yıl)</option>
                                    <option value="uzun">Uzun Vadeli (7+ yıl)</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notlar & Beklentiler</label>
                                <textarea id="notes" name="notes" rows="3"
                                          class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200"
                                          placeholder="Örn: Deniz manzaralı premium projeler, kiralama gelirine odaklı, yerli müşterilere uygun olsun."></textarea>
                            </div>
                            <div class="md:col-span-2 flex flex-col sm:flex-row gap-3">
                                <button type="submit"
                                        class="inline-flex items-center justify-center w-full sm:w-auto px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    <i class="fas fa-magic mr-2"></i>AI Analizi Başlat
                                </button>
                                <button type="button"
                                        class="inline-flex items-center justify-center w-full sm:w-auto px-6 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 font-semibold rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    <i class="fas fa-undo mr-2"></i>Parametreleri Sıfırla
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        {{-- Call to Action --}}
        <section class="bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-700 dark:to-indigo-700">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div>
                        <h2 class="text-3xl font-bold text-white mb-3">Danışmanlarımızdan destek alın</h2>
                        <p class="text-white/90 text-lg">
                            AI önerilerini gerçek dünyadaki portföylerle eşleştirmek için uzman gayrimenkul danışmanlarımızla iletişime geçin. Stratejinizi birlikte şekillendirelim.
                        </p>
                    </div>
                    <a href="{{ route('frontend.danismanlar.index') }}"
                       class="inline-flex items-center justify-center px-6 py-3 bg-white text-blue-700 font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-white">
                        <i class="fas fa-user-tie mr-2"></i>Danışmanları Gör
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection
@extends('layouts.frontend')

@section('content')
    <div class="px-4 py-6">
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-semibold">Öne Çıkan Bölgeler</h1>
            <p class="text-gray-600">Hızlıca bölge seçin veya aşağıdaki filtrelerle aramayı daraltın.</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
            @foreach ($featured ?? [] as $f)
                <a href="{{ $f['href'] ?? '#' }}"
                    class="rounded-lg overflow-hidden border bg-white shadow hover:shadow-md transition">
                    <div class="h-28 bg-cover bg-center"
                        style="background-image:url('{{ $f['img'] ?? '/images/featured/placeholder.jpg' }}')"></div>
                    <div class="p-3">
                        <div class="font-medium">{{ $f['title'] }}</div>
                        <div class="text-sm text-gray-500">{{ $f['count'] }} ilan</div>
                    </div>
                </a>
            @endforeach
        </div>

        <div id="stickyFilters" class="sticky top-0 z-10 bg-white/80 backdrop-blur border-b py-3 mb-6">
            <div class="flex flex-wrap gap-2 items-center">
                <select id="ilanTuru" class="border rounded px-4 py-2.5">
                    <option value="satilik">Satılık</option>
                    <option value="kiralik">Kiralık</option>
                </select>
                <input id="minFiyat" class="border rounded px-4 py-2.5 w-36" placeholder="Min ₺" type="number" />
                <input id="maxFiyat" class="border rounded px-4 py-2.5 w-36" placeholder="Max ₺" type="number" />
                <select id="odaSayisi" class="border rounded px-4 py-2.5">
                    <option value="">Oda</option>
                    <option value="1+0">1+0</option>
                    <option value="2+1">2+1</option>
                    <option value="3+1">3+1</option>
                    <option value="4+1">4+1</option>
                    <option value="5+1">5+1</option>
                </select>
                <div class="flex items-center gap-1">
                    <span class="text-sm text-gray-600">Denize</span>
                    <button class="distance-btn px-2 py-1 border rounded text-sm" data-distance="0.5">0.5km</button>
                    <button class="distance-btn px-2 py-1 border rounded text-sm" data-distance="1">1km</button>
                    <button class="distance-btn px-2 py-1 border rounded text-sm" data-distance="2">2km</button>
                </div>
                <div class="relative">
                    <input id="locationInput" class="border rounded px-4 py-2.5 w-64"
                        placeholder="İl, ilçe veya mahalle ara..." />
                    <div id="locationSuggestions"
                        class="absolute top-full left-0 right-0 bg-white border rounded-b shadow-lg z-20 hidden"></div>
                </div>
            </div>
        </div>

        <!-- Map and Listings Container -->
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Map Section -->
            <div class="lg:w-1/2">
                <div class="sticky top-20">
                    <div class="bg-white border rounded-lg p-4 mb-4">
                        <h3 class="font-semibold mb-2">Harita Görünümü</h3>
                        <div id="mapContainer" class="h-96 bg-gray-200 rounded-lg flex items-center justify-center">
                            <div class="text-center text-gray-500">
                                <div class="text-4xl mb-2">🗺️</div>
                                <div>Harita yükleniyor...</div>
                            </div>
                        </div>
                        <div class="mt-2 flex gap-2">
                            <button id="toggleMap" class="px-3 py-1 bg-orange-500 text-white rounded text-sm">
                                Liste Görünümü
                            </button>
                            <button id="centerMap" class="px-3 py-1 border rounded text-sm">
                                Merkeze Al
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Listings Section -->
            <div class="lg:w-1/2">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold">İlanlar</h3>
                    <div class="flex gap-2">
                        <button id="sortNewest" class="px-3 py-1 border rounded text-sm sort-btn active">En Yeni</button>
                        <button id="sortPrice" class="px-3 py-1 border rounded text-sm sort-btn">Fiyat</button>
                        <button id="sortPopular" class="px-3 py-1 border rounded text-sm sort-btn">Popüler</button>
                    </div>
                </div>

                <div id="listingsContainer" class="space-y-4">
                    @for ($i = 0; $i < 6; $i++)
                        <div class="rounded-lg overflow-hidden bg-white border shadow-sm hover:shadow-md transition">
                            <div class="h-48 bg-gray-200"></div>
                            <div class="p-4">
                                <div class="flex justify-between items-center mb-1">
                                    <div class="font-semibold">Örnek İlan Başlığı</div>
                                    <div class="text-orange-600 font-bold">₺ 5.250.000</div>
                                </div>
                                <div class="text-sm text-gray-600">3+1 • 160 m² • Yalıkavak</div>
                                <div class="flex gap-2 mt-2">
                                    <button class="px-2 py-1 bg-gray-100 rounded text-xs">Favori</button>
                                    <button class="px-2 py-1 bg-gray-100 rounded text-xs">Karşılaştır</button>
                                    <button class="px-2 py-1 bg-gray-100 rounded text-xs">Paylaş</button>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <div id="aiDock" class="fixed bottom-4 right-4">
        <div class="bg-white border shadow-lg rounded-xl w-80 overflow-hidden">
            <div class="px-4 py-2.5 font-medium border-b">YalihanAI</div>
            <div class="p-3">
                <input id="aiInput" class="w-full border rounded px-4 py-2.5"
                    placeholder="Örn: 6M bütçe, Yalıkavak 3+1 denize 1km" />
                <button id="aiSend" class="mt-2 w-full bg-orange-500 text-white rounded px-4 py-2.5">Ara</button>
                <pre id="aiOut" class="mt-3 text-xs bg-gray-50 p-2 border rounded overflow-auto max-h-40"></pre>
            </div>
        </div>
    </div>

    <script>
        // Global state
        let currentFilters = {
            ilan_turu: 'satilik',
            min_fiyat: '',
            max_fiyat: '',
            rooms: '',
            location: '',
            distance_km: '',
            center_lat: '',
            center_lng: '',
            ne_lat: '',
            ne_lng: '',
            sw_lat: '',
            sw_lng: ''
        };

        // Debounce function
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Location autocomplete
        const locationInput = document.getElementById('locationInput');
        const locationSuggestions = document.getElementById('locationSuggestions');
        let locationTimeout;

        locationInput.addEventListener('input', debounce(async (e) => {
            const query = e.target.value.trim();
            if (query.length < 2) {
                locationSuggestions.classList.add('hidden');
                return;
            }

            try {
                const response = await fetch(`/api/locations/search?q=${encodeURIComponent(query)}`);
                const data = await response.json();

                if (data.status === 'success' && data.data.length > 0) {
                    locationSuggestions.innerHTML = data.data.map(item => `
                        <div class="px-4 py-2.5 hover:bg-gray-100 cursor-pointer border-b last:border-b-0"
                             data-location='${JSON.stringify(item)}'>
                            ${item.text}
                        </div>
                    `).join('');
                    locationSuggestions.classList.remove('hidden');
                } else {
                    locationSuggestions.classList.add('hidden');
                }
            } catch (error) {
                console.error('Location search error:', error);
                locationSuggestions.classList.add('hidden');
            }
        }, 300));

        // Location suggestion click
        locationSuggestions.addEventListener('click', (e) => {
            const item = e.target.closest('[data-location]');
            if (item) {
                const location = JSON.parse(item.dataset.location);
                locationInput.value = location.text;
                currentFilters.location = location.text;
                currentFilters.center_lat = location.lat;
                currentFilters.center_lng = location.lng;
                locationSuggestions.classList.add('hidden');
                updateURLAndSearch();
            }
        });

        // Hide suggestions when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#locationInput') && !e.target.closest('#locationSuggestions')) {
                locationSuggestions.classList.add('hidden');
            }
        });

        // Filter change handlers
        document.getElementById('ilanTuru').addEventListener('change', (e) => {
            currentFilters.ilan_turu = e.target.value;
            updateURLAndSearch();
        });

        document.getElementById('minFiyat').addEventListener('input', debounce((e) => {
            currentFilters.min_fiyat = e.target.value;
            updateURLAndSearch();
        }, 600));

        document.getElementById('maxFiyat').addEventListener('input', debounce((e) => {
            currentFilters.max_fiyat = e.target.value;
            updateURLAndSearch();
        }, 600));

        document.getElementById('odaSayisi').addEventListener('change', (e) => {
            currentFilters.rooms = e.target.value;
            updateURLAndSearch();
        });

        // Distance buttons
        document.querySelectorAll('.distance-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                // Remove active class from all buttons
                document.querySelectorAll('.distance-btn').forEach(b => b.classList.remove('bg-orange-500',
                    'text-white'));
                // Add active class to clicked button
                e.target.classList.add('bg-orange-500', 'text-white');
                currentFilters.distance_km = e.target.dataset.distance;
                updateURLAndSearch();
            });
        });

        // Update URL and search
        function updateURLAndSearch() {
            const params = new URLSearchParams();
            Object.keys(currentFilters).forEach(key => {
                if (currentFilters[key]) {
                    params.set(key, currentFilters[key]);
                }
            });

            // Update URL without page reload
            const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
            window.history.pushState({}, '', newUrl);

            // Trigger search (this would normally update the listings)
            console.log('Search with filters:', currentFilters);
        }

        // AI Chat functionality
        document.getElementById('aiSend').addEventListener('click', async () => {
            const q = document.getElementById('aiInput').value;
            const res = await fetch('/api/ai/parse', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    q
                })
            });
            const data = await res.json();
            document.getElementById('aiOut').textContent = JSON.stringify(data, null, 2);

            if (data && data.ok && data.filters) {
                const f = data.filters;

                // Update form fields
                if (f.price_min) {
                    document.getElementById('minFiyat').value = f.price_min;
                    currentFilters.min_fiyat = f.price_min;
                }
                if (f.price_max) {
                    document.getElementById('maxFiyat').value = f.price_max;
                    currentFilters.max_fiyat = f.price_max;
                }
                if (f.rooms) {
                    document.getElementById('odaSayisi').value = f.rooms;
                    currentFilters.rooms = f.rooms;
                }
                if (f.distance_km) {
                    document.querySelector(`[data-distance="${f.distance_km}"]`).click();
                }
                if (Array.isArray(f.districts) && f.districts.length) {
                    locationInput.value = f.districts[0];
                    currentFilters.location = f.districts[0];
                }

                updateURLAndSearch();
            }
        });

        // Initialize from URL parameters
        function initializeFromURL() {
            const urlParams = new URLSearchParams(window.location.search);
            Object.keys(currentFilters).forEach(key => {
                if (urlParams.has(key)) {
                    currentFilters[key] = urlParams.get(key);
                }
            });

            // Update form fields
            document.getElementById('ilanTuru').value = currentFilters.ilan_turu;
            document.getElementById('minFiyat').value = currentFilters.min_fiyat;
            document.getElementById('maxFiyat').value = currentFilters.max_fiyat;
            document.getElementById('odaSayisi').value = currentFilters.rooms;
            locationInput.value = currentFilters.location;

            if (currentFilters.distance_km) {
                const btn = document.querySelector(`[data-distance="${currentFilters.distance_km}"]`);
                if (btn) btn.classList.add('bg-orange-500', 'text-white');
            }
        }

        // Initialize on page load
        initializeFromURL();

        // Map functionality
        let map = null;
        let mapMarkers = [];
        let isMapVisible = true;

        // Initialize map
        function initializeMap() {
            // This would normally initialize a real map (Google Maps, Leaflet, etc.)
            // For now, we'll simulate map functionality
            console.log('Map initialized');

            // Simulate map bounds change
            document.getElementById('centerMap').addEventListener('click', () => {
                console.log('Centering map...');
                // This would normally center the map on current location or search results
            });
        }

        // Toggle map visibility
        document.getElementById('toggleMap').addEventListener('click', () => {
            const mapContainer = document.getElementById('mapContainer').parentElement;
            const listingsContainer = document.getElementById('listingsContainer').parentElement;

            if (isMapVisible) {
                mapContainer.classList.add('hidden');
                listingsContainer.classList.remove('lg:w-1/2');
                listingsContainer.classList.add('w-full');
                document.getElementById('toggleMap').textContent = 'Harita Görünümü';
            } else {
                mapContainer.classList.remove('hidden');
                listingsContainer.classList.remove('w-full');
                listingsContainer.classList.add('lg:w-1/2');
                document.getElementById('toggleMap').textContent = 'Liste Görünümü';
            }
            isMapVisible = !isMapVisible;
        });

        // Sort functionality
        document.querySelectorAll('.sort-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                // Remove active class from all buttons
                document.querySelectorAll('.sort-btn').forEach(b => b.classList.remove('active',
                    'bg-orange-500', 'text-white'));
                // Add active class to clicked button
                e.target.classList.add('active', 'bg-orange-500', 'text-white');

                const sortType = e.target.id.replace('sort', '').toLowerCase();
                console.log('Sorting by:', sortType);
                // This would normally trigger a new search with sort parameter
            });
        });

        // Simulate map bounds change for testing
        function simulateMapBoundsChange() {
            // This would normally be triggered by map drag/zoom events
            const bounds = {
                ne_lat: 37.1,
                ne_lng: 27.5,
                sw_lat: 37.0,
                sw_lng: 27.4
            };

            currentFilters.ne_lat = bounds.ne_lat;
            currentFilters.ne_lng = bounds.ne_lng;
            currentFilters.sw_lat = bounds.sw_lat;
            currentFilters.sw_lng = bounds.sw_lng;

            updateURLAndSearch();
        }

        // Initialize map on page load
        initializeMap();
    </script>
@endsection
