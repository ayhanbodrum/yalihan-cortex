/**
 * EmlakPro - İl-İlçe-Mahalle Seçici JavaScript
 * Bu dosya, il, ilçe ve mahalle seçimi için standart bir arayüz sağlar.
 * Tüm sayfalarda aynı form elemanlarını kullanarak tutarlı bir kullanıcı deneyimi sunar.
 *
 * Sürüm: 2.0 - Tüm sayfalarda standart seçim için optimize edilmiştir
 * Son Güncelleme: 22 Mayıs 2025
 */

document.addEventListener('DOMContentLoaded', function () {
    console.log('EmlakPro LocationHelper 2.0 yükleniyor...');

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

    // Select2 kütüphanesini başlat
    initializeSelect2();

    // İl değiştiğinde
    const ilSelectEl = document.getElementById('il_select');
    const ilceSelectEl = document.getElementById('ilce_select');
    const mahalleSelectEl = document.getElementById('mahalle_select');

    if (ilSelectEl) {
        ilSelectEl.addEventListener('change', function () {
            const il = ilSelectEl.value;
            const ilceSelect = ilceSelectEl;
            const mahalleSelect = mahalleSelectEl;

            // İlçe ve mahalle seçimlerini sıfırla
            resetSelect(ilceSelect);
            resetSelect(mahalleSelect);

            if (il) {
                // İlçeleri getir
                getIlceler(il, ilceSelect);
            }
        });
    }

    // İlçe değiştiğinde
    if (ilceSelectEl) {
        ilceSelectEl.addEventListener('change', function () {
            const il = ilSelectEl ? ilSelectEl.value : '';
            const ilce = ilceSelectEl.value;
            const mahalleSelect = mahalleSelectEl;

            // Mahalle seçimini sıfırla
            resetSelect(mahalleSelect);

            if (il && ilce) {
                // Mahalleleri getir
                getMahalleler(il, ilce, mahalleSelect);
            }
        });
    }

    // Form gönderilmeden önce validasyon
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!(form instanceof HTMLFormElement)) return;
        // Gerekli alanların kontrol edilmesi
        const ilSelect = document.getElementById('il_select');
        const ilceSelect = document.getElementById('ilce_select');
        const mahalleSelect = document.getElementById('mahalle_select');

        let isValid = true;

        // İl kontrolü
        if (ilSelect && ilSelect.required && !ilSelect.value) {
            const err = document.getElementById('il_error');
            if (err) err.classList.remove('hidden');
            isValid = false;
        } else {
            const err = document.getElementById('il_error');
            if (err) err.classList.add('hidden');
        }

        // İlçe kontrolü
        if (ilceSelect && ilceSelect.required && !ilceSelect.value) {
            const err = document.getElementById('ilce_error');
            if (err) err.classList.remove('hidden');
            isValid = false;
        } else {
            const err = document.getElementById('ilce_error');
            if (err) err.classList.add('hidden');
        }

        // Mahalle kontrolü
        if (mahalleSelect && mahalleSelect.required && !mahalleSelect.value) {
            const err = document.getElementById('mahalle_error');
            if (err) err.classList.remove('hidden');
            isValid = false;
        } else {
            const err = document.getElementById('mahalle_error');
            if (err) err.classList.add('hidden');
        }

        if (!isValid) {
            e.preventDefault();
        }
    });

    // Sayfa yüklendiğinde seçili değerleri ayarla
    setSelectedValues();
});

/**
 * Select2 inicializasyonu
 */
function initializeSelect2() {
    // Select2 kütüphanesi yüklü mü kontrol et
    if (typeof $.fn !== 'undefined' && $.fn.select2) {
        var els = document.querySelectorAll('.select2-basic')
        els.forEach(function (el) { window.$(el).select2({
            placeholder: 'Seçiniz...',
            allowClear: true,
            language: {
                noResults: function () {
                    return 'Sonuç bulunamadı';
                },
                searching: function () {
                    return 'Aranıyor...';
                },
            },
        }) })
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
    if (!select) return;
    select.innerHTML = '<option value="">-- Seçiniz --</option>';
    select.disabled = true;

    // Select2 varsa güncelle
    triggerChanged(select)
}

/**
 * İlçeleri getirme
 * @param {string} il - İl adı
 * @param {jQuery} ilceSelect - İlçe select elementi
 */
function getIlceler(il, ilceSelect) {
    if (!ilceSelect) return;
    ilceSelect.disabled = true;
    console.log(`İl için ilçeler getiriliyor: ${il}`);
    const url = route('admin.get-ilceler') + `?il=${encodeURIComponent(il)}`;
    const headers = {};
    if (window.__csrfToken) headers['X-CSRF-TOKEN'] = window.__csrfToken;
    fetch(url, { method: 'GET', headers })
        .then((res) => res.json())
        .then((data) => {
            ilceSelect.innerHTML = '<option value="">-- İlçe Seçin --</option>';
            if (data && data.length > 0) {
                for (const ilce of data) {
                    ilceSelect.append(new Option(ilce.ilce_adi, ilce.ilce_adi));
                }
                ilceSelect.disabled = false;

                const selectedIlce = ilceSelect.dataset.selected;
                if (selectedIlce) {
                    ilceSelect.value = selectedIlce;
                    triggerChanged(ilceSelect)
                }
            }
        })
        .catch((err) => {
            console.error('İlçe getirme hatası:', err);
        });
}

/**
 * Mahalleleri getirme
 * @param {string} il - İl adı
 * @param {string} ilce - İlçe adı
 * @param {jQuery} mahalleSelect - Mahalle select elementi
 */
function getMahalleler(il, ilce, mahalleSelect) {
    if (!mahalleSelect) return;
    mahalleSelect.disabled = true;
    console.log(`İl: ${il}, İlçe: ${ilce} için mahalleler getiriliyor`);
    const params = new URLSearchParams({ il, ilce });
    const url = route('admin.get-mahalleler') + `?${params.toString()}`;
    const headers = {};
    if (window.__csrfToken) headers['X-CSRF-TOKEN'] = window.__csrfToken;
    fetch(url, { method: 'GET', headers })
        .then((res) => res.json())
        .then((data) => {
            mahalleSelect.innerHTML = '<option value="">-- Mahalle Seçin --</option>';
            if (data && data.length > 0) {
                for (const mahalle of data) {
                    mahalleSelect.append(new Option(mahalle.mahalle_adi, mahalle.mahalle_adi));
                }
                mahalleSelect.disabled = false;

                const selectedMahalle = mahalleSelect.dataset.selected;
                if (selectedMahalle) {
                    mahalleSelect.value = selectedMahalle;
                    triggerChanged(mahalleSelect)
                }
            }
        })
        .catch((err) => {
            console.error('Mahalle getirme hatası:', err);
        });
}

/**
 * Sayfa yüklendiğinde seçili değerleri ayarlama
 */
function setSelectedValues() {
    const ilSelect = document.getElementById('il_select');
    if (!ilSelect) return;
    const selectedIl = ilSelect.dataset.selected;
    if (selectedIl) {
        ilSelect.value = selectedIl;
        triggerChanged(ilSelect)
    }
}

function triggerChanged(el) {
    if (!el) return
    if (typeof $.fn !== 'undefined' && $.fn.select2) {
        window.$(el).trigger('change.select2')
    } else {
        el.dispatchEvent(new Event('change'))
    }
}
