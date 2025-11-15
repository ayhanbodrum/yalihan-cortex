/**
 * Harita Marker Otomatik Güncelleme Sistemi
 * Bu dosya, il/ilçe/mahalle seçimi sonrasında haritada otomatik marker gösterilmesini sağlar
 */

// Marker güncelleme fonksiyonu
window.updateMapMarker = function (skipEventTrigger = false) {
    console.log('🗺️ Marker güncelleme fonksiyonu çağrıldı');

    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');

    if (!latInput || !lngInput || !latInput.value || !lngInput.value) {
        console.log('⚠️ Koordinatlar bulunamadı, marker eklenemiyor');
        return;
    }

    if (!window.propertyMap) {
        console.log('⚠️ Harita bulunamadı, marker eklenemiyor');
        return;
    }

    const lat = parseFloat(latInput.value);
    const lng = parseFloat(lngInput.value);

    if (isNaN(lat) || isNaN(lng)) {
        console.log('⚠️ Geçersiz koordinatlar:', lat, lng);
        return;
    }

    try {
        // Önceki marker'ı kaldır
        if (window.currentMapMarker) {
            window.propertyMap.removeLayer(window.currentMapMarker);
        }

        // Il, ilce, mahalle bilgilerini al
        const ilSelect = document.getElementById('adres_il');
        const ilceSelect = document.getElementById('adres_ilce');
        const mahalleSelect = document.getElementById('adres_mahalle');

        const il = ilSelect ? ilSelect.selectedOptions[0]?.textContent || ilSelect.value : '';
        const ilce = ilceSelect
            ? ilceSelect.selectedOptions[0]?.textContent || ilceSelect.value
            : '';
        const mahalle = mahalleSelect
            ? mahalleSelect.selectedOptions[0]?.textContent || mahalleSelect.value
            : '';

        // Özel marker ikonu oluştur
        const customIcon = L.divIcon({
            className: 'custom-marker-red',
            html: '<div style="background-color: #dc2626; color: white; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); width: 25px; height: 25px; border: 2px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; font-size: 12px;">📍</div>',
            iconSize: [25, 25],
            iconAnchor: [12, 25],
        });

        // Yeni marker ekle
        const marker = L.marker([lat, lng], {
            draggable: true,
            icon: customIcon,
        }).addTo(window.propertyMap);

        // Popup içeriği oluştur
        let popupContent = '📍 <strong>Seçilen Konum</strong>';
        if (mahalle && mahalle !== 'Mahalle seçiniz...') {
            popupContent += `<br><strong>${mahalle}</strong>`;
        }
        if (ilce && ilce !== 'İlçe seçiniz...') {
            popupContent += `<br>${ilce}`;
        }
        if (il && il !== 'İl seçiniz...') {
            popupContent += `<br>${il}`;
        }
        popupContent += `<br><small>Lat: ${lat.toFixed(6)}<br>Lng: ${lng.toFixed(6)}</small>`;

        marker.bindPopup(popupContent);

        // Global değişkene ata
        window.currentMapMarker = marker;

        // Haritayı bu konuma odakla ve biraz animate ekle
        window.propertyMap.setView([lat, lng], 16, {
            animate: true,
            duration: 1,
        });

        // Marker sürüklendiğinde koordinatları güncelle (infinite loop'u önlemek için flag kullan)
        marker.on('dragend', function (e) {
            if (window.markerDragInProgress) return;
            window.markerDragInProgress = true;

            const position = e.target.getLatLng();
            latInput.value = position.lat.toFixed(6);
            lngInput.value = position.lng.toFixed(6);

            // Alpine.js formData'yı güncelle
            const formComponent = document.querySelector('[x-data*="ilanFormLogic"]');
            if (formComponent && formComponent._x_dataStack) {
                const data = formComponent._x_dataStack[0];
                if (data.formData) {
                    data.formData.latitude = position.lat.toFixed(6);
                    data.formData.longitude = position.lng.toFixed(6);
                }
            }

            console.log('📍 Marker sürüklendi:', position);

            setTimeout(() => {
                window.markerDragInProgress = false;
            }, 100);
        });

        console.log('✅ Marker başarıyla eklendi ve güncellendi!', {
            lat: lat.toFixed(6),
            lng: lng.toFixed(6),
            il: il,
            ilce: ilce,
            mahalle: mahalle,
        });

        // Marker'ın popup'ını göster (dikkat çekmek için)
        if (!skipEventTrigger) {
            setTimeout(() => {
                marker.openPopup();
            }, 500);
        }
    } catch (error) {
        console.error('❌ Marker güncelleme hatası:', error);
    }
};

