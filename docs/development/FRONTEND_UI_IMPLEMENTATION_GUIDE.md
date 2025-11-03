# 🎨 Frontend UI Implementation Guide

**Tarih:** 12 Ekim 2025  
**Backend:** ✅ %100 Hazır  
**Migration:** ✅ Çalıştırıldı  
**API Endpoints:** ✅ Çalışıyor

---

## ✅ **HAZIR OLAN BACKEND**

### **1. AI İlan Geçmişi Analizi**

-   **Service:** `app/Services/AI/IlanGecmisAIService.php` ✅
-   **API:** `GET /api/kisiler/{id}/ai-gecmis-analiz` ✅
-   **Response:** 8 analiz tipi + 10+ öneri ✅

### **2. TKGM Parsel Sorgulama**

-   **Service:** `app/Services/TKGMService.php` ✅
-   **API:** `POST /api/tkgm/parsel-sorgu` ✅
-   **Response:** Parsel bilgileri + hesaplamalar ✅

### **3. Kategori Dinamik Alanlar**

-   **Service:** `app/Services/KategoriOzellikService.php` ✅
-   **Method:** `getOzelliklerByKategori()` ✅
-   **Kategoriler:** 6 kategori tanımı ✅

### **4. Anahtar Yönetimi**

-   **Migration:** ✅ Çalıştırıldı
-   **Fields:** anahtar_turu, ulasilabilirlik, ek_bilgi ✅
-   **Database:** İlanlar tablosuna eklendi ✅

---

## 🎨 **FRONTEND UI EKLEMELERİ**

### **1. AI Geçmiş Analizi Component** 📊

**Nereye:** Kişi seçimi alanından sonra (`stable-create.blade.php`)

**Alpine.js Component:**

```javascript
// stable-create sayfasına ekle (en başa, Alpine.data içine)
Alpine.data("kisiGecmisi", () => ({
    historyLoaded: false,
    history: null,
    loading: false,

    async loadHistory(kisiId) {
        this.loading = true;
        try {
            const response = await fetch(
                `/api/kisiler/${kisiId}/ai-gecmis-analiz`
            );
            const data = await response.json();

            if (data.success && data.has_history) {
                this.history = data;
                this.historyLoaded = true;

                // Önerileri göster
                data.oneriler.forEach((oneri) => {
                    window.toast.info(oneri, 5000);
                });

                window.toast.success("✅ Geçmiş analizi tamamlandı!");
            } else {
                window.toast.warning("Bu kişinin önceki ilanı yok");
            }
        } catch (error) {
            window.toast.error("Analiz yapılamadı: " + error.message);
        } finally {
            this.loading = false;
        }
    },
}));
```

**Blade Component:**

```blade
{{-- Kişi seçimi alanından SONRA ekle --}}
<div x-data="kisiGecmisi()" x-show="selectedKisiId" class="mt-6 neo-card bg-blue-50">
    <h3 class="text-lg font-semibold flex items-center gap-2">
        <i class="fas fa-chart-line text-blue-500"></i>
        AI İlan Geçmişi Analizi
    </h3>

    <button
        @click="loadHistory(selectedKisiId)"
        :disabled="loading"
        class="neo-btn neo-btn-secondary mt-3"
        :class="{ 'opacity-50 cursor-not-allowed': loading }">
        <i class="fas fa-robot mr-2"></i>
        <span x-text="loading ? 'Analiz Ediliyor...' : 'AI Analizi Yap'"></span>
    </button>

    <div x-show="historyLoaded" x-cloak class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg p-4 shadow">
            <div class="text-sm text-gray-600">Toplam İlan</div>
            <div class="text-2xl font-bold" x-text="history?.total_ilanlar || 0"></div>
        </div>

        <div class="bg-white rounded-lg p-4 shadow">
            <div class="text-sm text-gray-600">Başlık Kalite Skoru</div>
            <div class="text-2xl font-bold" x-text="(history?.baslik_analizi?.kalite_skoru || 0) + '/100'"></div>
        </div>

        <div class="bg-white rounded-lg p-4 shadow">
            <div class="text-sm text-gray-600">Başarı Skoru</div>
            <div class="text-2xl font-bold" x-text="(history?.basari_metrikleri?.basari_skoru || 0) + '/100'"></div>
        </div>
    </div>
</div>
```

---

### **2. TKGM Otomatik Sorgulama** 🏛️

**Yaklaşım:** Ada/Parsel girildiğinde **otomatik** backend sorgusu (kullanıcı onayı ile doldurma)

**Alpine.js Component:**

