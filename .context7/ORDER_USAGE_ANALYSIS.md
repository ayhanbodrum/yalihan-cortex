# Order Kullanımı Analizi - 2025-11-09

## 📊 ÖZET

**Toplam `orderBy('order')` Kullanımı:** 30 satır  
**Etkilenen Dosya:** ~20 dosya  
**Durum:** ⚠️ Düzeltme Gerekiyor

---

## 🚨 KRİTİK SORUNLAR

### 1. Controller'larda `orderBy('order')` Kullanımı

**Sorun:** Veritabanında `order` kolonu yok, `display_order` var. Bu kullanımlar hata verebilir.

**Etkilenen Dosyalar:**

#### Admin Controllers:
- `app/Http/Controllers/Admin/PropertyTypeManagerController.php`
  - Satır 685: `->orderBy('order')`
  
- `app/Http/Controllers/Admin/YayinTipiYoneticisiController.php`
  - Satır 28: `->orderBy('order')`
  - Satır 35: `->orderBy('order')`
  
- `app/Http/Controllers/Admin/OzellikKategoriController.php`
  - Satır 37: `->orderBy('order')`
  - Satır 180: `->orderBy('order')`
  
- `app/Http/Controllers/Admin/OzellikController.php`
  - Satır 19: `->orderBy('order')`
  - Satır 34: `->orderBy('order')`
  
- `app/Http/Controllers/Admin/TalepController.php`
  - Satır 85: `->orderBy('order')`
  - Satır 231: `->orderBy('order')`
  
- `app/Http/Controllers/Admin/YazlikKiralamaController.php`
  - Satır 568: `->orderBy('order')`
  
- `app/Http/Controllers/Admin/AdminController.php`
  - Satır 85: `->orderBy('order')`
  
- `app/Http/Controllers/Admin/AICategoryController.php`
  - Satır 27: `->orderBy('order')`
  - Satır 43: `->orderBy('order')`

#### Frontend Controllers:
- `app/Http/Controllers/Frontend/DynamicFormController.php`
  - Satır 54: `->orderBy('order')`

#### API Controllers:
- `app/Http/Controllers/Api/CategoriesController.php`
  - Satır 23: `->orderBy('order')`
  - Satır 86: `->orderBy('order')`
  
- `app/Http/Controllers/Api/CategoryController.php`
  - Satır 27: `->orderBy('order')`
  - Satır 88: `->orderBy('order')`
  
- `app/Http/Controllers/Api/FieldDependencyController.php`
  - Satır 45: `->orderBy('order', 'asc')`
  - Satır 190: `->orderBy('order', 'asc')`

---

### 2. Model'lerde `orderBy('order')` Kullanımı

**Sorun:** Bu modellerde `order` kolonu hala kullanılıyor.

**Etkilenen Modeller:**

- `app/Models/IlanKategoriYayinTipi.php`
  - Satır 76: `return $query->orderBy('order');` ❌
  - **Not:** `scopeSiralı()` metodunda `display_order` kullanılmalı

- `app/Models/FeatureAssignment.php`
  - Satır 70: `return $query->orderBy('order')->orderBy('id');`
  - **Not:** Bu tabloda `order` kolonu var mı kontrol edilmeli

- `app/Models/Etiket.php`
  - Satır 112: `return $query->orderBy('order')->orderBy('name');`
  - Satır 119: `->orderBy('order')`
  - **Not:** `etiketler` tablosunda `order` kolonu var mı kontrol edilmeli

- `app/Models/DashboardWidget.php`
  - Satır 76: `return $query->orderBy('order')->orderBy('position_y')->orderBy('position_x');`
  - **Not:** `dashboard_widgets` tablosunda `order` kolonu var mı kontrol edilmeli

- `app/Models/KategoriYayinTipiFieldDependency.php`
  - Satır 105: `return $query->orderBy('order')->orderBy('field_name');`
  - **Not:** Bu tabloda `order` kolonu var mı kontrol edilmeli

