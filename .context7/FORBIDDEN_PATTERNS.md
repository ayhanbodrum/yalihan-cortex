# 🚫 Context7 Forbidden Patterns - Yasak Desenler

**Son Güncelleme:** Kasım 2025  
**Versiyon:** 5.4.0  
**Durum:** ✅ ACTIVE - PERMANENT  
**Kaynak:** `.context7/authority.json` (TEK YETKİLİ KAYNAK)

---

## ⚠️ ÖNEMLİ NOT

**Tüm yasak pattern'ler `authority.json` dosyasında tanımlıdır.** Bu dosya sadece referans amaçlıdır. Gerçek kurallar için `authority.json` dosyasına bakın.

---

## 🚫 Database Field Naming - Yasak Desenler

### Status Field

```php
// ❌ YASAK
'enabled'
'is_active' 
'aktif'
'durum'
'active' (as field name)

// ✅ ZORUNLU
'status'
```

**Detay:** `.context7/ENABLED_FIELD_FORBIDDEN.md`

### Order Field

```php
// ❌ YASAK
'order'
$table->integer('order')->default(0);
->orderBy('order')

// ✅ ZORUNLU
'display_order'
$table->integer('display_order')->default(0);
->orderBy('display_order')
```

**Detay:** `.context7/ORDER_DISPLAY_ORDER_STANDARD.md`

### Location Fields

```php
// ❌ YASAK
'sehir' / 'sehir_id'
'semt_id'

// ✅ ZORUNLU
'il' / 'il_id'
'mahalle_id'
```

**Detay:** `.context7/LOCATION_MAHALLE_ID_STANDARD.md`

### Terminology

```php
// ❌ YASAK
'musteri'
'musteri_id'

// ✅ ZORUNLU
'kisi'
'kisi_id'
```

---

## 🚫 CSS Classes - Yasak Desenler

### Neo Design System

```html
<!-- ❌ YASAK -->
<button class="neo-btn neo-btn-primary">Kaydet</button>
<div class="neo-card">...</div>
<input class="neo-input">...</input>

<!-- ✅ ZORUNLU -->
<button class="px-4 py-2 bg-blue-600 text-white rounded-lg
               hover:bg-blue-700 hover:scale-105
               transition-all duration-200
               dark:bg-blue-500 dark:hover:bg-blue-600">
    Kaydet
</button>
```

**Detay:** `.context7/TAILWIND-TRANSITION-RULE.md`

### Bootstrap Classes

```html
<!-- ❌ YASAK -->
<button class="btn btn-primary">Kaydet</button>
<div class="card">...</div>
<input class="form-control">...</input>

<!-- ✅ ZORUNLU -->
<!-- Tailwind utility classes kullan -->
```

---

## 🚫 Route Naming - Yasak Desenler

### Double Prefix

```php
// ❌ YASAK - Çift prefix oluşur
Route::prefix('admin')->name('admin.')->group(function () {
    Route::prefix('crm')->name('admin.crm.')->group(function () {
        Route::get('/', ...)->name('dashboard');
        // Sonuç: admin.admin.crm.dashboard ❌ BLOCKED!
    });
});

// ✅ ZORUNLU - İç group sadece kendi prefix'ini ekler
Route::prefix('admin')->name('admin.')->group(function () {
    Route::prefix('crm')->name('crm.')->group(function () {
        Route::get('/', ...)->name('dashboard');
        // Sonuç: admin.crm.dashboard ✅
    });
});
```

**Detay:** `.context7/ROUTE_NAMING_STANDARD.md`

### Old Route Prefixes

```php
// ❌ YASAK
route('crm.*')

// ✅ ZORUNLU
route('admin.*')
```

---

## 📋 Tüm Yasak Pattern'ler

Tüm yasak pattern'ler `authority.json` dosyasında tanımlıdır:

```json
{
  "context7": {
    "permanent_standards": {
      "enabled_field": "FORBIDDEN - PERMANENT",
      "order_field": "FORBIDDEN - PERMANENT (use display_order)",
      "musteri_terminology": "FORBIDDEN - PERMANENT",
      "neo_design": "FORBIDDEN - PERMANENT",
      "route_naming": "DOUBLE_PREFIX_FORBIDDEN - PERMANENT"
    }
  }
}
```

---

## 🔍 Detaylı Dokümantasyon

- **Status Field:** `.context7/ENABLED_FIELD_FORBIDDEN.md`
- **Order Field:** `.context7/ORDER_DISPLAY_ORDER_STANDARD.md`
- **Route Naming:** `.context7/ROUTE_NAMING_STANDARD.md`
- **Location:** `.context7/LOCATION_MAHALLE_ID_STANDARD.md`
- **CSS/Tailwind:** `.context7/TAILWIND-TRANSITION-RULE.md`
- **Form Design:** `.context7/FORM_DESIGN_STANDARDS.md`

---

## ⚡ Hızlı Referans

| Kategori | Yasak | Zorunlu |
|----------|-------|---------|
| Status Field | `enabled`, `aktif`, `durum` | `status` |
| Order Field | `order` | `display_order` |
| Location | `sehir_id`, `semt_id` | `il_id`, `mahalle_id` |
| Terminology | `musteri` | `kisi` |
| CSS | `neo-*`, `btn-*` | Tailwind utilities |
| Routes | `crm.*`, double prefix | `admin.*`, single prefix |

---

**Kaynak:** `.context7/authority.json` (TEK YETKİLİ KAYNAK)  
**Son Güncelleme:** Kasım 2025

