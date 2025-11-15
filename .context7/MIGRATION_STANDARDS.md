# Context7 Migration Standards

**Tarih:** 2025-11-11  
**Durum:** ✅ ACTIVE - CRITICAL  
**Severity:** CRITICAL  
**Enforcement:** STRICT

---

## 🚫 FORBIDDEN PATTERNS

### 1. Laravel `renameColumn()` Kullanımı (MySQL İçin)

```php
// ❌ YANLIŞ: MySQL'de çalışmayabilir (doctrine/dbal gerektirir)
Schema::table('table_name', function (Blueprint $table) {
    $table->renameColumn('old_name', 'new_name');
});

// ✅ DOĞRU: MySQL için direkt SQL kullan
DB::statement("ALTER TABLE `table_name` CHANGE `old_name` `new_name` {$columnType} {$isNullable} {$default}");
```

### 2. Index ve Foreign Key Kontrolü Eksikliği

```php
// ❌ YANLIŞ: Index'leri kontrol etmeden rename
DB::statement("ALTER TABLE `table_name` CHANGE `old_name` `new_name` ...");

// ✅ DOĞRU: Index'leri kontrol et ve yeniden oluştur
$this->dropIndexesForColumn($tableName, 'old_name');
DB::statement("ALTER TABLE `table_name` CHANGE `old_name` `new_name` ...");
Schema::table($tableName, function (Blueprint $table) {
    $table->index('new_name', "idx_{$tableName}_new_name");
});
```

### 3. Kolon Tipi ve Özelliklerin Kaybolması

```php
// ❌ YANLIŞ: Kolon tipi ve özellikler kaybolur
DB::statement("ALTER TABLE `table_name` CHANGE `old_name` `new_name` INT");

// ✅ DOĞRU: Kolon tipi, nullable, default değerler korunmalı
$columnInfo = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'old_name'");
$col = $columnInfo[0];
$columnType = $col->Type;
$isNullable = $col->Null === 'YES' ? 'NULL' : 'NOT NULL';
$default = $col->Default !== null ? "DEFAULT {$col->Default}" : ($col->Null === 'YES' ? 'DEFAULT NULL' : 'DEFAULT 0');
DB::statement("ALTER TABLE `{$tableName}` CHANGE `old_name` `new_name` {$columnType} {$isNullable} {$default}");
```

### 4. Veri Kaybı Riski

```php
// ❌ YANLIŞ: Veri kontrolü yok
DB::statement("ALTER TABLE `table_name` CHANGE `old_name` `new_name` ...");

// ✅ DOĞRU: Veri kontrolü ve migration
if ($hasOldColumn && !$hasNewColumn) {
    // Veriyi yeni kolona kopyala
    DB::statement("UPDATE `{$tableName}` SET new_name = COALESCE(new_name, old_name) WHERE new_name IS NULL");
    // Sonra rename yap
}
```

---

## ✅ REQUIRED PATTERNS

