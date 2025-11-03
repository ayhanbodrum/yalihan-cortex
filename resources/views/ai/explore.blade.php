@extends('admin.layouts.unified')

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
