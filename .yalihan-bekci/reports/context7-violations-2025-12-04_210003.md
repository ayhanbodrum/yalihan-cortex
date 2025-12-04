# Context7 Violation Report

**Generated:** 2025-12-04 21:00:03

## Summary

| Metric | Count |
|--------|-------|
| Files Scanned | 190 |
| Total Violations | 236 |
| 🔴 Critical | 90 |
| 🟠 High | 146 |
| 🟡 Medium | 0 |
| 🟢 Low | 0 |

## Violations by Severity

### 🔴 CRITICAL (90)

#### 1. 2025_10_12_174311_rename_site_adi_to_name_in_site_apartmanlar_table.php:19

- **Type:** METHOD
- **Issue:** Forbidden method: renameColumn()
- **Suggestion:** Use DB::statement() with ALTER TABLE ... CHANGE

**Original Code:**
```php
$table->renameColumn('site_adi', 'name');
```

**Fix Example:**
```php
// ❌ WRONG
$table->renameColumn('old_name', 'new_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'old_name')) {
    DB::statement("ALTER TABLE table_name CHANGE `old_name` `new_name` VARCHAR(255) NOT NULL");
}
```

---

#### 2. 2025_10_12_174311_rename_site_adi_to_name_in_site_apartmanlar_table.php:32

- **Type:** METHOD
- **Issue:** Forbidden method: renameColumn()
- **Suggestion:** Use DB::statement() with ALTER TABLE ... CHANGE

**Original Code:**
```php
$table->renameColumn('name', 'site_adi');
```

**Fix Example:**
```php
// ❌ WRONG
$table->renameColumn('old_name', 'new_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'old_name')) {
    DB::statement("ALTER TABLE table_name CHANGE `old_name` `new_name` VARCHAR(255) NOT NULL");
}
```

---

#### 3. 2025_10_24_155759_fix_ilan_kategorileri_status_field_to_boolean.php:25

- **Type:** NAMING
- **Issue:** Forbidden field: 'aktif'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
DB::statement("UPDATE ilan_kategorileri SET status = '1' WHERE status IN ('Aktif', '1', 'aktif', 'active', 'Active')");
```

**Fix Example:**
```php
        DB::statement("UPDATE ilan_kategorileri SET status = '1' WHERE status IN ('Aktif', '1', 'status', 'active', 'Active')");
```

---

#### 4. 2025_10_24_163945_fix_yayin_tipleri_status_field_to_boolean.php:27

- **Type:** NAMING
- **Issue:** Forbidden field: 'aktif'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
DB::statement("UPDATE ilan_kategori_yayin_tipleri SET status = '1' WHERE status IN ('active', 'Aktif', '1', 'aktif')");
```

**Fix Example:**
```php
        DB::statement("UPDATE ilan_kategori_yayin_tipleri SET status = '1' WHERE status IN ('active', 'Aktif', '1', 'status')");
```

---

#### 5. 2025_10_25_080242_create_ozellik_kategorileri_table.php:25

- **Type:** NAMING
- **Issue:** Forbidden field: 'aktif'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
$table->boolean('aktif')->default(true);
```

**Fix Example:**
```php
            $table->boolean('status')->default(true);
```

---

#### 6. 2025_10_25_080245_create_ozellik_alt_kategorileri_table.php:25

- **Type:** NAMING
- **Issue:** Forbidden field: 'aktif'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
$table->boolean('aktif')->default(true);
```

**Fix Example:**
```php
            $table->boolean('status')->default(true);
```

---

#### 7. 2025_10_25_080252_create_kategori_ozellik_matrix_table.php:20

- **Type:** NAMING
- **Issue:** Forbidden field: 'aktif'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
$table->boolean('aktif')->default(true);
```

**Fix Example:**
```php
            $table->boolean('status')->default(true);
```

---

#### 8. 2025_10_25_092335_create_konut_ozellik_hibrit_siralama_table.php:24

- **Type:** NAMING
- **Issue:** Forbidden field: 'aktif'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
$table->boolean('aktif')->default(true);
```

**Fix Example:**
```php
            $table->boolean('status')->default(true);
```

---

#### 9. 2025_10_25_103504_create_ai_core_system_table.php:22

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
$table->boolean('is_active')->default(true);
```

**Fix Example:**
```php
            $table->boolean('status')->default(true);
```

---

#### 10. 2025_11_05_133340_create_dashboard_widgets_table.php:25

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
$table->boolean('is_active')->default(true);
```

**Fix Example:**
```php
            $table->boolean('status')->default(true);
```

---

#### 11. 2025_11_05_133340_create_dashboard_widgets_table.php:32

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
$table->index(['user_id', 'is_active']);
```

**Fix Example:**
```php
            $table->index(['user_id', 'status']);
```

---

#### 12. 2025_11_06_000001_context7_rename_enabled_to_status.php:24

- **Type:** METHOD
- **Issue:** Forbidden method: renameColumn()
- **Suggestion:** Use DB::statement() with ALTER TABLE ... CHANGE

**Original Code:**
```php
$table->renameColumn('enabled', 'status');
```

**Fix Example:**
```php
// ❌ WRONG
$table->renameColumn('old_name', 'new_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'old_name')) {
    DB::statement("ALTER TABLE table_name CHANGE `old_name` `new_name` VARCHAR(255) NOT NULL");
}
```

---

#### 13. 2025_11_06_000001_context7_rename_enabled_to_status.php:43

- **Type:** METHOD
- **Issue:** Forbidden method: renameColumn()
- **Suggestion:** Use DB::statement() with ALTER TABLE ... CHANGE

**Original Code:**
```php
$table->renameColumn('status', 'enabled');
```

**Fix Example:**
```php
// ❌ WRONG
$table->renameColumn('old_name', 'new_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'old_name')) {
    DB::statement("ALTER TABLE table_name CHANGE `old_name` `new_name` VARCHAR(255) NOT NULL");
}
```

---

#### 14. 2025_11_06_000003_remove_enabled_from_features_tables.php:24

- **Type:** NAMING
- **Issue:** Forbidden field: 'order'
- **Suggestion:** Use 'display_order' instead

**Original Code:**
```php
$afterColumn = Schema::hasColumn('features', 'display_order') ? 'display_order' : 'order';
```

**Fix Example:**
```php
                    $afterColumn = Schema::hasColumn('features', 'display_display_order') ? 'display_display_order' : 'display_order';
```

---

#### 15. 2025_11_06_000003_remove_enabled_from_features_tables.php:47

- **Type:** NAMING
- **Issue:** Forbidden field: 'order'
- **Suggestion:** Use 'display_order' instead

**Original Code:**
```php
$afterColumn = Schema::hasColumn('feature_categories', 'display_order') ? 'display_order' : 'order';
```

**Fix Example:**
```php
                    $afterColumn = Schema::hasColumn('feature_categories', 'display_display_order') ? 'display_display_order' : 'display_order';
