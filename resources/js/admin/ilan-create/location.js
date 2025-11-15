// ilan-create-location.js - OpenStreetMap Location System v3.0
// Context7 uyumlu - Leaflet.js ile açık kaynak harita sistemi

// OpenStreetMap Configuration
console.log('📍 OpenStreetMap location system loaded (Context7 uyumlu)');

// OpenStreetMap global variables
let leafletMap = null;
let searchHistory = JSON.parse(localStorage.getItem('addressSearchHistory') || '[]');

// Helper functions (Context7 uyumlu)
function showLoading(message) {
    window.toast?.info(message, 2000);
}

function hideLoading() {
    // Toast otomatik kapanır
}

function showNotification(message, type = 'info') {
    if (type === 'error') {
        window.toast?.error(message);
    } else if (type === 'success') {
        window.toast?.success(message);
    } else {
        window.toast?.info(message);
    }
}

// Context7: Dead code removed - LocationManager ve updateFormValues kullanılmıyor

// Update coordinate fields
function updateCoordinateFields(latitude, longitude) {
    const latElement = document.getElementById('latitude');
    const lngElement = document.getElementById('longitude');

    if (latElement) latElement.value = latitude;
    if (lngElement) lngElement.value = longitude;
}

// Fill address from geocoding result
function fillAddressFromGeocoding(addressData) {
    if (addressData.formatted_address) {
        const addressField = document.getElementById('cadde_sokak');
        if (addressField && !addressField.value) {
            addressField.value = addressData.formatted_address.split(',')[0].trim();
        }
    }
}

// Context7: Dead code removed - initializeLegacySystem kullanılmıyor

function loadIlceler(ilId) {
    // Context7: LocationManager opsiyonel, basic implementation kullanılıyor
    // if (locationManager) {
    //     console.log("📍 Using LocationManager for district loading");
    //     return;
    // }

    // Legacy fallback
    if (!ilId) {
        clearIlceler();
        return Promise.resolve();
    }

    showLoading('İlçeler yükleniyor...');

    // ✅ Context7: Sadece veritabanından veri çek (TurkiyeAPI kullanma)
    return fetch(`/api/location/districts/${ilId}`)
        .then((response) => response.json())
        .then((result) => {
            hideLoading();
            // ✅ Context7: API response format'ı (direkt array - adres-yonetimi ile uyumlu)
            const districts = Array.isArray(result.data) ? result.data : [];
            if (result.success && districts.length > 0) {
                populateIlceler(districts);

                // 🗺️ V2.0: İl seçildiğinde haritayı o ile odakla (Leaflet)
                try {
                    if (typeof focusMapOnProvince === 'function') {
                        focusMapOnProvince(ilId);
                    }
                } catch (e) {
                    console.log('📍 Map focus skipped:', e);
                }
                return Promise.resolve();
            } else {
                // Context7: DB'de ilçe yoksa boş liste göster
                console.log("⚠️ DB'de ilçe bulunamadı");
                populateIlceler([]);
                showNotification('Bu il için ilçe bulunamadı', 'info');
                return Promise.resolve();
            }
        })
        .catch((error) => {
            hideLoading();
            console.error('İlçe yükleme hatası:', error);
            showNotification('İlçeler yüklenemedi', 'error');
            return Promise.reject(error);
        });
}

// Context7: TurkiyeAPI kullanımı kaldırıldı - Sadece veritabanından veri çekiliyor

// 🗺️ V2.0: İl bazlı harita odaklama (Leaflet uyumlu)
function focusMapOnProvince(ilId) {
    // ✅ Guard: Leaflet map tanımlı değilse çalışma
    if (!leafletMap) {
        console.log('📍 focusMapOnProvince skipped (Leaflet map not initialized)');
        return;
    }

    // Context7: İl koordinatları (Türkiye illeri için)
    const provinceCoords = {
        48: [37.2153, 28.3636], // Muğla
        34: [41.0082, 28.9784], // İstanbul
        6: [39.9334, 32.8597], // Ankara
        35: [38.4237, 27.1428], // İzmir
        7: [36.8969, 30.7133], // Antalya
    };

    const coords = provinceCoords[ilId];
    if (coords) {
        try {
            leafletMap.setView(coords, 10); // Zoom level 10 (şehir görünümü)
            showNotification('Harita il seçimine göre odaklandı', 'success');
        } catch (error) {
            console.warn('Map focus error:', error);
        }
    }
}

function clearIlceler() {
    const ilceSelect = document.getElementById('ilce_id');
    const mahalleSelect = document.getElementById('mahalle_id'); // Context7: mahalle_id

    ilceSelect.innerHTML = '<option value="">Önce İl Seçin...</option>';
    if (mahalleSelect) {
        mahalleSelect.innerHTML = '<option value="">Önce İlçe Seçin...</option>';
    }
}

