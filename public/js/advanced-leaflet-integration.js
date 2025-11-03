/**
 * Advanced Leaflet.js OpenStreetMap System v2.0
 * Multi-layer, Drag & Drop, Satellite View Support
 * Context7 Compatible with Neo Design System
 */

class AdvancedLeafletManager {
    constructor(containerId = "map", options = {}) {
        this.containerId = containerId;
        this.map = null;
        this.markers = [];
        this.currentLocationMarker = null;
        this.nearbyMarkers = [];
        this.drawingTools = null;
        this.measureControl = null;

        // Map layers
        this.baseLayers = {};
        this.overlayLayers = {};
        this.layerControl = null;

        // Rate limiting for Overpass API
        this.lastApiCall = 0;
        this.apiCallDelay = 1000; // 1 second between API calls
        this.apiCallQueue = [];
        this.processingQueue = false;

        // Default options
        this.options = {
            center: [41.0082, 28.9784], // Istanbul
            zoom: 13,
            enableSatellite: true,
            enableDrawing: true,
            enableMeasurement: true,
            enableSearch: true,
            enableGeolocation: true,
            ...options,
        };

        this.init();
    }

    /**
     * Initialize advanced map with multiple layers
     */
    init() {
        const container = document.getElementById(this.containerId);
        if (!container) {
            console.error(`Map container '${this.containerId}' not found`);
            return;
        }

        // Check if map is already initialized
        if (container._leaflet_id) {
            console.warn(
                `Map container '${this.containerId}' already initialized, removing existing instance...`
            );
            // Remove existing map instance
            if (container._leaflet_map) {
                container._leaflet_map.remove();
            }
            // Clear all Leaflet-related data
            delete container._leaflet_id;
            delete container._leaflet_map;
            // Clear container content
            container.innerHTML = "";
            console.log(
                `✅ Map container '${this.containerId}' cleared successfully`
            );
        }

        // Create map
        this.map = L.map(this.containerId, {
            center: this.options.center,
            zoom: this.options.zoom,
            zoomControl: false, // Custom zoom control
            fullscreenControl: true,
        });

        // Initialize base layers
        this.initBaseLayers();

        // Initialize overlay layers
        this.initOverlayLayers();

        // Add layer control
        this.initLayerControl();

        // Add custom controls
        this.initCustomControls();

        // Initialize drawing tools
        if (this.options.enableDrawing) {
            this.initDrawingTools();
        }

        // Initialize measurement tools
        if (this.options.enableMeasurement) {
            this.initMeasurementTools();
        }

        // Initialize search
        if (this.options.enableSearch) {
            this.initAdvancedSearch();
        }

        // Initialize geolocation
        if (this.options.enableGeolocation) {
            this.initGeolocation();
        }

        // Set Turkey bounds
        this.setTurkeyBounds();

        // Setup event listeners
        this.setupEventListeners();

        console.log("✅ Advanced Leaflet Map Manager initialized");
    }

    /**
     * Initialize base map layers
     */
    initBaseLayers() {
        // OpenStreetMap Standard
        this.baseLayers["🗺️ Standart Harita"] = L.tileLayer(
            "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
            {
                attribution: "© OpenStreetMap contributors",
                maxZoom: 19,
            }
        );

        // Satellite View (Esri)
        if (this.options.enableSatellite) {
            this.baseLayers["🛰️ Uydu Görünümü"] = L.tileLayer(
                "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}",
                {
                    attribution:
                        "© Esri, DigitalGlobe, GeoEye, Earthstar Geographics",
                    maxZoom: 18,
                }
            );
        }

        // Topographic Map
        this.baseLayers["🏔️ Topografik"] = L.tileLayer(
            "https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png",
            {
                attribution: "© OpenTopoMap contributors",
                maxZoom: 17,
            }
        );

        // CartoDB Positron (Light theme)
        this.baseLayers["🌟 Açık Tema"] = L.tileLayer(
            "https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png",
            {
                attribution: "© CartoDB, © OpenStreetMap contributors",
                maxZoom: 19,
            }
        );

        // CartoDB Dark Matter
        this.baseLayers["🌙 Koyu Tema"] = L.tileLayer(
            "https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png",
            {
                attribution: "© CartoDB, © OpenStreetMap contributors",
                maxZoom: 19,
            }
        );

        // Add default layer
        this.baseLayers["🗺️ Standart Harita"].addTo(this.map);
    }

    /**
     * Initialize overlay layers
     */
    initOverlayLayers() {
        // Traffic layer (would need a traffic service)
        // this.overlayLayers['🚦 Trafik'] = L.tileLayer(...);

        // Points of Interest markers group
        this.overlayLayers["📍 Yakındaki Yerler"] = L.layerGroup();

        // User drawings
        this.overlayLayers["✏️ Çizimler"] = L.layerGroup();
    }