```

---

#### 16. 2025_11_06_230000_remove_enabled_field_complete.php:57

- **Type:** NAMING
- **Issue:** Forbidden field: 'order'
- **Suggestion:** Use 'display_order' instead

**Original Code:**
```php
$afterColumn = Schema::hasColumn('features', 'display_order') ? 'display_order' : 'order';
```

**Fix Example:**
```php
                $afterColumn = Schema::hasColumn('features', 'display_display_order') ? 'display_display_order' : 'display_order';
```

---

#### 17. 2025_11_06_230000_remove_enabled_field_complete.php:66

- **Type:** NAMING
- **Issue:** Forbidden field: 'order'
- **Suggestion:** Use 'display_order' instead

**Original Code:**
```php
$afterColumn = Schema::hasColumn('feature_categories', 'display_order') ? 'display_order' : 'order';
```

**Fix Example:**
```php
                $afterColumn = Schema::hasColumn('feature_categories', 'display_display_order') ? 'display_display_order' : 'display_order';
```

---

#### 18. 2025_11_09_062448_add_status_column_to_takim_uyeleri_table.php:19

- **Type:** NAMING
- **Issue:** Forbidden field: 'aktif'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
$table->string('status', 50)->default('aktif')->after('durum');
```

**Fix Example:**
```php
                $table->string('status', 50)->default('status')->after('durum');
```

---

#### 19. 2025_11_09_063338_remove_old_status_columns_from_tables.php:19

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'blog_categories' => 'is_active',
```

**Fix Example:**
```php
            'blog_categories' => 'status',
```

---

#### 20. 2025_11_09_063338_remove_old_status_columns_from_tables.php:20

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'blog_tags' => 'is_active',
```

**Fix Example:**
```php
            'blog_tags' => 'status',
```

---

#### 21. 2025_11_09_063338_remove_old_status_columns_from_tables.php:21

- **Type:** NAMING
- **Issue:** Forbidden field: 'aktif'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'eslesmeler' => 'aktif', // Not: eslesmeler'de durum ENUM var, aktif TINYINT var
```

**Fix Example:**
```php
            'eslesmeler' => 'status', // Not: eslesmeler'de durum ENUM var, status TINYINT var
```

---

#### 22. 2025_11_09_063338_remove_old_status_columns_from_tables.php:22

- **Type:** NAMING
- **Issue:** Forbidden field: 'aktif'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'etiketler' => 'aktif',
```

**Fix Example:**
```php
            'etiketler' => 'status',
```

---

#### 23. 2025_11_09_063338_remove_old_status_columns_from_tables.php:23

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'feature_categories' => 'is_active',
```

**Fix Example:**
```php
            'feature_categories' => 'status',
```

---

#### 24. 2025_11_09_063338_remove_old_status_columns_from_tables.php:24

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'features' => 'is_active',
```

**Fix Example:**
```php
            'features' => 'status',
```

---

#### 25. 2025_11_09_063338_remove_old_status_columns_from_tables.php:25

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'ilan_kategori_yayin_tipleri' => 'is_active',
```

**Fix Example:**
```php
            'ilan_kategori_yayin_tipleri' => 'status',
```

---

#### 26. 2025_11_09_063338_remove_old_status_columns_from_tables.php:26

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'ilan_kategorileri' => 'is_active',
```

**Fix Example:**
```php
            'ilan_kategorileri' => 'status',
```

---

#### 27. 2025_11_09_063338_remove_old_status_columns_from_tables.php:27

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'users' => 'is_active',
```

**Fix Example:**
```php
            'users' => 'status',
```

---

#### 28. 2025_11_09_063338_remove_old_status_columns_from_tables.php:30

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'ai_knowledge_base' => 'is_active',
```

**Fix Example:**
```php
            'ai_knowledge_base' => 'status',
```

---

#### 29. 2025_11_09_063338_remove_old_status_columns_from_tables.php:31

- **Type:** NAMING
- **Issue:** Forbidden field: 'aktif'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'arsa_detaylari' => 'aktif',
```

**Fix Example:**
```php
            'arsa_detaylari' => 'status',
```

---

#### 30. 2025_11_09_063338_remove_old_status_columns_from_tables.php:32

- **Type:** NAMING
- **Issue:** Forbidden field: 'aktif'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'arsa_ozellikleri' => 'aktif',
```

**Fix Example:**
```php
            'arsa_ozellikleri' => 'status',
```

---

#### 31. 2025_11_09_063338_remove_old_status_columns_from_tables.php:33

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'categories' => 'is_active',
```

**Fix Example:**
```php
            'categories' => 'status',
```

---

#### 32. 2025_11_09_063338_remove_old_status_columns_from_tables.php:34

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'commission_rates' => 'is_active',
```

**Fix Example:**
```php
            'commission_rates' => 'status',
```

---

#### 33. 2025_11_09_063338_remove_old_status_columns_from_tables.php:35

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'exchange_rates' => 'is_active',
```

**Fix Example:**
```php
            'exchange_rates' => 'status',
```

---

#### 34. 2025_11_09_063338_remove_old_status_columns_from_tables.php:36

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'expertise_areas' => 'is_active',
```

**Fix Example:**
```php
            'expertise_areas' => 'status',
```

---

#### 35. 2025_11_09_063338_remove_old_status_columns_from_tables.php:37

- **Type:** NAMING
- **Issue:** Forbidden field: 'aktif'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'ilan_dinamik_ozellikler' => 'aktif',
```

**Fix Example:**
```php
            'ilan_dinamik_ozellikler' => 'status',
```

---

#### 36. 2025_11_09_063338_remove_old_status_columns_from_tables.php:38

- **Type:** NAMING
- **Issue:** Forbidden field: 'aktif'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'ilan_takvim_sezonlar' => 'aktif',
```

**Fix Example:**
```php
            'ilan_takvim_sezonlar' => 'status',
```

---

#### 37. 2025_11_09_063338_remove_old_status_columns_from_tables.php:39

- **Type:** NAMING
- **Issue:** Forbidden field: 'aktif'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'ilceler' => 'aktif',
```

**Fix Example:**
```php
            'ilceler' => 'status',
```

---

#### 38. 2025_11_09_063338_remove_old_status_columns_from_tables.php:40

- **Type:** NAMING
- **Issue:** Forbidden field: 'aktif'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'isyeri_ozellikleri' => 'aktif',
```

**Fix Example:**
```php
            'isyeri_ozellikleri' => 'status',
```

---

#### 39. 2025_11_09_063338_remove_old_status_columns_from_tables.php:41

