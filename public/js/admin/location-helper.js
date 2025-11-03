/**
 * EmlakPro - İl-İlçe-Mahalle Seçici JavaScript
 * Bu dosya, il, ilçe ve mahalle seçimi için standart bir arayüz sağlar.
 * Tüm sayfalarda aynı form elemanlarını kullanarak tutarlı bir kullanıcı deneyimi sunar.
 * 
 * Sürüm: 2.0 - Tüm sayfalarda standart seçim için optimize edilmiştir
 * Son Güncelleme: 22 Mayıs 2025
 */

$(document).ready(function() {
    console.log('EmlakPro LocationHelper 2.0 yükleniyor...');
    
    // CSRF token ayarla
    let token = document.head.querySelector('meta[name="csrf-token"]');
    
    if (token) {
        console.log('CSRF token bulundu:', token.content.substring(0, 10) + '...');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': token.content
            }
        });
    } else {
        console.error('CSRF token bulunamadı! Sayfa yenilenecek...');
        // 2 saniye sonra sayfayı yenile
        setTimeout(function() {
            window.location.reload(true);
        }, 2000);
    }
    
    // Select2 kütüphanesini başlat
    initializeSelect2();
    
    // İl değiştiğinde
    $(document).on('change', '#il_select', function() {
        const il = $(this).val();
        const ilceSelect = $('#ilce_select');
        const mahalleSelect = $('#mahalle_select');
        
        // İlçe ve mahalle seçimlerini sıfırla
        resetSelect(ilceSelect);
        resetSelect(mahalleSelect);
        
        if (il) {
            // İlçeleri getir
            getIlceler(il, ilceSelect);
        }
    });
    
    // İlçe değiştiğinde
    $(document).on('change', '#ilce_select', function() {
        const il = $('#il_select').val();
        const ilce = $(this).val();
        const mahalleSelect = $('#mahalle_select');
        
        // Mahalle seçimini sıfırla
        resetSelect(mahalleSelect);
        
        if (il && ilce) {
            // Mahalleleri getir
            getMahalleler(il, ilce, mahalleSelect);
        }
    });
    
    // Form gönderilmeden önce validasyon
    $(document).on('submit', 'form', function() {
        // Gerekli alanların kontrol edilmesi
        const ilSelect = $('#il_select');
        const ilceSelect = $('#ilce_select');
        const mahalleSelect = $('#mahalle_select');
        
        let isValid = true;
        
        // İl kontrolü
        if (ilSelect.length > 0 && ilSelect.prop('required') && !ilSelect.val()) {
            $('#il_error').removeClass('hidden');
            isValid = false;
        } else {
            $('#il_error').addClass('hidden');
        }
        
        // İlçe kontrolü
        if (ilceSelect.length > 0 && ilceSelect.prop('required') && !ilceSelect.val()) {
            $('#ilce_error').removeClass('hidden');
            isValid = false;
        } else {
            $('#ilce_error').addClass('hidden');
        }
        
        // Mahalle kontrolü
        if (mahalleSelect.length > 0 && mahalleSelect.prop('required') && !mahalleSelect.val()) {
            $('#mahalle_error').removeClass('hidden');
            isValid = false;
        } else {
            $('#mahalle_error').addClass('hidden');
        }
        
        return isValid;
    });
    
    // Sayfa yüklendiğinde seçili değerleri ayarla
    setSelectedValues();
});

/**
 * Select2 inicializasyonu
 */
function initializeSelect2() {
    // Select2 kütüphanesi yüklü mü kontrol et
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2-basic').select2({
            placeholder: 'Seçiniz...',
            allowClear: true,
            language: {
                noResults: function() {
                    return "Sonuç bulunamadı";
                },
                searching: function() {
                    return "Aranıyor...";
                }
            }
        });
        console.log('Select2 inicializasyonu tamamlandı');
    } else {
        console.warn('Select2 kütüphanesi yüklü değil!');
    }
}

/**
 * Select'i sıfırlama
 * @param {jQuery} select - Sıfırlanacak select elementi
 */
function resetSelect(select) {
    select.empty().append('<option value="">-- Seçiniz --</option>');
    select.prop('disabled', true);
    
    // Select2 varsa güncelle
    if (typeof $.fn.select2 !== 'undefined') {
        select.trigger('change.select2');
    } else {
        select.trigger('change');
    }
}

/**
 * İlçeleri getirme
 * @param {string} il - İl adı
 * @param {jQuery} ilceSelect - İlçe select elementi
 */
function getIlceler(il, ilceSelect) {
    $.ajax({
        url: route('admin.get-ilceler'),
        type: 'GET',
        data: { il: il },
        dataType: 'json',
        beforeSend: function() {
            ilceSelect.prop('disabled', true);
            console.log(`İl için ilçeler getiriliyor: ${il}`);
        },
        success: function(data) {
            ilceSelect.empty().append('<option value="">-- İlçe Seçin --</option>');
            
            if (data && data.length > 0) {
                $.each(data, function(index, ilce) {
                    ilceSelect.append(new Option(ilce.ilce_adi, ilce.ilce_adi));
                });
                ilceSelect.prop('disabled', false);
                
                // Eğer data-selected değeri varsa seç
                const selectedIlce = ilceSelect.data('selected');
                if (selectedIlce) {
                    ilceSelect.val(selectedIlce);
                    
                    // Select2 varsa güncelle
                    if (typeof $.fn.select2 !== 'undefined') {
                        ilceSelect.trigger('change.select2');
                    } else {
                        ilceSelect.trigger('change');
                    }
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('İlçe getirme hatası:', error);
            console.log(xhr.responseText);
        }
    });
}

/**
 * Mahalleleri getirme
 * @param {string} il - İl adı
 * @param {string} ilce - İlçe adı
 * @param {jQuery} mahalleSelect - Mahalle select elementi
 */
function getMahalleler(il, ilce, mahalleSelect) {
    $.ajax({
        url: route('admin.get-mahalleler'),
        type: 'GET',
        data: { il: il, ilce: ilce },
        dataType: 'json',
        beforeSend: function() {
            mahalleSelect.prop('disabled', true);
            console.log(`İl: ${il}, İlçe: ${ilce} için mahalleler getiriliyor`);
        },
        success: function(data) {
            mahalleSelect.empty().append('<option value="">-- Mahalle Seçin --</option>');
            
            if (data && data.length > 0) {
                $.each(data, function(index, mahalle) {
                    mahalleSelect.append(new Option(mahalle.mahalle_adi, mahalle.mahalle_adi));
                });
                mahalleSelect.prop('disabled', false);
                
                // Eğer data-selected değeri varsa seç
                const selectedMahalle = mahalleSelect.data('selected');
                if (selectedMahalle) {
                    mahalleSelect.val(selectedMahalle);
                    
                    // Select2 varsa güncelle
                    if (typeof $.fn.select2 !== 'undefined') {
                        mahalleSelect.trigger('change.select2');
                    } else {
                        mahalleSelect.trigger('change');
                    }
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Mahalle getirme hatası:', error);
            console.log(xhr.responseText);
        }
    });
}

/**
 * Sayfa yüklendiğinde seçili değerleri ayarlama
 */
function setSelectedValues() {
    const ilSelect = $('#il_select');
    const selectedIl = ilSelect.data('selected');
    
    if (selectedIl) {
        ilSelect.val(selectedIl);
        
        // Select2 varsa güncelle
        if (typeof $.fn.select2 !== 'undefined') {
            ilSelect.trigger('change.select2');
        } else {
            ilSelect.trigger('change');
        }
    }
}
