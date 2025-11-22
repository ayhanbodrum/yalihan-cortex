# Context7 CRITICAL Rule: enabled Field FORBIDDEN

**Date:** 2025-11-06  
**Status:** ACTIVE - MANDATORY  
**Severity:** CRITICAL  
**Auto-fix:** Available

---

## 🚫 FORBIDDEN PATTERN

### Field Naming
```php
// ❌ NEVER USE
'enabled'
'is_active' 
'aktif'
'durum'
'active' (as field name)

// ✅ ALWAYS USE
'status'
```

---

## 📋 RULE DETAILS

### Database Schema
```sql
-- ❌ WRONG
CREATE TABLE example (
    enabled TINYINT(1) DEFAULT 1
);

-- ✅ CORRECT
CREATE TABLE example (
    status TINYINT(1) DEFAULT 1
);
```

### Laravel Models
```php
// ❌ WRONG
protected $fillable = [
    'name',
    'enabled',
];

protected $casts = [
    'enabled' => 'boolean',
];

// ✅ CORRECT
protected $fillable = [
    'name',
    'status',
];

protected $casts = [
    'status' => 'boolean',
];
```

### Queries
```php
// ❌ WRONG
Model::where('enabled', true)->get();
Model::where('is_active', 1)->get();

// ✅ CORRECT
Model::where('status', true)->get();
Model::where('status', 1)->get();
```

---

## 🎯 WHY THIS RULE EXISTS

### 1. **Semantic Clarity**
- `status` supports workflows (active, pending, suspended, archived)
- `enabled` only supports true/false (limited)

### 2. **Industry Standards**
- HTTP Status Codes
- REST API Conventions
- Database Normalization

### 3. **Scalability**
- Easy to extend from boolean to enum/varchar
- No breaking changes when adding new states

### 4. **API Consistency**
```json
{
    "status": "active",  // ✅ Standard
    "enabled": true      // ❌ Non-standard
}
```

---

## 📊 STATISTICS

**Before Cleanup (2025-11-05):**
- `enabled` usage: 647 matches in 180 files
- `status` usage: 5,229 matches in 954 files
- Ratio: 8:1 (status winning)

**After Cleanup (2025-11-06):**
- `enabled` in models: 0 ✅
- `enabled` in DB: 0 ✅
- Context7 Compliance: 100% ✅

---

## 🔧 ENFORCEMENT

### 1. Authority File
Location: `.context7/authority.json`

```json
"database_fields": {
    "status": {
        "standard": "status",
        "forbidden": ["enabled", "is_active", "aktif", "durum"],
        "severity": "critical",
        "auto_fix": true
    }
}
```

### 2. Yalıhan Bekçi Rule
Location: `yalihan-bekci/rules/status-field-standard.json`

```json
"field_naming_standard": {
    "required": "status",
    "forbidden": ["enabled", "is_active", "aktif", "durum"],
    "severity": "CRITICAL"
}
```

### 3. Pre-commit Hook
```bash
# Check for enabled usage
if grep -r "'enabled'" app/Models/; then
    echo "❌ ERROR: 'enabled' field forbidden! Use 'status' instead."
    exit 1
fi
```

---

## 🛠️ MIGRATION GUIDE

### Step 1: Database Migration
```php
Schema::table('example_table', function (Blueprint $table) {
    $table->renameColumn('enabled', 'status');
});
```

### Step 2: Model Update
```php
// Find: 'enabled'
// Replace: 'status'

// Find: 'enabled' => 'boolean'
// Replace: 'status' => 'boolean'

// Find: where('enabled',
// Replace: where('status',
```

### Step 3: Verify
```bash
# Check remaining usage
grep -r "enabled" app/Models/
# Should return: 0 matches
```

---

## ✅ COMPLIANCE CHECKLIST

- [ ] No `enabled` in database schema
- [ ] No `enabled` in model $fillable
- [ ] No `enabled` in model $casts
- [ ] No `enabled` in queries
- [ ] No `enabled` in API responses
- [ ] All models use `status` field
- [ ] Pre-commit hook active

---

## 📚 REFERENCES

- Authority: `.context7/authority.json` (line 340-347)
- Rule: `yalihan-bekci/rules/status-field-standard.json`
- Migration: `database/migrations/2025_11_06_000001_context7_rename_enabled_to_status.php`

---

## 🚨 VIOLATIONS

**Report violations:**
```bash
php artisan context7:check enabled
```

**Auto-fix:**
```bash
php artisan context7:fix enabled
```

---

**Last Updated:** 2025-11-06  
**Compliance Level:** 100%  
**Status:** ✅ ENFORCED

