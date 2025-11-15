# 📚 Context7 Master Guide - Kapsamlı Referans

**Version:** 2.0 (Consolidated)  
**Tarih:** 24 Ekim 2025  
**Durum:** ✅ %98.82 Compliance

---

## 📋 İÇİNDEKİLER

1. [Context7 Nedir?](#context7-nedir)
2. [Core Rules (Temel Kurallar)](#core-rules)
3. [Database Standards](#database-standards)
4. [Neo Design System](#neo-design-system)
5. [AI Standards](#ai-standards)
6. [Form Design Standards](#form-design-standards)
7. [Status Field Crisis & Solution](#status-field-crisis)
8. [Migration Guide](#migration-guide)
9. [Best Practices](#best-practices)
10. [Quick Reference](#quick-reference)

---

## 🎯 CONTEXT7 NEDİR?

Context7, **Yalıhan Emlak Warp Sistemi**'nde %100 uyulması zorunlu **kod ve veri standardı**dır.

### **Ana Prensipler:**

1. **İngilizce Field Adları** (Database)
2. **Neo Design System** (CSS/UI)
3. **Vanilla JavaScript** (Heavy library yasak)
4. **Context7 Toast Sistemi** (subtleVibrantToast yasak)
5. **Price Display** (Para birimi ile birlikte)
6. **Accessibility First** (Label, aria, semantic HTML)

### **Compliance Target:**

```
Hedef: %100
Mevcut: %98.82
Kalan İhlal: 7 adet
```

---

## ⚠️ CORE RULES (TEMEL KURALLAR)

### **1. FORBIDDEN PATTERNS (YASAK KALIPLAR)**

#### **Database Fields:**

```yaml
❌ YASAK:
    - durum → ✅ status
    - aktif → ✅ active / enabled
    - sehir → ✅ il
    - sehir_id → ✅ il_id
    - is_active → ✅ status (boolean)

❌ YASAK (Turkish column names):
    - musteri → ✅ kisi
    - subtleVibrantToast → ✅ Context7 Toast
    - layouts.app → ✅ Context7 Layout
```

#### **CSS Classes:**

```yaml
❌ YASAK (Bootstrap):
    - btn-primary → ✅ neo-btn neo-btn--primary
    - card-body → ✅ neo-card__body
    - form-control → ✅ neo-input

✅ ZORUNLU (Neo Design):
    - neo-* prefix (ZORUNLU)
    - BEM naming (block__element--modifier)
```

#### **JavaScript:**

```yaml
❌ YASAK:
    - jQuery (Heavy library)
    - React-Select (Heavy library)
    - subtleVibrantToast()

✅ ZORUNLU:
    - Vanilla JS ONLY
    - Alpine.js (lightweight, OK)
    - Context7 Toast
```

---

## 🗄️ DATABASE STANDARDS

### **Field Naming Convention:**

```sql
-- ✅ DOĞRU
CREATE TABLE ilanlar (
    id BIGINT,
    status TINYINT(1) DEFAULT 1 COMMENT '0=inactive, 1=active',
    il_id BIGINT,
    para_birimi ENUM('TRY','USD','EUR','GBP'),
    fiyat DECIMAL(15,2)
);

-- ❌ YANLIŞ
CREATE TABLE ilanlar (
    id BIGINT,
    durum VARCHAR(50) DEFAULT 'Aktif',  -- ❌ Turkish
    sehir_id BIGINT,  -- ❌ Should be il_id
    currency VARCHAR(10),  -- ❌ Should be para_birimi
    price DECIMAL(15,2)  -- ❌ Should be fiyat
);
```

### **Status Field Standard:**

```yaml
Type: TINYINT(1) or BOOLEAN
Values: 0 (inactive), 1 (active)
Default: 1
Comment: '0=inactive, 1=active'

❌ ASLA:
    - VARCHAR('Aktif', 'Pasif')
    - ENUM('Aktif', 'Pasif')
    - is_active (field adı)
```

### **Model Casting:**

```php
<?php

namespace App\Models;

class Ilan extends Model
{
    protected $casts = [
        'status' => 'boolean',  // ✅ ZORUNLU
        'fiyat' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    // ✅ Accessor for display
    public function getStatusTextAttribute(): string
    {
        return $this->status ? 'Aktif' : 'Pasif';
    }
}
```

---

## 🎨 NEO DESIGN SYSTEM

### **Component Prefix:**

```css
/* ✅ DOĞRU */
.neo-btn {
}
.neo-card {
}
.neo-input {
}
.neo-form-group {
}

/* ❌ YANLIŞ */
.btn {
}
.card {
}
.form-control {
}
```

### **Color Palette:**

```css
:root {
    --neo-primary: #f97316; /* Orange */
    --neo-success: #10b981; /* Green */
    --neo-warning: #f59e0b; /* Yellow */
    --neo-danger: #ef4444; /* Red */
    --neo-info: #06b6d4; /* Cyan */
}
```

### **Dark Mode:**

```css
/* ✅ ZORUNLU: dark: prefix */
.neo-card {
    @apply bg-white dark:bg-gray-800;
    @apply text-gray-900 dark:text-gray-100;
}
```

### **Button Component:**

```blade
{{-- ✅ DOĞRU --}}
<button type="button" class="neo-btn neo-btn--primary neo-btn--md">
    Kaydet
</button>

{{-- ❌ YANLIŞ --}}
<button type="button" class="btn btn-primary">
    Kaydet
</button>
```

---

## 🤖 AI STANDARDS

### **AI Service Standards:**

```php
<?php

namespace App\Services;

class AIService
{
    /**
     * Context7-compliant AI service
     *
     * Providers:
     * - Ollama (default, local)
     * - OpenAI (GPT-4)
     * - Gemini (Google)
     * - Claude (Anthropic)
     * - DeepSeek
     */
    public function analyze($data, $context): array
    {
        // Context7 response format
        return [
            'success' => true,
            'data' => $result,
            'metadata' => [
                'model' => 'gemma2:2b',
                'provider' => 'ollama',
                'response_time' => 2150,
            ],
            'context7_compliant' => true,
        ];
    }
}
```

### **AI Widget Usage:**

```blade
{{-- ✅ Context7 AI Widget --}}
<x-admin.ai-widget
    :action="'analyze'"
    :endpoint="'/api/admin/ai/analyze'"
    :title="'AI Analiz'"
    :data="$data"
    :context="$context" />
```

### **AI API Endpoints:**

```yaml
POST /api/admin/ai/analyze
POST /api/admin/ai/suggest
POST /api/admin/ai/generate
GET /api/admin/ai/health
GET /api/admin/ai/stats
```

---

## 📝 FORM DESIGN STANDARDS

### **Form Structure:**

```blade
{{-- ✅ Context7 Form Structure --}}
<form id="context7-form" class="neo-form" x-data="formHandler()">
    <div class="neo-form-grid">

        {{-- Form Group --}}
        <div class="neo-form-group">
            <label for="field" class="neo-label neo-label--required">
                Field Adı
            </label>
            <input
                type="text"
                id="field"
                name="field"
                class="neo-input"
                x-model="form.field"
                required
            />
            <span class="neo-help-text">Yardım metni</span>
        </div>

        {{-- Loading State --}}
        <div x-show="loading" class="neo-loading">
            <div class="neo-spinner"></div>
            <p>Yükleniyor...</p>
        </div>

    </div>

    {{-- Form Actions --}}
    <div class="neo-form-actions">
        <button type="submit" class="neo-btn neo-btn--primary">
            Kaydet
        </button>
        <button type="button" class="neo-btn neo-btn--secondary">
            İptal
        </button>
    </div>
</form>
```

### **Required Elements:**

```yaml
✅ ZORUNLU:
    - Label (her input için)
    - Loading state (submit için)
    - Error handling
    - Success feedback (toast)
    - Accessibility (aria-*, role)
    - Responsive (mobile-first)
```

---

## 🚨 STATUS FIELD CRISIS & SOLUTION

### **Problem:**

```sql
-- ❌ 22 tablo bu şekilde (YANLIŞ)
status VARCHAR(255) DEFAULT 'Aktif'
status ENUM('Aktif', 'Pasif')
```

### **Solution:**

```sql
-- ✅ DOĞRU
status TINYINT(1) NOT NULL DEFAULT 1 COMMENT '0=inactive, 1=active'
```

### **Migration:**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Convert to VARCHAR
        DB::statement('ALTER TABLE table_name MODIFY COLUMN status VARCHAR(50) NULL');

        // Step 2: Normalize data
        DB::statement("UPDATE table_name SET status = '1' WHERE status IN ('Aktif', 'aktif', 'active', '1', 1)");
        DB::statement("UPDATE table_name SET status = '0' WHERE status IN ('Pasif', 'pasif', 'inactive', '0', 0)");

        // Step 3: Convert to TINYINT(1)
        DB::statement('ALTER TABLE table_name MODIFY COLUMN status TINYINT(1) NOT NULL DEFAULT 1 COMMENT "0=inactive, 1=active"');
    }
};
```

### **Progress:**

```
Phase 1: ✅ 5 critical tables (ilanlar, kisiler, projeler, ozellikler, talepler)
Phase 2: 🔄 6 medium tables (blog, location tables)
Phase 3: ⏳ 5 low tables (rest)

Current Compliance: 29.2% → Target: 75%
```

---

## 📖 MIGRATION GUIDE

### **Step-by-Step:**

```bash
# 1. Backup
mysqldump yalihanemlak_ultra > backup_before_context7.sql

# 2. Run migration
php artisan migrate --path=database/migrations/fixes/

# 3. Update models
# Add: protected $casts = ['status' => 'boolean'];

# 4. Update controllers
# Change: where('status', 'Aktif') → where('status', true)

# 5. Test
php artisan test

# 6. Deploy
php artisan migrate --force
```

---

## ✅ BEST PRACTICES

### **1. Database:**

```php
// ✅ DOĞRU
Ilan::where('status', true)
    ->where('il_id', $ilId)
    ->get();

// ❌ YANLIŞ
Ilan::where('durum', 'Aktif')
    ->where('sehir_id', $sehirId)
    ->get();
```

### **2. CSS:**

```css
/* ✅ DOĞRU */
.neo-btn {
    @apply px-4 py-2 rounded;
}

/* ❌ YANLIŞ */
.btn {
    padding: 0.5rem 1rem;
}
```

### **3. JavaScript:**

```javascript
// ✅ DOĞRU (Vanilla JS)
document.getElementById('btn').addEventListener('click', () => {
    window.Context7Toast.success('Başarılı!');
});

// ❌ YANLIŞ (jQuery)
$('#btn').click(function () {
    subtleVibrantToast('success', 'Başarılı!');
});
```

### **4. Blade:**

```blade
{{-- ✅ DOĞRU --}}
<x-admin.neo-card title="Başlık">
    İçerik
</x-admin.neo-card>

{{-- ❌ YANLIŞ --}}
<div class="card">
    <div class="card-body">
        İçerik
    </div>
</div>
```

---

## 🔍 QUICK REFERENCE

### **Cheat Sheet:**

```yaml
# Database
status: TINYINT(1) ✅
durum: ❌
il_id: ✅
sehir_id: ❌

# CSS
neo-btn: ✅
btn-primary: ❌

# JavaScript
Vanilla JS: ✅
jQuery: ❌
Alpine.js: ✅ (lightweight)

# Toast
window.Context7Toast.success(): ✅
subtleVibrantToast(): ❌

# Price
para_birimi + fiyat: ✅
currency + price: ❌
```

### **Command Reference:**

```bash
# Context7 Check
php artisan context7:check

# Compliance Report
php context7_final_compliance_checker.php

# Pre-commit Hook
git commit  # Otomatik kontrol

# Migration
php artisan migrate --path=database/migrations/fixes/
```

---

## 📚 RESOURCES

### **Files:**

- `.context7/authority.json` - Context7 kuralları
- `CONTEXT7_ULTIMATE_STATUS_REPORT.md` - Durum raporu
- `docs/context7/STATUS_FIELD_CRISIS_ANALYSIS.md` - Status field analizi
- `yalihan-bekci/rules/status-field-standard.json` - Status field kuralları

### **Scripts:**

- `scripts/yalihan-temizlik` - Kod temizlik aracı
- `scripts/bekci-watch.sh` - Real-time monitoring
- `scripts/context7-optimization.php` - Optimization tool

---

## 🎯 COMPLIANCE PROGRESS

```
Current: 98.82%
Target: 100%

Completed:
✅ Database field naming (95%)
✅ Neo Design System (100%)
✅ Toast system (100%)
✅ Status field Phase 1 (29.2%)

In Progress:
🔄 Status field Phase 2/3 (70.8%)
🔄 Label accessibility (7 violations)

Remaining:
⏳ 7 violations total
```

---

## 🚀 NEXT STEPS

1. **Complete Status Field Migration** (Phase 2/3)
2. **Fix Remaining 7 Violations**
3. **100% Compliance Achievement**
4. **Context7 v2.0 Launch**

---

**📚 Bu dosya, tüm Context7 dokümantasyonunu tek bir referans olarak birleştirir.**

**Kaynak Dosyalar:** (48 dosya konsolide edildi)

- context7-rules.md (105 KB)
- AI-MASTER-REFERENCE-2025-10-12.md (23 KB)
- NEO-DESIGN-SYSTEM-MASTER-REFERENCE.md (18 KB)
- STATUS_FIELD_CRISIS_ANALYSIS.md (16 KB)
-   - 44 diğer dosya

**Version:** 2.0 (Consolidated)  
**Last Update:** 24 Ekim 2025