---

## ✅ ÇÖZÜM ÖNERİLERİ

### Öncelik 1: Kritik Controller'ları Düzelt

**Hedef Tablolar:**
- `ilan_kategorileri` → `display_order` ✅ (zaten düzeltildi)
- `ilan_kategori_yayin_tipleri` → `display_order` ✅ (zaten düzeltildi)
- `ozellik_kategorileri` → `display_order` ✅ (zaten düzeltildi)

**Yapılacaklar:**
1. Tüm `orderBy('order')` → `orderBy('display_order')` değiştir
2. `select(['...', 'order'])` → `select(['...', 'display_order'])` değiştir
3. `'order' => $value` → `'display_order' => $value` değiştir

### Öncelik 2: Diğer Tabloları Kontrol Et

**Kontrol Edilecek Tablolar:**
- `feature_assignments` → `order` kolonu var mı?
- `etiketler` → `order` kolonu var mı?
- `dashboard_widgets` → `order` kolonu var mı?
- `kategori_yayin_tipi_field_dependencies` → `order` kolonu var mı?

**Eğer `order` kolonu varsa:**
- Migration ile `order` → `display_order` rename yapılmalı
- Model ve Controller'lar güncellenmeli

**Eğer `order` kolonu yoksa:**
- `orderBy('order')` → `orderBy('display_order')` değiştirilmeli

---

## 📋 DÜZELTME LİSTESİ

### Controller'lar (20 dosya)

1. ✅ `PropertyTypeManagerController.php` - `orderBy('order')` → `orderBy('display_order')`
2. ✅ `YayinTipiYoneticisiController.php` - `orderBy('order')` → `orderBy('display_order')`
3. ✅ `OzellikKategoriController.php` - `orderBy('order')` → `orderBy('display_order')`
4. ✅ `OzellikController.php` - `orderBy('order')` → `orderBy('display_order')`
5. ✅ `TalepController.php` - `orderBy('order')` → `orderBy('display_order')`
6. ✅ `YazlikKiralamaController.php` - `orderBy('order')` → `orderBy('display_order')`
7. ✅ `AdminController.php` - `orderBy('order')` → `orderBy('display_order')`
8. ✅ `AICategoryController.php` - `orderBy('order')` → `orderBy('display_order')`
9. ✅ `DynamicFormController.php` - `orderBy('order')` → `orderBy('display_order')`
10. ✅ `CategoriesController.php` - `orderBy('order')` → `orderBy('display_order')`
11. ✅ `CategoryController.php` - `orderBy('order')` → `orderBy('display_order')`
12. ✅ `FieldDependencyController.php` - `orderBy('order')` → `orderBy('display_order')`

### Model'ler (5 dosya)

1. ✅ `IlanKategoriYayinTipi.php` - `scopeSiralı()` metodunu düzelt
2. ⚠️ `FeatureAssignment.php` - Tablo kontrolü gerekli
3. ⚠️ `Etiket.php` - Tablo kontrolü gerekli
4. ⚠️ `DashboardWidget.php` - Tablo kontrolü gerekli
5. ⚠️ `KategoriYayinTipiFieldDependency.php` - Tablo kontrolü gerekli

---

## 🔍 TABLO KONTROLÜ

Önce şu tablolarda `order` kolonu var mı kontrol edilmeli:

```sql
SELECT TABLE_NAME, COLUMN_NAME 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'yalihan_emlak' 
AND COLUMN_NAME = 'order' 
AND TABLE_NAME IN (
    'feature_assignments',
    'etiketler',
    'dashboard_widgets',
    'kategori_yayin_tipi_field_dependencies'
);
```

---

**Son Güncelleme:** 2025-11-09  
**Durum:** ⚠️ Düzeltme Gerekiyor - 30 satır `orderBy('order')` kullanımı var

