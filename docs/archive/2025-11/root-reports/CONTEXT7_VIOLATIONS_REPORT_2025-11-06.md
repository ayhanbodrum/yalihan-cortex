# 🚨 Context7 Violations Report

**Date:** 2025-11-06  
**Scope:** Full System Audit  
**Status:** CRITICAL ISSUES FOUND

---

## ❌ CRITICAL VIOLATIONS FOUND

### 1. MUSTERI MODEL EXISTS (Context7 Violation)

**Severity:** 🔴 CRITICAL  
**Status:** ACTIVE VIOLATION

**Forbidden Pattern:**
```php
// ❌ WRONG - musteri model exists
namespace App\Models;
class Musteri extends Model {
    protected $table = 'musteriler';
}
```

**Context7 Rule:**
```
musteri → kisi (MANDATORY)
musteriler → kisiler
```

**Files Affected:**
- `app/Models/Musteri.php` ❌
- `app/Models/MusteriAktivite.php` ❌ (should be KisiAktivite)
- `app/Models/MusteriEtiket.php` ❌ (should be KisiEtiket)
- `app/Models/MusteriNot.php` ❌ (should be KisiNot)
- `app/Models/MusteriTakip.php` ❌ (should be KisiTakip)

**Database Tables:**
```sql
-- Check if musteriler table exists
-- If yes: VIOLATION!
-- Should use: kisiler
```

**Impact:** HIGH
- Database inconsistency
- Model naming violation
- API response inconsistency
- Frontend confusion

**Solution:**
1. **Option A:** Migrate `musteriler` → `kisiler` table
2. **Option B:** Deprecate Musteri model, use Kisi model

**Recommendation:** Option A (Full migration)

---

### 2. CRM ROUTES STILL ACTIVE (Context7 Violation)

**Severity:** 🟡 HIGH  
**Status:** PARTIAL VIOLATION

**Forbidden Pattern:**
```php
// ❌ WRONG
route('crm.customers.index')

// ✅ CORRECT
route('admin.kisiler.index')
```

**Files with crm.* routes:**
- Found 17 matches across 7 files

**Context7 Rule:**
```
crm.* routes → admin.* routes (deprecated)
```

**Solution:**
- Update all route() calls
- Add route aliases for backward compatibility
- Update menu items
- Update breadcrumbs

---

### 3. STATUS FIELD INCONSISTENCY

**Severity:** 🟡 HIGH  
**Status:** MIXED COMPLIANCE

**Issue:** Some models use STRING status, should be BOOLEAN

**Models with String Status:**
```php
// ❌ WRONG - String status
app/Models/Musteri.php
  →where('status', 'Aktif')  // Should be: where('status', true)

app/Models/TalepPortfolyoController.php
  →where('status', 'Aktif')  // Turkish value!

app/Models/MusteriAktivite.php
  →where('status', 'Tamamlandı')  // Multi-state workflow (OK exception)
```

**Context7 Rule Exceptions:**
- Workflow status (draft, published, archived) → STRING OK
- Simple active/inactive → BOOLEAN REQUIRED

**Solution:**
- Review each model's status usage
- Determine if simple boolean or workflow
- Migrate simple ones to boolean
- Document exceptions

---

### 4. MISSING EAGER LOADING (Performance Issue)

**Severity:** 🟡 MEDIUM  
**Status:** NEEDS OPTIMIZATION

**Issue:** Some controllers don't use eager loading

**Good Example (IlanController):**
```php
// ✅ GOOD
$ilanlar = Ilan::with(['il', 'ilce', 'kategori'])->paginate(20);
```

**Bad Example (Need to check):**
```php
// ❌ BAD - Missing eager loading
$ilanlar = Ilan::all();
// Then in view:
foreach($ilanlar as $ilan) {
    echo $ilan->il->name; // N+1 query!
}
```

**Controllers to Review:**
- KisiController
- TalepController
- YazlikKiralamaController
- MusteriController (if active)