function populateIlceler(districts) {
    const ilceSelect = document.getElementById('ilce_id');
    const mahalleSelect = document.getElementById('mahalle_id'); // Context7: mahalle_id

    if (!ilceSelect) {
        console.log('📍 ilce_id elementi bulunamadı');
        return;
    }

    // ✅ Clear existing options
    ilceSelect.innerHTML = '<option value="">İlçe Seçin...</option>';
    if (mahalleSelect) {
        mahalleSelect.innerHTML = '<option value="">Önce İlçe Seçin...</option>';
    }

    // ✅ Context7: API response formatına uygun field mapping
    districts.forEach((district) => {
        const option = document.createElement('option');
        option.value = district.id || ''; // TurkiyeAPI'den gelenler id: null olabilir
        const ilceName = district.ilce || district.name || district.ilce_adi;
        option.textContent = ilceName + (district._from_turkiyeapi ? ' (TurkiyeAPI)' : '');
        ilceSelect.appendChild(option);
    });

    // ✅ İlçe dropdown'ını aktif et
    ilceSelect.disabled = false;

    // ✅ FIX: Edit mode için otomatik seçim
    if (window.editMode && window.ilanData?.ilce_id) {
        const ilceId = String(window.ilanData.ilce_id);
        if (ilceSelect.querySelector(`option[value="${ilceId}"]`)) {
            ilceSelect.value = ilceId;
            // İlçe seçildi, mahalleleri yükle
            loadMahalleler(ilceId);
        }
    }

    console.log('✅ İlçeler populate edildi:', districts.length, 'adet');
}

function loadMahalleler(ilceId) {
    if (!ilceId) {
        clearMahalleler();
        return Promise.resolve();
    }

    // İl ID'sini al (mahalle çekmek için gerekli)
    const ilSelect = document.getElementById('il_id');
    const ilId = ilSelect ? ilSelect.value : null;

    showLoading('Mahalleler yükleniyor...');

    // ✅ Context7: Sadece veritabanından veri çek (TurkiyeAPI kullanma)
    return fetch(`/api/location/neighborhoods/${ilceId}`)
        .then((response) => response.json())
        .then((result) => {
            hideLoading();
            // ✅ Context7: API response format'ı (direkt array - adres-yonetimi ile uyumlu)
            const neighborhoods = Array.isArray(result.data) ? result.data : [];
            if (result.success && neighborhoods.length > 0) {
                populateMahalleler(neighborhoods);
                return Promise.resolve();
            } else {
                // Context7: DB'de mahalle yoksa boş liste göster
                console.log("⚠️ DB'de mahalle bulunamadı");
                populateMahalleler([]);
                showNotification('Bu ilçe için mahalle bulunamadı', 'info');
                return Promise.resolve();
            }
        })
        .catch((error) => {
            hideLoading();
            console.error('Mahalle yükleme hatası:', error);
            showNotification('Mahalleler yüklenemedi', 'error');
            return Promise.reject(error);
        });
}

// Context7: TurkiyeAPI kullanımı kaldırıldı - Sadece veritabanından veri çekiliyor

function clearMahalleler() {
    const mahalleSelect = document.getElementById('mahalle_id'); // Context7: mahalle_id
    if (mahalleSelect) {
        mahalleSelect.innerHTML = '<option value="">Önce İlçe Seçin...</option>';
        mahalleSelect.disabled = true;
    }
}

function populateMahalleler(neighborhoods) {
    const mahalleSelect = document.getElementById('mahalle_id'); // Context7: mahalle_id

    if (!mahalleSelect) {
        console.log('📍 mahalle_id elementi bulunamadı');
        return;
    }

    mahalleSelect.innerHTML = '<option value="">Mahalle Seçin...</option>';

    // ✅ Context7: API response formatına uygun field mapping
    neighborhoods.forEach((neighborhood) => {
        const option = document.createElement('option');
        option.value = neighborhood.id || ''; // TurkiyeAPI'den gelenler id: null olabilir
        const mahalleName = neighborhood.mahalle || neighborhood.name || neighborhood.mahalle_adi;
        option.textContent = mahalleName + (neighborhood._from_turkiyeapi ? ' (TurkiyeAPI)' : '');
        mahalleSelect.appendChild(option);
    });

    // ✅ Mahalle dropdown'ını aktif et
    mahalleSelect.disabled = false;

    // ✅ FIX: Edit mode için otomatik seçim
    if (window.editMode && window.ilanData?.mahalle_id) {
        const mahalleId = String(window.ilanData.mahalle_id);
        if (mahalleSelect.querySelector(`option[value="${mahalleId}"]`)) {
            mahalleSelect.value = mahalleId;
        }
    }

    console.log('✅ Mahalleler populate edildi:', neighborhoods.length, 'adet');
}

function getCurrentLocation() {
    // 🔧 Modern Geolocation with Permissions Policy Check
    if (!navigator.geolocation) {
        showNotification('Tarayıcınız konum servislerini desteklemiyor', 'error');
        return;
    }

    // Check permissions policy first to avoid violations
    if (navigator.permissions) {
        navigator.permissions
            .query({ name: 'geolocation' })
            .then((result) => {
                if (result.state === 'denied') {
                    showNotification(
                        'Konum izni reddedildi. Tarayıcı ayarlarından izin verin.',
                        'warning'
                    );
                    return;
                }

                if (result.state === 'prompt' || result.state === 'granted') {
                    requestGeolocation();
                }
            })
            .catch(() => {
                // Fallback if permissions API not available
                requestGeolocation();
            });
    } else {
        // Fallback if permissions API not available
        requestGeolocation();
    }
}

