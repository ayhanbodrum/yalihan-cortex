/**
 * İl-İlçe-Mahalle Seçim Sistemi
 *
 * - İl seçilince ilçeler yüklenir ve mahalle sıfırlanır
 * - İlçe seçilince mahalleler yüklenir
 * - Select2 ile gelişmiş arama yapılabilir
 */
$(document).ready(function() {
    // Select elementlerinin referanslarını al
    const ilSelect = $('#il');
    const ilceSelect = $('#ilce');
    const mahalleSelect = $('#mahalle');

    // Hata gösterge divleri
    const ilError = $('#il_error');
    const ilceError = $('#ilce_error');
    const mahalleError = $('#mahalle_error');

    // Seçili değerleri sakla (düzenle sayfası için)
    const selectedIl = ilSelect.data('selected') || ilSelect.val();
    const selectedIlce = ilceSelect.data('selected') || '';
    const selectedMahalle = mahalleSelect.data('selected') || '';

    // Select2 uygulaması
    initializeSelect2();

    // İl değişince ilçeleri yükle
    ilSelect.on('change', function() {
        const ilId = $(this).val();
        ilError.addClass('hidden');
        resetIlce();
        resetMahalle();

        if (ilId) {
            loadIlceler(ilId);
        }
    });

    // İlçe değişince mahalleleri yükle
    ilceSelect.on('change', function() {
        const ilceId = $(this).val();
        ilceError.addClass('hidden');
        resetMahalle();

        if (ilceId) {
            loadMahalleler(ilceId);
        }
    });

    // Mahalle seçildiğinde hata mesajını gizle
    mahalleSelect.on('change', function() {
        if ($(this).val()) {
            mahalleError.addClass('hidden');
        }
    });

    // Form gönderildiğinde validasyon
    $('form').on('submit', function(e) {
        const il = ilSelect.val();
        const ilce = ilceSelect.val();
        const mahalle = mahalleSelect.val();
        let hasError = false;

        // Hata mesajlarını temizle
        ilError.addClass('hidden');
        ilceError.addClass('hidden');
        mahalleError.addClass('hidden');

        // Validasyonları kontrol et
        if (!il) {
            ilError.removeClass('hidden');
            hasError = true;
        }

        if (!ilce) {
            ilceError.removeClass('hidden');
            hasError = true;
        }

        if (!mahalle) {
            mahalleError.removeClass('hidden');
            hasError = true;
        }

        if (hasError) {
            e.preventDefault();
            // İlk hataya scroll yap
            $('html, body').animate({
                scrollTop: $('#il').offset().top - 100
            }, 300);
            return false;
        }

        return true;
    });

    // Düzenleme sayfası için mevcut değerleri yükle
    if (selectedIl) {
        ilSelect.val(selectedIl).trigger('change');

        // İlçe ve mahalleleri yükle
        if (selectedIlce) {
            setTimeout(function() {
                loadIlcelerWithSelected(selectedIl, selectedIlce, selectedMahalle);
            }, 300);
        }
    }

    // Select2 initialize fonksiyonu
    function initializeSelect2() {
        ilSelect.select2({
            placeholder: '-- İl Seçin --',
            allowClear: true,
            width: '100%',
            language: {
                noResults: function() { return "Sonuç bulunamadı"; },
                searching: function() { return "Aranıyor..."; },
                inputTooShort: function() { return "Lütfen en az 2 karakter girin"; }
            },
            theme: 'classic'
        });

        ilceSelect.select2({
            placeholder: '-- Önce İl Seçin --',
            allowClear: true,
            width: '100%',
            language: {
                noResults: function() { return "Sonuç bulunamadı"; },
                searching: function() { return "Aranıyor..."; }
            },
            theme: 'classic'
        });

        mahalleSelect.select2({
            placeholder: '-- Önce İlçe Seçin --',
            allowClear: true,
            width: '100%',
            language: {
                noResults: function() { return "Sonuç bulunamadı"; },
                searching: function() { return "Aranıyor..."; }
            },
            theme: 'classic'
        });
    }

    // İlçeleri yükle
    function loadIlceler(ilId) {
        ilceSelect.prop('disabled', true).html('<option value="">Yükleniyor...</option>').trigger('change.select2');

        $.ajax({
            url: '/api/ilceler',
            type: 'GET',
            data: { il_id: ilId },
            dataType: 'json',
            success: function(data) {
                ilceSelect.empty().append('<option value="">-- İlçe Seçin --</option>');

                if (Array.isArray(data) && data.length > 0) {
                    $.each(data, function(i, ilce) {
                        ilceSelect.append(new Option(ilce.ad, ilce.id));
                    });
                    ilceSelect.prop('disabled', false);
                } else {
                    ilceSelect.append(new Option('Bu ile ait ilçe bulunamadı', ''));
                }

                ilceSelect.trigger('change.select2');
            },
            error: function(xhr) {
                console.error('İlçe yükleme hatası:', xhr);
                ilceSelect.empty().append('<option value="">Hata oluştu!</option>');
                ilceSelect.prop('disabled', true).trigger('change.select2');
            }
        });
    }

    // Mahalleleri yükle
    function loadMahalleler(ilceId) {
        mahalleSelect.prop('disabled', true).html('<option value="">Yükleniyor...</option>').trigger('change.select2');

        $.ajax({
            url: '/api/mahalleler',
            type: 'GET',
            data: { ilce_id: ilceId },
            dataType: 'json',
            success: function(data) {
                mahalleSelect.empty().append('<option value="">-- Mahalle Seçin --</option>');

                if (Array.isArray(data) && data.length > 0) {
                    $.each(data, function(i, mahalle) {
                        mahalleSelect.append(new Option(mahalle.ad, mahalle.id));
                    });
                    mahalleSelect.prop('disabled', false);
                } else {
                    mahalleSelect.append(new Option('Bu ilçeye ait mahalle bulunamadı', ''));
                }

                mahalleSelect.trigger('change.select2');
            },
            error: function(xhr) {
                console.error('Mahalle yükleme hatası:', xhr);
                mahalleSelect.empty().append('<option value="">Hata oluştu!</option>');
                mahalleSelect.prop('disabled', true).trigger('change.select2');
            }
        });
    }

    // İlçeleri yükle ve seçili getir (düzenleme sayfası için)
    function loadIlcelerWithSelected(ilId, selectedIlceId, selectedMahalleId) {
        $.ajax({
            url: '/api/ilceler',
            type: 'GET',
            data: { il_id: ilId },
            dataType: 'json',
            success: function(data) {
                ilceSelect.empty().append('<option value="">-- İlçe Seçin --</option>');

                if (Array.isArray(data) && data.length > 0) {
                    $.each(data, function(i, ilce) {
                        ilceSelect.append(new Option(ilce.ad, ilce.id));
                    });

                    ilceSelect.prop('disabled', false);

                    // Seçili ilçeyi ayarla
                    if (selectedIlceId) {
                        ilceSelect.val(selectedIlceId).trigger('change');

                        // Mahalleleri seçili ile yükle
                        if (selectedMahalleId) {
                            setTimeout(function() {
                                loadMahallelerWithSelected(selectedIlceId, selectedMahalleId);
                            }, 300);
                        }
                    }
                }

                ilceSelect.trigger('change.select2');
            },
            error: function(xhr) {
                console.error('İlçe yükleme hatası:', xhr);
            }
        });
    }

    // Mahalleleri yükle ve seçili getir (düzenleme sayfası için)
    function loadMahallelerWithSelected(ilceId, selectedMahalleId) {
        $.ajax({
            url: '/api/mahalleler',
            type: 'GET',
            data: { ilce_id: ilceId },
            dataType: 'json',
            success: function(data) {
                mahalleSelect.empty().append('<option value="">-- Mahalle Seçin --</option>');

                if (Array.isArray(data) && data.length > 0) {
                    $.each(data, function(i, mahalle) {
                        mahalleSelect.append(new Option(mahalle.ad, mahalle.id));
                    });

                    mahalleSelect.prop('disabled', false);

                    // Seçili mahalleyi ayarla
                    if (selectedMahalleId) {
                        mahalleSelect.val(selectedMahalleId).trigger('change');
                    }
                }

                mahalleSelect.trigger('change.select2');
            },
            error: function(xhr) {
                console.error('Mahalle yükleme hatası:', xhr);
            }
        });
    }

    // İlçe seçimini sıfırla
    function resetIlce() {
        ilceSelect.empty().append('<option value="">-- Önce İl Seçin --</option>');
        ilceSelect.prop('disabled', true).trigger('change.select2');
    }

    // Mahalle seçimini sıfırla
    function resetMahalle() {
        mahalleSelect.empty().append('<option value="">-- Önce İlçe Seçin --</option>');
        mahalleSelect.prop('disabled', true).trigger('change.select2');
    }
});