- **Type:** NAMING
- **Issue:** Forbidden field: 'aktif'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'konut_ozellikleri' => 'aktif',
```

**Fix Example:**
```php
            'konut_ozellikleri' => 'status',
```

---

#### 40. 2025_11_09_063338_remove_old_status_columns_from_tables.php:42

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'language_settings' => 'is_active',
```

**Fix Example:**
```php
            'language_settings' => 'status',
```

---

#### 41. 2025_11_09_063338_remove_old_status_columns_from_tables.php:44

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'nearby_place_categories' => 'is_active',
```

**Fix Example:**
```php
            'nearby_place_categories' => 'status',
```

---

#### 42. 2025_11_09_063338_remove_old_status_columns_from_tables.php:45

- **Type:** NAMING
- **Issue:** Forbidden field: 'aktif'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'ozellik_kategorileri' => 'aktif',
```

**Fix Example:**
```php
            'ozellik_kategorileri' => 'status',
```

---

#### 43. 2025_11_09_063338_remove_old_status_columns_from_tables.php:46

- **Type:** NAMING
- **Issue:** Forbidden field: 'aktif'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'para_birimleri' => 'aktif',
```

**Fix Example:**
```php
            'para_birimleri' => 'status',
```

---

#### 44. 2025_11_09_063338_remove_old_status_columns_from_tables.php:47

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'people' => 'is_active',
```

**Fix Example:**
```php
            'people' => 'status',
```

---

#### 45. 2025_11_09_063338_remove_old_status_columns_from_tables.php:48

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'property_types' => 'is_active',
```

**Fix Example:**
```php
            'property_types' => 'status',
```

---

#### 46. 2025_11_09_063338_remove_old_status_columns_from_tables.php:50

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'site_settings' => 'is_active',
```

**Fix Example:**
```php
            'site_settings' => 'status',
```

---

#### 47. 2025_11_09_063338_remove_old_status_columns_from_tables.php:51

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'tax_rates' => 'is_active',
```

**Fix Example:**
```php
            'tax_rates' => 'status',
```

---

#### 48. 2025_11_09_063338_remove_old_status_columns_from_tables.php:52

- **Type:** NAMING
- **Issue:** Forbidden field: 'aktif'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'turistik_ozellikleri' => 'aktif',
```

**Fix Example:**
```php
            'turistik_ozellikleri' => 'status',
```

---

#### 49. 2025_11_09_063338_remove_old_status_columns_from_tables.php:53

- **Type:** NAMING
- **Issue:** Forbidden field: 'aktif'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'yazlik_fiyatlandirma' => 'aktif',
```

**Fix Example:**
```php
            'yazlik_fiyatlandirma' => 'status',
```

---

#### 50. 2025_11_09_063338_remove_old_status_columns_from_tables.php:118

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'blog_categories' => ['is_active' => 'tinyint'],
```

**Fix Example:**
```php
            'blog_categories' => ['status' => 'tinyint'],
```

---

#### 51. 2025_11_09_063338_remove_old_status_columns_from_tables.php:119

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'blog_tags' => ['is_active' => 'tinyint'],
```

**Fix Example:**
```php
            'blog_tags' => ['status' => 'tinyint'],
```

---

#### 52. 2025_11_09_063338_remove_old_status_columns_from_tables.php:120

- **Type:** NAMING
- **Issue:** Forbidden field: 'aktif'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'eslesmeler' => ['aktif' => 'tinyint'],
```

**Fix Example:**
```php
            'eslesmeler' => ['status' => 'tinyint'],
```

---

#### 53. 2025_11_09_063338_remove_old_status_columns_from_tables.php:121

- **Type:** NAMING
- **Issue:** Forbidden field: 'aktif'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'etiketler' => ['aktif' => 'tinyint'],
```

**Fix Example:**
```php
            'etiketler' => ['status' => 'tinyint'],
```

---

#### 54. 2025_11_09_063338_remove_old_status_columns_from_tables.php:122

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'feature_categories' => ['is_active' => 'tinyint'],
```

**Fix Example:**
```php
            'feature_categories' => ['status' => 'tinyint'],
```

---

#### 55. 2025_11_09_063338_remove_old_status_columns_from_tables.php:123

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'features' => ['is_active' => 'tinyint'],
```

**Fix Example:**
```php
            'features' => ['status' => 'tinyint'],
```

---

#### 56. 2025_11_09_063338_remove_old_status_columns_from_tables.php:124

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'ilan_kategori_yayin_tipleri' => ['is_active' => 'tinyint'],
```

**Fix Example:**
```php
            'ilan_kategori_yayin_tipleri' => ['status' => 'tinyint'],
```

---

#### 57. 2025_11_09_063338_remove_old_status_columns_from_tables.php:125

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'ilan_kategorileri' => ['is_active' => 'tinyint'],
```

**Fix Example:**
```php
            'ilan_kategorileri' => ['status' => 'tinyint'],
```

---

#### 58. 2025_11_09_063338_remove_old_status_columns_from_tables.php:126

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
'users' => ['is_active' => 'tinyint'],
```

**Fix Example:**
```php
            'users' => ['status' => 'tinyint'],
```

---

#### 59. 2025_11_09_070721_rename_order_to_display_order_in_tables.php:33

- **Type:** NAMING
- **Issue:** Forbidden field: 'order'
- **Suggestion:** Use 'display_order' instead

**Original Code:**
```php
$hasOrder = Schema::hasColumn($tableName, 'order');
```

**Fix Example:**
```php
            $hasOrder = Schema::hasColumn($tableName, 'display_order');
```

---

#### 60. 2025_11_09_070721_rename_order_to_display_order_in_tables.php:38

- **Type:** NAMING
- **Issue:** Forbidden field: 'order'
- **Suggestion:** Use 'display_order' instead

**Original Code:**
```php
$this->dropIndexesForColumn($tableName, 'order');
```

**Fix Example:**
```php
                $this->dropIndexesForColumn($tableName, 'display_order');
```

---

#### 61. 2025_11_09_070721_rename_order_to_display_order_in_tables.php:41

- **Type:** NAMING
- **Issue:** Forbidden field: 'order'
- **Suggestion:** Use 'display_order' instead

**Original Code:**
```php
$columnInfo = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'order'");
```

**Fix Example:**
```php
                $columnInfo = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'display_order'");
```

---

#### 62. 2025_11_09_070721_rename_order_to_display_order_in_tables.php:69

- **Type:** NAMING
- **Issue:** Forbidden field: 'order'
- **Suggestion:** Use 'display_order' instead

**Original Code:**
```php
$this->dropIndexesForColumn($tableName, 'order');
```