function requestGeolocation() {
    showLoading('Konumunuz belirleniyor...');

    navigator.geolocation.getCurrentPosition(
        (position) => {
            hideLoading();
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            // Update form fields (Context7 uyumlu - null guard)
            const latEl = document.getElementById('latitude');
            const lngEl = document.getElementById('longitude');

            if (latEl) latEl.value = lat;
            if (lngEl) lngEl.value = lng;

            // Update map
            updateMapLocation(lat, lng);

            // Reverse geocode to get address
            reverseGeocode(lat, lng);
        },
        (error) => {
            hideLoading();
            let message = 'Konum alınamadı';
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    message = 'Konum izni reddedildi';
                    break;
                case error.POSITION_UNAVAILABLE:
                    message = 'Konum bilgisi mevcut değil';
                    break;
                case error.TIMEOUT:
                    message = 'Konum alma işlemi zaman aşımına uğradı';
                    break;
            }
            showNotification(message, 'error');
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 300000,
        }
    );
}

// 🔍 Advanced Address Search v2.0
async function searchAddress() {
    const addressInput =
        document.getElementById('address-search-input') ||
        document.querySelector('[x-model="addressSearch"]');

    if (!addressInput) return;

    const address = addressInput.value.trim();
    if (!address) {
        showNotification('Lütfen aranacak adresi girin', 'warning');
        return;
    }

    // Save to search history
    saveToSearchHistory(address);

    // 🔧 Enhanced: Safe Geocoding with error handling
    if (window.google && window.google.maps && geocoder) {
        try {
            showLoading('🔍 Gelişmiş adres arama...');

            geocoder.geocode(
                {
                    address: address,
                    region: 'TR', // Turkey bias
                    componentRestrictions: { country: 'TR' },
                },
                (results, status) => {
                    if (status === 'OK' && results[0]) {
                        const location = results[0].geometry.location;
                        const latitude = location.lat();
                        const longitude = location.lng();

                        // Update coordinate fields
                        updateCoordinateFields(latitude, longitude);

                        // Update map if available
                        if (map) {
                            updateMapLocation(latitude, longitude);

                            // 🎯 V2.0: Yakındaki yerleri göster
                            setTimeout(() => {
                                searchNearbyPlaces(latitude, longitude);
                            }, 1000);
                        }

                        // Fill address fields
                        if (results[0].formatted_address) {
                            fillAddressFromGeocoding(results[0]);

                            // 🗺️ V2.0: Address component'larını parse et ve form'u doldur
                            parseAddressComponents(results[0].address_components);
                        }

                        showNotification(
                            '✅ Adres başarıyla bulundu ve yakındaki yerler yükleniyor',
                            'success'
                        );
                    } else {
                        showNotification('❌ Adres bulunamadı', 'error');
                        // Autocomplete suggestions'ı göster
                        showAddressSuggestions(address);
                    }

                    hideLoading();
                }
            );
        } catch (error) {
            hideLoading();
            console.error('Enhanced geocoding failed:', error);

            // API yetkilendirme hatası kontrolü
            if (error.message && error.message.includes('not authorized')) {
                showApiStatusIndicator('API yetkilendirme sorunu: Kurulum gerekli');
                showNotification(
                    'API yetkilendirme sorunu - Kurulum kılavuzunu kontrol edin',
                    'warning'
                );
            } else {
                showNotification('Adres arama başarısız', 'error');
            }
        }
        return;
    }

    // Use OpenStreetMap Nominatim API for geocoding
    try {
        const response = await fetch(
            `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(
                address
            )}&countrycodes=tr&limit=1&addressdetails=1`
        );

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const results = await response.json();

        if (results && results.length > 0) {
            const location = results[0];
            const lat = parseFloat(location.lat);
            const lng = parseFloat(location.lon);

            // Update form fields
            const latEl = document.getElementById('latitude');
            const lngEl = document.getElementById('longitude');

            if (latEl) latEl.value = lat;
            if (lngEl) lngEl.value = lng;

            // Update Leaflet map if available
            if (window.Context7LeafletManager) {
                window.Context7LeafletManager.setMarker(lat, lng, 'Seçilen Konum');
            }

            // Parse address
            const addressParts = location.display_name.split(',');
            const addressField = document.getElementById('cadde_sokak');
            if (addressField && !addressField.value && addressParts.length > 0) {
                addressField.value = addressParts[0].trim();
            }

            showNotification('Adres başarıyla bulundu', 'success');
        } else {
            showNotification('Adres bulunamadı', 'error');
        }
    } catch (error) {
        console.error('Nominatim geocoding failed:', error);
        showNotification('Adres arama başarısız', 'error');
    }

    hideLoading();
}

