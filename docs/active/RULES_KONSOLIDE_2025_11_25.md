# 📐 EmlakPro Kurallar ve Standardlar - Konsolide Dokümantasyon

**Son Güncelleme:** 25 Kasım 2025  
**Context7 Standardı:** C7-RULES-KONSOLIDE-2025-11-25  
**Master Rules & Standards:** 3 Dokümandan Birleştirme

---

## 📋 İÇİNDEKİLER

1. [Master Rules](#master-rules)
2. [Standardization Guide](#standardization-guide)
3. [AI Model Kuralları](#ai-model-kurallari)
4. [Development Standards](#development-standards)
5. [Code Quality Rules](#code-quality-rules)

---

## 📏 MASTER RULES

### Context7 Core Standards

#### Database Field Naming (ZORUNLU)

```sql
-- ✅ DOĞRU Pattern
status ENUM('active','passive','archived') DEFAULT 'active'
is_published TINYINT(1) DEFAULT 0
is_featured TINYINT(1) DEFAULT 0
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

-- ❌ YASAK Pattern
durum VARCHAR(255)                    -- Use 'status'
aktif_mi TINYINT(1)                   -- Use 'is_published'
OLD_FIELD TINYINT(1)                  -- Use proper naming
yayin_durumu INT                      -- Use 'is_published'
```

#### CSS Framework Rules (ZORUNLU)

```css
/* ✅ SADECE Tailwind CSS */
.bg-blue-600 .hover:bg-blue-700 .text-white .font-medium

/* ❌ YASAK - Neo Design System */
/* .neo-btn .neo-card .neo-input */

/* ❌ YASAK - Bootstrap Classes */
/* .btn .btn-primary .card .form-control */
```

#### JavaScript Framework Rules

```javascript
// ✅ TERCİH EDİLEN - Alpine.js
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle</button>
</div>

// ❌ MİNİMİZE ET - jQuery (Legacy support only)
// $('#element').on('click', function() { ... });

// ❌ YASAK - Heavy frameworks for simple tasks
// Vue.js, React.js for basic interactions
```

### API Standards

#### Response Format (ZORUNLU)

```json
{
    "status": "success|error",
    "data": {...},
    "message": "Optional message",
    "meta": {
        "pagination": {...},
        "timestamp": "ISO-8601",
        "version": "2.0"
    }
}
```

#### Rate Limiting

```php
// API throttling (ZORUNLU)
Route::middleware(['throttle:60,1'])->group(function () {
    // Standard API routes
});

Route::middleware(['throttle:10,1'])->group(function () {
    // AI/Heavy computation routes
});
```

---

## 🎯 STANDARDIZATION GUIDE

### File Naming Standards

#### Migration Files

```php
// ✅ DOĞRU Format
2025_11_25_000001_create_properties_table.php
2025_11_25_000002_add_status_field_to_categories_table.php

// ❌ YANLIŞ Format
migration_create_ilanlar.php
add_column_to_table.php
```

#### Model Files

```php
// ✅ DOĞRU - Singular, PascalCase
app/Models/Property.php
app/Models/PropertyCategory.php
app/Models/UserProfile.php

// ❌ YANLIŞ
app/Models/properties.php
app/Models/ilan.php
app/Models/user-profile.php
```

#### Service Files

```php
// ✅ DOĞRU - Service suffix
app/Services/PropertyService.php
app/Services/AIAnalysisService.php
app/Services/EmailNotificationService.php

// ❌ YANLIŞ
app/Services/PropertyManager.php
app/Services/AIHelper.php
```

### Directory Structure Standards

#### Module Organization

```
app/Modules/
├── ModuleName/
│   ├── Controllers/
│   ├── Models/
│   ├── Services/
│   ├── Requests/
│   ├── Resources/
│   └── routes.php
```

#### Resource Organization

```
resources/
├── views/
│   ├── admin/
│   ├── frontend/
│   └── emails/
├── js/
│   ├── admin/
│   ├── frontend/
│   └── components/
└── css/
    ├── admin/
    └── frontend/
```

---

## 🤖 AI MODEL KURALLARI

### Eloquent Model Standards

#### Relationship Loading (KRİTİK)

```php
// ✅ DOĞRU - with() ile relationship yükleme
$property = Property::with(['category', 'features', 'user'])->find(1);

// ❌ YANLIŞ - Accessor'ları with() ile yüklemeye çalışmak
$property = Property::with(['formatted_title'])->find(1); // HATA!

// ✅ DOĞRU - Accessor kullanımı
$property = Property::find(1);
$title = $property->formatted_title; // Bu doğru
```

#### Field Dependencies ve Relationships

```php
class Property extends Model {
    // ✅ DOĞRU - Relationship definitions
    public function category() {
        return $this->belongsTo(PropertyCategory::class, 'category_id');
    }

    public function features() {
        return $this->belongsToMany(PropertyFeature::class);
    }

    // ✅ DOĞRU - Accessor definitions
    public function getFormattedTitleAttribute() {
        return ucfirst($this->title);
    }

    // ❌ YANLIŞ - Relationship accessor karıştırması
    // Accessor'ları eager load etmeye çalışmak
}
```

#### Query Optimization (ZORUNLU)

```php
// ✅ DOĞRU - N+1 Query Prevention
Property::with(['category', 'user', 'features'])->get();

// ✅ DOĞRU - Specific field selection
Property::select('id', 'title', 'price', 'status')
    ->with('category:id,name')
    ->get();

// ❌ YANLIŞ - N+1 Query Problem
foreach (Property::all() as $property) {
    echo $property->category->name; // N+1 problem!
}
```

### Database Migration Standards

#### Index Strategy

```php
Schema::create('properties', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->enum('status', ['active', 'passive', 'archived'])->default('active');
    $table->tinyInteger('is_published')->default(0);
    $table->foreignId('category_id')->constrained();
    $table->timestamps();

    // ✅ ZORUNLU Indexes
    $table->index(['status', 'is_published']); // Compound index
    $table->index('created_at'); // Timeline queries
    $table->index('category_id'); // Foreign key index
});
```

#### Null Handling Standards

```php
// ✅ DOĞRU - Blade template null handling
{{ $property->description ?? '—' }}
{{ $property->price ?? 'Fiyat Belirtilmemiş' }}

// ❌ YANLIŞ - Null kontrolsüz kullanım
{{ $property->description }}
{{ $property->price }}
```

---

## 💻 DEVELOPMENT STANDARDS

### Code Quality Requirements

#### PSR-12 Compliance (ZORUNLU)

```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Property;
use Illuminate\Support\Collection;

class PropertyService
{
    public function __construct(
        private Property $propertyModel
    ) {
    }

    public function getActiveProperties(): Collection
    {
        return $this->propertyModel
            ->where('status', 'active')
            ->where('is_published', 1)
            ->with(['category', 'user'])
            ->get();
    }
}
```

#### Type Declaration (ZORUNLU)

```php
// ✅ DOĞRU - Type hints and return types
public function calculatePrice(int $basePrice, float $taxRate): float
{
    return $basePrice * (1 + $taxRate);
}

public function getProperties(): Collection
{
    return Property::all();
}

// ❌ YANLIŞ - Type declaration yok
public function calculatePrice($basePrice, $taxRate)
{
    return $basePrice * (1 + $taxRate);
}
```

### Security Standards

#### Input Validation (ZORUNLU)

```php
class StorePropertyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,passive,archived',
            'is_published' => 'boolean',
            'category_id' => 'required|exists:property_categories,id'
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Başlık alanı zorunludur.',
            'price.numeric' => 'Fiyat sayısal bir değer olmalıdır.'
        ];
    }
}
```

#### SQL Injection Prevention (ZORUNLU)

```php
// ✅ DOĞRU - Eloquent ORM kullanımı
Property::where('status', 'active')
    ->where('price', '>=', $minPrice)
    ->get();

// ✅ DOĞRU - Parameter binding
DB::select('SELECT * FROM properties WHERE price BETWEEN ? AND ?', [$min, $max]);

// ❌ YASAK - Raw SQL concatenation
DB::select("SELECT * FROM properties WHERE price > $minPrice");
```

### Performance Standards

#### Caching Strategy (ÖNERİLEN)

```php
// ✅ DOĞRU - Query result caching
$categories = Cache::remember('property_categories', 3600, function () {
    return PropertyCategory::where('status', 'active')->get();
});

// ✅ DOĞRU - View caching
Cache::remember("property_view_{$propertyId}", 1800, function () use ($propertyId) {
    return Property::with(['category', 'features'])->find($propertyId);
});
```

#### Pagination (ZORUNLU)

```php
// ✅ DOĞRU - Pagination kullanımı
public function index(Request $request)
{
    $properties = Property::with(['category', 'user'])
        ->where('status', 'active')
        ->paginate(15);

    return view('properties.index', compact('properties'));
}

// ❌ YANLIŞ - get() ile tüm kayıtları çekme
$properties = Property::with(['category', 'user'])->get();
```

---

## 🧪 CODE QUALITY RULES

### Testing Standards

#### Unit Test Structure

```php
<?php

namespace Tests\Unit\Services;

use App\Services\PropertyService;
use App\Models\Property;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PropertyServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PropertyService $propertyService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->propertyService = app(PropertyService::class);
    }

    public function test_can_get_active_properties(): void
    {
        // Arrange
        Property::factory()->create(['status' => 'active', 'is_published' => 1]);
        Property::factory()->create(['status' => 'passive', 'is_published' => 0]);

        // Act
        $result = $this->propertyService->getActiveProperties();

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals('active', $result->first()->status);
    }
}
```

#### Feature Test Structure

```php
public function test_user_can_create_property(): void
{
    // Arrange
    $user = User::factory()->create();
    $categoryId = PropertyCategory::factory()->create()->id;

    $propertyData = [
        'title' => 'Test Property',
        'description' => 'Test description',
        'price' => 100000,
        'category_id' => $categoryId,
        'status' => 'active'
    ];

    // Act
    $response = $this->actingAs($user)
        ->post('/admin/properties', $propertyData);

    // Assert
    $response->assertRedirect();
    $this->assertDatabaseHas('properties', [
        'title' => 'Test Property',
        'status' => 'active'
    ]);
}
```

### Documentation Standards

#### Code Comments (ÖNERİLEN)

```php
/**
 * Calculate property valuation based on market data
 *
 * @param Property $property The property to valuate
 * @param array $marketData Market comparison data
 * @return float The calculated valuation in TRY
 *
 * @throws InvalidArgumentException If property data is insufficient
 */
public function calculateValuation(Property $property, array $marketData): float
{
    // Complex business logic requires explanation
    $baseValue = $property->area * $this->getLocationMultiplier($property->location);

    // Apply market adjustment based on recent sales
    $marketAdjustment = $this->calculateMarketAdjustment($marketData);

    return $baseValue * $marketAdjustment;
}
```

#### README Documentation (ZORUNLU)

```markdown
# Module Name

## Overview

Brief description of what this module does.

## Installation

Step-by-step installation instructions.

## Configuration

Configuration options and environment variables.

## Usage Examples

Code examples showing how to use the module.

## API Reference

Detailed API documentation.

## Testing

How to run tests for this module.
```

---

## 📚 KAYNAK DOSYALAR (BİRLEŞTİRİLDİ)

Bu dokümanda şu dosyalar birleştirilmiştir:

1. `docs/rules/master-rules.md`
2. `docs/rules/STANDARDIZATION_GUIDE.md`
3. `docs/rules/instructions/ai-model-kurallari.instructions.md`

**Context7 Compliance:** ✅ C7-RULES-KONSOLIDE-2025-11-25  
**Tarih:** 25 Kasım 2025
