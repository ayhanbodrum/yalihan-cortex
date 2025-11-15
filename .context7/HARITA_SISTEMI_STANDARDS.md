# 🗺️ Harita Sistemi Standartları - Context7

**Tarih:** 31 Ekim 2025  
**Context7 Version:** 3.6.1  
**Durum:** Active

---

## 🎯 Temel Standartlar

### **1. Harita Altyapısı**

```yaml
Library: Leaflet.js 1.9.4
Source: npm (LOCAL, not CDN)
Tile Provider: OpenStreetMap + Esri (Satellite)
Geocoding: Nominatim API
Drawing: Leaflet.draw (npm)
```

### **2. Çift Yönlü Sync Pattern**

```javascript
// ✅ DOGRU (Silent Update Pattern ile)
isSilentUpdate: false,  // Flag tanımla

async autoSelectDropdowns() {
    this.isSilentUpdate = true;  // Loop önle
    // ... dropdown updates
    setTimeout(() => this.isSilentUpdate = false, 100);
}

// Event listener'da kontrol:
if (window.VanillaLocationManager.isSilentUpdate) {
    return;  // Skip map focus
}

// ❌ YANLIŞ (Loop riski)
// Harita tıklama → Dropdown update → Harita focus (tekrar)
```

### **3. Console Log Standardı**

```javascript
// ✅ DOGRU (DEBUG_MODE Pattern)
const DEBUG_MODE = {{ config('app.debug') ? 'true' : 'false' }};
const log = (...args) => DEBUG_MODE && console.log(...args);

log('✅ Debug mesaj');  // Production'da görünmez
console.error('❌ Hata');  // Her zaman görünür

// ❌ YANLIŞ
console.log('Debug mesaj');  // Production'da kirlilik
```

### **4. Field Naming (Context7)**

```yaml
✅ DOGRU:
    - mahalle_id (NOT semt_id)
    - il_id (NOT sehir_id)
    - nearby_distances (JSON)
    - boundary_geojson (JSON)

❌ YANLIŞ:
    - semt_id (Context7 violation!)
    - sehir_id (eski naming)
```

### **5. API Response Pattern**

```javascript
// ✅ DOGRU (Wrapper parse)
const response = await fetch('/api/location/provinces');
const jsonData = await response.json();
const iller = jsonData.data || jsonData;  // Handle wrapper

if (!Array.isArray(iller)) {
    console.error('Not an array');
    return;
}

// ❌ YANLIŞ
const iller = await response.json();  // Direkt assign
iller.find(...)  // TypeError risk
```

### **6. UI Buton Boyutları**

```yaml
Harita Kontrolleri:
  ✅ DOGRU: w-8 h-8 (32x32px) - Kompakt
  ❌ YANLIŞ: w-10 h-10 (40x40px) - Çok büyük

Standart/Uydu Toggle:
  ✅ DOGRU: px-2.5 py-1.5 text-xs - Kompakt
  ❌ YANLIŞ: px-4 py-2.5 text-sm - Büyük

Z-Index:
  ✅ DOGRU: z-index: 9999 !important
  ❌ YANLIŞ: z-[100] (Leaflet kontrollerinin altında)
```

### **7. CSP Compliance**

```yaml
✅ DOGRU:
    - Leaflet.js: npm package + local
    - Leaflet.draw: npm package + spritesheet public folder
    - Spritesheet: public/vendor/leaflet-draw/images/
    - CSS override: background-image path

❌ YANLIŞ:
    - CDN links (CSP violation risk)
    - Vite dev server assets (http://localhost:5175/...)
```

---

## 🔧 Kullanım Örnekleri

### **Harita Initialization**

```javascript
const VanillaLocationManager = {
    map: null,
    marker: null,
    standardLayer: null,
    satelliteLayer: null,
    isSilentUpdate: false, // ✅ Loop önleme flag

    initMap() {
        this.map = L.map('map').setView([37.0344, 27.4305], 13);

        this.standardLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap',
            maxZoom: 19,
        }).addTo(this.map);

        this.satelliteLayer = L.tileLayer(
            'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
            { attribution: '© Esri', maxZoom: 19 }
        );
    },
};
```

### **Reverse Geocoding**

```javascript
async reverseGeocode(lat, lng) {
    const url = `https://nominatim.openstreetmap.org/reverse?` +
        `lat=${lat}&lon=${lng}&format=json&addressdetails=1`;

    const response = await fetch(url, {
        headers: { 'User-Agent': 'YalihanEmlak/1.0' }  // ✅ Gerekli
    });

    const data = await response.json();

    // Address components parsing
    if (data.address.road) {
        if (road.includes('bulvar')) bulvarField.value = road;
        else if (road.includes('cadde')) caddeField.value = road;
        else sokakField.value = road;
    }
}
```

### **Distance Calculation (Haversine)**

```javascript
function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371e3; // Earth radius (meters)
    const φ1 = (lat1 * Math.PI) / 180;
    const φ2 = (lat2 * Math.PI) / 180;
    const Δφ = ((lat2 - lat1) * Math.PI) / 180;
    const Δλ = ((lon2 - lon1) * Math.PI) / 180;

    const a =
        Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
        Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ / 2) * Math.sin(Δλ / 2);

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

    return R * c; // meters
}
```

---

## 🚫 Yasaklanan Kullanımlar

```yaml
❌ Heavy Map Libraries:
    - Google Maps (ücretli)
    - Mapbox GL JS (ağır)

❌ CDN Links:
    - <script src="https://unpkg.com/leaflet-draw@..."> (CSP risk)

❌ Duplicate Definitions:
    - Aynı object/function 2+ yerde tanımlı

❌ Console Log Abuse:
    - Production'da 50+ log mesajı

❌ Infinite Loops:
    - Çift yönlü sync'de flag kullanmamak
```

---

## ✅ Best Practices

1. **Her Zaman Silent Update Pattern Kullan** (çift yönlü sync'de)
2. **DEBUG_MODE ile Console Log'ları Kontrol Et**
3. **API Response Wrapper'ları Parse Et** (jsonData.data || jsonData)
4. **Fuzzy Matching Yap** (case-insensitive, includes)
5. **Highlight Effects Ekle** (user feedback)
6. **Z-Index'i Yüksek Tut** (9999 for custom controls)
7. **Kompakt UI Tasarla** (mobil için)
8. **Local Assets Kullan** (CSP compliance)
9. **Backup Oluştur** (refactoring öncesi)
10. **Error Handling Yap** (try/catch + toast messages)

---

**Bu standartlar TÜM harita implementasyonları için geçerlidir.**