**Fix Example:**
```php
                $this->dropIndexesForColumn($tableName, 'display_order');
```

---

#### 63. 2025_11_09_070721_rename_order_to_display_order_in_tables.php:72

- **Type:** NAMING
- **Issue:** Forbidden field: 'order'
- **Suggestion:** Use 'display_order' instead

**Original Code:**
```php
$table->dropColumn('order');
```

**Fix Example:**
```php
                    $table->dropColumn('display_order');
```

---

#### 64. 2025_11_09_070721_rename_order_to_display_order_in_tables.php:97

- **Type:** NAMING
- **Issue:** Forbidden field: 'order'
- **Suggestion:** Use 'display_order' instead

**Original Code:**
```php
$hasOrder = Schema::hasColumn($tableName, 'order');
```

**Fix Example:**
```php
            $hasOrder = Schema::hasColumn($tableName, 'display_order');
```

---

#### 65. 2025_11_09_070721_rename_order_to_display_order_in_tables.php:121

- **Type:** NAMING
- **Issue:** Forbidden field: 'order'
- **Suggestion:** Use 'display_order' instead

**Original Code:**
```php
$table->index('order', "idx_{$tableName}_order");
```

**Fix Example:**
```php
                        $table->index('display_order', "idx_{$tableName}_display_order");
```

---

#### 66. 2025_11_09_122119_rename_order_to_display_order_in_alt_kategori_yayin_tipi_table.php:27

- **Type:** NAMING
- **Issue:** Forbidden field: 'order'
- **Suggestion:** Use 'display_order' instead

**Original Code:**
```php
$hasOrder = Schema::hasColumn($tableName, 'order');
```

**Fix Example:**
```php
        $hasOrder = Schema::hasColumn($tableName, 'display_order');
```

---

#### 67. 2025_11_09_122119_rename_order_to_display_order_in_alt_kategori_yayin_tipi_table.php:32

- **Type:** NAMING
- **Issue:** Forbidden field: 'order'
- **Suggestion:** Use 'display_order' instead

**Original Code:**
```php
$this->dropIndexesForColumn($tableName, 'order');
```

**Fix Example:**
```php
            $this->dropIndexesForColumn($tableName, 'display_order');
```

---

#### 68. 2025_11_09_122119_rename_order_to_display_order_in_alt_kategori_yayin_tipi_table.php:35

- **Type:** NAMING
- **Issue:** Forbidden field: 'order'
- **Suggestion:** Use 'display_order' instead

**Original Code:**
```php
$columnInfo = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'order'");
```

**Fix Example:**
```php
            $columnInfo = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'display_order'");
```

---

#### 69. 2025_11_09_122119_rename_order_to_display_order_in_alt_kategori_yayin_tipi_table.php:63

- **Type:** NAMING
- **Issue:** Forbidden field: 'order'
- **Suggestion:** Use 'display_order' instead

**Original Code:**
```php
$this->dropIndexesForColumn($tableName, 'order');
```

**Fix Example:**
```php
            $this->dropIndexesForColumn($tableName, 'display_order');
```

---

#### 70. 2025_11_09_122119_rename_order_to_display_order_in_alt_kategori_yayin_tipi_table.php:66

- **Type:** NAMING
- **Issue:** Forbidden field: 'order'
- **Suggestion:** Use 'display_order' instead

**Original Code:**
```php
$table->dropColumn('order');
```

**Fix Example:**
```php
                $table->dropColumn('display_order');
```

---

#### 71. 2025_11_09_122119_rename_order_to_display_order_in_alt_kategori_yayin_tipi_table.php:85

- **Type:** NAMING
- **Issue:** Forbidden field: 'order'
- **Suggestion:** Use 'display_order' instead

**Original Code:**
```php
$hasOrder = Schema::hasColumn($tableName, 'order');
```

**Fix Example:**
```php
        $hasOrder = Schema::hasColumn($tableName, 'display_order');
```

---

#### 72. 2025_11_09_122119_rename_order_to_display_order_in_alt_kategori_yayin_tipi_table.php:109

- **Type:** NAMING
- **Issue:** Forbidden field: 'order'
- **Suggestion:** Use 'display_order' instead

**Original Code:**
```php
$table->index('order', "idx_{$tableName}_order");
```

**Fix Example:**
```php
                    $table->index('display_order', "idx_{$tableName}_display_order");
```

---

#### 73. 2025_11_11_103353_rename_aktif_to_status_in_multiple_tables.php:36

- **Type:** NAMING
- **Issue:** Forbidden field: 'aktif'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
$hasAktif = Schema::hasColumn($tableName, 'aktif');
```

**Fix Example:**
```php
            $hasAktif = Schema::hasColumn($tableName, 'status');
```

---

#### 74. 2025_11_11_103353_rename_aktif_to_status_in_multiple_tables.php:41

- **Type:** NAMING
- **Issue:** Forbidden field: 'aktif'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
$this->dropIndexesForColumn($tableName, 'aktif');
```

**Fix Example:**
```php
                $this->dropIndexesForColumn($tableName, 'status');
```

---

#### 75. 2025_11_11_103353_rename_aktif_to_status_in_multiple_tables.php:44

- **Type:** NAMING
- **Issue:** Forbidden field: 'aktif'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
$columnInfo = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'aktif'");
```

**Fix Example:**
```php
                $columnInfo = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'status'");
```

---

#### 76. 2025_11_11_103353_rename_aktif_to_status_in_multiple_tables.php:116

- **Type:** NAMING
- **Issue:** Forbidden field: 'aktif'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
$table->index('aktif', "idx_{$tableName}_aktif");
```

**Fix Example:**
```php
                        $table->index('status', "idx_{$tableName}_status");
```

---

#### 77. 2025_11_11_103353_rename_order_to_display_order_in_multiple_tables.php:38

- **Type:** NAMING
- **Issue:** Forbidden field: 'order'
- **Suggestion:** Use 'display_order' instead

**Original Code:**
```php
$hasOrder = Schema::hasColumn($tableName, 'order');
```

**Fix Example:**
```php
            $hasOrder = Schema::hasColumn($tableName, 'display_order');
```

---

#### 78. 2025_11_11_103353_rename_order_to_display_order_in_multiple_tables.php:43

- **Type:** NAMING
- **Issue:** Forbidden field: 'order'
- **Suggestion:** Use 'display_order' instead

**Original Code:**
```php
$this->dropIndexesForColumn($tableName, 'order');
```

**Fix Example:**
```php
                $this->dropIndexesForColumn($tableName, 'display_order');
```

---

#### 79. 2025_11_11_103353_rename_order_to_display_order_in_multiple_tables.php:46

- **Type:** NAMING
- **Issue:** Forbidden field: 'order'
- **Suggestion:** Use 'display_order' instead

