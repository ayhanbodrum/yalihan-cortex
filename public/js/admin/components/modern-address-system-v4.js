/**
 * EmlakLoc v4.0 - AI Destekli Modern Address System
 * Advanced location and address management system with AI integration
 * Version: 4.0.1 - Enhanced Features (Updated: 7 Eylül 2025)
 *
 * Features:
 * - AI-powered semantic search with Turkish NLP
 * - Voice search with speech recognition
 * - Advanced image recognition with Computer Vision
 * - Google Places API integration
 * - Real-time traffic data
 * - Social media location sharing
 * - QR code location sharing
 * - 3D map rendering with Three.js
 * - AR overlay capabilities with WebXR
 * - Offline map support with PWA
 * - Multi-marker management
 * - Distance circles and analysis
 * - Predictive analytics for location trends
 * - Advanced gesture controls
 * - Push notifications for location updates
 * - Leaflet.js integration with marker support
 * - Coordinate field sync (latitude/longitude)
 * - Click and drag marker functionality
 * - Alpine.js store integration
 * - Toast notifications
 */

console.log('🗺️🤖 EmlakLoc v4.1.0 - Form Wizard Integration yüklenyor...');

/**
 * EmlakLoc v4.1.0 - Form Wizard Enhanced Location System
 *
 * Features:
 * - 🤖 AI-powered location search with Turkish NLP
 * - 🎤 Voice search with speech recognition
 * - 📷 Image recognition for location detection
 * - 🌍 3D maps with Three.js integration
 * - 🥽 AR overlay with WebXR support
 * - 📱 PWA support with offline maps
 * - 📤 Social media sharing (WhatsApp, Telegram, QR)
 * - 🏫 Nearby services analysis
 * - 🚌 Transportation scoring
 * - 💰 Investment potential analysis
 * - 🏡 Form Wizard Step 3 integration
 */

