# 🚀 Yalıhan Emlak Teknoloji Standartları

**Tarih:** 21 Ekim 2025  
**Durum:** ✅ ACTIVE  
**Kapsam:** Tüm proje

---

## 📊 TEKNOLOJİ STACK'İ

### **1. 🔍 ARAMA SİSTEMİ**

| Özellik       | Değer                                                             |
| ------------- | ----------------------------------------------------------------- |
| **Standart**  | Context7 Live Search                                              |
| **Teknoloji** | Vanilla JS (0 dependency)                                         |
| **Dosya**     | `public/js/context7-live-search.js`                               |
| **Boyut**     | 35KB (unminified)                                                 |
| **API**       | `/api/kisiler/search`, `/api/sites/search`, `/api/ilanlar/search` |
| **Kullanım**  | 14 admin sayfası                                                  |

**✅ Özellikler:**

- Zero dependencies
- Debounce 300ms
- Keyboard navigation
- Cache system
- API integration

**❌ YASAK:**

- Select2 (jQuery - LEGACY)
- React-Select (170KB - ÇOK AĞIR)
- Choices.js (48KB - AĞIR)
- Selectize.js (jQuery)
- Vue-Select (Vue)

---

### **2. ⚛️ REACTIVE UI**

| Özellik      | Değer               |
| ------------ | ------------------- |
| **Standart** | Alpine.js           |
| **Versiyon** | 3.15.0              |
| **Boyut**    | 15KB                |
| **Kullanım** | Reactive components |

**✅ İzin Verilen:**

- `x-data` (reactive state)
- `x-model` (two-way binding)
- `x-show` / `x-if` (conditionals)
- `@click` / `@input` (events)
- `x-transition` (animations)

**❌ YASAK:**

- React (too heavy)
- Vue (not in stack)
- Angular (overkill)
- jQuery (legacy)

---

### **3. 🎨 CSS FRAMEWORK**

| Özellik           | Değer             |
| ----------------- | ----------------- |
| **Standart**      | Tailwind CSS      |
| **Versiyon**      | 3.4.18            |
| **Design System** | Neo Design System |

**🎨 Renk Paleti:**

- Primary: Orange `#f97316`
- Secondary: Blue `#3b82f6`
- Success: Green `#10b981`
- Warning: Yellow `#f59e0b`
- Danger: Red `#ef4444`

**🧩 Neo Components:**

- `neo-btn` (buttons)
- `neo-card` (cards)
- `neo-input` (form inputs)
- `neo-select` (dropdowns)
- `neo-badge` (badges)

**❌ YASAK:**

- Bootstrap (old stack)
- Material UI (React)
- Bulma (not in use)

---

### **4. 🗺️ HARİTA SİSTEMİ**

| Özellik      | Değer         |
| ------------ | ------------- |
| **Standart** | Leaflet.js    |
| **Versiyon** | 1.9.4         |
| **Provider** | OpenStreetMap |

**✅ Özellikler:**

- Interactive maps
- Marker placement
- Address geocoding
- Custom tiles

**❌ YASAK:**

- Google Maps (API cost)
- Mapbox (licensing)

---

### **5. 🛠️ BUILD TOOL**

| Özellik      | Değer            |
| ------------ | ---------------- |
| **Standart** | Vite             |
| **Config**   | `vite.config.js` |

**✅ Özellikler:**

- Fast HMR
- ES modules
- Code splitting
- Tree shaking

**❌ YASAK:**

- Webpack (old, slow)
- Parcel (not in stack)

---

## 📦 BUNDLE SIZE HEDEFLERI

```
Per Page JS:    < 50KB gzipped
Vendor Bundle:  < 100KB gzipped
Total Bundle:   < 150KB gzipped
CSS Bundle:     < 30KB gzipped
```

**✅ MEVCUT DURUM:**

- stable-create: 44KB (11.57KB gzipped) ✅
- Context7 Live Search: 35KB ✅
- Alpine.js: 15KB ✅
- **TOPLAM: ~60KB ✅ OPTIMAL!**

---

## 🎯 PERFORMANS HEDEFLERİ

- **Page Load:** < 2 seconds
- **API Response:** < 500ms
- **Search Debounce:** 300ms
- **Lighthouse Score:** > 90
- **Bundle Optimization:** Tree shaking + minification

---

## 🧩 COMPONENT LIBRARY

**Standart:** Neo Components (Blade)  
**Location:** `resources/views/components/`

**Mevcut:**

- ✅ `neo-input.blade.php`
- ✅ `neo-select.blade.php`
- 🔜 `neo-textarea.blade.php`
- 🔜 `neo-checkbox.blade.php`
- 🔜 `neo-radio.blade.php`

**Kullanım:** %1.4 → **Hedef: %80**

**Örnek:**

```blade
<x-neo-input
    name="baslik"
    label="İlan Başlığı"
    :required="true"
    placeholder="Örn: Merkezi Konumda 3+1 Daire" />

<x-neo-select
    name="kategori"
    label="Kategori"
    :required="true">
    <option value="">Kategori Seçin</option>
    <option value="1">Daire</option>
</x-neo-select>
```

---

## ⚠️ DEPRECATED TEKNOLOJİLER

### **Select2**

- **Durum:** LEGACY - Kaldırılıyor
- **Kullanım:** 34 dosya (azalıyor)
- **Yerine:** Context7 Live Search
- **Deadline:** 2026-Q2

### **jQuery**

- **Durum:** LEGACY - Yeni kullanım yasak
- **Mevcut:** Sadece Select2 üzerinden
- **Yerine:** Vanilla JS + Alpine.js
- **Hedef:** 2026-Q4 tamamen kaldırılacak

### **Bootstrap**

- **Durum:** KALDIRILDI ✅
- **Yerine:** Tailwind CSS + Neo Design
- **Migrasyon:** Tamamlandı

---

## 🛡️ ENFORCEMENT KURALLARI

1. **Yeni Kod:** Sadece onaylı teknolojiler kullanılmalı
2. **Ağır Kütüphaneler:** >50KB kütüphaneler onay gerektirir
3. **Vanilla JS First:** Kütüphane yerine Vanilla JS tercih et
4. **Alpine for Reactive:** Reactive UI için Alpine.js kullan
5. **Context7 for Search:** Tüm aramalar Context7 Live Search
6. **Neo Components:** Tüm formlar Neo component'ler
7. **Bundle Monitoring:** Her build'de bundle boyutu kontrol et

---

## 📊 MİGRASYON DURUMU

### **Context7 Live Search:**

- ✅ Tamamlanan: 14 sayfa
- ⏳ Kalan: 34 dosya
- 🎯 Hedef: 2026'da %100

### **Neo Components:**

- ✅ Dönüştürülen dosya: 4
- ✅ Dönüştürülen alan: 16
- 🎯 Hedef: 2026'da tüm formlar

---

## 🎓 REFERANSLAR

- **Authority:** `.context7/authority.json` → `technology_standards`
- **Kural:** `JAVASCRIPT-STANDART-KURALLARI.md`
- **Bekçi:** `yalihan-bekci/knowledge/javascript-vanilla-only-rule.json`
- **Migration:** `docs/technical/CONTEXT7-LIVE-SEARCH-MIGRATION-2025-10-13.md`

---

**🛡️ Yalıhan Bekçi bu standartları aktif olarak koruyacak!**

**Status:** 🟢 ACTIVE  
**Last Updated:** 2025-10-21