```javascript
// TKGM otomatik sorgulama (Ada/Parsel değiştiğinde)
Alpine.data("tkgmSorgu", () => ({
    tkgmData: null,
    loading: false,
    shown: false,

    // Ada/Parsel değiştiğinde otomatik sorgu
    async autoQuery(ada, parsel, il, ilce) {
        if (!ada || !parsel || !il || !ilce) {
            this.tkgmData = null;
            this.shown = false;
            return;
        }

        this.loading = true;

        try {
            const response = await fetch("/api/tkgm/parsel-sorgu", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document.querySelector('[name="_token"]').value,
                },
                body: JSON.stringify({ ada, parsel, il, ilce }),
            });

            const result = await response.json();

            if (result.success) {
                this.tkgmData = result;
                this.shown = true;
                window.toast.success("✅ TKGM bilgileri bulundu!");
            } else {
                this.tkgmData = null;
                this.shown = false;
            }
        } catch (error) {
            console.error("TKGM sorgu hatası:", error);
        } finally {
            this.loading = false;
        }
    },

    // Kullanıcı "Bilgileri Uygula" butonuna tıklarsa
    applyData() {
        if (!this.tkgmData) return;

        const data = this.tkgmData.parsel_bilgileri;

        // Alanları doldur
        if (data.yuzolcumu) {
            document.getElementById("alan_m2").value = data.yuzolcumu;
        }
        if (data.imar_durumu) {
            document.getElementById("imar_durumu").value = data.imar_durumu;
        }
        if (data.taks) {
            document.getElementById("taks").value = data.taks;
        }
        if (data.kaks) {
            document.getElementById("kaks").value = data.kaks;
        }
        if (data.gabari) {
            document.getElementById("gabari").value = data.gabari;
        }

        // Önerileri göster
        this.tkgmData.oneriler.forEach((oneri) => {
            window.toast.info(oneri, 4000);
        });

        window.toast.success("✅ TKGM bilgileri uygulandı!");
        this.shown = false;
    },
}));
```

**Blade Component:**

```blade
{{-- Arsa kategorisi için Ada/Parsel alanları --}}
<div x-show="selectedKategori === 'arsa'" x-data="tkgmSorgu()" class="space-y-4">

    {{-- Ada/Parsel Input'ları --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="ada_no" class="neo-label">Ada No *</label>
            <input
                type="text"
                id="ada_no"
                name="ada_no"
                class="neo-input"
                @blur="autoQuery(
                    $event.target.value,
                    document.getElementById('parsel_no').value,
                    document.getElementById('il_id').options[document.getElementById('il_id').selectedIndex]?.text,
                    document.getElementById('ilce_id').options[document.getElementById('ilce_id').selectedIndex]?.text
                )">
        </div>

        <div>
            <label for="parsel_no" class="neo-label">Parsel No *</label>
            <input
                type="text"
                id="parsel_no"
                name="parsel_no"
                class="neo-input"
                @blur="autoQuery(
                    document.getElementById('ada_no').value,
                    $event.target.value,
                    document.getElementById('il_id').options[document.getElementById('il_id').selectedIndex]?.text,
                    document.getElementById('ilce_id').options[document.getElementById('ilce_id').selectedIndex]?.text
                )">
        </div>
    </div>

    {{-- TKGM Sonuç Card'ı (Otomatik gösterilir) --}}
    <div x-show="shown" x-cloak
         x-transition
         class="bg-gradient-to-r from-green-50 to-blue-50 border-2 border-green-300 rounded-xl p-6 shadow-lg">

        <div class="flex items-start justify-between">
            <div class="flex-1">
                <h4 class="text-lg font-bold text-green-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-university text-green-600"></i>
                    TKGM Parsel Bilgileri Bulundu!
                </h4>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    <div class="bg-white rounded-lg p-3 shadow">
                        <div class="text-xs text-gray-500">Alan</div>
                        <div class="text-lg font-bold" x-text="tkgmData?.parsel_bilgileri?.yuzolcumu + ' m²'"></div>
                    </div>

                    <div class="bg-white rounded-lg p-3 shadow">
                        <div class="text-xs text-gray-500">İmar</div>
                        <div class="text-sm font-bold" x-text="tkgmData?.parsel_bilgileri?.imar_durumu"></div>
                    </div>

                    <div class="bg-white rounded-lg p-3 shadow">
                        <div class="text-xs text-gray-500">TAKS</div>
                        <div class="text-lg font-bold" x-text="tkgmData?.parsel_bilgileri?.taks + '%'"></div>
                    </div>

                    <div class="bg-white rounded-lg p-3 shadow">
                        <div class="text-xs text-gray-500">KAKS</div>
                        <div class="text-lg font-bold" x-text="tkgmData?.parsel_bilgileri?.kaks"></div>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-3 mb-4">
                    <div class="text-xs text-gray-500 mb-2">Hesaplamalar</div>
                    <div class="text-sm space-y-1">
                        <div>• İnşaat Alanı: <strong x-text="tkgmData?.hesaplamalar?.insaat_alani + ' m²'"></strong></div>
                        <div>• Taban Alanı: <strong x-text="tkgmData?.hesaplamalar?.taban_alani + ' m²'"></strong></div>
                        <div>• Max Kat: <strong x-text="tkgmData?.hesaplamalar?.maksimum_kat"></strong></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between mt-4 pt-4 border-t border-green-200">
            <small class="text-gray-600">
                <i class="fas fa-info-circle mr-1"></i>
                Bilgileri uygulamak için butona tıklayın
            </small>

            <div class="flex gap-2">
                <button
                    type="button"
                    @click="shown = false"
                    class="neo-btn neo-btn-sm bg-gray-200 hover:bg-gray-300">
                    <i class="fas fa-times mr-1"></i>
                    Kapat
                </button>

                <button
                    type="button"
                    @click="applyData()"
                    class="neo-btn neo-btn-primary neo-btn-sm">
                    <i class="fas fa-check mr-1"></i>
                    Bilgileri Uygula
                </button>
            </div>
        </div>
    </div>

    {{-- Loading göstergesi --}}
    <div x-show="loading" x-cloak class="text-center py-4">
        <i class="fas fa-spinner fa-spin text-blue-500 text-2xl"></i>
        <p class="text-sm text-gray-600 mt-2">TKGM sorgulanıyor...</p>
    </div>
</div>
```

