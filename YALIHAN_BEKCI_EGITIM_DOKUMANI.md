# 🎓 YALIHAN BEKÇİ EĞİTİM DOKÜMANI

**Tarih:** 2025-11-12  
**Versiyon:** 1.0.0  
**Durum:** ✅ AKTIF  
**Hedef:** Yalıhan Bekçi'ye tüm işlemleri öğretmek

---

## 📚 İÇİNDEKİLER

1. [Context7 Nedir?](#context7-nedir)
2. [Yalıhan Bekçi'nin Rolü](#yalihan-bekcinin-rolü)
3. [Temel Kurallar ve Standartlar](#temel-kurallar-ve-standartlar)
4. [Migration İşlemleri](#migration-işlemleri)
5. [Seeder İşlemleri](#seeder-işlemleri)
6. [Kod Kontrolü ve Doğrulama](#kod-kontrolü-ve-doğrulama)
7. [Tailwind CSS ve Tasarım Standartları](#tailwind-css-ve-tasarım-standartları)
8. [API ve Servis Standartları](#api-ve-servis-standartları)
9. [Hata Ayıklama ve Çözüm Yöntemleri](#hata-ayıklama-ve-çözüm-yöntemleri)
10. [Warp Workflows Kullanımı](#warp-workflows-kullanımı)
11. [Günlük İşlem Rutini](#günlük-işlem-rutini)

---

## 🎯 CONTEXT7 NEDİR?

Context7, Yalıhan Emlak projesinin kod standartları ve kurallarını yöneten merkezi bir sistemdir.

### İki Ana Bileşen:

1. **Upstash Context7 MCP**
    - Kütüphane dokümantasyonu (Laravel, React, MySQL vb.)
    - Güncel API örnekleri
    - Versiyon-spesifik dokümantasyon

2. **Yalıhan Bekçi Context7**
    - Proje kuralları ve standartları
    - Kod doğrulaması
    - Pattern kontrolü
    - Sistem yapısı kontrolü

### Kullanıcı "Context7 Kullan" Dediğinde:

```
1. Upstash Context7 MCP → Otomatik aktif
2. Yalıhan Bekçi Context7 → Otomatik aktif
3. Kütüphane dokümantasyonu + Proje kuralları → Birleştirilir
4. Context7 uyumlu kod → Üretilir/Doğrulanır
```

### 📸 Snapshot Raporlar vs Aktif Standartlar

**KRITIK:** Audit raporları SNAPSHOT'tır, yapılacak iş listesi DEĞİL.

| Klasör                             | Durum    | Yorum                           |
| ---------------------------------- | -------- | ------------------------------- |
| `docs/archive/**`                  | 📦 ARŞİV | Tarihsel kayıt, tarama dışı tut |
| `.context7/archive/**`             | 📦 ARŞİV | Tarihsel kayıt, tarama dışı tut |
| `yalihan-bekci/reports/archive/**` | 📦 ARŞİV | Snapshot raporlar, referans     |

**Örnek:**

- MD_AUDIT_SUMMARY.txt içinde [outdated] var
- Ama docs/archive/ klasöründe
- → Bu "yapılacak iş" DEĞİL, tarihsel kayıt

**Tarama yapılırken:**

```bash
# ✅ DOĞRU
grep -r "forbidden_pattern" --exclude-dir="archive" app/

# ❌ YANLIŞ
grep -r "forbidden_pattern" docs/  # archive dahil
```

**Aktif standartlar:**

- `.context7/authority.json`
- `docs/active/RULES_KONSOLIDE_2025_11_25.md`
- `YALIHAN_BEKCI_EGITIM_DOKUMANI.md`

---

## 🛡️ YALIHAN BEKÇİ'NİN ROLÜ

### Ana Görevler:

#### 1. **Kod Doğrulama (Code Validation)**

```bash
# Yasaklı pattern kontrolü
grep -r "order\|aktif\|enabled\|is_active" --include="*.php" app/
```

#### 2. **Migration Kontrolü**

```bash
# Migration dosyalarını kontrol et
cat database/migrations/[migration_file].php
```

#### 3. **Seeder Kontrolü**

```bash
# Seeder dosyalarını kontrol et
grep -r "'order'\|\"order\"\|'aktif'" database/seeders/
```

#### 4. **Pre-commit Hook Çalıştırma**

```bash
# Pre-commit hook'u manuel çalıştır
.githooks/context7-pre-commit
```

#### 5. **Raporlama**

```bash
# Bekçi raporu oluştur
php artisan yalihan-bekci:report
```

---

## 📋 TEMEL KURALLAR VE STANDARTLAR

### ❌ YASAKLI PATTERN'LER

#### 1. Database Kolonları

| Yasaklı     | Doğru           | Sebep                  |
| ----------- | --------------- | ---------------------- |
| `order`     | `display_order` | SQL anahtar kelimesi   |
| `durum`     | `status`        | Türkçe kolon adı yasak |
| `aktif`     | `status`        | Türkçe kolon adı yasak |
| `enabled`   | `status`        | Boolean field yasak    |
| `is_active` | `status`        | Boolean field yasak    |
| `sehir_id`  | `il_id`         | Yanlış terminoloji     |
| `musteri_*` | `kisi_*`        | Yanlış terminoloji     |

#### 2. CSS Class'ları

| Yasaklı        | Doğru                    | Sebep            |
| -------------- | ------------------------ | ---------------- |
| `neo-btn`      | Tailwind utility classes | Neo Design YASAK |
| `neo-card`     | Tailwind utility classes | Neo Design YASAK |
| `neo-input`    | Tailwind utility classes | Neo Design YASAK |
| `btn-*`        | Tailwind utility classes | Bootstrap YASAK  |
| `form-control` | Tailwind utility classes | Bootstrap YASAK  |

#### 3. JavaScript

| Yasaklı              | Doğru                | Sebep              |
| -------------------- | -------------------- | ------------------ |
| React-Select (170KB) | Vanilla JS (3KB)     | Çok ağır           |
| Choices.js (48KB)    | Vanilla JS           | Çok ağır           |
| Select2              | Context7 Live Search | jQuery bağımlılığı |

### ✅ ZORUNLU STANDARTLAR

#### 1. Tailwind CSS Zorunluluğu

```html
<!-- ❌ YANLIŞ -->
<button class="neo-btn">Kaydet</button>

<!-- ✅ DOĞRU -->
<button
    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 
               transition-all duration-200 dark:bg-blue-700 dark:hover:bg-blue-800
               focus:ring-2 focus:ring-blue-500"
>
    Kaydet
</button>
```

#### 2. Transition Zorunluluğu

```css
/* Her interaktif elementte ZORUNLU */
transition-all duration-200

/* Hover efektleri */
hover:scale-105 hover:shadow-lg

/* Active state */
active:scale-95
```

#### 3. Dark Mode Zorunluluğu

```html
<!-- Her element dark mode variant içermeli -->
<div
    class="bg-white dark:bg-gray-800 
            text-gray-900 dark:text-white
            border-gray-200 dark:border-gray-700"
></div>
```

---

## 🗄️ MIGRATION İŞLEMLERİ

### Migration Oluşturma Şablonu

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
                echo "⚠️ Table {$tableName} does not exist. Skipping...\n";
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

### Migration Çalıştırma Adımları

```bash
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

# 6. Test et (endpoint'i veya sayfayı aç)
```

### Migration Rollback

```bash
# Son migration'ı geri al
php artisan migrate:rollback --step=1

# Belirli bir migration'ı geri al
php artisan migrate:rollback --path=database/migrations/[migration_file].php
```

---

## 🌱 SEEDER İŞLEMLERİ

### Seeder Oluşturma Şablonu

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
                'name' => 'Example 1',
                'display_order' => 1, // Context7: order → display_order
                'status' => 1, // Context7: aktif/enabled → status
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Example 2',
                'display_order' => 2,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($data as $item) {
            DB::table('table_name')->insertOrIgnore($item);
        }

        $this->command->info('✅ Seeded: table_name (' . count($data) . ' records)');
    }
}
```

### Seeder Çalıştırma Adımları

```bash
# 1. Migration'ların çalıştırıldığından emin ol
php artisan migrate:status

# 2. Seeder'ı çalıştır
php artisan db:seed --class=ExampleSeeder

# 3. Veriyi kontrol et
php artisan tinker
>>> DB::table('table_name')->count();
>>> DB::table('table_name')->first();

# 4. Cache temizle
php artisan cache:clear
```

---

## 🔍 KOD KONTROLÜ VE DOĞRULAMA

### 1. Yasaklı Pattern Kontrolü

```bash
# Database kolonları kontrolü
grep -r "order\|aktif\|enabled\|is_active\|sehir\|musteri" \
  --include="*.php" \
  app/ database/ \
  | grep -v "display_order\|status\|il\|kisi\|//"

# Migration dosyaları kontrolü
grep -r "renameColumn" database/migrations/

# Neo Design kontrolü
grep -r "neo-btn\|neo-card\|neo-input" resources/views/
```

### 2. Context7 Uyumluluk Kontrolü

```bash
# Tüm Context7 ihlallerini kontrol et
php artisan yalihan-bekci:check

# Sadece migration kontrolü
php artisan yalihan-bekci:check --type=migration

# Sadece seeder kontrolü
php artisan yalihan-bekci:check --type=seeder

# Otomatik düzeltme
php artisan yalihan-bekci:check --auto-fix
```

### 3. Kod Kalitesi Kontrolü

```bash
# PHPStan analizi
./vendor/bin/phpstan analyse

# PHP CS Fixer
./vendor/bin/php-cs-fixer fix --dry-run

# Pint (Laravel code style)
./vendor/bin/pint --test
```

---

## 🎨 TAILWIND CSS VE TASARIM STANDARTLARI

### Temel Tailwind Pattern'leri

#### 1. Button

```html
<!-- Primary Button -->
<button
    class="px-4 py-2 bg-blue-600 text-white rounded-lg 
               hover:bg-blue-700 active:scale-95
               transition-all duration-200 
               dark:bg-blue-700 dark:hover:bg-blue-800
               focus:ring-2 focus:ring-blue-500 focus:outline-none"
>
    Kaydet
</button>

<!-- Secondary Button -->
<button
    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg 
               hover:bg-gray-300 active:scale-95
               transition-all duration-200 
               dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600
               focus:ring-2 focus:ring-gray-400 focus:outline-none"
>
    İptal
</button>
```

#### 2. Input

```html
<input
    type="text"
    class="w-full px-4 py-2.5 
              border border-gray-300 rounded-lg 
              focus:ring-2 focus:ring-blue-500 focus:border-blue-500
              transition-all duration-200
              dark:bg-gray-800 dark:border-gray-700 dark:text-white
              dark:focus:ring-blue-600"
    placeholder="Ad Soyad"
/>
```

#### 3. Select

```html
<select
    class="w-full px-4 py-2.5 
               border border-gray-300 rounded-lg 
               cursor-pointer
               focus:ring-2 focus:ring-blue-500 focus:border-blue-500
               transition-all duration-200
               dark:bg-gray-900 dark:border-gray-700 dark:text-white
               dark:focus:ring-blue-600"
    style="color-scheme: light dark;"
>
    <option value="" class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 py-2">
        Seçiniz
    </option>
    <option
        value="1"
        class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-2 font-medium"
    >
        Seçenek 1
    </option>
</select>
```

#### 4. Card

```html
<div
    class="bg-white rounded-xl shadow-lg 
            border border-gray-200 
            p-6
            transition-all duration-300 
            hover:shadow-xl hover:scale-[1.02]
            dark:bg-gray-800 dark:border-gray-700"
>
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Başlık</h3>
    <p class="text-gray-600 dark:text-gray-400">İçerik</p>
</div>
```

### Zorunlu Kurallar

```css
/* 1. Her interaktif element transition içermeli */
transition-all duration-200

/* 2. Dark mode variant'ları zorunlu */
dark:bg-gray-800 dark:text-white dark:border-gray-700

/* 3. Focus state'leri zorunlu (accessibility) */
focus:ring-2 focus:ring-blue-500 focus:outline-none

/* 4. Hover efektleri zorunlu */
hover:bg-blue-700 hover:shadow-lg

/* 5. Active state'ler zorunlu */
active:scale-95
```

---

## 🌐 API VE SERVİS STANDARTLARI

### Response Format

```php
// Başarılı response
return ResponseService::success([
    'data' => $data,
    'message' => 'İşlem başarılı'
], 200);

// Hata response
return ResponseService::error(
    'Hata mesajı',
    ['field' => 'Detaylı hata'],
    422
);

// Server error
return ResponseService::serverError('Sunucu hatası', $exception);
```

### Cache Kullanımı

```php
// Cache'e kaydet
CacheHelper::remember('namespace', 'key', 'medium', function() {
    return $data;
});

// Cache'den al
$data = CacheHelper::get('namespace', 'key');

// Cache'i temizle
CacheHelper::forget('namespace', 'key');
```

### Logging

```php
// Genel log
LogService::info('Bilgi mesajı', ['context' => $data]);
LogService::error('Hata mesajı', ['context' => $data], $exception);

// API log
LogService::api('/api/endpoint', $requestData, $responseData, $duration);

// Database log
LogService::database('insert', 'table_name', $data, $affectedRows);

// Auth log
LogService::auth('login', $userId, ['ip' => $ip]);
```

---

## 🐛 HATA AYIKLAMA VE ÇÖZÜM YÖNTEMLERİ

### Sık Karşılaşılan Hatalar ve Çözümleri

#### 1. Migration Hatası: "Column not found"

```bash
# Sorun: Kolon adı yanlış veya kolon yok

# Çözüm 1: Kolon varlığını kontrol et
php artisan tinker
>>> use Illuminate\Support\Facades\Schema;
>>> Schema::hasColumn('table_name', 'column_name');

# Çözüm 2: Migration'ı düzelt ve yeniden çalıştır
php artisan migrate:rollback --step=1
# Migration dosyasını düzelt
php artisan migrate
```

#### 2. Seeder Hatası: "Unknown column"

```bash
# Sorun: Seeder'da yanlış kolon adı kullanılmış

# Çözüm: Seeder'ı Context7'ye uygun hale getir
# order → display_order
# aktif → status
# enabled → status
```

#### 3. Tailwind CSS Çalışmıyor

```bash
# Çözüm: Asset'leri yeniden derle
npm run build

# veya development mode'da
npm run dev
```

#### 4. Dark Mode Çalışmıyor

```html
<!-- Sorun: dark: variant'ları eksik -->

<!-- Çözüm: Her element dark mode içermeli -->
<div
    class="bg-white dark:bg-gray-800 
            text-gray-900 dark:text-white"
></div>
```

---

## ⚡ WARP WORKFLOWS KULLANIMI

### Mevcut Workflow'lar

1. **Context7: Compliance Check**

    ```bash
    # Çalıştırma
    Context7: Compliance Check
    ```

2. **Context7: Auto-fix Violations**

    ```bash
    # Çalıştırma
    Context7: Auto-fix Violations
    ```

3. **Context7: Forbidden Pattern Scan (quick)**

    ```bash
    # Çalıştırma
    Context7: Forbidden Pattern Scan (quick)
    ```

4. **Context7: Standardization Scan**

    ```bash
    # Çalıştırma
    Context7: Standardization Scan
    ```

5. **Context7: Laravel Cache Refresh**
    ```bash
    # Çalıştırma
    Context7: Laravel Cache Refresh
    ```

### Yeni Workflow Oluşturma

```yaml
# .warp/workflows/context7-custom.yaml

name: Context7 Custom Check
command: |-
    php artisan yalihan-bekci:check --type=custom
tags:
    - context7
    - custom
description: Özel Context7 kontrolü
```

---

## 📅 GÜNLÜK İŞLEM RUTİNİ

### Sabah Rutini (Günlük Başlangıç)

```bash
# 1. Git pull (güncellemeleri al)
git pull origin main

# 2. Composer paketlerini güncelle
composer install

# 3. NPM paketlerini güncelle
npm install

# 4. Migration'ları kontrol et
php artisan migrate:status

# 5. Context7 uyumluluk kontrolü
php artisan yalihan-bekci:check

# 6. Cache temizle
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Kod Yazma Sırasında

```bash
# 1. Yeni migration oluşturma
php artisan make:migration create_example_table
# Migration dosyasını Context7'ye uygun yaz

# 2. Migration'ı çalıştır
php artisan migrate

# 3. Seeder oluştur
php artisan make:seeder ExampleSeeder
# Seeder'ı Context7'ye uygun yaz

# 4. Seeder'ı çalıştır
php artisan db:seed --class=ExampleSeeder

# 5. Kod kontrolü yap
php artisan yalihan-bekci:check

# 6. Test et
# Sayfayı aç, endpoint'i test et
```

### Akşam Rutini (Günlük Bitiş)

```bash
# 1. Son kontroller
php artisan yalihan-bekci:check

# 2. PHPStan analizi
./vendor/bin/phpstan analyse

# 3. PHP CS Fixer
./vendor/bin/pint

# 4. Git add ve commit
git add .
git commit -m "feat: [açıklama] (Context7 uyumlu)"

# 5. Git push
git push origin main

# 6. Yalıhan Bekçi raporu
php artisan yalihan-bekci:report
```

---

## 📊 KONTROL LİSTESİ

### Migration Oluşturma Kontrol Listesi

- [ ] Context7 forbidden → required mapping kontrolü
- [ ] Index ve foreign key kontrolü
- [ ] Kolon tipi ve özellikler korunuyor
- [ ] Veri kaybı riski yok
- [ ] `up()` ve `down()` metodları tam
- [ ] Helper metodlar (dropIndexesForColumn) var
- [ ] Tablo ve kolon varlık kontrolü var
- [ ] PHP DocBlock açıklaması var

### Seeder Oluşturma Kontrol Listesi

- [ ] Context7 standart kolon adları kullanılıyor
- [ ] Tablo ve kolon varlık kontrolü var
- [ ] `insertOrIgnore` veya `updateOrInsert` kullanılıyor
- [ ] PHP DocBlock açıklaması var
- [ ] Query'lerde Context7 standartları kullanılıyor

### Blade Template Kontrol Listesi

- [ ] Neo Design class'ları YOK (neo-btn, neo-card vb.)
- [ ] Tailwind utility classes kullanılıyor
- [ ] `transition-all duration-200` her interaktif elementte
- [ ] `dark:` variant'ları her elementte
- [ ] `focus:ring-2 focus:ring-blue-500` accessibility için
- [ ] Vanilla JS kullanılıyor (ağır kütüphaneler YOK)

### API/Controller Kontrol Listesi

- [ ] ResponseService kullanılıyor
- [ ] CacheHelper kullanılıyor
- [ ] LogService kullanılıyor
- [ ] Context7 standart kolon adları kullanılıyor
- [ ] PHPDoc açıklamaları tam

---

## 🔗 REFERANS DOSYALAR

### Ana Dokümantasyon

- `.context7/authority.json` - Master otorite dosyası
- `.context7/CURSOR_MEMORY_CONTEXT7.md` - Context7 memory dokümantasyonu
- `.context7/MIGRATION_STANDARDS.md` - Migration standartları
- `.context7/MIGRATION_TEMPLATE_STANDARDS.md` - Migration template standartları

### Yalıhan Bekçi Dosyaları

- `yalihan-bekci/knowledge/` - Öğrenme raporları
- `yalihan-bekci/reports/` - Bekçi raporları
- `yalihan-bekci/rules/` - Kural dosyaları
- `yalihan-bekci/analysis/` - Analiz raporları

### Proje Dosyaları

- `DEVELOPER_ONBOARDING_CONTEXT7.md` - Geliştirici oryantasyon
- `stubs/migration.create.stub` - Migration şablonu
- `.warp/workflows/context7.yaml` - Warp workflow'ları

---

## 💡 İPUÇLARI VE EN İYİ UYGULAMALAR

### 1. Migration İpuçları

```bash
# Her zaman tablo ve kolon varlığını kontrol et
Schema::hasTable('table_name')
Schema::hasColumn('table_name', 'column_name')

# MySQL için direkt SQL kullan (renameColumn YASAK)
DB::statement("ALTER TABLE ...")

# Index'leri kontrol et ve yeniden oluştur
$this->dropIndexesForColumn($tableName, 'old_column')
```

### 2. Seeder İpuçları

```bash
# insertOrIgnore kullan (duplicate entry hatası önlenir)
DB::table('table_name')->insertOrIgnore($data);

# Timestamp'leri unutma
'created_at' => now(),
'updated_at' => now(),
```

### 3. Blade Template İpuçları

```html
<!-- Tailwind class'larını grupla -->
<!-- Layout → Typography → Colors → Effects → States → Responsive -->
<div
    class="flex items-center justify-between 
            text-lg font-semibold 
            text-gray-900 dark:text-white
            transition-all duration-200
            hover:scale-105 active:scale-95
            md:text-xl lg:text-2xl"
></div>
```

### 4. API İpuçları

```php
// Her zaman try-catch kullan
try {
    $result = $service->doSomething();
    return ResponseService::success($result);
} catch (\Exception $e) {
    LogService::error('Hata', ['context' => $data], $e);
    return ResponseService::serverError('İşlem başarısız', $e);
}
```

---

## ❓ SIKÇA SORULAN SORULAR

### S1: "Context7 kullan" ne demek?

**C:** Hem Upstash Context7 MCP hem de Yalıhan Bekçi Context7'yi otomatik aktif et, kütüphane dokümantasyonu + proje kurallarını birleştir, Context7 uyumlu kod üret/doğrula.

### S2: Migration'da renameColumn neden yasak?

**C:** MySQL'de `renameColumn()` doctrine/dbal gerektirir ve çalışmayabilir. Bunun yerine `DB::statement("ALTER TABLE ... CHANGE ...")` kullanılmalı.

### S3: Neo Design neden yasak?

**C:** 1 Kasım 2025'te BREAKING CHANGE yapıldı. Neo Design kaldırıldı, Pure Tailwind CSS ZORUNLU hale getirildi. Neo-\* class'ları artık YASAK.

### S4: order kolonu neden yasak?

**C:** `order` SQL anahtar kelimesidir. `display_order` kullanılmalı. Ayrıca Context7 standardı `display_order` kullanımını zorunlu kılar.

### S5: status field'ı nasıl olmalı?

**C:**

- Boolean field'lar (`aktif`, `enabled`, `is_active`) YASAK
- Sadece `status` field'ı kullanılmalı
- Tip: `tinyInteger` (1 = aktif, 0 = pasif) veya `string` (enum değerler)

---

## 🎓 SONUÇ

Bu doküman, Yalıhan Bekçi'nin Context7 standartlarına uygun kod yazması, kontrol etmesi ve doğrulaması için gerekli tüm bilgileri içerir.

### Unutma:

1. ✅ Context7 kullan → Otomatik aktivasyon
2. ✅ Migration'larda DB::statement() kullan
3. ✅ Tailwind CSS ONLY, Neo Design YASAK
4. ✅ transition-all duration-200 ZORUNLU
5. ✅ dark: variant'ları ZORUNLU
6. ✅ display_order ZORUNLU, order YASAK
7. ✅ status ZORUNLU, aktif/enabled/is_active YASAK

**Son Güncelleme:** 2025-11-12  
**Versiyon:** 1.0.0  
**Durum:** ✅ AKTIF

---

**📞 İletişim:**  
Sorularınız için: `.context7/authority.json` dosyasını kontrol edin  
Yalıhan Bekçi: `php artisan yalihan-bekci:help`
