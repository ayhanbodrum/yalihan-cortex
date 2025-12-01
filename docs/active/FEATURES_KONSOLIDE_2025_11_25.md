# 🚀 EmlakPro Özellikler - Konsolide Dokümantasyon

**Son Güncelleme:** 25 Kasım 2025  
**Context7 Standardı:** C7-FEATURES-KONSOLIDE-2025-11-25  
**Özellik Sayısı:** 3 Ana Özellik Modülü

---

## 📋 İÇİNDEKİLER

1. [Harita Sistemi](#harita)
2. [Property Type Manager](#property-type)
3. [Yazlık Kiralama](#yazlik)
4. [Entegrasyon Noktaları](#entegrasyon)
5. [API Endpoints](#api-endpoints)

---

## 🗺️ HARİTA SİSTEMİ {#harita}

### Leaflet.js Tabanlı Modern Harita

**Özellikler:**

- ✅ **Interactive Map** - Leaflet.js 1.9+
- ✅ **Location Picker** - Click-to-select koordinat
- ✅ **Multi-layer Support** - OpenStreetMap, Google Satellite
- ✅ **Marker Management** - Custom property markers
- ✅ **Search Integration** - Address → coordinate dönüşümü
- ✅ **Mobile Responsive** - Touch-friendly controls

### Kullanım Alanları

#### İlan Lokasyon Seçimi

```javascript
// Leaflet harita entegrasyonu
const map = L.map('property-map').setView([37.0167, 27.4167], 13);

// İlan lokasyon seçimi
map.on('click', function (e) {
    const { lat, lng } = e.latlng;
    updatePropertyLocation(lat, lng);
    addPropertyMarker(lat, lng);
});

// Custom property marker
function addPropertyMarker(lat, lng) {
    L.marker([lat, lng], {
        icon: L.divIcon({
            html: '<div class="property-marker">🏠</div>',
            className: 'custom-property-marker',
            iconSize: [30, 30],
        }),
    }).addTo(map);
}
```

#### Harita Katmanları

```javascript
// Base layers
const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
});

const satelliteLayer = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
    maxZoom: 20,
    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
    attribution: '© Google',
});

// Layer control
L.control
    .layers({
        Harita: osmLayer,
        Uydu: satelliteLayer,
    })
    .addTo(map);
```

### Context7 Uyumlu Implementation

#### Database Schema

```sql
CREATE TABLE property_locations (
    id BIGINT UNSIGNED PRIMARY KEY,
    property_id BIGINT UNSIGNED NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    address TEXT,
    district_id INT,
    neighborhood_id INT,
    map_zoom_level INT DEFAULT 15,
    status ENUM('active', 'passive', 'archived') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    INDEX idx_coordinates (latitude, longitude),
    INDEX idx_district (district_id),
    INDEX idx_status (status)
);
```

---

## 🏠 PROPERTY TYPE MANAGER {#property-type}

### Dinamik Kategori Yönetimi

**Özellikler:**

- ✅ **Hierarchical Categories** - Ana → Alt kategori sistemi
- ✅ **Dynamic Fields** - Kategoriye özel alan yönetimi
- ✅ **Field Dependencies** - Koşullu alan gösterimi
- ✅ **Validation Rules** - Kategori-özel validation
- ✅ **Custom Properties** - Özelleştirilebilir özellikler

### Kategori Hiyerarşisi

#### Ana Kategoriler

```php
class PropertyCategory extends Model {
    const TYPE_RESIDENTIAL = 'residential';
    const TYPE_COMMERCIAL = 'commercial';
    const TYPE_LAND = 'land';
    const TYPE_PROJECT = 'project';
    const TYPE_VACATION = 'vacation';

    public function subCategories() {
        return $this->hasMany(PropertySubCategory::class, 'parent_id');
    }

    public function requiredFields() {
        return $this->hasMany(CategoryField::class);
    }
}
```

#### Field Dependencies Sistemi

```javascript
// Alpine.js ile dinamik field gösterimi
window.propertyTypeManager = function () {
    return {
        selectedCategory: null,
        selectedSubCategory: null,
        availableFields: [],
        fieldValues: {},

        async loadCategoryFields() {
            if (!this.selectedCategory) return;

            const response = await fetch(`/api/categories/${this.selectedCategory}/fields`);
            this.availableFields = await response.json();

            // Reset field values for new category
            this.fieldValues = {};
        },

        isFieldVisible(field) {
            if (!field.depends_on) return true;

            return this.fieldValues[field.depends_on] === field.depends_value;
        },
    };
};
```

### Özellik Yönetimi

#### Category-Specific Fields

```php
class CategoryField extends Model {
    protected $fillable = [
        'category_id',
        'field_name',
        'field_type',
        'field_label',
        'is_required',
        'depends_on_field',
        'depends_on_value',
        'validation_rules',
        'field_options',
        'display_order'
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'validation_rules' => 'array',
        'field_options' => 'array'
    ];
}
```

#### Field Types

```yaml
Supported Field Types:
    - text: Basic text input
    - number: Numeric input
    - select: Dropdown selection
    - multi_select: Multiple selection
    - boolean: Checkbox
    - date: Date picker
    - textarea: Large text area
    - file: File upload
    - coordinates: Map location picker
```

---

## 🏖️ YAZLIK KİRALAMA {#yazlik}

### Modern Vacation Rental Sistemi

**Özellikler:**

- ✅ **Seasonal Pricing** - Yaz/Kış/Ara sezon fiyatlandırma
- ✅ **Booking Calendar** - Müsaitlik takvimi
- ✅ **Guest Management** - Misafir takip sistemi
- ✅ **Amenities System** - Tesis özellikleri
- ✅ **Revenue Analytics** - Gelir analizi
- ✅ **Multi-currency** - TRY, USD, EUR support

### Rezervasyon Sistemi

#### Calendar Implementation

```javascript
// FullCalendar.js entegrasyonu
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('booking-calendar');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        selectable: true,
        selectMirror: true,

        events: '/api/vacation-rentals/bookings',

        select: function (info) {
            // Yeni rezervasyon oluştur
            createBooking(info.start, info.end);
        },

        eventClick: function (info) {
            // Rezervasyon detaylarını göster
            showBookingDetails(info.event.id);
        },
    });

    calendar.render();
});
```

#### Pricing Calculator

```php
class VacationRentalPricingService {
    public function calculateTotalPrice($propertyId, $checkIn, $checkOut, $guestCount) {
        $property = VacationProperty::find($propertyId);
        $nights = Carbon::parse($checkIn)->diffInDays($checkOut);

        // Sezonluk fiyat hesaplama
        $seasonalPrice = $this->getSeasonalPrice($property, $checkIn, $checkOut);

        // Base price
        $basePrice = $seasonalPrice * $nights;

        // Extra guest fee
        $extraGuestFee = max(0, $guestCount - $property->base_guest_count) * $property->extra_guest_fee;

        // Cleaning fee (one-time)
        $cleaningFee = $property->cleaning_fee;

        // Service fee
        $serviceFee = ($basePrice + $extraGuestFee) * 0.1; // 10%

        return [
            'base_price' => $basePrice,
            'extra_guest_fee' => $extraGuestFee,
            'cleaning_fee' => $cleaningFee,
            'service_fee' => $serviceFee,
            'total' => $basePrice + $extraGuestFee + $cleaningFee + $serviceFee
        ];
    }
}
```

### Sezonluk Fiyatlandırma

#### Season Management

```sql
CREATE TABLE vacation_seasons (
    id BIGINT UNSIGNED PRIMARY KEY,
    property_id BIGINT UNSIGNED NOT NULL,
    season_name VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    daily_price DECIMAL(10,2) NOT NULL,
    weekly_price DECIMAL(10,2),
    monthly_price DECIMAL(10,2),
    minimum_stay INT DEFAULT 1,
    currency ENUM('TRY','USD','EUR') DEFAULT 'TRY',
    status ENUM('active','passive','archived') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    INDEX idx_property_dates (property_id, start_date, end_date),
    INDEX idx_season (season_name)
);
```

---

## 🔗 ENTEGRASYON NOKTALARI {#entegrasyon}

### API Endpoints

#### Harita Sistemi

```php
// Map-related API endpoints
Route::group(['prefix' => 'maps'], function() {
    Route::get('/search-address', [MapController::class, 'searchAddress']);
    Route::get('/reverse-geocode', [MapController::class, 'reverseGeocode']);
    Route::post('/save-location', [MapController::class, 'saveLocation']);
    Route::get('/nearby-properties', [MapController::class, 'nearbyProperties']);
});
```

#### Property Type Manager

```php
// Category management API
Route::group(['prefix' => 'categories'], function() {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{id}/subcategories', [CategoryController::class, 'subcategories']);
    Route::get('/{id}/fields', [CategoryController::class, 'fields']);
    Route::post('/validate-fields', [CategoryController::class, 'validateFields']);
});
```

#### Yazlık Kiralama

```php
// Vacation rental API
Route::group(['prefix' => 'vacation-rentals'], function() {
    Route::get('/{id}/availability', [VacationController::class, 'checkAvailability']);
    Route::post('/{id}/booking', [VacationController::class, 'createBooking']);
    Route::get('/{id}/pricing', [VacationController::class, 'calculatePricing']);
    Route::get('/bookings', [VacationController::class, 'getBookings']);
});
```

### Frontend Components

#### Vue.js Components (Legacy Support)

```javascript
// Property type selector component
Vue.component('property-type-selector', {
    template: `
        <div class="property-type-selector">
            <select v-model="selectedType" @change="loadSubTypes">
                <option v-for="type in propertyTypes" :value="type.id">
                    {{ type.name }}
                </option>
            </select>
        </div>
    `,

    data() {
        return {
            propertyTypes: [],
            selectedType: null,
        };
    },
});
```

#### Alpine.js Components (Preferred)

```html
<!-- Modern Alpine.js implementation -->
<div x-data="propertyFeatures()">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <template x-for="feature in availableFeatures">
            <div class="feature-item">
                <label class="flex items-center space-x-3">
                    <input
                        type="checkbox"
                        :value="feature.id"
                        x-model="selectedFeatures"
                        class="rounded border-gray-300"
                    />
                    <span x-text="feature.name"></span>
                </label>
            </div>
        </template>
    </div>
</div>
```

---

## 📚 KAYNAK DOSYALAR (BİRLEŞTİRİLDİ)

Bu dokümanda şu dosyalar birleştirilmiştir:

1. `docs/features/HARITA_SISTEMI.md`
2. `docs/features/PROPERTY_TYPE_MANAGER.md`
3. `docs/features/YAZLIK_KIRALAMA.md`

**Context7 Compliance:** ✅ C7-FEATURES-KONSOLIDE-2025-11-25  
**Tarih:** 25 Kasım 2025