**Original Code:**
```php
$columnInfo = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'order'");
```

**Fix Example:**
```php
                $columnInfo = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'display_order'");
```

---

#### 80. 2025_11_11_103353_rename_order_to_display_order_in_multiple_tables.php:119

- **Type:** NAMING
- **Issue:** Forbidden field: 'order'
- **Suggestion:** Use 'display_order' instead

**Original Code:**
```php
$table->index('order', "idx_{$tableName}_order");
```

**Fix Example:**
```php
                        $table->index('display_order', "idx_{$tableName}_display_order");
```

---

#### 81. 2025_11_11_103355_rename_is_active_to_status_in_ai_core_system.php:29

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
$hasIsActive = Schema::hasColumn($tableName, 'is_active');
```

**Fix Example:**
```php
        $hasIsActive = Schema::hasColumn($tableName, 'status');
```

---

#### 82. 2025_11_11_103355_rename_is_active_to_status_in_ai_core_system.php:34

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
$this->dropIndexesForColumn($tableName, 'is_active');
```

**Fix Example:**
```php
            $this->dropIndexesForColumn($tableName, 'status');
```

---

#### 83. 2025_11_11_103355_rename_is_active_to_status_in_ai_core_system.php:37

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
$columnInfo = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'is_active'");
```

**Fix Example:**
```php
            $columnInfo = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'status'");
```

---

#### 84. 2025_11_11_103355_rename_is_active_to_status_in_ai_core_system.php:103

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
$table->index('is_active', "idx_{$tableName}_is_active");
```

**Fix Example:**
```php
                    $table->index('status', "idx_{$tableName}_status");
```

---

#### 85. 2025_11_22_152324_convert_status_to_boolean_in_ilan_kategorileri_table.php:53

- **Type:** NAMING
- **Issue:** Forbidden field: 'aktif'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
WHERE status IN ('Aktif', 'aktif', 'active', 'Active', '1', 1)
```

**Fix Example:**
```php
                WHERE status IN ('Aktif', 'status', 'active', 'Active', '1', 1)
```

---

#### 86. 2025_11_22_152526_fix_all_status_columns_to_boolean_global_fix.php:103

- **Type:** NAMING
- **Issue:** Forbidden field: 'aktif'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
WHERE status IN ('Aktif', 'aktif', 'active', 'Active', '1', 1)
```

**Fix Example:**
```php
                    WHERE status IN ('Aktif', 'status', 'active', 'Active', '1', 1)
```

---

#### 87. 2025_11_25_052644_fix_sync_enabled_field_in_ilan_takvim_sync_table.php:43

- **Type:** METHOD
- **Issue:** Forbidden method: renameColumn()
- **Suggestion:** Use DB::statement() with ALTER TABLE ... CHANGE

**Original Code:**
```php
$table->renameColumn('sync_status_field', 'sync_status');
```

**Fix Example:**
```php
// ❌ WRONG
$table->renameColumn('old_name', 'new_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'old_name')) {
    DB::statement("ALTER TABLE table_name CHANGE `old_name` `new_name` VARCHAR(255) NOT NULL");
}
```

---

#### 88. 2025_12_05_000000_test_violation.php:16

- **Type:** NAMING
- **Issue:** Forbidden field: 'order'
- **Suggestion:** Use 'display_order' instead

**Original Code:**
```php
$table->integer('order');
```

**Fix Example:**
```php
            $table->integer('display_order');
```

---

#### 89. 2025_12_05_000000_test_violation.php:19

- **Type:** NAMING
- **Issue:** Forbidden field: 'is_active'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
$table->boolean('is_active');
```

**Fix Example:**
```php
            $table->boolean('status');
```

---

#### 90. 2025_12_05_000000_test_violation.php:29

- **Type:** NAMING
- **Issue:** Forbidden field: 'order'
- **Suggestion:** Use 'display_order' instead

**Original Code:**
```php
$table->dropColumn('order');
```

**Fix Example:**
```php
            $table->dropColumn('display_order');
```

---

### 🟠 HIGH (146)

#### 1. 2025_10_07_214921_add_missing_columns_to_users_table.php:45

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn([
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 2. 2025_10_08_132426_update_sites_status_column.php:31

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('active');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 3. 2025_10_08_132426_update_sites_status_column.php:57

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('status');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 4. 2025_10_10_071029_add_last_activity_at_to_users_table.php:22

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('last_activity_at');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 5. 2025_10_10_174618_add_seviye_to_ilan_kategorileri_table.php:31

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('seviye');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 6. 2025_10_10_184720_add_talep_id_to_eslesmeler_table.php:27

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('talep_id');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 7. 2025_10_11_180000_add_referans_system_to_ilanlar_table.php:57

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn([
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 8. 2025_10_11_215700_add_anahtar_management_fields_to_ilanlar_table.php:51

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn([
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 9. 2025_10_15_213116_add_danisman_id_to_ilanlar_table.php:28

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('danisman_id');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 10. 2025_10_15_213509_add_danisman_id_to_kisiler_table.php:28

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('danisman_id');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 11. 2025_10_19_223750_add_coordinates_to_mahalleler_table.php:36

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn(['enlem', 'boylam', 'status']);
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 12. 2025_10_19_223803_add_coordinates_to_ilceler_table.php:32

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('status');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 13. 2025_10_19_223815_add_coordinates_to_iller_table.php:32

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('status');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 14. 2025_10_19_223916_add_new_category_fields_to_ilanlar_table.php:51

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn(['ana_kategori_id', 'alt_kategori_id', 'yayin_tipi_id']);
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 15. 2025_10_19_225326_add_missing_coordinates_to_location_tables.php:62

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('country_id');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 16. 2025_10_19_225326_add_missing_coordinates_to_location_tables.php:65

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('plaka_kodu');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 17. 2025_10_19_225326_add_missing_coordinates_to_location_tables.php:68

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('telefon_kodu');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 18. 2025_10_19_225326_add_missing_coordinates_to_location_tables.php:74

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('ilce_kodu');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 19. 2025_10_19_225326_add_missing_coordinates_to_location_tables.php:80

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('mahalle_kodu');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 20. 2025_10_22_072529_add_arsa_fields_to_ilanlar_table.php:59

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn([
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 21. 2025_10_22_072548_add_yazlik_fields_to_ilanlar_table.php:58

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn([
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 22. 2025_10_22_142744_add_villa_isyeri_fields_to_ilanlar_table.php:36

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn(['isinma_tipi', 'site_ozellikleri']);
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 23. 2025_10_22_142744_add_villa_isyeri_fields_to_ilanlar_table.php:39

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn([
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 24. 2025_10_22_145330_add_para_birimi_to_ilanlar_table.php:25

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('para_birimi');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 25. 2025_10_22_203233_add_tip_column_to_site_apartmanlar_table.php:25

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('tip');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 26. 2025_10_23_143000_add_yayin_tipi_details_to_ilan_kategori_yayin_tipleri.php:46

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn([
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 27. 2025_10_25_160239_create_kategori_yayin_tipi_field_dependencies_table.php:37

- **Type:** NAMING
- **Issue:** Forbidden field: 'enabled'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
$table->boolean('enabled')->default(true)->comment('Field aktif mi?');
```

