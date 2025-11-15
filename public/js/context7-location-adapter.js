/*
 * Context7 Location Adapter
 * Yalıhan Bekçi / Context7 standartlarına uygun tekleştirilmiş harita arayüzü
 * Sağlayıcılar: Advanced Leaflet (tercih), Basic Leaflet, (opsiyonel) Google
 */
(function () {
    class Context7LocationAdapter {
        constructor(options = {}) {
            this.options = {
                latFieldIds: ['latitude', 'enlem'],
                lngFieldIds: ['longitude', 'boylam'],
                provinceId: 'il_id',
                districtId: 'ilce_id',
                neighborhoodId: 'mahalle_id',
                mapContainerIds: ['map', 'location_map'],
                ...options,
            };

            // Defaults from meta
            const latMeta = document.querySelector('meta[name="location-default-latitude"]');
            const lngMeta = document.querySelector('meta[name="location-default-longitude"]');
            const zoomMeta = document.querySelector('meta[name="location-default-zoom"]');
            this.defaults = {
                center: latMeta && lngMeta
                    ? [parseFloat(latMeta.content), parseFloat(lngMeta.content)]
                    : [41.0082, 28.9784],
                zoom: zoomMeta ? parseInt(zoomMeta.content) : 13,
            };

            // Provider: 'leaflet-advanced' | 'leaflet-basic' | 'google' | 'none'
            this.provider = 'none';
        }

        init() {
            this.detectProvider();
            this.bindMapClickToForm();
            // Optional: align form selects later if needed
            console.log('[C7-Location] provider =', this.provider);
        }

        detectProvider() {
            // Prefer advanced Leaflet
            if (window.advancedMap && document.getElementById(this.findMapContainerId())) {
                this.provider = 'leaflet-advanced';
                return;
            }
            // Fallback to basic Leaflet
            if (window.mapManager && document.getElementById(this.findMapContainerId())) {
                this.provider = 'leaflet-basic';
                return;
            }
            // Optional Google provider (only if map/service globally hazır)
            if (window.google && document.getElementById('location_map')) {
                this.provider = 'google';
                return;
            }
            this.provider = 'none';
        }

        findMapContainerId() {
            return this.options.mapContainerIds.find((id) => document.getElementById(id));
        }

        setMarker(lat, lng, title = 'Seçilen Konum') {
            switch (this.provider) {
                case 'leaflet-advanced':
                    if (typeof window.advancedMap.setMarker === 'function') {
                        window.advancedMap.setMarker(lat, lng, title);
                    }
                    break;
                case 'leaflet-basic':
                    if (typeof window.mapManager.setMarker === 'function') {
                        window.mapManager.setMarker(lat, lng, title);
                    }
                    break;
                case 'google':
                    // Google servisiniz global değilse sadece formu güncelle
                    break;
                default:
                    break;
            }
            this.updateFormCoordinates(lat, lng);
        }

        async searchAddress(query) {
            if (!query || query.trim().length < 2) return [];
            switch (this.provider) {
                case 'leaflet-advanced':
                    if (typeof window.advancedMap.searchAndCenterMap === 'function') {
                        return window.advancedMap.searchAndCenterMap(query);
                    }
                    break;
                case 'leaflet-basic':
                    if (typeof window.mapManager.searchAddress === 'function') {
                        return window.mapManager.searchAddress(query);
                    }
                    break;
                default:
                    // Fallback to Nominatim
                    return this.nominatimSearch(query);
            }
            return [];
        }

        async reverseGeocode(lat, lng) {
            // Prefer provider reverse if exists; otherwise Nominatim fallback
            if (this.provider === 'leaflet-basic' && typeof window.mapManager.reverseGeocode === 'function') {
                return window.mapManager.reverseGeocode(lat, lng);
            }
            return this.nominatimReverse(lat, lng);
        }

        async nominatimSearch(query) {
            try {
                const resp = await fetch('/api/geo/geocode', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                    body: JSON.stringify({ query, limit: 1 }),
                });
                if (!resp.ok) throw new Error('Geocode proxy error');
                const payload = await resp.json();
                const results = payload.data || [];
                if (results && results.length > 0) {
                    const lat = parseFloat(results[0].lat);
                    const lng = parseFloat(results[0].lon);
                    this.setMarker(lat, lng, results[0].display_name);
                }
                return results;
            } catch (e) {
                console.error('[C7-Location] Geocode proxy error', e);
                return [];
            }
        }

        async nominatimReverse(lat, lng) {
            try {
                const resp = await fetch('/api/geo/reverse-geocode', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                    body: JSON.stringify({ latitude: lat, longitude: lng }),
                });
                if (!resp.ok) throw new Error('Reverse proxy error');
                const payload = await resp.json();
                return payload.data || null;
            } catch (e) {
                console.error('[C7-Location] Reverse proxy error', e);
                return null;
            }
        }

        updateFormCoordinates(lat, lng) {
            this.options.latFieldIds.forEach((id) => {
                const el = document.getElementById(id);
                if (el) {
                    el.value = Number(lat).toFixed(6);
                    el.dispatchEvent(new Event('change'));
                }
            });
            this.options.lngFieldIds.forEach((id) => {
                const el = document.getElementById(id);
                if (el) {
                    el.value = Number(lng).toFixed(6);
                    el.dispatchEvent(new Event('change'));
                }
            });
        }

        bindMapClickToForm() {
            // Leaflet advanced: already binds; just add passive helper if needed
            if (this.provider === 'leaflet-advanced') {
                // No-op: advanced manager already updates coordinates via updateFormCoordinates
                return;
            }
            if (this.provider === 'leaflet-basic' && window.mapManager && window.mapManager.map) {
                window.mapManager.map.on('click', (e) => {
                    this.setMarker(e.latlng.lat, e.latlng.lng, 'Seçilen Konum');
                    this.reverseGeocode(e.latlng.lat, e.latlng.lng);
                });
            }
        }
    }

    // Global bootstrap
    document.addEventListener('DOMContentLoaded', () => {
        try {
            const hasMap = ['map', 'location_map'].some((id) => document.getElementById(id));
            if (!hasMap) return;
            window.c7Location = new Context7LocationAdapter();
            window.c7Location.init();
            console.log('✅ Context7 Location Adapter aktif');
        } catch (e) {
            console.error('Context7 Location Adapter init failed', e);
        }
    });
})();
