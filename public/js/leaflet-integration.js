/**
 * Leaflet.js OpenStreetMap Entegrasyonu
 * Google Maps yerine açık kaynak alternatif
 * Context7 uyumlu nearby search ile
 */

class LeafletMapManager {
    constructor(containerId = 'map') {
        this.containerId = containerId;
        this.map = null;
        this.markers = [];
        this.currentLocationMarker = null;
        this.nearbyMarkers = [];

        // Context7: Nominatim rate limiting (min 1.2s between calls) + backoff
        this.nominatim = {
            lastCall: 0,
            minDelay: 1200,
            maxRetries: 3,
        };

        this.init();
    }

    /**
     * Haritayı başlat
     */
    init() {
        if (!document.getElementById(this.containerId)) {
            console.error(`Map container '${this.containerId}' not found`);
            return;
        }

        // Context7: Varsayılan merkez/zoom meta etiketlerinden
        const latMeta = document.querySelector('meta[name="location-default-latitude"]');
        const lngMeta = document.querySelector('meta[name="location-default-longitude"]');
        const zoomMeta = document.querySelector('meta[name="location-default-zoom"]');
        const center =
            latMeta && lngMeta
                ? [parseFloat(latMeta.content), parseFloat(lngMeta.content)]
                : [41.0082, 28.9784];
        const zoom = zoomMeta ? parseInt(zoomMeta.content) : 13;

        this.map = L.map(this.containerId).setView(center, zoom);

        // OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(this.map);

        // Harita tıklama eventi
        this.map.on('click', (e) => {
            this.onMapClick(e.latlng);
        });

        // Konum butonu ekle
        this.addLocationControl();

        this.drawAdded = false;
        const tryAddDraw = () => {
            if (window.L && window.L.Control && window.L.Control.Draw && this.map && !this.drawAdded) {
                const drawControl = new window.L.Control.Draw({
                    draw: { polyline: true, polygon: true, rectangle: true, circle: false, marker: true },
                    edit: { featureGroup: new window.L.FeatureGroup().addTo(this.map) },
                });
                this.map.addControl(drawControl);
                this.drawAdded = true;
            }
        };
        tryAddDraw();
        const drawTimer = setInterval(() => {
            if (this.drawAdded) { clearInterval(drawTimer); return; }
            tryAddDraw();
        }, 300);
    }

    /**
     * Harita tıklama olayı
     */
    onMapClick(latlng) {
        this.setMarker(latlng.lat, latlng.lng);
        this.updateLocationInputs(latlng.lat, latlng.lng);
        this.reverseGeocode(latlng.lat, latlng.lng);
    }

    /**
     * Marker ekle/güncelle
     */
    setMarker(lat, lng, title = 'Seçilen Konum') {
        if (this.currentLocationMarker) {
            this.map.removeLayer(this.currentLocationMarker);
        }

        this.currentLocationMarker = L.marker([lat, lng], {
            title: title,
            draggable: true,
        }).addTo(this.map);

        // Marker sürüklenme eventi
        this.currentLocationMarker.on('dragend', (e) => {
            const position = e.target.getLatLng();
            this.updateLocationInputs(position.lat, position.lng);
            this.reverseGeocode(position.lat, position.lng);
        });

        // Popup ekle
        this.currentLocationMarker
            .bindPopup(
                `
            <div class="p-2">
                <strong>${title}</strong><br>
                <small>Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}</small>
            </div>
        `
            )
            .openPopup();
    }

    /**
     * Form inputlarını güncelle
     */
    updateLocationInputs(lat, lng) {
        const latInput = document.getElementById('enlem');
        const lngInput = document.getElementById('boylam');

        if (latInput) latInput.value = lat.toFixed(6);
        if (lngInput) lngInput.value = lng.toFixed(6);

        // Context7 event trigger
        if (window.Context7) {
            window.Context7.trigger('location:updated', { lat, lng });
        }
    }

