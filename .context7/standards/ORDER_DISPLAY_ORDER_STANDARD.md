# Context7 Standard: order → display_order

**Date:** 2025-11-09  
**Status:** ✅ ACTIVE - PERMANENT  
**Severity:** CRITICAL  
**Enforcement:** STRICT

---

## 🚫 FORBIDDEN PATTERN

### Database Column Naming
```php
// ❌ NEVER USE
'order'
$table->integer('order')->default(0);
->orderBy('order')
'order' => $value

// ✅ ALWAYS USE
'display_order'
$table->integer('display_order')->default(0);
->orderBy('display_order')
'display_order' => $value
```

---

## 📋 RULE DETAILS

### Database Schema
```sql
-- ❌ WRONG
CREATE TABLE example (
    order INT DEFAULT 0
);

-- ✅ CORRECT
CREATE TABLE example (
    display_order INT DEFAULT 0
);
```

### Laravel Migrations
```php
// ❌ WRONG
Schema::create('example', function (Blueprint $table) {
    $table->integer('order')->default(0);
});

// ✅ CORRECT
Schema::create('example', function (Blueprint $table) {
    $table->integer('display_order')->default(0);
});
```

### Laravel Models
```php
// ❌ WRONG
protected $fillable = ['name', 'order'];
protected $casts = ['order' => 'integer'];
public function scopeOrdered($query) {
    return $query->orderBy('order');
}

// ✅ CORRECT
protected $fillable = ['name', 'display_order'];
protected $casts = ['display_order' => 'integer'];
public function scopeOrdered($query) {
    return $query->orderBy('display_order');
}
```

### Controllers & Queries
```php
// ❌ WRONG
Model::orderBy('order')->get();
$data = ['order' => 1];

// ✅ CORRECT
Model::orderBy('display_order')->get();
$data = ['display_order' => 1];
```

---

## 🎯 WHY THIS RULE EXISTS

### 1. **Semantic Clarity**
- `display_order` clearly indicates sorting/display purpose
- `order` is ambiguous (could be SQL ORDER BY, business order, etc.)

### 2. **Industry Standards**
- Laravel conventions prefer descriptive field names
- Avoids conflicts with SQL reserved keywords

### 3. **Consistency**
- All sorting fields use `display_order` consistently
- Easier to search and maintain

---

## ✅ BACKWARD COMPATIBILITY

Backward compatibility için accessor/mutator kullanılabilir:

```php
// Model'de backward compatibility
public function getOrderAttribute() {
    return $this->display_order;
}

public function setOrderAttribute($value) {
    $this->attributes['display_order'] = $value;
}
```

Bu sayede eski kodlar (`$model->order`) çalışmaya devam eder.

---

## 📊 AFFECTED TABLES

Migration ile düzeltilen tablolar:
- ✅ `ilan_kategorileri` → `display_order`
- ✅ `ilan_kategori_yayin_tipleri` → `display_order`
- ✅ `ozellik_kategorileri` → `display_order`

Zaten `display_order` kullanan tablolar:
- ✅ `features` → `display_order`
- ✅ `feature_categories` → `display_order`
- ✅ `ilan_etiketler` → `display_order`

---

## 🔧 ENFORCEMENT

### 1. Authority File
Location: `.context7/authority.json`

```json
"database_fields": {
    "order": {
        "replacement": "display_order",
        "severity": "critical",
        "enforcement": "STRICT"
    }
}
```

### 2. Pre-commit Hook
- ✅ BLOCKS commits with `order` column
- ✅ Checks migration files
- ✅ Checks model files

### 3. CI/CD
- ✅ FAILS builds with `order` column
- ✅ Validates all migrations

### 4. Model Template
- ✅ Auto-generates `display_order` only
- ✅ Never generates `order`

### 5. Migration Template
- ✅ Auto-generates `display_order` only
- ✅ Never generates `order`

---

## 📚 REFERENCES

- `.context7/authority.json` (master authority file)
- `.context7/MIGRATION_COMPLIANCE_REPORT.md`
- `.context7/MIGRATION_ORDER_VIOLATIONS.md`
- `.context7/ORDER_USAGE_ANALYSIS.md`
- `yalihan-bekci/knowledge/order-display-order-standard-2025-11-09.json`

---

## 📊 STATISTICS

**Migration Applied (2025-11-09):**
- Migration: `2025_11_09_070721_rename_order_to_display_order_in_tables.php`
- Tables updated: 3
- Models updated: 3
- Controllers updated: 12
- Services updated: 2
- Backward compatibility: ✅ Accessor/Mutator added

**Remaining Violations:**
- Migration files: 19 files still use `order` (low priority - already run)
- Code usage: 0 critical violations ✅

---

**Last Updated:** 2025-11-09  
**Status:** ✅ ACTIVE - ENFORCED