async function reverseGeocode(lat, lng) {
    try {
        const response = await fetch(
            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`
        );

        if (response.ok) {
            const result = await response.json();
            if (result && result.display_name) {
                const addressField = document.getElementById('cadde_sokak');
                if (addressField && !addressField.value) {
                    const addressParts = result.display_name.split(',');
                    addressField.value = addressParts[0].trim();
                }
            }
        }
    } catch (error) {
        console.error('Reverse geocoding failed:', error);
    }
}

// OpenStreetMap/Nominatim address parsing
function fillAddressFields(nominatimResult) {
    if (!nominatimResult || !nominatimResult.display_name) return;

    const addressParts = nominatimResult.display_name.split(',');
    const addressField = document.getElementById('cadde_sokak');

    if (addressField && !addressField.value && addressParts.length > 0) {
        addressField.value = addressParts[0].trim();
    }

    // Parse Nominatim address components if available
    if (nominatimResult.address) {
        const addr = nominatimResult.address;

        // Try to fill city/district/neighborhood from Nominatim data
        // Note: Nominatim structure is different from Google Maps
        const cityField = document.getElementById('il');
        const districtField = document.getElementById('ilce');
        const neighborhoodField = document.getElementById('mahalle');

        if (cityField && !cityField.value && addr.state) {
            cityField.value = addr.state;
        }

        if (districtField && !districtField.value && (addr.county || addr.city_district)) {
            districtField.value = addr.county || addr.city_district;
        }

        if (neighborhoodField && !neighborhoodField.value && (addr.suburb || addr.neighbourhood)) {
            neighborhoodField.value = addr.suburb || addr.neighbourhood;
        }
    }
}

// Initialize Advanced Google Maps v2.0 (Context7: Safe initialization)
function initializeMap() {
    const mapElement = document.getElementById('map');
    if (!mapElement) {
        console.warn('⚠️ Map element (#map) not found');
        return;
    }

    // Default location (Bodrum, Turkey - suited for real estate)
    const defaultLat = 37.0344;
    const defaultLng = 27.4305;

    // Initialize OpenStreetMap with Leaflet
    if (typeof L === 'undefined') {
        console.warn('⚠️ Leaflet not loaded, trying to load from global window...');
        if (window.Context7LeafletManager) {
            console.log('✅ Using Context7 Leaflet Manager');
            leafletMap = window.Context7LeafletManager.getMap();
            if (leafletMap) {
                setupLeafletEventListeners();
                return;
            }
        }

        // Try to initialize with Leaflet CDN
        setTimeout(initializeMap, 1000);
        return;
    }

    try {
        // Create Leaflet map
        leafletMap = L.map(mapElement.id).setView(
            [defaultLat, defaultLng],
            MAP_CONFIG.DEFAULT_ZOOM
        );

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(leafletMap);

        // Set bounds for Turkey
        const turkeyBounds = L.latLngBounds([35.5, 25.5], [42.5, 45.0]);
        leafletMap.setMaxBounds(turkeyBounds);

        console.log('✅ OpenStreetMap with Leaflet initialized');

        // Setup event listeners
        setupLeafletEventListeners();
    } catch (error) {
        console.error('❌ Leaflet map error:', error);
        return;
    }

    // Try to load existing coordinates
    const latitudeElement = document.getElementById('latitude');
    const longitudeElement = document.getElementById('longitude');
    const latitude = latitudeElement?.value;
    const longitude = longitudeElement?.value;

    if (latitude && longitude) {
        updateMapLocation(parseFloat(latitude), parseFloat(longitude));
    } else {
        // Place default marker
        placeLeafletMarker([defaultLat, defaultLng]);
    }
}

function placeMarker(location) {
    // Remove existing marker
    if (marker) {
        marker.setMap(null);
    }

    // Create new marker (Context7: Modern Google Maps API)
    try {
        // Use AdvancedMarkerElement if available (recommended)
        if (google.maps.marker && google.maps.marker.AdvancedMarkerElement) {
            marker = new google.maps.marker.AdvancedMarkerElement({
                position: location,
                map: map,
                gmpDraggable: true,
                title: 'İlan Konumu',
            });
        } else {
            // Fallback to legacy Marker (will be deprecated)
            marker = new google.maps.Marker({
                position: location,
                map: map,
                draggable: true,
                animation: google.maps.Animation.DROP,
            });
        }
    } catch (error) {
        console.warn('Advanced marker failed, using legacy marker:', error);
        // Fallback to legacy Marker
        marker = new google.maps.Marker({
            position: location,
            map: map,
            draggable: true,
            animation: google.maps.Animation.DROP,
        });
    }

    // Update form fields (Context7 uyumlu - null guard)
    const latElement = document.getElementById('latitude');
    const lngElement = document.getElementById('longitude');

    if (latElement) latElement.value = location.lat();
    if (lngElement) lngElement.value = location.lng();

    // Add drag listener (Context7: Support both marker types)
    const addDragListener = () => {
        const eventName = marker.gmpDraggable !== undefined ? 'gmp-dragend' : 'dragend';
        marker.addListener(eventName, (event) => {
            const position = event.latLng || marker.position;
            const latEl = document.getElementById('latitude');
            const lngEl = document.getElementById('longitude');

            if (latEl) latEl.value = position.lat();
            if (lngEl) lngEl.value = position.lng();
        });
    };

    addDragListener();

    // Center map on marker
    map.setCenter(location);
}

function updateMapLocation(lat, lng) {
    const location = new google.maps.LatLng(lat, lng);
    placeMarker(location);
    map.setCenter(location);
    map.setZoom(16);
}

// Advanced Map Controls v2.0
function toggleMapType() {
    if (!map) return;

    // Context7: Google Maps kontrolü
    if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
        window.toast?.warning('Harita henüz yüklenmedi');
        return;
    }

    const mapTypes = [
        google.maps.MapTypeId.ROADMAP,
        google.maps.MapTypeId.SATELLITE,
        google.maps.MapTypeId.HYBRID,
        google.maps.MapTypeId.TERRAIN,
    ];

    const currentIndex = mapTypes.indexOf(map.getMapTypeId());
    const nextIndex = (currentIndex + 1) % mapTypes.length;
    const nextType = mapTypes[nextIndex];

    map.setMapTypeId(nextType);

    // Show notification
    const typeNames = {
        [google.maps.MapTypeId.ROADMAP]: 'Yol Haritası',
        [google.maps.MapTypeId.SATELLITE]: 'Uydu Görünümü',
        [google.maps.MapTypeId.HYBRID]: 'Hibrit Görünüm',
        [google.maps.MapTypeId.TERRAIN]: 'Arazi Haritası',
    };

    showNotification(`Harita türü: ${typeNames[nextType]}`, 'info');
}

function zoomIn() {
    if (map) {
        const newZoom = Math.min(map.getZoom() + 1, 20);
        map.setZoom(newZoom);
        showNotification(`Zoom: ${newZoom}`, 'info');
    }
}

function zoomOut() {
    if (map) {
        const newZoom = Math.max(map.getZoom() - 1, 1);
        map.setZoom(newZoom);
        showNotification(`Zoom: ${newZoom}`, 'info');
    }
}

function centerOnTurkey() {
    if (map) {
        map.setCenter({ lat: 39.9334, lng: 32.8597 });
        map.setZoom(MAP_CONFIG.COUNTRY_ZOOM);
        showNotification('Türkiye merkezlendi', 'success');
    }
}

function shareLocation() {
    const lat = document.getElementById('latitude')?.value;
    const lng = document.getElementById('longitude')?.value;

    if (lat && lng) {
        const url = `https://www.google.com/maps/@${lat},${lng},15z`;
        navigator.clipboard
            .writeText(url)
            .then(() => {
                showNotification('Konum linki kopyalandı!', 'success');
            })
            .catch(() => {
                showNotification('Link kopyalanamadı', 'error');
            });
    } else {
        showNotification('Önce bir konum seçin', 'warning');
    }
}

function validateLocation() {
    const il = document.getElementById('il_id').value;
    const ilce = document.getElementById('ilce_id').value;
    const latitude = document.getElementById('latitude').value;
    const longitude = document.getElementById('longitude').value;

    if (!il) {
        showFieldError(document.getElementById('il_id'), 'İl seçimi zorunludur.');
        return false;
    }

    if (!ilce) {
        showFieldError(document.getElementById('ilce_id'), 'İlçe seçimi zorunludur.');
        return false;
    }

    if (!latitude || !longitude) {
        showNotification('Lütfen harita üzerinde konum belirleyin', 'warning');
        return false;
    }

    return true;
}

// Alpine.js data function for advanced location manager
window.advancedLocationManager = function () {
    return {
        selectedIl: '',
        selectedIlce: '',
        selectedMahalle: '', // Context7: mahalle_id
        latitude: '',
        longitude: '',
        addressSearch: '',

        init() {
            // Load saved values
            this.selectedIl = document.getElementById('il_id')?.value || '';
            this.selectedIlce = document.getElementById('ilce_id')?.value || '';
            this.selectedMahalle = document.getElementById('mahalle_id')?.value || '';
            this.latitude = document.getElementById('latitude')?.value || '';
            this.longitude = document.getElementById('longitude')?.value || '';

            // Initialize map after Alpine
            this.$nextTick(() => {
                if (typeof google !== 'undefined') {
                    initializeMap();
                }
            });
        },

        loadIlceler(ilId) {
            this.selectedIl = ilId;
            this.selectedIlce = '';
            this.selectedMahalle = '';
            loadIlceler(ilId);
        },

        loadMahalleler(ilceId) {
            this.selectedIlce = ilceId;
            this.selectedMahalle = '';
            loadMahalleler(ilceId);
        },

        getCurrentLocation() {
            getCurrentLocation();
        },

        searchAddress() {
            searchAddress();
        },
    };
};

// Initialize location event listeners
document.addEventListener('DOMContentLoaded', () => {
    console.log('📍 Location system initializing...');

    // Context7: VanillaLocationManager global olarak yükleniyor (leaflet-loader.js'den)
    // initializeLocationManager() çağrısı kaldırıldı - VanillaLocationManager otomatik init ediliyor

    console.log('✅ Location system ready (VanillaLocationManager will auto-initialize)');

    // Legacy event listeners (as fallback)
    const ilSelect = document.getElementById('il_id');
    const ilceSelect = document.getElementById('ilce_id');

    // ✅ Vanilla JS: location-container üzerinde event listener ekle
    if (ilSelect && !ilSelect.hasAttribute('data-vanilla-listener')) {
        ilSelect.setAttribute('data-vanilla-listener', 'true');
        ilSelect.addEventListener('change', function () {
            console.log('📍 Vanilla JS: İl seçildi:', this.value);
            loadIlceler(this.value);

            // 🗺️ Haritayı ile odakla (VanillaLocationManager'dan çağır)
            // ⚠️ SKIP: İlçe cascade loading yapacak, gereksiz zoom
            // Sadece ilçe seçildiğinde zoom yap (cascade tamamlandığında)
        });
    }

    if (ilceSelect && !ilceSelect.hasAttribute('data-vanilla-listener')) {
        ilceSelect.setAttribute('data-vanilla-listener', 'true');
        ilceSelect.addEventListener('change', function () {
            console.log('📍 Vanilla JS: İlçe seçildi:', this.value);
            loadMahalleler(this.value);

            // 🗺️ Haritayı ilçeye odakla (VanillaLocationManager'dan çağır)
            // ⚠️ SKIP: Mahalle cascade loading yapacak, gereksiz zoom
            // Sadece mahalle seçildiğinde zoom yap (cascade tamamlandığında)
        });
    }

    // Mahalle event listener
    const mahalleSelect = document.getElementById('mahalle_id');
    if (mahalleSelect && !mahalleSelect.hasAttribute('data-vanilla-listener')) {
        mahalleSelect.setAttribute('data-vanilla-listener', 'true');
        mahalleSelect.addEventListener('change', function () {
            console.log('📍 Vanilla JS: Mahalle seçildi:', this.value);

            // 🗺️ Haritayı mahalleye odakla (VanillaLocationManager'dan çağır)
            if (window.VanillaLocationManager && this.value) {
                // 🔧 Silent update kontrolü (reverse geocoding sırasında skip et)
                if (window.VanillaLocationManager.isSilentUpdate) {
                    console.log('⏭️ Silent update aktif, mahalle focus atlandı');
                    return;
                }

                // ✅ SON ADIM: Mahalle seçildi, direkt buraya zoom yap
                const checkAndFocus = () => {
                    if (window.VanillaLocationManager.map) {
                        const mahalleName = this.options[this.selectedIndex].text;
                        const ilceName =
                            document.getElementById('ilce_id').options[
                                document.getElementById('ilce_id').selectedIndex
                            ].text;
                        const ilName =
                            document.getElementById('il_id').options[
                                document.getElementById('il_id').selectedIndex
                            ].text;
                        console.log(
                            '🎯 [DEBUG] SON ADIM: Mahalle focus (cascade complete):',
                            mahalleName
                        );
                        window.VanillaLocationManager.focusMapOnNeighborhood(
                            mahalleName,
                            ilceName,
                            ilName
                        );
                    } else {
                        console.log('⏳ [DEBUG] Harita henüz hazır değil, 1s sonra tekrar...');
                        setTimeout(checkAndFocus, 1000);
                    }
                };
                checkAndFocus();
            }
        });
    }

    console.log('✅ Vanilla JS location event listeners registered');

    // ✅ Edit Mode: Load existing location values
    if (window.editMode && window.ilanData) {
        console.log('📝 Edit mode detected, loading existing location values...', window.ilanData);

        const ilId = window.ilanData.il_id;
        const ilceId = window.ilanData.ilce_id;
        const mahalleId = window.ilanData.mahalle_id;

        // Set il if exists
        if (ilId && ilSelect) {
            ilSelect.value = ilId;

            // Load ilçeler and wait for response
            loadIlceler(ilId).then(() => {
                // Set ilçe after districts are loaded
                if (ilceId && ilceSelect) {
                    setTimeout(() => {
                        if (ilceSelect.querySelector(`option[value="${ilceId}"]`)) {
                            ilceSelect.value = ilceId;

                            // Load mahalleler
                            loadMahalleler(ilceId).then(() => {
                                // Set mahalle after neighborhoods are loaded
                                if (mahalleId && mahalleSelect) {
                                    setTimeout(() => {
                                        if (
                                            mahalleSelect.querySelector(
                                                `option[value="${mahalleId}"]`
                                            )
                                        ) {
                                            mahalleSelect.value = mahalleId;
                                            console.log(
                                                '✅ All location values loaded in edit mode'
                                            );
                                        }
                                    }, 300);
                                }
                            });
                        }
                    }, 300);
                }
            });
        }
    }

    // Initialize map when Google Maps API is loaded
    if (typeof google !== 'undefined') {
        initializeMap();
    } else {
        // Wait for Google Maps API to load
        window.initMap = initializeMap;
    }
});

// 🗺️ Advanced Map Features v2.0

// Setup Map Event Listeners
function setupMapEventListeners() {
    if (!map) return;

    // Click listener to place marker
    map.addListener('click', (event) => {
        placeMarker(event.latLng);

        // Reverse geocode the clicked location
        reverseGeocode(event.latLng.lat(), event.latLng.lng());
    });

    // Zoom change listener
    map.addListener('zoom_changed', () => {
        const zoom = map.getZoom();
        updateMapBehaviorByZoom(zoom);
    });

    // Map type change listener
    map.addListener('maptypeid_changed', () => {
        const mapType = map.getMapTypeId();
        console.log(`📍 Map type changed to: ${mapType}`);
    });
}

// Initialize Map Controls
function initializeMapControls() {
    if (!map) return;

    // Custom control için container
    const controlDiv = document.createElement('div');
    controlDiv.style.margin = '10px';

    // Türkiye'ye odaklanma butonu (Context7: Tailwind CSS)
    const turkeyButton = document.createElement('button');
    turkeyButton.innerHTML = '🇹🇷 Türkiye';
    turkeyButton.className =
        'px-3 py-1.5 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors duration-200';
    turkeyButton.onclick = centerOnTurkey;
    controlDiv.appendChild(turkeyButton);

    map.controls[google.maps.ControlPosition.TOP_CENTER].push(controlDiv);
}

// Search History Management
function saveToSearchHistory(address) {
    if (!searchHistory.includes(address)) {
        searchHistory.unshift(address);
        if (searchHistory.length > 10) {
            searchHistory = searchHistory.slice(0, 10);
        }
        localStorage.setItem('addressSearchHistory', JSON.stringify(searchHistory));
    }
}

function showSearchHistory() {
    if (searchHistory.length === 0) {
        showNotification('Arama geçmişi boş', 'info');
        return;
    }

    // Search history UI gösterebiliriz
    console.log('🔍 Search History:', searchHistory);
}

// Address Components Parser
function parseAddressComponents(components) {
    let il = '',
        ilce = '',
        mahalle = '';

    components.forEach((component) => {
        const types = component.types;

        if (types.includes('administrative_area_level_1')) {
            il = component.long_name;
        } else if (types.includes('administrative_area_level_2')) {
            ilce = component.long_name;
        } else if (types.includes('sublocality_level_1') || types.includes('neighborhood')) {
            mahalle = component.long_name;
        }
    });

    // Form alanlarını doldur
    if (il) {
        const ilSelect = document.getElementById('il_id');
        if (ilSelect) {
            // İl adına göre değer bul ve seç
            for (const option of ilSelect.options) {
                if (option.text.toLowerCase().includes(il.toLowerCase())) {
                    ilSelect.value = option.value;
                    // İlçeleri yükle
                    loadIlceler(option.value);
                    break;
                }
            }
        }
    }

    console.log(`📍 Parsed address: İl: ${il}, İlçe: ${ilce}, Mahalle: ${mahalle}`);
}

// 🔧 Modern Places Search - Avoiding deprecated API warnings
function searchNearbyPlaces(lat, lng) {
    // Check if coordinates are provided
    if (!lat || !lng) {
        const latEl = document.getElementById('latitude');
        const lngEl = document.getElementById('longitude');

        if (latEl && lngEl && latEl.value && lngEl.value) {
            lat = parseFloat(latEl.value);
            lng = parseFloat(lngEl.value);
        } else {
            showNotification('Önce bir konum seçin', 'warning');
            return;
        }
    }

    // For now, use simple marker placement to avoid deprecated API warnings
    // Modern Places API integration would require API key reconfiguration

    showLoading('Yakındaki yerler aranıyor...');

    // Simulate nearby places with known locations for demo
    const simulatedPlaces = [
        {
            name: 'Eğitim Kurumu',
            lat: lat + 0.002,
            lng: lng + 0.001,
            type: 'school',
            icon: '🏫',
        },
        {
            name: 'Sağlık Merkezi',
            lat: lat - 0.001,
            lng: lng + 0.002,
            type: 'hospital',
            icon: '🏥',
        },
        {
            name: 'Alışveriş Merkezi',
            lat: lat + 0.001,
            lng: lng - 0.001,
            type: 'shopping_mall',
            icon: '🛒',
        },
    ];

    clearNearbyMarkers();

    simulatedPlaces.forEach((place, index) => {
        setTimeout(() => {
            createSimulatedNearbyMarker(place);
        }, index * 300);
    });

    setTimeout(() => {
        hideLoading();
        showNotification(`${simulatedPlaces.length} yakındaki yer bulundu (demo)`, 'success');
    }, 1000);

    // TODO: Integrate with modern Places API when available
    console.log('ℹ️ Using simulated nearby places to avoid deprecated API warnings');
}

// Nearby Markers Management
function clearNearbyMarkers() {
    nearbyMarkers.forEach((marker) => marker.setMap(null));
    nearbyMarkers = [];
}

function createNearbyMarker(place) {
    const marker = new google.maps.Marker({
        position: place.geometry.location,
        map: map,
        title: place.name,
        icon: {
            url: place.icon,
            scaledSize: new google.maps.Size(20, 20),
        },
        animation: google.maps.Animation.DROP,
    });

    // Info window
    const infoWindow = new google.maps.InfoWindow({
        content: `
            <div class="p-2">
                <h6 class="font-bold">${place.name}</h6>
                <p class="text-sm text-gray-600">${place.types[0]}</p>
                ${place.rating ? `<p class="text-sm">⭐ ${place.rating}</p>` : ''}
            </div>
        `,
    });

    marker.addListener('click', () => {
        infoWindow.open(map, marker);
    });

    nearbyMarkers.push(marker);
}

// 🆕 Modern simulated marker function
function createSimulatedNearbyMarker(place) {
    const marker = new google.maps.Marker({
        position: new google.maps.LatLng(place.lat, place.lng),
        map: map,
        title: place.name,
        icon: {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 8,
            fillColor: getTypeColor(place.type),
            fillOpacity: 0.8,
            strokeWeight: 2,
            strokeColor: '#FFFFFF',
        },
        animation: google.maps.Animation.DROP,
    });

    // Modern info window
    const infoWindow = new google.maps.InfoWindow({
        content: `
            <div class="p-3 min-w-40">
                <div class="flex items-center mb-2">
                    <span class="text-lg mr-2">${place.icon}</span>
                    <h6 class="font-semibold text-gray-800">${place.name}</h6>
                </div>
                <p class="text-sm text-gray-600">${getTypeLabel(place.type)}</p>
                <p class="text-xs text-blue-600 mt-1">Demo yakındaki yer</p>
            </div>
        `,
    });

    marker.addListener('click', () => {
        infoWindow.open(map, marker);
    });

    nearbyMarkers.push(marker);
}

// Helper functions for simulated places
function getTypeColor(type) {
    const colors = {
        school: '#10B981', // green
        hospital: '#EF4444', // red
        shopping_mall: '#8B5CF6', // purple
        bank: '#F59E0B', // amber
        pharmacy: '#06B6D4', // cyan
        supermarket: '#84CC16', // lime
    };
    return colors[type] || '#6B7280';
}

function getTypeLabel(type) {
    const labels = {
        school: 'Eğitim Kurumu',
        hospital: 'Sağlık Merkezi',
        shopping_mall: 'Alışveriş Merkezi',
        bank: 'Banka',
        pharmacy: 'Eczane',
        supermarket: 'Market',
    };
    return labels[type] || 'Yakındaki Yer';
}

// Temporary Marker for Location Focus
function showTemporaryMarker(location, title, duration = 3000) {
    const tempMarker = new google.maps.Marker({
        position: location,
        map: map,
        title: title,
        animation: google.maps.Animation.BOUNCE,
        icon: {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 8,
            fillColor: '#FF6B35',
            fillOpacity: 0.8,
            strokeWeight: 2,
            strokeColor: '#FFFFFF',
        },
    });

    setTimeout(() => {
        tempMarker.setMap(null);
    }, duration);
}

// Map Behavior by Zoom Level
function updateMapBehaviorByZoom(zoom) {
    if (zoom > 15) {
        // High zoom - show detailed POIs
        map.setOptions({
            styles: [
                {
                    featureType: 'poi',
                    stylers: [{ visibility: 'on' }],
                },
            ],
        });
    } else if (zoom < 10) {
        // Low zoom - hide POIs for cleaner view
        map.setOptions({
            styles: [
                {
                    featureType: 'poi.business',
                    stylers: [{ visibility: 'off' }],
                },
            ],
        });
    }
}

// 🔧 Address Suggestions - Disabled to avoid deprecated API warnings
function showAddressSuggestions(query) {
    // Autocomplete service is deprecated, provide alternative solution
    console.log('ℹ️ Address suggestions disabled to avoid deprecated API warnings');

    // Alternative: Show search history as suggestions
    if (searchHistory.length > 0) {
        const matchingHistory = searchHistory
            .filter((item) => item.toLowerCase().includes(query.toLowerCase()))
            .slice(0, 3);

        if (matchingHistory.length > 0) {
            console.log('🔍 Search history suggestions:', matchingHistory);
            showNotification('Arama geçmişinden öneriler mevcut', 'info');
        }
    }

    // TODO: Implement modern Places API autocomplete when available
}

// Context7: Google Maps KULLANILMIYOR
// OpenStreetMap (Leaflet.js) location-map.blade.php component'inde kullanılıyor
// Bu fonksiyonlar legacy kod olarak bırakıldı ama kullanılmıyor

// Main initialization function
async function initializeLocation() {
    try {
        console.log('🗺️ Initializing location system...');

        // Context7: OpenStreetMap kullanılıyor (location-map.blade.php component'inde)
        // Google Maps API'ye ihtiyaç YOK
        console.log('📍 Using OpenStreetMap (Leaflet.js) - Context7 uyumlu');

        // Map initialization location-map.blade.php component'inde Alpine.js ile yapılıyor
        console.log('✅ Location system uses Alpine.js locationManager() from blade component');

        console.log('✅ Location system initialized successfully');
    } catch (error) {
        console.error('❌ Location system initialization failed:', error);
        showNotification('Harita sistemi yüklenemedi. Sayfayı yenileyin.', 'error');
    }
}

// Alpine.js data function
window.advancedLocationManager = function () {
    return {
        selectedIl: '',
        selectedIlce: '',
        selectedMahalle: '', // Context7: mahalle_id
        ilceler: [],
        mahalleler: [], // Context7: mahalleler (mahalleler tablosu)

        async loadIlceler() {
            if (!this.selectedIl) {
                this.ilceler = [];
                this.mahalleler = [];
                return;
            }

            try {
                const response = await fetch(`/api/location/districts/${this.selectedIl}`);
                const result = await response.json();

                if (result.success && result.data) {
                    this.ilceler = result.data;
                } else {
                    this.ilceler = [];
                }

                this.mahalleler = [];
            } catch (error) {
                console.error('İlçe yükleme hatası:', error);
                this.ilceler = [];
            }
        },

        async loadMahalleler() {
            if (!this.selectedIlce) {
                this.mahalleler = [];
                return;
            }

            try {
                const response = await fetch(`/api/location/neighborhoods/${this.selectedIlce}`);
                const result = await response.json();

                if (result.success && result.data) {
                    this.mahalleler = result.data;
                } else {
                    this.mahalleler = [];
                }
            } catch (error) {
                console.error('Mahalle yükleme hatası:', error);
                this.mahalleler = [];
            }
        },
    };
};

// Export functions for use in other modules - ADVANCED v2.0
window.IlanCreateLocation = {
    // Main initialization
    initializeLocation,

    // Basic functions
    loadIlceler,
    loadMahalleler,
    getCurrentLocation,
    searchAddress,
    initializeMap,
    validateLocation,
    advancedLocationManager: window.advancedLocationManager,

    // 🗺️ V2.0 Leaflet Features
    focusMapOnProvince,
};

// API Status Indicator Helper
function showApiStatusIndicator(message = 'API Kurulum Gerekli') {
    const indicator = document.getElementById('api-status-indicator');
    if (indicator) {
        indicator.classList.remove('hidden');
        indicator.title = message;
        console.warn('🔑 Google Maps API Status:', message);
    }
}

// Global function export
window.showApiStatusIndicator = showApiStatusIndicator;

// Console error listener for Google Maps API errors
const originalConsoleError = console.error;
console.error = function (...args) {
    const message = args.join(' ');

    // Detect Google Maps API authorization errors
    if (
        message.includes('Geocoding Service: This API project is not authorized') ||
        message.includes('This API project is not authorized to use this API')
    ) {
        showApiStatusIndicator('Geocoding API yetkilendirme hatası');
    }

    // Call original console.error
    originalConsoleError.apply(console, args);
};
