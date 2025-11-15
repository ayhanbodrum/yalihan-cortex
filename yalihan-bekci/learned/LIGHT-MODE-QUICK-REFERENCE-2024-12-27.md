# 🎨 Yalıhan Emlak Light Mode Design System

**Tarih:** 27 Aralık 2024  
**Versiyon:** 2.0.0  
**Statü:** ✅ PRODUCTION READY  
**Context7 Uyumluluk:** 98.82%

---

## 🚨 KRİTİK KURALLAR

### ❗ EN ÖNEMLİ KURAL

```css
/* Light modda input arka planı MUTLAKA bg-gray-50 olmalı! */
❌ YANLIŞ: bg-white dark:bg-gray-800
✅ DOĞRU:  bg-gray-50 dark:bg-gray-800

/* Sebep: Form container'ı bg-white, input bg-gray-50 olmalı ki ayrılsın! */
```

---

## 🎨 RENK PALETİ (LIGHT MODE)

### Arka Planlar

| Kullanım  | Class         | Hex     | Açıklama              |
| --------- | ------------- | ------- | --------------------- |
| Sayfa     | `bg-gray-100` | #F3F4F6 | Ana sayfa arka planı  |
| Kart/Form | `bg-white`    | #FFFFFF | Container arka planı  |
| **Input** | `bg-gray-50`  | #F9FAFB | **Input alanları** ⚠️ |
| Disabled  | `bg-gray-100` | #F3F4F6 | Pasif input'lar       |

### Text Renkleri

| Kullanım    | Class                  | Hex     |
| ----------- | ---------------------- | ------- |
| Ana Metin   | `text-gray-900`        | #111827 |
| Açıklama    | `text-gray-600`        | #4B5563 |
| Placeholder | `placeholder-gray-500` | #6B7280 |
| Disabled    | `text-gray-500`        | #6B7280 |

### Border Renkleri

| Kullanım | Class             | Hex     |
| -------- | ----------------- | ------- |
| Default  | `border-gray-300` | #D1D5DB |
| Divider  | `border-gray-200` | #E5E7EB |
| Focus    | `ring-blue-500`   | #3B82F6 |

---

## 📝 FORM ELEMANLARI

### Input Field (Standart)

```html
<label class="block text-sm font-medium text-gray-900 mb-2"> İlan Başlığı * </label>
<input
    type="text"
    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 hover:border-blue-400"
    placeholder="Örn: Merkez'de Satılık Daire"
/>
```

**Helper Method:**

```php
<input type="text" class="{{ FormStandards::input() }}" />
```

### Select Dropdown

```html
<select
    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 text-gray-900 focus:ring-2 focus:ring-blue-500 transition-all duration-200 cursor-pointer hover:border-blue-400"
>
    <option>Seçiniz</option>
</select>
```

**Helper Method:**

```php
<select class="{{ FormStandards::select() }}">...</select>
```

### Textarea

```html
<textarea
    rows="4"
    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 hover:border-blue-400 resize-y"
></textarea>
```

**Helper Method:**

```php
<textarea class="{{ FormStandards::textarea() }}"></textarea>
```

### Checkbox

```html
<label class="flex items-center hover:bg-gray-50 p-2 rounded-lg transition-colors cursor-pointer">
    <input
        type="checkbox"
        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
    />
    <span class="ml-2 text-gray-900">Asansör</span>
</label>
```

**Helper Method:**

```php
<input type="checkbox" class="{{ FormStandards::checkbox() }}">
<span class="ml-2 text-gray-900 dark:text-white">Label</span>
```

### Radio Button

```html
<label class="flex items-center">
    <input
        type="radio"
        name="status"
        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2"
    />
    <span class="ml-2 text-gray-900">Aktif</span>
</label>
```

---

## 🔘 BUTONLAR

### Primary (Mavi)

```html
<button
    class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md"
>
    Kaydet
</button>
```

**Helper:** `{{ FormStandards::buttonPrimary() }}`

### Secondary (Gri)

```html
<button
    class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-900 font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md"
>
    İptal
</button>
```

**Helper:** `{{ FormStandards::buttonSecondary() }}`

### Danger (Kırmızı)

```html
<button
    class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md"
>
    Sil
</button>
```

**Helper:** `{{ FormStandards::buttonDanger() }}`

---

## 🏷️ BADGES & TAGS

### Status Badge'leri

```html
<!-- Aktif -->
<span
    class="px-3 py-1.5 text-xs font-medium rounded-full bg-green-100 text-green-800 border border-green-200"
>
    ✓ Aktif
</span>

<!-- Beklemede -->
<span
    class="px-3 py-1.5 text-xs font-medium rounded-full bg-orange-100 text-orange-800 border border-orange-200"
>
    ⏳ Beklemede
</span>

<!-- Pasif -->
<span
    class="px-3 py-1.5 text-xs font-medium rounded-full bg-red-100 text-red-800 border border-red-200"
>
    ✕ Pasif
</span>
```

