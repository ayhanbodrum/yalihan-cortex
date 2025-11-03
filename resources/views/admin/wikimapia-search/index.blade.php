@extends('admin.layouts.neo')

@section('content')
<div class="wikimapia-search-panel px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Wikimapia Site/Apartman Sorgulama</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">Wikimapia'dan site ve apartman bilgilerini sorgula</p>
    </div>

    <!-- Map Section -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">📍 Harita</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Haritaya tıklayarak konum seçin veya aşağıdaki koordinatları girin</p>
        <div id="map" style="height: 400px; width: 100%; border-radius: 12px;" class="mb-4"></div>
    </div>

    <!-- Search Section -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">🔍 Arama</h2>

        <form id="searchForm" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Site/Apartman Adı
                    </label>
                    <input type="text" id="searchQuery"
                        placeholder="Örn: Bahçeşehir Sitesi"
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 w-full">
                </div>
                                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Lat (Enlem)
                        </label>
                        <input type="number" step="0.000001" id="searchLat"
                            value="37.0345" placeholder="37.0345"
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 w-full">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Lon (Boylam)
                        </label>
                        <input type="number" step="0.000001" id="searchLon"
                            value="27.4305" placeholder="27.4305"
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 w-full">
                    </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                    Arama Yarıçapı (km)
                </label>
                <input type="range" id="searchRadius" min="0.01" max="1" step="0.01" value="0.05"
                    class="w-full"
                    oninput="document.getElementById('radiusValue').textContent = (this.value * 100).toFixed(0) + ' km'">
                <p class="text-sm text-gray-500 mt-1">
                    <span id="radiusValue">5 km</span> çevresinde ara
                </p>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="neo-btn neo-btn-primary">
                    <i class="fas fa-search mr-2"></i>
                    Site/Apartman Ara
                </button>
                <button type="button" onclick="searchNearby()" class="neo-btn neo-btn-secondary">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    Yakındakileri Göster
                </button>
            </div>
        </form>
    </div>

    <!-- Results Section -->
    <div id="resultsSection" class="hidden">
        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">📋 Sonuçlar</h2>
            <div id="resultsContainer" class="space-y-4"></div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-8 text-center">
            <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p class="text-gray-900 dark:text-white">Aranıyor...</p>
        </div>
    </div>

    <!-- Toast Messages -->
    <div id="successToast" class="hidden fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        ✅ Başarılı!
    </div>
    <div id="errorToast" class="hidden fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        ❌ Hata oluştu!
    </div>
</div>

<script>
document.getElementById('searchForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const query = document.getElementById('searchQuery').value;
    const lat = parseFloat(document.getElementById('searchLat').value);
    const lon = parseFloat(document.getElementById('searchLon').value);
    const radius = parseFloat(document.getElementById('searchRadius').value);

    showLoading(true);

    try {
        const response = await fetch('{{ route("admin.wikimapia-search.search") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ query, lat, lon, radius })
        });

        // Response'u kontrol et
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Response error:', errorText);
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const contentType = response.headers.get('content-type');
        console.log('Response content-type:', contentType);

        const data = await response.json();
        console.log('Response data:', data);

        if (data.success) {
            displayResults(data.data);
            showToast('success');
        } else {
            showToast('error');
        }
    } catch (error) {
        console.error('Search error:', error);
        alert('Arama hatası: ' + error.message);
        showToast('error');
    } finally {
        showLoading(false);
    }
});

async function searchNearby() {
    const lat = parseFloat(document.getElementById('searchLat').value);
    const lon = parseFloat(document.getElementById('searchLon').value);
    const radius = parseFloat(document.getElementById('searchRadius').value);

    showLoading(true);

    try {
        const response = await fetch('{{ route("admin.wikimapia-search.nearby") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ lat, lon, radius })
        });

        const data = await response.json();

        if (data.success) {
            displayResults(data.data);
            showToast('success');
        } else {
            showToast('error');
        }
    } catch (error) {
        console.error('Nearby search error:', error);
        showToast('error');
    } finally {
        showLoading(false);
    }
}

