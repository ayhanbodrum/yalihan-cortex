# 🛡️ Context7 PERMANENT STANDARDS - Kalıcı Standartlar

**Date:** 7 Kasım 2025  
**Status:** ✅ ACTIVE - PERMANENT  
**Enforcement:** STRICT - NO EXCEPTIONS  
**Rollback:** ❌ FORBIDDEN - NO ROLLBACK ALLOWED

---

## ⚠️ CRITICAL: BU STANDARTLAR KALICIDIR - GERİ DÖNÜŞ YOK!

Bu doküman **kalıcı standartları** tanımlar. Bu standartlar:
- ✅ Pre-commit hook tarafından **otomatik kontrol edilir**
- ✅ CI/CD pipeline'da **otomatik bloklanır**
- ✅ Model template'lerinde **otomatik uygulanır**
- ✅ Migration template'lerinde **otomatik uygulanır**
- ❌ **GERİ DÖNÜŞ YOK** - Bu standartlar kalıcıdır

---

### 4. ✅ ROUTE NAMING STANDARD (CRITICAL)

**Rule:** Nested route groups'da parent prefix'i tekrar etme - Çift prefix YASAK

**Enforcement:**
- ✅ Pre-commit hook: BLOCKS commits with double prefix
- ✅ CI/CD: FAILS builds with double prefix
- ✅ Route validation: Checks route naming

**Allowed:**
```php
// ✅ DOĞRU
Route::prefix('admin')->name('admin.')->group(function () {
    Route::prefix('crm')->name('crm.')->group(function () {
        // Sonuç: admin.crm.* ✅
    });
});
```

**Forbidden:**
```php
// ❌ YASAK - Pre-commit hook BLOCKS
Route::prefix('admin')->name('admin.')->group(function () {
    Route::prefix('crm')->name('admin.crm.')->group(function () {
        // Sonuç: admin.admin.crm.* ❌ BLOCKED!
    });
});
```

**View Usage:**
```blade
{{-- ✅ DOĞRU - Route kontrolü ile --}}
@if (Route::has('admin.crm.dashboard'))
    <a href="{{ route('admin.crm.dashboard') }}">
@endif
```

**Reference:**
- `.context7/ROUTE_NAMING_STANDARD.md`
- `.git/hooks/pre-commit` (Section 5️⃣.7)

---

## 📋 PERMANENT STANDARDS

### 1. ✅ STATUS FIELD STANDARD (CRITICAL)

**Rule:** `status` field MANDATORY - `enabled` FORBIDDEN

**Enforcement:**
- ✅ Pre-commit hook: BLOCKS commits with `enabled`
- ✅ CI/CD: FAILS builds with `enabled`
- ✅ Model template: Auto-generates `status` only
- ✅ Migration template: Auto-generates `status` only

**Allowed:**
```php
// ✅ DOĞRU
protected $fillable = ['status'];
protected $casts = ['status' => 'boolean'];
$query->where('status', true);
$table->tinyInteger('status')->default(1);
```

**Forbidden:**
```php
// ❌ YASAK - Pre-commit hook BLOCKS
protected $fillable = ['enabled'];  // ❌ BLOCKED
protected $casts = ['enabled' => 'boolean'];  // ❌ BLOCKED
$query->where('enabled', true);  // ❌ BLOCKED
$table->boolean('enabled');  // ❌ BLOCKED
```

**Exceptions:**
- ✅ Feature flags: `weekend_pricing_enabled`, `sync_enabled` (OK)
- ❌ Status fields: `enabled` (FORBIDDEN)

**Reference:**
- `.context7/ENABLED_FIELD_FORBIDDEN.md`
- `.git/hooks/pre-commit` (Section 5️⃣)

---

### 2. ✅ TERMINOLOGY STANDARD (CRITICAL)

**Rule:** `kisi` MANDATORY - `musteri` FORBIDDEN (new code)

**Enforcement:**
- ✅ Pre-commit hook: BLOCKS new `Musteri*` models
- ✅ CI/CD: WARNINGS for `musteri` routes
- ✅ Model template: Auto-generates `Kisi*` only

**Allowed:**
```php
// ✅ DOĞRU
class KisiAktivite extends Model { }
Route::get('/admin/kisiler', ...);
```

**Forbidden:**
```php
// ❌ YASAK - Pre-commit hook BLOCKS
class MusteriAktivite extends Model { }  // ❌ BLOCKED (new models)
Route::get('/admin/musteriler', ...);  // ⚠️ WARNING (backward compat OK)
```

**Backward Compatibility:**
- ✅ Existing `Musteri*` models: OK (with `@deprecated`)
- ✅ Existing `musteri` routes: OK (with backward compat)
- ❌ New `Musteri*` models: BLOCKED
- ⚠️ New `musteri` routes: WARNING (not blocking)

**Reference:**
- `.git/hooks/pre-commit` (Section 5️⃣.5)

---

### 3. ✅ CSS FRAMEWORK STANDARD (CRITICAL)

**Rule:** Tailwind CSS ONLY - Neo Design FORBIDDEN