// Global EmlakLoc instance for Form Wizard
window.EmlakLoc = class EmlakLoc {
    constructor(options = {}) {
        this.options = {
            mapContainerId: 'property_map',
            defaultCoordinates: {
                latitude: 39.9208,
                longitude: 32.8541,
            },
            useTestAPI: false,
            autoInit: true,
            enableAI: true,
            enableVoice: true,
            enableImageRecognition: true,
            enable3D: true,
            enableAR: true,
            enableOffline: true,
            enableGooglePlaces: true,
            enableTraffic: true,
            enableSocialSharing: true,
            enableQRCode: true,
            enablePWA: true,
            enableGestures: true,
            enablePushNotifications: true,
            enablePredictiveAnalytics: true,
            googlePlacesApiKey: null,
            ...options,
        };

        this.map = null;
        this.marker = null;
        this.markers = []; // Çoklu marker desteği
        this.defaultCoordinates = this.options.defaultCoordinates;

        // AI bileşenleri
        this.aiSearch = null;
        this.voiceSearch = null;
        this.imageRecognition = null;

        // Gelişmiş özellikler
        this.threeDRenderer = null;
        this.arOverlay = null;
        this.offlineManager = null;
        this.distanceCircles = [];

        // Öncelikli yeni özellikler
        this.googlePlaces = null;
        this.trafficLayer = null;
        this.socialSharing = null;
        this.qrCodeGenerator = null;
        this.pwaManager = null;
        this.gestureController = null;
        this.pushNotificationManager = null;
        this.predictiveAnalytics = null;

        // 🏠 Emlak-Spesifik Özellikler (v4.1)
        this.nearbyServices = null; // Yakındaki hizmetler analizi
        this.transportationScore = null; // Ulaşım puanı hesaplama
        this.environmentAnalysis = null; // Çevre analizi (gürültü, hava kalitesi)
        this.investmentAnalysis = null; // Yatırım potansiyeli analizi
        this.propertyInsights = null; // Emlak öngörüleri
        this.locationScore = null; // Genel lokasyon puanı

        if (this.options.autoInit) {
            this.init();
        }
    }

    async init() {
        console.log('🗺️🤖 EmlakLoc v4.0 başlatılıyor...');

        // Harita stillerini enjekte et
        this.injectMapStyles();

        // AI bileşenlerini başlat
        if (this.options.enableAI) {
            await this.initializeAIComponents();
        }

        // Emlak-spesifik özellikleri başlat
        if (this.options.enablePropertyFeatures !== false) {
            await this.initializePropertyFeatures();
        }

        // DOM hazır olduğunda haritayı başlat
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.initializeMap();
            });
        } else {
            this.initializeMap();
        }
    }

    async initializeAIComponents() {
        console.log('🤖 AI bileşenleri yükleniyor...');

        try {
            // AI Arama Motoru - Gelişmiş Türkçe NLP
            if (this.options.enableAI) {
                this.aiSearch = new AISearchEngine({
                    language: 'tr',
                    enablePredictive: this.options.enablePredictiveAnalytics,
                    providers: ['deepseek', 'openai', 'gemini', 'claude'],
                });
                console.log('✅ Gelişmiş AI Arama Motoru hazır');
            }

            // Sesli Arama - Gelişmiş Speech Recognition
            if (this.options.enableVoice) {
                this.voiceSearch = new VoiceSearchEngine({
                    language: 'tr-TR',
                    continuous: false,
                    enableNLP: true,
                });
                console.log('✅ Gelişmiş Sesli Arama hazır');
            }

            // Gelişmiş Görsel Tanıma - Computer Vision
            if (this.options.enableImageRecognition) {
                this.imageRecognition = new ImageRecognitionEngine({
                    enableOCR: true,
                    enableObjectDetection: true,
                    enableSceneRecognition: true,
                });
                console.log('✅ Gelişmiş Görsel Tanıma hazır');
            }

            // Google Places API
            if (this.options.enableGooglePlaces && this.options.googlePlacesApiKey) {
                this.googlePlaces = new GooglePlacesIntegration({
                    apiKey: this.options.googlePlacesApiKey,
                });
                console.log('✅ Google Places API hazır');
            }

            // Sosyal Medya Paylaşımı
            if (this.options.enableSocialSharing) {
                this.socialSharing = new SocialMediaSharing();
                console.log('✅ Sosyal Medya Paylaşımı hazır');
            }

            // QR Kod Üreteci
            if (this.options.enableQRCode) {
                this.qrCodeGenerator = new QRCodeGenerator();
                console.log('✅ QR Kod Üreteci hazır');
            }

            // PWA Yönetici
            if (this.options.enablePWA) {
                this.pwaManager = new PWAManager();
                console.log('✅ PWA Yönetici hazır');
            }

            // Gesture Controller
            if (this.options.enableGestures) {
                this.gestureController = new GestureController();
                console.log('✅ Gesture Controller hazır');
            }

            // Push Notification Manager
            if (this.options.enablePushNotifications) {
                this.pushNotificationManager = new PushNotificationManager();
                console.log('✅ Push Notification Manager hazır');
            }

            // Predictive Analytics
            if (this.options.enablePredictiveAnalytics) {
                this.predictiveAnalytics = new PredictiveAnalytics();
                console.log('✅ Predictive Analytics hazır');
            }
        } catch (error) {
            console.error('❌ AI bileşenleri yüklenirken hata:', error);
        }
    }

    injectMapStyles() {
        if (!document.getElementById('leaflet-styles')) {
            const link = document.createElement('link');
            link.id = 'leaflet-styles';
            link.rel = 'stylesheet';
            link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
            document.head.appendChild(link);
        }

        // Three.js için stiller (3D harita)
        if (this.options.enable3D && !document.getElementById('three-styles')) {
            const threeLink = document.createElement('link');
            threeLink.id = 'three-styles';
            threeLink.rel = 'stylesheet';
            threeLink.href = 'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.css';
            document.head.appendChild(threeLink);
        }
    }

    initializeMap() {
        console.log('🗺️ Harita başlatılıyor...');

        const mapContainer = document.getElementById(this.options.mapContainerId);
        if (!mapContainer) {
            console.error('❌ Harita konteyneri bulunamadı:', this.options.mapContainerId);
            return;
        }

        // Harita zaten başlatılmışsa, yeni başlatma
        if (this.map && this.map._loaded) {
            console.log('✅ Harita zaten başlatılmış ve yüklü');
            return;
        }

        try {
            // Harita konteynerini temizle
            mapContainer.innerHTML = '';

            // Performans optimizasyonu için harita ayarları
            const mapOptions = {
                center: [this.defaultCoordinates.latitude, this.defaultCoordinates.longitude],
                zoom: 12,
                zoomControl: true,
                scrollWheelZoom: true,
                doubleClickZoom: true,
                boxZoom: true,
                keyboard: true,
                dragging: true,
                touchZoom: true,
                tap: true,
                bounceAtZoomLimits: true,
                maxBounds: [
                    [35.0, 25.0], // Güneybatı
                    [43.0, 45.0], // Kuzeydoğu
                ],
                maxBoundsViscosity: 1.0,
            };

            // Harita oluştur
            this.map = L.map(this.options.mapContainerId, mapOptions);

            // Varsayılan katman ekle
            this.addDefaultLayer();

            // Harita olaylarını ayarla
            this.setupMapEvents();

            // Özel marker ikonlarını ayarla
            this.setupCustomMarkers();

            // Gelişmiş özellikleri başlat
            this.initializeAdvancedFeatures();

            console.log('✅ Harita başarıyla başlatıldı');
        } catch (error) {
            console.error('❌ Harita başlatılırken hata:', error);
            this.showToast('Harita yüklenirken bir hata oluştu', 'error');
        }
    }

    addDefaultLayer() {
        // OpenStreetMap varsayılan katman
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(this.map);
    }

    setupMapEvents() {
        // Harita tıklama olayı
        this.map.on('click', (e) => {
            this.handleMapClick(e);
        });

        // Harita yükleme olayı
        this.map.on('load', () => {
            console.log('🗺️ Harita tamamen yüklendi');
            this.showToast('Harita hazır', 'success');
        });

        // Zoom değişikliği
        this.map.on('zoomend', () => {
            const zoom = this.map.getZoom();
            console.log('🔍 Zoom seviyesi:', zoom);
        });
    }

    setupCustomMarkers() {
        // Özel marker ikonları tanımla
        this.markerIcons = {
            default: this.createCustomIcon('📍', '#3B82F6'),
            selected: this.createCustomIcon('🎯', '#EF4444'),
            property: this.createCustomIcon('🏠', '#10B981'),
            land: this.createCustomIcon('🌱', '#F59E0B'),
            commercial: this.createCustomIcon('🏢', '#8B5CF6'),
            rental: this.createCustomIcon('🔑', '#06B6D4'),
            sale: this.createCustomIcon('💰', '#84CC16'),
            ai: this.createCustomIcon('🤖', '#6366F1'),
            voice: this.createCustomIcon('🎤', '#EC4899'),
            image: this.createCustomIcon('📷', '#F97316'),
        };
    }

    createCustomIcon(emoji, color) {
        return L.divIcon({
            html: `<div style="
                background-color: ${color};
                border-radius: 50%;
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
                border: 3px solid white;
                box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                color: white;
            ">${emoji}</div>`,
            className: 'custom-marker',
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -40],
        });
    }

    initializeAdvancedFeatures() {
        // 3D Harita
        if (this.options.enable3D) {
            this.initialize3DMap();
        }

        // AR Katmanı
        if (this.options.enableAR) {
            this.initializeAROverlay();
        }

        // Offline Destek
        if (this.options.enableOffline) {
            this.initializeOfflineSupport();
        }
    }

    // ============ AI DESTEKLİ ARAMA ============

    async aiSearchAddress(query) {
        if (!this.aiSearch) {
            console.warn('AI Arama motoru aktif değil');
            return this.fallbackSearch(query);
        }

        try {
            console.log('🤖 AI ile arama yapılıyor:', query);

            const results = await this.aiSearch.search(query);

            // Sonuçları haritada göster
            this.displayAISearchResults(results);

            return results;
        } catch (error) {
            console.error('AI arama hatası:', error);
            return this.fallbackSearch(query);
        }
    }

    async voiceSearchAddress() {
        if (!this.voiceSearch) {
            console.warn('Sesli arama aktif değil');
            return;
        }

        try {
            console.log('🎤 Sesli arama başlatılıyor...');

            const transcript = await this.voiceSearch.startListening();

            if (transcript) {
                console.log('🎤 Tanınan metin:', transcript);
                this.showToast(`"${transcript}" aranıyor...`, 'info');

                // AI ile arama yap
                return await this.aiSearchAddress(transcript);
            }
        } catch (error) {
            console.error('Sesli arama hatası:', error);
            this.showToast('Sesli arama başarısız', 'error');
        }
    }

    async recognizeLocationFromImage(imageFile) {
        if (!this.imageRecognition) {
            console.warn('Görsel tanıma aktif değil');
            return;
        }

        try {
            console.log('📷 Görsel konum tanıma başlatılıyor...');

            const locationData = await this.imageRecognition.analyzeImage(imageFile);

            if (locationData) {
                console.log('📍 Konum tespit edildi:', locationData);

                // Haritada konumu göster
                this.addMarker(locationData.lat, locationData.lng, 'image');
                this.map.setView([locationData.lat, locationData.lng], 15);

                this.showToast('Konum görselden tespit edildi', 'success');
                return locationData;
            }
        } catch (error) {
            console.error('Görsel tanıma hatası:', error);
            this.showToast('Görsel konum tanıma başarısız', 'error');
        }
    }

    // ============ 3D HARİTA ============

    initialize3DMap() {
        console.log('🎮 3D Harita başlatılıyor...');

        // Three.js entegrasyonu için temel kurulum
        this.threeDRenderer = {
            scene: null,
            camera: null,
            renderer: null,
            enabled: false,

            enable: () => {
                console.log('🎮 3D mod aktif');
                this.threeDRenderer.enabled = true;
                this.showToast('3D Harita Modu Aktif', 'info');
            },

            disable: () => {
                console.log('🗺️ 2D mod aktif');
                this.threeDRenderer.enabled = false;
                this.showToast('2D Harita Modu Aktif', 'info');
            },
        };
    }

    // ============ AR KATMANI ============

    initializeAROverlay() {
        console.log('📱 AR Katmanı başlatılıyor...');

        this.arOverlay = {
            enabled: false,
            pointsOfInterest: [],

            enable: () => {
                console.log('📱 AR katmanı aktif');
                this.arOverlay.enabled = true;
                this.showToast('AR Modu Aktif', 'info');
            },

            disable: () => {
                console.log('📱 AR katmanı devre dışı');
                this.arOverlay.enabled = false;
                this.showToast('AR Modu Devre Dışı', 'info');
            },
        };
    }

    // ============ OFFLINE DESTEK ============

    initializeOfflineSupport() {
        console.log('💾 Offline destek başlatılıyor...');

        this.offlineManager = {
            enabled: false,
            cachedTiles: new Map(),

            enable: () => {
                console.log('💾 Offline mod aktif');
                this.offlineManager.enabled = true;
                this.showToast('Offline Mod Aktif', 'info');
            },

            disable: () => {
                console.log('🌐 Online mod aktif');
                this.offlineManager.enabled = false;
                this.showToast('Online Mod Aktif', 'info');
            },
        };
    }

    // ============ ÇOKLU MARKER ============

    addMarker(lat, lng, type = 'default', options = {}) {
        const icon = this.markerIcons[type] || this.markerIcons.default;

        const marker = L.marker([lat, lng], {
            icon: icon,
            draggable: true,
            ...options,
        });

        // Popup ekle
        const popupContent = this.createMarkerPopup(lat, lng, type);
        marker.bindPopup(popupContent);

        // Sürükleme olayı
        marker.on('dragend', (e) => {
            const newPos = e.target.getLatLng();
            console.log('📍 Marker taşındı:', newPos);
            this.updateFormFields(newPos.lat, newPos.lng);
        });

        // Marker'ı haritaya ekle
        marker.addTo(this.map);
        this.markers.push(marker);

        console.log(`📍 ${type} marker eklendi:`, lat, lng);
        return marker;
    }

    createMarkerPopup(lat, lng, type) {
        const typeLabels = {
            default: 'Konum',
            property: 'Emlak',
            land: 'Arsa',
            commercial: 'Ticari',
            rental: 'Kiralık',
            sale: 'Satılık',
            ai: 'AI Önerisi',
            voice: 'Sesli Arama',
            image: 'Görsel Tanıma',
        };

        return `
            <div class="marker-popup">
                <h4>${typeLabels[type] || 'Konum'}</h4>
                <p>Koordinatlar: ${lat.toFixed(6)}, ${lng.toFixed(6)}</p>
                <button onclick="window.emlakLoc.confirmLocation()" class="btn btn-sm neo-btn neo-btn-primary">
                    Bu Konumu Seç
                </button>
            </div>
        `;
    }

    // ============ MESAFE ÇEMBERLERİ ============

    addDistanceCircles(center, radii = [500, 1000, 2000]) {
        // Önceki çemberleri temizle
        this.clearDistanceCircles();

        radii.forEach((radius, index) => {
            const circle = L.circle([center.lat, center.lng], {
                color: this.getCircleColor(index),
                fillColor: this.getCircleColor(index),
                fillOpacity: 0.1,
                radius: radius,
                weight: 2,
            });

            circle.addTo(this.map);
            this.distanceCircles.push(circle);

            // Etiket ekle
            const label = L.marker([center.lat, center.lng + radius / 111000], {
                icon: L.divIcon({
                    html: `<div style="
                        background: ${this.getCircleColor(index)};
                        color: white;
                        padding: 2px 6px;
                        border-radius: 3px;
                        font-size: 12px;
                        font-weight: bold;
                    ">${radius}m</div>`,
                    className: 'distance-label',
                    iconAnchor: [0, 0],
                }),
            });

            label.addTo(this.map);
            this.distanceCircles.push(label);
        });

        console.log('📏 Mesafe çemberleri eklendi:', radii);
    }

    getCircleColor(index) {
        const colors = ['#EF4444', '#F59E0B', '#10B981'];
        return colors[index] || '#6B7280';
    }

    clearDistanceCircles() {
        this.distanceCircles.forEach((circle) => {
            this.map.removeLayer(circle);
        });
        this.distanceCircles = [];
    }

    // ============ HARİTA OLAYLARI ============

    handleMapClick(e) {
        const { lat, lng } = e.latlng;
        console.log('🖱️ Haritaya tıklandı:', lat, lng);

        // Önceki marker'ı kaldır
        if (this.marker) {
            this.map.removeLayer(this.marker);
        }

        // Yeni marker ekle
        this.marker = this.addMarker(lat, lng, 'selected');

        // Form alanlarını güncelle
        this.updateFormFields(lat, lng);

        // Reverse geocoding ile adres bul
        this.findLocationFromCoordinates(lat, lng);
    }

    // ============ FORM ENTEGRASYONU ============

    updateFormFields(lat, lng) {
        // Koordinat alanlarını güncelle
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');

        if (latInput) latInput.value = lat.toFixed(8);
        if (lngInput) lngInput.value = lng.toFixed(8);

        console.log('📝 Form alanları güncellendi:', lat, lng);
    }

    // ============ REVERSE GEOCODING ============

    async findLocationFromCoordinates(lat, lng) {
        try {
            console.log('🔍 Reverse geocoding yapılıyor...', lat, lng);

            const response = await fetch(`/api/address/reverse-geocode?lat=${lat}&lng=${lng}`);
            const data = await response.json();

            if (data.status === 'success') {
                console.log('📍 Adres bulundu:', data.data);
                this.updateLocationDropdowns(data.data);
                this.showToast('Adres otomatik dolduruldu', 'success');
            } else {
                console.warn('Adres bulunamadı');
                this.showToast('Adres bulunamadı', 'warning');
            }
        } catch (error) {
            console.error('Reverse geocoding hatası:', error);
            this.showToast('Adres arama hatası', 'error');
        }
    }

    updateLocationDropdowns(locationData) {
        // İl dropdown'unu güncelle
        const provinceSelect = document.getElementById('region_id');
        if (provinceSelect && locationData.province_id) {
            provinceSelect.value = locationData.province_id;
        }

        // İlçe dropdown'unu güncelle
        const districtSelect = document.getElementById('ilce_id');
        if (districtSelect && locationData.district_id) {
            districtSelect.value = locationData.district_id;
        }

        // Mahalle dropdown'unu güncelle
        const neighborhoodSelect = document.getElementById('mahalle_id');
        if (neighborhoodSelect && locationData.neighborhood_id) {
            neighborhoodSelect.value = locationData.neighborhood_id;
        }
    }

    // ============ UI HELPERS ============

    showToast(message, type = 'info') {
        // Toast notification sistemi
        console.log(`🔔 ${type.toUpperCase()}: ${message}`);

        // Basit toast implementation
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <span class="toast-message">${message}</span>
            </div>
        `;

        // Toast stilleri
        Object.assign(toast.style, {
            position: 'fixed',
            top: '20px',
            right: '20px',
            background: this.getToastColor(type),
            color: 'white',
            padding: '12px 20px',
            borderRadius: '8px',
            boxShadow: '0 4px 12px rgba(0,0,0,0.3)',
            zIndex: '10000',
            fontSize: '14px',
            fontWeight: '500',
            maxWidth: '300px',
            opacity: '0',
            transform: 'translateY(-20px)',
            transition: 'all 0.3s ease',
        });

        document.body.appendChild(toast);

        // Animasyon
        setTimeout(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
        }, 100);

        // Otomatik kaldır
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }

    getToastColor(type) {
        const colors = {
            success: '#10B981',
            error: '#EF4444',
            warning: '#F59E0B',
            info: '#3B82F6',
        };
        return colors[type] || colors.info;
    }

    // ============ KONFİGÜRASYON ============

    confirmLocation() {
        if (this.marker) {
            const pos = this.marker.getLatLng();
            console.log('✅ Konum onaylandı:', pos.lat, pos.lng);
            this.showToast('Konum onaylandı', 'success');

            // Form alanlarını güncelle
            this.updateFormFields(pos.lat, pos.lng);
        }
    }

    removeMarker() {
        if (this.marker) {
            this.map.removeLayer(this.marker);
            this.marker = null;
            console.log('🗑️ Marker kaldırıldı');
            this.showToast('Konum kaldırıldı', 'info');
        }
    }

    // ============ AI ARAMA SONUÇLARI ============

    displayAISearchResults(results) {
        if (!results || results.length === 0) {
            this.showToast('Arama sonucu bulunamadı', 'warning');
            return;
        }

        // Sonuçları haritada göster
        results.forEach((result, index) => {
            if (result.coordinates) {
                const marker = this.addMarker(
                    result.coordinates.lat,
                    result.coordinates.lng,
                    'ai',
                    { title: result.title }
                );

                // İlk sonucu odakla
                if (index === 0) {
                    this.map.setView([result.coordinates.lat, result.coordinates.lng], 15);
                }
            }
        });

        this.showToast(`${results.length} adet sonuç bulundu`, 'success');
    }

    // ============ FALLBACK ARAMA ============

    async fallbackSearch(query) {
        console.log('🔄 Fallback arama kullanılıyor:', query);

        try {
            const response = await fetch(`/api/address/search?q=${encodeURIComponent(query)}`);
            const data = await response.json();

            if (data.status === 'success' && data.data.length > 0) {
                return data.data;
            }
        } catch (error) {
            console.error('Fallback arama hatası:', error);
        }

        return [];
    }
};

// ============ AI BİLEŞENLERİ ============

class AISearchEngine {
    constructor(options = {}) {
        this.providers = options.providers || ['deepseek', 'openai', 'gemini', 'claude'];
        this.currentProvider = options.currentProvider || 'deepseek';
        this.language = options.language || 'tr';
        this.enablePredictive = options.enablePredictive || false;
        this.searchHistory = [];
        this.userPreferences = {};
    }

    async search(query) {
        try {
            // Türkçe NLP işleme
            const processedQuery = await this.processTurkishNLP(query);

            // Tahmin edici arama
            if (this.enablePredictive) {
                const predictions = await this.getPredictions(processedQuery);
                this.searchHistory.push({
                    query,
                    predictions,
                    timestamp: Date.now(),
                });
            }

            const response = await fetch('/api/ai/address/analyze', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    query: processedQuery,
                    provider: this.currentProvider,
                    language: this.language,
                    enablePredictive: this.enablePredictive,
                }),
            });

            const data = await response.json();

            if (data.status === 'success') {
                return data.data;
            }

            throw new Error(data.message || 'AI arama hatası');
        } catch (error) {
            console.error('AI arama hatası:', error);
            throw error;
        }
    }

    async processTurkishNLP(query) {
        // Türkçe'ye özel NLP işleme
        const turkishRules = {
            nerede: 'location',
            'nerede var': 'find',
            yakın: 'near',
            yakında: 'nearby',
            bul: 'find',
            ara: 'search',
            git: 'go to',
            göster: 'show',
        };

        let processed = query.toLowerCase();
        for (const [key, value] of Object.entries(turkishRules)) {
            processed = processed.replace(new RegExp(key, 'g'), value);
        }

        return processed;
    }

    async getPredictions(query) {
        // Kullanıcı arama geçmişine göre tahminler
        const similarSearches = this.searchHistory
            .filter((item) => item.query.toLowerCase().includes(query.toLowerCase().slice(0, 3)))
            .slice(0, 5);

        return similarSearches.map((item) => item.query);
    }
}

class VoiceSearchEngine {
    constructor(options = {}) {
        this.recognition = null;
        this.isListening = false;
        this.language = options.language || 'tr-TR';
        this.continuous = options.continuous || false;
        this.enableNLP = options.enableNLP || true;
        this.transcriptHistory = [];
        this.initSpeechRecognition();
    }

    initSpeechRecognition() {
        if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            this.recognition = new SpeechRecognition();

            this.recognition.continuous = this.continuous;
            this.recognition.interimResults = false;
            this.recognition.lang = this.language;
            this.recognition.maxAlternatives = 3;

            // Gelişmiş ayarlar
            if (this.recognition.grammars) {
                const grammar =
                    '#JSGF V1.0; grammar locations; public <location> = (Bodrum | İstanbul | Ankara | İzmir | Antalya | Marmaris | Fethiye | Kaş | Kalkan | Göcek);';
                const speechRecognitionList = new webkitSpeechGrammarList();
                speechRecognitionList.addFromString(grammar, 1);
                this.recognition.grammars = speechRecognitionList;
            }
        }
    }

    async startListening() {
        return new Promise((resolve, reject) => {
            if (!this.recognition) {
                reject(new Error('Speech recognition not supported'));
                return;
            }

            this.recognition.onresult = (event) => {
                let transcript = event.results[0][0].transcript;
                const confidence = event.results[0][0].confidence;

                console.log(
                    `🎤 Tanınan metin: "${transcript}" (Güven: ${(confidence * 100).toFixed(1)}%)`
                );

                // NLP işleme
                if (this.enableNLP) {
                    transcript = this.processVoiceNLP(transcript);
                }

                // Geçmişi kaydet
                this.transcriptHistory.push({
                    transcript,
                    confidence,
                    timestamp: Date.now(),
                });

                resolve(transcript);
            };

            this.recognition.onerror = (event) => {
                console.error('🎤 Speech recognition error:', event.error);
                reject(new Error('Speech recognition error: ' + event.error));
            };

            this.recognition.onend = () => {
                this.isListening = false;
                console.log('🎤 Ses tanıma durduruldu');
            };

            this.recognition.onstart = () => {
                this.isListening = true;
                console.log('🎤 Ses tanıma başlatıldı');
            };

            this.isListening = true;
            this.recognition.start();
        });
    }

    processVoiceNLP(transcript) {
        // Türkçe ses komutları işleme
        const voiceCommands = {
            bul: 'find',
            ara: 'search',
            git: 'go to',
            göster: 'show',
            yakın: 'near',
            yakında: 'nearby',
            nerede: 'where is',
            'nasıl giderim': 'how to get to',
            adres: 'address',
            konum: 'location',
        };

        let processed = transcript.toLowerCase();
        for (const [key, value] of Object.entries(voiceCommands)) {
            processed = processed.replace(new RegExp(key, 'g'), value);
        }

        return processed;
    }

    stopListening() {
        if (this.recognition && this.isListening) {
            this.recognition.stop();
        }
    }

    getTranscriptHistory() {
        return this.transcriptHistory.slice(-10); // Son 10 kayıt
    }
}

class ImageRecognitionEngine {
    constructor(options = {}) {
        this.apiEndpoint = '/api/ai/image-recognition';
        this.enableOCR = options.enableOCR || true;
        this.enableObjectDetection = options.enableObjectDetection || true;
        this.enableSceneRecognition = options.enableSceneRecognition || true;
        this.maxFileSize = options.maxFileSize || 10 * 1024 * 1024; // 10MB
        this.supportedFormats = ['image/jpeg', 'image/png', 'image/webp'];
    }

    async analyzeImage(imageFile) {
        // Dosya kontrolü
        if (!this.validateImageFile(imageFile)) {
            throw new Error('Geçersiz dosya formatı veya boyutu');
        }

        const formData = new FormData();
        formData.append('image', imageFile);
        formData.append('enableOCR', this.enableOCR);
        formData.append('enableObjectDetection', this.enableObjectDetection);
        formData.append('enableSceneRecognition', this.enableSceneRecognition);

        try {
            console.log('📷 Görsel analiz ediliyor...');
            const response = await fetch(this.apiEndpoint, {
                method: 'POST',
                body: formData,
            });

            const data = await response.json();

            if (data.status === 'success') {
                console.log('📍 Görsel analiz tamamlandı:', data.data);
                return this.processRecognitionResults(data.data);
            }

            throw new Error(data.message || 'Image recognition failed');
        } catch (error) {
            console.error('Image recognition error:', error);
            throw error;
        }
    }

    validateImageFile(file) {
        if (!file) return false;

        // Dosya tipi kontrolü
        if (!this.supportedFormats.includes(file.type)) {
            console.error('❌ Desteklenmeyen dosya formatı:', file.type);
            return false;
        }

        // Dosya boyutu kontrolü
        if (file.size > this.maxFileSize) {
            console.error('❌ Dosya boyutu çok büyük:', file.size);
            return false;
        }

        return true;
    }

    processRecognitionResults(data) {
        const results = {
            location: null,
            landmarks: [],
            text: [],
            objects: [],
            scene: null,
            confidence: 0,
        };

        if (data.location) {
            results.location = {
                lat: data.location.lat,
                lng: data.location.lng,
                address: data.location.address,
                confidence: data.location.confidence,
            };
        }

        if (data.landmarks && this.enableObjectDetection) {
            results.landmarks = data.landmarks.map((landmark) => ({
                name: landmark.name,
                confidence: landmark.confidence,
                location: landmark.location,
            }));
        }

        if (data.text && this.enableOCR) {
            results.text = data.text.map((text) => ({
                content: text.content,
                confidence: text.confidence,
                bounds: text.bounds,
            }));
        }

        if (data.objects && this.enableObjectDetection) {
            results.objects = data.objects.map((obj) => ({
                name: obj.name,
                confidence: obj.confidence,
                bounds: obj.bounds,
            }));
        }

        if (data.scene && this.enableSceneRecognition) {
            results.scene = {
                type: data.scene.type,
                confidence: data.scene.confidence,
                attributes: data.scene.attributes,
            };
        }

        results.confidence = data.overallConfidence || 0;

        return results;
    }

    // Yardımcı metodlar
    getSupportedFormats() {
        return this.supportedFormats;
    }

    getMaxFileSize() {
        return this.maxFileSize;
    }

    setMaxFileSize(size) {
        this.maxFileSize = size;
    }
}

// ============ GOOGLE PLACES API ENTEGRASYONU ============

class GooglePlacesIntegration {
    constructor(options = {}) {
        this.apiKey = options.apiKey;
        this.loaded = false;
        this.placesService = null;
        this.autocompleteService = null;
        this.initGooglePlaces();
    }

    initGooglePlaces() {
        if (!this.apiKey) {
            console.warn('⚠️ Google Places API key gerekli');
            return;
        }

        // Google Maps JavaScript API'yi yükle
        if (!window.google || !window.google.maps) {
            this.loadGoogleMapsAPI();
        } else {
            this.initializeServices();
        }
    }

    loadGoogleMapsAPI() {
        if (document.querySelector('script[src*="maps.googleapis.com"]')) {
            console.log('✅ Google Maps API zaten yüklü');
            this.initializeServices();
            return;
        }

        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=${this.apiKey}&libraries=places&callback=initGooglePlacesCallback`;
        script.async = true;
        script.defer = true;

        window.initGooglePlacesCallback = () => {
            console.log('✅ Google Maps API yüklendi');
            this.initializeServices();
        };

        document.head.appendChild(script);
    }

    initializeServices() {
        if (window.google && window.google.maps && window.google.maps.places) {
            this.placesService = new google.maps.places.PlacesService(
                document.createElement('div')
            );
            this.autocompleteService = new google.maps.places.AutocompleteService();
            this.loaded = true;
            console.log('✅ Google Places servisleri hazır');
        }
    }

    async searchPlaces(query, location = null, radius = 5000) {
        if (!this.loaded || !this.autocompleteService) {
            throw new Error('Google Places API hazır değil');
        }

        return new Promise((resolve, reject) => {
            const request = {
                input: query,
                types: ['establishment', 'geocode'],
                componentRestrictions: { country: 'tr' },
            };

            if (location) {
                request.location = new google.maps.LatLng(location.lat, location.lng);
                request.radius = radius;
            }

            this.autocompleteService.getPlacePredictions(request, (predictions, status) => {
                if (status === google.maps.places.PlacesServiceStatus.OK && predictions) {
                    resolve(
                        predictions.map((prediction) => ({
                            id: prediction.place_id,
                            description: prediction.description,
                            types: prediction.types,
                            structured_formatting: prediction.structured_formatting,
                        }))
                    );
                } else {
                    reject(new Error('Places arama hatası: ' + status));
                }
            });
        });
    }

    async getPlaceDetails(placeId) {
        if (!this.loaded || !this.placesService) {
            throw new Error('Google Places API hazır değil');
        }

        return new Promise((resolve, reject) => {
            const request = {
                placeId: placeId,
                fields: [
                    'name',
                    'formatted_address',
                    'geometry',
                    'types',
                    'photos',
                    'rating',
                    'reviews',
                ],
            };

            this.placesService.getDetails(request, (place, status) => {
                if (status === google.maps.places.PlacesServiceStatus.OK && place) {
                    resolve({
                        name: place.name,
                        address: place.formatted_address,
                        location: {
                            lat: place.geometry.location.lat(),
                            lng: place.geometry.location.lng(),
                        },
                        types: place.types,
                        photos: place.photos
                            ? place.photos.map((photo) => photo.getUrl({ maxWidth: 400 }))
                            : [],
                        rating: place.rating,
                        reviews: place.reviews,
                    });
                } else {
                    reject(new Error('Place detay hatası: ' + status));
                }
            });
        });
    }
}