// Temizle butonu fonksiyonu - Sonsuz döngüyü önler
window.clearMapMarker = function () {
    console.log('🧹 Harita temizleniyor...');

    // Marker'ı kaldır
    if (window.currentMapMarker && window.propertyMap) {
        window.propertyMap.removeLayer(window.currentMapMarker);
        window.currentMapMarker = null;
        console.log('✅ Marker kaldırıldı');
    }

    // Koordinatları temizle (event trigger olmadan)
    const latField = document.getElementById('latitude');
    const lngField = document.getElementById('longitude');

    if (latField && lngField) {
        // Event listener'ları geçici olarak devre dışı bırak
        window.markerUpdateDisabled = true;

        latField.value = '';
        lngField.value = '';

        // Alpine.js formData'yı temizle
        const formComponent = document.querySelector('[x-data*="ilanFormLogic"]');
        if (formComponent && formComponent._x_dataStack) {
            const data = formComponent._x_dataStack[0];
            if (data.formData) {
                data.formData.latitude = '';
                data.formData.longitude = '';
            }
        }

        // Event listener'ları tekrar aktif et
        setTimeout(() => {
            window.markerUpdateDisabled = false;
        }, 500);

        console.log('✅ Koordinatlar temizlendi');
    }

    // Haritayı varsayılan görünüme döndür
    if (window.propertyMap) {
        window.propertyMap.setView([39.9334, 32.8597], 6); // Türkiye genel görünümü
        console.log('✅ Harita varsayılan görünüme döndürüldü');
    }
};

// CSRF helper (aynı origin isteklerde header eklemek için güvenli yardımcı)
function getCsrfToken() {
    try {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : null;
    } catch (e) {
        return null;
    }
}