    /**
     * Initialize layer control panel
     */
    initLayerControl() {
        this.layerControl = L.control
            .layers(this.baseLayers, this.overlayLayers, {
                position: "topright",
                collapsed: false,
            })
            .addTo(this.map);
    }

    /**
     * Initialize custom controls
     */
    initCustomControls() {
        // Custom zoom control
        L.control
            .zoom({
                position: "topleft",
            })
            .addTo(this.map);

        // Fullscreen control
        if (L.control.fullscreen) {
            L.control
                .fullscreen({
                    position: "topleft",
                })
                .addTo(this.map);
        }

        // Scale control
        L.control
            .scale({
                position: "bottomleft",
                metric: true,
                imperial: false,
            })
            .addTo(this.map);

        // Add custom GPS button
        this.addGPSControl();

        // Add map type switcher
        this.addMapTypeSwitcher();
    }

    /**
     * Add GPS location control
     */
    addGPSControl() {
        const gpsControl = L.Control.extend({
            onAdd: (map) => {
                const container = L.DomUtil.create(
                    "div",
                    "leaflet-bar leaflet-control leaflet-control-custom"
                );
                container.style.backgroundColor = "white";
                container.style.width = "34px";
                container.style.height = "34px";
                container.style.cursor = "pointer";
                container.innerHTML = "📍";
                container.title = "Konumumu Bul";

                container.onclick = () => {
                    this.getCurrentLocation();
                };

                return container;
            },
        });

        new gpsControl({ position: "topleft" }).addTo(this.map);
    }

    /**
     * Add quick map type switcher
     */
    addMapTypeSwitcher() {
        const mapTypeControl = L.Control.extend({
            onAdd: (map) => {
                const container = L.DomUtil.create(
                    "div",
                    "leaflet-bar leaflet-control"
                );
                container.innerHTML = `
                    <div class="map-type-switcher">
                        <button onclick="window.advancedMap.switchToSatellite()" title="Uydu Görünümü">🛰️</button>
                        <button onclick="window.advancedMap.switchToStandard()" title="Standart Harita">🗺️</button>
                        <button onclick="window.advancedMap.switchToTopo()" title="Topografik">🏔️</button>
                    </div>
                `;
                container.style.backgroundColor = "white";

                return container;
            },
        });

        new mapTypeControl({ position: "topright" }).addTo(this.map);
    }

    /**
     * Switch map layers
     */
    switchToSatellite() {
        this.map.eachLayer((layer) => {
            if (
                this.baseLayers["🗺️ Standart Harita"] === layer ||
                this.baseLayers["🏔️ Topografik"] === layer ||
                this.baseLayers["🌟 Açık Tema"] === layer ||
                this.baseLayers["🌙 Koyu Tema"] === layer
            ) {
                this.map.removeLayer(layer);
            }
        });
        this.baseLayers["🛰️ Uydu Görünümü"].addTo(this.map);
    }

    switchToStandard() {
        this.map.eachLayer((layer) => {
            if (
                this.baseLayers["🛰️ Uydu Görünümü"] === layer ||
                this.baseLayers["🏔️ Topografik"] === layer ||
                this.baseLayers["🌟 Açık Tema"] === layer ||
                this.baseLayers["🌙 Koyu Tema"] === layer
            ) {
                this.map.removeLayer(layer);
            }
        });
        this.baseLayers["🗺️ Standart Harita"].addTo(this.map);
    }

    switchToTopo() {
        this.map.eachLayer((layer) => {
            if (
                this.baseLayers["🛰️ Uydu Görünümü"] === layer ||
                this.baseLayers["🗺️ Standart Harita"] === layer ||
                this.baseLayers["🌟 Açık Tema"] === layer ||
                this.baseLayers["🌙 Koyu Tema"] === layer
            ) {
                this.map.removeLayer(layer);
            }
        });
        this.baseLayers["🏔️ Topografik"].addTo(this.map);
    }

    /**
     * Initialize drawing tools
     */
    initDrawingTools() {
        if (!L.Control.Draw) {
            console.warn("Leaflet.draw not loaded, drawing tools disabled");
            return;
        }

        const drawControl = new L.Control.Draw({
            draw: {
                polygon: {
                    allowIntersection: false,
                    showArea: true,
                    drawError: {
                        color: "#e1e100",
                        message:
                            "<strong>Hata:</strong> Şekil kendi üzerine gelemez!",
                    },
                    shapeOptions: {
                        color: "#97009c",
                    },
                },
                rectangle: {
                    shapeOptions: {
                        color: "#97009c",
                    },
                },
                circle: {
                    shapeOptions: {
                        color: "#662d91",
                    },
                },
                marker: true,
                polyline: {
                    shapeOptions: {
                        color: "#f357a1",
                        weight: 3,
                    },
                },
            },
            edit: {
                featureGroup: this.overlayLayers["✏️ Çizimler"],
                remove: true,
            },
        });

        this.map.addControl(drawControl);

        // Drawing event handlers
        this.map.on(L.Draw.Event.CREATED, (e) => {
            const layer = e.layer;
            this.overlayLayers["✏️ Çizimler"].addLayer(layer);
            this.showNotification("Çizim eklendi", "success");
        });

        this.map.on(L.Draw.Event.EDITED, (e) => {
            this.showNotification("Çizim düzenlendi", "info");
        });

        this.map.on(L.Draw.Event.DELETED, (e) => {
            this.showNotification("Çizim silindi", "warning");
        });
    }

