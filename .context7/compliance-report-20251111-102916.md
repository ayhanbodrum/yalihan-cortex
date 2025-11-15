# Context7 Compliance Report

**Tarih:** 2025-11-11 10:29:17
**Durum:** ⚠️ TARAMA TAMAMLANDI

---

## 📊 Özet

- **Toplam İhlal:** 1100
- **Critical:** 807
- **High:** 293
- **Medium:** 0
- **Low:** 0

---

## critical Violations

### app/Models/CategoryField.php:20

**Pattern:** `'order',`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Models/OzellikKategori.php:63

**Pattern:** `* Context7: Database column 'order', not 'sira'`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Models/OzellikKategori.php:188

**Pattern:** `* Context7: Database column is 'order'`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Models/OzellikKategori.php:256

**Pattern:** `* Accessor for 'order' attribute (backward compatibility)`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Models/OzellikKategori.php:265

**Pattern:** `* Mutator for 'order' attribute (backward compatibility)`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Models/IlanKategori.php:79

**Pattern:** `* Accessor for 'order' attribute (backward compatibility)`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Models/IlanKategori.php:82

**Pattern:** `* ⚠️ TEMPORARILY DISABLED: This accessor might cause Laravel to try to select 'order' column`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Models/IlanKategori.php:93

**Pattern:** `* Mutator for 'order' attribute (backward compatibility)`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Models/IlanKategori.php:96

**Pattern:** `* ⚠️ TEMPORARILY DISABLED: This mutator might cause Laravel to try to select 'order' column`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Models/IlanKategori.php:118

**Pattern:** `* CRITICAL: Eager loading sırasında Laravel'in 'order' kolonunu seçmesini önlemek için`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Models/IlanKategori.php:130

**Pattern:** `* CRITICAL: Eager loading sırasında Laravel'in 'order' kolonunu seçmesini önlemek için`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Models/IlanKategori.php:143

**Pattern:** `* CRITICAL: Eager loading sırasında Laravel'in 'order' kolonunu seçmesini önlemek için`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Models/IlanKategoriYayinTipi.php:97

**Pattern:** `* Accessor for 'order' attribute (backward compatibility)`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Models/IlanKategoriYayinTipi.php:106

**Pattern:** `* Mutator for 'order' attribute (backward compatibility)`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Http/Requests/Admin/IlanKategoriRequest.php:42

**Pattern:** `'order' => 'nullable|integer|min:0',`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Http/Controllers/Admin/PropertyTypeManagerController.php:794

**Pattern:** `'order' => 'nullable|integer|min:0',`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Http/Controllers/Admin/PropertyTypeManagerController.php:864

**Pattern:** `'order' => 'nullable|integer|min:0',`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Http/Controllers/Admin/PropertyTypeManagerController.php:943

**Pattern:** `'order' => 0,`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Http/Controllers/Admin/PropertyTypeManagerController.php:1182

**Pattern:** `'order' => 'nullable|integer|min:0',`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Http/Controllers/Admin/PropertyTypeManagerController.php:1193

**Pattern:** `'order' => $request->input('order', 0),`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Http/Controllers/Admin/PropertyTypeManagerController.php:1356

**Pattern:** `'order' => 'nullable|integer|min:0',`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Http/Controllers/Admin/PropertyTypeManagerController.php:1366

**Pattern:** `'order',`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Http/Controllers/Admin/PropertyTypeManagerController.php:1372

**Pattern:** `'updates' => $request->only(['is_required', 'is_visible', 'order', 'group_name'])`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Http/Controllers/Admin/PropertyTypeManagerController.php:1378

**Pattern:** `'data' => $assignment->only(['id', 'is_required', 'is_visible', 'order', 'group_name'])`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Http/Controllers/Admin/OzellikKategoriController.php:59

**Pattern:** `'order' => ['nullable', 'integer'],`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Http/Controllers/Admin/OzellikKategoriController.php:65

**Pattern:** `if (!array_key_exists('order', $data) || is_null($data['order'])) {`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Http/Controllers/Admin/OzellikKategoriController.php:69

**Pattern:** `unset($data['order']);`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Http/Controllers/Admin/OzellikKategoriController.php:110

**Pattern:** `'order' => ['nullable', 'integer'],`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Http/Controllers/Admin/OzellikKategoriController.php:158

**Pattern:** `'order' => ['required', 'array'],`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Http/Controllers/Admin/OzellikKategoriController.php:163

**Pattern:** `foreach ($data['order'] as $item) {`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Http/Controllers/Admin/OzellikKategoriController.php:194

**Pattern:** `'order' => ['sometimes', 'integer'],`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Http/Controllers/Admin/FeatureCategoryController.php:47

**Pattern:** `'order' => 'nullable|integer|min:0',`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Http/Controllers/Admin/FeatureCategoryController.php:114

**Pattern:** `'order' => 'nullable|integer|min:0',`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Http/Controllers/Admin/OzellikController.php:101

**Pattern:** `'order' => 'nullable|integer',`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Http/Controllers/Admin/OzellikController.php:147

**Pattern:** `'order' => 'nullable|integer',`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Http/Controllers/Admin/PhotoController.php:143

**Pattern:** `'order' => $index,`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Http/Controllers/Admin/PhotoController.php:278

**Pattern:** `'order' => $request->order ?? $photo->order,`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Http/Controllers/Admin/PhotoController.php:285

**Pattern:** `'order' => $photo->order,`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Http/Controllers/Admin/YayinTipiYoneticisiController.php:103

**Pattern:** `'order' => $yayinTipi->order,`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Http/Controllers/Admin/YayinTipiYoneticisiController.php:159

**Pattern:** `'order' => $yayinTipi->order,`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Http/Controllers/Api/PhotoController.php:28

**Pattern:** `'order' => 'nullable|integer',`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Http/Controllers/Api/PhotoController.php:63

**Pattern:** `'order' => $photo->sira,`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Http/Controllers/Api/PhotoController.php:82

**Pattern:** `'order' => $photo->sira,`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Http/Controllers/Api/PhotoController.php:101

**Pattern:** `'order' => 'nullable|integer',`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Modules/Emlak/Models/FeatureCategory.php:63

**Pattern:** `'order' => 'integer',`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Modules/Crm/Models/Etiket.php:40

**Pattern:** `'order',`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Services/AICategoryManager.php:124

**Pattern:** `'order' => $category->order,`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_10_174859_create_blog_categories_and_tags_tables.php:19

**Pattern:** `$table->integer('order')->default(0);`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_19_224521_add_missing_indexes_to_existing_tables.php:50

**Pattern:** `$table->index('order', 'idx_ilan_kategorileri_order');`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_19_224521_add_missing_indexes_to_existing_tables.php:79

**Pattern:** `$table->index('order', 'idx_ozellikler_order');`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_10_175050_create_ozellikler_table.php:24

**Pattern:** `$table->integer('order')->default(0);`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_10_175050_create_ozellikler_table.php:34

**Pattern:** `$table->index('order');`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_15_172758_create_features_table.php:23

**Pattern:** `$table->integer('order')->default(0);`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_15_172758_create_features_table.php:29

**Pattern:** `$table->index(['status', 'order']);`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_05_000001_create_feature_assignments_table.php:30

**Pattern:** `$table->integer('order')->default(0);`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_28_071725_fix_yazlik_category_hierarchy.php:46

**Pattern:** `['name' => 'Günlük Kiralama', 'slug' => 'gunluk-kiralama', 'order' => 1],`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_28_071725_fix_yazlik_category_hierarchy.php:47

**Pattern:** `['name' => 'Haftalık Kiralama', 'slug' => 'haftalik-kiralama', 'order' => 2],`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_28_071725_fix_yazlik_category_hierarchy.php:48

**Pattern:** `['name' => 'Aylık Kiralama', 'slug' => 'aylik-kiralama', 'order' => 3],`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_28_071725_fix_yazlik_category_hierarchy.php:49

**Pattern:** `['name' => 'Sezonluk Kiralama', 'slug' => 'sezonluk-kiralama', 'order' => 4],`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_28_071725_fix_yazlik_category_hierarchy.php:68

**Pattern:** `'order' => $yayin['order'],`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_15_160340_create_feature_categories_table.php:20

**Pattern:** `$table->integer('order')->default(0);`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_15_160340_create_feature_categories_table.php:29

**Pattern:** `$table->index(['status', 'order']);`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_29_170932_create_alt_kategori_yayin_tipi_table.php:43

**Pattern:** `$table->integer('order')->default(0);`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_10_073503_create_ilan_kategorileri_table.php:18

**Pattern:** `$table->integer('order')->default(0);`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_10_073503_create_ilan_kategorileri_table.php:24

**Pattern:** `$table->index('order');`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_24_210000_create_kategori_yayin_tipi_field_dependencies_table.php:60

**Pattern:** `$table->integer('order')->default(0)->comment('Sıralama');`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_24_210000_create_kategori_yayin_tipi_field_dependencies_table.php:93

**Pattern:** `$table->index(['field_category', 'order'], 'idx_category_order');`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_27_112301_fix_yazlik_kiralama_category_structure.php:41

**Pattern:** `'order' => 3,`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_27_112301_fix_yazlik_kiralama_category_structure.php:56

**Pattern:** `'order' => 4,`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_27_112301_fix_yazlik_kiralama_category_structure.php:72

**Pattern:** `'order' => 1,`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_27_112301_fix_yazlik_kiralama_category_structure.php:88

**Pattern:** `'order' => 2,`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_27_112301_fix_yazlik_kiralama_category_structure.php:104

**Pattern:** `'order' => 3,`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_27_112301_fix_yazlik_kiralama_category_structure.php:120

**Pattern:** `'order' => 4,`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_15_170751_create_etiketler_table.php:18

**Pattern:** `$table->integer('order')->default(0);`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_15_170751_create_etiketler_table.php:22

**Pattern:** `$table->index(['status', 'order']);`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_05_133340_create_dashboard_widgets_table.php:26

**Pattern:** `$table->integer('order')->default(0);`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_05_133340_create_dashboard_widgets_table.php:33

**Pattern:** `$table->index('order');`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_10_174808_create_ilan_kategori_yayin_tipleri_table.php:20

**Pattern:** `$table->integer('order')->default(0);`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_02_000001_create_polymorphic_features_system.php:23

**Pattern:** `$table->integer('order')->default(0);`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_02_000001_create_polymorphic_features_system.php:64

**Pattern:** `$table->integer('order')->default(0);`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_02_000001_create_polymorphic_features_system.php:94

**Pattern:** `$table->integer('order')->default(0);`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_10_160010_create_ozellik_kategorileri_table.php:18

**Pattern:** `$table->integer('order')->default(0);`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_10_160010_create_ozellik_kategorileri_table.php:24

**Pattern:** `$table->index('order');`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_09_070721_rename_order_to_display_order_in_tables.php:33

**Pattern:** `$hasOrder = Schema::hasColumn($tableName, 'order');`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_09_070721_rename_order_to_display_order_in_tables.php:38

**Pattern:** `$this->dropIndexesForColumn($tableName, 'order');`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_09_070721_rename_order_to_display_order_in_tables.php:41

**Pattern:** `$columnInfo = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'order'");`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_09_070721_rename_order_to_display_order_in_tables.php:69

**Pattern:** `$this->dropIndexesForColumn($tableName, 'order');`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_09_070721_rename_order_to_display_order_in_tables.php:72

**Pattern:** `$table->dropColumn('order');`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_09_070721_rename_order_to_display_order_in_tables.php:97

**Pattern:** `$hasOrder = Schema::hasColumn($tableName, 'order');`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_09_070721_rename_order_to_display_order_in_tables.php:121

**Pattern:** `$table->index('order', "idx_{$tableName}_order");`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_09_122119_rename_order_to_display_order_in_alt_kategori_yayin_tipi_table.php:27

**Pattern:** `$hasOrder = Schema::hasColumn($tableName, 'order');`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_09_122119_rename_order_to_display_order_in_alt_kategori_yayin_tipi_table.php:32

**Pattern:** `$this->dropIndexesForColumn($tableName, 'order');`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_09_122119_rename_order_to_display_order_in_alt_kategori_yayin_tipi_table.php:35

**Pattern:** `$columnInfo = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'order'");`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_09_122119_rename_order_to_display_order_in_alt_kategori_yayin_tipi_table.php:63

**Pattern:** `$this->dropIndexesForColumn($tableName, 'order');`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_09_122119_rename_order_to_display_order_in_alt_kategori_yayin_tipi_table.php:66

**Pattern:** `$table->dropColumn('order');`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_09_122119_rename_order_to_display_order_in_alt_kategori_yayin_tipi_table.php:85

**Pattern:** `$hasOrder = Schema::hasColumn($tableName, 'order');`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_09_122119_rename_order_to_display_order_in_alt_kategori_yayin_tipi_table.php:109

**Pattern:** `$table->index('order', "idx_{$tableName}_order");`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_03_093414_create_photos_table.php:33

**Pattern:** `$table->index('order');`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_25_160239_create_kategori_yayin_tipi_field_dependencies_table.php:39

**Pattern:** `$table->integer('order')->default(0)->comment('Sıralama');`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_23_121215_create_site_ozellikleri_table.php:20

**Pattern:** `$table->integer('order')->default(0)->comment('Sıralama');`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_23_121215_create_site_ozellikleri_table.php:27

**Pattern:** `$table->index('order', 'idx_site_ozellikleri_order');`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### resources/views/admin/ilan-kategorileri/edit.blade.php:74

**Pattern:** `<label for="order" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sıra</label>`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### resources/views/admin/ilan-kategorileri/edit.blade.php:76

**Pattern:** `@error('order')`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### resources/views/admin/ilan-kategorileri/create.blade.php:69

**Pattern:** `<label for="order" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sıra</label>`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### resources/views/admin/ilan-kategorileri/create.blade.php:70

**Pattern:** `<input type="number" id="order" name="order" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-black dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200" value="{{ old('order', 0) }}" min="0">`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### resources/views/admin/ilan-kategorileri/create.blade.php:71

**Pattern:** `@error('order')`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### resources/views/admin/ilan-kategorileri/partials/advanced-filters.blade.php:91

**Pattern:** `<option value="order">🎯 Manuel Sıra</option>`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### resources/views/admin/ayarlar/edit.blade.php:83

**Pattern:** `<label for="order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sıra</label>`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### resources/views/admin/ayarlar/edit.blade.php:84

**Pattern:** `<input id="order" name="order" type="number" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-black dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 @error('order') border-red-500 @enderror"`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### resources/views/admin/ayarlar/edit.blade.php:85

**Pattern:** `value="{{ old('order', $ayar->order ?? 0) }}" min="0" placeholder="0">`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### resources/views/admin/ayarlar/edit.blade.php:86

**Pattern:** `@error('order')`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### resources/views/admin/ilanlar/components/photo-upload-manager.blade.php:248

**Pattern:** `formData.append('order', tempPhoto.order);`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### resources/views/admin/ilanlar/partials/yazlik-features.blade.php:49

**Pattern:** `'Temel Donanımlar' => $yazlikFeatures->whereBetween('order', [1, 10]),`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### resources/views/admin/ilanlar/partials/yazlik-features.blade.php:50

**Pattern:** `'Manzara & Konum' => $yazlikFeatures->whereBetween('order', [11, 20]),`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### resources/views/admin/ilanlar/partials/yazlik-features.blade.php:51

**Pattern:** `'Dış Mekan' => $yazlikFeatures->whereBetween('order', [21, 30]),`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### resources/views/admin/ilanlar/partials/yazlik-features.blade.php:52

**Pattern:** `'Güvenlik & Ekstralar' => $yazlikFeatures->whereBetween('order', [31, 40]),`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### resources/views/admin/ozellikler/kategoriler/edit.blade.php:53

**Pattern:** `<x-admin.input name="order" type="number" label="Sıra" :value="$kategori->order" />`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### resources/views/admin/ozellikler/kategoriler/create.blade.php:100

**Pattern:** `<label for="order" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### resources/views/admin/ozellikler/kategoriler/create.blade.php:103

**Pattern:** `<input type="number" id="order" name="order" value="{{ old('order', 0) }}"`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### resources/views/admin/ozellikler/kategoriler/create.blade.php:108

**Pattern:** `@error('order')`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### resources/views/admin/ozellikler/edit.blade.php:47

**Pattern:** `selectedOrder: '{{ old('order', $ozellik->order) }}',`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### resources/views/admin/ozellikler/edit.blade.php:105

**Pattern:** `<label for="order" class="block text-sm font-medium text-gray-700 mb-2">`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### resources/views/admin/ozellikler/edit.blade.php:108

**Pattern:** `<input type="number" id="order" name="order" min="0"`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### resources/views/admin/ozellikler/edit.blade.php:111

**Pattern:** `placeholder="Otomatik" value="{{ old('order', $ozellik->order) }}">`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### resources/views/admin/ozellikler/edit.blade.php:113

**Pattern:** `@error('order')`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### resources/views/admin/ozellikler/create.blade.php:80

**Pattern:** `<label for="order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sıra</label>`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### resources/views/admin/ozellikler/create.blade.php:81

**Pattern:** `<input type="number" id="order" name="order" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-black dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200"`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### resources/views/admin/ozellikler/create.blade.php:82