    /**
     * Reverse geocoding - koordinatlardan adres
     */
    async reverseGeocode(lat, lng) {
        try {
            const response = await fetch('/api/geo/reverse-geocode', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                body: JSON.stringify({ latitude: lat, longitude: lng }),
            });

            const payload = await response.json();
            const data = payload.data || {};

            if (data && data.address) {
                this.updateAddressFields(data.address, data.display_name);
            }
        } catch (error) {
            console.error('Reverse geocoding error:', error);
        }
    }

    /**
     * Adres alanlarını güncelle
     */
    updateAddressFields(address, displayName) {
        // İl
        const ilSelect = document.getElementById('il_id');
        if (ilSelect && address.state) {
            const ilOption = Array.from(ilSelect.options).find((opt) =>
                opt.text.toLowerCase().includes(address.state.toLowerCase())
            );
            if (ilOption) ilSelect.value = ilOption.value;
        }

        // İlçe
        const ilceSelect = document.getElementById('ilce_id');
        if (ilceSelect && address.county) {
            // İlçe dropdown'ını güncelle
            this.updateDistrictDropdown(address.county);
        }

        // Mahalle
        const mahalleInput = document.getElementById('mahalle');
        if (mahalleInput && address.suburb) {
            mahalleInput.value = address.suburb;
        }

        // Tam adres
        const adresInput = document.getElementById('adres');
        if (adresInput) {
            adresInput.value = displayName;
        }

        const sokakInput = document.getElementById('sokak');
        if (sokakInput && address.road) {
            sokakInput.value = address.road;
        }

        const caddeInput = document.getElementById('cadde');
        if (caddeInput && address.road) {
            caddeInput.value = address.road;
        }

        const bulvarInput = document.getElementById('bulvar');
        if (bulvarInput && address.road) {
            bulvarInput.value = address.road;
        }

        const binaNoInput = document.getElementById('bina_no');
        if (binaNoInput && address.house_number) {
            binaNoInput.value = address.house_number;
        }

        const daireNoInput = document.getElementById('daire_no');
        if (daireNoInput && address.apartment) {
            daireNoInput.value = address.apartment;
        }

        // Toast notification
        if (window.showToast) {
            window.showToast('success', 'Konum bilgileri güncellendi');
        }
    }

    /**
     * Nearby places - çevredeki yerler
     */
    async findNearbyPlaces(lat, lng, radius = 1000, type = '') {
        try {
            const params = new URLSearchParams({ lat: String(lat), lng: String(lng) });
            if (type) params.append('type', type);
            if (radius) params.append('radius', String(radius));

            const response = await fetch(`/api/geo/nearby?${params.toString()}`);
            const payload = await response.json();
            const places = payload.data || [];

            this.displayNearbyPlaces(places);
            return places;
        } catch (error) {
            console.error('Nearby places error:', error);
            return [];
        }
    }

    /**
     * Nearby places'i haritada göster
     */
    displayNearbyPlaces(places) {
        // Önceki nearby marker'ları temizle
        this.clearNearbyMarkers();

        places.forEach((place) => {
            if (place.lat && place.lon) {
                const marker = L.marker([parseFloat(place.lat), parseFloat(place.lon)], {
                    icon: this.createNearbyIcon(place.type),
                }).addTo(this.map);

                marker.bindPopup(`
                    <div class="p-2">
                        <strong>${place.display_name}</strong><br>
                        <small>${place.type || 'Yer'}</small>
                    </div>
                `);

                this.nearbyMarkers.push(marker);
            }
        });
    }

    /**
     * Özel ikon oluştur
     */
    createNearbyIcon(type) {
        let color = '#3b82f6';

        switch (type) {
            case 'hospital':
                color = '#ef4444';
                break;
            case 'school':
                color = '#10b981';
                break;
            case 'shopping':
                color = '#f59e0b';
                break;
            case 'restaurant':
                color = '#8b5cf6';
                break;
            default:
                color = '#6b7280';
        }

        return L.divIcon({
            className: 'custom-nearby-marker',
            html: `<div style="background-color: ${color}; width: 12px; height: 12px; border-radius: 50%; border: 2px solid white;"></div>`,
            iconSize: [16, 16],
            iconAnchor: [8, 8],
        });
    }

    /**
     * Nearby marker'ları temizle
     */
    clearNearbyMarkers() {
        this.nearbyMarkers.forEach((marker) => {
            this.map.removeLayer(marker);
        });
        this.nearbyMarkers = [];
    }

    /**
     * Konum kontrolü ekle
     */
    addLocationControl() {
        const locationControl = L.control({ position: 'topright' });

        locationControl.onAdd = () => {
            const div = L.DomUtil.create('div', 'leaflet-control leaflet-bar');
            div.innerHTML = `
                <a href="#" title="Konumumu Bul" style="background: white; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; text-decoration: none; color: #333;">
                    📍
                </a>
            `;

            div.onclick = (e) => {
                e.preventDefault();
                this.getCurrentLocation();
            };

            return div;
        };

        locationControl.addTo(this.map);
    }

    /**
     * Mevcut konumu al
     */
    getCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    this.map.setView([lat, lng], 16);
                    this.setMarker(lat, lng, 'Mevcut Konumum');
                    this.updateLocationInputs(lat, lng);
                    this.reverseGeocode(lat, lng);
                },
                (error) => {
                    console.error('Konum alınamadı:', error);
                    if (window.showToast) {
                        window.showToast('error', 'Konum bilgisi alınamadı');
                    }
                }
            );
        }
    }

    /**
     * Adres arama
     */
    async searchAddress(query) {
        try {
            const response = await fetch('/api/geo/geocode', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                body: JSON.stringify({ query, limit: 5 }),
            });

            const payload = await response.json();
            const results = payload.data || [];

            if (results.length > 0) {
                const result = results[0];
                const lat = parseFloat(result.lat);
                const lng = parseFloat(result.lon);

                this.map.setView([lat, lng], 16);
                this.setMarker(lat, lng, result.display_name);
                this.updateLocationInputs(lat, lng);
            }

            return results;
        } catch (error) {
            console.error('Address search error:', error);
            return [];
        }
    }

    /**
     * Nominatim: rate limited fetch with exponential backoff
     */
    async nominatimFetch(url, options = {}, attempt = 0) {
        const now = Date.now();
        const wait = Math.max(0, this.nominatim.minDelay - (now - this.nominatim.lastCall));
        if (wait > 0) {
            await new Promise((r) => setTimeout(r, wait));
        }
        this.nominatim.lastCall = Date.now();

        const resp = await fetch(url, options);
        if ((resp.status === 429 || resp.status === 503) && attempt < this.nominatim.maxRetries) {
            const backoff = Math.pow(2, attempt) * 1000;
            await new Promise((r) => setTimeout(r, backoff));
            return this.nominatimFetch(url, options, attempt + 1);
        }
        return resp;
    }

    /**
     * İlçe dropdown güncelle
     */
    async updateDistrictDropdown(countyName) {
        // İlçe API'si Context7 uyumlu
        const ilId = document.getElementById('il_id')?.value;
        if (!ilId) return;

        try {
            // DÜZELTME: Mevcut route yapısına uyumlu endpoint ve esnek yanıt ayrıştırma
            const response = await fetch(
                `/api/location/districts?il_id=${encodeURIComponent(ilId)}`
            );
            const json = await response.json();
            const districts = Array.isArray(json)
                ? json
                : Array.isArray(json?.data)
                  ? json.data
                  : [];

            const ilceSelect = document.getElementById('ilce_id');
            if (ilceSelect && districts.length > 0) {
                ilceSelect.innerHTML = '<option value="">İlçe Seçin</option>';

                districts.forEach((district) => {
                    const option = document.createElement('option');
                    option.value = district.id;
                    option.textContent = district.name || district.ilce_adi || '';

                    // Eşleşen ilçeyi seç
                    if (
                        countyName &&
                        (district.name || district.ilce_adi || '')
                            .toLowerCase()
                            .includes(countyName.toLowerCase())
                    ) {
                        option.selected = true;
                    }

                    ilceSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('District update error:', error);
        }
    }

    parseFreeAddress(text) {
        if (!text || typeof text !== 'string') return {};
        const t = text.toLowerCase();
        const result = {};

        const noMatch = t.match(/\bno\s*[:\-]?\s*(\d{1,5})/i);
        if (noMatch) result.house_number = noMatch[1];

        const aptMatch = t.match(/\b(daire|ofis)\s*[:\-]?\s*([\w\-]+)/i);
        if (aptMatch) result.apartment = aptMatch[2];

        const roadMatch = t.match(/\b(sokak|sk\.?|cadde|cd\.?|bulvar|blv\.?)\b([^,\n]*)/i);
        if (roadMatch) {
            result.road = roadMatch[2].trim();
            result.roadType = roadMatch[1];
        }

        return result;
    }

    attachFreeAddressListener() {
        const adresInput = document.getElementById('adres');
        if (!adresInput) return;

        let timer = null;
        const handler = () => {
            const parsed = this.parseFreeAddress(adresInput.value || '');

            if (parsed.house_number) {
                const binaNoInput = document.getElementById('bina_no');
                if (binaNoInput) binaNoInput.value = parsed.house_number;
            }
            if (parsed.apartment) {
                const daireNoInput = document.getElementById('daire_no');
                if (daireNoInput) daireNoInput.value = parsed.apartment;
            }
            if (parsed.road) {
                const roadVal = parsed.road;
                const type = parsed.roadType ? parsed.roadType.toLowerCase() : '';
                const sokakInput = document.getElementById('sokak');
                const caddeInput = document.getElementById('cadde');
                const bulvarInput = document.getElementById('bulvar');
                if (type.startsWith('sokak') || type.startsWith('sk')) {
                    if (sokakInput) sokakInput.value = roadVal;
                } else if (type.startsWith('cadde') || type.startsWith('cd')) {
                    if (caddeInput) caddeInput.value = roadVal;
                } else if (type.startsWith('bulvar') || type.startsWith('blv')) {
                    if (bulvarInput) bulvarInput.value = roadVal;
                } else if (sokakInput) {
                    sokakInput.value = roadVal;
                }
            }

            if (window.Context7) {
                window.Context7.trigger('address:parsed', parsed);
            }
        };

        const debounced = () => {
            if (timer) clearTimeout(timer);
            timer = setTimeout(handler, 300);
        };

        adresInput.addEventListener('input', debounced);
        adresInput.addEventListener('change', handler);
    }
}

// Global initialization
window.LeafletMapManager = LeafletMapManager;

// Global fonksiyonlar (Alpine.js için)
window.addressSearch = function (query) {
    if (!query) {
        console.warn('Empty query');
        return [];
    }
    // Context7 Adapter öncelikli
    if (window.c7Location && typeof window.c7Location.searchAddress === 'function') {
        return window.c7Location.searchAddress(query);
    }
    if (window.mapManager && typeof window.mapManager.searchAddress === 'function') {
        return window.mapManager.searchAddress(query);
    }
    console.warn('Map manager not initialized');
    return [];
};

window.getCurrentLocation = function () {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function (position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                if (window.c7Location && typeof window.c7Location.setMarker === 'function') {
                    window.c7Location.setMarker(lat, lng, 'Mevcut Konumum');
                    if (typeof window.c7Location.reverseGeocode === 'function') {
                        window.c7Location.reverseGeocode(lat, lng);
                    }
                } else if (window.mapManager) {
                    window.mapManager.map.setView([lat, lng], 16);
                    window.mapManager.setMarker(lat, lng, 'Mevcut Konumum');
                    window.mapManager.updateLocationInputs(lat, lng);
                    window.mapManager.reverseGeocode(lat, lng);
                }

                // Toast notification
                if (window.toast) {
                    window.toast.success('Konum bulundu!');
                }
            },
            function (error) {
                console.error('Geolocation error:', error);
                if (window.toast) {
                    window.toast.error('Konum alınamadı: ' + error.message);
                }
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 60000,
            }
        );
    } else {
        console.error('Geolocation not supported');
        if (window.toast) {
            window.toast.error('Tarayıcınız konum özelliğini desteklemiyor');
        }
    }
};

// Diğer eksik fonksiyonlar
window.searchNearby = function (type, radius = 1000) {
    const lat = document.getElementById('enlem')?.value;
    const lng = document.getElementById('boylam')?.value;

    if (lat && lng && window.mapManager) {
        window.mapManager.findNearbyPlaces(parseFloat(lat), parseFloat(lng), radius, type);
    }
};

// DOM ready
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('map')) {
        window.mapManager = new LeafletMapManager('map');
        if (window.mapManager && typeof window.mapManager.attachFreeAddressListener === 'function') {
            window.mapManager.attachFreeAddressListener();
        }

        // Nearby search butonları
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('nearby-search-btn')) {
                const type = e.target.dataset.type;
                const lat = document.getElementById('enlem')?.value;
                const lng = document.getElementById('boylam')?.value;

                if (lat && lng && window.mapManager) {
                    window.mapManager.findNearbyPlaces(
                        parseFloat(lat),
                        parseFloat(lng),
                        1000,
                        type
                    );
                }
            }
        });
    }
});
