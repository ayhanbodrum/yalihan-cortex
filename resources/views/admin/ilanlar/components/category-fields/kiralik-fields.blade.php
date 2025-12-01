{{-- Kiralık Kategorisi Özel Alanlar --}}
<div x-show="selectedKategoriSlug && (selectedKategoriSlug.includes('kiralik') || selectedKategoriSlug.includes('rental') || selectedKategoriSlug.includes('yazlik'))"
    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95"
    x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95"
    class="space-y-4 mb-4">

    {{-- Kiralık Kategorisi Bilgilendirme --}}
    <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span class="font-semibold text-green-900 dark:text-green-100">Kiralık Kategorisi Seçildi</span>
        </div>
        <p class="text-sm text-green-700 dark:text-green-300">
            Kiralık kategorisine özel alanlar (gunluk_fiyat, min_konaklama, havuz, sezon_baslangic, vb.) aktif edildi.
        </p>
    </div>

    {{-- Yazlık Otomatik Fiyatlandırma (Sadece Yazlık kategorisi için) --}}
    <div x-show="selectedKategoriSlug && selectedKategoriSlug.includes('yazlik')"
        x-data="{
            calculating: false,
            calculatePrices() {
                // Günlük fiyat input'unu bul
                const gunlukFiyatInput = document.getElementById('field_gunluk_fiyat') ||
                    document.getElementById('gunluk_fiyat') ||
                    document.querySelector('[name=\"gunluk_fiyat\"]'); if (!gunlukFiyatInput || !gunlukFiyatInput.value ||
        parseFloat(gunlukFiyatInput.value) <=0) { alert('Lütfen önce günlük fiyatı giriniz.'); return; }
        this.calculating=true; const gunlukFiyat=parseFloat(gunlukFiyatInput.value);
        fetch('/api/ai/calculate-seasonal-price', { method: 'POST' , headers: { 'Content-Type' : 'application/json'
        , 'X-CSRF-TOKEN' : document.querySelector('meta[name=\"csrf-token\"]')?.content || '' , 'X-Requested-With'
        : 'XMLHttpRequest' , 'Accept' : 'application/json' }, body: JSON.stringify({ gunluk_fiyat: gunlukFiyat }) })
        .then(response=> response.json())
        .then(data => {
        this.calculating = false;
        if (data.success && data.data) {
        // Haftalık fiyat
        const haftalikInput = document.getElementById('field_haftalik_fiyat') ||
        document.getElementById('haftalik_fiyat') ||
        document.querySelector('[name=\"haftalik_fiyat\"]');
        if (haftalikInput) {
        haftalikInput.value = data.data.haftalik_fiyat;
        this.flashInput(haftalikInput);
        }

        // Aylık fiyat
        const aylikInput = document.getElementById('field_aylik_fiyat') ||
        document.getElementById('aylik_fiyat') ||
        document.querySelector('[name=\"aylik_fiyat\"]');
        if (aylikInput) {
        aylikInput.value = data.data.aylik_fiyat;
        this.flashInput(aylikInput);
        }

        // Sezonluk fiyatlar (opsiyonel - eğer field'lar varsa)
        if (data.data.sezonluk_fiyatlar) {
        const sezonFiyatlar = data.data.sezonluk_fiyatlar;

        // Yaz sezonu
        const yazGunlukInput = document.querySelector('[name*=\"yaz_gunluk\"]') ||
        document.querySelector('[name*=\"yaz_sezonu_gunluk\"]');
        if (yazGunlukInput) {
        yazGunlukInput.value = sezonFiyatlar.yaz.gunluk;
        this.flashInput(yazGunlukInput);
        }

        // Kış sezonu
        const kisGunlukInput = document.querySelector('[name*=\"kis_gunluk\"]') ||
        document.querySelector('[name*=\"kis_sezonu_gunluk\"]');
        if (kisGunlukInput) {
        kisGunlukInput.value = sezonFiyatlar.kis.gunluk;
        this.flashInput(kisGunlukInput);
        }

        // Ara sezon
        const araSezonGunlukInput = document.querySelector('[name*=\"ara_sezon_gunluk\"]');
        if (araSezonGunlukInput) {
        araSezonGunlukInput.value = sezonFiyatlar.ara_sezon.gunluk;
        this.flashInput(araSezonGunlukInput);
        }
        }

        // Başarı mesajı (Toast)
        this.showToast('Fiyatlandırma hesaplandı!', 'success');
        } else {
        this.showToast(data.message || 'Fiyatlandırma hesaplanamadı.', 'error');
        }
        })
        .catch(error => {
        this.calculating = false;
        console.error('Yazlık Fiyatlandırma Hatası:', error);
        this.showToast('Fiyatlandırma hesaplanırken bir hata oluştu.', 'error');
        });
        },
        flashInput(input) {
        // Flash effect: Yeşil arka plan animasyonu
        input.classList.add('bg-green-100', 'dark:bg-green-900/30');
        setTimeout(() => {
        input.classList.remove('bg-green-100', 'dark:bg-green-900/30');
        }, 1000);
        },
        showToast(message, type) {
        // Basit toast mesajı (Context7 toast sistemi varsa onu kullan)
        if (window.showToast && typeof window.showToast === 'function') {
        window.showToast(message, type);
        } else {
        // Fallback: Alert
        alert(message);
        }
        }
        }"
        x-init="// Günlük fiyat input'una buton ekle
        $watch('selectedKategoriSlug', (value) => {
                    if (value && value.includes('yazlik')) {
                        setTimeout(() => {
                                    const gunlukFiyatInput = document.getElementById('field_gunluk_fiyat') ||
                                        document.getElementById('gunluk_fiyat') ||
                                        document.querySelector('[name=\"gunluk_fiyat\"]');
        if (gunlukFiyatInput && !gunlukFiyatInput.parentElement.querySelector('.auto-calculate-btn')) {
        const wrapper = document.createElement('div');
        wrapper.className = 'relative';
        gunlukFiyatInput.parentNode.insertBefore(wrapper, gunlukFiyatInput);
        wrapper.appendChild(gunlukFiyatInput);

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1.5 text-xs font-medium bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 flex items-center gap-1.5';
        btn.innerHTML = '<span>⚡</span><span>Otomatik Hesapla</span>';
        btn.onclick = () => { Alpine.store('yazlikPricing')?.calculatePrices?.() || calculatePrices(); };
        wrapper.appendChild(btn);
        }
        }, 500);
        }
        });
        "
        class="p-6 bg-indigo-50 dark:bg-indigo-900/20 border-2 border-indigo-200 dark:border-indigo-800 rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
                <div
                    class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-indigo-900 dark:text-indigo-100 mb-1 flex items-center gap-2">
                    ⚡ Yazlık Otomatik Fiyatlandırma
                </h3>
                <p class="text-sm text-indigo-700 dark:text-indigo-300 mb-4">
                    Günlük fiyatı girdikten sonra "⚡ Otomatik Hesapla" butonuna tıklayın. Sistem haftalık, aylık ve
                    sezonluk fiyatları otomatik hesaplayacaktır.
                </p>
                <div class="text-xs text-indigo-600 dark:text-indigo-400 space-y-1">
                    <p>• Haftalık: %5 indirimli</p>
                    <p>• Aylık: %15 indirimli</p>
                    <p>• Kış Sezonu: %50 indirimli</p>
                    <p>• Ara Sezon: %30 indirimli</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Global function for button click (Alpine.js dışından erişim için)
    window.calculateYazlikPrices = function() {
        const gunlukFiyatInput = document.getElementById('field_gunluk_fiyat') ||
            document.getElementById('gunluk_fiyat') ||
            document.querySelector('[name="gunluk_fiyat"]');

        if (!gunlukFiyatInput || !gunlukFiyatInput.value || parseFloat(gunlukFiyatInput.value) <= 0) {
            alert('Lütfen önce günlük fiyatı giriniz.');
            return;
        }

        const gunlukFiyat = parseFloat(gunlukFiyatInput.value);

        fetch('/api/ai/calculate-seasonal-price', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    gunluk_fiyat: gunlukFiyat
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    // Haftalık fiyat
                    const haftalikInput = document.getElementById('field_haftalik_fiyat') ||
                        document.getElementById('haftalik_fiyat') ||
                        document.querySelector('[name="haftalik_fiyat"]');
                    if (haftalikInput) {
                        haftalikInput.value = data.data.haftalik_fiyat;
                        haftalikInput.classList.add('bg-green-100', 'dark:bg-green-900/30');
                        setTimeout(() => {
                            haftalikInput.classList.remove('bg-green-100', 'dark:bg-green-900/30');
                        }, 1000);
                    }

                    // Aylık fiyat
                    const aylikInput = document.getElementById('field_aylik_fiyat') ||
                        document.getElementById('aylik_fiyat') ||
                        document.querySelector('[name="aylik_fiyat"]');
                    if (aylikInput) {
                        aylikInput.value = data.data.aylik_fiyat;
                        aylikInput.classList.add('bg-green-100', 'dark:bg-green-900/30');
                        setTimeout(() => {
                            aylikInput.classList.remove('bg-green-100', 'dark:bg-green-900/30');
                        }, 1000);
                    }

                    // Toast mesajı
                    if (window.showToast && typeof window.showToast === 'function') {
                        window.showToast('Fiyatlandırma hesaplandı!', 'success');
                    } else {
                        console.log('✅ Fiyatlandırma hesaplandı!');
                    }
                } else {
                    alert(data.message || 'Fiyatlandırma hesaplanamadı.');
                }
            })
            .catch(error => {
                console.error('Yazlık Fiyatlandırma Hatası:', error);
                alert('Fiyatlandırma hesaplanırken bir hata oluştu.');
            });
    };
</script>