**Pattern:** `value="{{ old('order', 0) }}" min="0">`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### resources/views/admin/ozellikler/create.blade.php:83

**Pattern:** `@error('order')`

**Mesaj:** order → display_order kullanılmalı

**Replacement:** `display_order`

---

### app/Console/Commands/StandardCheck.php:225

**Pattern:** `$forbiddenFields = ['durum', 'aktif', 'sehir', 'sehir_id', 'ad_soyad'];`

**Mesaj:** durum → status kullanılmalı

**Replacement:** `status`

---

### app/Console/Commands/TestSpriteAutoLearn.php:60

**Pattern:** `// Yasaklı kurallar: ❌ YASAK "durum"`

**Mesaj:** durum → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_10_27_090709_create_yazlik_doluluk_durumlari_table.php:15

**Pattern:** `$table->enum('durum', ['musait', 'rezerve', 'bloke', 'bakim', 'temizlik', 'kapali'])->default('musait');`

**Mesaj:** durum → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_10_27_090709_create_yazlik_doluluk_durumlari_table.php:23

**Pattern:** `$table->index(['ilan_id', 'durum']);`

**Mesaj:** durum → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_10_27_090709_create_yazlik_doluluk_durumlari_table.php:24

**Pattern:** `$table->index(['ilan_id', 'tarih', 'durum']);`

**Mesaj:** durum → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_10_27_090709_create_yazlik_doluluk_durumlari_table.php:25

**Pattern:** `$table->index(['tarih', 'durum']);`

**Mesaj:** durum → status kullanılmalı

**Replacement:** `status`

---

### app/Http/Controllers/Frontend/DanismanController.php:84

**Pattern:** `'aktif' => User::whereHas('roles', function($q) {`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### app/Http/Controllers/Api/BulkOperationsController.php:108

**Pattern:** `$action = $request->enabled ? 'aktif' : 'pasif';`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### app/Modules/TakimYonetimi/Models/TakimUyesi.php:49

**Pattern:** `return ['aktif', 'pasif', 'izinli', 'tatilde'];`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### app/Modules/TakimYonetimi/Models/TakimUyesi.php:120

**Pattern:** `'aktif' => '<span class="badge bg-success">Aktif</span>',`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### app/Console/Commands/StandardCheck.php:225

**Pattern:** `$forbiddenFields = ['durum', 'aktif', 'sehir', 'sehir_id', 'ad_soyad'];`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_10_25_080245_create_ozellik_alt_kategorileri_table.php:25

**Pattern:** `$table->boolean('aktif')->default(true);`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_10_25_080252_create_kategori_ozellik_matrix_table.php:20

**Pattern:** `$table->boolean('aktif')->default(true);`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:21

**Pattern:** `'eslesmeler' => 'aktif', // Not: eslesmeler'de durum ENUM var, aktif TINYINT var`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:22

**Pattern:** `'etiketler' => 'aktif',`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:31

**Pattern:** `'arsa_detaylari' => 'aktif',`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:32

**Pattern:** `'arsa_ozellikleri' => 'aktif',`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:37

**Pattern:** `'ilan_dinamik_ozellikler' => 'aktif',`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:38

**Pattern:** `'ilan_takvim_sezonlar' => 'aktif',`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:39

**Pattern:** `'ilceler' => 'aktif',`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:40

**Pattern:** `'isyeri_ozellikleri' => 'aktif',`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:41

**Pattern:** `'konut_ozellikleri' => 'aktif',`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:43

**Pattern:** `'etiketler' => 'aktif', // Context7: musteri_etiketler → etiketler`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:45

**Pattern:** `'ozellik_kategorileri' => 'aktif',`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:46

**Pattern:** `'para_birimleri' => 'aktif',`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:49

**Pattern:** `'iller' => 'aktif', // Context7: sehirler → iller`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:52

**Pattern:** `'turistik_ozellikleri' => 'aktif',`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:53

**Pattern:** `'yazlik_fiyatlandirma' => 'aktif',`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:120

**Pattern:** `'eslesmeler' => ['aktif' => 'tinyint'],`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:121

**Pattern:** `'etiketler' => ['aktif' => 'tinyint'],`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_10_25_080242_create_ozellik_kategorileri_table.php:25

**Pattern:** `$table->boolean('aktif')->default(true);`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_10_25_092335_create_konut_ozellik_hibrit_siralama_table.php:24

**Pattern:** `$table->boolean('aktif')->default(true);`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/seeders/OzellikKategorileriSeeder.php:155

**Pattern:** `'aktif' => $ozellik['aktif'],`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/seeders/OzellikKategorileriSeeder.php:220

**Pattern:** `'aktif' => true,`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/seeders/KonutOzellikHibritSiralamaSeeder.php:26

**Pattern:** `'aktif' => true,`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/seeders/KonutOzellikHibritSiralamaSeeder.php:38

**Pattern:** `'aktif' => true,`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/seeders/KonutOzellikHibritSiralamaSeeder.php:50

**Pattern:** `'aktif' => true,`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/seeders/KonutOzellikHibritSiralamaSeeder.php:62

**Pattern:** `'aktif' => true,`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/seeders/KonutOzellikHibritSiralamaSeeder.php:74

**Pattern:** `'aktif' => true,`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/seeders/KonutOzellikHibritSiralamaSeeder.php:86

**Pattern:** `'aktif' => true,`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/seeders/KonutOzellikHibritSiralamaSeeder.php:98

**Pattern:** `'aktif' => true,`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/seeders/KonutOzellikHibritSiralamaSeeder.php:110

**Pattern:** `'aktif' => true,`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/seeders/KonutOzellikHibritSiralamaSeeder.php:122

**Pattern:** `'aktif' => true,`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/seeders/KonutOzellikHibritSiralamaSeeder.php:134

**Pattern:** `'aktif' => true,`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/seeders/KonutOzellikHibritSiralamaSeeder.php:146

**Pattern:** `'aktif' => true,`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/seeders/KonutOzellikHibritSiralamaSeeder.php:158

**Pattern:** `'aktif' => true,`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/seeders/KonutOzellikHibritSiralamaSeeder.php:170

**Pattern:** `'aktif' => true,`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/seeders/KonutOzellikHibritSiralamaSeeder.php:182

**Pattern:** `'aktif' => true,`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/seeders/KonutOzellikHibritSiralamaSeeder.php:194

**Pattern:** `'aktif' => true,`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/seeders/KonutOzellikHibritSiralamaSeeder.php:206

**Pattern:** `'aktif' => true,`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/seeders/KonutOzellikHibritSiralamaSeeder.php:218

**Pattern:** `'aktif' => true,`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/seeders/KonutOzellikHibritSiralamaSeeder.php:230

**Pattern:** `'aktif' => true,`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/seeders/KonutOzellikHibritSiralamaSeeder.php:242

**Pattern:** `'aktif' => true,`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/seeders/KonutOzellikHibritSiralamaSeeder.php:254

**Pattern:** `'aktif' => true,`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/seeders/KonutOzellikHibritSiralamaSeeder.php:266

**Pattern:** `'aktif' => true,`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/seeders/KonutOzellikHibritSiralamaSeeder.php:278

**Pattern:** `'aktif' => true,`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### database/seeders/KonutOzellikHibritSiralamaSeeder.php:290

**Pattern:** `'aktif' => true,`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### resources/views/frontend/danismanlar/index.blade.php:28

**Pattern:** `<div class="text-3xl font-bold text-white mb-2">{{ $stats['aktif'] ?? 0 }}</div>`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### resources/views/admin/ilan-kategorileri/index.blade.php:43

**Pattern:** `<div class="text-xl font-bold text-purple-700 dark:text-purple-300">{{ $istatistikler['aktif'] }}</div>`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### resources/views/admin/danisman/edit.blade.php:501

**Pattern:** `$currentStatus = 'aktif';`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### resources/views/admin/danisman/edit.blade.php:505

**Pattern:** `$currentStatus = 'aktif'; // Default`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### resources/views/admin/ozellikler/index.blade.php:41

**Pattern:** `<div class="text-3xl font-bold">{{ $istatistikler['aktif'] }}</div>`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### resources/views/admin/ozellikler/index-tabs.blade.php:41

**Pattern:** `<div class="text-3xl font-bold">{{ $istatistikler['aktif'] }}</div>`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### resources/views/components/neo/status-badge.blade.php:37

**Pattern:** `'aktif' => [`

**Mesaj:** aktif → status kullanılmalı

**Replacement:** `status`

---

### app/Traits/HasActiveScope.php:13

**Pattern:** `* ⚠️ IMPORTANT: enabled field FORBIDDEN by Context7 (removed 2025-11-06)`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Traits/HasActiveScope.php:25

**Pattern:** `* ❌ FORBIDDEN: enabled field (Context7 rule violation)`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Traits/HasActiveScope.php:53

**Pattern:** `// ❌ REMOVED: enabled field support (Context7 violation)`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Models/Feature.php:149

**Pattern:** `* Scope: Only enabled features`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Models/Feature.php:155

**Pattern:** `// ❌ REMOVED: enabled field check (Context7 violation)`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Models/AICoreSystem.php:26

**Pattern:** `'is_active', // Context7: Tablo yok, enabled kaldırıldı`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Models/AICoreSystem.php:32

**Pattern:** `'is_active' => 'boolean', // Context7: enabled kaldırıldı`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Models/AICoreSystem.php:42

**Pattern:** `return $query->where('is_active', true); // Context7: enabled kaldırıldı`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Models/FeatureCategory.php:54

**Pattern:** `* Get only enabled features`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Models/FeatureCategory.php:57

**Pattern:** `public function enabledFeatures()`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Models/FeatureCategory.php:62

**Pattern:** `// ❌ REMOVED: enabled field check (Context7 violation)`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Models/FeatureCategory.php:71

**Pattern:** `* Scope: Only enabled categories`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Models/FeatureCategory.php:77

**Pattern:** `// ❌ REMOVED: enabled field check (Context7 violation)`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Models/Ilan.php:27

**Pattern:** `* @property bool $enabled`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Http/Middleware/Context7ComplianceMiddleware.php:20

**Pattern:** `// Check if Context7 debugging is enabled`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Http/Middleware/PreventRequestsDuringMaintenance.php:10

**Pattern:** `* The URIs that should be reachable while maintenance mode is enabled.`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Http/Controllers/Admin/PropertyTypeManagerController.php:291

**Pattern:** `// Grupla: field_slug => [yayin_tipi => enabled]`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Http/Controllers/Admin/PropertyTypeManagerController.php:341

**Pattern:** `$fieldDependencies[$dep->field_slug]['yayin_tipleri'][$yayinTipiId] = $dep->enabled;`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Http/Controllers/Admin/PropertyTypeManagerController.php:760

**Pattern:** `->enabled()`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Http/Controllers/Admin/PropertyTypeManagerController.php:983

**Pattern:** `'enabled' => $enabled,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Http/Controllers/Admin/PropertyTypeManagerController.php:991

**Pattern:** `'message' => $enabled ? 'Alan aktif edildi' : 'Alan pasif edildi',`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Http/Controllers/Admin/PropertyTypeManagerController.php:994

**Pattern:** `'enabled' => $enabled`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Http/Controllers/Admin/PropertyTypeManagerController.php:1051

**Pattern:** `'enabled' => 'required|boolean'`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Http/Controllers/Admin/PropertyTypeManagerController.php:1075

**Pattern:** `'field_dependencies.*.enabled' => 'required_with:field_dependencies|boolean',`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Http/Controllers/Admin/PropertyTypeManagerController.php:1078

**Pattern:** `'features.*.enabled' => 'required_with:features|boolean'`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Http/Controllers/Admin/PropertyTypeManagerController.php:1135

**Pattern:** `'enabled' => $data['enabled'],`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Http/Controllers/Admin/OzellikKategoriController.php:34

**Pattern:** `// ❌ REMOVED: enabled field fallback (Context7 violation)`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Http/Controllers/Admin/TakvimController.php:446

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Http/Controllers/Admin/TakvimController.php:457

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Http/Controllers/Admin/TakvimController.php:468

