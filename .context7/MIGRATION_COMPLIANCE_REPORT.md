# Migration Dosyaları Context7 Uyumluluk Raporu

**Tarih:** 2025-11-09  
**Durum:** ⚠️ İHLALLER TESPİT EDİLDİ  
**Toplam İhlal:** 3 kategori

---

## 🚨 TESPİT EDİLEN İHLALLER

### 1. ❌ `order` Kolonu Kullanımı (21 dosya)

**Kural:** `order` → `display_order` olmalı

**İhlal Eden Dosyalar:**
- `2025_11_05_133340_create_dashboard_widgets_table.php` (satır 26)
- `2025_11_05_000001_create_feature_assignments_table.php` (satır 30)
- `2025_11_03_093414_create_photos_table.php` (satır 21)
- `2025_11_02_000001_create_polymorphic_features_system.php` (satır 22, 61, 89)
- `2025_10_29_170932_create_alt_kategori_yayin_tipi_table.php` (satır 43)
- `2025_10_25_160239_create_kategori_yayin_tipi_field_dependencies_table.php` (satır 35)
- `2025_10_24_210000_create_kategori_yayin_tipi_field_dependencies_table.php` (satır 60)
- `2025_10_23_121215_create_site_ozellikleri_table.php` (satır 20)
- `2025_10_15_172758_create_features_table.php` (satır 23)
- `2025_10_15_170751_create_etiketler_table.php` (satır 18)
- `2025_10_15_160340_create_feature_categories_table.php` (satır 20)
- `2025_10_10_175050_create_ozellikler_table.php` (satır 24)
- `2025_10_10_174859_create_blog_categories_and_tags_tables.php` (satır 19)
- `2025_10_10_174808_create_ilan_kategori_yayin_tipleri_table.php` (satır 20)
- `2025_10_10_160010_create_ozellik_kategorileri_table.php` (satır 18)
- `2025_10_10_073503_create_ilan_kategorileri_table.php` (satır 18)

**✅ Doğru Kullanım (Örnek):**
- `2025_10_27_085026_create_ilan_etiketler_table.php` → `display_order` ✅
- `2025_10_26_160410_add_applies_to_to_features_table.php` → `display_order` ✅
- `2025_10_26_115934_add_applies_to_to_feature_categories_table.php` → `display_order` ✅

**Öneri:** Bu dosyalarda `order` → `display_order` olarak değiştirilmeli.

---

### 2. ⚠️ Eski Tablo İsimleri (2 dosya)

**Kural:** `musteri_etiketler` → `etiketler`, `sehirler` → `iller`

**İhlal Eden Dosyalar:**
- `2025_11_09_063338_remove_old_status_columns_from_tables.php`:
  - Satır 43: `'musteri_etiketler' => 'aktif'` → `'etiketler' => 'aktif'` olmalı
  - Satır 49: `'sehirler' => 'aktif'` → `'iller' => 'aktif'` olmalı

**Not:** Bu migration dosyası eski kolonları temizlemek için kullanılıyor. Tablo isimleri güncellenmeli.

---

### 3. ❌ `enabled` Kolonu Rollback (2 dosya)

**Kural:** `enabled` kolonu FORBIDDEN - Rollback'te bile eklenmemeli

**İhlal Eden Dosyalar:**
- `2025_11_06_000003_remove_enabled_from_features_tables.php`:
  - Satır 69, 81: Rollback kısmında `enabled` kolonu geri ekleniyor ❌
  
- `2025_11_06_230000_remove_enabled_field_complete.php`:
  - Satır 56, 63: `after('order')` kullanımı var (order → display_order olmalı)

**Öneri:** Rollback kısmından `enabled` kolonu ekleme kodları kaldırılmalı veya yorum satırına alınmalı.

---

## ✅ UYUMLU DOSYALAR

Aşağıdaki migration dosyaları Context7 standartlarına uygun:

1. ✅ `2025_11_09_065813_add_missing_danisman_fields_to_users_table.php`
2. ✅ `2025_11_09_062448_add_status_column_to_takim_uyeleri_table.php`
3. ✅ `2025_11_07_110659_create_danisman_yorumlari_table.php` (kisi_id kullanıyor ✅)
4. ✅ `2025_11_07_110654_add_danisman_profile_fields_to_users_table.php`
5. ✅ `2025_11_06_230200_add_kisi_tipi_field.php` (kisi_tipi kullanıyor ✅)
6. ✅ `2025_11_06_230100_rename_musteri_tables_to_kisi.php` (musteri → kisi ✅)
7. ✅ `2025_10_27_085026_create_ilan_etiketler_table.php` (display_order ✅)

---

## 📊 İSTATİSTİKLER

- **Toplam Migration Dosyası:** ~80+
- **İhlal Eden Dosya:** 19
- **Uyumluluk Oranı:** ~76%
- **Kritik İhlal:** 3 kategori

---

## 🔧 ÖNERİLEN DÜZELTMELER

### Öncelik 1: `order` → `display_order` (Kritik)

**Etkilenen Tablolar:**
- `dashboard_widgets`
- `feature_assignments`
- `photos`
- `features` (zaten düzeltilmiş migration var)
- `feature_categories` (zaten düzeltilmiş migration var)
- `etiketler`
- `ozellikler`
- `ilan_kategorileri`
- `ilan_kategori_yayin_tipleri`
- `blog_categories`
- `blog_tags`
- `ozellik_kategorileri`
- `site_ozellikleri`
- `kategori_yayin_tipi_field_dependencies`
- `alt_kategori_yayin_tipi`

**Çözüm:** Yeni migration dosyası oluşturarak `order` → `display_order` rename işlemi yapılmalı.

### Öncelik 2: Tablo İsimleri (Orta)

**Düzeltme:**
```php
// 2025_11_09_063338_remove_old_status_columns_from_tables.php
'musteri_etiketler' => 'aktif',  // ❌
'etiketler' => 'aktif',          // ✅

'sehirler' => 'aktif',           // ❌
'iller' => 'aktif',              // ✅
```

### Öncelik 3: `enabled` Rollback (Düşük)

**Düzeltme:** Rollback kısmından `enabled` kolonu ekleme kodları kaldırılmalı veya yorum satırına alınmalı.

---

## 📚 REFERANSLAR

- `.context7/ENABLED_FIELD_FORBIDDEN.md`
- `.context7/PERMANENT_STANDARDS.md`
- `.context7/authority.json`

---

**Son Güncelleme:** 2025-11-09  
**Durum:** ⚠️ İHLALLER TESPİT EDİLDİ - DÜZELTME GEREKLİ