    /**
     * Initialize measurement tools
     */
    initMeasurementTools() {
        if (L.Control.Measure) {
            L.control
                .measure({
                    position: "topleft",
                    primaryLengthUnit: "kilometers",
                    secondaryLengthUnit: "meters",
                    primaryAreaUnit: "sqmeters",
                    secondaryAreaUnit: undefined,
                    activeColor: "#db4a29",
                    completedColor: "#9b2d14",
                })
                .addTo(this.map);
        }
    }

    /**
     * Initialize advanced search with autocomplete
     */
    initAdvancedSearch() {
        // Create search control
        const searchControl = L.Control.extend({
            onAdd: (map) => {
                const container = L.DomUtil.create(
                    "div",
                    "leaflet-control-search neo-bg-white neo-rounded-lg neo-shadow-lg"
                );
                container.innerHTML = `
                    <div class="neo-p-4">
                        <div class="neo-relative">
                            <input
                                type="text"
                                id="advanced-search-input"
                                placeholder="Adres, yer adı ara..."
                                class="neo-w-full neo-px-4 neo-py-2 neo-border neo-border-gray-300 neo-rounded-lg focus:neo-border-blue-500 focus:neo-outline-none"
                            />
                            <div id="search-suggestions" class="neo-absolute neo-top-full neo-left-0 neo-right-0 neo-bg-white neo-border neo-border-gray-200 neo-rounded-lg neo-shadow-lg neo-hidden neo-z-50 neo-max-h-60 neo-overflow-y-auto">
                            </div>
                        </div>
                        <div class="neo-flex neo-gap-2 neo-mt-2">
                            <button onclick="window.advancedMap.searchNearby('restaurant')" class="neo-btn neo-btn-sm neo-btn-outline">🍽️ Restoran</button>
                            <button onclick="window.advancedMap.searchNearby('hospital')" class="neo-btn neo-btn-sm neo-btn-outline">🏥 Hastane</button>
                            <button onclick="window.advancedMap.searchNearby('gas_station')" class="neo-btn neo-btn-sm neo-btn-outline">⛽ Benzinlik</button>
                        </div>
                    </div>
                `;

                return container;
            },
        });

        new searchControl({ position: "topright" }).addTo(this.map);

        // Setup search functionality
        this.setupAdvancedSearch();
    }

    /**
     * Setup advanced search with debounce
     */
    setupAdvancedSearch() {
        const searchInput = document.getElementById("advanced-search-input");
        const suggestions = document.getElementById("search-suggestions");
        let searchTimeout;

        if (!searchInput) return;

        searchInput.addEventListener("input", (e) => {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();

            if (query.length < 3) {
                suggestions.classList.add("neo-hidden");
                return;
            }

            searchTimeout = setTimeout(() => {
                this.performSearch(query, suggestions);
            }, 500);
        });

        // Hide suggestions when clicking outside
        document.addEventListener("click", (e) => {
            if (!e.target.closest(".leaflet-control-search")) {
                suggestions.classList.add("neo-hidden");
            }
        });
    }

    /**
     * Perform search with Nominatim API
     */
    async performSearch(query, suggestionsContainer) {
        try {
            const response = await fetch(
                `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(
                    query
                )}&countrycodes=tr&limit=5&addressdetails=1`
            );

            const results = await response.json();
            this.displaySearchSuggestions(results, suggestionsContainer);
        } catch (error) {
            console.error("Search failed:", error);
        }
    }

    /**
     * Display search suggestions
     */
    displaySearchSuggestions(results, container) {
        container.innerHTML = "";

        if (results.length === 0) {
            container.innerHTML =
                '<div class="neo-p-3 neo-text-gray-500">Sonuç bulunamadı</div>';
            container.classList.remove("neo-hidden");
            return;
        }

        results.forEach((result) => {
            const item = document.createElement("div");
            item.className =
                "neo-p-3 neo-cursor-pointer hover:neo-bg-gray-50 neo-border-b neo-border-gray-100 last:neo-border-0";
            item.innerHTML = `
                <div class="neo-font-medium">${
                    result.display_name.split(",")[0]
                }</div>
                <div class="neo-text-sm neo-text-gray-600">${
                    result.display_name
                }</div>
            `;

            item.addEventListener("click", () => {
                this.selectSearchResult(result);
                container.classList.add("neo-hidden");
            });

            container.appendChild(item);
        });

        container.classList.remove("neo-hidden");
    }

