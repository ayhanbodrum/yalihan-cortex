# Duplicate Routes - CSV Export

## Full Details CSV

```csv
RouteNumber,DuplicateName,Count,Severity,Files,Line1,Line2,URL1,URL2,Controller1,Controller2,HTTPMethod,BlockingIssue
1,admin.validate.field,2,HIGH,routes/admin.php | routes/web/admin/validation.php,66,18,POST /admin/validate/field,POST /admin/validate-field,FormValidationController@validateField,ValidationController@validateField,POST,YES
2,admin.property-features.validate,2,CRITICAL,routes/api-admin.php (same file),99,466,POST /admin/property-features/validate,POST /admin/property-features/validate,PropertyFeatureSuggestionController@validateFeatures,PropertyFeatureSuggestionController@validateFeatures,POST,YES
3,api.reference.validate,2,HIGH,routes/api.php | routes/api/v1/common.php,921,130,GET /api/reference/validate/{id},GET /api/v1/reference/validate/{id},ReferenceController@validateRef,ReferenceController@validateRef,GET,YES
4,api.reference.generate,2,HIGH,routes/api.php | routes/api/v1/common.php,918,128,POST /api/reference/generate,POST /api/v1/reference/generate,ReferenceController@generateRef,ReferenceController@generateRef,POST,YES
5,api.reference.basename,2,HIGH,routes/api.php | routes/api/v1/common.php,924,131,POST /api/reference/basename,POST /api/v1/reference/basename,ReferenceController@generateBasename,ReferenceController@generateBasename,POST,YES
6,api.reference.portal,2,HIGH,routes/api.php | routes/api/v1/common.php,927,132,POST /api/reference/portal,POST /api/v1/reference/portal,ReferenceController@updatePortalNumber,ReferenceController@updatePortalNumber,POST,YES
7,api.reference.info,2,HIGH,routes/api.php | routes/api/v1/common.php,930,133,GET /api/reference/{id},GET /api/v1/reference/{id},ReferenceController@getReferenceInfo,ReferenceController@getReferenceInfo,GET,YES
```

## Summary Statistics

```json
{
  "analysis_date": "2025-12-02",
  "total_duplicates": 7,
  "total_instances": 14,
  "blocking_issues": 7,
  "critical_severity": 1,
  "high_severity": 6,
  "by_file": {
    "routes/admin.php": 1,
    "routes/api-admin.php": 1,
    "routes/api.php": 5,
    "routes/web/admin/validation.php": 1,
    "routes/api/v1/common.php": 5
  },
  "by_severity": {
    "CRITICAL": 1,
    "HIGH": 6
  },
  "pattern_breakdown": {
    "same_file_duplicates": 1,
    "cross_file_duplicates": 6,
    "api_version_conflicts": 5,
    "validation_conflicts": 1,
    "feature_conflicts": 1
  }
}
```

## Routes by File

### routes/admin.php
- `admin.validate.field` (Line 66) - DUPLICATE

### routes/api-admin.php
- `admin.property-features.validate` (Lines 99 & 466) - DUPLICATE (CRITICAL)

### routes/api.php
- `api.reference.validate` (Line 921) - DUPLICATE
- `api.reference.generate` (Line 918) - DUPLICATE
- `api.reference.basename` (Line 924) - DUPLICATE
- `api.reference.portal` (Line 927) - DUPLICATE
- `api.reference.info` (Line 930) - DUPLICATE

### routes/web/admin/validation.php
- `admin.validate.field` (Line 18) - DUPLICATE

### routes/api/v1/common.php
- `api.reference.validate` (Line 130) - DUPLICATE
- `api.reference.generate` (Line 128) - DUPLICATE
- `api.reference.basename` (Line 131) - DUPLICATE
- `api.reference.portal` (Line 132) - DUPLICATE
- `api.reference.info` (Line 133) - DUPLICATE

## Quick Fixes

```php
// FIX #1: routes/api-admin.php - DELETE lines 463-469
// (Exact duplicate of lines 95-100)

// FIX #2: routes/web/admin/validation.php - RENAME route
// FROM:
->name('validate.field');
// TO:
->name('validation-widget.field');
// Results in: admin.validation-widget.field

// FIX #3: routes/api/v1/common.php - ADD VERSION PREFIX
// FROM:
Route::prefix('reference')->name('api.reference.')->group(function () {
// TO:
Route::prefix('reference')->name('api.v1.reference.')->group(function () {
```

## Verification Commands

```bash
# Test route caching (will fail with current duplicates)
php artisan route:cache

# Show affected routes
grep -n "admin.validate.field" routes/**/*.php
grep -n "admin.property-features.validate" routes/**/*.php
grep -n "api.reference" routes/api.php routes/api/v1/common.php

# List all routes after fixing
php artisan route:list | grep -E "(validate|reference|property-features)"
```

---

Generated: 2025-12-02