**Pattern:** `'enabled' => false,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Http/Controllers/Admin/AyarlarController.php:285

**Pattern:** `if (in_array($key, ['qrcode_enabled', 'qrcode_show_on_cards', 'qrcode_show_on_detail',`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Http/Controllers/Admin/AyarlarController.php:286

**Pattern:** `'navigation_enabled', 'navigation_show_similar',`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Http/Controllers/Admin/AyarlarController.php:297

**Pattern:** `} elseif (in_array($key, ['qrcode_enabled', 'qrcode_show_on_cards', 'qrcode_show_on_detail',`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Http/Controllers/Admin/AyarlarController.php:298

**Pattern:** `'navigation_enabled', 'navigation_show_similar',`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Http/Controllers/Admin/AyarlarController.php:410

**Pattern:** `'key' => 'ai_enabled',`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Http/Controllers/Api/FeatureController.php:40

**Pattern:** `// ❌ REMOVED: enabled field fallback (Context7 violation)`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Http/Controllers/Api/FeatureController.php:76

**Pattern:** `// ❌ REMOVED: enabled field fallback (Context7 violation)`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Http/Controllers/Api/BulkOperationsController.php:89

**Pattern:** `'enabled' => 'required|boolean'`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Http/Controllers/Api/BulkOperationsController.php:104

**Pattern:** `->update(['enabled' => $request->enabled]);`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Http/Controllers/Api/BulkOperationsController.php:108

**Pattern:** `$action = $request->enabled ? 'aktif' : 'pasif';`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Http/Controllers/Api/BulkOperationsController.php:112

**Pattern:** `'enabled' => $request->enabled,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Modules/Auth/Models/User.php:20

**Pattern:** `* @property bool $enabled`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Modules/TakimYonetimi/Services/Context7AIService.php:1282

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Modules/TakimYonetimi/Services/Context7AIService.php:1287

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Modules/TakimYonetimi/Services/Context7AIService.php:1292

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Modules/TakimYonetimi/Services/Context7AIService.php:1306

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Modules/TakimYonetimi/Services/Context7AIService.php:1312

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Modules/TakimYonetimi/Services/Context7AIService.php:1318

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Modules/TakimYonetimi/Services/Context7AIService.php:1324

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Modules/TakimYonetimi/Services/Context7AIService.php:1372

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Modules/TakimYonetimi/Services/Context7AIService.php:1377

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Modules/TakimYonetimi/Services/Context7AIService.php:1382

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Modules/TakimYonetimi/Services/Context7AIService.php:1387

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Helpers/FormStandards.php:142

**Pattern:** `* SELECT OPTION classes (enabled)`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Services/AdvancedAIPropertyGenerator.php:112

**Pattern:** `'ab_test_enabled' => $options['ab_test']`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Services/FieldRegistryService.php:202

**Pattern:** `'enabled',`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Services/FieldRegistryService.php:204

**Pattern:** `->where('enabled', true);`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Services/QRCodeService.php:38

**Pattern:** `* Check if QR code is enabled`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Services/QRCodeService.php:42

**Pattern:** `return $this->getSetting('qrcode_enabled', true);`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Services/QRCodeService.php:62

**Pattern:** `// Check if QR code is enabled`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Services/AICoreSystem.php:80

**Pattern:** `->where('enabled', true)`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Services/AIService.php:183

**Pattern:** `// AI-enabled field'ları getir`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Services/TKGMService.php:29

**Pattern:** `$this->cacheEnabled = config('services.tkgm.cache_enabled', true);`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Services/NominatimService.php:29

**Pattern:** `$this->cacheEnabled = config('services.nominatim.cache_enabled', true);`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Services/AIPromptManager.php:59

**Pattern:** `->where('enabled', true)`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Services/WikimapiaService.php:38

**Pattern:** `$this->cacheEnabled = $config['cache_enabled'];`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Services/ListingNavigationService.php:30

**Pattern:** `* Check if navigation is enabled`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Services/ListingNavigationService.php:34

**Pattern:** `return $this->getSetting('navigation_enabled', true);`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Services/ListingNavigationService.php:62

**Pattern:** `// Check if navigation is enabled`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Console/Commands/AnalyzePagesComplete.php:456

**Pattern:** `'Ensure CSRF protection is enabled',`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Console/Commands/ComponentMake.php:111

**Pattern:** `'toggle' => '<x-form.toggle name="enabled" label="Aktif" />',`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_10_22_072600_create_yazlik_fiyatlandirma_table.php:39

**Pattern:** `// Status (Context7: boolean active/enabled)`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_06_230000_remove_enabled_field_complete.php:19

**Pattern:** `// This migration ensures NO 'enabled' column exists anywhere`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_06_230000_remove_enabled_field_complete.php:21

**Pattern:** `// Check and remove 'enabled' from features table (if exists)`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_06_230000_remove_enabled_field_complete.php:22

**Pattern:** `if (Schema::hasColumn('features', 'enabled')) {`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_06_230000_remove_enabled_field_complete.php:27

**Pattern:** `WHERE enabled IS NOT NULL`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_06_230000_remove_enabled_field_complete.php:31

**Pattern:** `$table->dropColumn('enabled');`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_06_230000_remove_enabled_field_complete.php:34

**Pattern:** `echo "✅ Removed 'enabled' column from features table\n";`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_06_230000_remove_enabled_field_complete.php:37

**Pattern:** `// Check and remove 'enabled' from feature_categories table (if exists)`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_06_230000_remove_enabled_field_complete.php:38

**Pattern:** `if (Schema::hasColumn('feature_categories', 'enabled')) {`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_06_230000_remove_enabled_field_complete.php:43

**Pattern:** `WHERE enabled IS NOT NULL`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_06_230000_remove_enabled_field_complete.php:47

**Pattern:** `$table->dropColumn('enabled');`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_06_230000_remove_enabled_field_complete.php:50

**Pattern:** `echo "✅ Removed 'enabled' column from feature_categories table\n";`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_06_230000_remove_enabled_field_complete.php:79

**Pattern:** `// If needed, manually restore 'enabled' columns`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_06_000003_remove_enabled_from_features_tables.php:14

**Pattern:** `* This migration removes enabled columns if they exist`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_06_000003_remove_enabled_from_features_tables.php:18

**Pattern:** `// Check and remove enabled from features table (if exists)`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_06_000003_remove_enabled_from_features_tables.php:19

**Pattern:** `if (Schema::hasColumn('features', 'enabled')) {`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_06_000003_remove_enabled_from_features_tables.php:31

**Pattern:** `// If both exist, sync them then drop enabled`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_06_000003_remove_enabled_from_features_tables.php:35

**Pattern:** `// Now drop enabled column`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_06_000003_remove_enabled_from_features_tables.php:37

**Pattern:** `$table->dropColumn('enabled');`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_06_000003_remove_enabled_from_features_tables.php:41

**Pattern:** `// Check and remove enabled from feature_categories table (if exists)`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_06_000003_remove_enabled_from_features_tables.php:42

**Pattern:** `if (Schema::hasColumn('feature_categories', 'enabled')) {`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_06_000003_remove_enabled_from_features_tables.php:54

**Pattern:** `// If both exist, sync them then drop enabled`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_06_000003_remove_enabled_from_features_tables.php:58

**Pattern:** `// Now drop enabled column`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_06_000003_remove_enabled_from_features_tables.php:60

**Pattern:** `$table->dropColumn('enabled');`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_06_000003_remove_enabled_from_features_tables.php:68

**Pattern:** `* ⚠️ WARNING: Context7 Violation - enabled field is FORBIDDEN`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_06_000003_remove_enabled_from_features_tables.php:73

**Pattern:** `// ⚠️ Context7: enabled field is FORBIDDEN`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_06_000003_remove_enabled_from_features_tables.php:81

**Pattern:** `echo "⚠️ WARNING: Rollback disabled - enabled field is FORBIDDEN per Context7 standards\n";`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_10_29_170932_create_alt_kategori_yayin_tipi_table.php:40

**Pattern:** `$table->boolean('enabled')->default(true);`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_10_29_170932_create_alt_kategori_yayin_tipi_table.php:51

**Pattern:** `$table->index(['alt_kategori_id', 'enabled']);`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_10_24_210000_create_kategori_yayin_tipi_field_dependencies_table.php:56

**Pattern:** `$table->tinyInteger('enabled')->default(1)->comment('0=disabled, 1=enabled (Context7 boolean)');`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_10_24_210000_create_kategori_yayin_tipi_field_dependencies_table.php:91

**Pattern:** `$table->index('enabled', 'idx_enabled');`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_02_000001_create_polymorphic_features_system.php:24

**Pattern:** `$table->boolean('enabled')->default(true);`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_02_000001_create_polymorphic_features_system.php:29

**Pattern:** `$table->index(['type', 'enabled']);`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_02_000001_create_polymorphic_features_system.php:65

**Pattern:** `$table->boolean('enabled')->default(true);`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_02_000001_create_polymorphic_features_system.php:74

**Pattern:** `$table->index(['category_id', 'enabled']);`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_02_000001_create_polymorphic_features_system.php:75

**Pattern:** `$table->index(['field_type', 'enabled']);`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_11_02_000001_create_polymorphic_features_system.php:77

**Pattern:** `$table->index(['is_filterable', 'enabled']);`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_10_28_083829_create_yayin_tipleri_table.php:23

**Pattern:** `$table->boolean('enabled')->default(true);`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_10_25_160239_create_kategori_yayin_tipi_field_dependencies_table.php:37

**Pattern:** `$table->boolean('enabled')->default(true)->comment('Field aktif mi?');`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/migrations/2025_10_25_160239_create_kategori_yayin_tipi_field_dependencies_table.php:57

**Pattern:** `$table->index('enabled', 'idx_enabled');`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/SampleFeaturesSeeder.php:47

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/SampleFeaturesSeeder.php:90

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/SampleFeaturesSeeder.php:133

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/SampleFeaturesSeeder.php:149

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/SampleFeaturesSeeder.php:169

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/SampleFeaturesSeeder.php:211

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/SampleFeaturesSeeder.php:241

**Pattern:** `['Aktif Kategori', FeatureCategory::where('enabled', true)->count()],`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/SampleFeaturesSeeder.php:243

**Pattern:** `['Aktif Özellik', Feature::where('enabled', true)->count()],`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/AIProviderSettingsSeeder.php:131

**Pattern:** `'key' => 'ai_enabled',`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/AIProviderSettingsSeeder.php:135

**Pattern:** `'description' => 'AI system enabled (0=disabled, 1=enabled)'`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/Context7ListingSeeder.php:54

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/Context7ListingSeeder.php:78

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/Context7ListingSeeder.php:102

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/Context7ListingSeeder.php:121

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/Context7ListingSeeder.php:140

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/Context7ListingSeeder.php:164

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/YazlikKiralikOzellikIliskilendirmeSeeder.php:68

**Pattern:** `$hasEnabledColumn = Schema::hasColumn('features', 'enabled');`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/YazlikKiralikOzellikIliskilendirmeSeeder.php:73

**Pattern:** `$q->where('enabled', true);`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/KonutFieldDependencySeeder.php:31

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/KonutFieldDependencySeeder.php:46

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/KonutFieldDependencySeeder.php:60

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/KonutFieldDependencySeeder.php:75

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/KonutFieldDependencySeeder.php:91

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/KonutFieldDependencySeeder.php:105

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/KonutFieldDependencySeeder.php:120

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/KonutFieldDependencySeeder.php:135

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/ArsaFieldDependencySeeder.php:28

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/ArsaFieldDependencySeeder.php:42

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/ArsaFieldDependencySeeder.php:57

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/ArsaFieldDependencySeeder.php:71

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/ArsaFieldDependencySeeder.php:85

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/ArsaFieldDependencySeeder.php:100

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/ArsaFieldDependencySeeder.php:115

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/ArsaFieldDependencySeeder.php:129

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/ArsaFieldDependencySeeder.php:146

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/ArsaFieldDependencySeeder.php:160

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/ArsaFieldDependencySeeder.php:175

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/ArsaFieldDependencySeeder.php:189

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/KategoriYayinTipiFieldDependencySeeder.php:224

**Pattern:** `'enabled' => 1,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/ProjeOzellikIliskilendirmeSeeder.php:62

**Pattern:** `$hasEnabledColumn = Schema::hasColumn('features', 'enabled');`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/ProjeOzellikIliskilendirmeSeeder.php:67

**Pattern:** `$q->where('enabled', true);`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/ArsaIsyeriYayinTipiSeeder.php:156

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/AIMasterDataSeeder.php:40

**Pattern:** `['key' => 'ai_analysis_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'ai', 'description' => 'AI analiz aktif mi'],`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/AIMasterDataSeeder.php:41

**Pattern:** `['key' => 'ai_training_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'ai', 'description' => 'AI eğitim aktif mi'],`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/AIMasterDataSeeder.php:44

**Pattern:** `['key' => 'ai_log_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'ai', 'description' => 'AI log aktif mi'],`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/AIMasterDataSeeder.php:45

**Pattern:** `['key' => 'ai_analytics_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'ai', 'description' => 'AI analitik aktif mi'],`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/YazlikVillaOzellikleriSeeder.php:118

**Pattern:** `$hasEnabledColumn = Schema::hasColumn('feature_categories', 'enabled');`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/YazlikVillaOzellikleriSeeder.php:136

**Pattern:** `$data['enabled'] = true;`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/YazlikVillaOzellikleriSeeder.php:159

**Pattern:** `$hasEnabledColumn = Schema::hasColumn('features', 'enabled');`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/YazlikVillaOzellikleriSeeder.php:201

**Pattern:** `$data['enabled'] = true;`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/ActivateFeatureCategoriesSeeder.php:24

**Pattern:** `$updated = FeatureCategory::where('enabled', false)->update(['enabled' => true]);`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/ActivateFeatureCategoriesSeeder.php:28

**Pattern:** `$featuresUpdated = Feature::where('enabled', false)->update(['enabled' => true]);`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/ActivateFeatureCategoriesSeeder.php:108

**Pattern:** `FeatureCategory::where('enabled', true)->count(),`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/ActivateFeatureCategoriesSeeder.php:109

**Pattern:** `FeatureCategory::where('enabled', false)->count()`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/ActivateFeatureCategoriesSeeder.php:114

**Pattern:** `Feature::where('enabled', true)->count(),`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/ActivateFeatureCategoriesSeeder.php:115

**Pattern:** `Feature::where('enabled', false)->count()`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/KonutYazlikYayinTipiSeeder.php:109

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/PolymorphicFeaturesMigrationSeeder.php:63

**Pattern:** `'enabled' => $kategori->enabled ?? true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/PolymorphicFeaturesMigrationSeeder.php:106

**Pattern:** `'enabled' => $ozellik->enabled ?? true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/PolymorphicFeaturesMigrationSeeder.php:142

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/PolymorphicFeaturesMigrationSeeder.php:156

**Pattern:** `'enabled' => $ozellik->enabled ?? true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/PolymorphicFeaturesMigrationSeeder.php:185

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/PolymorphicFeaturesMigrationSeeder.php:205

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/RevyStyleFeatureCategoriesSeeder.php:37

**Pattern:** `$hasEnabledColumn = Schema::hasColumn('feature_categories', 'enabled');`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/RevyStyleFeatureCategoriesSeeder.php:54

**Pattern:** `$icOzellikleriData['enabled'] = true;`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/RevyStyleFeatureCategoriesSeeder.php:117

**Pattern:** `$disOzellikleriData['enabled'] = true;`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/RevyStyleFeatureCategoriesSeeder.php:158

**Pattern:** `$muhitData['enabled'] = true;`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/RevyStyleFeatureCategoriesSeeder.php:196

**Pattern:** `$ulasimData['enabled'] = true;`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/RevyStyleFeatureCategoriesSeeder.php:232

**Pattern:** `$cepheData['enabled'] = true;`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/RevyStyleFeatureCategoriesSeeder.php:270

**Pattern:** `$manzaraData['enabled'] = true;`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/RevyStyleFeatureCategoriesSeeder.php:334

**Pattern:** `$hasEnabledColumn = Schema::hasColumn('features', 'enabled');`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/RevyStyleFeatureCategoriesSeeder.php:352

**Pattern:** `$featureData['enabled'] = true;`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/SimpleTestDataSeeder.php:121

**Pattern:** `'enabled' => 1,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/SimpleTestDataSeeder.php:144

**Pattern:** `'enabled' => 1,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/YazlikOzellikIliskilendirmeSeeder.php:71

**Pattern:** `$hasEnabledColumn = Schema::hasColumn('features', 'enabled');`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/YazlikOzellikIliskilendirmeSeeder.php:76

**Pattern:** `$q->where('enabled', true);`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/YazlikKiralikAnaKategoriSeeder.php:120

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/ProjeOzellikleriSeeder.php:28

**Pattern:** `$hasEnabledColumn = Schema::hasColumn('feature_categories', 'enabled');`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/ProjeOzellikleriSeeder.php:64

**Pattern:** `$hasEnabledColumn = Schema::hasColumn('feature_categories', 'enabled');`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/ProjeOzellikleriSeeder.php:82

**Pattern:** `$data['enabled'] = true;`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/ProjeOzellikleriSeeder.php:105

**Pattern:** `$hasEnabledColumn = Schema::hasColumn('features', 'enabled');`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/seeders/ProjeOzellikleriSeeder.php:147

**Pattern:** `$data['enabled'] = true;`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/factories/FeatureCategoryFactory.php:20

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### database/factories/FeatureFactory.php:22

**Pattern:** `'enabled' => true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/leaflet-draw-loader.js:132

**Pattern:** `.leaflet-draw-toolbar a.leaflet-draw-draw-polygon.leaflet-draw-toolbar-button-enabled,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/leaflet-draw-loader.js:133

**Pattern:** `.leaflet-draw-toolbar a.leaflet-draw-edit-edit.leaflet-draw-toolbar-button-enabled,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/leaflet-draw-loader.js:134

**Pattern:** `.leaflet-draw-toolbar a.leaflet-draw-edit-remove.leaflet-draw-toolbar-button-enabled {`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/admin/mobile-first-responsive.js:339

**Pattern:** `element.classList.add('offline-enabled');`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/admin/mobile-first-responsive.js:443

**Pattern:** `document.querySelectorAll('[data-swipe-enabled]').forEach((element) => {`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/admin/mobile-first-responsive.js:498

**Pattern:** `element.classList.add('hover-enabled');`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/admin/mobile-first-responsive.js:602

**Pattern:** `.hover-enabled:hover {`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/admin/mobile-first-responsive.js:612

**Pattern:** `.offline-enabled {`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/admin/ilan-create/portals.js:11

**Pattern:** `sahibinden: { enabled: false, id: '', price: '' },`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/admin/ilan-create/portals.js:12

**Pattern:** `hepsiemlak: { enabled: false, id: '', price: '' },`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/admin/ilan-create/portals.js:13

**Pattern:** `emlakjet: { enabled: false, id: '', price: '' },`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/admin/ilan-create/portals.js:14

**Pattern:** `zingat: { enabled: false, id: '', price: '' },`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/admin/ilan-create/portals.js:15

**Pattern:** `hurriyetemlak: { enabled: false, id: '', price: '' },`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/admin/ilan-create/portals.js:16

**Pattern:** `emlak365: { enabled: false, id: '', price: '' },`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/admin/ilan-create/portals.js:35

**Pattern:** `return Object.values(this.portals).filter((p) => p.enabled).length;`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/admin/ilan-create/portals.js:44

**Pattern:** `this.portals[key].enabled = this.allSelected;`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/admin/ilan-create/portals.js:52

**Pattern:** `this.portals[portalName].enabled = !this.portals[portalName].enabled;`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/admin/ilan-create/portals.js:60

**Pattern:** `this.allSelected = Object.values(this.portals).every((p) => p.enabled);`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/admin/ilan-create/portals.js:74

**Pattern:** `const enabled = Object.entries(this.portals)`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/admin/ilan-create/portals.js:75

**Pattern:** `.filter(([key, portal]) => portal.enabled)`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/admin/ilan-create/portals.js:78

**Pattern:** `if (enabled.length === 0) {`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/admin/ilan-create/portals.js:83

**Pattern:** `window.toast?.info(`${enabled.length} portala senkronizasyon başlatılıyor...`);`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/admin/ilan-create/portals.js:86

**Pattern:** `enabled.forEach((portalName) => {`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/admin/ilan-create/portals.js:90

**Pattern:** `enabled: true,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/admin/ilan-create/portals.js:95

**Pattern:** `console.log('Syncing to portals:', enabled);`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/admin/ilan-create/touch-gestures.js:31

**Pattern:** `if (e.target.closest('[data-swipe-enabled="true"]')) {`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/admin/ilan-create/touch-gestures.js:41

**Pattern:** `if (e.target.closest('[data-swipe-enabled="true"]')) {`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/admin/ilan-create/touch-gestures.js:51

**Pattern:** `if (e.target.closest('[data-swipe-enabled="true"]')) {`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/admin/ilan-create/touch-gestures.js:415

**Pattern:** `[data-swipe-enabled="true"],`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/admin/ilan-create/touch-gestures.js:551

**Pattern:** `element.setAttribute('data-swipe-enabled', 'true');`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/admin/ilan-create/touch-gestures.js:564

**Pattern:** `element.removeAttribute('data-swipe-enabled');`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/admin/ilan-create/touch-gestures.js:605

**Pattern:** `element.setAttribute('data-swipe-enabled', 'true');`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/components/ilan/publication-manager.js:102

**Pattern:** `this.autoRenewal = data.auto_renewal_enabled;`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/components/ilan/publication-manager.js:216

**Pattern:** `enabled: this.enableAutoRenewal,`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/components/ilan/portal-manager.js:106

**Pattern:** `this.masterPublishEnabled = data.master_publish_enabled;`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/app.js:364

**Pattern:** `element.classList.add('offline-enabled');`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/app.js:468

**Pattern:** `document.querySelectorAll('[data-swipe-enabled]').forEach((element) => {`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/app.js:523

**Pattern:** `element.classList.add('hover-enabled');`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/app.js:631

**Pattern:** `.hover-enabled:hover {`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/js/app.js:641

**Pattern:** `.offline-enabled {`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/admin/settings/index.blade.php:334

**Pattern:** `<label for="qrcode_enabled" class="text-sm font-medium text-gray-900 dark:text-white block mb-1">`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/admin/settings/index.blade.php:341

**Pattern:** `id="qrcode_enabled"`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/admin/settings/index.blade.php:342

**Pattern:** `name="qrcode_enabled"`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/admin/settings/index.blade.php:344

**Pattern:** `{{ (($settings['qrcode_enabled'] ?? 'true') == 'true' || ($settings['qrcode_enabled'] ?? true) === true) ? 'checked' : '' }}`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/admin/settings/index.blade.php:424

**Pattern:** `<label for="navigation_enabled" class="text-sm font-medium text-gray-900 dark:text-white block mb-1">`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/admin/settings/index.blade.php:431

**Pattern:** `id="navigation_enabled"`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/admin/settings/index.blade.php:432

**Pattern:** `name="navigation_enabled"`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/admin/settings/index.blade.php:434

**Pattern:** `{{ (($settings['navigation_enabled'] ?? 'true') == 'true' || ($settings['navigation_enabled'] ?? true) === true) ? 'checked' : '' }}`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/admin/takvim/sezonlar.blade.php:131

**Pattern:** `<span class="text-sm {{ ($sezon['enabled'] ?? true) ? 'text-green-600' : 'text-red-600' }}">`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/admin/takvim/sezonlar.blade.php:132

**Pattern:** `<i class="fas {{ ($sezon['enabled'] ?? true) ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/admin/takvim/sezonlar.blade.php:133

**Pattern:** `{{ ($sezon['enabled'] ?? true) ? 'Aktif' : 'Pasif' }}`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/admin/ayarlar/create.blade.php:378

**Pattern:** `<span class="text-gray-700 dark:text-gray-300">ai_enabled</span>`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/admin/ayarlar/create.blade.php:547

**Pattern:** `{ key: 'ai_enabled', value: 'true', type: 'boolean', group: 'ai', description: 'AI özellikleri aktif' },`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/admin/ai-redirect/edit.blade.php:96

**Pattern:** `<input type="checkbox" name="cache_enabled" value="1"`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/admin/ai-redirect/create.blade.php:94

**Pattern:** `<input type="checkbox" name="cache_enabled" value="1"`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/admin/ilanlar/components/publication-status.blade.php:86

**Pattern:** `{{-- Otomatik Aktif Etme (Context7: auto_enabled) --}}`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/admin/ilanlar/components/publication-status.blade.php:89

**Pattern:** `<input type="checkbox" name="auto_enabled" id="auto_enabled"`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/admin/ilanlar/components/publication-status.blade.php:90

**Pattern:** `{{ old('auto_enabled', $ilan->auto_enabled ?? false) ? 'checked' : '' }}`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/admin/ilanlar/components/publication-status.blade.php:93

**Pattern:** `<label for="auto_enabled" class="text-sm font-medium text-gray-900 dark:text-white cursor-pointer">`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/admin/ilanlar/components/publication-status.blade.php:160

**Pattern:** `- auto_enabled (NOT otomatik_aktif) ✅`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/admin/eslesme/index.blade.php:168

**Pattern:** `<label for="notification_enabled"`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/admin/eslesme/index.blade.php:172

**Pattern:** `<input type="checkbox" name="notification_enabled"`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/admin/eslesme/index.blade.php:173

**Pattern:** `id="notification_enabled"`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/components/qr-code-display.blade.php:26

**Pattern:** `// Check if QR code is enabled from settings`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/components/context7/feedback/notification-center.blade.php:154

**Pattern:** `console.log('Real-time notifications enabled');`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/components/admin/toggle.blade.php:70

**Pattern:** `x-data="{ enabled: {{ $checked ? 'true' : 'false' }} }"`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/components/admin/toggle.blade.php:76

**Pattern:** `@click="if (!{{ $disabled ? 'true' : 'false' }}) enabled = !enabled"`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/components/admin/toggle.blade.php:77

**Pattern:** `@keydown.space.prevent="if (!{{ $disabled ? 'true' : 'false' }}) enabled = !enabled"`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/components/admin/toggle.blade.php:78

**Pattern:** `@keydown.enter.prevent="if (!{{ $disabled ? 'true' : 'false' }}) enabled = !enabled"`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/components/admin/toggle.blade.php:79

**Pattern:** `:aria-checked="enabled"`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/components/admin/toggle.blade.php:89

**Pattern:** `'bg-blue-600': enabled && !{{ $hasError ? 'true' : 'false' }},`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/components/admin/toggle.blade.php:90

**Pattern:** `'bg-gray-200 dark:bg-gray-700': !enabled && !{{ $hasError ? 'true' : 'false' }},`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/components/admin/toggle.blade.php:91

**Pattern:** `'bg-red-600': enabled && {{ $hasError ? 'true' : 'false' }},`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/components/admin/toggle.blade.php:92

**Pattern:** `'bg-red-200': !enabled && {{ $hasError ? 'true' : 'false' }}`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/components/admin/toggle.blade.php:101

**Pattern:** `:class="{ '{{ $sizeClasses['translate'] }}': enabled }"`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/components/admin/toggle.blade.php:110

**Pattern:** `:value="enabled ? '1' : '0'"`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/components/admin/toggle.blade.php:121

**Pattern:** `@click="if (!{{ $disabled ? 'true' : 'false' }}) enabled = !enabled"`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### resources/views/components/listing-navigation.blade.php:20

**Pattern:** `// Check if navigation is enabled from settings`

**Mesaj:** enabled → status kullanılmalı (status field olarak)

**Replacement:** `status`

---

### app/Http/Controllers/Admin/SiteController.php:44

**Pattern:** `->orWhere('sehir', 'LIKE', "%{$searchTerm}%")`

**Mesaj:** sehir → il kullanılmalı

**Replacement:** `il`

---

### app/Http/Controllers/Admin/SiteController.php:54

**Pattern:** `'sehir' => $site->sehir,`

**Mesaj:** sehir → il kullanılmalı

**Replacement:** `il`

---

### app/Console/Commands/StandardCheck.php:225

**Pattern:** `$forbiddenFields = ['durum', 'aktif', 'sehir', 'sehir_id', 'ad_soyad'];`

**Mesaj:** sehir → il kullanılmalı

**Replacement:** `il`

---

### database/seeders/OzellikKategorileriSeeder.php:86

**Pattern:** `['alt_kategori_adi' => 'Şehir', 'alt_kategori_slug' => 'sehir', 'alt_kategori_icon' => '🏙️', 'field_type' => 'select', 'field_options' => json_encode(['Var', 'Yok', 'Yakın', 'Uzak']), 'field_unit' => 'km'],`

**Mesaj:** sehir → il kullanılmalı

**Replacement:** `il`

---

### database/seeders/OzellikKategorileriSeeder.php:184

**Pattern:** `'manzara' => ['deniz', 'dag', 'sehir'],`

**Mesaj:** sehir → il kullanılmalı

**Replacement:** `il`

---

### database/seeders/OzellikKategorileriSeeder.php:190

**Pattern:** `'manzara' => ['deniz', 'dag', 'sehir', 'doga'],`

**Mesaj:** sehir → il kullanılmalı

**Replacement:** `il`

---

### database/seeders/OzellikKategorileriSeeder.php:202

**Pattern:** `'manzara' => ['sehir'],`

**Mesaj:** sehir → il kullanılmalı

**Replacement:** `il`

---

### database/seeders/RevyStyleFeatureCategoriesSeeder.php:290

**Pattern:** `['name' => 'Şehir', 'slug' => 'sehir'],`

**Mesaj:** sehir → il kullanılmalı

**Replacement:** `il`

---

### resources/js/admin/ilan-create/features-ai.js:215

**Pattern:** `city_id: document.getElementById('sehir_id')?.value || null,`

**Mesaj:** sehir → il kullanılmalı

**Replacement:** `il`

---

### app/Models/Kisi.php:247

**Pattern:** `return $this->hasMany(Talep::class, 'musteri_id');`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Models/Gorev.php:25

**Pattern:** `'musteri_id',`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Models/Gorev.php:54

**Pattern:** `return $this->belongsTo(User::class, 'musteri_id');`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Models/Talep.php:25

**Pattern:** `'musteri_id',`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Models/Talep.php:63

**Pattern:** `return $this->belongsTo(Kisi::class, 'musteri_id');`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Http/Controllers/Admin/BulkKisiController.php:403

**Pattern:** `'tip' => $row[5] ?? 'musteri',`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Http/Controllers/Admin/DanismanAIController.php:1083

**Pattern:** `'musteri' => 'Müşteri Hizmetleri',`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Emlak/Services/ProjeService.php:81

**Pattern:** `'rol' => 'musteri',`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Crm/Models/Randevu.php:27

**Pattern:** `'musteri_id',`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Crm/Models/Randevu.php:60

**Pattern:** `return $this->belongsTo(Musteri::class, 'musteri_id');`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Crm/Controllers/MusteriController.php:210

**Pattern:** `$aktiviteler = Aktivite::where('musteri_id', $id)`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Crm/Controllers/MusteriController.php:216

**Pattern:** `$yaklasanRandevular = Randevu::where('musteri_id', $id)`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Crm/Controllers/MusteriController.php:225

**Pattern:** `return view('crm::musteriler.show', compact('musteri', 'aktiviteler', 'yaklasanRandevular', 'ilgilenilenIlanlar'));`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Crm/Controllers/MusteriController.php:245

**Pattern:** `return view('crm::musteriler.edit', compact('musteri'));`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Crm/Controllers/MusteriController.php:309

**Pattern:** `Aktivite::where('musteri_id', $id)->delete();`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Crm/Controllers/MusteriController.php:310

**Pattern:** `Randevu::where('musteri_id', $id)->delete();`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Crm/Controllers/MusteriController.php:314

**Pattern:** `\Illuminate\Support\Facades\DB::table('musteri_ilan')->where('musteri_id', $id)->delete();`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Crm/Controllers/MusteriController.php:411

**Pattern:** `$aktiviteler = Aktivite::where('musteri_id', $id)`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Crm/Controllers/MusteriController.php:415

**Pattern:** `return view('crm::musteriler.aktiviteler', compact('musteri', 'aktiviteler'));`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Crm/Controllers/MusteriController.php:435

**Pattern:** `$randevular = Randevu::where('musteri_id', $id)`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Crm/Controllers/MusteriController.php:439

**Pattern:** `return view('crm::musteriler.randevular', compact('musteri', 'randevular'));`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Crm/Controllers/RandevuController.php:24

**Pattern:** `$query = Randevu::with(['musteri', 'danisman', 'ilan']);`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Crm/Controllers/RandevuController.php:33

**Pattern:** `->orWhereHas('musteri', function ($q) use ($searchTerm) {`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Crm/Controllers/RandevuController.php:54

**Pattern:** `if ($request->has('musteri_id') && ! empty($request->musteri_id)) {`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Crm/Controllers/RandevuController.php:55

**Pattern:** `$query->where('musteri_id', $request->musteri_id);`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Crm/Controllers/RandevuController.php:103

**Pattern:** `if ($request->has('musteri_id') && ! empty($request->musteri_id)) {`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Crm/Controllers/RandevuController.php:104

**Pattern:** `$musteri = Musteri::findOrFail($request->musteri_id);`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Crm/Controllers/RandevuController.php:126

**Pattern:** `return view('crm::randevular.create', compact('musteri', 'ilan', 'musteriler'));`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Crm/Controllers/RandevuController.php:141

**Pattern:** `'musteri_id' => 'required|exists:musteriler,id',`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Crm/Controllers/RandevuController.php:155

**Pattern:** `$musteri = Musteri::findOrFail($request->musteri_id);`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Crm/Controllers/RandevuController.php:201

**Pattern:** `$randevu = Randevu::with(['musteri', 'danisman', 'ilan'])->findOrFail($id);`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Crm/Controllers/RandevuController.php:221

**Pattern:** `$randevu = Randevu::with(['musteri', 'ilan'])->findOrFail($id);`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Crm/Controllers/RandevuController.php:258

**Pattern:** `'musteri_id' => 'required|exists:musteriler,id',`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Crm/Controllers/RandevuController.php:272

**Pattern:** `$musteri = Musteri::findOrFail($request->musteri_id);`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Crm/Controllers/RandevuController.php:369

**Pattern:** `$query = Randevu::with(['musteri', 'danisman'])`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Crm/Controllers/RandevuController.php:408

**Pattern:** `'musteri_id' => $randevu->musteri_id,`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Analitik/routes/web.php:21

**Pattern:** `Route::get('/musteri-raporu', [RaporController::class, 'musteriRaporu'])->name('musteri');`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Analitik/routes/web.php:33

**Pattern:** `Route::get('/musteri', [IstatistikController::class, 'musteri'])->name('musteri');`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Analitik/routes/api.php:24

**Pattern:** `Route::get('/musteri', [RaporApiController::class, 'musteriRaporu'])->name('musteri');`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Analitik/routes/api.php:36

**Pattern:** `Route::get('/musteri', [IstatistikApiController::class, 'musteri'])->name('musteri');`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Analitik/Services/AnalitikService.php:221

**Pattern:** `$satislar = CRMSatis::with('user', 'musteri')->latest()->limit(5)->get();`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Finans/Models/Komisyon.php:29

**Pattern:** `'musteri_id',`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Finans/Models/FinansalIslem.php:30

**Pattern:** `'musteri_id',`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Finans/Services/FinansalIslemService.php:49

**Pattern:** `'musteri_id' => $musteri->id,`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/Finans/Services/FinansalIslemService.php:64

**Pattern:** `'musteri_id' => $musteri->id,`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/TakimYonetimi/Models/Gorev.php:28

**Pattern:** `'musteri_id',`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/TakimYonetimi/Models/Gorev.php:70

**Pattern:** `return $this->belongsTo(\App\Models\Kisi::class, 'musteri_id');`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/TakimYonetimi/Controllers/Admin/GorevController.php:13

**Pattern:** `$gorevler = Gorev::with(['admin', 'danisman', 'musteri', 'proje'])->paginate(15);`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/TakimYonetimi/Controllers/Admin/GorevController.php:59

**Pattern:** `'musteri_id' => 'nullable|exists:users,id',`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/TakimYonetimi/Controllers/Admin/GorevController.php:72

**Pattern:** `$gorev->load(['admin', 'danisman', 'musteri', 'proje', 'takip', 'dosyalar']);`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/TakimYonetimi/Controllers/Admin/GorevController.php:104

**Pattern:** `'musteri_id' => 'nullable|exists:users,id',`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/TakimYonetimi/Controllers/API/GorevApiController.php:19

**Pattern:** `$query = Gorev::with(['admin', 'danisman', 'musteri', 'proje']);`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/TakimYonetimi/Controllers/API/GorevApiController.php:65

**Pattern:** `$gorev->load(['admin', 'danisman', 'musteri', 'proje', 'takip', 'dosyalar']);`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/TakimYonetimi/Services/GorevYonetimService.php:382

**Pattern:** `->with(['danisman', 'musteri', 'proje'])`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/TakimYonetimi/Services/GorevYonetimService.php:399

**Pattern:** `->with(['danisman', 'musteri', 'proje'])`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/TakimYonetimi/Services/TelegramBotService.php:385

**Pattern:** `$gorevler = Gorev::with(['danisman', 'musteri'])`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/CRMSatis/Models/Sozlesme.php:30

**Pattern:** `'musteri_id',`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/CRMSatis/Models/SatisRaporu.php:30

**Pattern:** `'musteri_id',`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/CRMSatis/Models/Satis.php:30

**Pattern:** `'musteri_id',`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/CRMSatis/Services/SatisService.php:220

**Pattern:** `->orWhereHas('musteri', function ($mq) use ($query) {`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/CRMSatis/Services/SatisService.php:240

**Pattern:** `'musteri_id' => $satis->musteri_id,`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/CRMSatis/Services/SatisService.php:306

**Pattern:** `'musteri_id' => $satis->musteri_id,`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/CRMSatis/Services/SatisService.php:325

**Pattern:** `'musteri_id' => $satis->musteri_id,`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/CRMSatis/Services/SatisService.php:344

**Pattern:** `'musteri_id' => $satis->musteri_id,`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Modules/CRMSatis/Services/SatisService.php:361

**Pattern:** `'musteri_id' => $satis->musteri_id,`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### database/seeders/Context7SystemSeeder.php:58

**Pattern:** `['name' => 'musteri', 'guard_name' => 'web'],`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### resources/views/admin/users/index.blade.php:170

**Pattern:** `<option value="musteri" {{ request('role')=='musteri' ? 'selected' : '' }}>Müşteri</option>`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### resources/views/admin/users/index.blade.php:268

**Pattern:** `'musteri' => 'Müşteri',`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### resources/views/admin/bulk-kisi/index.blade.php:246

**Pattern:** `<option value="musteri">Müşteri</option>`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### resources/views/admin/bulk-kisi/index.blade.php:306

**Pattern:** `tip: 'musteri',`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### resources/views/admin/bulk-kisi/index.blade.php:339

**Pattern:** `tip: 'musteri',`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### resources/views/admin/bulk-kisi/index.blade.php:356

**Pattern:** `tip: 'musteri',`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### resources/views/admin/takim-yonetimi/gorevler/edit.blade.php:257

**Pattern:** `<label for="musteri_id" class="block text-sm font-medium text-purple-700 mb-2">`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### resources/views/admin/takim-yonetimi/gorevler/edit.blade.php:260

**Pattern:** `<select style="color-scheme: light dark;" class="admin-input @error('musteri_id') border-red-300 @enderror transition-all duration-200" id="musteri_id"`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### resources/views/admin/takim-yonetimi/gorevler/edit.blade.php:261

**Pattern:** `name="musteri_id">`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### resources/views/admin/takim-yonetimi/gorevler/edit.blade.php:265

**Pattern:** `{{ old('musteri_id', $gorev->musteri_id) == $musteri->id ? 'selected' : '' }}>`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### resources/views/admin/takim-yonetimi/gorevler/edit.blade.php:270

**Pattern:** `@error('musteri_id')`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### resources/views/admin/takim-yonetimi/gorevler/edit.blade.php:457

**Pattern:** `'musteri_id', 'proje_id', 'tags'`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### resources/views/admin/takim-yonetimi/gorevler/edit.blade.php:488

**Pattern:** `'musteri_id', 'proje_id', 'tags'`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### resources/views/admin/takim-yonetimi/gorevler/edit.blade.php:520

**Pattern:** `'musteri_id', 'proje_id', 'tags'`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### resources/views/admin/takim-yonetimi/gorevler/edit.blade.php:541

**Pattern:** `'musteri_id', 'proje_id', 'tags'`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### resources/views/admin/takim-yonetimi/gorevler/edit.blade.php:834

**Pattern:** `'musteri_id', 'proje_id', 'tags'`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### resources/views/components/crm-contact-manager.blade.php:25

**Pattern:** `<option value="musteri">Müşteri</option>`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### resources/views/components/crm-contact-manager.blade.php:118

**Pattern:** `<option value="musteri">Müşteri</option>`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### resources/views/components/crm-contact-manager.blade.php:172

**Pattern:** `category: 'musteri',`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### resources/views/components/crm-contact-manager.blade.php:188

**Pattern:** `category: 'musteri',`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### resources/views/components/crm-contact-manager.blade.php:308

**Pattern:** `category: 'musteri',`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### resources/views/components/crm-contact-manager.blade.php:315

**Pattern:** `'musteri': 'bg-green-100 text-green-800',`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### resources/views/components/crm-contact-manager.blade.php:325

**Pattern:** `'musteri': 'Müşteri',`

**Mesaj:** musteri → kisi kullanılmalı

**Replacement:** `kisi`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:93

**Pattern:** `$html = '<div class="neo-dynamic-form">';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:94

**Pattern:** `$html .= '<div class="neo-form-header">';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:95

**Pattern:** `$html .= '<h3 class="neo-form-title">' . ucfirst($kategoriSlug) . ' - ' . $yayinTipi . ' Formu</h3>';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:96

**Pattern:** `$html .= '<p class="neo-form-subtitle">' . $fields->count() . ' alan AI ile otomatik doldurulabilir</p>';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:99

**Pattern:** `$html .= '<form id="dynamicForm" class="neo-form" data-kategori="' . $kategoriSlug . '" data-yayin-tipi="' . $yayinTipi . '">';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:107

**Pattern:** `$html .= '<div class="neo-form-section">';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:108

**Pattern:** `$html .= '<h4 class="neo-section-title">' . ucfirst($category) . ' Bilgileri</h4>';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:118

**Pattern:** `$html .= '<div class="neo-ai-actions">';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:119

**Pattern:** `$html .= '<button type="button" class="neo-btn neo-btn-ai" onclick="fillAllWithAI()">';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:120

**Pattern:** `$html .= '<i class="neo-icon-ai"></i> Tüm Alanları AI ile Doldur';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:122

**Pattern:** `$html .= '<button type="button" class="neo-btn neo-btn-secondary" onclick="clearAllFields()">';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:123

**Pattern:** `$html .= '<i class="neo-icon-clear"></i> Tümünü Temizle';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:128

**Pattern:** `$html .= '<div class="neo-form-actions">';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:129

**Pattern:** `$html .= '<button type="submit" class="neo-btn neo-btn-primary">';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:130

**Pattern:** `$html .= '<i class="neo-icon-save"></i> Formu Kaydet';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:145

**Pattern:** `$html = '<div class="neo-field-group">';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:148

**Pattern:** `$html .= '<label class="neo-field-label" for="' . $field->field_slug . '">';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:151

**Pattern:** `$html .= ' <span class="neo-required">*</span>';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:156

**Pattern:** `$html .= '<div class="neo-field-input">';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:161

**Pattern:** `$html .= 'class="neo-input" placeholder="' . $field->field_name . ' giriniz"';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:168

**Pattern:** `$html .= 'class="neo-input" placeholder="' . $field->field_name . ' giriniz"';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:175

**Pattern:** `$html .= 'class="neo-textarea" placeholder="' . $field->field_name . ' giriniz"';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:182

**Pattern:** `$html .= 'class="neo-select"';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:198

**Pattern:** `$html .= '<div class="neo-checkbox-group">';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:200

**Pattern:** `$html .= 'class="neo-checkbox" value="1">';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:201

**Pattern:** `$html .= '<label for="' . $field->field_slug . '" class="neo-checkbox-label">Evet</label>';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:207

**Pattern:** `$html .= 'class="neo-input"';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:215

**Pattern:** `$html .= '<span class="neo-field-unit">' . $field->field_unit . '</span>';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:222

**Pattern:** `$html .= '<div class="neo-ai-features">';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:225

**Pattern:** `$html .= '<button type="button" class="neo-ai-btn neo-ai-suggestion" onclick="getAISuggestion(\'' . $field->field_slug . '\')">';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:226

**Pattern:** `$html .= '<i class="neo-icon-suggestion"></i> AI Öneri';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:231

**Pattern:** `$html .= '<button type="button" class="neo-ai-btn neo-ai-autofill" onclick="autoFillField(\'' . $field->field_slug . '\')">';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:232

**Pattern:** `$html .= '<i class="neo-icon-autofill"></i> Otomatik Doldur';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:237

**Pattern:** `$html .= '<button type="button" class="neo-ai-btn neo-ai-calculation" onclick="calculateField(\'' . $field->field_slug . '\')">';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:238

**Pattern:** `$html .= '<i class="neo-icon-calculation"></i> Hesapla';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Admin/YalihanBekciController.php:132

**Pattern:** `$componentUsage += substr_count($content, '<x-neo-input');`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Admin/YalihanBekciController.php:133

**Pattern:** `$componentUsage += substr_count($content, '<x-neo-select');`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Admin/PageAnalyzerController.php:530

**Pattern:** `'action' => 'Neo Design System classes ile değiştirin (btn- → neo-btn, card- → neo-card)',`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/View/Components/NeoSelect.php:50

**Pattern:** `return view('components.neo-select');`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/View/Components/NeoInput.php:50

**Pattern:** `return view('components.neo-input');`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Services/Integration/EmlakProYalihanIntegrationService.php:310

**Pattern:** `'.neo-card' => 'padding: var(--neo-spacing-lg); border-radius: var(--neo-radius-lg); box-shadow: var(--neo-shadow-md);',`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Services/Integration/EmlakProYalihanIntegrationService.php:311

**Pattern:** `'.neo-btn' => 'padding: var(--neo-button-padding-md); border-radius: var(--neo-radius-md); transition: var(--neo-transition-colors);',`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Services/Integration/EmlakProYalihanIntegrationService.php:312

**Pattern:** `'.neo-input' => 'padding: var(--neo-input-padding); border-radius: var(--neo-radius-base);',`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Services/Integration/EmlakProYalihanIntegrationService.php:313

**Pattern:** `'.neo-form' => 'gap: var(--neo-form-gap);'`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Services/Integration/EmlakProYalihanIntegrationService.php:409

**Pattern:** `padding: var(--neo-spacing-sm);`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Services/Integration/EmlakProYalihanIntegrationService.php:412

**Pattern:** `.neo-card {`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Services/Integration/EmlakProYalihanIntegrationService.php:413

**Pattern:** `margin: var(--neo-spacing-xs);`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Services/Integration/EmlakProYalihanIntegrationService.php:419

**Pattern:** `padding: var(--neo-spacing-lg);`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Services/KategoriOzellikService.php:668

**Pattern:** `$html .= '<div class="neo-form-group">';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Services/KategoriOzellikService.php:669

**Pattern:** `$html .= '<label class="neo-label">';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Services/KategoriOzellikService.php:685

**Pattern:** `$html .= 'class="neo-input" ';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Services/KategoriOzellikService.php:693

**Pattern:** `$html .= '<select name="' . $fieldName . '" class="neo-select">';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Services/KategoriOzellikService.php:708

**Pattern:** `$html .= 'class="neo-checkbox" ' . $checked . '>';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Services/KategoriOzellikService.php:715

**Pattern:** `$html .= 'class="neo-textarea" ';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/AnalyzePagesComplete.php:672

**Pattern:** `$neoClasses = ['neo-card', 'neo-btn', 'neo-btn-primary', 'neo-btn-outline', 'neo-grid', 'neo-container'];`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/StandardCheck.php:130

**Pattern:** `if (preg_match('/class="[^"]*neo-btn[^"]*"/', $content)) {`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/StandardCheck.php:135

**Pattern:** `'line' => $this->getLineNumber($content, 'neo-btn'),`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/YalihanBekciEnforce.php:67

**Pattern:** `'replacement' => 'Neo Design System (neo-btn, neo-input)',`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/YalihanBekciEnforce.php:234

**Pattern:** `'btn-primary' => 'neo-btn neo-btn-primary',`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/YalihanBekciEnforce.php:235

**Pattern:** `'btn-secondary' => 'neo-btn neo-btn-secondary',`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/YalihanBekciEnforce.php:236

**Pattern:** `'form-control' => 'neo-input',`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/BootstrapToNeoMigration.php:23

**Pattern:** `'btn-primary' => 'neo-btn neo-btn-primary',`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/BootstrapToNeoMigration.php:24

**Pattern:** `'btn-secondary' => 'neo-btn neo-btn-secondary',`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/BootstrapToNeoMigration.php:25

**Pattern:** `'btn-success' => 'neo-btn neo-btn-success',`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/BootstrapToNeoMigration.php:26

**Pattern:** `'btn-danger' => 'neo-btn neo-btn-danger',`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/BootstrapToNeoMigration.php:27

**Pattern:** `'btn-warning' => 'neo-btn neo-btn-warning',`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/BootstrapToNeoMigration.php:28

**Pattern:** `'btn-info' => 'neo-btn neo-btn-info',`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/BootstrapToNeoMigration.php:31

**Pattern:** `'form-control' => 'neo-input',`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/BootstrapToNeoMigration.php:32

**Pattern:** `'form-select' => 'neo-select',`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/BootstrapToNeoMigration.php:35

**Pattern:** `// 'card-header' => 'neo-card-header',`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/BootstrapToNeoMigration.php:36

**Pattern:** `// 'card-body' => 'neo-card-body',`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/BootstrapToNeoMigration.php:137

**Pattern:** `// "btn-primary" → "neo-btn neo-btn-primary btn-primary"`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/BootstrapToNeoMigration.php:148

**Pattern:** `if (strpos($matches[0], 'neo-btn') !== false || strpos($matches[0], 'neo-input') !== false) {`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/YalihanBekciMonitor.php:130

**Pattern:** `$componentUsage += substr_count($content, '<x-neo-input');`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/YalihanBekciMonitor.php:131

**Pattern:** `$componentUsage += substr_count($content, '<x-neo-select');`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/mobile-first-responsive.js:597

**Pattern:** `gap: var(--neo-spacing-md);`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/mobile-first-responsive.js:598

**Pattern:** `transition: grid-template-columns var(--neo-animation-base) var(--neo-ease-in-out);`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/mobile-first-responsive.js:604

**Pattern:** `box-shadow: var(--neo-shadow-lg);`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/mobile-first-responsive.js:613

**Pattern:** `background: var(--neo-color-warning-50);`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/mobile-first-responsive.js:614

**Pattern:** `border: 1px dashed var(--neo-color-warning-300);`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/mobile-first-responsive.js:625

**Pattern:** `transition: opacity var(--neo-animation-base) var(--neo-ease-in-out);`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/neo.js:8

**Pattern:** `const saved = localStorage.getItem('neo-theme');`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/neo.js:11

**Pattern:** `localStorage.setItem('neo-theme', val ? 'dark' : 'light');`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/ilan-create/dark-mode-toggle.js:117

**Pattern:** `.dark .neo-card {`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/ilan-create/dark-mode-toggle.js:123

**Pattern:** `.dark .neo-input,`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/ilan-create/dark-mode-toggle.js:124

**Pattern:** `.dark .neo-select,`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/ilan-create/dark-mode-toggle.js:125

**Pattern:** `.dark .neo-textarea {`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/ilan-create/dark-mode-toggle.js:131

**Pattern:** `.dark .neo-input:focus,`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/ilan-create/dark-mode-toggle.js:132

**Pattern:** `.dark .neo-select:focus,`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/ilan-create/dark-mode-toggle.js:133

**Pattern:** `.dark .neo-textarea:focus {`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/ilan-create/dark-mode-toggle.js:138

**Pattern:** `.dark .neo-btn-primary {`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/ilan-create/dark-mode-toggle.js:144

**Pattern:** `.dark .neo-btn-primary:hover {`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/ilan-create/dark-mode-toggle.js:149

**Pattern:** `.dark .neo-btn-secondary {`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/ilan-create/dark-mode-toggle.js:155

**Pattern:** `.dark .neo-btn-secondary:hover {`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/ilan-create/dark-mode-toggle.js:160

**Pattern:** `.dark .neo-table {`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/ilan-create/dark-mode-toggle.js:165

**Pattern:** `.dark .neo-table th {`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/ilan-create/dark-mode-toggle.js:171

**Pattern:** `.dark .neo-table td {`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/ilan-create/dark-mode-toggle.js:176

**Pattern:** `.dark .neo-table tbody tr:hover {`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/ilan-create/lazy-components.js:299

**Pattern:** `<button type="button" class="neo-neo-btn neo-btn-primary" onclick="initMap()">`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/ilan-create/lazy-components.js:389

**Pattern:** `<button type="button" class="neo-neo-btn neo-btn-primary" onclick="document.getElementById('photo-upload').click()">`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/ilan-create/location.js:1081

**Pattern:** `turkeyButton.className = 'neo-btn neo-btn-sm bg-blue-500 text-white';`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/services/AutoSaveManager.js:170

**Pattern:** `const currentStep = document.querySelector('.neo-step-card[style*="block"]');`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/dashboard-modernization.js:120

**Pattern:** `<button class="kanban-btn kanban-neo-btn neo-btn-primary" data-action="add">`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/dashboard-modernization.js:123

**Pattern:** `<button class="kanban-btn kanban-neo-btn neo-btn-secondary" data-action="refresh">`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/dashboard-modernization.js:867

**Pattern:** `.kanban-neo-btn neo-btn-primary {`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/dashboard-modernization.js:872

**Pattern:** `.kanban-neo-btn neo-btn-primary:hover {`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/dashboard-modernization.js:876

**Pattern:** `.kanban-neo-btn neo-btn-secondary {`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/dashboard-modernization.js:882

**Pattern:** `.kanban-neo-btn neo-btn-secondary:hover {`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/smart-ilan-create.js:962

**Pattern:** `<button type="button" class="neo-btn neo-btn--primary neo-btn--sm"`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/components/UnifiedPersonSelector.js:135

**Pattern:** `? `<label class="neo-label block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/components/UnifiedPersonSelector.js:145

**Pattern:** `class="neo-input w-full px-4 py-3 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/components/UnifiedPersonSelector.js:175

**Pattern:** `<button type="button" class="neo-btn neo-btn-primary btn-sm mt-2" onclick="this.showCreateModal()">`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/components/UnifiedPersonSelector.js:372

**Pattern:** `<div class="neo-result-item flex items-center justify-between p-4 hover:bg-purple-50 dark:hover:bg-purple-900/20 cursor-pointer border-b border-gray-200 dark:border-gray-600 last:border-b-0 transition-colors duration-200"`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/components/UnifiedPersonSelector.js:397

**Pattern:** `this.elements.resultsList.querySelectorAll('.neo-result-item').forEach((item) => {`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/app.js:626

**Pattern:** `gap: var(--neo-spacing-md);`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/app.js:627

**Pattern:** `transition: grid-template-columns var(--neo-animation-base) var(--neo-ease-in-out);`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/app.js:633

**Pattern:** `box-shadow: var(--neo-shadow-lg);`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/app.js:642

**Pattern:** `background: var(--neo-color-warning-50);`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/app.js:643

**Pattern:** `border: 1px dashed var(--neo-color-warning-300);`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/app.js:654

**Pattern:** `transition: opacity var(--neo-animation-base) var(--neo-ease-in-out);`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/layouts/neo.blade.php:205

**Pattern:** `<x-admin.neo-toast />`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/admin/neo-toast.blade.php:6

**Pattern:** `<x-admin.neo-toast />  <!-- Session mesajları için -->`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/admin/neo-toast.blade.php:7

**Pattern:** `<x-admin.neo-toast type="success" message="İşlem başarılı!" />  <!-- Manuel -->`

**Mesaj:** Neo Design System yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/StandardCheck.php:166

**Pattern:** `// jQuery usage`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### app/Console/Commands/StandardCheck.php:167

**Pattern:** `if (preg_match('/\$\([\'"]/', $content) || preg_match('/jQuery/', $content)) {`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### app/Console/Commands/StandardCheck.php:170

**Pattern:** `'type' => 'jQuery Usage',`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### app/Console/Commands/StandardCheck.php:171

**Pattern:** `'message' => 'jQuery kullanımı bulundu. Alpine.js veya Vanilla JS kullanın.',`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/performance-optimizer.js:108

**Pattern:** `clearTimeout(this.debounceTimers.get(func));`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/context7-ai-service.js:50

**Pattern:** `return this.cache.get(cacheKey);`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/context7-ai-service.js:80

**Pattern:** `return this.cache.get(cacheKey);`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/components/DynamicFeaturesLoader.js:164

**Pattern:** `this.renderFeatures(this.featuresCache.get(cacheKey));`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/form-validator.js:49

**Pattern:** `this.dynamicFields.get(triggerField).push({`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/form-validator.js:72

**Pattern:** `const behaviors = this.dynamicFields.get(triggerField);`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/unified-search-engine.js:275

**Pattern:** `return this.searchCache.get(cacheKey);`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/ai.js:66

**Pattern:** `data.baslik = formData.get('baslik') || '';`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/ai.js:67

**Pattern:** `data.aciklama = formData.get('aciklama') || '';`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/ai.js:75

**Pattern:** `data.fiyat = formData.get('fiyat') || '';`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/ai.js:76

**Pattern:** `data.para_birimi = formData.get('para_birimi') || 'TRY';`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/ai.js:77

**Pattern:** `data.metrekare = formData.get('metrekare') || '';`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/ai.js:82

**Pattern:** `odaSayisiElement?.selectedOptions?.[0]?.text || formData.get('oda_sayisi') || '';`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/ai.js:88

**Pattern:** `data.cadde_sokak = formData.get('cadde_sokak') || '';`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/skeleton-loader.js:412

**Pattern:** `this.skeletonTemplates.get(type) || this.skeletonTemplates.get('form');`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/core.js:478

**Pattern:** `const siteName = formData.get('site_name');`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/toast-notifications.js:119

**Pattern:** `const toastData = this.toasts.get(id);`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/master-integration.js:131

**Pattern:** `this.systems.get('performance').instance.optimize(target, type);`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/master-integration.js:137

**Pattern:** `this.systems.get('skeleton').instance.showSkeleton(element, type, duration);`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/master-integration.js:142

**Pattern:** `this.systems.get('darkMode').instance.toggle();`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/master-integration.js:148

**Pattern:** `this.systems.get('toast').instance.show(message, options);`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/master-integration.js:228

**Pattern:** `if (this.systems.get('performance')) {`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/master-integration.js:229

**Pattern:** `metrics.performance = this.systems.get('performance').instance.getPerformanceReport();`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/master-integration.js:255

**Pattern:** `if (this.systems.get('performance')) {`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/master-integration.js:256

**Pattern:** `this.systems.get('performance').instance.cleanup();`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/master-integration.js:260

**Pattern:** `if (this.systems.get('skeleton')) {`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/master-integration.js:261

**Pattern:** `this.systems.get('skeleton').instance.cleanup();`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/master-integration.js:265

**Pattern:** `if (this.systems.get('dragDrop')) {`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/master-integration.js:282

**Pattern:** `if (this.systems.get('toast')) {`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/master-integration.js:283

**Pattern:** `this.systems.get('toast').instance.error(`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/master-integration.js:297

**Pattern:** `if (this.systems.get('dashboard')) {`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/master-integration.js:309

**Pattern:** `if (this.systems.get('dashboard')) {`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/master-integration.js:361

**Pattern:** `if (this.systems.get('toast')) {`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/master-integration.js:362

**Pattern:** `this.systems.get('toast').instance.info(`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/master-integration.js:659

**Pattern:** `const system = this.systems.get(systemKey);`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/performance-optimizer.js:212

**Pattern:** `const cached = this.cache.get(key);`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/performance-optimizer.js:232

**Pattern:** `const cached = await this.get(cacheKey);`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/lazy-components.js:31

**Pattern:** `return this.loadedComponents.get(componentName);`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/lazy-components.js:91

**Pattern:** `return this.loadedComponents.get(componentName);`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/lazy-components.js:119

**Pattern:** `const component = this.loadedComponents.get(componentName);`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/lazy-components.js:271

**Pattern:** `return window.lazyLoader.loadedComponents.get(componentName);`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/touch-gestures.js:211

**Pattern:** `const handlers = this.gestureHandlers.get('swipe');`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/touch-gestures.js:230

**Pattern:** `const handlers = this.gestureHandlers.get('swipe');`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/touch-gestures.js:249

**Pattern:** `const handlers = this.gestureHandlers.get('swipe');`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/ilan-create/touch-gestures.js:263

**Pattern:** `const handlers = this.gestureHandlers.get('swipe');`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/admin/services/ValidationManager.js:220

**Pattern:** `const customValidator = this.customValidators.get(fieldName);`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/components/LocationManager.js:297

**Pattern:** `return this.state.cache.get(cacheKey);`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/components/LocationManager.js:321

**Pattern:** `return this.state.cache.get(cacheKey);`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/components/LocationManager.js:345

**Pattern:** `return this.state.cache.get(cacheKey);`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/js/components/KisiArama.js:1

**Pattern:** `$('#kisiArama').select2({`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/views/frontend/dynamic-form/index.blade.php:130

**Pattern:** `const kategori = formData.get('kategori');`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/views/frontend/dynamic-form/index.blade.php:131

**Pattern:** `const yayinTipi = formData.get('yayin_tipi');`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/views/admin/ai-category/index.blade.php:276

**Pattern:** `const task = formData.get(`examples[${i}][task]`);`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/views/admin/ai-category/index.blade.php:277

**Pattern:** `const expected = formData.get(`examples[${i}][expected_output]`);`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/views/admin/ai-category/index.blade.php:291

**Pattern:** `category_slug: formData.get('category_slug'),`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/views/admin/crm/dashboard.blade.php:340

**Pattern:** `const modal = $(``

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/views/admin/crm/dashboard.blade.php:370

**Pattern:** `$('body').append(modal);`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/views/admin/crm/dashboard.blade.php:374

**Pattern:** `$('#ai-modal-content').html(message);`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/views/admin/crm/dashboard.blade.php:375

**Pattern:** `$('#ai-modal .animate-pulse').remove();`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/views/admin/crm/dashboard.blade.php:379

**Pattern:** `$('#ai-modal').remove();`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/views/admin/layouts/neo.blade.php:193

**Pattern:** `<!-- ⚠️ GEÇICI: jQuery - Migration tamamlanana kadar (2025-10-21) -->`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/views/admin/layouts/neo.blade.php:194

**Pattern:** `<!-- FIXME: 6 dosya hala $.ajax() kullanıyor - Vanilla JS'e migrate edilecek -->`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/views/admin/layouts/neo.blade.php:199

**Pattern:** `console.log('⚠️ jQuery temporarily loaded - Migration in progress...');`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/views/admin/layouts/admin.blade.php:197

**Pattern:** `<!-- ⚠️ GEÇICI: jQuery - Migration tamamlanana kadar (2025-10-21) -->`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/views/admin/layouts/admin.blade.php:198

**Pattern:** `<!-- FIXME: 6 dosya hala $.ajax() kullanıyor - Vanilla JS'e migrate edilecek -->`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/views/admin/layouts/admin.blade.php:203

**Pattern:** `console.log('⚠️ jQuery temporarily loaded - Migration in progress...');`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/views/admin/property-type-manager/show.blade.php:528

**Pattern:** `const contentType = response.headers.get('content-type');`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/views/admin/talepler/partials/_form.blade.php:257

**Pattern:** `$(document).ready(function() {`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/views/admin/wikimapia-search/index.blade.php:691

**Pattern:** `const contentType = response.headers.get('content-type');`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/views/admin/wikimapia-search/index.blade.php:979

**Pattern:** `const contentType = response.headers.get('content-type');`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/views/admin/takim-yonetimi/takim/show.blade.php:737

**Pattern:** `// ✅ CONTEXT7: Vanilla JS - jQuery kaldırıldı`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/views/admin/takim-yonetimi/takim/show.blade.php:753

**Pattern:** `// ✅ CONTEXT7: Vanilla JS - jQuery kaldırıldı`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/views/admin/takim-yonetimi/takim/show.blade.php:765

**Pattern:** `const uzmanlikAlani = formData.get('uzmanlik_alani');`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/views/admin/takim-yonetimi/takim/show.blade.php:824

**Pattern:** `// ✅ CONTEXT7: Vanilla JS - jQuery kaldırıldı`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/views/admin/analytics/dashboard.blade.php:825

**Pattern:** `$('#exportModal').modal('show');`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/views/admin/analytics/dashboard.blade.php:835

**Pattern:** `$('#exportModal').modal('hide');`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/views/components/home/hero-emlakjet.blade.php:197

**Pattern:** `const ilanTuru = urlParams.get('ilan_turu');`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/views/components/gelismis-ilan-arama.blade.php:207

**Pattern:** `const q = urlParams.get('q');`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/views/ai/explore.blade.php:523

**Pattern:** `currentFilters[key] = urlParams.get(key);`

**Mesaj:** jQuery yasak - Vanilla JS kullanılmalı

**Replacement:** `Vanilla JS`

---

### resources/views/frontend/dynamic-form/index.blade.php:1

**Pattern:** `@extends('layouts.app')`

**Mesaj:** layouts.app yasak - admin.layouts.neo kullanılmalı

**Replacement:** `@extends('admin.layouts.neo')`

---

### app/Modules/Crm/Controllers/EtiketController.php:50

**Pattern:** `return redirect()->route('crm.etiketler.index')->with('success', 'Etiket başarıyla oluşturuldu.');`

**Mesaj:** crm.* routes yasak - admin.* kullanılmalı

**Replacement:** `route('admin.`

---

### app/Modules/Crm/Controllers/EtiketController.php:81

**Pattern:** `return redirect()->route('crm.etiketler.index')->with('success', 'Etiket başarıyla güncellendi.');`

**Mesaj:** crm.* routes yasak - admin.* kullanılmalı

**Replacement:** `route('admin.`

---

### app/Modules/Crm/Controllers/EtiketController.php:91

**Pattern:** `return redirect()->route('crm.etiketler.index')->with('error', 'Bu etiket kişilere bağlı olduğu için silinemez.');`

**Mesaj:** crm.* routes yasak - admin.* kullanılmalı

**Replacement:** `route('admin.`

---

### app/Modules/Crm/Controllers/EtiketController.php:95

**Pattern:** `return redirect()->route('crm.etiketler.index')->with('success', 'Etiket başarıyla silindi.');`

**Mesaj:** crm.* routes yasak - admin.* kullanılmalı

**Replacement:** `route('admin.`

---

### app/Modules/Crm/Controllers/AktiviteController.php:91

**Pattern:** `return redirect()->route('crm.kisiler.show', $validatedData['kisi_id'])`

**Mesaj:** crm.* routes yasak - admin.* kullanılmalı

**Replacement:** `route('admin.`

---

### app/Modules/Crm/Controllers/AktiviteController.php:95

**Pattern:** `return redirect()->route('crm.aktiviteler.index')`

**Mesaj:** crm.* routes yasak - admin.* kullanılmalı

**Replacement:** `route('admin.`

---

### app/Modules/Crm/Controllers/AktiviteController.php:144

**Pattern:** `return redirect()->route('crm.aktiviteler.index')->with('success', 'Aktivite başarıyla güncellendi.');`

**Mesaj:** crm.* routes yasak - admin.* kullanılmalı

**Replacement:** `route('admin.`

---

### app/Modules/Crm/Controllers/AktiviteController.php:155

**Pattern:** `return redirect()->route('crm.aktiviteler.index')->with('success', 'Aktivite başarıyla silindi.');`

**Mesaj:** crm.* routes yasak - admin.* kullanılmalı

**Replacement:** `route('admin.`

---

### app/Modules/Crm/Controllers/KisiController.php:114

**Pattern:** `return redirect()->route('crm.kisiler.index') // Rota adı crm.kisiler.index olmalı`

**Mesaj:** crm.* routes yasak - admin.* kullanılmalı

**Replacement:** `route('admin.`

---

### app/Modules/Crm/Controllers/KisiController.php:227

**Pattern:** `return redirect()->route('crm.kisiler.index') // Rota adı crm.kisiler.index olmalı`

**Mesaj:** crm.* routes yasak - admin.* kullanılmalı

**Replacement:** `route('admin.`

---

### app/Modules/Crm/Controllers/KisiController.php:248

**Pattern:** `return redirect()->route('crm.kisiler.index') // Rota adı crm.kisiler.index olmalı`

**Mesaj:** crm.* routes yasak - admin.* kullanılmalı

**Replacement:** `route('admin.`

---

### database/migrations/2025_10_10_174859_create_blog_categories_and_tags_tables.php:19

**Pattern:** `$table->integer('order')->default(0);`

**Mesaj:** Migration'da order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_10_175050_create_ozellikler_table.php:24

**Pattern:** `$table->integer('order')->default(0);`

**Mesaj:** Migration'da order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_10_175050_create_ozellikler_table.php:34

**Pattern:** `$table->index('order');`

**Mesaj:** Migration'da order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_15_172758_create_features_table.php:23

**Pattern:** `$table->integer('order')->default(0);`

**Mesaj:** Migration'da order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_05_000001_create_feature_assignments_table.php:30

**Pattern:** `$table->integer('order')->default(0);`

**Mesaj:** Migration'da order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_15_160340_create_feature_categories_table.php:20

**Pattern:** `$table->integer('order')->default(0);`

**Mesaj:** Migration'da order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_29_170932_create_alt_kategori_yayin_tipi_table.php:43

**Pattern:** `$table->integer('order')->default(0);`

**Mesaj:** Migration'da order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_10_073503_create_ilan_kategorileri_table.php:18

**Pattern:** `$table->integer('order')->default(0);`

**Mesaj:** Migration'da order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_10_073503_create_ilan_kategorileri_table.php:24

**Pattern:** `$table->index('order');`

**Mesaj:** Migration'da order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_24_210000_create_kategori_yayin_tipi_field_dependencies_table.php:60

**Pattern:** `$table->integer('order')->default(0)->comment('Sıralama');`

**Mesaj:** Migration'da order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_15_170751_create_etiketler_table.php:18

**Pattern:** `$table->integer('order')->default(0);`

**Mesaj:** Migration'da order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_05_133340_create_dashboard_widgets_table.php:26

**Pattern:** `$table->integer('order')->default(0);`

**Mesaj:** Migration'da order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_05_133340_create_dashboard_widgets_table.php:33

**Pattern:** `$table->index('order');`

**Mesaj:** Migration'da order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_10_174808_create_ilan_kategori_yayin_tipleri_table.php:20

**Pattern:** `$table->integer('order')->default(0);`

**Mesaj:** Migration'da order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_02_000001_create_polymorphic_features_system.php:23

**Pattern:** `$table->integer('order')->default(0);`

**Mesaj:** Migration'da order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_02_000001_create_polymorphic_features_system.php:64

**Pattern:** `$table->integer('order')->default(0);`

**Mesaj:** Migration'da order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_02_000001_create_polymorphic_features_system.php:94

**Pattern:** `$table->integer('order')->default(0);`

**Mesaj:** Migration'da order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_10_160010_create_ozellik_kategorileri_table.php:18

**Pattern:** `$table->integer('order')->default(0);`

**Mesaj:** Migration'da order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_10_160010_create_ozellik_kategorileri_table.php:24

**Pattern:** `$table->index('order');`

**Mesaj:** Migration'da order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_09_070721_rename_order_to_display_order_in_tables.php:72

**Pattern:** `$table->dropColumn('order');`

**Mesaj:** Migration'da order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_09_122119_rename_order_to_display_order_in_alt_kategori_yayin_tipi_table.php:66

**Pattern:** `$table->dropColumn('order');`

**Mesaj:** Migration'da order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_03_093414_create_photos_table.php:21

**Pattern:** `$table->integer('order')->default(0); // Sıralama`

**Mesaj:** Migration'da order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_11_03_093414_create_photos_table.php:33

**Pattern:** `$table->index('order');`

**Mesaj:** Migration'da order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_25_160239_create_kategori_yayin_tipi_field_dependencies_table.php:39

**Pattern:** `$table->integer('order')->default(0)->comment('Sıralama');`

**Mesaj:** Migration'da order → display_order kullanılmalı

**Replacement:** `display_order`

---

### database/migrations/2025_10_23_121215_create_site_ozellikleri_table.php:20

**Pattern:** `$table->integer('order')->default(0)->comment('Sıralama');`

**Mesaj:** Migration'da order → display_order kullanılmalı

**Replacement:** `display_order`

---

## high Violations

### app/Traits/HasActiveScope.php:23

**Pattern:** `* - is_active = true`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### app/Traits/HasActiveScope.php:48

**Pattern:** `// is_active field'ı varsa (legacy support)`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### app/Traits/HasActiveScope.php:49

**Pattern:** `if ($this->getConnection()->getSchemaBuilder()->hasColumn($this->getTable(), 'is_active')) {`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### app/Traits/HasActiveScope.php:50

**Pattern:** `return $query->where('is_active', true);`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### app/Models/AICoreSystem.php:26

**Pattern:** `'is_active', // Context7: Tablo yok, enabled kaldırıldı`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### app/Models/AICoreSystem.php:32

**Pattern:** `'is_active' => 'boolean', // Context7: enabled kaldırıldı`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### app/Models/AICoreSystem.php:42

**Pattern:** `return $query->where('is_active', true); // Context7: enabled kaldırıldı`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### app/Models/DashboardWidget.php:33

**Pattern:** `'is_active',`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### app/Models/DashboardWidget.php:39

**Pattern:** `'is_active' => 'boolean',`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### app/Models/DashboardWidget.php:60

**Pattern:** `return $query->where('is_active', true);`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### app/Http/Controllers/Admin/TakvimController.php:506

**Pattern:** `'is_active' => true,`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### app/Http/Controllers/Admin/DanismanAIController.php:277

**Pattern:** `'is_active' => 'boolean'`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### app/Http/Controllers/Api/BookingRequestController.php:202

**Pattern:** `->where('is_active', true)`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### app/Http/Controllers/Api/SeasonController.php:62

**Pattern:** `'is_active' => 'nullable|boolean',`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### app/Http/Controllers/Api/SeasonController.php:102

**Pattern:** `'is_active' => 'nullable|boolean',`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### app/Services/AIPromptManager.php:42

**Pattern:** `'is_active' => true,`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_10_25_103504_create_ai_core_system_table.php:22

**Pattern:** `$table->boolean('is_active')->default(true);`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_03_095932_create_seasons_table.php:51

**Pattern:** `$table->boolean('is_active')->default(true); // Aktif mi?`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_03_095932_create_seasons_table.php:64

**Pattern:** `$table->index('is_active');`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_03_095932_create_seasons_table.php:65

**Pattern:** `$table->index(['ilan_id', 'is_active']);`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_05_133340_create_dashboard_widgets_table.php:25

**Pattern:** `$table->boolean('is_active')->default(true);`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_05_133340_create_dashboard_widgets_table.php:32

**Pattern:** `$table->index(['user_id', 'is_active']);`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:19

**Pattern:** `'blog_categories' => 'is_active',`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:20

**Pattern:** `'blog_tags' => 'is_active',`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:23

**Pattern:** `'feature_categories' => 'is_active',`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:24

**Pattern:** `'features' => 'is_active',`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:25

**Pattern:** `'ilan_kategori_yayin_tipleri' => 'is_active',`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:26

**Pattern:** `'ilan_kategorileri' => 'is_active',`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:27

**Pattern:** `'users' => 'is_active',`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:30

**Pattern:** `'ai_knowledge_base' => 'is_active',`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:33

**Pattern:** `'categories' => 'is_active',`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:34

**Pattern:** `'commission_rates' => 'is_active',`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:35

**Pattern:** `'exchange_rates' => 'is_active',`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:36

**Pattern:** `'expertise_areas' => 'is_active',`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:42

**Pattern:** `'language_settings' => 'is_active',`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:44

**Pattern:** `'nearby_place_categories' => 'is_active',`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:47

**Pattern:** `'people' => 'is_active',`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:48

**Pattern:** `'property_types' => 'is_active',`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:50

**Pattern:** `'site_settings' => 'is_active',`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:51

**Pattern:** `'tax_rates' => 'is_active',`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:118

**Pattern:** `'blog_categories' => ['is_active' => 'tinyint'],`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:119

**Pattern:** `'blog_tags' => ['is_active' => 'tinyint'],`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:122

**Pattern:** `'feature_categories' => ['is_active' => 'tinyint'],`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:123

**Pattern:** `'features' => ['is_active' => 'tinyint'],`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:124

**Pattern:** `'ilan_kategori_yayin_tipleri' => ['is_active' => 'tinyint'],`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:125

**Pattern:** `'ilan_kategorileri' => ['is_active' => 'tinyint'],`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/migrations/2025_11_09_063338_remove_old_status_columns_from_tables.php:126

**Pattern:** `'users' => ['is_active' => 'tinyint'],`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### database/seeders/Context7MasterSeeder.php:29

**Pattern:** `* - Türkçe alan adları yasak (durum, aktif, is_active)`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### resources/views/admin/ilanlar/components/season-pricing-manager.blade.php:92

**Pattern:** `x-model="season.is_active"`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### resources/views/admin/ilanlar/components/season-pricing-manager.blade.php:180

**Pattern:** `<p class="text-3xl font-bold text-green-600 dark:text-green-400" x-text="seasons.filter(s => s.is_active === 1).length"></p>`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### resources/views/admin/ilanlar/components/season-pricing-manager.blade.php:246

**Pattern:** `is_active: 1`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### resources/views/admin/ai-monitor/index.blade.php:393

**Pattern:** `<code class="bg-blue-100 px-1 rounded-lg">is_active</code>,`

**Mesaj:** is_active → status kullanılmalı

**Replacement:** `status`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:119

**Pattern:** `$html .= '<button type="button" class="neo-btn neo-btn-ai" onclick="fillAllWithAI()">';`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:122

**Pattern:** `$html .= '<button type="button" class="neo-btn neo-btn-secondary" onclick="clearAllFields()">';`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Frontend/DynamicFormController.php:129

**Pattern:** `$html .= '<button type="submit" class="neo-btn neo-btn-primary">';`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Http/Controllers/Admin/PageAnalyzerController.php:530

**Pattern:** `'action' => 'Neo Design System classes ile değiştirin (btn- → neo-btn, card- → neo-card)',`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Services/Analysis/EmlakYonetimPageAnalyzer.php:324

**Pattern:** `if (strpos($viewContent, 'neo-') !== false && strpos($viewContent, 'btn-') === false) {`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/AnalyzePagesComplete.php:672

**Pattern:** `$neoClasses = ['neo-card', 'neo-btn', 'neo-btn-primary', 'neo-btn-outline', 'neo-grid', 'neo-container'];`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/AnalyzePagesComplete.php:673

**Pattern:** `$legacyClasses = ['btn-', 'card-', 'bg-blue-', 'bg-green-', 'bg-red-'];`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/YalihanBekciEnforce.php:63

**Pattern:** `'/class=[\'"].*btn-primary/',`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/YalihanBekciEnforce.php:64

**Pattern:** `'/class=[\'"].*btn-secondary/',`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/YalihanBekciEnforce.php:65

**Pattern:** `'/class=[\'"].*form-control/',`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/YalihanBekciEnforce.php:234

**Pattern:** `'btn-primary' => 'neo-btn neo-btn-primary',`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/YalihanBekciEnforce.php:235

**Pattern:** `'btn-secondary' => 'neo-btn neo-btn-secondary',`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/YalihanBekciEnforce.php:236

**Pattern:** `'form-control' => 'neo-input',`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/BootstrapToNeoMigration.php:23

**Pattern:** `'btn-primary' => 'neo-btn neo-btn-primary',`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/BootstrapToNeoMigration.php:24

**Pattern:** `'btn-secondary' => 'neo-btn neo-btn-secondary',`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/BootstrapToNeoMigration.php:25

**Pattern:** `'btn-success' => 'neo-btn neo-btn-success',`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/BootstrapToNeoMigration.php:26

**Pattern:** `'btn-danger' => 'neo-btn neo-btn-danger',`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/BootstrapToNeoMigration.php:27

**Pattern:** `'btn-warning' => 'neo-btn neo-btn-warning',`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/BootstrapToNeoMigration.php:28

**Pattern:** `'btn-info' => 'neo-btn neo-btn-info',`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/BootstrapToNeoMigration.php:31

**Pattern:** `'form-control' => 'neo-input',`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/BootstrapToNeoMigration.php:35

**Pattern:** `// 'card-header' => 'neo-card-header',`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/BootstrapToNeoMigration.php:36

**Pattern:** `// 'card-body' => 'neo-card-body',`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### app/Console/Commands/BootstrapToNeoMigration.php:137

**Pattern:** `// "btn-primary" → "neo-btn neo-btn-primary btn-primary"`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/ai.js:55

**Pattern:** `<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/ai.js:231

**Pattern:** `<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/ai-settings/provider-test.js:85

**Pattern:** `const btn = document.querySelector(`.btn-test[data-provider="${provider}"]`);`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/ilan-create/dark-mode-toggle.js:138

**Pattern:** `.dark .neo-btn-primary {`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/ilan-create/dark-mode-toggle.js:144

**Pattern:** `.dark .neo-btn-primary:hover {`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/ilan-create/dark-mode-toggle.js:149

**Pattern:** `.dark .neo-btn-secondary {`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/ilan-create/dark-mode-toggle.js:155

**Pattern:** `.dark .neo-btn-secondary:hover {`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/ilan-create/lazy-components.js:299

**Pattern:** `<button type="button" class="neo-neo-btn neo-btn-primary" onclick="initMap()">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/ilan-create/lazy-components.js:389

**Pattern:** `<button type="button" class="neo-neo-btn neo-btn-primary" onclick="document.getElementById('photo-upload').click()">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/ilan-create/touch-gestures.js:286

**Pattern:** `element.querySelector('.btn-next') ||`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/ilan-create/touch-gestures.js:302

**Pattern:** `element.querySelector('.btn-prev') ||`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/ilan-create/location.js:1081

**Pattern:** `turkeyButton.className = 'neo-btn neo-btn-sm bg-blue-500 text-white';`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/dashboard-modernization.js:120

**Pattern:** `<button class="kanban-btn kanban-neo-btn neo-btn-primary" data-action="add">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/dashboard-modernization.js:123

**Pattern:** `<button class="kanban-btn kanban-neo-btn neo-btn-secondary" data-action="refresh">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/dashboard-modernization.js:867

**Pattern:** `.kanban-neo-btn neo-btn-primary {`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/dashboard-modernization.js:872

**Pattern:** `.kanban-neo-btn neo-btn-primary:hover {`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/dashboard-modernization.js:876

**Pattern:** `.kanban-neo-btn neo-btn-secondary {`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/dashboard-modernization.js:882

**Pattern:** `.kanban-neo-btn neo-btn-secondary:hover {`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/admin/smart-ilan-create.js:962

**Pattern:** `<button type="button" class="neo-btn neo-btn--primary neo-btn--sm"`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/components/UnifiedPersonSelector.js:175

**Pattern:** `<button type="button" class="neo-btn neo-btn-primary btn-sm mt-2" onclick="this.showCreateModal()">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/js/components/UnifiedPersonSelector.js:218

**Pattern:** `<button type="button" id="${containerId}-create" class="btn-outline text-sm">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/frontend/ilanlar/international.blade.php:161

**Pattern:** `<x-frontend.property-card-global :property="$property" :currency="$currency ?? 'USD'" :converted-price="$property->converted_price ?? null" />`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/frontend/portfolio/index.blade.php:114

**Pattern:** `<x-frontend.property-card-global`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/setup-api-token.blade.php:9

**Pattern:** `<h1 class="stat-card-value mb-6">🔑 API Token Setup</h1>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/ai-settings/index.blade.php:102

**Pattern:** `class="btn-test ml-2 px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition-colors">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/etiketler/edit.blade.php:19

**Pattern:** `<div class="admin-card-header">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/etiketler/edit.blade.php:24

**Pattern:** `<div class="admin-card-body">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/telegram-bot/index.blade.php:10

**Pattern:** `<h1 class="stat-card-value flex items-center">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/ilan-kategorileri/stats.blade.php:102

**Pattern:** `<p class="stat-card-value text-green-600 dark:text-green-400">{{ number_format($stats['active']) }}`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/ilan-kategorileri/stats.blade.php:125

**Pattern:** `<p class="stat-card-value text-red-600 dark:text-red-400">{{ number_format($stats['inactive']) }}</p>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/ilan-kategorileri/stats.blade.php:149

**Pattern:** `<p class="stat-card-value text-purple-600 dark:text-purple-400">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/talep-portfolyo/index.blade.php:184

**Pattern:** `<div class="ds-card-body p-0">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/crm/customers/index.blade.php:83

**Pattern:** `<div class="ds-card-header">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/crm/customers/index.blade.php:84

**Pattern:** `<h3 class="ds-card-title">🔍 Filtreler ve Arama</h3>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/crm/customers/index.blade.php:86

**Pattern:** `<div class="ds-card-body">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/crm/customers/index.blade.php:155

**Pattern:** `<div class="ds-card-header">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/crm/customers/index.blade.php:156

**Pattern:** `<h3 class="ds-card-title">📋 Müşteri Listesi</h3>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/crm/customers/index.blade.php:157

**Pattern:** `<div class="ds-card-actions">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/crm/customers/index.blade.php:161

**Pattern:** `<div class="ds-card-body">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/ayarlar/location.blade.php:9

**Pattern:** `<div class="card-body">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/smart-calculator/index.blade.php:28

**Pattern:** `class="sv-card-padding sv-shadow-md rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 mb-8">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/smart-calculator/index.blade.php:40

**Pattern:** `<div class="subtle-vibrant-blue sv-header rounded-lg sv-shadow-sm sv-card-hover sv-card-padding mb-6">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/smart-calculator/index.blade.php:53

**Pattern:** `<div class="calculation-type-card sv-card-hover" :class="{ 'selected': selectedType === type }"`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/smart-calculator/index.blade.php:55

**Pattern:** `<div class="card-icon">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/smart-calculator/index.blade.php:58

**Pattern:** `<div class="card-title" x-text="name"></div>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/smart-calculator/index.blade.php:65

**Pattern:** `<div class="subtle-vibrant-green sv-header rounded-lg sv-shadow-sm sv-card-hover sv-card-padding mb-6">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/smart-calculator/index.blade.php:80

**Pattern:** `<div class="calculation-type-card sv-card-hover" :class="{ 'selected': selectedType === type }"`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/smart-calculator/index.blade.php:82

**Pattern:** `<div class="card-icon">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/smart-calculator/index.blade.php:85

**Pattern:** `<div class="card-title" x-text="name"></div>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/smart-calculator/index.blade.php:92

**Pattern:** `<div class="subtle-vibrant-purple sv-header rounded-lg sv-shadow-sm sv-card-hover sv-card-padding mb-6">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/smart-calculator/index.blade.php:104

**Pattern:** `<div class="calculation-type-card sv-card-hover" :class="{ 'selected': selectedType === type }"`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/smart-calculator/index.blade.php:106

**Pattern:** `<div class="card-icon">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/smart-calculator/index.blade.php:109

**Pattern:** `<div class="card-title" x-text="name"></div>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/smart-calculator/index.blade.php:116

**Pattern:** `<div class="subtle-vibrant-orange sv-header rounded-lg sv-shadow-sm sv-card-hover sv-card-padding mb-6">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/smart-calculator/index.blade.php:128

**Pattern:** `<div class="calculation-type-card sv-card-hover" :class="{ 'selected': selectedType === type }"`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/smart-calculator/index.blade.php:130

**Pattern:** `<div class="card-icon">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/smart-calculator/index.blade.php:133

**Pattern:** `<div class="card-title" x-text="name"></div>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/smart-calculator/index.blade.php:140

**Pattern:** `<div class="subtle-vibrant-gray sv-header rounded-lg sv-shadow-sm sv-card-hover sv-card-padding">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/smart-calculator/index.blade.php:155

**Pattern:** `<div class="calculation-type-card sv-card-hover"`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/smart-calculator/index.blade.php:157

**Pattern:** `<div class="card-icon">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/smart-calculator/index.blade.php:160

**Pattern:** `<div class="card-title" x-text="name"></div>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/smart-calculator/index.blade.php:171

**Pattern:** `class="sv-header rounded-xl sv-shadow-md sv-card-hover sv-card-padding mb-8">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/smart-calculator/index.blade.php:323

**Pattern:** `<div class="admin-card-header">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/smart-calculator/index.blade.php:324

**Pattern:** `<h3 class="admin-card-title">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/smart-calculator/index.blade.php:329

**Pattern:** `<div class="admin-card-body">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/smart-calculator/index.blade.php:344

**Pattern:** `<button @click="shareResult()" class="btn-neutral sv-button-padding sv-button-hover touch-target-optimized touch-target-optimized touch-target-optimized touch-target-optimized">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/smart-calculator/index.blade.php:384

**Pattern:** `class="btn btn-sm admin-button admin-button-primary touch-target-optimized touch-target-optimized touch-target-optimized touch-target-optimized">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/smart-calculator/index.blade.php:426

**Pattern:** `class="btn btn-sm admin-button admin-button-primary touch-target-optimized touch-target-optimized touch-target-optimized touch-target-optimized">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/smart-calculator/index.blade.php:431

**Pattern:** `class="btn btn-sm admin-button admin-button-danger touch-target-optimized touch-target-optimized touch-target-optimized touch-target-optimized">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/blog/comments/index.blade.php:32

**Pattern:** `<p class="stat-card-value">{{ $totalComments }}</p>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/blog/comments/index.blade.php:47

**Pattern:** `<p class="stat-card-value">{{ $stats['approved'] ?? 0 }}</p>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/blog/comments/index.blade.php:62

**Pattern:** `<p class="stat-card-value">{{ $stats['pending'] ?? 0 }}</p>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/blog/comments/index.blade.php:78

**Pattern:** `<p class="stat-card-value">{{ $stats['rejected'] ?? 0 }}</p>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/blog/index.blade.php:103

**Pattern:** `<a href="{{ route('admin.blog.posts.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg btn-sm touch-target-optimized touch-target-optimized">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/blog/analytics.blade.php:9

**Pattern:** `.btn-modern {`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/blog/analytics.blade.php:13

**Pattern:** `.btn-modern-primary {`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/blog/analytics.blade.php:17

**Pattern:** `.btn-modern-secondary {`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/blog/posts/show.blade.php:9

**Pattern:** `.btn-modern {`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/blog/posts/show.blade.php:13

**Pattern:** `.btn-modern-primary {`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/blog/posts/show.blade.php:17

**Pattern:** `.btn-modern-secondary {`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/blog/posts/show.blade.php:334

**Pattern:** `<div class="stat-card-value text-blue-600">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/blog/posts/show.blade.php:339

**Pattern:** `<div class="stat-card-value text-green-600">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/blog/posts/show.blade.php:344

**Pattern:** `<div class="stat-card-value text-purple-600">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/blog/posts/show.blade.php:349

**Pattern:** `<div class="stat-card-value text-orange-600">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/blog/tags/index.blade.php:192

**Pattern:** `<div class="stat-card-value text-blue-600 dark:text-blue-400">{{ $tags->count() }}</div>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/blog/tags/index.blade.php:196

**Pattern:** `<div class="stat-card-value text-green-600 dark:text-green-400">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/blog/tags/index.blade.php:206

**Pattern:** `<div class="stat-card-value text-purple-600 dark:text-purple-400">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/blog/categories/index.blade.php:278

**Pattern:** `<div class="stat-card-value text-blue-600 dark:text-blue-400">{{ $categories->count() }}</div>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/blog/categories/index.blade.php:282

**Pattern:** `<div class="stat-card-value text-green-600 dark:text-green-400">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/blog/categories/index.blade.php:292

**Pattern:** `<div class="stat-card-value text-purple-600 dark:text-purple-400">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/blog/categories/edit.blade.php:10

**Pattern:** `<div class="admin-card-body">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/blog/categories/edit.blade.php:28

**Pattern:** `<div class="admin-card-header">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/blog/categories/edit.blade.php:31

**Pattern:** `<div class="admin-card-body">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/dashboard/danisman.blade.php:172

**Pattern:** `<h2 class="stat-card-value flex items-center">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/dashboard/danisman.blade.php:220

**Pattern:** `<h2 class="stat-card-value flex items-center">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/dashboard/danisman.blade.php:265

**Pattern:** `<h2 class="stat-card-value mb-6 flex items-center">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/dashboard/admin.blade.php:20

**Pattern:** `<button class="btn-modern btn-modern-primary touch-target-optimized" onclick="generatePerformanceReport()">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/dashboard/admin.blade.php:28

**Pattern:** `<button class="btn-modern btn-modern-secondary touch-target-optimized" onclick="optimizeSystem()">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/dashboard/admin.blade.php:48

**Pattern:** `<p class="stat-card-value">{{ $quickStats['total_ilanlar'] ?? 0 }}</p>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/dashboard/admin.blade.php:114

**Pattern:** `<h2 class="stat-card-value mb-6 flex items-center">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/dashboard/admin.blade.php:141

**Pattern:** `<h2 class="stat-card-value flex items-center">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/dashboard/admin.blade.php:149

**Pattern:** `<a href="{{ route('admin.ilanlar.index') }}" class="btn-modern btn-modern-primary">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/dashboard/admin.blade.php:184

**Pattern:** `<h2 class="stat-card-value mb-6 flex items-center">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/dashboard/user.blade.php:130

**Pattern:** `<h2 class="stat-card-value mb-6 flex items-center">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/dashboard/user.blade.php:213

**Pattern:** `<h2 class="stat-card-value mb-6 flex items-center">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/dashboard/user.blade.php:247

**Pattern:** `<h2 class="stat-card-value mb-6 flex items-center">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/kisiler/takip.blade.php:53

**Pattern:** `<p class="stat-card-value dark:text-gray-100">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/kisiler/takip.blade.php:71

**Pattern:** `<p class="stat-card-value dark:text-gray-100">{{ $istatistikler['status_musteri'] }}`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/kisiler/takip.blade.php:89

**Pattern:** `<p class="stat-card-value dark:text-gray-100">{{ $istatistikler['yeni_musteri'] }}`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/kisiler/takip.blade.php:108

**Pattern:** `<p class="stat-card-value dark:text-gray-100">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/ilanlar/my-listings.blade.php:346

**Pattern:** `.btn-modern {`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/ilanlar/my-listings.blade.php:350

**Pattern:** `.btn-modern-primary {`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/ilanlar/my-listings.blade.php:354

**Pattern:** `.btn-modern-secondary {`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/ilanlar/edit.blade.php:584

**Pattern:** `const btnStandard = document.getElementById('btn-map-standard');`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/ilanlar/edit.blade.php:585

**Pattern:** `const btnSatellite = document.getElementById('btn-map-satellite');`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/ilanlar/create.blade.php:550

**Pattern:** `const btnStandard = document.getElementById('btn-map-standard');`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/ilanlar/create.blade.php:551

**Pattern:** `const btnSatellite = document.getElementById('btn-map-satellite');`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/talepler/edit.blade.php:19

**Pattern:** `class="inline-flex items-center px-4 py-2 btn-outline  font-semibold rounded-lg transition-colors touch-target-optimized touch-target-optimized">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/talepler/analiz_detay.blade.php:10

**Pattern:** `<div class="admin-card-header">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/talepler/analiz_detay.blade.php:13

**Pattern:** `<div class="admin-card-body">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/talepler/analiz_detay.blade.php:38

**Pattern:** `<div class="card-header">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/talepler/analiz_detay.blade.php:41

**Pattern:** `<div class="card-body">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/talepler/analiz_detay.blade.php:56

**Pattern:** `<div class="card-body">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/talepler/analiz_detay.blade.php:72

**Pattern:** `class="btn btn-sm inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg touch-target-optimized touch-target-optimized">Detaylar</a>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/talepler/show.blade.php:25

**Pattern:** `.detail-card-header {`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/talepler/show.blade.php:35

**Pattern:** `.dark .detail-card-header {`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/talepler/show.blade.php:120

**Pattern:** `<h3 class="detail-card-header dark:text-gray-200">Talep ve Müşteri Bilgileri</h3>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/talepler/show.blade.php:155

**Pattern:** `<h3 class="detail-card-header dark:text-gray-200">Aranan Konum</h3>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/talepler/show.blade.php:179

**Pattern:** `<h3 class="detail-card-header dark:text-gray-200">Aranan Kriterler</h3>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/talepler/show.blade.php:197

**Pattern:** `<h3 class="detail-card-header dark:text-gray-200">İstenen Ek Özellikler</h3>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/talepler/show.blade.php:211

**Pattern:** `<a href="{{ route('admin.talepler.eslesen', ['talep' => $talep->id]) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg btn-lg touch-target-optimized touch-target-optimized">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/gorevler/edit.blade.php:360

**Pattern:** `<button type="button" class="inline-flex items-center px-4 py-2 border-2 border-green-500 text-green-700 dark:text-green-400 rounded-lg hover:bg-green-50 dark:hover:bg-green-900 hover:scale-105 transition-all duration-200 btn-sm touch-target-optimized touch-target-optimized"`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/gorevler/raporlar.blade.php:22

**Pattern:** `<div class="btn-group touch-target-optimized touch-target-optimized" role="group">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/gorevler/raporlar.blade.php:44

**Pattern:** `<div class="card-body">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/gorevler/raporlar.blade.php:61

**Pattern:** `<div class="card-body">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/gorevler/raporlar.blade.php:78

**Pattern:** `<div class="card-body">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/gorevler/raporlar.blade.php:95

**Pattern:** `<div class="card-body">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/gorevler/raporlar.blade.php:116

**Pattern:** `<div class="card-header">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/gorevler/raporlar.blade.php:117

**Pattern:** `<h5 class="card-title mb-0">Görev Durumu Dağılımı</h5>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/gorevler/raporlar.blade.php:119

**Pattern:** `<div class="card-body">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/gorevler/raporlar.blade.php:126

**Pattern:** `<div class="card-header">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/gorevler/raporlar.blade.php:127

**Pattern:** `<h5 class="card-title mb-0">Aylık Görev Trendi</h5>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/gorevler/raporlar.blade.php:129

**Pattern:** `<div class="card-body">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/gorevler/raporlar.blade.php:138

**Pattern:** `<div class="card-header">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/gorevler/raporlar.blade.php:139

**Pattern:** `<h5 class="card-title mb-0">Danışman Performans Raporu</h5>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/gorevler/raporlar.blade.php:141

**Pattern:** `<div class="card-body">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/takim/index.blade.php:58

**Pattern:** `<p class="stat-card-value dark:text-gray-100">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/takim/index.blade.php:76

**Pattern:** `<p class="stat-card-value dark:text-gray-100">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/takim/edit.blade.php:41

**Pattern:** `<div class="card-header">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/takim/edit.blade.php:42

**Pattern:** `<h3 class="card-title">Takım Üyesi Bilgileri</h3>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/takim/edit.blade.php:44

**Pattern:** `<div class="card-body">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/takim/takim-performans.blade.php:22

**Pattern:** `<div class="btn-group touch-target-optimized touch-target-optimized" role="group">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/takim/takim-performans.blade.php:42

**Pattern:** `<div class="card-body">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/takim/takim-performans.blade.php:87

**Pattern:** `<div class="card-body">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/takim/takim-performans.blade.php:104

**Pattern:** `<div class="card-body">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/takim/takim-performans.blade.php:121

**Pattern:** `<div class="card-body">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/takim/takim-performans.blade.php:138

**Pattern:** `<div class="card-body">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/takim/takim-performans.blade.php:159

**Pattern:** `<div class="card-header">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/takim/takim-performans.blade.php:160

**Pattern:** `<h5 class="card-title mb-0">Takım Performans Karşılaştırması</h5>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/takim/takim-performans.blade.php:162

**Pattern:** `<div class="card-body">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/takim/takim-performans.blade.php:169

**Pattern:** `<div class="card-header">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/takim/takim-performans.blade.php:170

**Pattern:** `<h5 class="card-title mb-0">Aylık Görev Trendi</h5>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/takim/takim-performans.blade.php:172

**Pattern:** `<div class="card-body">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/takim/takim-performans.blade.php:181

**Pattern:** `<div class="card-header">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/takim/takim-performans.blade.php:182

**Pattern:** `<h5 class="card-title mb-0">Takım Detay Performans Raporu</h5>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/takim/takim-performans.blade.php:184

**Pattern:** `<div class="card-body">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/takim/takim-performans.blade.php:260

**Pattern:** `<div class="card-header">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/takim/takim-performans.blade.php:261

**Pattern:** `<h5 class="card-title mb-0">Danışman Performans Detayı</h5>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/takim/takim-performans.blade.php:263

**Pattern:** `<div class="card-body">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/takim-yonetimi/takim/show.blade.php:547

**Pattern:** `<button type="button" class="btn-close btn-close-white touch-target-optimized touch-target-optimized" data-bs-dismiss="modal"`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/reports/danisman.blade.php:118

**Pattern:** `<span class="stat-card-value text-blue-600">{{ $stats['monthly_ilanlar'] ?? 0 }}</span>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/reports/danisman.blade.php:131

**Pattern:** `<span class="stat-card-value text-green-600">{{ $stats['monthly_musteriler'] ?? 0 }}</span>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/reports/danisman.blade.php:143

**Pattern:** `<h2 class="stat-card-value mb-6 flex items-center">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/reports/danisman.blade.php:222

**Pattern:** `<h2 class="stat-card-value mb-6 flex items-center">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/reports/admin.blade.php:119

**Pattern:** `<span class="stat-card-value text-blue-600">{{ $stats['monthly_ilanlar'] ?? 0 }}</span>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/reports/admin.blade.php:131

**Pattern:** `<span class="stat-card-value text-green-600">{{ $stats['monthly_kisiler'] ?? 0 }}</span>`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/reports/admin.blade.php:142

**Pattern:** `<h2 class="stat-card-value mb-6 flex items-center">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/reports/admin.blade.php:221

**Pattern:** `<h2 class="stat-card-value mb-6 flex items-center">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/reports/user.blade.php:127

**Pattern:** `<h2 class="stat-card-value mb-6 flex items-center">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/reports/user.blade.php:218

**Pattern:** `<h2 class="stat-card-value mb-6 flex items-center">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/admin/reports/user.blade.php:302

**Pattern:** `<h2 class="stat-card-value mb-6 flex items-center">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/blog/tag.blade.php:258

**Pattern:** `<button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg btn-sm">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/blog/category.blade.php:222

**Pattern:** `<button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg btn-sm">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/blog/show.blade.php:418

**Pattern:** `class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg btn-sm inline-flex items-center gap-1">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/blog/show.blade.php:424

**Pattern:** `class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 btn-sm inline-flex items-center gap-1">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/ilan-card-rental.blade.php:13

**Pattern:** `<div class="ilan-card-rental bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/home/featured-properties.blade.php:26

**Pattern:** `<div class="ds-card-hover overflow-hidden group ds-fade-in">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/home/featured-projects.blade.php:16

**Pattern:** `<div class="ds-card ds-card-hover group">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/context7/map-picker.blade.php:44

**Pattern:** `<button type="button" onclick="VanillaLocationManager?.setMapType?.('standard')" id="btn-map-standard"`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/context7/map-picker.blade.php:54

**Pattern:** `id="btn-map-satellite"`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/context7/card.blade.php:24

**Pattern:** `<div class="card-header px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/context7/card.blade.php:29

**Pattern:** `<div class="card-body p-6">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/context7/card.blade.php:34

**Pattern:** `<div class="card-footer px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/listing-card.blade.php:24

**Pattern:** `<a href="{{ route('ilanlar.show', $ilan->id) }}" class="action-btn action-btn-view" title="Görüntüle">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/listing-card.blade.php:27

**Pattern:** `<a href="{{ route('ilanlar.edit', $ilan->id) }}" class="action-btn action-btn-edit" title="Düzenle">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/listing-card.blade.php:30

**Pattern:** `<button onclick="deleteListing({{ $ilan->id }})" class="action-btn action-btn-delete"`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/listing-card.blade.php:191

**Pattern:** `.action-btn-view {`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/listing-card.blade.php:195

**Pattern:** `.action-btn-edit {`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/listing-card.blade.php:199

**Pattern:** `.action-btn-delete {`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/advanced-search.blade.php:167

**Pattern:** `<button type="button" @click="bulkAssignLabels()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 btn-sm">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/advanced-search.blade.php:170

**Pattern:** `<button type="button" @click="bulkUpdateStatus()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 btn-sm">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/advanced-search.blade.php:173

**Pattern:** `<button type="button" @click="bulkDelete()" class="btn-danger btn-sm">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/admin/button.blade.php:10

**Pattern:** `'primary' => 'btn-primary',`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/admin/button.blade.php:11

**Pattern:** `'secondary' => 'btn-secondary',`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/admin/button.blade.php:12

**Pattern:** `'danger' => 'btn-danger',`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/admin/button.blade.php:13

**Pattern:** `'ghost' => 'btn-ghost',`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/form-builder/field.blade.php:155

**Pattern:** `<button type="button" @click="getCurrentLocation()" class="mt-2 w-full btn btn-outline btn-sm">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/form-builder/field.blade.php:199

**Pattern:** `<button @click="fields.push({key: '', value: ''})" type="button" class="mt-3 btn btn-outline btn-sm">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/ai-smart-search.blade.php:29

**Pattern:** `<div class="ds-card-glass p-8 mb-12 ds-shadow-glow">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/context7-live-search.blade.php:16

**Pattern:** `'class' => 'form-control',`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/context7-live-search.blade.php:33

**Pattern:** `$class = $class ?? 'form-control';`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/filter-panel.blade.php:98

**Pattern:** `.ultra-modern-btn-success {`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/filter-panel.blade.php:110

**Pattern:** `.ultra-modern-btn-success:hover {`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/filter-panel.blade.php:115

**Pattern:** `.ultra-modern-btn-purple {`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/filter-panel.blade.php:127

**Pattern:** `.ultra-modern-btn-purple:hover {`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/filter-panel.blade.php:259

**Pattern:** `class="ultra-modern-btn-success flex items-center space-x-2">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/components/filter-panel.blade.php:267

**Pattern:** `class="ultra-modern-btn-purple flex items-center space-x-2">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

### resources/views/ilans/index.blade.php:220

**Pattern:** `<button class="admin-tab-btn admin-tab-btn-active" data-tab="all" onclick="switchTab('all')">`

**Mesaj:** Bootstrap yasak - Tailwind CSS kullanılmalı

**Replacement:** `Tailwind CSS`

---