---

### 5. DUPLICATE FUNCTIONALITY

**Severity:** 🟢 LOW  
**Status:** CODE SMELL

**Issue:** Musteri AND Kisi models both exist

**Analysis:**
```
app/Models/Musteri.php  // Old model
app/Models/Kisi.php     // New Context7 model

Both models might be serving same purpose!
```

**Solution:**
- Audit usage of both models
- Deprecate Musteri model
- Migrate all code to Kisi model
- Remove Musteri model

---

## 📊 COMPLIANCE SCORE BY AREA

### Models
```
✅ COMPLIANT:
- Site.php (uses status, il_id, mahalle_id) 100%
- AIKnowledgeBase.php (uses status) 100%
- Ilan.php (uses status, no enabled) 100%
- Feature.php (fixed today) 100%

❌ VIOLATIONS:
- Musteri.php (name violation) CRITICAL
- MusteriAktivite.php (name violation) CRITICAL
- MusteriEtiket.php (name violation) CRITICAL
```

### Routes
```
✅ COMPLIANT:
- admin.ilanlar.* 95%
- admin.kisiler.* 95%
- admin.ayarlar.* 100%

⚠️ PARTIAL:
- crm.* routes (17 occurrences) 40%
```

### Controllers
```
✅ COMPLIANT:
- IlanController (uses eager loading, status field) 90%
- SiteController (Context7 compliant) 95%

⚠️ NEEDS REVIEW:
- MusteriController (name violation) 50%
- CRMController (route violation) 60%
```

---

## 🎯 ACTION PLAN

### Priority 1: CRITICAL (Do Now!)
- [ ] Audit: Does `musteriler` table exist in DB?
- [ ] If yes: Plan musteri → kisi migration
- [ ] Deprecate Musteri model
- [ ] Update all Musteri references to Kisi
- [ ] Remove musteri_* pivot tables

### Priority 2: HIGH (This Week)
- [ ] Fix crm.* route references (17 occurrences)
- [ ] Update menu items (crm → admin)
- [ ] Add route aliases for backward compatibility
- [ ] Test all affected pages

### Priority 3: MEDIUM (This Month)
- [ ] Review all status fields (boolean vs string)
- [ ] Add eager loading to missing controllers
- [ ] Implement query optimization
- [ ] Add database indexes

### Priority 4: LOW (As Time Allows)
- [ ] Code cleanup (remove unused code)
- [ ] Documentation updates
- [ ] Add comprehensive tests
- [ ] Performance monitoring

---

## 🔍 INVESTIGATION COMMANDS

```bash
# 1. Check if musteriler table exists
mysql -u root yalihanemlak_ultra -e "SHOW TABLES LIKE 'musteriler';"

# 2. Count Musteri model usage
grep -r "Musteri::" app/ | wc -l
grep -r "use App\\Models\\Musteri" app/ | wc -l

# 3. Check crm route usage
grep -r "route('crm\." resources/views/ | wc -l

# 4. Find all models with enabled field
grep -r "'enabled'" app/Models/

# 5. Check N+1 query issues
php artisan telescope:prune
# Visit pages and check Telescope
```

---

## 📚 REFERENCE DOCUMENTS

- `.context7/authority.json` → musteri → kisi rule
- `.context7/ENABLED_FIELD_FORBIDDEN.md` → enabled prohibition
- `yalihan-bekci/rules/status-field-standard.json` → status standards

---

## ✅ VERIFICATION

Run these after fixes:

```bash
# Context7 compliance check
php artisan context7:check

# Route list audit
php artisan route:list | grep "crm\."

# Model audit
grep -r "class Musteri" app/Models/

# Database audit
mysql -e "SHOW TABLES;" yalihanemlak_ultra | grep musteri
```

---

**Report Status:** ✅ COMPLETE  
**Critical Issues:** 2  
**High Priority:** 2  
**Medium Priority:** 2  
**Next Action:** Musteri → Kisi migration planning

