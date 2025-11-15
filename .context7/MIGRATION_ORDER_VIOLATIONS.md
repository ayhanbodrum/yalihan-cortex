# Kalan İhlaller - Detaylı Dosya Listesi

**Tarih:** 2025-11-09  
**Kategori:** `order` → `display_order` İhlalleri

---

## 📋 İHLAL EDEN DOSYALAR (19 dosya)

### 1. `2025_11_05_133340_create_dashboard_widgets_table.php`
- **Satır:** 26, 33
- **İhlal:** `$table->integer('order')->default(0);`
- **Tablo:** `dashboard_widgets`
- **Durum:** ❌ Düzeltilmeli

### 2. `2025_11_05_000001_create_feature_assignments_table.php`
- **Satır:** 30
- **İhlal:** `$table->integer('order')->default(0);`
- **Tablo:** `feature_assignments`
- **Durum:** ❌ Düzeltilmeli

### 3. `2025_11_03_093414_create_photos_table.php`
- **Satır:** 21, 33
- **İhlal:** `$table->integer('order')->default(0);` ve `$table->index('order');`
- **Tablo:** `photos`
- **Durum:** ❌ Düzeltilmeli

### 4. `2025_11_02_000001_create_polymorphic_features_system.php`
- **Satır:** 22, 61, 89
- **İhlal:** `$table->integer('order')->default(0);` (3 yerde)
- **Tablolar:** `feature_groups`, `feature_group_items`, `feature_assignments`
- **Durum:** ❌ Düzeltilmeli

### 5. `2025_10_29_170932_create_alt_kategori_yayin_tipi_table.php`
- **Satır:** 43
- **İhlal:** `$table->integer('order')->default(0);`
- **Tablo:** `alt_kategori_yayin_tipi`
- **Durum:** ❌ Düzeltilmeli

### 6. `2025_10_28_071725_fix_yazlik_category_hierarchy.php`
- **Satır:** 43-46, 65
- **İhlal:** `'order' => 1` (data array'lerinde)
- **Tablo:** `ilan_kategorileri` (data seeder)
- **Durum:** ❌ Düzeltilmeli

### 7. `2025_10_27_112301_fix_yazlik_kiralama_category_structure.php`
- **Satır:** 41, 56, 72, 88, 104, 120
- **İhlal:** `'order' => X` (data array'lerinde)
- **Tablo:** `ilan_kategorileri` (data seeder)
- **Durum:** ❌ Düzeltilmeli

### 8. `2025_10_25_160239_create_kategori_yayin_tipi_field_dependencies_table.php`
- **Satır:** 35
- **İhlal:** `$table->integer('order')->default(0)->comment('Sıralama');`
- **Tablo:** `kategori_yayin_tipi_field_dependencies`
- **Durum:** ❌ Düzeltilmeli

### 9. `2025_10_24_210000_create_kategori_yayin_tipi_field_dependencies_table.php`
- **Satır:** 60, 93
- **İhlal:** `$table->integer('order')->default(0);` ve `$table->index(['field_category', 'order']);`
- **Tablo:** `kategori_yayin_tipi_field_dependencies`
- **Durum:** ❌ Düzeltilmeli

### 10. `2025_10_23_121215_create_site_ozellikleri_table.php`
- **Satır:** 20, 27
- **İhlal:** `$table->integer('order')->default(0);` ve `$table->index('order');`
- **Tablo:** `site_ozellikleri`
- **Durum:** ❌ Düzeltilmeli

### 11. `2025_10_19_224521_add_missing_indexes_to_existing_tables.php`
- **Satır:** 49-50, 78-79
- **İhlal:** `$table->index('order', 'idx_ilan_kategorileri_order');` ve `$table->index('order', 'idx_ozellikler_order');`
- **Tablolar:** `ilan_kategorileri`, `ozellikler`
- **Durum:** ❌ Düzeltilmeli (index isimleri)

### 12. `2025_10_15_172758_create_features_table.php`
- **Satır:** 23, 29
- **İhlal:** `$table->integer('order')->default(0);` ve `$table->index(['status', 'order']);`
- **Tablo:** `features`
- **Durum:** ❌ Düzeltilmeli (Not: `display_order` migration'ı var ama eski migration'da hala `order` var)

### 13. `2025_10_15_170751_create_etiketler_table.php`
- **Satır:** 18, 22
- **İhlal:** `$table->integer('order')->default(0);` ve `$table->index(['status', 'order']);`
- **Tablo:** `etiketler`
- **Durum:** ❌ Düzeltilmeli

### 14. `2025_10_15_160340_create_feature_categories_table.php`
- **Satır:** 20, 29
- **İhlal:** `$table->integer('order')->default(0);` ve `$table->index(['status', 'order']);`
- **Tablo:** `feature_categories`
- **Durum:** ❌ Düzeltilmeli (Not: `display_order` migration'ı var ama eski migration'da hala `order` var)

### 15. `2025_10_10_175050_create_ozellikler_table.php`
- **Satır:** 24, 34
- **İhlal:** `$table->integer('order')->default(0);` ve `$table->index('order');`
- **Tablo:** `ozellikler`
- **Durum:** ❌ Düzeltilmeli

### 16. `2025_10_10_174859_create_blog_categories_and_tags_tables.php`
- **Satır:** 19
- **İhlal:** `$table->integer('order')->default(0);`
- **Tablo:** `blog_categories`
- **Durum:** ❌ Düzeltilmeli

### 17. `2025_10_10_174808_create_ilan_kategori_yayin_tipleri_table.php`
- **Satır:** 20
- **İhlal:** `$table->integer('order')->default(0);`
- **Tablo:** `ilan_kategori_yayin_tipleri`
- **Durum:** ❌ Düzeltilmeli

### 18. `2025_10_10_160010_create_ozellik_kategorileri_table.php`
- **Satır:** 18, 24
- **İhlal:** `$table->integer('order')->default(0);` ve `$table->index('order');`
- **Tablo:** `ozellik_kategorileri`
- **Durum:** ❌ Düzeltilmeli

### 19. `2025_10_10_073503_create_ilan_kategorileri_table.php`
- **Satır:** 18, 24
- **İhlal:** `$table->integer('order')->default(0);` ve `$table->index('order');`
- **Tablo:** `ilan_kategorileri`
- **Durum:** ❌ Düzeltilmeli

---

## ✅ UYUMLU DOSYALAR (Örnek)

- ✅ `2025_10_27_085026_create_ilan_etiketler_table.php` → `display_order` kullanıyor
- ✅ `2025_10_26_160410_add_applies_to_to_features_table.php` → `display_order` kullanıyor
- ✅ `2025_10_26_115934_add_applies_to_to_feature_categories_table.php` → `display_order` kullanıyor

---

## 📊 ÖZET

- **Toplam İhlal:** 19 dosya
- **Etkilenen Tablo:** 15+ tablo
- **Öncelik:** Orta (Mevcut migration'lar çalıştırılmış olabilir)
- **Çözüm:** Yeni migration ile `order` → `display_order` rename işlemi

---

**Son Güncelleme:** 2025-11-09