function displayResults(data) {
    const container = document.getElementById('resultsContainer');
    const section = document.getElementById('resultsSection');

    container.innerHTML = '';

    if (!data || !data.places || data.places.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center py-8">Sonuç bulunamadı</p>';
        section.classList.remove('hidden');
        return;
    }

    data.places.forEach((place, index) => {
        const placeCard = document.createElement('div');
        placeCard.className = 'bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-600 hover:border-blue-500 transition-colors';
        placeCard.innerHTML = `
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        ${place.title || 'İsimsiz'}
                    </h3>
                    ${place.description ? `<p class="text-sm text-gray-600 dark:text-gray-400 mb-2">${place.description.substring(0, 200)}...</p>` : ''}
                    ${place.location ? `
                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            Lat: ${place.location.latitude || 'N/A'}, Lon: ${place.location.longitude || 'N/A'}
                        </div>
                    ` : ''}
                    <div class="flex gap-2 mt-3">
                        ${place.url ? `<a href="${place.url}" target="_blank" class="text-xs px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded hover:bg-blue-200 dark:hover:bg-blue-800">
                            <i class="fas fa-external-link-alt mr-1"></i> Wikimapia
                        </a>` : ''}
                        <button onclick="selectSite(${index})" class="text-xs px-3 py-1 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded hover:bg-green-200 dark:hover:bg-green-800">
                            <i class="fas fa-check mr-1"></i> Seç
                        </button>
                    </div>
                </div>
            </div>
        `;
        placeCard.setAttribute('data-place', JSON.stringify(place));
        container.appendChild(placeCard);
    });

    section.classList.remove('hidden');
}

function showLoading(show) {
    const overlay = document.getElementById('loadingOverlay');
    if (show) {
        overlay.classList.remove('hidden');
    } else {
        overlay.classList.add('hidden');
    }
}

function showToast(type) {
    const toast = document.getElementById(type === 'success' ? 'successToast' : 'errorToast');
    toast.classList.remove('hidden');

    setTimeout(() => {
        toast.classList.add('hidden');
    }, 3000);
}

function selectSite(index) {
    const cards = document.querySelectorAll('#resultsContainer > div');
    const selectedCard = cards[index];

    if (!selectedCard) return;

    const placeData = JSON.parse(selectedCard.getAttribute('data-place'));

    // Kullanıcıya seçimi göster
    alert(`✅ Site Seçildi!\n\nAd: ${placeData.title}\nKoordinatlar: ${placeData.location?.latitude || 'N/A'}, ${placeData.location?.longitude || 'N/A'}\n\nBu site seçildi ve panonuza eklendi.`);

    // İşte burada siteyi kaydedebilirsiniz
    console.log('Seçilen site:', placeData);

    // Örneğin: LocalStorage'a kaydet veya API'ye gönder
    // saveSelectedSite(placeData);

    // Veya modal aç ve detayları göster
    // showSiteDetailModal(placeData);
}

// Gelecekte kullanılmak üzere site kaydetme fonksiyonu
function saveSelectedSite(placeData) {
    // API'ye gönder veya localStorage'a kaydet
    localStorage.setItem('selectedSite', JSON.stringify(placeData));
    console.log('Site kaydedildi:', placeData);
}