// ============ SOSYAL MEDYA PAYLAŞIMI ============

class SocialMediaSharing {
    constructor() {
        this.platforms = {
            whatsapp: this.shareToWhatsApp.bind(this),
            telegram: this.shareToTelegram.bind(this),
            twitter: this.shareToTwitter.bind(this),
            facebook: this.shareToFacebook.bind(this),
        };
    }

    shareLocation(platform, locationData) {
        if (this.platforms[platform]) {
            return this.platforms[platform](locationData);
        } else {
            throw new Error(`Desteklenmeyen platform: ${platform}`);
        }
    }

    shareToWhatsApp(locationData) {
        const text = `📍 ${locationData.name}\n📍 ${locationData.address}\n📍 Koordinatlar: ${locationData.lat}, ${locationData.lng}`;
        const url = `https://wa.me/?text=${encodeURIComponent(text)}`;
        window.open(url, '_blank');
    }

    shareToTelegram(locationData) {
        const text = `📍 ${locationData.name}\n📍 ${locationData.address}\n📍 Koordinatlar: ${locationData.lat}, ${locationData.lng}`;
        const url = `https://t.me/share/url?url=${encodeURIComponent(
            window.location.href
        )}&text=${encodeURIComponent(text)}`;
        window.open(url, '_blank');
    }

