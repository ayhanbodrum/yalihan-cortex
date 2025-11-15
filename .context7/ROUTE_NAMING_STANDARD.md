# 🛣️ Context7 Route Naming Standard - Kalıcı Standart

**Date:** 7 Kasım 2025  
**Status:** ✅ ACTIVE - PERMANENT  
**Enforcement:** STRICT - NO EXCEPTIONS

---

## ⚠️ CRITICAL: ROUTE NAMING STANDARDI

Bu doküman **route naming standartlarını** tanımlar. Bu standartlar:
- ✅ Pre-commit hook tarafından **otomatik kontrol edilir**
- ✅ CI/CD pipeline'da **otomatik bloklanır**
- ✅ Route validation script'inde **otomatik kontrol edilir**
- ❌ **GERİ DÖNÜŞ YOK** - Bu standartlar kalıcıdır

---

## 📋 ROUTE NAMING RULES

### 1. ✅ Nested Route Groups - Çift Prefix YASAK

**Rule:** Nested route groups'da parent prefix'i tekrar etme!

**Allowed:**
```php
// ✅ DOĞRU - İç group sadece kendi prefix'ini ekler
Route::prefix('admin')->name('admin.')->group(function () {
    Route::prefix('crm')->name('crm.')->group(function () {
        Route::get('/', ...)->name('dashboard');
        // Sonuç: admin.crm.dashboard ✅
    });
});
```

**Forbidden:**
```php
// ❌ YASAK - Çift prefix oluşur
Route::prefix('admin')->name('admin.')->group(function () {
    Route::prefix('crm')->name('admin.crm.')->group(function () {
        Route::get('/', ...)->name('dashboard');
        // Sonuç: admin.admin.crm.dashboard ❌ BLOCKED!
    });
});
```

**Enforcement:**
- ✅ Pre-commit hook: BLOCKS commits with double prefix
- ✅ CI/CD: FAILS builds with double prefix

---

### 2. ✅ View'da Route Kullanımı - Kontrol ZORUNLU

**Rule:** View'larda route kullanırken `Route::has()` kontrolü yap!

**Allowed:**
```blade
{{-- ✅ DOĞRU - Route kontrolü ile --}}
@if (Route::has('admin.crm.dashboard'))
    <a href="{{ route('admin.crm.dashboard') }}">
        CRM Dashboard
    </a>
@endif
```

**Forbidden:**
```blade
{{-- ❌ YANLIŞ - Kontrolsüz, hata oluşabilir --}}
<a href="{{ route('admin.crm.dashboard') }}">
    CRM Dashboard
</a>
```

**Enforcement:**
- ⚠️ Pre-commit hook: WARNING (not blocking)
- ✅ Best practice: Always use `Route::has()`

---

### 3. ✅ Route Name Pattern

**Rule:** Route name'ler kısa, açıklayıcı ve tutarlı olmalı

**Pattern:**
```
{prefix}.{module}.{action}
```

**Examples:**
```php
// ✅ DOĞRU
admin.dashboard          // Ana dashboard
admin.crm.dashboard      // CRM dashboard
admin.kisiler.index      // Kişiler listesi
admin.kisiler.create     // Kişi oluşturma
admin.kisiler.show       // Kişi detayı
admin.talepler.index     // Talepler listesi
```

**Forbidden:**
```php
// ❌ YASAK
admin.admin.crm.dashboard  // Çift prefix
admin.crm.dashboard.index  // Gereksiz .index
admin.crm-dashboard        // Tire kullanma
```

---

## 🔒 ENFORCEMENT MECHANISMS

### 1. Pre-commit Hook

**Checks:**
- ✅ Double prefix pattern: `name('admin.admin.`
- ⚠️ Missing `Route::has()` in views (warning)

**Action:** Commit BLOCKED if double prefix found

---

### 2. CI/CD Pipeline

**Checks:**
- ✅ Double prefix detection
- ✅ Route list validation
- ✅ Missing route check

**Action:** Build FAILS if violations found

---

### 3. Route Validation Script

**File:** `scripts/validate-routes.php` (to be created)

**Checks:**
- Route list validation
- Duplicate route names
- Missing routes in views

---

## 📊 COMMON MISTAKES

### Mistake 1: Double Prefix

```php
// ❌ YANLIŞ
Route::prefix('admin')->name('admin.')->group(function () {
    Route::prefix('crm')->name('admin.crm.')->group(function () {
        // Çift prefix: admin.admin.crm.*
    });
});

// ✅ DOĞRU
Route::prefix('admin')->name('admin.')->group(function () {
    Route::prefix('crm')->name('crm.')->group(function () {
        // Tek prefix: admin.crm.*
    });
});
```

### Mistake 2: Missing Route Check

```blade
{{-- ❌ YANLIŞ - Route yoksa hata verir --}}
<a href="{{ route('admin.crm.dashboard') }}">

{{-- ✅ DOĞRU - Route kontrolü ile --}}
@if (Route::has('admin.crm.dashboard'))
    <a href="{{ route('admin.crm.dashboard') }}">
@endif
```

---

## ✅ VERIFICATION

### Check Route Naming
```bash
# Check for double prefix
grep -r "->name('admin\.admin\." routes/

# Check route list
php artisan route:list --name=admin.crm

# Validate routes
php scripts/validate-routes.php
```

---

## 📚 REFERENCES

- `yalihan-bekci/reports/route-error-root-cause-analysis-2025-11-07.md` - Kök neden analizi
- `.git/hooks/pre-commit` (Section 5️⃣.7) - Pre-commit kontrolü
- `.github/workflows/context7-compliance.yml` - CI/CD kontrolü

---

## 🎯 CONCLUSION

**Route naming standardı kalıcı:**
- ✅ Çift prefix YASAK
- ✅ Route kontrolü ZORUNLU (view'larda)
- ✅ Naming pattern STANDART

**Enforcement:**
- ✅ Pre-commit hook aktif
- ✅ CI/CD validation aktif
- ✅ Best practices dokümante edildi

**Status:** 🟢 PERMANENT STANDARD ENFORCED

---

**Last Updated:** 7 Kasım 2025  
**Version:** 1.0.0  
**Status:** ✅ PERMANENT - NO ROLLBACK