// Site detay modal'ı (isteğe bağlı)
function showSiteDetailModal(placeData) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center';
    modal.innerHTML = `
        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6 max-w-md w-full">
            <h2 class="text-2xl font-bold mb-4">${placeData.title}</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-4">${placeData.description || 'Açıklama yok'}</p>
            <div class="flex gap-3">
                <button onclick="this.closest('.fixed').remove()" class="flex-1 neo-btn neo-btn-secondary">Kapat</button>
                <button onclick="saveSelectedSite(${JSON.stringify(placeData).replace(/"/g, '&quot;')})" class="flex-1 neo-btn neo-btn-primary">Kaydet</button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}
</script>
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""/>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""></script>

<script>
// Harita başlatma
let map, marker;

document.addEventListener('DOMContentLoaded', function() {
    // Varsayılan koordinatlar (Bodrum)
    const defaultLat = 37.0345;
    const defaultLng = 27.4305;

    // Harita oluştur
    map = L.map('map').setView([defaultLat, defaultLng], 12);

    // Tile layer ekle
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);

    // Başlangıç marker'ı
    marker = L.marker([defaultLat, defaultLng], {
        draggable: true
    }).addTo(map);

    // Input değerlerini güncelle
    updateInputs(defaultLat, defaultLng);

    // Marker sürükleme olayı
    marker.on('dragend', function(e) {
        const position = marker.getLatLng();
        updateInputs(position.lat, position.lng);
    });

    // Harita tıklama olayı - Yakındaki siteleri otomatik getir
    map.on('click', async function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        // Marker'ı yeni konuma taşı
        marker.setLatLng([lat, lng]);

        // Input'ları güncelle
        updateInputs(lat, lng);

        // Otomatik olarak yakındaki siteleri getir
        console.log('📍 Harita tıklandı:', lat, lng);
        await searchNearbyPlaces(lat, lng);
    });
});

function updateInputs(lat, lng) {
    document.getElementById('searchLat').value = lat.toFixed(6);
    document.getElementById('searchLon').value = lng.toFixed(6);
}

async function searchNearbyPlaces(lat, lng) {
    console.log('🔍 Yakındaki yerleri arıyor:', { lat, lng });

    showLoading(true);
    const radius = parseFloat(document.getElementById('searchRadius').value);

    try {
        const requestBody = { lat, lon: lng, radius };
        console.log('📤 Gönderilen istek:', requestBody);

        const response = await fetch('{{ route("admin.wikimapia-search.nearby") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(requestBody)
        });

        console.log('📥 Response status:', response.status);

        if (!response.ok) {
            const errorText = await response.text();
            console.error('❌ Response error:', errorText);
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log('✅ Response data:', data);
        console.log('✅ Response data.places:', data.data?.places);
        console.log('✅ Response data.count:', data.data?.count);
        if (data.data?.places && data.data.places.length > 0) {
            console.log('✅ İlk place örneği:', data.data.places[0]);
        }

        if (data.success) {
            displayResults(data.data);
            showToast('success');

            // Sonuçları harita üzerinde göster
            displayOnMap(data.data);
        } else {
            console.error('❌ Success false:', data.message);
            showToast('error');
        }
    } catch (error) {
        console.error('❌ Nearby places search error:', error);
        showToast('error');
    } finally {
        showLoading(false);
    }
}

function displayOnMap(data) {
    // Önceki marker'ları temizle (sadece ana marker kalsın)

    if (!data || !data.places) return;

    // Önemli yerleri haritada göster
    data.places.slice(0, 20).forEach(place => { // İlk 20 sonucu göster
        if (place.location && place.location.latitude && place.location.longitude) {
            L.marker([place.location.latitude, place.location.longitude])
                .addTo(map)
                .bindPopup(
                    `<div>
                        <h3 class="font-semibold">${place.title || 'İsimsiz'}</h3>
                        <p class="text-sm text-gray-600">${place.url ? `<a href="${place.url}" target="_blank" class="text-blue-500">Detay</a>` : ''}</p>
                    </div>`
                );
        }
    });

    // Haritayı tüm marker'ları gösterecek şekilde zoom yap
    if (data.places.length > 0) {
        const bounds = L.latLngBounds(
            data.places
                .filter(p => p.location && p.location.latitude && p.location.longitude)
                .map(p => [p.location.latitude, p.location.longitude])
        );
        if (bounds.isValid()) {
            map.fitBounds(bounds, { padding: [50, 50] });
        }
    }
}
</script>
@endpush
@endsection