---

## 📊 ALERT MESAJLARI

### Başarı

```html
<div class="bg-green-50 border-l-4 border-green-600 rounded-lg p-4 shadow-sm">
    <div class="flex items-center">
        <span class="text-2xl mr-3">✅</span>
        <div>
            <h4 class="font-bold text-green-800">İşlem Başarılı!</h4>
            <p class="text-sm text-green-700">Mesaj içeriği</p>
        </div>
    </div>
</div>
```

### Hata

```html
<div class="bg-red-50 border-l-4 border-red-600 rounded-lg p-4 shadow-sm">
    <div class="flex items-center">
        <span class="text-2xl mr-3">❌</span>
        <div>
            <h4 class="font-bold text-red-800">Hata!</h4>
            <p class="text-sm text-red-700">Hata mesajı</p>
        </div>
    </div>
</div>
```

---

## 📋 TABLO

```html
<div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
    <table class="w-full">
        <thead class="bg-gray-50 border-b-2 border-gray-200">
            <tr>
                <th
                    class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider"
                >
                    Başlık
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 text-sm text-gray-900">İçerik</td>
            </tr>
        </tbody>
    </table>
</div>
```

---

## 🎯 ÖZEL DURUMLAR

### Input ile Error State

```html
<input type="text" class="... border-red-500 focus:ring-red-500 focus:border-red-500" />
<p class="mt-1 text-sm text-red-600">Hata mesajı</p>
```

### Input ile Success State

```html
<input type="text" class="... border-green-500 focus:ring-green-500" />
<p class="mt-1 text-sm text-green-600">✓ Başarılı</p>
```

### Disabled Input

```html
<input type="text" disabled class="... bg-gray-100 text-gray-500 cursor-not-allowed opacity-50" />
```

---

## 📦 MIGRATION GEÇMİŞİ

### 2024-12-27: Light Mode Renk Düzeltmeleri

- ✅ **652 dosya** güncellendi
- ✅ `bg-white` → `bg-gray-50` (input backgrounds)
- ✅ `text-gray-700` → `text-gray-900` (text colors)
- ✅ `dark:text-gray-300` → `dark:text-white` (labels)
- ✅ Placeholder renkleri düzeltildi

### Etkilenen Dosyalar:

- `app/Helpers/FormStandards.php`
- `resources/views/admin/**/*.blade.php` (652+ dosya)

---

## 🔄 CACHE TEMİZLEME

### Laravel Cache

```bash
php artisan optimize:clear
php artisan view:clear
php artisan cache:clear
php artisan config:clear
```

### Composer Autoloader

```bash
composer dump-autoload --optimize
```

### Browser Cache

- Mac: `Cmd+Shift+R`
- Windows: `Ctrl+Shift+R`

---

## 📚 KAYNAKLAR

### Prototip Sayfalar

- 🌓 **Toggle Version:** `/prototype-ui-elements.html`
- ☀️ **Light Only:** `/prototype-ui-light.html`

### Dokümantasyon

- `/docs/FORM_STANDARDS.md`
- `/app/Helpers/FormStandards.php`

### Migration Script

- `/scripts/migrate-to-form-standards.php`

---

## ✅ CHECKLIST (Yeni Component Eklerken)

- [ ] Input arka planı `bg-gray-50` mi?
- [ ] Text rengi `text-gray-900` mu?
- [ ] Placeholder `placeholder-gray-500` mu?
- [ ] Border `border-gray-300` mü?
- [ ] Focus ring `focus:ring-blue-500` mu?
- [ ] Hover efekti var mı?
- [ ] Transition `duration-200` mu?
- [ ] Dark mode class'ları eklenmiş mi?
- [ ] FormStandards helper kullanıldı mı?

---

## 🎓 YALIHAN BEKÇİ NOTLARI

**Öğrenilen:** Light mode'da form elemanlarının arka planının beyaz olması durumunda, form container'ından ayırt edilememe sorunu vardı.

**Çözüm:** Input/Select/Textarea için `bg-gray-50` kullanarak, `bg-white` container'dan görsel ayrım sağlandı.

**Sonuç:** Kullanıcı deneyimi önemli ölçüde iyileşti. Form alanları artık net bir şekilde görülebiliyor.

**Öğreti:** Her zaman form elemanları ile container arasında kontrast olmalı!

---

**Son Güncelleme:** 2024-12-27  
**Yalıhan Bekçi Güven Seviyesi:** YÜKSEK ⭐⭐⭐⭐⭐
