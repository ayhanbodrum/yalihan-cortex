# Context7 Compliance Report

**Tarih:** 2025-11-11 13:45:14
**Durum:** ⚠️ TARAMA TAMAMLANDI

---

## 📊 Özet

- **Toplam İhlal:** 19
- **Critical:** 19
- **High:** 0
- **Medium:** 0
- **Low:** 0

---

## İhlaller

### critical: database/migrations/2025_10_19_224521_add_missing_indexes_to_existing_tables.php:50

**Pattern:** `'order'`
**Mesaj:** order → display_order kullanılmalı

---

### critical: database/migrations/2025_10_19_224521_add_missing_indexes_to_existing_tables.php:79

**Pattern:** `'order'`
**Mesaj:** order → display_order kullanılmalı

---

### critical: database/migrations/2025_10_10_175050_create_ozellikler_table.php:24

**Pattern:** `'order'`
**Mesaj:** order → display_order kullanılmalı

---

### critical: app/Console/Commands/StandardCheck.php:225

**Pattern:** `'durum'`
**Mesaj:** durum → status kullanılmalı

---

### critical: database/migrations/2025_10_27_090709_create_yazlik_doluluk_durumlari_table.php:15

**Pattern:** `'durum'`
**Mesaj:** durum → status kullanılmalı

---

### critical: database/migrations/2025_10_27_090709_create_yazlik_doluluk_durumlari_table.php:23

**Pattern:** `'durum'`
**Mesaj:** durum → status kullanılmalı

---

### critical: database/migrations/2025_10_27_090709_create_yazlik_doluluk_durumlari_table.php:24

**Pattern:** `'durum'`
**Mesaj:** durum → status kullanılmalı

---

### critical: database/migrations/2025_10_27_090709_create_yazlik_doluluk_durumlari_table.php:25

**Pattern:** `'durum'`
**Mesaj:** durum → status kullanılmalı

---

### critical: app/Http/Controllers/Frontend/DanismanController.php:84

**Pattern:** `'aktif'`
**Mesaj:** aktif → status kullanılmalı

---

### critical: app/Console/Commands/StandardCheck.php:225

**Pattern:** `sehir`
**Mesaj:** sehir → il kullanılmalı

---

### critical: database/seeders/OzellikKategorileriSeeder.php:86

**Pattern:** `sehir`
**Mesaj:** sehir → il kullanılmalı

---

### critical: database/seeders/OzellikKategorileriSeeder.php:184

**Pattern:** `sehir`
**Mesaj:** sehir → il kullanılmalı

---

### critical: database/seeders/OzellikKategorileriSeeder.php:190

**Pattern:** `sehir`
**Mesaj:** sehir → il kullanılmalı

---

### critical: database/seeders/OzellikKategorileriSeeder.php:202

**Pattern:** `sehir`
**Mesaj:** sehir → il kullanılmalı

---

### critical: database/seeders/RevyStyleFeatureCategoriesSeeder.php:290

**Pattern:** `sehir`
**Mesaj:** sehir → il kullanılmalı

---

### critical: resources/views/admin/layouts/neo.blade.php:205

**Pattern:** `neo-*`
**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

---

### critical: resources/views/components/admin/neo-toast.blade.php:6

**Pattern:** `neo-*`
**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

---

### critical: resources/views/components/admin/neo-toast.blade.php:7

**Pattern:** `neo-*`
**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

---

### critical: app/Modules/Crm/Controllers/KisiController.php:248

**Pattern:** `route('crm.`
**Mesaj:** crm.* routes yasak - admin.* kullanılmalı

---

