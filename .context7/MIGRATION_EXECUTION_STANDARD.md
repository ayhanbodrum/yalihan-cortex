# Context7 Standard: Migration Execution & Schema Sync

**Date:** 2025-11-09  
**Status:** ✅ ACTIVE - CRITICAL  
**Severity:** CRITICAL  
**Enforcement:** STRICT

---

## 🚫 FORBIDDEN PATTERN

### Migration Execution Negligence

```php
// ❌ NEVER DO THIS
// Migration dosyası var ama çalıştırılmamış
// Kod güncellenmiş ama veritabanı eski kalmış

// ✅ ALWAYS DO THIS
// 1. Migration oluştur
// 2. HEMEN çalıştır: php artisan migrate
// 3. Veritabanı şemasını kontrol et
// 4. Kodu test et
```

---

## 📋 RULE DETAILS

### Problem Pattern: Code-Database Mismatch

**Semptomlar:**
- `QueryException: Column not found`
- `SQLSTATE[42S22]: Unknown column`
- Kod yeni kolon adını kullanıyor ama veritabanı eski kolon adını içeriyor

**Kök Nedenler:**

1. **Migration Çalıştırılmamış**
   - Migration dosyası var ama `php artisan migrate` çalıştırılmamış
   - Kod güncellenmiş ama veritabanı güncellenmemiş

2. **Context7 Standardizasyon Süreci**
   - Kod Context7 standartlarına göre güncellenmiş
   - Migration'lar oluşturulmuş ama çalıştırılmamış
   - Veritabanı eski şemada kalmış

3. **Veritabanı Restore/Rollback**
   - Veritabanı eski bir backup'tan restore edilmiş
   - Migration'lar rollback edilmiş
   - Kod yeni şemayı bekliyor

---

## 🎯 MANDATORY WORKFLOW

### 1. Migration Oluşturma Sonrası

```bash
# ✅ ZORUNLU ADIMLAR
php artisan migrate              # Migration'ı çalıştır
php artisan migrate:status        # Durumu kontrol et
php artisan db:show --table=tablo_adi  # Şemayı kontrol et
```

### 2. Kod Güncelleme Sonrası

```bash
# ✅ ZORUNLU ADIMLAR
# 1. Migration oluştur (eğer kolon değişiyorsa)
php artisan make:migration add_column_to_table

# 2. Migration'ı çalıştır
php artisan migrate

# 3. Cache temizle
php artisan cache:clear
php artisan config:clear

# 4. Test et
# Sayfayı aç, endpoint'i test et
```

### 3. Context7 Standardizasyon Süreci

```bash
# ✅ ZORUNLU ADIMLAR
# 1. Kod güncelle (order → display_order)
# 2. Migration oluştur (rename kolon)
# 3. Migration'ı çalıştır
php artisan migrate

# 4. Veritabanı şemasını kontrol et
DESCRIBE ilan_kategorileri;  # display_order var mı?

# 5. Cache temizle
php artisan cache:clear

# 6. Test et
```

---

## 🔧 PREVENTION PATTERNS

### 1. Schema Check Before Code

```php
// ✅ DOĞRU: Kolon varlığını kontrol et
use Illuminate\Support\Facades\Schema;

$orderColumn = Schema::hasColumn('ilan_kategorileri', 'display_order') 
    ? 'display_order' 
    : 'order';

$query->select([$orderColumn]);
```

### 2. Try-Catch for Missing Columns

```php
// ✅ DOĞRU: Güvenli sorgu
try {
    $count = Eslesme::where('skor', '>=', 8)->count();
} catch (\Exception $e) {
    // Kolon yoksa güvenli değer döndür
    $count = 0;
}
```

### 3. Migration Guards

```php
// ✅ DOĞRU: Kolon bazlı kontrol
if (!Schema::hasColumn('table_name', 'column_name')) {
    Schema::table('table_name', function (Blueprint $table) {
        $table->integer('column_name')->default(0);
    });
}

// ❌ YANLIŞ: Tablo bazlı kontrol (migration'ı atlatabilir)
if (Schema::hasTable('table_name')) {
    return; // Migration atlanıyor!
}
```

---

## 📊 COMMON ISSUES & SOLUTIONS

### Issue 1: `order` → `display_order`

**Semptom:**
```
QueryException: Unknown column 'order' in 'field list'
```

**Çözüm:**
```bash
# Migration çalıştır
php artisan migrate

# Migration: 2025_11_09_070721_rename_order_to_display_order_in_tables.php
```

### Issue 2: Missing `skor` Column

**Semptom:**
```
QueryException: Unknown column 'skor' in 'where clause'
```

**Çözüm:**
```bash
# Migration çalıştır
php artisan migrate

# Migration: 2025_11_09_095517_add_skor_column_to_eslesmeler_table_if_missing.php
```

### Issue 3: Cache Issues

**Semptom:**
- Migration çalıştırıldı ama hala eski hata görünüyor

**Çözüm:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

---

## ✅ ENFORCEMENT CHECKLIST

### Before Code Changes

- [ ] Migration durumunu kontrol et: `php artisan migrate:status`
- [ ] Veritabanı şemasını kontrol et: `DESCRIBE table_name`
- [ ] Context7 compliance kontrol et: `.context7/authority.json`

### After Code Changes

- [ ] Migration oluştur (eğer gerekiyorsa)
- [ ] Migration'ı çalıştır: `php artisan migrate`
- [ ] Şemayı kontrol et: `DESCRIBE table_name`
- [ ] Cache temizle: `php artisan cache:clear`
- [ ] Test et: Sayfayı aç, endpoint'i test et

### Context7 Standardization

- [ ] Kod güncelle (yeni kolon adı)
- [ ] Migration oluştur (kolon rename/add)
- [ ] Migration'ı çalıştır
- [ ] Backward compatibility ekle (accessor/mutator)
- [ ] Test et
- [ ] Dokümante et

---

## 📚 REFERENCES

- `.context7/authority.json` (master authority file)
- `.context7/ORDER_DISPLAY_ORDER_STANDARD.md`
- `.context7/MIGRATION_COMPLIANCE_REPORT.md`
- `.yalihan-bekci/knowledge/database-schema-sync-patterns-2025-11-08.json`

---

## 🎯 YALIHAN BEKÇİ RULES

### New Rules

1. **ALWAYS check migration status before suggesting code**
   - Reason: Prevent code-database mismatch
   - Severity: CRITICAL
   - Action: Run `php artisan migrate:status` before code suggestions

2. **ALWAYS verify column existence before querying**
   - Reason: Prevent QueryException errors
   - Severity: CRITICAL
   - Action: Use `Schema::hasColumn()` or try-catch

3. **ALWAYS run migrations after creating them**
   - Reason: Code expects new schema
   - Severity: CRITICAL
   - Action: Immediately run `php artisan migrate`

4. **ALWAYS clear cache after schema changes**
   - Reason: Cache might have old queries
   - Severity: HIGH
   - Action: Run `php artisan cache:clear`

5. **NEVER suggest code that uses non-existent columns**
   - Reason: Prevent QueryException errors
   - Severity: CRITICAL
   - Action: Verify column existence first

---

**Last Updated:** 2025-11-09  
**Status:** ✅ ACTIVE - ENFORCED