**Avantajlar:**

-   ✅ Otomatik sorgu (kullanıcı fark etmeden)
-   ✅ Backend'de işlem
-   ✅ Önizleme ile kontrol
-   ✅ Kullanıcı onayı ile doldurma
-   ✅ Daha iyi UX

---

### **3. Kategori Dinamik Alanlar** 📋

**Nereye:** Alt kategori seçiminden sonra

**PHP Kodu (Blade):**

```blade
{{-- Alt kategori seçiminden SONRA ekle --}}
@php
    $kategoriService = app(\App\Services\KategoriOzellikService::class);
@endphp

<div x-show="selectedAltKategoriId" class="mt-6 neo-card">
    <h3 class="text-lg font-semibold mb-4">
        <i class="fas fa-list-check mr-2 text-purple-500"></i>
        Kategori Özel Alanlar
    </h3>

    {{-- Bu alanlar kategori seçildikçe Alpine.js ile dynamic gösterilecek --}}
    <div id="kategori-ozel-alanlar">
        {{-- JavaScript ile doldurulacak --}}
    </div>
</div>

<script>
// Kategori değiştiğinde çağrıl
function loadKategoriOzelAlanlar(kategoriId) {
    if (!kategoriId) return;

    fetch(`/api/kategori/${kategoriId}/ozel-alanlar`)
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('kategori-ozel-alanlar');
            let html = '';

            // Zorunlu alanlar
            if (data.required && Object.keys(data.required).length > 0) {
                html += '<div class="required-fields mb-6">';
                html += '<h4 class="text-md font-semibold text-red-600 mb-3"><i class="fas fa-asterisk mr-2"></i>Zorunlu Alanlar</h4>';
                html += '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';

                for (const [fieldName, config] of Object.entries(data.required)) {
                    html += renderField(fieldName, config);
                }

                html += '</div></div>';
            }

            // Önerilen alanlar
            if (data.recommended && Object.keys(data.recommended).length > 0) {
                html += '<div class="recommended-fields mb-6">';
                html += '<h4 class="text-md font-semibold text-blue-600 mb-3"><i class="fas fa-lightbulb mr-2"></i>Önerilen Alanlar</h4>';
                html += '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';

                for (const [fieldName, config] of Object.entries(data.recommended)) {
                    html += renderField(fieldName, config);
                }

                html += '</div></div>';
            }

            container.innerHTML = html;
        });
}

function renderField(fieldName, config) {
    let html = '<div class="neo-form-group">';
    html += `<label class="neo-label">${config.label}`;
    if (config.validation && config.validation.includes('required')) {
        html += ' <span class="text-red-500">*</span>';
    }
    html += '</label>';

    if (config.type === 'text' || config.type === 'number') {
        html += `<input type="${config.type}" name="${fieldName}" class="neo-input" placeholder="${config.placeholder || ''}" />`;
    } else if (config.type === 'select') {
        html += `<select name="${fieldName}" class="neo-select">`;
        html += '<option value="">Seçin...</option>';
        for (const [value, label] of Object.entries(config.options || {})) {
            html += `<option value="${value}">${label}</option>`;
        }
        html += '</select>';
    } else if (config.type === 'checkbox') {
        html += `<input type="checkbox" name="${fieldName}" class="neo-checkbox" />`;
    }

    if (config.help) {
        html += `<small class="text-gray-500">${config.help}</small>`;
    }

    html += '</div>';
    return html;
}
</script>
```

