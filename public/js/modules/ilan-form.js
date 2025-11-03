/**
 * İlan Form JavaScript - EmlakPro
 * ---
 * Bu modül, ilan ekleme ve düzenleme sayfalarında kullanılan ortak
 * JavaScript fonksiyonlarını içerir. Kod, camelCase isimlendirme
 * standardına ve EmlakPro projesi global kurallarına uygun olarak yazılmıştır.
 */

(function() {
    'use strict';

    // DOM yüklendiğinde çalışacak kod
    document.addEventListener('DOMContentLoaded', function() {
        setupCsrfToken();
        initSelect2();
        setupLocationSelectors();
        setupFiyatFormatting();
        setupPhotoUpload();
        // Initialize map if container exists
        if (document.getElementById('map-container')) {
            initMap();
        } else {
            console.log('ℹ️ Map container not found, skipping map initialization');
        }
        setupFormValidation();
    });

    /**
     * CSRF token ayarları
     */
    function setupCsrfToken() {
        let token = document.head.querySelector('meta[name="csrf-token"]');
        if (token) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': token.content
                }
            });
        }
    }

    /**
     * Select2 kütüphanesini başlat
     */
    function initSelect2() {
        // Temel select2 konfigürasyonu
        $('.select2-basic').select2({
            theme: 'classic',
            language: 'tr',
            width: '100%',
            placeholder: 'Seçiniz...',
            allowClear: true
        });

        // Danışman seçimi için özel select2
        $('#danisman_id').select2({
            theme: 'classic',
            language: 'tr',
            width: '100%',
            placeholder: 'Danışman Seçin',
            allowClear: true
        });

        // Proje seçimi için özel select2
        $('#proje_id').select2({
            theme: 'classic',
            language: 'tr',
            width: '100%',
            placeholder: 'Proje Seçin',
            allowClear: true
        });
    }

    /**
     * İl, İlçe, Mahalle seçicilerini ayarla
     */
    function setupLocationSelectors() {
        const ilSelect = $('#il_select');
        const ilceSelect = $('#ilce_select');
        const mahalleSelect = $('#mahalle_select');

        // İl değişiminde ilçeleri getir
        ilSelect.on('change', function() {
            const selectedIl = $(this).val();
            ilceSelect.prop('disabled', true).empty().append('<option value="">-- İlçe Seçin --</option>');
            mahalleSelect.prop('disabled', true).empty().append('<option value="">-- Önce ilçe seçin --</option>');
            
            if (selectedIl) {
                $.ajax({
                    url: '/api/location/ilceler',
                    type: 'GET',
                    data: { il: selectedIl },
                    dataType: 'json',
                    success: function(response) {
                        if (response.data && response.data.length > 0) {
                            const selectedIlceId = ilceSelect.data('selected');
                            
                            $.each(response.data, function(index, ilce) {
                                const selected = (selectedIlceId && selectedIlceId == ilce.id) ? 'selected' : '';
                                ilceSelect.append(`<option value="${ilce.ilce_adi}" ${selected}>${ilce.ilce_adi}</option>`);
                            });
                            
                            ilceSelect.prop('disabled', false).trigger('change');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('İlçe verileri alınırken hata oluştu:', error);
                    }
                });
            }
        });

        // İlçe değişiminde mahalleleri getir
        ilceSelect.on('change', function() {
            const selectedIlce = $(this).val();
            const selectedIl = ilSelect.val();
            mahalleSelect.prop('disabled', true).empty().append('<option value="">-- Mahalle Seçin --</option>');
            
            if (selectedIl && selectedIlce) {
                $.ajax({
                    url: '/api/location/mahalleler',
                    type: 'GET',
                    data: { 
                        il: selectedIl,
                        ilce: selectedIlce 
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.data && response.data.length > 0) {
                            const selectedMahalleId = mahalleSelect.data('selected');
                            
                            $.each(response.data, function(index, mahalle) {
                                const selected = (selectedMahalleId && selectedMahalleId == mahalle.id) ? 'selected' : '';
                                mahalleSelect.append(`<option value="${mahalle.mahalle_adi}" ${selected}>${mahalle.mahalle_adi}</option>`);
                            });
                            
                            mahalleSelect.prop('disabled', false);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Mahalle verileri alınırken hata oluştu:', error);
                    }
                });
            }
        });

        // Sayfa yüklendiğinde, eğer il seçili ise ilçeleri yükle
        if (ilSelect.val()) {
            ilSelect.trigger('change');
        }
    }

    /**
     * Fiyat formatlaması
     */
    function setupFiyatFormatting() {
        const fiyatDisplay = document.getElementById('fiyat_display');
        const fiyatInput = document.getElementById('fiyat');
        
        if (fiyatDisplay && fiyatInput) {
            // Ekranda formatlanmış gösterimi
            fiyatDisplay.addEventListener('input', function(e) {
                // Sadece sayıları al
                let value = this.value.replace(/[^\d]/g, '');
                
                // Boş değilse, formatla
                if (value) {
                    // Binlik ayraç ile formatla
                    value = parseInt(value, 10).toLocaleString('tr-TR');
                    this.value = value;
                }
                
                // Gerçek değeri hidden input'a aktar
                fiyatInput.value = this.value.replace(/[^\d]/g, '');
            });
            
            // Başlangıçta formatla
            if (fiyatDisplay.value) {
                const numValue = parseInt(fiyatDisplay.value.replace(/[^\d]/g, ''), 10);
                if (!isNaN(numValue)) {
                    fiyatDisplay.value = numValue.toLocaleString('tr-TR');
                }
            }
        }
    }

    /**
     * Fotoğraf yükleme işlemleri
     */
    function setupPhotoUpload() {
        const fileInput = document.getElementById('fotograflar');
        const dropzone = document.getElementById('dropzone');
        const previewGrid = document.getElementById('preview-grid');
        
        if (fileInput && dropzone) {
            // Sürükle-bırak olayları
            dropzone.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('border-indigo-500');
            });
            
            dropzone.addEventListener('dragleave', function() {
                this.classList.remove('border-indigo-500');
            });
            
            dropzone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('border-indigo-500');
                
                if (e.dataTransfer.files.length) {
                    fileInput.files = e.dataTransfer.files;
                    handleFileSelect(e.dataTransfer.files);
                }
            });
            
            // Dosya seçme olayı
            fileInput.addEventListener('change', function(e) {
                // Önizleme alanını temizle
                if (previewGrid) {
                    previewGrid.innerHTML = '';
                }
                
                handleFileSelect(this.files);
            });
            
            // "Dosya Seç" butonuna tıklama
            dropzone.addEventListener('click', function() {
                fileInput.click();
            });
        }
        
        // Kapak fotoğrafı seçme (düzenleme sayfasında)
        setupCoverPhotoSelection();
    }
    
    /**
     * Dosya seçimi işleme
     */
    function handleFileSelect(files) {
        const previewGrid = document.getElementById('preview-grid');
        
        if (!previewGrid || !files.length) return;
        
        // Dosya boyutu kontrolü
        const maxFileSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            
            // Dosya tipi kontrolü
            if (!allowedTypes.includes(file.type)) {
                alert(`"${file.name}" desteklenmeyen bir dosya formatıdır. Lütfen JPG veya PNG dosyaları yükleyin.`);
                continue;
            }
            
            // Dosya boyutu kontrolü
            if (file.size > maxFileSize) {
                alert(`"${file.name}" dosyası çok büyük (${Math.round(file.size / 1024 / 1024)}MB). Maksimum dosya boyutu 5MB'dir.`);
                continue;
            }
            
            // Dosya önizleme
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const previewItem = document.createElement('div');
                previewItem.className = 'photo-upload__preview-item';
                
                previewItem.innerHTML = `
                    <img src="${e.target.result}" alt="Önizleme">
                    <div class="photo-upload__actions">
                        <button type="button" class="p-1 bg-white rounded-full shadow hover:bg-gray-100" title="Kapak Fotoğrafı Yap">
                            <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                            </svg>
                        </button>
                        <button type="button" class="p-1 bg-white rounded-full shadow hover:bg-red-100" title="Kaldır">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                `;
                
                previewGrid.appendChild(previewItem);
            };
            
            reader.readAsDataURL(file);
        }
    }

    /**
     * Kapak fotoğrafı seçme işlemleri (düzenleme sayfasında)
     */
    function setupCoverPhotoSelection() {
        // Mevcut kapak fotoğrafı radyo butonları
        $(document).on('change', 'input[name="kapak_fotografi"]', function() {
            $('.photo-cover-badge').hide();
            const selectedId = $(this).val();
            $(`#photo-${selectedId} .photo-cover-badge`).show();
        });
        
        // Mevcut fotoğraf silme işlemi
        $(document).on('click', '.photo-delete-btn', function() {
            const photoId = $(this).data('photo-id');
            const photoItem = $(`#photo-${photoId}`);
            
            if (confirm('Bu fotoğrafı silmek istediğinize emin misiniz?')) {
                // Silme işareti ekle
                photoItem.addClass('opacity-50');
                
                // Hidden input ekle
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'sil_fotograflar[]';
                input.value = photoId;
                document.querySelector('form').appendChild(input);
                
                // Eğer kapak fotoğrafı ise, silme işaretini kaldır
                if ($(`#kapak_${photoId}`).is(':checked')) {
                    $(`#kapak_${photoId}`).prop('checked', false);
                    $('.photo-cover-badge').hide();
                }
            }
        });
    }

    /**
     * Harita başlatma
     */
    function initMap() {
        // Map loading göster
        $('#map-loading').removeClass('hidden').addClass('flex');

        // Mevcut koordinatları al
        const latitudeInput = document.getElementById('latitude');
        const longitudeInput = document.getElementById('longitude');
        
        if (!latitudeInput || !longitudeInput) return;
        
        const latitude = parseFloat(latitudeInput.value) || 38.4237;
        const longitude = parseFloat(longitudeInput.value) || 27.1428;
        
        // Harita oluştur
        const map = L.map('map', {
            center: [latitude, longitude],
            zoom: 12,
            scrollWheelZoom: false
        });
        
        // OSM katmanı ekle
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        // Marker ekle
        const marker = L.marker([latitude, longitude], {
            draggable: true
        }).addTo(map);
        
        // Marker sürükleme olayları
        marker.on('dragend', function(e) {
            const position = marker.getLatLng();
            latitudeInput.value = position.lat.toFixed(6);
            longitudeInput.value = position.lng.toFixed(6);
            
            // Haritayı yeni konuma merkezle
            map.panTo(position);
        });
        
        // Map loading gizle
        $('#map-loading').removeClass('flex').addClass('hidden');
        
        // Adres arama butonu
        $('#map-search-btn').on('click', function() {
            searchAddress();
        });
        
        // Enter tuşu ile arama
        $('#map-search-input').on('keypress', function(e) {
            if (e.which === 13) { // Enter tuşu
                e.preventDefault();
                searchAddress();
            }
        });
        
        // Harita sıfırlama butonu
        $('#map-reset-btn').on('click', function() {
            // Türkiye'nin merkezi
            const defaultLat = 38.4237;
            const defaultLng = 27.1428;
            
            // Konum bilgilerini güncelle
            latitudeInput.value = defaultLat;
            longitudeInput.value = defaultLng;
            
            // Marker ve haritayı güncelle
            marker.setLatLng([defaultLat, defaultLng]);
            map.setView([defaultLat, defaultLng], 6);
        });
        
        // Adres arama fonksiyonu
        function searchAddress() {
            const searchValue = $('#map-search-input').val();
            
            if (!searchValue) return;
            
            // Loading göster
            $('#map-loading').removeClass('hidden').addClass('flex');
            
            // Nominatim API ile adres ara
            $.ajax({
                url: `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(searchValue)}`,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data && data.length > 0) {
                        const location = data[0];
                        const lat = parseFloat(location.lat);
                        const lng = parseFloat(location.lon);
                        
                        // Konum bilgilerini güncelle
                        latitudeInput.value = lat.toFixed(6);
                        longitudeInput.value = lng.toFixed(6);
                        
                        // Marker ve haritayı güncelle
                        marker.setLatLng([lat, lng]);
                        map.setView([lat, lng], 14);
                    } else {
                        alert('Aranan adres bulunamadı.');
                    }
                    
                    // Loading gizle
                    $('#map-loading').removeClass('flex').addClass('hidden');
                },
                error: function() {
                    alert('Adres arama sırasında bir hata oluştu.');
                    // Loading gizle
                    $('#map-loading').removeClass('flex').addClass('hidden');
                }
            });
        }
    }

    /**
     * Form doğrulama işlemleri
     */
    function setupFormValidation() {
        $('form').on('submit', function(e) {
            let hasError = false;
            
            // Zorunlu alanların doğrulaması
            const requiredFields = [
                { id: 'baslik', name: 'Başlık' },
                { id: 'fiyat', name: 'Fiyat' },
                { id: 'kategori', name: 'Emlak Kategorisi' },
                { id: 'il_select', name: 'İl' },
                { id: 'ilce_select', name: 'İlçe' },
                { id: 'mahalle_select', name: 'Mahalle' },
                { id: 'aciklama', name: 'Açıklama' }
            ];
            
            // Hata mesajlarını gizle
            $('.validation-error').addClass('hidden');
            
            // Tüm zorunlu alanları kontrol et
            requiredFields.forEach(function(field) {
                const input = document.getElementById(field.id);
                if (!input || !input.value.trim()) {
                    $(`#${field.id}_error`).removeClass('hidden');
                    hasError = true;
                }
            });
            
            // Fiyat kontrolü
            const fiyatInput = document.getElementById('fiyat');
            if (fiyatInput && (!fiyatInput.value || parseInt(fiyatInput.value) <= 0)) {
                $('#fiyat_error').removeClass('hidden');
                hasError = true;
            }
            
            // Hata varsa formu gönderme
            if (hasError) {
                e.preventDefault();
                
                // Sayfayı ilk hataya kaydır
                const firstError = $('.validation-error:not(.hidden)').first();
                if (firstError.length) {
                    $('html, body').animate({
                        scrollTop: firstError.offset().top - 100
                    }, 500);
                }
                
                return false;
            }
            
            return true;
        });
    }
})();