    /**
     * Select search result and add marker
     */
    selectSearchResult(result) {
        const lat = parseFloat(result.lat);
        const lng = parseFloat(result.lon);

        // Add marker
        const marker = this.addMarker(
            lat,
            lng,
            result.display_name.split(",")[0],
            {
                draggable: true,
                color: "blue",
            }
        );

        // Center map
        this.map.setView([lat, lng], 16);

        // Update form and analyze environment
        this.autoAnalyzeLocation(lat, lng);

        this.showNotification(
            "Konum eklendi - Yakın çevre analizi başlatılıyor...",
            "success"
        );
    }

    /**
     * Search nearby places
     */
    async searchNearby(category) {
        const center = this.map.getCenter();
        const radius = 2000; // 2km

        try {
            const response = await fetch(
                `https://overpass-api.de/api/interpreter?data=[out:json][timeout:25];(node["amenity"="${category}"](around:${radius},${center.lat},${center.lng}););out;`
            );

            const data = await response.json();
            this.displayNearbyPlaces(data.elements, category);
        } catch (error) {
            console.error("Nearby search failed:", error);
            this.showNotification("Yakındaki yerler yüklenemedi", "error");
        }
    }

    /**
     * Display nearby places on map
     */
    displayNearbyPlaces(places, category) {
        // Clear previous nearby markers
        this.clearNearbyMarkers();

        const categoryEmoji = {
            restaurant: "🍽️",
            hospital: "🏥",
            gas_station: "⛽",
            pharmacy: "💊",
            bank: "🏦",
            school: "🏫",
        };

        places.forEach((place) => {
            if (place.lat && place.lon) {
                const marker = L.marker([place.lat, place.lon], {
                    icon: L.divIcon({
                        html: categoryEmoji[category] || "📍",
                        className: "nearby-marker",
                        iconSize: [25, 25],
                    }),
                }).addTo(this.overlayLayers["📍 Yakındaki Yerler"]);

                const name = place.tags?.name || `${category} (isimsiz)`;
                marker.bindPopup(
                    `<strong>${name}</strong><br><small>${category}</small>`
                );

                this.nearbyMarkers.push(marker);
            }
        });

        this.showNotification(
            `${places.length} ${category} bulundu`,
            "success"
        );
    }

    /**
     * Add draggable marker
     */
    addMarker(lat, lng, title = "Marker", options = {}) {
        const marker = L.marker([lat, lng], {
            draggable: options.draggable || true,
            title: title,
        }).addTo(this.map);

        if (options.popup !== false) {
            marker.bindPopup(title).openPopup();
        }

        // Drag event
        if (options.draggable !== false) {
            marker.on("dragend", (e) => {
                const position = e.target.getLatLng();
                this.updateFormCoordinates(position.lat, position.lng);
                this.showNotification("Marker konumu güncellendi", "info");
            });
        }

        this.markers.push(marker);
        return marker;
    }

    /**
     * Set marker with animation
     */
    setMarker(lat, lng, title = "Konum") {
        // Clear existing markers
        this.clearMarkers();

        // Add new marker with popup
        const marker = this.addMarker(lat, lng, title);

        // Animate to location
        this.map.flyTo([lat, lng], 16, {
            animate: true,
            duration: 1.0,
        });

        return marker;
    }

    /**
     * Clear all markers
     */
    clearMarkers() {
        this.markers.forEach((marker) => {
            this.map.removeLayer(marker);
        });
        this.markers = [];
    }

    /**
     * Clear nearby markers
     */
    clearNearbyMarkers() {
        this.nearbyMarkers.forEach((marker) => {
            this.overlayLayers["📍 Yakındaki Yerler"].removeLayer(marker);
        });
        this.nearbyMarkers = [];
    }

    /**
     * Initialize geolocation features
     */
    initGeolocation() {
        console.log("✅ Geolocation features initialized");
    }