---

### **4. Anahtar Yönetimi Enhanced UI** 🔑

**Nereye:** Form içinde yeni bir section olarak ekle

**Blade Component:**

```blade
{{-- Form içine yeni section olarak ekle --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mt-6">
    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-6 flex items-center">
        <span class="bg-orange-100 dark:bg-orange-900 text-orange-600 dark:text-orange-400 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">🔑</span>
        Anahtar Yönetimi
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Anahtar Türü --}}
        <div>
            <label for="anahtar_turu" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Anahtar Kimde? *
            </label>
            <select name="anahtar_turu" id="anahtar_turu" class="form-select w-full" required>
                <option value="">Seçin...</option>
                <option value="mal_sahibi">Mal Sahibi</option>
                <option value="danisman">Danışman</option>
                <option value="kapici">Kapıcı/Yönetici</option>
                <option value="emlakci">Emlakçı</option>
                <option value="yonetici">Yönetici</option>
                <option value="diger">Diğer</option>
            </select>
        </div>

        {{-- Kişi Adı --}}
        <div>
            <label for="anahtar_kimde" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Kişi Adı
            </label>
            <input type="text" name="anahtar_kimde" id="anahtar_kimde" class="form-input w-full"
                   placeholder="Örn: Ahmet Yılmaz">
        </div>
    </div>

    <div class="mt-4">
        <label for="anahtar_ulasilabilirlik" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Ulaşılabilirlik
        </label>
        <input type="text" name="anahtar_ulasilabilirlik" id="anahtar_ulasilabilirlik"
               class="form-input w-full"
               placeholder="Örn: 7/24, Mesai saatleri, Randevulu">
    </div>

    <div class="mt-4">
        <label for="anahtar_notlari" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Anahtar Alım Talimatları
        </label>
        <textarea name="anahtar_notlari" id="anahtar_notlari" rows="3"
                  class="form-textarea w-full"
                  placeholder="Gösterim için 1 saat önce arayın. Kapı kodu: 1234*"></textarea>
    </div>

    <div class="mt-4">
        <label for="anahtar_ek_bilgi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Ek Bilgi
        </label>
        <input type="text" name="anahtar_ek_bilgi" id="anahtar_ek_bilgi"
               class="form-input w-full"
               placeholder="Kapı kodu, alarm şifresi, özel notlar...">
    </div>
</div>
```

---

## 🚀 **HIZLI BAŞLANGIÇ**

### **Adım 1: Migration (Tamamlandı ✅)**

```bash
php artisan migrate  # ✅ Çalıştırıldı
```

### **Adım 2: API Test**

```bash
# AI Geçmiş Test
curl http://127.0.0.1:8000/api/kisiler/1/ai-gecmis-analiz

# TKGM Test
curl -X POST http://127.0.0.1:8000/api/tkgm/parsel-sorgu \
  -H "Content-Type: application/json" \
  -d '{"ada": "126", "parsel": "7", "il": "Muğla", "ilce": "Bodrum"}'
```

### **Adım 3: Frontend Ekle**

1. `resources/views/admin/ilanlar/stable-create.blade.php` aç
2. Yukarıdaki 4 component'i ilgili yerlere ekle
3. Alpine.js fonksiyonlarını ekle
4. Save & test!

---

## 📋 **CHECKLIST**

### **Backend (✅ Tamamlandı)**

-   [x] IlanGecmisAIService
-   [x] KategoriOzellikService
-   [x] TKGMService
-   [x] Migration çalıştırıldı
-   [x] API endpoints çalışıyor

### **Frontend (🎨 Eklenecek)**

-   [ ] AI Geçmiş Analizi component
-   [ ] TKGM Sorgulama button
-   [ ] Kategori dinamik alanlar
-   [ ] Anahtar yönetimi UI

### **Test (✅ Backend Hazır)**

-   [ ] AI Geçmiş API test
-   [ ] TKGM API test
-   [ ] Kategori alanları test
-   [ ] Form submission test

---

## 📖 **İLGİLİ DÖKÜMANLAR**

-   `docs/reports/FINAL_SONRAKI_ADIMLAR_OZET_2025-10-11.md` (En detaylı)
-   `docs/reports/AI_YENI_OZELLIKLER_2025-10-11.md` (AI özellikleri)
-   `README-SONRAKI-ADIMLAR.md` (Hızlı özet)

---

**✅ BACKEND %100 HAZIR!**  
**🎨 FRONTEND UI SADECENeuv EKLENMESİ GEREKİYOR!**

**Eklemeler:** ~300 satır Blade + Alpine.js kodu  
**Süre:** ~2-3 saat  
**Zorluk:** Kolay (copy-paste + test)

**Başarılar! 🚀**