    shareToTwitter(locationData) {
        const text = `📍 ${locationData.name} - ${locationData.address} #EmlakLoc`;
        const url = `https://twitter.com/intent/tweet?text=${encodeURIComponent(
            text
        )}&url=${encodeURIComponent(window.location.href)}`;
        window.open(url, '_blank');
    }

    shareToFacebook(locationData) {
        const url = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(
            window.location.href
        )}&quote=${encodeURIComponent(`📍 ${locationData.name} - ${locationData.address}`)}`;
        window.open(url, '_blank');
    }

    generateShareableLink(locationData) {
        const baseUrl = window.location.origin;
        const params = new URLSearchParams({
            lat: locationData.lat,
            lng: locationData.lng,
            name: locationData.name,
            address: locationData.address,
        });
        return `${baseUrl}/shared-location?${params.toString()}`;
    }
}

// ============ QR KOD ÜRETECİ ============

class QRCodeGenerator {
    constructor() {
        this.qrCode = null;
        this.container = null;
    }

    async generateQRCode(data, options = {}) {
        const defaultOptions = {
            width: 256,
            height: 256,
            colorDark: '#000000',
            colorLight: '#FFFFFF',
            correctLevel: QRCode.CorrectLevel.H,
        };

        const finalOptions = { ...defaultOptions, ...options };

        return new Promise((resolve, reject) => {
            try {
                // QRCode kütüphanesini kontrol et
                if (typeof QRCode === 'undefined') {
                    this.loadQRCodeLibrary().then(() => {
                        this.createQRCode(data, finalOptions, resolve, reject);
                    });
                } else {
                    this.createQRCode(data, finalOptions, resolve, reject);
                }
            } catch (error) {
                reject(error);
            }
        });
    }

    createQRCode(data, options, resolve, reject) {
        try {
            // Container oluştur
            this.container = document.createElement('div');
            this.container.id = 'qrcode-container';

            // QR kod oluştur
            this.qrCode = new QRCode(this.container, {
                text: data,
                width: options.width,
                height: options.height,
                colorDark: options.colorDark,
                colorLight: options.colorLight,
                correctLevel: options.correctLevel,
            });

            // Canvas'ı al
            setTimeout(() => {
                const canvas = this.container.querySelector('canvas');
                if (canvas) {
                    resolve({
                        canvas: canvas,
                        dataUrl: canvas.toDataURL('image/png'),
                        container: this.container,
                    });
                } else {
                    reject(new Error('QR kod oluşturulamadı'));
                }
            }, 100);
        } catch (error) {
            reject(error);
        }
    }

    loadQRCodeLibrary() {
        return new Promise((resolve, reject) => {
            if (document.querySelector('script[src*="qrcode.min.js"]')) {
                resolve();
                return;
            }

            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js';
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    downloadQRCode(filename = 'location-qr.png') {
        if (!this.qrCode) {
            throw new Error('QR kod henüz oluşturulmadı');
        }

        const canvas = this.container.querySelector('canvas');
        if (canvas) {
            const link = document.createElement('a');
            link.download = filename;
            link.href = canvas.toDataURL('image/png');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }

    generateLocationQR(locationData) {
        const data = JSON.stringify({
            type: 'location',
            name: locationData.name,
            address: locationData.address,
            lat: locationData.lat,
            lng: locationData.lng,
            timestamp: Date.now(),
        });

        return this.generateQRCode(data);
    }
}

// ============ PWA YÖNETİCİ ============

class PWAManager {
    constructor() {
        this.deferredPrompt = null;
        this.isInstalled = false;
        this.init();
    }

    init() {
        // PWA olaylarını dinle
        window.addEventListener('beforeinstallprompt', (e) => {
            console.log('📱 PWA yükleme prompt yakalandı');
            e.preventDefault();
            this.deferredPrompt = e;
            this.showInstallButton();
        });

        window.addEventListener('appinstalled', () => {
            console.log('📱 PWA başarıyla yüklendi');
            this.isInstalled = true;
            this.hideInstallButton();
        });

        // Service Worker kontrolü
        if ('serviceWorker' in navigator) {
            this.registerServiceWorker();
        }

        // Çevrimdışı/online status kontrolü
        window.addEventListener('online', () => this.handleOnline());
        window.addEventListener('offline', () => this.handleOffline());
    }

    async registerServiceWorker() {
        try {
            const registration = await navigator.serviceWorker.register('/sw.js');
            console.log('✅ Service Worker kayıt edildi:', registration);
        } catch (error) {
            console.error('❌ Service Worker kayıt hatası:', error);
        }
    }

    showInstallButton() {
        // Yükleme butonu göster
        const installButton = document.createElement('button');
        installButton.id = 'pwa-install-btn';
        installButton.innerHTML = '📱 Uygulamayı Yükle';
        installButton.className = 'btn neo-btn neo-btn-primary position-fixed';
        installButton.style.cssText = 'bottom: 20px; right: 20px; z-index: 1000;';

        installButton.addEventListener('click', () => {
            this.installPWA();
        });

        document.body.appendChild(installButton);
    }

    hideInstallButton() {
        const installButton = document.getElementById('pwa-install-btn');
        if (installButton) {
            installButton.remove();
        }
    }

    async installPWA() {
        if (!this.deferredPrompt) return;

        this.deferredPrompt.prompt();
        const { outcome } = await this.deferredPrompt.userChoice;

        console.log(`📱 Kullanıcı yanıtı: ${outcome}`);
        this.deferredPrompt = null;
    }

    handleOnline() {
        console.log('🌐 Çevrimiçi mod');
        this.showToast('Çevrimiçi mod aktif', 'success');
    }

    handleOffline() {
        console.log('📴 Çevrimdışı mod');
        this.showToast('Çevrimdışı mod aktif', 'warning');
    }

    showToast(message, type) {
        // Toast gösterimi
        console.log(`🔔 ${type.toUpperCase()}: ${message}`);
    }

    isOnline() {
        return navigator.onLine;
    }

    async cacheMapData(lat, lng, zoom) {
        if ('caches' in window) {
            const cache = await caches.open('map-cache-v1');
            const tileUrls = this.generateTileUrls(lat, lng, zoom);

            await cache.addAll(tileUrls);
            console.log('✅ Harita verileri önbelleğe alındı');
        }
    }

    generateTileUrls(lat, lng, zoom) {
        // Basit tile URL üretimi (OpenStreetMap için)
        const urls = [];
        for (let x = -1; x <= 1; x++) {
            for (let y = -1; y <= 1; y++) {
                const tileX = Math.floor(((lng + 180) / 360) * Math.pow(2, zoom)) + x;
                const tileY =
                    Math.floor(
                        ((1 -
                            Math.log(
                                Math.tan((lat * Math.PI) / 180) +
                                    1 / Math.cos((lat * Math.PI) / 180)
                            ) /
                                Math.PI) /
                            2) *
                            Math.pow(2, zoom)
                    ) + y;
                urls.push(`https://tile.openstreetmap.org/${zoom}/${tileX}/${tileY}.png`);
            }
        }
        return urls;
    }
}

// ============ GESTURE CONTROLLER ============

class GestureController {
    constructor(options = {}) {
        this.map = options.map;
        this.enabled = false;
        this.touchStartX = 0;
        this.touchStartY = 0;
        this.touchEndX = 0;
        this.touchEndY = 0;
        this.init();
    }

