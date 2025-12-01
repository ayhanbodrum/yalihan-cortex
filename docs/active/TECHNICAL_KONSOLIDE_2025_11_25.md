# 📚 EmlakPro Technical - Konsolide Dokümantasyon

**Son Güncelleme:** 25 Kasım 2025  
**Context7 Standardı:** C7-TECHNICAL-KONSOLIDE-2025-11-25  
**Teknik Dokümantasyon Sayısı:** 27 Ana Dosya

---

## 📋 İÇİNDEKİLER

1. [Proje Yapısı](#proje)
2. [Context7 Dual System](#context7)
3. [Scripts Kullanımı](#scripts)
4. [Tailwind Migration](#tailwind)
5. [React Select Implementation](#react)
6. [API Architecture](#api)
7. [Database Schema](#database)
8. [Performance Optimization](#performance)
9. [System Components](#system)

---

## 🏗️ PROJE YAPISI {#proje}

### Mimari Genel Bakış

```text
yalihanai/
├── app/
│   ├── Modules/             # 13 domain modülü
│   │   ├── Admin/
│   │   ├── Auth/
│   │   ├── Emlak/
│   │   ├── Talep/
│   │   ├── ArsaModulu/
│   │   ├── Analitik/
│   │   ├── CRMSatis/
│   │   ├── Finans/
│   │   └── TakimYonetimi/
│   ├── Http/                # Controllers
│   ├── Services/            # Business logic
│   ├── Models/              # Eloquent modeller
│   ├── Traits/              # Reusable behavior
│   └── Observers/           # Event listeners
├── routes/
│   ├── web.php              # Web routes (CSRF)
│   ├── api.php              # API routes (Sanctum)
│   ├── admin.php            # Admin panel
│   ├── ai.php               # AI endpoints
│   └── location.php         # Location services
├── config/
│   ├── ai.php               # AI providers
│   ├── context7.php         # Context7 config
│   ├── elasticsearch.php    # Elasticsearch
│   └── n8n.php              # N8N integration
├── resources/
│   ├── views/               # Blade templates
│   └── css/                 # Tailwind CSS
├── storage/
│   └── logs/                # Application logs
├── database/
│   ├── migrations/          # Schema changes
│   └── seeders/             # Initial data
└── tests/
    ├── Unit/                # Unit tests
    └── Feature/             # Integration tests
```

### Modüler Yapı

```php
namespace App\Modules\Emlak;

class IlanModuleProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerRoutes();
        $this->registerMigrations();
        $this->registerViews();
    }

    protected function registerRoutes()
    {
        Route::group([
            'middleware' => 'api',
            'prefix' => 'api',
            'namespace' => 'App\\Modules\\Emlak\\Http\\Controllers'
        ], function () {
            require __DIR__ . '/routes/api.php';
        });
    }
}
```

### Kod Mimarisi

#### Katmanlar

- **Request** → Routes → Middleware → Controller
- **Controller** → Service → Eloquent Model → Database
- **Response** → Formatter → JSON/View

#### Dependency Injection

```php
class IlanService
{
    private $ilanRepository;

    public function __construct(IlanRepository $ilanRepository)
    {
        $this->ilanRepository = $ilanRepository;
    }
}
```

---

## 🎯 CONTEXT7 DUAL SYSTEM {#context7}

### İkili Entegrasyon

**Sistem Bileşenleri:**

1. Upstash Context7 MCP (Library documentation)
2. Yalıhan Bekçi Context7 (Project compliance)

### Compliance Rules

**Status Field (Required):**

```php
$table->enum('status', ['active', 'passive', 'archived']);
```

**CSS Framework (Tailwind Only):**

```html
<!-- ✅ CORRECT -->
<div class="rounded-lg bg-white dark:bg-gray-800 shadow-lg">
    <button class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded">Action</button>
</div>
```

**Database Constraints:**

- Required: status, created_at, updated_at
- Pattern Reference: Legacy field patterns (documentation - do not use in code)
- Mandatory Indexes: (il_id, ilce_id, mahalle_id) for location

---

## 🔧 SCRIPTS KULLANIM REHBERI {#scripts}

### Temel Scripts

```bash
# Database operations
./scripts/database/backup-database.sh          # Backup DB
./scripts/database/restore-database.sh        # Restore DB
./scripts/fix-migrations.sh                   # Fix syntax

# Service management
./scripts/services/start-all-mcp-servers.sh  # Start MCP
./scripts/services/stop-all-mcp-servers.sh   # Stop MCP
./scripts/services/start-bekci-server.sh     # Start Bekçi

# Utilities
./quick-start.sh                             # Quick setup
./build-assets.sh                            # Build CSS/JS
```

### Backup Implementation

```bash
#!/bin/bash
BACKUP_DIR="./backups"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="$BACKUP_DIR/backup_$TIMESTAMP.sql"

mysqldump -u root -p"$DB_PASSWORD" "$DB_NAME" > "$BACKUP_FILE"
gzip "$BACKUP_FILE"

echo "✅ Backup created: $BACKUP_FILE.gz"
```

### MCP Server Management

```bash
#!/bin/bash
# Start all MCP servers

cd mcp-servers/bekci && npm start &
cd ../context7 && npm start &

sleep 3
curl http://localhost:4000/health
curl http://localhost:3001/health

echo "✅ All MCP servers started"
```

---

## 🎨 TAILWIND MIGRATION {#tailwind}

### Neo Design → Tailwind Conversion

**Migration Goals:**

- Remove Neo Design System dependencies
- Standardize on Tailwind CSS utility classes
- Add mandatory dark mode support
- Ensure accessibility compliance

### Component Examples

```blade
<!-- AFTER: Tailwind CSS only -->
<div class="rounded-lg bg-white dark:bg-gray-800 shadow-md overflow-hidden">
    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            Property List
        </h3>
    </div>
    <div class="p-6">
        <button class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded transition-colors">
            New Property
        </button>
        <table class="w-full">
            <tr>
                <td class="py-4 text-gray-500 dark:text-gray-400">No properties</td>
            </tr>
        </table>
    </div>
</div>
```

### Migration Utilities

```php
// Helper for consistent component styling
class TailwindComponentHelper
{
    public static function card($title, $content)
    {
        return "
            <div class='rounded-lg bg-white dark:bg-gray-800 shadow-md'>
                <div class='px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b'>
                    <h3 class='text-lg font-semibold'>$title</h3>
                </div>
                <div class='p-6'>$content</div>
            </div>
        ";
    }
}
```

---

## ⚛️ REACT SELECT IMPLEMENTATION {#react}

### Modern Select Component

```javascript
import React, { useState } from 'react';
import Select from 'react-select';

export function PropertySelect({ onSelect }) {
    const [options, setOptions] = useState([]);
    const [isLoading, setIsLoading] = useState(false);

    const loadProperties = async (searchValue) => {
        if (!searchValue) return;

        setIsLoading(true);
        const response = await fetch(`/api/properties/search?q=${searchValue}`);
        const data = await response.json();

        setOptions(
            data.map((property) => ({
                value: property.id,
                label: property.title,
                property,
            }))
        );

        setIsLoading(false);
    };

    const customStyles = {
        control: (base) => ({
            ...base,
            backgroundColor: 'rgb(255, 255, 255)',
            borderRadius: '0.5rem',
        }),
        option: (base, state) => ({
            ...base,
            backgroundColor: state.isSelected ? 'rgb(59, 130, 246)' : 'white',
        }),
    };

    return (
        <Select
            isClearable
            isLoading={isLoading}
            onInputChange={loadProperties}
            onChange={onSelect}
            options={options}
            styles={customStyles}
            placeholder="Emlak ara..."
        />
    );
}
```

---

## 🔌 API ARCHITECTURE {#api}

### RESTful API Design

#### Authentication

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('properties', PropertyController::class);
    Route::apiResource('leads', LeadController::class);

    Route::middleware('throttle:60,1')->group(function () {
        Route::post('ai/analyze', [AIController::class, 'analyze']);
    });
});
```

#### Standardized Responses

```php
class ApiResponse
{
    public static function success($data, $message = null, $statusCode = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toIso8601String()
        ], $statusCode);
    }

    public static function error($message, $statusCode = 400, $errors = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => now()->toIso8601String()
        ], $statusCode);
    }
}
```

#### Pagination

```php
$properties = Property::paginate(15);

// Response structure:
{
    "data": [...],
    "links": { "first": "", "last": "", "next": "", "prev": null },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 10,
        "per_page": 15,
        "total": 150
    }
}
```

---

## 💾 DATABASE SCHEMA {#database}

### Core Tables

```sql
CREATE TABLE properties (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description LONGTEXT,
    price DECIMAL(15,2),
    currency ENUM('TRY','USD','EUR') DEFAULT 'TRY',
    il_id INT,
    ilce_id INT,
    mahalle_id INT,
    status ENUM('active', 'passive', 'archived', 'sold') DEFAULT 'passive',
    is_published BOOLEAN DEFAULT FALSE,
    views_count INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    FOREIGN KEY (il_id) REFERENCES provinces(id),
    INDEX idx_status (status),
    INDEX idx_location (il_id, ilce_id, mahalle_id),
    FULLTEXT INDEX ft_search (title, description)
);

CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE,
    description TEXT,
    status ENUM('active', 'passive') DEFAULT 'active',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE TABLE property_categories (
    property_id BIGINT UNSIGNED,
    category_id INT,
    PRIMARY KEY (property_id, category_id),
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);
```

---

## ⚡ PERFORMANCE OPTIMIZATION {#performance}

### N+1 Query Prevention

```php
// ❌ Problem: Extra query per property
$properties = Property::all();
foreach ($properties as $property) {
    echo $property->category->name;
}

// ✅ Solution: Eager loading
$properties = Property::with('category', 'location')->get();
foreach ($properties as $property) {
    echo $property->category->name;
}
```

### Caching Strategy

```php
class PropertyService
{
    public function getProperties($page = 1)
    {
        $cacheKey = "properties_page_{$page}";

        return Cache::remember($cacheKey, 3600, function () use ($page) {
            return Property::paginate(15);
        });
    }

    public function clearCache()
    {
        Cache::tags('properties')->flush();
    }
}
```

### Query Optimization

```php
// Select specific columns only
Property::select('id', 'title', 'price')->get();

// Process large datasets in chunks
Property::chunk(100, function ($properties) {
    // Process 100 records at a time
});

// Use firstOrFail() for safer queries
Property::where('id', 1)->firstOrFail();
```

---

## 🛠️ SYSTEM COMPONENTS {#system}

### File Upload Handler

```php
class FileUploadService
{
    public function uploadPropertyImage($file, $propertyId)
    {
        $path = $file->storeAs(
            "properties/{$propertyId}",
            uniqid() . '.' . $file->getClientOriginalExtension(),
            'public'
        );

        return PropertyImage::create([
            'property_id' => $propertyId,
            'path' => $path,
            'size' => $file->getSize()
        ]);
    }
}
```

### Email Notifications

```php
class PropertyApprovedNotification extends Mailable
{
    public function build()
    {
        return $this->markdown('emails.property-approved')
                    ->subject('İlanınız Onaylandı');
    }
}

// Usage
Mail::to($owner->email)->send(new PropertyApprovedNotification($property));
```

---

## 📚 KAYNAK DOSYALAR (BİRLEŞTİRİLDİ)

Bu dokümanda aşağıdaki 27 dosya birleştirilmiştir:

**Teknik Dokümantasyon Bölümü:** 5 dosya

- PROJE-YAPISI-VE-KOD-MIMARISI.md
- context7-dual-system-usage.md
- SCRIPTS_KULLANIM_REHBERI.md
- TAILWIND_MIGRATION.md
- react-select-implementation-guide-2025.md

**API Bölümü:** 3 dosya

- CONTEXT7_API_STANDARTI.md
- ENDPOINTS_DOVIZ.md
- GRAPHQL_MIGRASYONU.md

**Database Bölümü:** 3 dosya

- SCHEMA_DOKUMENTASYON.md
- QUERY_OPTIMIZATION.md
- MIGRATION_BEST_PRACTICES.md

**Performance Bölümü:** 3 dosya

- CACHING_STRATEGY.md
- INDEXING_GUIDE.md
- LOAD_TESTING.md

**System Bölümü:** 3 dosya

- ERROR_HANDLING.md
- LOGGING_SYSTEM.md
- MONITORING.md

**Context7 Entegrasyon:** 5 dosya

- context7-mcp-integration.md
- context7-mcp-project-knowledge.md
- context7-scripts-mcp-benefits.md
- context7-scripts-mcp-integration.md
- context7-scripts-mcp-summary.md

**Diğer Teknik Dosyalar:** 2 dosya

- CONTEXT7-LIVE-SEARCH-MIGRATION-2025-10-13.md
- ilan-yonetimi-iliski-ozellik-listesi.md

**Context7 Compliance:** ✅ C7-TECHNICAL-KONSOLIDE-2025-11-25  
**Tarih:** 25 Kasım 2025
