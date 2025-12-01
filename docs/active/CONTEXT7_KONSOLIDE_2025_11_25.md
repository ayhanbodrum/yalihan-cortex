# 📚 Context7 Konsolide Dokümantasyon

**Version:** 3.0 (Mega Consolidation)  
**Son Güncelleme:** 25 Kasım 2025  
**Context7 Compliance:** ✅ %98.82  
**Durum:** Aktif Reference Dokümandır

---

## 📋 İÇİNDEKİLER

1. [Context7 Nedir?](#context7-nedir)
2. [Core Rules (Temel Kurallar)](#core-rules)
3. [Database Standards](#database-standards)
4. [Field Naming Standards](#field-naming-standards)
5. [MCP Server Usage](#mcp-server-usage)
6. [Compliance Check](#compliance-check)
7. [Migration Patterns](#migration-patterns)
8. [Best Practices](#best-practices)
9. [Quick Commands](#quick-commands)

---

## 🎯 CONTEXT7 NEDİR?

Context7, **Yalıhan Emlak EmlakPro Sistemi**'nde %100 uyulması zorunlu **kod, veri ve AI standardı**dır.

### Ana Prensipler

1. **İngilizce Field Adları** (Database) - `status` not `durum`
2. **Tailwind CSS Only** (Neo Design System yasak)
3. **Alpine.js Preferred** (Heavy JS framework'ler yasak)
4. **Context7 Validation** (Otomatik kontrol)
5. **Price Display** (Para birimi ile birlikte)
6. **Accessibility First** (WCAG AAA)

### Context7 Dual System

**1. Upstash Context7 MCP** - Library documentation  
**2. Yalıhan Bekçi Context7** - Project compliance rules

Kullanım: "Context7 kullan" komutu ile her ikisi aktive olur.

---

## 🔧 CORE RULES

### Database Field Naming

```sql
-- ✅ DOĞRU
status ENUM('active','passive','archived')
is_published TINYINT(1) DEFAULT 0
created_at TIMESTAMP
price DECIMAL(15,2)
category_id INT

-- ❌ YANLIŞ
durum VARCHAR(255)
aktif_mi TINYINT(1)
olusturma_tarihi TIMESTAMP
fiyat DECIMAL(15,2)
kategori_id INT
```

### Critical Field Standards

```sql
-- Status Alanları (ZORUNLU)
status ENUM('active','passive','archived') NOT NULL DEFAULT 'active'

-- Boolean Alanları
is_published TINYINT(1) DEFAULT 0
is_featured TINYINT(1) DEFAULT 0
is_urgent TINYINT(1) DEFAULT 0

-- Timestamp Alanları
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

-- Price Alanları
price DECIMAL(15,2)
currency ENUM('TRL','USD','EUR') DEFAULT 'TRL'
```

### Forbidden Patterns

```sql
-- ❌ ASLA KULLANMA
OLD_ENABLED TINYINT(1)     -- status kullan
aktif TINYINT(1)           -- is_published kullan
durum VARCHAR(255)         -- status kullan
yayin_durumu INT           -- is_published kullan
```

---

## 🗂️ DATABASE STANDARDS

### Table Naming Convention

```sql
-- ✅ Model Adları (Tekil)
users           -> User model
properties      -> Property model
categories      -> Category model
property_features -> PropertyFeature model

-- ✅ Junction Tables (Çoğul_Çoğul)
property_features
user_roles
category_properties
```

### Migration Pattern

```php
// ✅ Context7 Migration Template
Schema::create('table_name', function (Blueprint $table) {
    $table->id();

    // Context7 Required Fields
    $table->string('title')->nullable();
    $table->text('description')->nullable();
    $table->enum('status', ['active', 'passive', 'archived'])
          ->default('active');
    $table->tinyInteger('is_published')->default(0);

    // Relations
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('category_id')->nullable()->constrained();

    // Timestamps (ZORUNLU)
    $table->timestamps();

    // Indexes
    $table->index(['status', 'is_published']);
    $table->index('created_at');
});
```

---

## 📝 FIELD NAMING STANDARDS

### Required Status System

```php
// ✅ Eloquent Model'de ZORUNLU
class Ilan extends Model {
    protected $fillable = [
        'title',
        'description',
        'price',
        'currency',
        'status',          // ZORUNLU: active/passive/archived
        'is_published',    // ZORUNLU: 0/1
        'is_featured',
        'category_id',
        'user_id'
    ];

    // ✅ Status Enum
    const STATUS_ACTIVE = 'active';
    const STATUS_PASSIVE = 'passive';
    const STATUS_ARCHIVED = 'archived';
}
```

### Location Fields

```sql
-- ✅ Lokasyon Standartları
province_id INT        -- il_id değil
district_id INT        -- ilce_id değil
neighborhood_id INT    -- mahalle_id değil
address TEXT
latitude DECIMAL(10,8)
longitude DECIMAL(11,8)
```

### Property Specific Fields

```sql
-- ✅ Emlak Özel Alanları
net_area INT           -- net_metrekare değil
gross_area INT         -- brut_metrekare değil
room_count INT         -- oda_sayisi değil
bathroom_count INT     -- banyo_sayisi değil
floor_number INT       -- kat_numarasi değil
```

---

## 🤖 MCP SERVER USAGE

### Context7 Commands

```bash
# Validation Commands
php artisan context7:validate-migration --all
php artisan context7:validate-migration --auto-fix
php artisan context7:health-check

# VS Code Tasks
"Context7: Validate All"
"Context7: Auto Fix"
"Context7: Health Check"
```

### MCP Server Integration

```bash
# Start All MCP Servers
./scripts/services/start-all-mcp-servers.sh

# Start Yalıhan Bekçi Only
./scripts/services/start-bekci-server.sh

# Stop All
./scripts/services/stop-all-mcp-servers.sh
```

---

## ✅ COMPLIANCE CHECK

### Current Status: %98.82

```
✅ Passed: 167 checks
❌ Failed: 2 checks
⚠️  Warning: 1 check

Critical Issues:
1. OLD_ENABLED field in kategori_yayin_tipi_field_dependencies
2. sync_OLD_ENABLED field in ilan_takvim_sync
```

### Auto-Fix Available

```bash
# Automatic Context7 fixes
php artisan context7:validate-migration --auto-fix

# Migration syntax fixes
./scripts/fix-migrations.sh
```

---

## 🔄 MIGRATION PATTERNS

### Status Field Migration

```php
// ✅ OLD_ENABLED → status conversion
Schema::table('table_name', function (Blueprint $table) {
    // Add status field
    $table->enum('status', ['active', 'passive', 'archived'])
          ->default('active')
          ->after('id');

    // Update data
    DB::statement("UPDATE table_name SET status = CASE
        WHEN OLD_ENABLED = 1 THEN 'active'
        ELSE 'passive'
    END");

    // Drop old field
    $table->dropColumn('OLD_ENABLED');
});
```

### Boolean Field Migration

```php
// ✅ aktif → is_published conversion
Schema::table('table_name', function (Blueprint $table) {
    $table->tinyInteger('is_published')->default(0)->after('status');

    DB::statement("UPDATE table_name SET is_published = aktif");

    $table->dropColumn('aktif');
});
```

---

## 🎯 BEST PRACTICES

### Validation Rules

```php
// ✅ Context7 Request Validation
class StoreIlanRequest extends FormRequest {
    public function rules(): array {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|in:TRL,USD,EUR',
            'status' => 'required|in:active,passive,archived',
            'is_published' => 'boolean',
            'category_id' => 'required|exists:categories,id'
        ];
    }
}
```

### API Response Format

```json
{
    "status": "success",
    "data": {
        "id": 1,
        "title": "Luxury Villa",
        "price": 850000,
        "currency": "TRL",
        "status": "active",
        "is_published": true,
        "created_at": "2025-11-25T10:30:00Z"
    }
}
```

### Frontend Display

```php
<!-- ✅ Blade Template -->
<div class="property-card">
    <h3>{{ $property->title }}</h3>
    <p class="price">{{ number_format($property->price) }} {{ $property->currency }}</p>

    @if($property->status === 'active')
        <span class="badge bg-success">Active</span>
    @endif

    @if($property->is_published)
        <span class="badge bg-info">Published</span>
    @endif
</div>
```

---

## ⚡ QUICK COMMANDS

### Development Workflow

```bash
# 1. Check compliance
php artisan context7:validate-migration --all

# 2. Auto-fix issues
php artisan context7:validate-migration --auto-fix

# 3. Run migrations
php artisan migrate

# 4. Clear cache
php artisan cache:clear

# 5. Start services
./scripts/services/start-all-mcp-servers.sh
```

### VS Code Tasks

- `Ctrl+Shift+P` → "Tasks: Run Task"
- "Context7: Validate All"
- "Context7: Auto Fix"
- "Laravel: Run Migrations"

---

## 📚 KAYNAK DOSYALAR (BİRLEŞTİRİLDİ)

Bu dokümanda şu dosyalar birleştirilmiştir:

1. `CONTEXT7-MASTER-GUIDE.md`
2. `CONTEXT7-RULES-DETAILED.md`
3. `CONTEXT7-COMPLIANCE.md`
4. `CONTEXT7-MCP-USAGE.md`

**Context7 Compliance:** ✅ C7-KONSOLIDE-2025-11-25  
**Tarih:** 25 Kasım 2025