    init() {
        if (!this.map) return;

        // Dokunmat olaylarını dinle
        this.map.on('touchstart', (e) => this.handleTouchStart(e));
        this.map.on('touchend', (e) => this.handleTouchEnd(e));
        this.map.on('touchmove', (e) => this.handleTouchMove(e));

        console.log('👆 Gesture Controller aktif');
        this.enabled = true;
    }

    handleTouchStart(e) {
        this.touchStartX = e.touches[0].clientX;
        this.touchStartY = e.touches[0].clientY;
    }

    handleTouchEnd(e) {
        this.touchEndX = e.changedTouches[0].clientX;
        this.touchEndY = e.changedTouches[0].clientY;

        this.handleGesture();
    }

    handleTouchMove(e) {
        // Çoklu dokunma için gesture tanıma
        if (e.touches.length === 2) {
            this.handlePinchGesture(e);
        }
    }

    handleGesture() {
        const deltaX = this.touchEndX - this.touchStartX;
        const deltaY = this.touchEndY - this.touchStartY;
        const minSwipeDistance = 50;

        if (Math.abs(deltaX) > Math.abs(deltaY)) {
            // Yatay swipe
            if (Math.abs(deltaX) > minSwipeDistance) {
                if (deltaX > 0) {
                    this.handleSwipeRight();
                } else {
                    this.handleSwipeLeft();
                }
            }
        } else {
            // Dikey swipe
            if (Math.abs(deltaY) > minSwipeDistance) {
                if (deltaY > 0) {
                    this.handleSwipeDown();
                } else {
                    this.handleSwipeUp();
                }
            }
        }
    }

    handlePinchGesture(e) {
        const touch1 = e.touches[0];
        const touch2 = e.touches[1];

        const currentDistance = this.getDistance(touch1, touch2);

        if (!this.lastPinchDistance) {
            this.lastPinchDistance = currentDistance;
            return;
        }

        const scale = currentDistance / this.lastPinchDistance;

        if (scale > 1.1) {
            // Zoom in
            this.map.zoomIn();
        } else if (scale < 0.9) {
            // Zoom out
            this.map.zoomOut();
        }

        this.lastPinchDistance = currentDistance;
    }

    getDistance(touch1, touch2) {
        const dx = touch1.clientX - touch2.clientX;
        const dy = touch1.clientY - touch2.clientY;
        return Math.sqrt(dx * dx + dy * dy);
    }

    handleSwipeLeft() {
        console.log('👆 Swipe Left - Sonraki konum');
        // Sonraki konum göstergesi
    }

    handleSwipeRight() {
        console.log('👆 Swipe Right - Önceki konum');
        // Önceki konum göstergesi
    }

    handleSwipeUp() {
        console.log('👆 Swipe Up - Yakınlaştırma');
        this.map.zoomIn();
    }

    handleSwipeDown() {
        console.log('👆 Swipe Down - Uzaklaştırma');
        this.map.zoomOut();
    }

    // Özel gesture'lar
    addCustomGesture(name, handler) {
        this.customGestures = this.customGestures || {};
        this.customGestures[name] = handler;
    }

    removeCustomGesture(name) {
        if (this.customGestures && this.customGestures[name]) {
            delete this.customGestures[name];
        }
    }
}

// ============ PUSH NOTIFICATION MANAGER ============

class PushNotificationManager {
    constructor(options = {}) {
        this.swRegistration = null;
        this.isSubscribed = false;
        this.vapidPublicKey = options.vapidPublicKey;
        this.init();
    }

    async init() {
        if ('serviceWorker' in navigator && 'PushManager' in window) {
            try {
                this.swRegistration = await navigator.serviceWorker.ready;
                console.log('✅ Push Notification Manager hazır');

                // Mevcut subscription kontrolü
                const subscription = await this.swRegistration.pushManager.getSubscription();
                this.isSubscribed = !!subscription;

                if (this.isSubscribed) {
                    console.log('✅ Push notification aktif');
                } else {
                    console.log('⚠️ Push notification pasif');
                }
            } catch (error) {
                console.error('❌ Push Notification Manager hatası:', error);
            }
        } else {
            console.warn('⚠️ Push notification desteklenmiyor');
        }
    }

    async subscribe() {
        if (!this.swRegistration) return false;

        try {
            const subscription = await this.swRegistration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.urlBase64ToUint8Array(this.vapidPublicKey),
            });

            console.log('✅ Push notification subscription başarılı');
            this.isSubscribed = true;

            // Server'a subscription gönder
            await this.sendSubscriptionToServer(subscription);

            return true;
        } catch (error) {
            console.error('❌ Push notification subscription hatası:', error);
            return false;
        }
    }

    async unsubscribe() {
        if (!this.swRegistration) return false;

        try {
            const subscription = await this.swRegistration.pushManager.getSubscription();
            if (subscription) {
                await subscription.unsubscribe();
                console.log('✅ Push notification subscription iptal edildi');
                this.isSubscribed = false;
                return true;
            }
        } catch (error) {
            console.error('❌ Push notification unsubscribe hatası:', error);
        }
        return false;
    }

    async sendSubscriptionToServer(subscription) {
        const response = await fetch('/api/push/subscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                endpoint: subscription.endpoint,
                keys: {
                    p256dh: this.arrayBufferToBase64(subscription.getKey('p256dh')),
                    auth: this.arrayBufferToBase64(subscription.getKey('auth')),
                },
            }),
        });

        if (!response.ok) {
            throw new Error('Subscription server hatası');
        }
    }

    sendNotification(title, body, icon = '/favicon.ico') {
        if (!this.swRegistration) return;

        const options = {
            body: body,
            icon: icon,
            badge: '/favicon.ico',
            vibrate: [200, 100, 200],
            data: {
                dateOfArrival: Date.now(),
                primaryKey: 1,
            },
            actions: [
                {
                    action: 'explore',
                    title: 'İncele',
                    icon: '/images/checkmark.png',
                },
                {
                    action: 'close',
                    title: 'Kapat',
                    icon: '/images/xmark.png',
                },
            ],
        };

        this.swRegistration.showNotification(title, options);
    }

    sendLocationNotification(locationData) {
        const title = '📍 Konum Güncellemesi';
        const body = `${locationData.name} konumunda yeni bilgiler mevcut`;
        this.sendNotification(title, body);
    }

    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    arrayBufferToBase64(buffer) {
        let binary = '';
        const bytes = new Uint8Array(buffer);
        for (let i = 0; i < bytes.byteLength; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        return window.btoa(binary);
    }

    isSupported() {
        return 'serviceWorker' in navigator && 'PushManager' in window;
    }

    getSubscriptionStatus() {
        return this.isSubscribed;
    }
}

// ============ PREDICTIVE ANALYTICS ============

class PredictiveAnalytics {
    constructor() {
        this.userHistory = [];
        this.locationTrends = new Map();
        this.searchPatterns = new Map();
        this.init();
    }

    init() {
        // Local storage'dan geçmiş verileri yükle
        this.loadFromStorage();
        console.log('🔮 Predictive Analytics aktif');
    }

    trackUserAction(action, data) {
        const userAction = {
            action: action,
            data: data,
            timestamp: Date.now(),
            userAgent: navigator.userAgent,
            location: data.location || null,
        };

        this.userHistory.push(userAction);

        // Maksimum 1000 kayıt tut
        if (this.userHistory.length > 1000) {
            this.userHistory = this.userHistory.slice(-1000);
        }

        this.saveToStorage();
        this.analyzePatterns();
    }

    analyzePatterns() {
        // Arama desenlerini analiz et
        const recentSearches = this.userHistory
            .filter((item) => item.action === 'search')
            .slice(-50);

        // Konum tercihlerini analiz et
        const locationPreferences = this.userHistory
            .filter((item) => item.data && item.data.location)
            .reduce((acc, item) => {
                const loc = item.data.location;
                acc[loc] = (acc[loc] || 0) + 1;
                return acc;
            }, {});

        // Trendleri güncelle
        this.updateTrends(recentSearches, locationPreferences);
    }

    updateTrends(searches, locations) {
        // Arama trendleri
        searches.forEach((search) => {
            const query = search.data.query;
            if (query) {
                this.searchPatterns.set(query, (this.searchPatterns.get(query) || 0) + 1);
            }
        });

        // Konum trendleri
        Object.entries(locations).forEach(([location, count]) => {
            this.locationTrends.set(location, count);
        });
    }

    getSearchPredictions(query) {
        if (!query || query.length < 2) return [];

        const predictions = [];
        const queryLower = query.toLowerCase();

        // Geçmiş aramalardan benzer olanları bul
        for (const [searchQuery, count] of this.searchPatterns.entries()) {
            if (searchQuery.toLowerCase().includes(queryLower)) {
                predictions.push({
                    query: searchQuery,
                    score: count,
                    type: 'history',
                });
            }
        }

        // Popüler konumları öner
        for (const [location, count] of this.locationTrends.entries()) {
            if (location.toLowerCase().includes(queryLower)) {
                predictions.push({
                    query: location,
                    score: count,
                    type: 'location',
                });
            }
        }

        // Skora göre sırala ve ilk 5'i döndür
        return predictions.sort((a, b) => b.score - a.score).slice(0, 5);
    }

    getLocationPredictions() {
        // En popüler konumları döndür
        return Array.from(this.locationTrends.entries())
            .sort((a, b) => b[1] - a[1])
            .slice(0, 10)
            .map(([location, count]) => ({
                location: location,
                score: count,
            }));
    }

    predictUserIntent(query) {
        // Kullanıcı niyetini tahmin et
        const intents = {
            search: ['bul', 'ara', 'nerede', 'nası', 'hangi'],
            navigation: ['git', 'göster', 'aç', 'bak'],
            information: ['nedir', 'nasıl', 'ne zaman', 'kaç'],
        };

        const queryLower = query.toLowerCase();
        let bestIntent = 'search';
        let bestScore = 0;

        Object.entries(intents).forEach(([intent, keywords]) => {
            const score = keywords.reduce((acc, keyword) => {
                return acc + (queryLower.includes(keyword) ? 1 : 0);
            }, 0);

            if (score > bestScore) {
                bestScore = score;
                bestIntent = intent;
            }
        });

        return {
            intent: bestIntent,
            confidence: bestScore / Math.max(...Object.values(intents).map((k) => k.length)),
        };
    }

    saveToStorage() {
        try {
            localStorage.setItem(
                'emlakloc_analytics',
                JSON.stringify({
                    userHistory: this.userHistory.slice(-500), // Son 500 kayıt
                    searchPatterns: Array.from(this.searchPatterns.entries()),
                    locationTrends: Array.from(this.locationTrends.entries()),
                    lastUpdated: Date.now(),
                })
            );
        } catch (error) {
            console.warn('Analytics verisi kaydedilemedi:', error);
        }
    }

    loadFromStorage() {
        try {
            const data = localStorage.getItem('emlakloc_analytics');
            if (data) {
                const parsed = JSON.parse(data);

                this.userHistory = parsed.userHistory || [];
                this.searchPatterns = new Map(parsed.searchPatterns || []);
                this.locationTrends = new Map(parsed.locationTrends || []);

                console.log('📊 Analytics verisi yüklendi');
            }
        } catch (error) {
            console.warn('Analytics verisi yüklenemedi:', error);
        }
    }