### 1. Migration Dosya Yapısı

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Context7 Compliance: [Eski Kolon] → [Yeni Kolon]
 * 
 * Bu migration aşağıdaki tablolardaki [eski kolon] kolonlarını [yeni kolon] olarak yeniden adlandırır:
 * - table1.old_column → new_column
 * - table2.old_column → new_column
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = ['table1', 'table2'];
        
        foreach ($tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }
            
            $hasOldColumn = Schema::hasColumn($tableName, 'old_column');
            $hasNewColumn = Schema::hasColumn($tableName, 'new_column');
            
            if ($hasOldColumn && !$hasNewColumn) {
                // 1. Index'leri kontrol et ve kaldır
                $this->dropIndexesForColumn($tableName, 'old_column');
                
                // 2. Kolon bilgilerini al
                $columnInfo = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'old_column'");
                if (!empty($columnInfo)) {
                    $col = $columnInfo[0];
                    $columnType = $col->Type;
                    $isNullable = $col->Null === 'YES' ? 'NULL' : 'NOT NULL';
                    $default = $col->Default !== null 
                        ? "DEFAULT '{$col->Default}'" 
                        : ($col->Null === 'YES' ? 'DEFAULT NULL' : 'DEFAULT 0');
                    
                    // 3. MySQL'de direkt SQL ile rename
                    DB::statement("ALTER TABLE `{$tableName}` CHANGE `old_column` `new_column` {$columnType} {$isNullable} {$default}");
                } else {
                    // Fallback: Varsayılan tip
                    DB::statement("ALTER TABLE `{$tableName}` CHANGE `old_column` `new_column` INT NOT NULL DEFAULT 0");
                }
                
                // 4. Index'leri yeniden oluştur
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    try {
                        $table->index('new_column', "idx_{$tableName}_new_column");
                    } catch (\Exception $e) {
                        // Index zaten varsa devam et
                    }
                });
                
                echo "✅ Renamed: {$tableName}.old_column → {$tableName}.new_column\n";
            } elseif ($hasOldColumn && $hasNewColumn) {
                // Her iki kolon da varsa, veriyi migrate et
                DB::statement("UPDATE `{$tableName}` SET new_column = COALESCE(new_column, old_column) WHERE new_column IS NULL OR new_column = 0");
                echo "⚠️ Both columns exist: {$tableName}. Migrated data from old_column to new_column\n";
            }
        }
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['table1', 'table2'];
        
        foreach ($tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }
            
            $hasNewColumn = Schema::hasColumn($tableName, 'new_column');
            
            if ($hasNewColumn) {
                // Rollback: new_column → old_column
                $this->dropIndexesForColumn($tableName, 'new_column');
                
                $columnInfo = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'new_column'");
                if (!empty($columnInfo)) {
                    $col = $columnInfo[0];
                    $columnType = $col->Type;
                    $isNullable = $col->Null === 'YES' ? 'NULL' : 'NOT NULL';
                    $default = $col->Default !== null 
                        ? "DEFAULT '{$col->Default}'" 
                        : ($col->Null === 'YES' ? 'DEFAULT NULL' : 'DEFAULT 0');
                    
                    DB::statement("ALTER TABLE `{$tableName}` CHANGE `new_column` `old_column` {$columnType} {$isNullable} {$default}");
                }
                
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    try {
                        $table->index('old_column', "idx_{$tableName}_old_column");
                    } catch (\Exception $e) {
                        // Index zaten varsa devam et
                    }
                });
            }
        }
    }
    
    /**
     * Helper: Kolon için index'leri kaldır
     */
    private function dropIndexesForColumn(string $tableName, string $columnName): void
    {
        $indexes = DB::select("SHOW INDEXES FROM `{$tableName}` WHERE Column_name = '{$columnName}'");
        
        foreach ($indexes as $index) {
            if ($index->Key_name !== 'PRIMARY') {
                try {
                    DB::statement("ALTER TABLE `{$tableName}` DROP INDEX `{$index->Key_name}`");
                } catch (\Exception $e) {
                    // Index zaten yoksa devam et
                }
            }
        }
    }
};
```

### 2. Kolon Rename Standartları

#### 2.1. Context7 Forbidden → Required Mapping

| Eski Kolon | Yeni Kolon | Tablolar |
|------------|------------|----------|
| `order` | `display_order` | Tüm tablolar |
| `durum` | `status` | Genel kullanım (domain-specific hariç) |
| `aktif` | `status` | Tüm tablolar |
| `enabled` | `status` | Tüm tablolar |
| `is_active` | `status` | Tüm tablolar |
| `sehir` | `il` | Lokasyon tabloları |
| `musteri_*` | `kisi_*` | CRM tabloları |

#### 2.2. Özel Durumlar

- **`yazlik_doluluk_durumlari.durum`**: Domain-specific enum (`musait`, `rezerve`, `bloke`, vb.), `status` olarak değiştirilebilir ama zorunlu değil
- **Manzara değerleri**: Seeder'larda `sehir` değeri olarak kullanılanlar Context7 ihlali değil (veri değeri, kolon adı değil)

### 3. Migration Çalıştırma Standartları

#### 3.1. Migration Oluşturma Sonrası

```bash
# ✅ ZORUNLU ADIMLAR
# 1. Migration dosyasını kontrol et
cat database/migrations/[migration_file].php

# 2. Migration'ı çalıştır
php artisan migrate

# 3. Durumu kontrol et
php artisan migrate:status

# 4. Veritabanı şemasını kontrol et
php artisan db:show --table=table_name

# 5. Cache temizle
php artisan cache:clear
php artisan config:clear

# 6. Test et
# Sayfayı aç, endpoint'i test et
```

#### 3.2. Rollback Durumunda

```bash
# Rollback yapılacaksa
php artisan migrate:rollback --step=1

# Veya belirli migration'a kadar
php artisan migrate:rollback --path=database/migrations/[migration_file].php
```

---

## 📋 SEEDER STANDARDS

### 1. Seeder Dosya Yapısı

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Context7 Compliance: [Seeder Açıklaması]
 * 
 * Bu seeder [açıklama] için veri ekler.
 * Context7 standartlarına uygun kolon adları kullanılır:
 * - display_order (NOT order)
 * - status (NOT aktif, enabled, is_active)
 * - il (NOT sehir)
 * - kisi_* (NOT musteri_*)
 */
class ExampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tablo kontrolü
        if (!Schema::hasTable('table_name')) {
            $this->command->warn('Table table_name does not exist. Skipping...');
            return;
        }
        
        // Kolon kontrolü
        if (!Schema::hasColumn('table_name', 'display_order')) {
            $this->command->warn('Column display_order does not exist. Skipping...');
            return;
        }
        
        $data = [
            [
                'name' => 'Example',
                'display_order' => 1, // Context7: order → display_order
                'status' => 1, // Context7: aktif/enabled → status
            ],
            // ...
        ];
        
        foreach ($data as $item) {
            DB::table('table_name')->insertOrIgnore($item);
        }
        
        $this->command->info('✅ Seeded: table_name');
    }
}
```

### 2. Seeder Standartları

#### 2.1. Forbidden Patterns in Seeders