**Fix Example:**
```php
            $table->boolean('status')->default(true)->comment('Field aktif mi?');
```

---

#### 28. 2025_10_25_160239_create_kategori_yayin_tipi_field_dependencies_table.php:57

- **Type:** NAMING
- **Issue:** Forbidden field: 'enabled'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
$table->index('enabled', 'idx_enabled');
```

**Fix Example:**
```php
            $table->index('status', 'idx_status');
```

---

#### 29. 2025_10_26_115934_add_applies_to_to_feature_categories_table.php:27

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn(['applies_to', 'display_order']);
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 30. 2025_10_26_160410_add_applies_to_to_features_table.php:20

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn(['applies_to', 'display_order']);
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 31. 2025_10_27_085004_add_etiket_fields_to_etiketler_table.php:35

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn(['type', 'icon', 'bg_color', 'badge_text', 'is_badge', 'target_url']);
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 32. 2025_10_27_101503_remove_legacy_category_fields_from_ilanlar_table.php:31

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('kategori_id');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 33. 2025_10_27_101503_remove_legacy_category_fields_from_ilanlar_table.php:35

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('parent_kategori_id');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 34. 2025_10_27_101503_remove_legacy_category_fields_from_ilanlar_table.php:39

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('yayinlama_tipi');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 35. 2025_10_27_140207_add_applies_to_to_feature_categories_table.php:42

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('applies_to');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 36. 2025_10_27_150000_update_yazlik_details_airbnb_fields.php:48

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn([
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 37. 2025_10_31_000000_add_mahalle_id_to_kisiler_table.php:44

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('mahalle_id');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 38. 2025_10_31_175103_add_address_components_to_ilanlar_table.php:45

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn([
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 39. 2025_11_01_220000_add_arsa_extended_fields_to_ilanlar_table.php:65

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn([
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 40. 2025_11_01_221500_add_konut_critical_fields_to_ilanlar_table.php:47

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn([
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 41. 2025_11_03_101305_add_bedroom_layout_to_ilanlar_table.php:39

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn(['bedroom_layout', 'sleeping_arrangement_notes']);
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 42. 2025_11_04_163654_add_latitude_longitude_to_site_apartmanlar_table.php:31

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn(['latitude', 'longitude']);
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 43. 2025_11_05_000001_add_turkiyeapi_fields_to_ilanlar.php:45

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn([
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 44. 2025_11_06_000001_context7_rename_enabled_to_status.php:22

- **Type:** NAMING
- **Issue:** Forbidden field: 'enabled'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
if (Schema::hasTable($table) && Schema::hasColumn($table, 'enabled') && ! Schema::hasColumn($table, 'status')) {
```

**Fix Example:**
```php
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'status') && ! Schema::hasColumn($table, 'status')) {
```

---

#### 45. 2025_11_06_000001_context7_rename_enabled_to_status.php:24

- **Type:** NAMING
- **Issue:** Forbidden field: 'enabled'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
$table->renameColumn('enabled', 'status');
```

**Fix Example:**
```php
                    $table->renameColumn('status', 'status');
```

---

#### 46. 2025_11_06_000001_context7_rename_enabled_to_status.php:41

- **Type:** NAMING
- **Issue:** Forbidden field: 'enabled'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
if (Schema::hasTable($table) && Schema::hasColumn($table, 'status') && ! Schema::hasColumn($table, 'enabled')) {
```

**Fix Example:**
```php
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'status') && ! Schema::hasColumn($table, 'status')) {
```

---

#### 47. 2025_11_06_000001_context7_rename_enabled_to_status.php:43

- **Type:** NAMING
- **Issue:** Forbidden field: 'enabled'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
$table->renameColumn('status', 'enabled');
```

**Fix Example:**
```php
                    $table->renameColumn('status', 'status');
```

---

#### 48. 2025_11_06_000003_remove_enabled_from_features_tables.php:19

- **Type:** NAMING
- **Issue:** Forbidden field: 'enabled'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
if (Schema::hasColumn('features', 'enabled')) {
```

**Fix Example:**
```php
        if (Schema::hasColumn('features', 'status')) {
```

---

#### 49. 2025_11_06_000003_remove_enabled_from_features_tables.php:37

- **Type:** NAMING
- **Issue:** Forbidden field: 'enabled'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
$table->dropColumn('enabled');
```

**Fix Example:**
```php
                $table->dropColumn('status');
```

---

#### 50. 2025_11_06_000003_remove_enabled_from_features_tables.php:37

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('enabled');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 51. 2025_11_06_000003_remove_enabled_from_features_tables.php:42

- **Type:** NAMING
- **Issue:** Forbidden field: 'enabled'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
if (Schema::hasColumn('feature_categories', 'enabled')) {
```

**Fix Example:**
```php
        if (Schema::hasColumn('feature_categories', 'status')) {
```

---

#### 52. 2025_11_06_000003_remove_enabled_from_features_tables.php:60

- **Type:** NAMING
- **Issue:** Forbidden field: 'enabled'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
$table->dropColumn('enabled');
```

**Fix Example:**
```php
                $table->dropColumn('status');
```

---

#### 53. 2025_11_06_000003_remove_enabled_from_features_tables.php:60

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('enabled');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 54. 2025_11_06_230000_remove_enabled_field_complete.php:22

- **Type:** NAMING
- **Issue:** Forbidden field: 'enabled'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
if (Schema::hasColumn('features', 'enabled')) {
```

**Fix Example:**
```php
        if (Schema::hasColumn('features', 'status')) {
```

---

#### 55. 2025_11_06_230000_remove_enabled_field_complete.php:31

- **Type:** NAMING
- **Issue:** Forbidden field: 'enabled'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
$table->dropColumn('enabled');
```

**Fix Example:**
```php
                $table->dropColumn('status');
```

---

#### 56. 2025_11_06_230000_remove_enabled_field_complete.php:31

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('enabled');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 57. 2025_11_06_230000_remove_enabled_field_complete.php:34

- **Type:** NAMING
- **Issue:** Forbidden field: 'enabled'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
echo "✅ Removed 'enabled' column from features table\n";
```

**Fix Example:**
```php
            echo "✅ Removed 'status' column from features table\n";
```