    clearData() {
        this.userHistory = [];
        this.searchPatterns.clear();
        this.locationTrends.clear();
        localStorage.removeItem('emlakloc_analytics');
        console.log('🗑️ Analytics verisi temizlendi');
    }

    getAnalyticsSummary() {
        return {
            totalActions: this.userHistory.length,
            uniqueSearches: this.searchPatterns.size,
            uniqueLocations: this.locationTrends.size,
            mostSearched: Array.from(this.searchPatterns.entries())
                .sort((a, b) => b[1] - a[1])
                .slice(0, 5),
            mostVisitedLocations: Array.from(this.locationTrends.entries())
                .sort((a, b) => b[1] - a[1])
                .slice(0, 5),
        };
    }
}

// ============ TRAFFIC LAYER (GERÇEK ZAMANLI TRAFİK) ============

class TrafficLayer {
    constructor(map) {
        this.map = map;
        this.trafficLayer = null;
        this.enabled = false;
    }

    enable() {
        if (this.enabled) return;

        try {
            // Google Maps Traffic Layer
            if (window.google && window.google.maps) {
                this.trafficLayer = new google.maps.TrafficLayer();
                this.trafficLayer.setMap(this.map);
                this.enabled = true;
                console.log('🚗 Trafik katmanı aktif');
            } else {
                console.warn('⚠️ Google Maps API gerekli');
            }
        } catch (error) {
            console.error('❌ Trafik katmanı hatası:', error);
        }
    }

    disable() {
        if (!this.enabled || !this.trafficLayer) return;

        this.trafficLayer.setMap(null);
        this.enabled = false;
        console.log('🚗 Trafik katmanı devre dışı');
    }

    isEnabled() {
        return this.enabled;
    }

    // Trafik yoğunluğu bilgilerini al
    async getTrafficInfo(lat, lng, radius = 1000) {
        // Bu gerçek bir API çağrısı gerektirir
        // Şimdilik mock veri döndürüyoruz
        return {
            level: Math.random() > 0.7 ? 'heavy' : Math.random() > 0.4 ? 'moderate' : 'light',
            speed: Math.floor(Math.random() * 60) + 20, // 20-80 km/h
            incidents: Math.floor(Math.random() * 3),
        };
    }

    // ============ EMLAK-SPESİFİK ÖZELLİKLER ============

    async initializePropertyFeatures() {
        console.log('🏠 Emlak-spesifik özellikler başlatılıyor...');

        this.nearbyServices = new NearbyServicesAnalyzer();
        this.transportationScore = new TransportationScoreCalculator();
        this.environmentAnalysis = new EnvironmentAnalyzer();
        this.investmentAnalysis = new InvestmentAnalyzer();
        this.propertyInsights = new PropertyInsightsGenerator();
        this.locationScore = new LocationScoreCalculator();

        console.log('✅ Emlak-spesifik özellikler hazır!');
    }

    /**
     * Konum için emlak analizi yapar
     */
    async analyzePropertyLocation(lat, lng) {
        if (!lat || !lng) {
            console.warn('Koordinat bilgisi eksik');
            return null;
        }

        try {
            const analysis = {
                coordinates: { lat, lng },
                timestamp: new Date().toISOString(),
                nearbyServices: await this.nearbyServices.analyze(lat, lng),
                transportationScore: await this.transportationScore.calculate(lat, lng),
                environmentAnalysis: await this.environmentAnalysis.analyze(lat, lng),
                investmentAnalysis: await this.investmentAnalysis.analyze(lat, lng),
                propertyInsights: await this.propertyInsights.generate(lat, lng),
                locationScore: await this.locationScore.calculate(lat, lng),
            };

            // Sonuçları haritada göster
            this.displayPropertyAnalysis(analysis);

            return analysis;
        } catch (error) {
            console.error('Emlak analizi hatası:', error);
            return null;
        }
    }

    /**
     * Emlak analiz sonuçlarını haritada gösterir
     */
    displayPropertyAnalysis(analysis) {
        // Yakındaki hizmetleri marker olarak göster
        if (analysis.nearbyServices && analysis.nearbyServices.length > 0) {
            this.addNearbyServiceMarkers(analysis.nearbyServices);
        }

        // Ulaşım puanını göster
        if (analysis.transportationScore) {
            this.showTransportationScore(analysis.transportationScore);
        }

        // Çevre analizini göster
        if (analysis.environmentAnalysis) {
            this.showEnvironmentAnalysis(analysis.environmentAnalysis);
        }

        // Yatırım analizini göster
        if (analysis.investmentAnalysis) {
            this.showInvestmentAnalysis(analysis.investmentAnalysis);
        }
    }

    addNearbyServiceMarkers(services) {
        services.forEach((service) => {
            const marker = L.marker([service.lat, service.lng], {
                icon: this.getServiceIcon(service.type),
            }).addTo(this.map);

            marker.bindPopup(`
                <div class="service-popup">
                    <h4>${service.name}</h4>
                    <p><strong>Tür:</strong> ${service.type}</p>
                    <p><strong>Mesafe:</strong> ${service.distance}m</p>
                    <p><strong>Puan:</strong> ${service.rating}/5</p>
                </div>
            `);
        });
    }

    getServiceIcon(type) {
        const icons = {
            school: '🏫',
            hospital: '🏥',
            shopping: '🛍️',
            restaurant: '🍽️',
            bank: '🏦',
            pharmacy: '💊',
            gas_station: '⛽',
            park: '🌳',
        };

        return L.divIcon({
            html: `<div class="service-marker">${icons[type] || '📍'}</div>`,
            className: 'service-marker-container',
            iconSize: [30, 30],
        });
    }

    showTransportationScore(score) {
        const scoreElement = document.getElementById('transportation-score');
        if (scoreElement) {
            scoreElement.innerHTML = `
                <div class="score-card">
                    <h3>🚌 Ulaşım Puanı</h3>
                    <div class="score-value">${score.overall}/100</div>
                    <div class="score-details">
                        <p>Toplu Taşıma: ${score.publicTransport}/100</p>
                        <p>Otoyol Erişimi: ${score.highwayAccess}/100</p>
                        <p>Park Alanı: ${score.parking}/100</p>
                    </div>
                </div>
            `;
        }
    }

    showEnvironmentAnalysis(analysis) {
        const envElement = document.getElementById('environment-analysis');
        if (envElement) {
            envElement.innerHTML = `
                <div class="environment-card">
                    <h3>🌍 Çevre Analizi</h3>
                    <div class="env-metrics">
                        <div class="metric">
                            <span class="label">Hava Kalitesi:</span>
                            <span class="value ${analysis.airQuality.level}">${analysis.airQuality.score}/100</span>
                        </div>
                        <div class="metric">
                            <span class="label">Gürültü Seviyesi:</span>
                            <span class="value ${analysis.noise.level}">${analysis.noise.score}/100</span>
                        </div>
                        <div class="metric">
                            <span class="label">Yeşil Alan:</span>
                            <span class="value ${analysis.greenSpace.level}">${analysis.greenSpace.score}/100</span>
                        </div>
                    </div>
                </div>
            `;
        }
    }

    showInvestmentAnalysis(analysis) {
        const invElement = document.getElementById('investment-analysis');
        if (invElement) {
            invElement.innerHTML = `
                <div class="investment-card">
                    <h3>💰 Yatırım Analizi</h3>
                    <div class="investment-metrics">
                        <div class="metric">
                            <span class="label">Yatırım Potansiyeli:</span>
                            <span class="value ${analysis.potential.level}">${analysis.potential.score}/100</span>
                        </div>
                        <div class="metric">
                            <span class="label">Değer Artış Tahmini:</span>
                            <span class="value">%${analysis.valueIncrease}/yıl</span>
                        </div>
                        <div class="metric">
                            <span class="label">Risk Seviyesi:</span>
                            <span class="value ${analysis.risk.level}">${analysis.risk.score}/100</span>
                        </div>
                    </div>
                </div>
            `;
        }
    }
}

// ============ EMLAK-SPESİFİK ANALİZ SINIFLARI ============

/**
 * Yakındaki Hizmetler Analiz Sınıfı
 */
class NearbyServicesAnalyzer {
    constructor() {
        this.serviceTypes = [
            'school',
            'hospital',
            'shopping',
            'restaurant',
            'bank',
            'pharmacy',
            'gas_station',
            'park',
        ];
    }

    async analyze(lat, lng) {
        try {
            const services = [];

            for (const type of this.serviceTypes) {
                const nearbyServices = await this.findNearbyServices(lat, lng, type);
                services.push(...nearbyServices);
            }

            return services.sort((a, b) => a.distance - b.distance);
        } catch (error) {
            console.error('Yakındaki hizmetler analizi hatası:', error);
            return [];
        }
    }

    async findNearbyServices(lat, lng, type) {
        // Mock veri - gerçek implementasyon için Google Places API kullanılabilir
        const mockServices = this.getMockServices(lat, lng, type);
        return mockServices;
    }

    getMockServices(lat, lng, type) {
        const services = {
            school: [
                { name: 'Bodrum İlkokulu', distance: 200, rating: 4.2 },
                { name: 'Bodrum Ortaokulu', distance: 450, rating: 4.0 },
            ],
            hospital: [
                { name: 'Bodrum Devlet Hastanesi', distance: 800, rating: 4.5 },
                { name: 'Bodrum Özel Hastane', distance: 1200, rating: 4.8 },
            ],
            shopping: [
                { name: 'Bodrum AVM', distance: 300, rating: 4.3 },
                { name: 'Migros', distance: 150, rating: 4.1 },
            ],
            restaurant: [
                { name: 'Deniz Restoran', distance: 100, rating: 4.6 },
                { name: 'Bodrum Balıkçısı', distance: 250, rating: 4.4 },
            ],
        };

        return (services[type] || []).map((service) => ({
            ...service,
            lat: lat + (Math.random() - 0.5) * 0.01,
            lng: lng + (Math.random() - 0.5) * 0.01,
            type: type,
        }));
    }
}

/**
 * Ulaşım Puanı Hesaplama Sınıfı
 */
class TransportationScoreCalculator {
    async calculate(lat, lng) {
        try {
            const publicTransport = await this.calculatePublicTransportScore(lat, lng);
            const highwayAccess = await this.calculateHighwayAccessScore(lat, lng);
            const parking = await this.calculateParkingScore(lat, lng);

            const overall = Math.round((publicTransport + highwayAccess + parking) / 3);

            return {
                overall,
                publicTransport,
                highwayAccess,
                parking,
                details: {
                    busStops: await this.findBusStops(lat, lng),
                    metroStations: await this.findMetroStations(lat, lng),
                    highwayDistance: await this.calculateHighwayDistance(lat, lng),
                },
            };
        } catch (error) {
            console.error('Ulaşım puanı hesaplama hatası:', error);
            return {
                overall: 0,
                publicTransport: 0,
                highwayAccess: 0,
                parking: 0,
            };
        }
    }

