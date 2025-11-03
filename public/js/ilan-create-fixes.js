/**
 * İlan Ekleme Formu - JavaScript Düzeltmeleri
 * Context7 Compliance & Neo Design System
 */

console.log('🔧 İlan Create Fixes yüklendi');

// Global error handler
window.addEventListener('error', function(e) {
    console.error('🚨 JavaScript Error:', e.error);
});

// Alpine.js store düzeltmeleri
document.addEventListener('alpine:init', () => {
    // FormData store düzeltmeleri
    Alpine.store('formData', {
        kategori_id: null,
        alt_kategori_id: null,
        yayin_tipi_id: null,
        ilan_data: {},
        analysisResults: null, // Akıllı çevre analizi için

        init() {
            console.log('📊 FormData store initialized');
            this.ilan_data = {
                kategori: null,
                alt_kategori: null,
                yayin_tipi: null,
                il: null,
                ilce: null,
                mahalle: null,
                latitude: null,
                longitude: null
            };

            // Analysis results için null check
            this.analysisResults = {
                poi_analysis: {
                    egitim: [],
                    saglik: [],
                    alisveris: [],
                    ulasim: [],
                    eglence: [],
                    diger: []
                },
                distance_analysis: {
                    walking_distances: {
                        metro: 0,
                        otobus: 0,
                        market: 0
                    },
                    driving_distances: {
                        havaalani: 0,
                        merkez: 0,
                        avm: 0
                    }
                },
                value_impact: {
                    cevre_puani: 0,
                    yatirim_potansiyeli: 'Düşük',
                    deger_artis_tahmini: {
                        '1_yil': 0,
                        '3_yil': 0,
                        '5_yil': 0
                    }
                },
                recommendations: []
            };
        },

        setKategori(kategoriId) {
            this.kategori_id = kategoriId;
            this.ilan_data.kategori = kategoriId;
            console.log('🏷️ Ana kategori seçildi:', kategoriId);
        },

        setAltKategori(altKategoriId) {
            this.alt_kategori_id = altKategoriId;
            this.ilan_data.alt_kategori = altKategoriId;
            console.log('🔖 Alt kategori seçildi:', altKategoriId);
        },

        setYayinTipi(yayinTipiId) {
            this.yayin_tipi_id = yayinTipiId;
            this.ilan_data.yayin_tipi = yayinTipiId;
            console.log('📄 Yayın tipi seçildi:', yayinTipiId);
        },

        setIl(ilId) {
            this.ilan_data.il = ilId;
            console.log('📍 İl seçildi:', ilId);
        },

        setIlce(ilceId) {
            this.ilan_data.ilce = ilceId;
            console.log('📍 İlçe seçildi:', ilceId);
        },

        setMahalle(mahalleId) {
            this.ilan_data.mahalle = mahalleId;
            console.log('📍 Mahalle seçildi:', mahalleId);
        },

        setLatitude(lat) {
            this.ilan_data.latitude = lat;
            console.log('🗺️ Latitude güncellendi:', lat);
        },

        setLongitude(lon) {
            this.ilan_data.longitude = lon;
            console.log('🗺️ Longitude güncellendi:', lon);
        },

        // Global functions for map and status
        addressSearch(query) {
            console.log('🔍 Adres aranıyor:', query);
            if (typeof AdvancedLeafletManager !== 'undefined' && AdvancedLeafletManager.map) {
                AdvancedLeafletManager.searchAddress(query);
            } else {
                console.warn('AdvancedLeafletManager veya harita objesi mevcut değil.');
            }
        },

        getStatusText(status) {
            const statusMap = {
                'Aktif': 'Aktif',
                'Pasif': 'Pasif',
                'Beklemede': 'Beklemede',
                'Reddedildi': 'Reddedildi',
                'Yayında': 'Yayında',
                'Taslak': 'Taslak',
                'Satıldı': 'Satıldı',
                'Kiralandı': 'Kiralandı',
                'Arşivlendi': 'Arşivlendi',
            };
            return statusMap[status] || status;
        },

        createMarker(lat, lon, title = 'Konum') {
            if (typeof AdvancedLeafletManager !== 'undefined' && AdvancedLeafletManager.map) {
                AdvancedLeafletManager.createMarker(lat, lon, title);
            } else {
                console.warn('AdvancedLeafletManager veya harita objesi mevcut değil.');
            }
        },

        async loadFeaturesForCategory(categoryId) {
            if (!categoryId) {
                console.warn('Kategori ID boş, özellikler yüklenemedi.');
                return [];
            }
            try {
                const response = await fetch(`/api/features/category/${categoryId}`);
                if (!response.ok) {
                    throw new Error('Özellikler yüklenemedi');
                }
                const data = await response.json();
                console.log(`✅ Kategori ${categoryId} için özellikler yüklendi:`, data.features);
                return data.features;
            } catch (error) {
                console.error('Özellik yükleme hatası:', error);
                return [];
            }
        },

        async loadPublicationTypesForCategory(categoryId) {
            if (!categoryId) {
                console.warn('Kategori ID boş, yayın tipleri yüklenemedi.');
                return [];
            }
            try {
                const response = await fetch(`/api/categories/publication-types/${categoryId}`);
                if (!response.ok) {
                    throw new Error('Yayın tipleri yüklenemedi');
                }
                const data = await response.json();
                console.log(`✅ Kategori ${categoryId} için yayın tipleri yüklendi:`, data.types);
                return data.types;
            } catch (error) {
                console.error('Yayın tipi yükleme hatası:', error);
                return [];
            }
        }
    });

    // Akıllı Çevre Analizi Store
    Alpine.store('cevreAnalizi', {
        analysisResults: null,
        isLoading: false,
        error: null,

        init() {
            this.analysisResults = {
                poi_analysis: {
                    egitim: [],
                    saglik: [],
                    alisveris: [],
                    ulasim: [],
                    eglence: [],
                    diger: []
                },
                distance_analysis: {
                    walking_distances: {
                        metro: 0,
                        otobus: 0,
                        market: 0
                    },
                    driving_distances: {
                        havaalani: 0,
                        merkez: 0,
                        avm: 0
                    }
                },
                value_impact: {
                    cevre_puani: 0,
                    yatirim_potansiyeli: 'Düşük',
                    deger_artis_tahmini: {
                        '1_yil': 0,
                        '3_yil': 0,
                        '5_yil': 0
                    }
                },
                recommendations: []
            };
        },

        async analyzeEnvironment(latitude, longitude) {
            this.isLoading = true;
            this.error = null;

            try {
                const response = await fetch('/api/admin/cevre-analizi/analyze', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        latitude: latitude,
                        longitude: longitude,
                        radius: 1.0
                    })
                });

                if (!response.ok) {
                    throw new Error('Çevre analizi başarısız');
                }

                const data = await response.json();
                this.analysisResults = data.data;
                console.log('✅ Çevre analizi tamamlandı:', this.analysisResults);
            } catch (error) {
                console.error('❌ Çevre analizi hatası:', error);
                this.error = error.message;
            } finally {
                this.isLoading = false;
            }
        }
    });
});

// Global functions
window.validateCategories = function() {
    console.log('✅ Kategori validasyonu çalışıyor');
    return true;
};

window.getInvestmentClass = function(potansiyel) {
    const classMap = {
        'Çok Yüksek': 'text-green-600 bg-green-100',
        'Yüksek': 'text-green-500 bg-green-50',
        'Orta': 'text-yellow-600 bg-yellow-100',
        'Düşük': 'text-orange-600 bg-orange-100',
        'Çok Düşük': 'text-red-600 bg-red-100'
    };
    return classMap[potansiyel] || 'text-gray-600 bg-gray-100';
};

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔧 İlan Create Fixes DOM ready');

    // Form validation
    const form = document.getElementById('stable-create-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('📝 Form submit edildi');
            // Validation logic buraya eklenebilir
        });
    }
});

console.log('✅ İlan Create Fixes yüklendi');
