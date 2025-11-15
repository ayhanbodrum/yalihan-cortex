# Controller'larda `order` Kullanım Analizi

**Tarih:** 2025-11-09  
**Toplam Kullanım:** 43 adet  
**Kritik:** 5 adet (Database operations)  
**Orta:** 35 adet (Array keys, Validation rules)  
**Düşük:** 3 adet (Model property access - accessor var)

---

## 📊 Dosya Bazında Kullanım

| Dosya | Kullanım Sayısı | Kritiklik |
|-------|----------------|-----------|
| `PropertyTypeManagerController.php` | 21 | 🔴 Yüksek |
| `OzellikKategoriController.php` | 9 | 🟡 Orta |
| `Api/PhotoController.php` | 5 | 🟢 Düşük (sira kolonu) |
| `YayinTipiYoneticisiController.php` | 5 | 🟡 Orta |
| `Admin/PhotoController.php` | 3 | 🟢 Düşük |
| `OzellikController.php` | 2 | 🟡 Orta |
| `IlanKategoriController.php` | 2 | 🟢 Düşük (backward compat) |
| `FeatureCategoryController.php` | 2 | 🟡 Orta |
| `Api/BulkOperationsController.php` | 1 | 🟡 Orta |

---

## 🔴 KRİTİK: Database Operations (5 adet)

Bu kullanımlar **mutlaka** `display_order` olmalı:

### 1. PropertyTypeManagerController.php
```php
// Satır 372, 378, 457, 516, 528, 998, 1080, 1088
->update(['order' => $value])  // ❌ display_order olmalı
->create(['order' => $value])  // ❌ display_order olmalı
```

### 2. OzellikKategoriController.php
```php
// Satır 161, 202
->update(['order' => $value])  // ❌ display_order olmalı
$model->order = $value;        // ❌ display_order olmalı
```

### 3. Api/BulkOperationsController.php
```php
// Satır 216
->update(['order' => $value])  // ❌ display_order olmalı
```

---

## 🟡 ORTA: Validation Rules & Array Keys (35 adet)

Bu kullanımlar **backward compatibility** için bırakılabilir ama ideal olarak `display_order` olmalı:

### Validation Rules
```php
'order' => 'nullable|integer|min:0'  // Form validation
```

**Not:** Form validation'da `order` kabul edilebilir çünkü:
- Model'de accessor var (`getOrderAttribute()`)
- Backward compatibility için gerekli
- Ama ideal olarak `display_order` kullanılmalı

### Array Keys (Response Data)
```php
'order' => $model->order  // Response'da gösteriliyor
```

**Not:** Response'da `order` gösterilmesi sorun değil çünkü:
- Model accessor kullanılıyor
- API backward compatibility için gerekli
- Ama ideal olarak `display_order` kullanılmalı

---

## 🟢 DÜŞÜK: Model Property Access (3 adet)

Bu kullanımlar **sorun değil** çünkü model'de accessor var:

```php
$model->order  // ✅ Accessor kullanılıyor (getOrderAttribute())
```

**Dosyalar:**
- `PropertyTypeManagerController.php:168, 215, 537`
- `YayinTipiYoneticisiController.php:103, 159`

---

## 🟢 ÖZEL DURUM: PhotoController

`PhotoController`'da `order` kullanımı **farklı bir durum**:

```php
'order' => $photo->sira  // ✅ Tabloda 'sira' kolonu var, 'order' değil
```

**Not:** Bu dosyalarda `order` → `sira` mapping var, `display_order` değil.

---

## ✅ ÖNERİLER

### 1. Kritik Düzeltmeler (5 adet)
- `PropertyTypeManagerController.php`: Database operations → `display_order`
- `OzellikKategoriController.php`: Database operations → `display_order`
- `Api/BulkOperationsController.php`: Database operations → `display_order`

### 2. Orta Öncelik (35 adet)
- Validation rules: `'order'` → `'display_order'` (backward compat için `'order'` de bırakılabilir)
- Array keys: Response'larda `'order'` → `'display_order'` (backward compat için `'order'` de bırakılabilir)

### 3. Düşük Öncelik (3 adet)
- Model property access: Değiştirmeye gerek yok (accessor var)

---

## 📝 ÖRNEK DÜZELTMELER

### Örnek 1: Database Update
```php
// ❌ YANLIŞ
->update(['order' => $value])

// ✅ DOĞRU
->update(['display_order' => $value])
```

### Örnek 2: Database Create
```php
// ❌ YANLIŞ
->create(['order' => $value])

// ✅ DOĞRU
->create(['display_order' => $value])
```

### Örnek 3: Model Property
```php
// ❌ YANLIŞ (eğer accessor yoksa)
$model->order = $value;

// ✅ DOĞRU
$model->display_order = $value;
```

### Örnek 4: Validation (Backward Compat)
```php
// ✅ İDEAL
'display_order' => 'nullable|integer|min:0'

// ✅ BACKWARD COMPAT (kabul edilebilir)
'order' => 'nullable|integer|min:0',  // Model accessor ile çalışır
'display_order' => 'nullable|integer|min:0'
```

---

## 🎯 ÖNCELİK SIRASI

1. **🔴 Yüksek:** Database operations (5 adet) - Hemen düzeltilmeli
2. **🟡 Orta:** Validation rules & Array keys (35 adet) - İdeal olarak düzeltilmeli
3. **🟢 Düşük:** Model property access (3 adet) - Değiştirmeye gerek yok

---

**Son Güncelleme:** 2025-11-09