    /**
     * Get current GPS location
     */
    getCurrentLocation() {
        if (!navigator.geolocation) {
            this.showNotification(
                "Tarayıcınız konum desteği sunmuyor",
                "error"
            );
            return;
        }

        this.showNotification("Konum alınıyor...", "info");

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                // Remove existing location marker
                if (this.currentLocationMarker) {
                    this.map.removeLayer(this.currentLocationMarker);
                }

                // Add current location marker
                this.currentLocationMarker = L.marker([lat, lng], {
                    icon: L.divIcon({
                        html: "📍",
                        className: "current-location-marker",
                        iconSize: [30, 30],
                    }),
                }).addTo(this.map);

                this.currentLocationMarker
                    .bindPopup("Mevcut Konumunuz")
                    .openPopup();

                // Center map
                this.map.setView([lat, lng], 16);

                // Update form and analyze environment
                this.autoAnalyzeLocation(lat, lng);

                this.showNotification(
                    "📍 GPS konumu alındı - Yakın çevre analizi başlatıldı",
                    "success"
                );
            },
            (error) => {
                let message = "Konum alınamadı";
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        message = "Konum izni reddedildi";
                        break;
                    case error.POSITION_UNAVAILABLE:
                        message = "Konum bilgisi mevcut değil";
                        break;
                    case error.TIMEOUT:
                        message = "Konum alma zaman aşımı";
                        break;
                }
                this.showNotification(message, "error");
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 60000,
            }
        );
    }

    /**
     * Set Turkey bounds
     */
    setTurkeyBounds() {
        const turkeyBounds = L.latLngBounds([35.5, 25.5], [42.5, 45.0]);
        this.map.setMaxBounds(turkeyBounds);
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Map click event with AI analysis
        this.map.on("click", (e) => {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;

            this.setMarker(lat, lng, "Seçilen Konum");
            this.autoAnalyzeLocation(lat, lng);

            this.showNotification(
                "🎯 Konum seçildi - AI analizi başlıyor...",
                "info"
            );
        });

        // Map zoom event
        this.map.on("zoomend", () => {
            console.log("Current zoom level:", this.map.getZoom());
        });
    }

    /**
     * Update form coordinates if form exists
     */
    updateFormCoordinates(lat, lng) {
        const latField = document.getElementById("latitude");
        const lngField = document.getElementById("longitude");

        if (latField) latField.value = lat.toFixed(7);
        if (lngField) lngField.value = lng.toFixed(7);

        // Trigger change event
        if (latField) latField.dispatchEvent(new Event("change"));
        if (lngField) lngField.dispatchEvent(new Event("change"));
    }

    /**
     * Show notification
     */
    showNotification(message, type = "info") {
        // Use existing notification system or create simple one
        if (window.toast) {
            window.toast[type](message);
        } else {
            console.log(`${type.toUpperCase()}: ${message}`);

            // Simple notification fallback
            const notification = document.createElement("div");
            notification.className = `neo-fixed neo-top-4 neo-right-4 neo-p-4 neo-rounded-lg neo-shadow-lg neo-z-50 ${
                type === "success"
                    ? "neo-bg-green-500 neo-text-white"
                    : type === "error"
                    ? "neo-bg-red-500 neo-text-white"
                    : type === "warning"
                    ? "neo-bg-yellow-500 neo-text-black"
                    : "neo-bg-blue-500 neo-text-white"
            }`;
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    }

    /**
     * Get map instance
     */
    getMap() {
        return this.map;
    }

    /**
     * Export map as image
     */
    exportMapImage() {
        if (this.map) {
            // This would require additional libraries like leaflet-image
            console.log(
                "Map export functionality requires leaflet-image plugin"
            );
        }
    }

    /**
     * 🎯 Akıllı Yakın Çevre Analizi Sistemi
     * AI-powered nearby places analysis
     */
    async analyzeNearbyEnvironment(lat, lng) {
        console.log("🤖 Starting AI-powered environment analysis...");

        const results = {
            transportation: [],
            healthcare: [],
            education: [],
            shopping: [],
            recreation: [],
            coastal: [],
        };

        try {
            // Search different categories
            const categories = {
                transportation: [
                    "public_transport",
                    "subway_station",
                    "bus_station",
                ],
                healthcare: ["hospital", "clinic", "pharmacy"],
                education: ["school", "university", "kindergarten"],
                shopping: ["supermarket", "shopping_centre", "marketplace"],
                recreation: ["park", "playground", "sports_centre"],
                coastal: ["beach", "marina", "waterfront"],
            };

            for (const [category, amenities] of Object.entries(categories)) {
                for (const amenity of amenities) {
                    const places = await this.searchOverpassAPI(
                        lat,
                        lng,
                        amenity,
                        3000
                    );
                    results[category] = [...results[category], ...places];
                }
            }

            // Calculate distances and update form
            this.updateNearbyDistances(lat, lng, results);

            // Display markers on map
            this.displayEnvironmentMarkers(results);

            return results;
        } catch (error) {
            console.error("Environment analysis failed:", error);
            return results;
        }
    }

    /**
     * Rate-limited API call queue processor
     */
    async processApiQueue() {
        if (this.processingQueue || this.apiCallQueue.length === 0) {
            return;
        }

        this.processingQueue = true;

        while (this.apiCallQueue.length > 0) {
            const { resolve, reject, query } = this.apiCallQueue.shift();

            const now = Date.now();
            const timeSinceLastCall = now - this.lastApiCall;

            if (timeSinceLastCall < this.apiCallDelay) {
                await new Promise((resolve) =>
                    setTimeout(resolve, this.apiCallDelay - timeSinceLastCall)
                );
            }

            try {
                this.lastApiCall = Date.now();
                const result = await this.makeOverpassAPICall(query);
                resolve(result);
            } catch (error) {
                reject(error);
            }
        }

        this.processingQueue = false;
    }

    /**
     * Actual Overpass API call
     */
    async makeOverpassAPICall(query) {
        const response = await fetch(
            `https://overpass-api.de/api/interpreter?data=${encodeURIComponent(
                query
            )}`
        );

        if (!response.ok) {
            if (response.status === 429) {
                throw new Error("Rate limited - please wait");
            }
            throw new Error("Overpass API error");
        }

        return await response.json();
    }

    /**
     * Search Overpass API for specific amenities (with rate limiting)
     */
    async searchOverpassAPI(lat, lng, amenity, radius = 2000) {
        try {
            const query = `
                [out:json][timeout:25];
                (
                    node["amenity"="${amenity}"](around:${radius},${lat},${lng});
                    way["amenity"="${amenity}"](around:${radius},${lat},${lng});
                    relation["amenity"="${amenity}"](around:${radius},${lat},${lng});
                );
                out center;
            `;

            // Add to queue and wait for result
            const data = await new Promise((resolve, reject) => {
                this.apiCallQueue.push({ resolve, reject, query });
                this.processApiQueue();
            });

            return data.elements
                .map((element) => ({
                    id: element.id,
                    lat: element.lat || element.center?.lat,
                    lng: element.lon || element.center?.lon,
                    name: element.tags?.name || `${amenity} (unnamed)`,
                    amenity: amenity,
                    tags: element.tags || {},
                }))
                .filter((place) => place.lat && place.lng);
        } catch (error) {
            console.error(`Overpass API search failed for ${amenity}:`, error);
            return [];
        }
    }

    /**
     * Calculate distances and update form fields
     */
    updateNearbyDistances(centerLat, centerLng, results) {
        const calculateDistance = (lat1, lng1, lat2, lng2) => {
            const R = 6371; // Earth's radius in km
            const dLat = ((lat2 - lat1) * Math.PI) / 180;
            const dLng = ((lng2 - lng1) * Math.PI) / 180;
            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos((lat1 * Math.PI) / 180) *
                    Math.cos((lat2 * Math.PI) / 180) *
                    Math.sin(dLng / 2) *
                    Math.sin(dLng / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c; // Distance in km
        };

        const fieldMapping = {
            transportation: "metro_mesafe",
            shopping: "avm_mesafe",
            healthcare: "hastane_mesafe",
            education: "okul_mesafe",
            recreation: "park_mesafe",
            coastal: "deniz_mesafe",
        };

        // Find closest place in each category
        for (const [category, places] of Object.entries(results)) {
            if (places.length === 0) continue;

            const closestPlace = places.reduce((closest, place) => {
                const distance = calculateDistance(
                    centerLat,
                    centerLng,
                    place.lat,
                    place.lng
                );
                return !closest || distance < closest.distance
                    ? { ...place, distance }
                    : closest;
            }, null);

            if (closestPlace && fieldMapping[category]) {
                const field = document.querySelector(
                    `input[name="${fieldMapping[category]}"]`
                );
                if (field) {
                    const distanceText =
                        closestPlace.distance < 1
                            ? `${Math.round(closestPlace.distance * 1000)}m - ${
                                  closestPlace.name
                              }`
                            : `${closestPlace.distance.toFixed(1)}km - ${
                                  closestPlace.name
                              }`;
                    field.value = distanceText;
                    field.style.color =
                        closestPlace.distance < 0.5
                            ? "#10b981"
                            : closestPlace.distance < 2
                            ? "#f59e0b"
                            : "#ef4444";
                }
            }
        }

        this.showNotification(`🎯 Yakın çevre analizi tamamlandı`, "success");
    }

    /**
     * Display environment markers on map
     */
    displayEnvironmentMarkers(results) {
        // Clear existing nearby markers
        this.clearNearbyMarkers();

        const categoryIcons = {
            transportation: { icon: "🚇", color: "#3b82f6" },
            healthcare: { icon: "🏥", color: "#ef4444" },
            education: { icon: "🏫", color: "#10b981" },
            shopping: { icon: "🛒", color: "#8b5cf6" },
            recreation: { icon: "🌳", color: "#059669" },
            coastal: { icon: "🌊", color: "#0891b2" },
        };

        for (const [category, places] of Object.entries(results)) {
            const categoryConfig = categoryIcons[category];
            if (!categoryConfig) continue;

            places.slice(0, 5).forEach((place) => {
                // Limit to 5 per category
                const marker = L.marker([place.lat, place.lng], {
                    icon: L.divIcon({
                        html: `<div style="background: ${categoryConfig.color}; color: white; border-radius: 50%; width: 25px; height: 25px; display: flex; align-items: center; justify-content: center; font-size: 12px; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">${categoryConfig.icon}</div>`,
                        className: "environment-marker",
                        iconSize: [25, 25],
                    }),
                }).addTo(this.overlayLayers["📍 Yakındaki Yerler"]);

                marker.bindPopup(`
                    <div class="neo-p-3">
                        <div class="neo-font-semibold neo-text-gray-800">${
                            place.name
                        }</div>
                        <div class="neo-text-sm neo-text-gray-600 neo-mt-1">${category}</div>
                        <div class="neo-text-xs neo-text-gray-500 neo-mt-2">
                            ${
                                place.tags.address ||
                                "Adres bilgisi mevcut değil"
                            }
                        </div>
                    </div>
                `);

                this.nearbyMarkers.push(marker);
            });
        }
    }

    /**
     * Auto-search nearby places when location is selected
     */
    async autoAnalyzeLocation(lat, lng) {
        console.log("🤖 Auto-analyzing location...");

        // Update coordinates first
        this.updateFormCoordinates(lat, lng);

        // Start environment analysis
        setTimeout(async () => {
            await this.analyzeNearbyEnvironment(lat, lng);
            // Also call backend API for AI insights
            await this.callBackendAnalysis(lat, lng);
        }, 1000);
    }

    /**
     * Call backend API for comprehensive AI analysis
     */
    async callBackendAnalysis(lat, lng) {
        try {
            this.showNotification("🤖 AI analizi başlatılıyor...", "info");

            const response = await fetch(
                `/api/environment/analyze?lat=${lat}&lng=${lng}`,
                {
                    method: "GET",
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                }
            );

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const result = await response.json();

            if (result.success) {
                this.displayAIInsights(result.data);
                this.showNotification("✅ AI analizi tamamlandı!", "success");
            } else {
                throw new Error(result.message || "API error");
            }
        } catch (error) {
            console.error("Backend analysis failed:", error);
            this.showNotification(
                "⚠️ AI analizi yapılamadı, yerel analiz kullanılıyor",
                "warning"
            );
        }
    }

    /**
     * Display AI insights in the UI
     */
    displayAIInsights(data) {
        console.log("🎯 AI Analysis Results:", data);

        // Update scores in UI if available
        if (data.scores) {
            this.updateScoreIndicators(data.scores);
        }

        // Show recommendations
        if (data.recommendations && data.recommendations.length > 0) {
            this.showRecommendations(data.recommendations);
        }

        // Update investment score
        if (data.insights) {
            this.displayInsightsSummary(data.insights);
        }
    }

    /**
     * Update score indicators in nearby analysis section
     */
    updateScoreIndicators(scores) {
        const categoryMapping = {
            transportation: "metro_mesafe",
            shopping: "avm_mesafe",
            healthcare: "hastane_mesafe",
            education: "okul_mesafe",
            recreation: "park_mesafe",
            coastal: "deniz_mesafe",
        };

        for (const [category, fieldName] of Object.entries(categoryMapping)) {
            const field = document.querySelector(`input[name="${fieldName}"]`);
            if (field && scores[category] !== undefined) {
                const score = scores[category];
                const parent = field.closest(".bg-white");

                if (parent) {
                    // Add score indicator
                    let indicator = parent.querySelector(".ai-score-indicator");
                    if (!indicator) {
                        indicator = document.createElement("div");
                        indicator.className =
                            "ai-score-indicator text-xs font-medium mt-1";
                        field.parentNode.appendChild(indicator);
                    }

                    // Color based on score
                    let color = "text-red-500";
                    let emoji = "❌";
                    if (score >= 80) {
                        color = "text-green-500";
                        emoji = "✅";
                    } else if (score >= 60) {
                        color = "text-yellow-500";
                        emoji = "⚠️";
                    }

                    indicator.className = `ai-score-indicator text-xs font-medium mt-1 ${color}`;
                    indicator.innerHTML = `${emoji} AI Skor: ${Math.round(
                        score
                    )}/100`;
                }
            }
        }
    }

    /**
     * Show AI recommendations
     */
    showRecommendations(recommendations) {
        // Create or update recommendations panel
        let panel = document.getElementById("ai-recommendations-panel");
        if (!panel) {
            panel = document.createElement("div");
            panel.id = "ai-recommendations-panel";
            panel.className =
                "mt-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg border border-blue-200 dark:border-blue-800";

            // Find the environment analysis section
            const envSection = document.querySelector(
                ".bg-gradient-to-r.from-blue-50.to-indigo-50"
            );
            if (envSection && envSection.querySelector(".relative.z-10")) {
                envSection.querySelector(".relative.z-10").appendChild(panel);
            }
        }

        panel.innerHTML = `
            <div class="flex items-center mb-3">
                <i class="fas fa-robot text-blue-600 mr-2"></i>
                <h5 class="text-sm font-semibold text-gray-800 dark:text-gray-200">🤖 AI Önerileri</h5>
            </div>
            <div class="space-y-2">
                ${recommendations
                    .map(
                        (rec) => `
                    <div class="text-xs text-gray-600 dark:text-gray-400 flex items-start">
                        <i class="fas fa-lightbulb text-yellow-500 mr-2 mt-0.5 flex-shrink-0"></i>
                        <span>${rec}</span>
                    </div>
                `
                    )
                    .join("")}
            </div>
        `;
    }

    /**
     * Display insights summary
     */
    displayInsightsSummary(insights) {
        console.log("📊 AI Insights:", insights);

        if (insights.summary) {
            this.showNotification(`🧠 AI: ${insights.summary}`, "info");
        }
    }

    /**
     * Search and center map on location from address selection
     */
    async searchAndCenterMap(query) {
        if (!query || !this.map) {
            console.warn("🗺️ Harita veya sorgu eksik");
            return;
        }

        console.log("🔍 Adres aranıyor:", query);

        try {
            // Use Nominatim API for geocoding
            const encodedQuery = encodeURIComponent(query);
            const response = await fetch(
                `https://nominatim.openstreetmap.org/search?format=json&q=${encodedQuery}&limit=1&countrycodes=tr&addressdetails=1`
            );

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const results = await response.json();

            if (results && results.length > 0) {
                const result = results[0];
                const lat = parseFloat(result.lat);
                const lng = parseFloat(result.lon);

                console.log("📍 Konum bulundu:", {
                    lat,
                    lng,
                    display_name: result.display_name,
                });

                // Center map on the location
                this.map.setView([lat, lng], 15);

                // Clear existing markers
                this.clearMarkers();

                // Add marker for the selected location
                const marker = L.marker([lat, lng], {
                    icon: L.divIcon({
                        html: `<div style="background: #10b981; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-size: 16px; border: 3px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3);">📍</div>`,
                        className: "selected-location-marker",
                        iconSize: [30, 30],
                    }),
                }).addTo(this.map);

                marker.bindPopup(`
                    <div class="neo-p-3">
                        <div class="neo-font-semibold neo-text-gray-800">Seçilen Konum</div>
                        <div class="neo-text-sm neo-text-gray-600 neo-mt-1">${
                            result.display_name
                        }</div>
                        <div class="neo-text-xs neo-text-gray-500 neo-mt-2">
                            Koordinat: ${lat.toFixed(6)}, ${lng.toFixed(6)}
                        </div>
                    </div>
                `);

                this.markers.push(marker);

                // Update form coordinates
                this.updateFormCoordinates(lat, lng);

                // Auto-analyze nearby environment
                setTimeout(() => {
                    this.analyzeNearbyEnvironment(lat, lng);
                }, 1000);

                this.showNotification(
                    `📍 Konum güncellendi: ${result.display_name}`,
                    "success"
                );
            } else {
                console.warn("🚫 Konum bulunamadı:", query);
                this.showNotification(
                    "🚫 Konum bulunamadı. Lütfen daha spesifik bir adres deneyin.",
                    "warning"
                );
            }
        } catch (error) {
            console.error("🚨 Adres arama hatası:", error);
            this.showNotification(
                "🚨 Adres arama sırasında hata oluştu.",
                "error"
            );
        }
    }
}

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", function () {
    if (document.getElementById("map")) {
        window.advancedMap = new AdvancedLeafletManager("map", {
            enableSatellite: true,
            enableDrawing: true,
            enableMeasurement: true,
            enableSearch: true,
            enableGeolocation: true,
        });

        // Make it globally accessible
        window.Context7LeafletManager = window.advancedMap;

        console.log("🗺️ Advanced Leaflet Map loaded successfully");
    }
});

// CSS for custom controls (to be added to CSS file)
const customStyles = `
<style>
.leaflet-control-search {
    min-width: 300px;
}

.map-type-switcher {
    display: flex;
    flex-direction: column;
}

.map-type-switcher button {
    padding: 8px;
    border: none;
    background: white;
    cursor: pointer;
    border-bottom: 1px solid #ddd;
}

.map-type-switcher button:hover {
    background: #f0f0f0;
}

.map-type-switcher button:last-child {
    border-bottom: none;
}

.nearby-marker {
    background: white;
    border-radius: 50%;
    border: 2px solid #333;
    text-align: center;
    line-height: 21px;
    font-size: 16px;
}

.current-location-marker {
    background: #007bff;
    border-radius: 50%;
    text-align: center;
    line-height: 26px;
    font-size: 18px;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.3);
}
</style>
`;

// Inject styles
if (!document.getElementById("advanced-leaflet-styles")) {
    const styleElement = document.createElement("div");
    styleElement.id = "advanced-leaflet-styles";
    styleElement.innerHTML = customStyles;
    document.head.appendChild(styleElement);
}
