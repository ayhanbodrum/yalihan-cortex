/**
 * EmlakPro - Gelişmiş Konum Seçici JavaScript (Harita Entegrasyonlu)
 * Bu dosya, ülke, il, ilçe ve mahalle seçimi için harita entegrasyonlu bir arayüz sağlar.
 *
 * Sürüm: 3.0 - Harita entegrasyonu eklenmiştir
 * Son Güncelleme: 22 Mayıs 2025
 */

class LocationMapHelper {
    constructor(options) {
        // Varsayılan ayarlar
        this.defaults = {
            uniqueId: 'loc_default',
            initialLat: 38.4237,
            initialLng: 27.1428,
            initialZoom: 6,
            language: 'tr',
            showMap: true,
        };

        // Kullanıcı ayarlarını varsayılanlarla birleştir
        this.options = { ...this.defaults, ...options };

        // Element referansları
        const containerId = this.options.uniqueId;

        // EmlakPro standardı ID'leri destekleme (hem id="konum_1_il_select" hem de id="il_select" formatları)
        const findElement = (suffix) => {
            // Önce bileşen özel ID'si ile ara
            const specificId = `${containerId}_${suffix}`;
            const specificElement = document.getElementById(specificId);

            // Bulunamadıysa, standart ID'yi dene
            if (!specificElement) {
                console.log(
                    `Özel ID (${specificId}) ile element bulunamadı, standart ID (${suffix}) deneniyor...`
                );
                return document.getElementById(suffix);
            }

            return specificElement;
        };

        this.elements = {
            container: document.getElementById(containerId),
            mapContainer: findElement('map'),
            ulkeSelect: findElement('ulke_select'),
            ilSelect: findElement('il_select'),
            ilceSelect: findElement('ilce_select'),
            mahalleSelect: findElement('mahalle_select'),
            latitudeInput: findElement('latitude'),
            longitudeInput: findElement('longitude'),
            searchButton: findElement('map_search'),
            resetButton: findElement('map_reset'),
        };

        // Harita ve işaretçi
        this.map = null;
        this.marker = null;

        // Haritayı başlat
        if (this.options.showMap && this.elements.mapContainer) {
            this.initMap();
        }

        // Olay dinleyicileri ekle
        this.attachEventListeners();
    }