---

#### 58. 2025_11_06_230000_remove_enabled_field_complete.php:38

- **Type:** NAMING
- **Issue:** Forbidden field: 'enabled'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
if (Schema::hasColumn('feature_categories', 'enabled')) {
```

**Fix Example:**
```php
        if (Schema::hasColumn('feature_categories', 'status')) {
```

---

#### 59. 2025_11_06_230000_remove_enabled_field_complete.php:47

- **Type:** NAMING
- **Issue:** Forbidden field: 'enabled'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
$table->dropColumn('enabled');
```

**Fix Example:**
```php
                $table->dropColumn('status');
```

---

#### 60. 2025_11_06_230000_remove_enabled_field_complete.php:47

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('enabled');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 61. 2025_11_06_230000_remove_enabled_field_complete.php:50

- **Type:** NAMING
- **Issue:** Forbidden field: 'enabled'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
echo "✅ Removed 'enabled' column from feature_categories table\n";
```

**Fix Example:**
```php
            echo "✅ Removed 'status' column from feature_categories table\n";
```

---

#### 62. 2025_11_06_230200_add_kisi_tipi_field.php:66

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('kisi_tipi');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 63. 2025_11_07_110654_add_danisman_profile_fields_to_users_table.php:117

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn([
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 64. 2025_11_07_115744_add_position_and_department_to_users_table.php:35

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('position');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 65. 2025_11_07_115744_add_position_and_department_to_users_table.php:38

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('department');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 66. 2025_11_07_120415_add_status_text_to_users_table.php:31

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('status_text');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 67. 2025_11_08_130028_add_slug_to_ilanlar_table.php:25

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('slug');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 68. 2025_11_08_132145_add_ilan_sahibi_id_to_ilanlar_table.php:103

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn(['ilan_sahibi_id', 'ilgili_kisi_id']);
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 69. 2025_11_09_062448_add_status_column_to_takim_uyeleri_table.php:49

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('status');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 70. 2025_11_09_063338_remove_old_status_columns_from_tables.php:106

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn($oldColumn);
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 71. 2025_11_09_065813_add_missing_danisman_fields_to_users_table.php:75

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn($column);
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 72. 2025_11_09_070721_rename_order_to_display_order_in_tables.php:72

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('order');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 73. 2025_11_09_095517_add_skor_column_to_eslesmeler_table_if_missing.php:40

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('skor');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 74. 2025_11_09_122119_rename_order_to_display_order_in_alt_kategori_yayin_tipi_table.php:66

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('order');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 75. 2025_11_11_103354_rename_enabled_to_status_in_multiple_tables.php:34

- **Type:** NAMING
- **Issue:** Forbidden field: 'enabled'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
$hasEnabled = Schema::hasColumn($tableName, 'enabled');
```

**Fix Example:**
```php
            $hasEnabled = Schema::hasColumn($tableName, 'status');
```

---

#### 76. 2025_11_11_103354_rename_enabled_to_status_in_multiple_tables.php:39

- **Type:** NAMING
- **Issue:** Forbidden field: 'enabled'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
$this->dropIndexesForColumn($tableName, 'enabled');
```

**Fix Example:**
```php
                $this->dropIndexesForColumn($tableName, 'status');
```

---

#### 77. 2025_11_11_103354_rename_enabled_to_status_in_multiple_tables.php:42

- **Type:** NAMING
- **Issue:** Forbidden field: 'enabled'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
$columnInfo = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'enabled'");
```

**Fix Example:**
```php
                $columnInfo = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'status'");
```

---

#### 78. 2025_11_11_103354_rename_enabled_to_status_in_multiple_tables.php:113

- **Type:** NAMING
- **Issue:** Forbidden field: 'enabled'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
$table->index('enabled', "idx_{$tableName}_enabled");
```

**Fix Example:**
```php
                        $table->index('status', "idx_{$tableName}_status");
```

---

#### 79. 2025_11_15_150500_add_owner_private_encrypted_to_ilanlar_table.php:22

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('owner_private_encrypted');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 80. 2025_11_16_093000_add_ulke_and_citizenship_columns_to_ilanlar.php:44

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('citizenship_eligible');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 81. 2025_11_16_093000_add_ulke_and_citizenship_columns_to_ilanlar.php:51

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('ulke_id');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 82. 2025_11_16_120500_add_tc_kimlik_encrypted_to_kisiler_table.php:22

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('tc_kimlik_encrypted');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 83. 2025_11_17_120300_add_fields_to_talepler_table.php:31

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('baslik');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 84. 2025_11_17_120300_add_fields_to_talepler_table.php:34

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('aciklama');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 85. 2025_11_17_120300_add_fields_to_talepler_table.php:37

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('status');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 86. 2025_11_17_120400_add_kisi_notlari_to_kisiler_table.php:22

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('kisi_notlari');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 87. 2025_11_17_120500_add_description_to_roles_table.php:22

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('description');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 88. 2025_11_17_120800_add_talep_id_to_ilanlar_table.php:22

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('talep_id');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 89. 2025_11_19_144200_rename_durum_to_status_in_yazlik_doluluk_durumlari.php:40

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('durum');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 90. 2025_11_19_144200_rename_durum_to_status_in_yazlik_doluluk_durumlari.php:92

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('status');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 91. 2025_11_19_150001_add_crm_only_to_ilanlar_table.php:32

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('crm_only');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 92. 2025_11_20_145127_create_ai_land_plot_analyses_table.php:53

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('display_order');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 93. 2025_11_20_145127_create_ai_land_plot_analyses_table.php:56

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('status');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 94. 2025_11_23_120000_add_ical_url_to_ilan_takvim_sync_table.php:25

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('ical_url');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 95. 2025_11_24_202215_add_talep_reform_fields_to_talepler_table.php:63

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('aranan_ozellikler_json');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 96. 2025_11_24_202215_add_talep_reform_fields_to_talepler_table.php:66

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('min_metrekare');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 97. 2025_11_24_202215_add_talep_reform_fields_to_talepler_table.php:69

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('max_metrekare');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 98. 2025_11_24_202215_add_talep_reform_fields_to_talepler_table.php:72

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('metadata');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 99. 2025_11_24_215950_add_ai_scoring_fields_to_kisiler_table.php:73

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn([
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 100. 2025_11_25_052528_fix_enabled_field_in_kategori_yayin_tipi_field_dependencies_table.php:33

- **Type:** NAMING
- **Issue:** Forbidden field: 'enabled'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
if (Schema::hasColumn('kategori_yayin_tipi_field_dependencies', 'enabled')) {
```

**Fix Example:**
```php
        if (Schema::hasColumn('kategori_yayin_tipi_field_dependencies', 'status')) {
```

---

#### 101. 2025_11_25_052528_fix_enabled_field_in_kategori_yayin_tipi_field_dependencies_table.php:51

- **Type:** NAMING
- **Issue:** Forbidden field: 'enabled'
- **Suggestion:** Use 'status' instead

**Original Code:**
```php
$table->dropColumn('enabled');
```

**Fix Example:**
```php
                $table->dropColumn('status');
```

---

#### 102. 2025_11_25_052528_fix_enabled_field_in_kategori_yayin_tipi_field_dependencies_table.php:51

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('enabled');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 103. 2025_11_25_052528_fix_enabled_field_in_kategori_yayin_tipi_field_dependencies_table.php:86

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('status');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 104. 2025_11_25_052644_fix_sync_enabled_field_in_ilan_takvim_sync_table.php:40

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('sync_enabled');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 105. 2025_11_25_052644_fix_sync_enabled_field_in_ilan_takvim_sync_table.php:73

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('sync_status');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 106. 2025_11_25_112256_add_ai_feedback_fields_to_ai_logs_table.php:86

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('feedback_at');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 107. 2025_11_25_112256_add_ai_feedback_fields_to_ai_logs_table.php:89

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('feedback_reason');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 108. 2025_11_25_112256_add_ai_feedback_fields_to_ai_logs_table.php:92

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('feedback_type');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 109. 2025_11_25_112256_add_ai_feedback_fields_to_ai_logs_table.php:95

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('user_rating');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 110. 2025_11_25_115537_add_split_commission_fields_to_satislar_and_komisyonlar_tables.php:166

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('satici_danisman_id');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 111. 2025_11_25_115537_add_split_commission_fields_to_satislar_and_komisyonlar_tables.php:169

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('alici_danisman_id');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 112. 2025_11_25_115537_add_split_commission_fields_to_satislar_and_komisyonlar_tables.php:172

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('satici_komisyon_orani');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 113. 2025_11_25_115537_add_split_commission_fields_to_satislar_and_komisyonlar_tables.php:175

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('alici_komisyon_orani');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 114. 2025_11_25_115537_add_split_commission_fields_to_satislar_and_komisyonlar_tables.php:178

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('satici_komisyon_tutari');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 115. 2025_11_25_115537_add_split_commission_fields_to_satislar_and_komisyonlar_tables.php:181

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('alici_komisyon_tutari');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 116. 2025_11_25_115537_add_split_commission_fields_to_satislar_and_komisyonlar_tables.php:200

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('satici_danisman_id');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 117. 2025_11_25_115537_add_split_commission_fields_to_satislar_and_komisyonlar_tables.php:203

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('alici_danisman_id');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 118. 2025_11_25_115537_add_split_commission_fields_to_satislar_and_komisyonlar_tables.php:206

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('satici_komisyon_orani');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 119. 2025_11_25_115537_add_split_commission_fields_to_satislar_and_komisyonlar_tables.php:209

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('alici_komisyon_orani');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 120. 2025_11_25_115537_add_split_commission_fields_to_satislar_and_komisyonlar_tables.php:212

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('satici_komisyon_tutari');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 121. 2025_11_25_115537_add_split_commission_fields_to_satislar_and_komisyonlar_tables.php:215

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('alici_komisyon_tutari');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 122. 2025_11_25_120501_add_split_commission_fields_to_satislar_and_komisyonlar_tables.php:125

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('satici_danisman_id');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 123. 2025_11_25_120501_add_split_commission_fields_to_satislar_and_komisyonlar_tables.php:128

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('alici_danisman_id');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 124. 2025_11_25_120501_add_split_commission_fields_to_satislar_and_komisyonlar_tables.php:131

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('satici_komisyon_orani');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 125. 2025_11_25_120501_add_split_commission_fields_to_satislar_and_komisyonlar_tables.php:134

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('alici_komisyon_orani');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 126. 2025_11_25_120501_add_split_commission_fields_to_satislar_and_komisyonlar_tables.php:137

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('satici_komisyon_tutari');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 127. 2025_11_25_120501_add_split_commission_fields_to_satislar_and_komisyonlar_tables.php:140

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('alici_komisyon_tutari');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 128. 2025_11_25_120501_add_split_commission_fields_to_satislar_and_komisyonlar_tables.php:160

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('satici_danisman_id');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 129. 2025_11_25_120501_add_split_commission_fields_to_satislar_and_komisyonlar_tables.php:163

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('alici_danisman_id');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 130. 2025_11_25_120501_add_split_commission_fields_to_satislar_and_komisyonlar_tables.php:166

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('satici_komisyon_orani');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 131. 2025_11_25_120501_add_split_commission_fields_to_satislar_and_komisyonlar_tables.php:169

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('alici_komisyon_orani');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 132. 2025_11_25_120501_add_split_commission_fields_to_satislar_and_komisyonlar_tables.php:172

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('satici_komisyon_tutari');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 133. 2025_11_25_120501_add_split_commission_fields_to_satislar_and_komisyonlar_tables.php:175

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('alici_komisyon_tutari');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 134. 2025_11_25_add_crm_fields_to_kisiler_table.php:59

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn([
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 135. 2025_11_30_120000_add_alt_kategori_id_to_talepler_table.php:46

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('alt_kategori_id');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 136. 2025_12_01_125702_add_telegram_cortex_fields_to_users_table.php:61

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('telegram_id');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 137. 2025_12_01_125702_add_telegram_cortex_fields_to_users_table.php:64

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('telegram_pairing_code');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 138. 2025_12_01_125702_add_telegram_cortex_fields_to_users_table.php:67

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('telegram_paired_at');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 139. 2025_12_03_000001_add_price_text_and_nearby_places_to_ilanlar_table.php:26

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('price_text');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 140. 2025_12_03_000001_add_price_text_and_nearby_places_to_ilanlar_table.php:30

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('nearby_places');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 141. 2025_12_03_010000_add_video_fields_to_ilanlar_table.php:30

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('video_url');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 142. 2025_12_03_010000_add_video_fields_to_ilanlar_table.php:34

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('video_status');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 143. 2025_12_03_010000_add_video_fields_to_ilanlar_table.php:38

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('video_last_frame');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 144. 2025_12_04_170137_add_environment_fields_to_ilanlar_table.php:60

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('environment_tags');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 145. 2025_12_04_170137_add_environment_fields_to_ilanlar_table.php:63

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('environment_pois');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

#### 146. 2025_12_05_000000_test_violation.php:29

- **Type:** METHOD
- **Issue:** Forbidden method: dropColumn()
- **Suggestion:** Always check column existence with Schema::hasColumn() first

**Original Code:**
```php
$table->dropColumn('order');
```

**Fix Example:**
```php
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
```

---