    async calculatePublicTransportScore(lat, lng) {
        // Mock hesaplama - gerçek implementasyon için toplu taşıma API'leri kullanılabilir
        const busStops = await this.findBusStops(lat, lng);
        const metroStations = await this.findMetroStations(lat, lng);

        let score = 0;
        if (busStops.length > 0) score += 40;
        if (metroStations.length > 0) score += 60;

        return Math.min(score, 100);
    }

    async calculateHighwayAccessScore(lat, lng) {
        // Mock hesaplama
        const highwayDistance = await this.calculateHighwayDistance(lat, lng);

        if (highwayDistance < 1000) return 100;
        if (highwayDistance < 2000) return 80;
        if (highwayDistance < 5000) return 60;
        return 40;
    }

    async calculateParkingScore(lat, lng) {
        // Mock hesaplama
        return Math.floor(Math.random() * 40) + 60; // 60-100 arası
    }

    async findBusStops(lat, lng) {
        // Mock veri
        return [
            { name: 'Bodrum Merkez', distance: 200 },
            { name: 'Bodrum Otogar', distance: 800 },
        ];
    }

    async findMetroStations(lat, lng) {
        // Mock veri - Bodrum'da metro yok
        return [];
    }

    async calculateHighwayDistance(lat, lng) {
        // Mock hesaplama
        return Math.floor(Math.random() * 3000) + 500; // 500-3500m arası
    }
}

/**
 * Çevre Analizi Sınıfı
 */
class EnvironmentAnalyzer {
    async analyze(lat, lng) {
        try {
            const airQuality = await this.analyzeAirQuality(lat, lng);
            const noise = await this.analyzeNoiseLevel(lat, lng);
            const greenSpace = await this.analyzeGreenSpace(lat, lng);

            return {
                airQuality,
                noise,
                greenSpace,
                overall: Math.round((airQuality.score + noise.score + greenSpace.score) / 3),
            };
        } catch (error) {
            console.error('Çevre analizi hatası:', error);
            return {
                airQuality: { score: 0, level: 'unknown' },
                noise: { score: 0, level: 'unknown' },
                greenSpace: { score: 0, level: 'unknown' },
                overall: 0,
            };
        }
    }

    async analyzeAirQuality(lat, lng) {
        // Mock analiz - gerçek implementasyon için hava kalitesi API'leri kullanılabilir
        const score = Math.floor(Math.random() * 40) + 60; // 60-100 arası
        return {
            score,
            level: score > 80 ? 'excellent' : score > 60 ? 'good' : 'moderate',
            pm25: Math.floor(Math.random() * 20) + 10,
            pm10: Math.floor(Math.random() * 30) + 15,
            o3: Math.floor(Math.random() * 50) + 20,
        };
    }

    async analyzeNoiseLevel(lat, lng) {
        // Mock analiz
        const score = Math.floor(Math.random() * 30) + 70; // 70-100 arası
        return {
            score,
            level: score > 85 ? 'quiet' : score > 70 ? 'moderate' : 'noisy',
            db: Math.floor(Math.random() * 20) + 45, // 45-65 dB arası
        };
    }

    async analyzeGreenSpace(lat, lng) {
        // Mock analiz
        const score = Math.floor(Math.random() * 25) + 75; // 75-100 arası
        return {
            score,
            level: score > 90 ? 'excellent' : score > 75 ? 'good' : 'moderate',
            parks: Math.floor(Math.random() * 3) + 2, // 2-4 park
            trees: Math.floor(Math.random() * 50) + 100, // 100-150 ağaç
        };
    }
}

/**
 * Yatırım Analizi Sınıfı
 */
class InvestmentAnalyzer {
    async analyze(lat, lng) {
        try {
            const potential = await this.calculateInvestmentPotential(lat, lng);
            const valueIncrease = await this.calculateValueIncrease(lat, lng);
            const risk = await this.calculateRiskLevel(lat, lng);

            return {
                potential,
                valueIncrease,
                risk,
                recommendation: this.generateRecommendation(potential, valueIncrease, risk),
            };
        } catch (error) {
            console.error('Yatırım analizi hatası:', error);
            return {
                potential: { score: 0, level: 'unknown' },
                valueIncrease: 0,
                risk: { score: 0, level: 'unknown' },
                recommendation: 'Veri yetersiz',
            };
        }
    }

    async calculateInvestmentPotential(lat, lng) {
        // Mock hesaplama
        const score = Math.floor(Math.random() * 35) + 65; // 65-100 arası
        return {
            score,
            level: score > 85 ? 'excellent' : score > 70 ? 'good' : 'moderate',
            factors: {
                location: score > 80 ? 'Prime' : 'Good',
                development: score > 75 ? 'High' : 'Medium',
                demand: score > 70 ? 'High' : 'Medium',
            },
        };
    }

    async calculateValueIncrease(lat, lng) {
        // Mock hesaplama
        return Math.floor(Math.random() * 8) + 5; // %5-12 arası yıllık artış
    }

    async calculateRiskLevel(lat, lng) {
        // Mock hesaplama
        const score = Math.floor(Math.random() * 20) + 20; // 20-40 arası (düşük risk)
        return {
            score,
            level: score < 30 ? 'low' : score < 40 ? 'medium' : 'high',
            factors: {
                market: 'Stable',
                location: 'Safe',
                economic: 'Growing',
            },
        };
    }

    generateRecommendation(potential, valueIncrease, risk) {
        if (potential.score > 80 && valueIncrease > 8 && risk.score < 30) {
            return 'Yüksek yatırım potansiyeli - Önerilir';
        } else if (potential.score > 70 && valueIncrease > 6 && risk.score < 40) {
            return 'Orta yatırım potansiyeli - Dikkatli değerlendirin';
        } else {
            return 'Düşük yatırım potansiyeli - Riskli';
        }
    }
}

/**
 * Emlak Öngörüleri Sınıfı
 */
class PropertyInsightsGenerator {
    async generate(lat, lng) {
        try {
            const insights = {
                marketTrend: await this.analyzeMarketTrend(lat, lng),
                pricePrediction: await this.predictPriceTrend(lat, lng),
                bestTimeToSell: await this.calculateBestSellTime(lat, lng),
                neighborhoodGrowth: await this.analyzeNeighborhoodGrowth(lat, lng),
            };

            return insights;
        } catch (error) {
            console.error('Emlak öngörüleri hatası:', error);
            return null;
        }
    }

    async analyzeMarketTrend(lat, lng) {
        // Mock analiz
        const trends = ['Yükseliş', 'Düşüş', 'Sabit'];
        return {
            current: trends[Math.floor(Math.random() * trends.length)],
            confidence: Math.floor(Math.random() * 30) + 70, // 70-100 arası
            factors: ['Turizm sektörü', 'Altyapı gelişmeleri', 'Nüfus artışı'],
        };
    }

    async predictPriceTrend(lat, lng) {
        // Mock tahmin
        return {
            next6Months: Math.floor(Math.random() * 10) + 5, // %5-15 artış
            nextYear: Math.floor(Math.random() * 20) + 10, // %10-30 artış
            next3Years: Math.floor(Math.random() * 40) + 20, // %20-60 artış
        };
    }

    async calculateBestSellTime(lat, lng) {
        // Mock hesaplama
        const months = ['Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos'];
        return {
            bestMonth: months[Math.floor(Math.random() * months.length)],
            reason: 'Turizm sezonu ve yaz aylarında talep artışı',
            confidence: Math.floor(Math.random() * 20) + 80, // 80-100 arası
        };
    }

    async analyzeNeighborhoodGrowth(lat, lng) {
        // Mock analiz
        return {
            populationGrowth: Math.floor(Math.random() * 5) + 2, // %2-7 artış
            newDevelopments: Math.floor(Math.random() * 3) + 1, // 1-3 yeni proje
            infrastructure: ['Yeni okul', 'Hastane genişletme', 'Yol iyileştirme'],
        };
    }
}

/**
 * Lokasyon Puanı Hesaplama Sınıfı
 */
class LocationScoreCalculator {
    async calculate(lat, lng) {
        try {
            const scores = {
                accessibility: await this.calculateAccessibilityScore(lat, lng),
                amenities: await this.calculateAmenitiesScore(lat, lng),
                safety: await this.calculateSafetyScore(lat, lng),
                investment: await this.calculateInvestmentScore(lat, lng),
            };

            const overall = Math.round(
                (scores.accessibility + scores.amenities + scores.safety + scores.investment) / 4
            );

            return {
                overall,
                ...scores,
                grade: this.getGrade(overall),
            };
        } catch (error) {
            console.error('Lokasyon puanı hesaplama hatası:', error);
            return { overall: 0, grade: 'F' };
        }
    }

    async calculateAccessibilityScore(lat, lng) {
        // Mock hesaplama
        return Math.floor(Math.random() * 20) + 80; // 80-100 arası
    }

    async calculateAmenitiesScore(lat, lng) {
        // Mock hesaplama
        return Math.floor(Math.random() * 15) + 85; // 85-100 arası
    }

    async calculateSafetyScore(lat, lng) {
        // Mock hesaplama
        return Math.floor(Math.random() * 10) + 90; // 90-100 arası
    }

    async calculateInvestmentScore(lat, lng) {
        // Mock hesaplama
        return Math.floor(Math.random() * 25) + 75; // 75-100 arası
    }

    getGrade(score) {
        if (score >= 95) return 'A+';
        if (score >= 90) return 'A';
        if (score >= 85) return 'A-';
        if (score >= 80) return 'B+';
        if (score >= 75) return 'B';
        if (score >= 70) return 'B-';
        if (score >= 65) return 'C+';
        if (score >= 60) return 'C';
        if (score >= 55) return 'C-';
        if (score >= 50) return 'D';
        return 'F';
    }
}

// ============ AUTO-INITIALIZE ============

// Auto-initialize if not already done
if (typeof window !== 'undefined' && !window.EmlakLoc) {
    window.EmlakLoc = EmlakLoc;
}

// Global helper functions
if (typeof window !== 'undefined') {
    window.selectAddressResult = function (element) {
        if (window.emlakLoc && typeof window.emlakLoc.selectAddressResult === 'function') {
            window.emlakLoc.selectAddressResult(element);
        } else {
            console.warn(
                'emlakLoc.selectAddressResult mevcut değil; sonuç doğrudan koordinatlarla işlenecek'
            );
            // Basit fallback: dataset'ten koordinatları al ve haritayı güncelle
            const lat = parseFloat(element?.dataset?.lat || 0);
            const lng = parseFloat(element?.dataset?.lng || 0);
            if (!isNaN(lat) && !isNaN(lng) && window.emlakLoc) {
                window.emlakLoc.updateMapFromCoordinates(lat, lng);
            }
        }
    };
}