    /**
     * Haritayı başlatır
     */
    initMap() {
        console.log('Harita başlatılıyor...', {
            container: this.elements.mapContainer,
            elementExists: document.getElementById(this.elements.mapContainer.id) !== null,
            dimensions: {
                width: this.elements.mapContainer.offsetWidth,
                height: this.elements.mapContainer.offsetHeight,
                style: window.getComputedStyle(this.elements.mapContainer),
            },
            options: this.options,
        });

        try {
            this.map = L.map(this.elements.mapContainer).setView(
                [this.options.initialLat, this.options.initialLng],
                this.options.initialZoom
            );
            console.log('Harita başarıyla oluşturuldu');

            // Harita tile layer'ını ekle
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution:
                    '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19,
            }).addTo(this.map);
            console.log('Harita katmanı eklendi');
        } catch (error) {
            console.error('Harita oluşturulurken hata:', error);
        }

        // Arama kontrolü ekle
        L.Control.geocoder().addTo(this.map);

        // Varsayılan işaretçi ekle
        if (this.elements.latitudeInput.value && this.elements.longitudeInput.value) {
            this.addMarker(
                parseFloat(this.elements.latitudeInput.value),
                parseFloat(this.elements.longitudeInput.value)
            );
        }

        // Harita tıklama olayı
        this.map.on('click', (e) => {
            this.addMarker(e.latlng.lat, e.latlng.lng);
            this.reverseGeocode(e.latlng.lat, e.latlng.lng);
        });

        // Arama ve sıfırlama butonları için olay dinleyicileri
        if (this.elements.searchButton) {
            this.elements.searchButton.addEventListener('click', () => this.openSearchModal());
        } else {
            console.warn('Arama butonu bulunamadı. Arama özelliği devre dışı.');
        }

        if (this.elements.resetButton) {
            this.elements.resetButton.addEventListener('click', () => this.resetMap());
        } else {
            console.warn('Sıfırlama butonu bulunamadı. Sıfırlama özelliği devre dışı.');
        }
    }

    /**
     * Haritaya işaretçi ekler
     * @param {number} lat - Enlem
     * @param {number} lng - Boylam
     */
    addMarker(lat, lng) {
        // Önceki işaretçiyi kaldır
        if (this.marker) {
            this.map.removeLayer(this.marker);
        }

        // Yeni işaretçi ekle
        this.marker = L.marker([lat, lng], {
            draggable: true,
        }).addTo(this.map);

        // Input değerlerini güncelle
        this.elements.latitudeInput.value = lat.toFixed(6);
        this.elements.longitudeInput.value = lng.toFixed(6);

        // Sürükleme olayı
        this.marker.on('dragend', (e) => {
            const position = e.target.getLatLng();
            this.elements.latitudeInput.value = position.lat.toFixed(6);
            this.elements.longitudeInput.value = position.lng.toFixed(6);
            this.reverseGeocode(position.lat, position.lng);
        });
    }

    /**
     * Tersine coğrafi kodlama yapar (koordinatlardan adres bilgisi)
     * @param {number} lat - Enlem
     * @param {number} lng - Boylam
     */
    reverseGeocode(lat, lng) {
        // Nominatim API kullanarak tersine coğrafi kodlama
        fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
            .then((response) => response.json())
            .then((data) => {
                console.log('Konum bilgisi:', data);

                // Ülke bilgisini güncelle
                if (data.address && data.address.country_code) {
                    const countryCode = data.address.country_code.toUpperCase();
                        if (this.elements.ulkeSelect) {
                        this.elements.ulkeSelect.value = countryCode;
                        triggerChanged(this.elements.ulkeSelect)
                    }
                }

                // İl bilgisini güncelle (Türkiye için)
                if (data.address && data.address.state) {
                    const state = data.address.state.toUpperCase();

                    // İl seçimini yap
                    if (this.elements.ilSelect) {
                        const options = this.elements.ilSelect.options;
                        for (let i = 0; i < options.length; i++) {
                            const opt = options[i];
                            if (opt.text.toUpperCase().includes(state)) {
                                this.elements.ilSelect.value = opt.value;
                                triggerChanged(this.elements.ilSelect)
                                break;
                            }
                        }
                    }
                }
            })
            .catch((error) => {
                console.error('Tersine coğrafi kodlama hatası:', error);
            });
    }

    /**
     * İlçeleri getirir
     * @param {string|number} ilId - İl ID
     */
    getIlceler(ilId) {
        if (!ilId) return;

        // İlçe ve mahalle seçimlerini sıfırla
        this.resetSelect(this.elements.ilceSelect);
        this.resetSelect(this.elements.mahalleSelect);

        // İlçeleri getir
        const params = new URLSearchParams({ il_id: ilId });
        const headers = {};
        if (window.__csrfToken) headers['X-CSRF-TOKEN'] = window.__csrfToken;
        fetch(`/api/location/ilceler?${params.toString()}`, { method: 'GET', headers })
            .then((res) => res.json())
            .then((response) => {
                if (response.status === 'success' && response.data && response.data.length > 0) {
                    // İlçe seçeneğini etkinleştir
                    this.elements.ilceSelect.disabled = false;

                    // İlçeleri ekle
                    response.data.forEach((ilce) => {
                        const option = new Option(ilce.ilce_adi, ilce.id);
                        this.elements.ilceSelect.append(option);
                    });

                    // Select2'yi güncelle
                    triggerChanged(this.elements.ilceSelect)

                    // Önceden seçili değer varsa ayarla
                    const selectedValue = this.elements.ilceSelect.dataset.selected;
                    if (selectedValue) {
                        this.elements.ilceSelect.value = selectedValue;
                        triggerChanged(this.elements.ilceSelect)
                    }
                } else {
                    console.log('İlçe verisi bulunamadı');
                }
            })
            .catch((error) => {
                console.error('İlçe getirme hatası:', error);
            });
    }

    /**
     * Mahalleleri getirir
     * @param {number} ilceId - İlçe ID
     */
    getMahalleler(ilceId) {
        if (!ilceId) return;

        // Mahalle seçimini sıfırla
        this.resetSelect(this.elements.mahalleSelect);

        // Mahalleleri getir
        const params = new URLSearchParams({ ilce_id: ilceId });
        const headers = {};
        if (window.__csrfToken) headers['X-CSRF-TOKEN'] = window.__csrfToken;
        fetch(`/api/location/mahalleler?${params.toString()}`, { method: 'GET', headers })
            .then((res) => res.json())
            .then((response) => {
                if (response.status === 'success' && response.data && response.data.length > 0) {
                    // Mahalle seçeneğini etkinleştir
                    this.elements.mahalleSelect.disabled = false;

                    // Mahalleleri ekle
                    response.data.forEach((mahalle) => {
                        const option = new Option(mahalle.mahalle_adi, mahalle.id);
                        this.elements.mahalleSelect.append(option);
                    });

                    // Select2'yi güncelle
                    triggerChanged(this.elements.mahalleSelect)

                    // Önceden seçili değer varsa ayarla
                    const selectedValue = this.elements.mahalleSelect.dataset.selected;
                    if (selectedValue) {
                        this.elements.mahalleSelect.value = selectedValue;
                        triggerChanged(this.elements.mahalleSelect)
                    }
                } else {
                    console.log('Mahalle verisi bulunamadı');
                }
            })
            .catch((error) => {
                console.error('Mahalle getirme hatası:', error);
            });
    }

    /**
     * Select'i sıfırlar
     * @param {HTMLElement} select - Sıfırlanacak select elementi
     */
    resetSelect(select) {
        if (!select) return;

        // Seçenekleri temizle (ilk seçenek hariç)
        const opts = Array.from(select.querySelectorAll('option'));
        opts.slice(1).forEach((o) => o.remove());

        // Devre dışı bırak
        select.disabled = true;

        // Select2'yi güncelle
        triggerChanged(select)
    }

    /**
     * Haritayı arama kutusunu açar
     */
    openSearchModal() {
        // Burada arama modalı açılabilir veya haritanın arama fonksiyonu kullanılabilir
        alert(
            this.options.language === 'tr'
                ? 'Haritanın sağ üst köşesindeki arama ikonunu kullanabilirsiniz.'
                : 'You can use the search icon in the top right corner of the map.'
        );
    }

    /**
     * Harita seçimini sıfırlar
     */
    resetMap() {
        if (!this.map) return;

        // Haritayı başlangıç konumuna getir
        this.map.setView(
            [this.options.initialLat, this.options.initialLng],
            this.options.initialZoom
        );

        // Mevcut işaretçiyi kaldır
        if (this.marker) {
            this.map.removeLayer(this.marker);
            this.marker = null;
        }

        // Input değerlerini sıfırla
        this.elements.latitudeInput.value = this.options.initialLat;
        this.elements.longitudeInput.value = this.options.initialLng;

        // Seçimleri sıfırla
        if (this.elements.ulkeSelect) {
            this.elements.ulkeSelect.value = 'TR'
            triggerChanged(this.elements.ulkeSelect)
        }

        this.resetSelect(this.elements.ilSelect);
        this.resetSelect(this.elements.ilceSelect);
        this.resetSelect(this.elements.mahalleSelect);
    }

    /**
     * Adres araması yapar
     * @param {string} query - Arama sorgusu
     */
    searchAddress(query) {
        if (!query || query.length < 3) return;

        const params = new URLSearchParams({ query });
        const headers = {};
        if (window.__csrfToken) headers['X-CSRF-TOKEN'] = window.__csrfToken;
        fetch(`/api/location/search?${params.toString()}`, { method: 'GET', headers })
            .then((res) => res.json())
            .then((response) => {
                if (response.status === 'success' && response.data && response.data.length > 0) {
                    // Sonucu göster (select2 için kullanılabilir)
                    const results = response.data;
                    console.log('Adres arama sonuçları:', results);

                    // İlk bulunan sonuç için haritayı güncelle
                    const firstResult = results[0];
                    if (firstResult.lat && firstResult.lng) {
                        this.map.setView([firstResult.lat, firstResult.lng], 15);
                        this.addMarker(firstResult.lat, firstResult.lng);

                        // Seçimleri güncelle
                        if (firstResult.il_id) {
                            this.elements.ilSelect.value = firstResult.il_id;
                            if (typeof $.fn !== 'undefined' && $.fn.select2) {
                                window.$(this.elements.ilSelect).trigger('change.select2');
                            } else {
                                this.elements.ilSelect.dispatchEvent(new Event('change'));
                            }

                            // İlçe seçimini bekleyip mahalle seçimini yap
                            setTimeout(() => {
                                if (firstResult.ilce_id) {
                                    this.elements.ilceSelect.value = firstResult.ilce_id;
                                    if (typeof $.fn !== 'undefined' && $.fn.select2) {
                                        window.$(this.elements.ilceSelect).trigger('change.select2');
                                    } else {
                                        this.elements.ilceSelect.dispatchEvent(new Event('change'));
                                    }

                                    // Mahalle seçimini bekle ve seç
                                    setTimeout(() => {
                                        if (firstResult.mahalle_id) {
                                            this.elements.mahalleSelect.value = firstResult.mahalle_id;
                                            if (typeof $.fn !== 'undefined' && $.fn.select2) {
                                                window.$(this.elements.mahalleSelect).trigger('change.select2');
                                            } else {
                                                this.elements.mahalleSelect.dispatchEvent(new Event('change'));
                                            }
                                        }
                                    }, 500);
                                }
                            }, 500);
                        }
                    }
                }
            })
            .catch((error) => {
                console.error('Adres arama hatası:', error);
            });
    }

    /**
     * Olay dinleyicileri ekler
     */
    attachEventListeners() {
        // Ülke değiştiğinde
        if (this.elements.ulkeSelect) {
            this.elements.ulkeSelect.addEventListener('change', () => {
                // Türkiye için statik il listesi zaten mevcut
                // Gelecekte API aracılığıyla ülkeye göre il listesi getirilebilir
            });
        }

        // İl değiştiğinde
        if (this.elements.ilSelect) {
            this.elements.ilSelect.addEventListener('change', () => {
                const ilId = this.elements.ilSelect.value;
                if (ilId) {
                    this.getIlceler(ilId);
                }
            });
        }

        // İlçe değiştiğinde
        if (this.elements.ilceSelect) {
            this.elements.ilceSelect.addEventListener('change', () => {
                const ilceId = this.elements.ilceSelect.value;
                if (ilceId) {
                    this.getMahalleler(ilceId);
                }
            });
        }

        // Mahalle değiştiğinde
        if (this.elements.mahalleSelect) {
            this.elements.mahalleSelect.addEventListener('change', () => {
                const mahalleId = this.elements.mahalleSelect.value;
                if (mahalleId) {
                    // Mahalle seçildiğinde, haritayı mahalle konumuna göre güncelleyebiliriz
                    // Gelecekte buraya mahalle koordinatlarını getirme fonksiyonu eklenebilir
                }
            });
        }

        // Harita arama butonu
        if (this.elements.searchButton) {
            this.elements.searchButton.addEventListener('click', () => {
                const query = prompt(
                    this.options.language === 'tr'
                        ? 'Adres aramak için bir yer adı girin:'
                        : 'Enter a place name to search:'
                );
                if (query) {
                    this.searchAddress(query);
                }
            });
        }

        // Harita sıfırlama butonu
        if (this.elements.resetButton) {
            this.elements.resetButton.addEventListener('click', () => {
                this.resetMap();
            });
        }
    }

    /**
     * Sayfa yüklendiğinde seçili değerleri ayarlar
     */
    setSelectedValues() {
        // Ülke seçili ise
        if (this.elements.ulkeSelect && this.elements.ulkeSelect.value) {
            // Zaten işlenmiş durumda
        }

        // İl seçili ise
        if (this.elements.ilSelect && this.elements.ilSelect.value) {
            this.getIlceler(this.elements.ilSelect.value);
        }

        // İlçe seçili ise
        if (
            this.elements.ilceSelect &&
            !this.elements.ilceSelect.disabled &&
            this.elements.ilceSelect.value
        ) {
            this.getMahalleler(this.elements.ilceSelect.value);
        }
    }
}