```php
// ❌ YANLIŞ: Eski kolon adları
$data = [
    ['name' => 'Example', 'order' => 1],
    ['name' => 'Example', 'aktif' => 1],
    ['name' => 'Example', 'enabled' => 1],
    ['name' => 'Example', 'sehir' => 'Istanbul'],
    ['name' => 'Example', 'musteri_adi' => 'John'],
];

// ✅ DOĞRU: Context7 standartları
$data = [
    ['name' => 'Example', 'display_order' => 1],
    ['name' => 'Example', 'status' => 1],
    ['name' => 'Example', 'il' => 'Istanbul'],
    ['name' => 'Example', 'kisi_adi' => 'John'],
];
```

#### 2.2. Query Methods

```php
// ❌ YANLIŞ: Eski kolon adları ile query
DB::table('table_name')->orderBy('order')->get();
DB::table('table_name')->where('aktif', 1)->get();

// ✅ DOĞRU: Context7 standartları
DB::table('table_name')->orderBy('display_order')->get();
DB::table('table_name')->where('status', 1)->get();
```

### 3. Seeder Çalıştırma Standartları

```bash
# ✅ ZORUNLU ADIMLAR
# 1. Migration'ların çalıştırıldığından emin ol
php artisan migrate:status

# 2. Seeder'ı çalıştır
php artisan db:seed --class=ExampleSeeder

# 3. Veriyi kontrol et
php artisan tinker
>>> DB::table('table_name')->count();

# 4. Cache temizle
php artisan cache:clear
```

---

## 🔍 PRE-COMMIT CHECKS

### 1. Migration Dosyası Kontrolü

```bash
# Migration dosyasında forbidden pattern kontrolü
grep -r "order\|aktif\|enabled\|is_active\|sehir\|musteri" database/migrations/[migration_file].php | grep -v "display_order\|status\|il\|kisi\|//"
```

### 2. Seeder Dosyası Kontrolü

```bash
# Seeder dosyasında forbidden pattern kontrolü
grep -r "'order'\|\"order\"\|'aktif'\|\"aktif\"\|'enabled'\|\"enabled\"" database/seeders/[seeder_file].php | grep -v "display_order\|status\|//"
```

---

## 📊 ENFORCEMENT CHECKLIST

### Migration Oluşturma

- [ ] Context7 forbidden → required mapping kontrolü
- [ ] Index ve foreign key kontrolü
- [ ] Kolon tipi ve özellikler korunuyor
- [ ] Veri kaybı riski yok
- [ ] `up()` ve `down()` metodları tam
- [ ] Helper metodlar (dropIndexesForColumn) var
- [ ] Tablo ve kolon varlık kontrolü var
- [ ] PHP DocBlock açıklaması var

### Seeder Oluşturma

- [ ] Context7 standart kolon adları kullanılıyor
- [ ] Tablo ve kolon varlık kontrolü var
- [ ] `insertOrIgnore` veya `updateOrInsert` kullanılıyor
- [ ] PHP DocBlock açıklaması var
- [ ] Query'lerde Context7 standartları kullanılıyor

### Migration Çalıştırma

- [ ] Migration durumu kontrol edildi
- [ ] Migration çalıştırıldı
- [ ] Veritabanı şeması kontrol edildi
- [ ] Cache temizlendi
- [ ] Test edildi

---

## 📚 REFERENCES

- `.context7/authority.json` (master authority file)
- `.context7/MIGRATION_EXECUTION_STANDARD.md`
- `.context7/ORDER_DISPLAY_ORDER_STANDARD.md`
- `.context7/ENABLED_FIELD_FORBIDDEN.md`

---

## 🎯 YALIHAN BEKÇİ RULES

### New Rules

1. **ALWAYS use DB::statement() for column renames in MySQL**
   - Reason: `renameColumn()` requires doctrine/dbal and may not work
   - Severity: CRITICAL
   - Action: Use `DB::statement("ALTER TABLE ... CHANGE ...")` pattern

2. **ALWAYS preserve column type, nullable, and default values**
   - Reason: Prevent data loss and schema issues
   - Severity: CRITICAL
   - Action: Use `SHOW COLUMNS` to get column info before rename

3. **ALWAYS handle indexes before column rename**
   - Reason: Indexes may prevent column rename
   - Severity: HIGH
   - Action: Drop indexes before rename, recreate after rename

4. **ALWAYS check table and column existence**
   - Reason: Prevent errors in different environments
   - Severity: HIGH
   - Action: Use `Schema::hasTable()` and `Schema::hasColumn()`

5. **ALWAYS use Context7 standard column names in seeders**
   - Reason: Consistency and compliance
   - Severity: CRITICAL
   - Action: Use `display_order`, `status`, `il`, `kisi_*` instead of forbidden patterns

6. **NEVER use Laravel renameColumn() in MySQL migrations**
   - Reason: Requires doctrine/dbal and may fail
   - Severity: CRITICAL
   - Action: Use `DB::statement()` with `ALTER TABLE ... CHANGE` pattern

---

**Last Updated:** 2025-11-11  
**Status:** ✅ ACTIVE - ENFORCED