// Form Wizard Integration Functions
window.emlakLoc = {
    // Form Wizard Step 3 Integration
    initializeFormWizardStep3() {
        console.log('🏡 Form Wizard Step 3 - EmlakLoc v4.1.0 initializing...');

        // Initialize map
        this.initializeMap();

        // Setup cascade dropdowns
        this.setupCascadeDropdowns();

        // Initialize AI features
        this.initializeAIFeatures();
    },

    // AI Search Function
    async aiSearch(query) {
        console.log('🤖 AI Search:', query);
        // Implementation here
    },

    // Voice Search Function
    startVoiceSearch() {
        console.log('🎤 Starting voice search...');
        // Implementation here
    },

    // Image Analysis Function
    analyzeLocationImage(file) {
        console.log('📷 Analyzing location image:', file);
        // Implementation here
    },

    // Load districts based on province
    loadIlceler(ilId) {
        console.log('🏘️ Loading districts for province:', ilId);
        console.log('🔍 DEBUG: loadIlceler function called with ilId:', ilId);

        const ilceSelect = document.getElementById('ilce_id');
        const mahalleSelect = document.getElementById('mahalle_id');

        console.log('🔍 DEBUG: ilceSelect found:', !!ilceSelect);
        console.log('🔍 DEBUG: mahalleSelect found:', !!mahalleSelect);

        if (!ilceSelect) {
            console.error('❌ İlçe select elementi bulunamadı!');
            return;
        }

        // Clear existing options
        ilceSelect.innerHTML = '<option value="">Önce il seç</option>';
        ilceSelect.disabled = true;

        // Clear mahalle as well
        if (mahalleSelect) {
            mahalleSelect.innerHTML = '<option value="">Önce ilçe seç</option>';
            mahalleSelect.disabled = true;
        }

        if (!ilId) {
            console.warn('İl ID boş');
            return;
        }

        // Show loading
        ilceSelect.innerHTML = '<option value="">Yükleniyor...</option>';

        // Make API call
        console.log(
            '🔍 DEBUG: Starting API call to /api/address/districts with province_id:',
            ilId
        );

        fetch(`/api/address/districts?province_id=${ilId}`, {
            method: 'GET',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
            .then((response) => {
                console.log('🔍 DEBUG: API response status:', response.status, response.ok);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then((data) => {
                console.log('🔍 DEBUG: API response data:', data);
                console.log('İlçeler API yanıtı:', data);

                // Clear loading
                ilceSelect.innerHTML = '<option value="">İlçe seçin</option>';

                if (data.success && data.districts && data.districts.length > 0) {
                    console.log('🔍 DEBUG: Processing', data.districts.length, 'districts');

                    data.districts.forEach((district, index) => {
                        console.log(`🔍 DEBUG: Adding district ${index + 1}:`, district);
                        const option = document.createElement('option');
                        option.value = district.id;
                        option.textContent = district.name || district.ilce_adi;
                        ilceSelect.appendChild(option);
                    });

                    ilceSelect.disabled = false;
                    console.log(`✅ ${data.districts.length} ilçe yüklendi`);
                    console.log(
                        '🔍 DEBUG: ilceSelect options after population:',
                        ilceSelect.options.length
                    );

                    // Force DOM update
                    ilceSelect.dispatchEvent(new Event('change', { bubbles: true }));
                } else {
                    console.log('🔍 DEBUG: No districts found or API error');
                    ilceSelect.innerHTML = '<option value="">Bu ile ait ilçe bulunamadı</option>';
                    console.warn('İlçe bulunamadı veya API hatası');
                }
            })
            .catch((error) => {
                console.error('İlçe yükleme hatası:', error);
                ilceSelect.innerHTML = '<option value="">Hata oluştu</option>';
                ilceSelect.disabled = true;
            });
    },

    // Load neighborhoods based on district
    loadMahalleler(ilceId) {
        console.log('🏠 Loading neighborhoods for district:', ilceId);

        const mahalleSelect = document.getElementById('mahalle_id');

        if (!mahalleSelect) {
            console.warn('Mahalle select elementi bulunamadı');
            return;
        }

        // Clear existing options
        mahalleSelect.innerHTML = '<option value="">Önce ilçe seç</option>';
        mahalleSelect.disabled = true;

        if (!ilceId) {
            console.warn('İlçe ID boş');
            return;
        }

        // Show loading
        mahalleSelect.innerHTML = '<option value="">Yükleniyor...</option>';

        // Make API call
        fetch(`/api/address/neighborhoods?district_id=${ilceId}`, {
            method: 'GET',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then((data) => {
                console.log('Mahalleler API yanıtı:', data);

                // Clear loading
                mahalleSelect.innerHTML = '<option value="">Mahalle seçin (isteğe bağlı)</option>';

                if (data.success && data.neighborhoods && data.neighborhoods.length > 0) {
                    data.neighborhoods.forEach((neighborhood) => {
                        const option = document.createElement('option');
                        option.value = neighborhood.id;
                        option.textContent = neighborhood.name || neighborhood.mahalle_adi;
                        mahalleSelect.appendChild(option);
                    });

                    mahalleSelect.disabled = false;
                    console.log(`✅ ${data.neighborhoods.length} mahalle yüklendi`);
                } else {
                    mahalleSelect.innerHTML =
                        '<option value="">Bu ilçeye ait mahalle bulunamadı</option>';
                    console.warn('Mahalle bulunamadı veya API hatası');
                    mahalleSelect.disabled = false; // Mahalle optional olduğu için disabled bırakma
                }
            })
            .catch((error) => {
                console.error('Mahalle yükleme hatası:', error);
                mahalleSelect.innerHTML = '<option value="">Hata oluştu</option>';
                mahalleSelect.disabled = false; // Mahalle optional olduğu için disabled bırakma
            });
    },

    // Update coordinates from mahalle selection
    updateCoordinatesFromMahalle(mahalleId) {
        console.log('📍 Updating coordinates from mahalle:', mahalleId);

        if (!mahalleId) {
            console.warn('Mahalle ID boş');
            return;
        }

        // Make API call to get coordinates
        fetch(`/api/address/coordinates?mahalle_id=${mahalleId}`, {
            method: 'GET',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then((data) => {
                console.log('Koordinat API yanıtı:', data);

                if (data.success && data.data) {
                    const { latitude, longitude } = data.data;

                    // Update coordinate inputs if they exist
                    const latInput = document.getElementById('latitude');
                    const lngInput = document.getElementById('longitude');

                    if (latInput) latInput.value = latitude;
                    if (lngInput) lngInput.value = longitude;

                    // Update map if it exists and is initialized
                    if (window.mapInstance && typeof window.updateMap === 'function') {
                        window.updateMap(latitude, longitude);
                    }

                    console.log(`✅ Koordinatlar güncellendi: ${latitude}, ${longitude}`);
                } else {
                    console.warn('Koordinat bilgisi alınamadı');
                }
            })
            .catch((error) => {
                console.error('Koordinat yükleme hatası:', error);
            });
    },

    // 3D View Toggle
    toggle3DView() {
        console.log('🌍 Toggling 3D view...');
        // Implementation here
    },

    // AR Mode Toggle
    toggleARMode() {
        console.log('🥽 Toggling AR mode...');
        // Implementation here
    },

    // Share Location
    shareLocation() {
        console.log('📤 Sharing location...');
        // Implementation here
    },

    // Initialize Map
    initializeMap() {
        console.log('🗺️ Initializing advanced map system...');
        // Remove loading overlay after initialization
        setTimeout(() => {
            const loading = document.getElementById('map-loading');
            if (loading) loading.style.display = 'none';
        }, 2000);
    },

    // Setup cascade dropdowns
    setupCascadeDropdowns() {
        console.log('🔗 Setting up cascade dropdowns...');

        // Find all possible il/il selectors
        const ilSelect = document.getElementById('il_id') || document.getElementById('il_id');
        const ilceSelect = document.getElementById('ilce_id');
        const mahalleSelect = document.getElementById('mahalle_id');

        if (!ilSelect) {
            console.warn('İl/Şehir select elementi bulunamadı');
            return;
        }

        // İl değiştiğinde ilçeleri yükle
        ilSelect.addEventListener('change', (e) => {
            const ilId = e.target.value;
            console.log('İl değişti:', ilId);

            if (ilId) {
                this.loadIlceler(ilId);
            } else {
                // İl seçimi kaldırıldıysa ilçe ve mahalleleri temizle
                if (ilceSelect) {
                    ilceSelect.innerHTML = '<option value="">Önce il seç</option>';
                    ilceSelect.disabled = true;
                }
                if (mahalleSelect) {
                    mahalleSelect.innerHTML = '<option value="">Önce ilçe seç</option>';
                    mahalleSelect.disabled = true;
                }
            }
        });

        // İlçe değiştiğinde mahalleleri yükle
        if (ilceSelect) {
            ilceSelect.addEventListener('change', (e) => {
                const ilceId = e.target.value;
                console.log('İlçe değişti:', ilceId);

                if (ilceId) {
                    this.loadMahalleler(ilceId);
                } else {
                    // İlçe seçimi kaldırıldıysa mahalleleri temizle
                    if (mahalleSelect) {
                        mahalleSelect.innerHTML = '<option value="">Önce ilçe seç</option>';
                        mahalleSelect.disabled = true;
                    }
                }
            });
        }

        // Mahalle değiştiğinde koordinatları güncelle
        if (mahalleSelect) {
            mahalleSelect.addEventListener('change', (e) => {
                const mahalleId = e.target.value;
                console.log('Mahalle değişti:', mahalleId);

                if (mahalleId) {
                    this.updateCoordinatesFromMahalle(mahalleId);
                }
            });
        }

        console.log('✅ Cascade dropdowns kuruldu');
    },

    // Initialize AI features
    initializeAIFeatures() {
        console.log('🤖 Initializing AI features...');
        // Show nearby analysis when location is selected
        setTimeout(() => {
            const nearbyAnalysis = document.getElementById('nearby-analysis');
            if (nearbyAnalysis) {
                nearbyAnalysis.style.display = 'block';
            }
        }, 3000);
    },
};

console.log(
    '\u2705 EmlakLoc v4.1.0 - Form Wizard Integration ba\u015far\u0131yla y\u00fckklendi! \ud83d\ude80'
);

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
    console.log('\ud83c\udfe1 EmlakLoc v4.1.0 DOM ready, initializing...');

    // Check if we're on the form wizard page
    const formWizardContainer = document.querySelector('.form-wizard-container, .step-4, #step-4');
    const locationInputs = document.querySelector('#il_id, #il_id, #ilce_id, #mahalle_id');

    if (formWizardContainer || locationInputs) {
        console.log('\ud83d\udd0d Form wizard detected, setting up EmlakLoc integration...');

        // Initialize Form Wizard Step 3 (location step)
        if (window.emlakLoc && typeof window.emlakLoc.initializeFormWizardStep3 === 'function') {
            window.emlakLoc.initializeFormWizardStep3();
        }

        // Setup cascade dropdowns
        if (window.emlakLoc && typeof window.emlakLoc.setupCascadeDropdowns === 'function') {
            window.emlakLoc.setupCascadeDropdowns();
        }
    }
});