// Reverse Geocoding - Koordinatlardan adres bulma
window.reverseGeocode = async function (lat, lng) {
    try {
        console.log('🔍 Reverse geocoding başlatılıyor:', lat, lng);

        // Nominatim API kullanarak adres bul
        const csrf = getCsrfToken();
        const response = await fetch(
            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=tr`,
            {
                headers: csrf ? { 'X-CSRF-TOKEN': csrf } : {},
            }
        );
        const data = await response.json();

        if (data && data.address) {
            const address = data.address;
            console.log('📍 Bulunan adres:', address);

            // Türkiye'deki adresleri işle
            const il = address.state || address.province || '';
            const ilce = address.city || address.town || address.suburb || '';
            const mahalle = address.neighbourhood || address.quarter || address.hamlet || '';

            return {
                il: il,
                ilce: ilce,
                mahalle: mahalle,
                fullAddress: data.display_name,
                success: true,
            };
        }

        return { success: false, error: 'Adres bulunamadı' };
    } catch (error) {
        console.error('❌ Reverse geocoding hatası:', error);
        return { success: false, error: error.message };
    }
};

// Harita tıklama event'i ekle
window.addMapClickHandler = function () {
    if (!window.propertyMap) return;

    window.propertyMap.on('click', async function (e) {
        const lat = e.latlng.lat.toFixed(6);
        const lng = e.latlng.lng.toFixed(6);

        console.log('🗺️ Haritaya tıklandı:', lat, lng);

        // Koordinatları güncelle
        const latField = document.getElementById('latitude');
        const lngField = document.getElementById('longitude');

        if (latField && lngField) {
            latField.value = lat;
            lngField.value = lng;

            // Marker'ı güncelle
            window.updateMapMarker(true);

            // Adres bilgisini bul ve dropdown'ları güncelle
            const addressInfo = await window.reverseGeocode(lat, lng);

            if (addressInfo.success) {
                console.log('✅ Adres bulundu:', addressInfo);

                // Dropdown'ları güncelle (eğer eşleşen değerler varsa)
                const ilSelect = document.getElementById('adres_il');
                const ilceSelect = document.getElementById('adres_ilce');
                const mahalleSelect = document.getElementById('adres_mahalle');

                // İl dropdown'ını güncelle
                if (ilSelect && addressInfo.il) {
                    const ilOptions = Array.from(ilSelect.options);
                    const matchingIl = ilOptions.find((opt) =>
                        opt.textContent.toLowerCase().includes(addressInfo.il.toLowerCase())
                    );
                    if (matchingIl) {
                        ilSelect.value = matchingIl.value;
                        ilSelect.dispatchEvent(new Event('change', { bubbles: true }));
                        console.log('📍 İl güncellendi:', matchingIl.textContent);
                    }
                }

                // Bildirim göster
                if (window.showToast) {
                    window.showToast(`📍 Konum seçildi: ${addressInfo.fullAddress}`, 'success');
                } else {
                    console.log('📍 Konum seçildi:', addressInfo.fullAddress);
                }
            }
        }
    });

    console.log('✅ Harita tıklama handler eklendi');
};

// DOM yüklendiğinde çalışacak fonksiyonlar
document.addEventListener('DOMContentLoaded', function () {
    // Koordinat alanlarına değişiklik dinleyicileri ekle
    const latField = document.getElementById('latitude');
    const lngField = document.getElementById('longitude');

    if (latField && lngField) {
        // Debounce fonksiyonu - çok sık tetiklenmesini önler
        let markerUpdateTimeout;

        const debouncedMarkerUpdate = function () {
            if (window.markerUpdateDisabled) return; // Temizle işlemi sırasında tetiklenmeyi önle

            clearTimeout(markerUpdateTimeout);
            markerUpdateTimeout = setTimeout(() => {
                if (!window.markerDragInProgress && !window.markerUpdateDisabled) {
                    window.updateMapMarker(true);
                }
            }, 300);
        };

        latField.addEventListener('change', debouncedMarkerUpdate);
        lngField.addEventListener('change', debouncedMarkerUpdate);
        latField.addEventListener('input', debouncedMarkerUpdate);
        lngField.addEventListener('input', debouncedMarkerUpdate);

        console.log('✅ Koordinat alanları event listeners eklendi');
    }

    // İl, İlçe, Mahalle seçimlerine marker güncelleme ekle
    const ilSelect = document.getElementById('adres_il');
    const ilceSelect = document.getElementById('adres_ilce');
    const mahalleSelect = document.getElementById('adres_mahalle');

    // Debounced location update function
    let locationUpdateTimeout;
    const debouncedLocationUpdate = function (type, value) {
        clearTimeout(locationUpdateTimeout);
        locationUpdateTimeout = setTimeout(() => {
            console.log(`🗺️ ${type} değişti: ${value}`);

            // Koordinatlar varsa marker'ı güncelle
            const lat = document.getElementById('latitude')?.value;
            const lng = document.getElementById('longitude')?.value;

            if (lat && lng && !isNaN(parseFloat(lat)) && !isNaN(parseFloat(lng))) {
                console.log('📍 Koordinatlar mevcut, marker güncelleniyor...');
                window.updateMapMarker();
            } else {
                console.log('⚠️ Henüz koordinatlar yok, marker güncellenemiyor');
            }
        }, 1000); // 1 saniye bekle ki koordinatlar da güncellensin
    };

    if (ilSelect) {
        ilSelect.addEventListener('change', function () {
            debouncedLocationUpdate('İl', this.selectedOptions[0]?.textContent || this.value);
        });
    }

    if (ilceSelect) {
        ilceSelect.addEventListener('change', function () {
            debouncedLocationUpdate('İlçe', this.selectedOptions[0]?.textContent || this.value);
        });
    }

    if (mahalleSelect) {
        mahalleSelect.addEventListener('change', function () {
            debouncedLocationUpdate('Mahalle', this.selectedOptions[0]?.textContent || this.value);
        });
    }

    console.log('✅ İl/İlçe/Mahalle dropdown event listeners eklendi!');

    // Sayfa yüklendiğinde mevcut koordinatlar varsa marker ekle
    setTimeout(() => {
        const lat = document.getElementById('latitude')?.value;
        const lng = document.getElementById('longitude')?.value;
        if (lat && lng && window.propertyMap && window.updateMapMarker) {
            console.log('📍 Sayfa yüklenirken mevcut koordinatlar için marker ekleniyor...');
            window.updateMapMarker();
        }

        // Harita tıklama handler'ını ekle
        if (window.propertyMap && window.addMapClickHandler) {
            window.addMapClickHandler();
        }
    }, 2000);
});

// Alpine.js ile entegrasyon için
document.addEventListener('alpine:init', () => {
    console.log('🏔️ Alpine.js ile marker sistemi entegre edildi');
});
