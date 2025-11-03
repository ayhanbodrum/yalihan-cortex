/**
 * EmlakPro CSRF Token Handler
 * Bu dosya, tüm AJAX istekleri için CSRF token korumasını otomatik olarak ayarlar
 * Sürüm: 1.1 - Tüm sayfalar için geliştirilmiş
 */

$(document).ready(function() {
    // Meta tag'den CSRF token'ı al
    var token = $('meta[name="csrf-token"]').attr('content');
    
    if (token) {
        // Tüm AJAX istekleri için otomatik olarak CSRF token'ı ekle
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': token
            }
        });
        console.log('CSRF token ayarlandı, tüm AJAX istekleri korumalı');
        
        // Form gönderimlerinde de token'i ekle
        $(document).on('submit', 'form', function() {
            // Form içinde csrf-token yoksa ekle
            if ($(this).find('input[name="_token"]').length === 0) {
                $(this).append('<input type="hidden" name="_token" value="' + token + '">');
            }
        });
        
        // XMLHttpRequest nesnesi için interceptor
        var oldXHR = window.XMLHttpRequest;
        function newXHR() {
            var xhr = new oldXHR();
            xhr.addEventListener('readystatechange', function() {
                if (xhr.readyState === 4 && xhr.status === 403) {
                    console.error('CSRF token hatası tespit edildi. Token yenileniyor...');
                    // Yeni token almak için sayfayı yenilemeyi öner
                    if (confirm('Oturum süresi dolmuş olabilir. Sayfayı yenilemek ister misiniz?')) {
                        window.location.reload();
                    }
                }
            }, false);
            return xhr;
        }
        window.XMLHttpRequest = newXHR;
    } else {
        console.error('CSRF token bulunamadı! Sayfa yeniden yüklenmeli');
        // Sayfada csrf-token meta etiketi yoksa, uyarı göster
        if ($('meta[name="csrf-token"]').length === 0) {
            console.warn('Meta etiketi içinde csrf-token bulunamadı. Sayfaya eklenmeli.');
        }
    }
});