// Sayfadaki tüm konum seçicileri için nesne deposu
window.locationHelpers = {};

// DOM yüklendiğinde konum seçicileri başlat
document.addEventListener('DOMContentLoaded', function () {
    // CSRF token ayarla
    let token = document.head.querySelector('meta[name="csrf-token"]');

    if (token) {
        console.log('CSRF token bulundu:', token.content.substring(0, 10) + '...');
        window.__csrfToken = token.content;
    } else {
        console.error('CSRF token bulunamadı! Sayfa yenilenecek...');
        // 2 saniye sonra sayfayı yenile
        setTimeout(function () {
            window.location.reload(true);
        }, 2000);
    }

    // Tüm konum seçicileri bul ve başlat
    document.querySelectorAll('.location-selector').forEach((container) => {
        const uniqueId = container.id;
        if (uniqueId) {
            // Her konum seçici için ayrı nesne oluştur
            window.locationHelpers[uniqueId] = new LocationMapHelper({
                uniqueId: uniqueId,
                initialLat: parseFloat(
                    document.getElementById(`${uniqueId}_latitude`)?.value || 38.4237
                ),
                initialLng: parseFloat(
                    document.getElementById(`${uniqueId}_longitude`)?.value || 27.1428
                ),
                initialZoom: 6,
                language: container.getAttribute('data-lang') || 'tr',
                showMap: document.getElementById(`${uniqueId}_map`) !== null,
            });

            // Başlangıç değerlerini ayarla
            window.locationHelpers[uniqueId].setSelectedValues();
        }
    });

    // Select2 kütüphanesini başlat
    if (typeof $.fn !== 'undefined' && $.fn.select2) {
        document.querySelectorAll('.select2-basic').forEach(function (el) {
            window.$(el).select2({ width: '100%', theme: 'classic', placeholder: 'Seçiniz...', allowClear: true })
        })
    }
});

function triggerChanged(el) {
    if (!el) return
    if (typeof $.fn !== 'undefined' && $.fn.select2) {
        window.$(el).trigger('change.select2')
    } else {
        el.dispatchEvent(new Event('change'))
    }
}
