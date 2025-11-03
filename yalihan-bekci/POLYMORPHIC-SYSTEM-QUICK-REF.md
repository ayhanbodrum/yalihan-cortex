# 🚀 POLYMORPHIC FEATURES SYSTEM - QUICK REFERENCE

> **Son Güncelleme:** 2 Kasım 2025  
> **Durum:** ✅ PRODUCTION READY

---

## 📦 4 CORE TABLE

```sql
feature_categories  → Kategoriler (Arsa, Konut, İşyeri, Site, Yazlık)
features           → Gerçek özellikler (Oda Sayısı, Havuz, etc.)
feature_assignments → Polymorphic atamalar (hangi type'a hangi özellik)
feature_values     → Polymorphic değerler (ilan'ların özellik değerleri)
```

---

## 🎯 4 MODEL + 1 TRAIT

```php
FeatureCategory     → app/Models/FeatureCategory.php
Feature            → app/Models/Feature.php
FeatureAssignment  → app/Models/FeatureAssignment.php (Polymorphic)
FeatureValue       → app/Models/FeatureValue.php (Polymorphic)

HasFeatures        → app/Traits/HasFeatures.php (Use in models)
```

---

## 🔧 TRAIT USAGE

```php
use App\Traits\HasFeatures;

class Ilan extends Model {
    use HasFeatures;
}

// Özellik Atama
$propertyType->assignFeature($feature, ['is_required' => true]);

// Değer Atama
$ilan->setFeatureValue('oda-sayisi', '3+1');

// Değer Okuma
$value = $ilan->getFeatureValue('oda-sayisi');
```

---

## 🛣️ KEY ROUTES

```
POST   /property-type/{id}/assign-feature
DELETE /property-type/{id}/unassign-feature
POST   /toggle-feature-assignment
GET    /admin/ozellikler/kategoriler
GET    /admin/property-type-manager/{id}/field-dependencies
```

---

## 📊 SEEDER COMMANDS

```bash
# Migration Seeder (Eski verilerden yeniye)
php artisan db:seed --class=PolymorphicFeaturesMigrationSeeder

# Sample Data (44 özellik)
php artisan db:seed --class=SampleFeaturesSeeder
```

---

## 🎨 FIELD TYPES

```
text, number, boolean, select, checkbox, radio, textarea, date, price
```

---

## ✅ ACTIVE PAGES

```
✅ /admin/ozellikler/kategoriler          (Kategori yönetimi)
✅ /admin/ozellikler                      (Özellik yönetimi)
✅ /admin/property-type-manager/1/field-dependencies  (Özellik atama)
🔄 /admin/site-ozellikleri                (→ Redirects to kategoriler)
```

---

## ❌ REMOVED SYSTEM

```
❌ SiteOzellikController
❌ resources/views/admin/site-ozellikleri/
❌ Eski site özellikleri sistemi
```

---

## 🎯 CONTEXT7 COMPLIANCE

```
Database Fields: English ✅ (category_id, enabled, field_type)
UI Translations: Türkçe ✅ (Özellik Yönetimi, Kategori, etc.)
Model Names: Context7 ✅ (FeatureCategory, Feature)
```

---

## 📈 STATISTICS

```
Categories: 5
Features:   44
  ├── 🏗️ Arsa:    8
  ├── 🏠 Konut:   14
  ├── 🏢 İşyeri:  12
  └── 🏖️ Yazlık:  10
```

---

## 🔍 TINKER COMMANDS

```php
// Kategori sayısı
FeatureCategory::count()

// Özellik sayısı
Feature::count()

// Atama sayısı
FeatureAssignment::count()

// Kategori ile özellikleri
FeatureCategory::with('features')->find(1)

// Property type'a atanmış özellikler
IlanKategoriYayinTipi::find(1)->featureAssignments
```

---

**Yalıhan Bekçi Ready ✅**