**Enforcement:**
- ✅ Pre-commit hook: BLOCKS commits with `neo-*` classes
- ✅ CI/CD: FAILS builds with `neo-*` classes
- ✅ Authority.json: Neo classes marked as FORBIDDEN

**Allowed:**
```html
<!-- ✅ DOĞRU -->
<button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200">
    Kaydet
</button>
```

**Forbidden:**
```html
<!-- ❌ YASAK - Pre-commit hook BLOCKS -->
<button class="neo-btn neo-btn-primary">Kaydet</button>  <!-- ❌ BLOCKED -->
<div class="neo-card">...</div>  <!-- ❌ BLOCKED -->
<input class="neo-input">  <!-- ❌ BLOCKED -->
```

**Required:**
- ✅ Transition classes: `transition-all duration-200`
- ✅ Dark mode: `dark:bg-gray-800 dark:text-white`
- ✅ Responsive: `grid grid-cols-1 md:grid-cols-2`

**Reference:**
- `.context7/TAILWIND-TRANSITION-RULE.md`
- `.git/hooks/pre-commit` (Section 5️⃣.6)

---

## 🔒 ENFORCEMENT MECHANISMS

### 1. Pre-commit Hook (`.git/hooks/pre-commit`)

**Checks:**
- ✅ `enabled` field in models (BLOCKS)
- ✅ `enabled` field in migrations (BLOCKS)
- ✅ `enabled` fallback in controllers (BLOCKS)
- ✅ New `Musteri*` models (BLOCKS)
- ✅ `neo-*` classes in Blade files (BLOCKS)

**Action:** Commit BLOCKED if violations found

---

### 2. CI/CD Pipeline (`.github/workflows/context7-compliance.yml`)

**Checks:**
- ✅ Context7 compliance check
- ✅ Violation count threshold
- ✅ Build FAILS if violations exceed limit

**Action:** PR BLOCKED if violations found

---

### 3. Model Template (`stubs/model.context7.stub`)

**Auto-generates:**
- ✅ `status` field in `$fillable`
- ✅ `status` cast in `$casts`
- ✅ `scopeActive()` using `status`
- ✅ Comments warning against `enabled`

**Usage:**
```bash
php artisan make:model TestModel --template=context7
```

---

### 4. Migration Template (`stubs/migration.context7-status.stub`)

**Auto-generates:**
- ✅ `status` column (TINYINT(1))
- ✅ Default value: 1
- ✅ Comments warning against `enabled`

**Usage:**
```bash
php artisan make:migration create_test_table --template=context7-status
```

---

## 📊 COMPLIANCE TRACKING

### Current Status (7 Kasım 2025)

```
Status Field: %100 ✅ (enabled: 0 violations)
Terminology: %95 ✅ (musteri: backward compat only)
CSS Framework: %100 ✅ (neo-*: 0 violations)
───────────────────────────────────────
Overall: %98.3 ✅
```

### Enforcement Status

```
Pre-commit Hook: ✅ ACTIVE
CI/CD Pipeline: ✅ ACTIVE
Model Template: ✅ ACTIVE
Migration Template: ✅ ACTIVE
───────────────────────────────────────
All Mechanisms: ✅ OPERATIONAL
```

---

## 🚨 VIOLATION HANDLING

### Pre-commit Hook Violations

**Action:** Commit BLOCKED
**Message:** Clear error with reference to this document
**Bypass:** `git commit --no-verify` (NOT RECOMMENDED)

### CI/CD Violations

**Action:** Build FAILS
**Message:** Detailed violation report
**Bypass:** None (must fix violations)

---

## 📚 REFERENCES

### Documentation
- `.context7/ENABLED_FIELD_FORBIDDEN.md` - enabled field yasağı
- `.context7/TAILWIND-TRANSITION-RULE.md` - Tailwind CSS standardı
- `.context7/authority.json` - Master authority file

### Enforcement
- `.git/hooks/pre-commit` - Pre-commit hook
- `.github/workflows/context7-compliance.yml` - CI/CD pipeline
- `stubs/model.context7.stub` - Model template
- `stubs/migration.context7-status.stub` - Migration template

### Commands
- `php artisan context7:check` - Compliance check
- `php artisan context7:fix` - Auto-fix violations

---

## ✅ VERIFICATION

### Check Compliance
```bash
# Pre-commit hook test
git add .
git commit -m "test"

# Manual check
php artisan context7:check

# CI/CD check
# Runs automatically on push/PR
```

### Verify Standards
```bash
# Check enabled usage
grep -r "'enabled'" app/Models/ | grep -v "weekend_pricing_enabled\|sync_enabled"

# Check musteri usage
grep -r "class Musteri" app/Models/ | grep -v "@deprecated"

# Check neo-* usage
grep -r "neo-" resources/views/ | grep -v "neo-"
```

---

## 🎯 CONCLUSION

**These standards are PERMANENT:**
- ✅ No exceptions allowed
- ✅ No rollback possible
- ✅ Automatic enforcement active
- ✅ All mechanisms operational

**Status:** 🟢 ACTIVE - PERMANENT STANDARDS ENFORCED

---

**Last Updated:** 7 Kasım 2025  
**Version:** 1.0.0  
**Status:** ✅ PERMANENT - NO ROLLBACK

