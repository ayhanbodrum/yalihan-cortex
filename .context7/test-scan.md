# Context7 Compliance Report

**Tarih:** 2025-11-11 13:31:13
**Durum:** ⚠️ TARAMA TAMAMLANDI

---

## 📊 Özet

- **Toplam İhlal:** 30
- **Critical:** 30
- **High:** 0
- **Medium:** 0
- **Low:** 0

---

## İhlaller

### critical: app/Models/CategoryField.php:20

**Pattern:** `'order'`
**Mesaj:** order → display_order kullanılmalı

---

### critical: database/migrations/2025_10_10_174859_create_blog_categories_and_tags_tables.php:19

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

### critical: app/Http/Controllers/Admin/SiteController.php:44

**Pattern:** `sehir`
**Mesaj:** sehir → il kullanılmalı

---

### critical: app/Http/Controllers/Admin/SiteController.php:54

**Pattern:** `sehir`
**Mesaj:** sehir → il kullanılmalı

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

### critical: resources/views/frontend/dynamic-form/index.blade.php:1

**Pattern:** `layouts.app`
**Mesaj:** layouts.app yasak - admin.layouts.neo kullanılmalı

---

### critical: app/Modules/Crm/Controllers/EtiketController.php:50

**Pattern:** `route('crm.`
**Mesaj:** crm.* routes yasak - admin.* kullanılmalı

---

### critical: app/Modules/Crm/Controllers/EtiketController.php:81

**Pattern:** `route('crm.`
**Mesaj:** crm.* routes yasak - admin.* kullanılmalı

---

### critical: app/Modules/Crm/Controllers/EtiketController.php:91

**Pattern:** `route('crm.`
**Mesaj:** crm.* routes yasak - admin.* kullanılmalı

---

### critical: app/Modules/Crm/Controllers/EtiketController.php:95

**Pattern:** `route('crm.`
**Mesaj:** crm.* routes yasak - admin.* kullanılmalı

---

### critical: app/Modules/Crm/Controllers/AktiviteController.php:91

**Pattern:** `route('crm.`
**Mesaj:** crm.* routes yasak - admin.* kullanılmalı

---

### critical: app/Modules/Crm/Controllers/AktiviteController.php:95

**Pattern:** `route('crm.`
**Mesaj:** crm.* routes yasak - admin.* kullanılmalı

---

### critical: app/Modules/Crm/Controllers/AktiviteController.php:144

**Pattern:** `route('crm.`
**Mesaj:** crm.* routes yasak - admin.* kullanılmalı

---

### critical: app/Modules/Crm/Controllers/AktiviteController.php:155

**Pattern:** `route('crm.`
**Mesaj:** crm.* routes yasak - admin.* kullanılmalı

---

### critical: app/Modules/Crm/Controllers/KisiController.php:114

**Pattern:** `route('crm.`
**Mesaj:** crm.* routes yasak - admin.* kullanılmalı

---

### critical: app/Modules/Crm/Controllers/KisiController.php:227

**Pattern:** `route('crm.`
**Mesaj:** crm.* routes yasak - admin.* kullanılmalı

---

