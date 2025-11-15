# 🗺️ Harita Araçları Standart Kuralları

**Tarih:** 5 Kasım 2025  
**Versiyon:** 2.0.0  
**Durum:** AKTIF - ZORUNLU  
**Kapsam:** Tüm harita kullanan sayfalar  
**Context7 Compliance:** %100

---

## 📋 GENEL STANDARTLAR

### **1. Harita Kütüphanesi**
```yaml
Library: Leaflet.js 1.9.4
Loading: Promise-based async
Timeout: 10 saniye max
Error Handling: ZORUNLU
CDN: Kabul edilir (npm tercih edilir)
```

### **2. Geocoding Service**
```yaml
Provider: Nominatim (OpenStreetMap)
User-Agent: ZORUNLU (YalihanEmlak/1.0)
Rate Limit: 1 request/second (ZORUNLU)
Retry: 3 attempt max
Backoff: Exponential (1s, 2s)
Language: Turkish (accept-language=tr)
```

---

## 🎯 ZORUNLU PATTERN'LER

### **A. Promise-Based Map Loading**

```javascript
// ❌ ESKİ (YASAK)
setTimeout(() => {
    if (typeof L === 'undefined') {
        setTimeout(() => this.initMap(), 1000);
    }
}, 500);

// ✅ YENİ (ZORUNLU)
async initMap() {
    try {
        await this.waitForLeaflet();
        // Harita init...
    } catch (error) {
        this.showMapError(error.message);
    }
}

waitForLeaflet() {
    return new Promise((resolve, reject) => {
        if (typeof L !== 'undefined') {
            resolve();
            return;
        }

        let attempts = 0;
        const maxAttempts = 50; // 10 saniye

        const checkInterval = setInterval(() => {
            attempts++;
            
            if (typeof L !== 'undefined') {
                clearInterval(checkInterval);
                resolve();
            } else if (attempts >= maxAttempts) {
                clearInterval(checkInterval);
                reject(new Error('Timeout: Leaflet yüklenemedi'));
            }
        }, 200);
    });
}
```

### **B. Draggable Marker (ZORUNLU)**

```javascript
// ❌ ESKİ (YASAK)
this.marker = L.marker([lat, lng]).addTo(this.map);

// ✅ YENİ (ZORUNLU)
this.marker = L.marker([lat, lng], {
    draggable: true,
    autoPan: true,
    title: 'Konumu değiştirmek için sürükleyin'
}).addTo(this.map);

// Drag event
this.marker.on('dragend', (e) => {
    const position = e.target.getLatLng();
    document.getElementById('enlem').value = position.lat.toFixed(7);
    document.getElementById('boylam').value = position.lng.toFixed(7);
    
    // Reverse geocoding yap
    this.reverseGeocode(position.lat, position.lng);
});

// Popup ekle
this.marker.bindPopup(`
    <strong>📍 Mülk Konumu</strong><br>
    ${lat.toFixed(6)}, ${lng.toFixed(6)}<br>
    <em class="text-xs">Sürükleyerek değiştirin</em>
`);
```

### **C. Bidirectional Coordinate Sync (ZORUNLU)**

```javascript
// Input → Map
document.getElementById('enlem').addEventListener('blur', () => {
    const lat = parseFloat(enlemInput.value);
    const lng = parseFloat(boylamInput.value);
    
    if (!isNaN(lat) && !isNaN(lng)) {
        VanillaLocationManager.setMarker(lat, lng, true); // skipReverseGeocode
        VanillaLocationManager.map.setView([lat, lng], 15);
    }
});

// Map → Input
map.on('click', (e) => {
    document.getElementById('enlem').value = e.latlng.lat.toFixed(7);
    document.getElementById('boylam').value = e.latlng.lng.toFixed(7);
});
```

### **D. Rate Limiting (Nominatim - ZORUNLU)**

```javascript
async reverseGeocode(lat, lng) {
    // ✅ RATE LIMITING
    const lastCall = this.lastGeocodeCall || 0;
    const timeSinceLastCall = Date.now() - lastCall;
    
    if (timeSinceLastCall < 1000) {
        const waitTime = 1000 - timeSinceLastCall;
        await new Promise(resolve => setTimeout(resolve, waitTime));
    }
    
    this.lastGeocodeCall = Date.now();
    
    // API call...
}
```

### **E. Retry Logic (ZORUNLU)**

```javascript
// ✅ 3 ATTEMPT RETRY
let response;
let lastError;

for (let attempt = 1; attempt <= 3; attempt++) {
    try {
        response = await fetch(url, { headers: {...} });

        if (response.ok) break;
        
        lastError = `HTTP ${response.status}`;
        
        // Exponential backoff
        if (attempt < 3) {
            await new Promise(r => setTimeout(r, attempt * 1000));
        }
    } catch (fetchError) {
        lastError = fetchError.message;
        if (attempt < 3) {
            await new Promise(r => setTimeout(r, attempt * 1000));
        }
    }
}

if (!response || !response.ok) {
    throw new Error(`Failed after 3 attempts: ${lastError}`);
}
```

### **F. Error UI (ZORUNLU)**

```javascript
showMapError(message) {
    const mapEl = document.getElementById('map');
    if (!mapEl) return;
    
    mapEl.innerHTML = `
        <div class="flex items-center justify-center h-full min-h-[400px] 
                    bg-red-50 dark:bg-red-900/20 
                    border-2 border-red-300 dark:border-red-700 
                    rounded-lg">
            <div class="text-center p-6">
                <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <h3 class="text-lg font-bold text-red-800 dark:text-red-200 mb-2">
                    Harita Yüklenemedi
                </h3>
                <p class="text-sm text-red-600 dark:text-red-400 mb-4">
                    ${message}
                </p>
                <button onclick="location.reload()" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg 
                               hover:bg-red-700 transition-all duration-200 
                               shadow-md hover:shadow-lg">
                    Sayfayı Yenile
                </button>
            </div>
        </div>
    `;
    
    window.toast?.error('Harita yüklenemedi');
}
```

### **G. Loading States (ÖNERİLİR)**

```javascript
// Button loading state
button.disabled = true;
button.classList.add('opacity-50', 'cursor-wait', 'animate-pulse');
button.innerHTML = button.innerHTML.replace('📍', '⏳');

// Success/Error sonrası restore
button.disabled = false;
button.classList.remove('opacity-50', 'cursor-wait', 'animate-pulse');
button.innerHTML = button.innerHTML.replace('⏳', '📍');
```

---

## 🚫 YASAK PATTERN'LER

### **1. setTimeout Without Timeout**
```javascript
// ❌ YASAK
setTimeout(() => this.initMap(), 1000);

// ✅ DOĞRU
await this.waitForLeaflet(); // Promise + timeout
```

### **2. Synchronous API Calls**
```javascript
// ❌ YASAK
fetch(url); // Fire and forget

// ✅ DOĞRU
try {
    const response = await fetch(url);
    if (!response.ok) throw new Error();
} catch (error) {
    // Handle error
}
```

### **3. No Rate Limiting**
```javascript
// ❌ YASAK (Nominatim ban yer!)
for (let i = 0; i < 10; i++) {
    await fetch(nominatimUrl);
}

// ✅ DOĞRU
for (let i = 0; i < 10; i++) {
    await this.reverseGeocode(lat, lng); // Rate limiting var
}
```

### **4. Static Markers**
```javascript
// ❌ YASAK
L.marker([lat, lng]).addTo(map);

// ✅ DOĞRU
L.marker([lat, lng], { 
    draggable: true,
    autoPan: true 
}).addTo(map).on('dragend', handler);
```

---

## 📊 ENFORCEMENT

### **Yalıhan Bekçi Auto-Check**
```yaml
Harita sayfası tespit edilince:
  ✅ Promise-based loading var mı?
  ✅ Error handling var mı?
  ✅ Marker draggable mı?
  ✅ Rate limiting var mı?
  ✅ Retry logic var mı?
  ❌ Yoksa → Warning + Suggestion
```

### **Pre-Commit Hook**
```bash
# Harita içeren dosyalarda check:
grep -r "L.marker" | grep -v "draggable: true" → WARNING
grep -r "nominatim" | grep -v "lastGeocodeCall" → ERROR
grep -r "initMap" | grep -v "async" → WARNING
```

---

## 🎓 EĞİTİM MATERYALI

### **Yeni Geliştirici İçin Checklist**
- [ ] Promise-based loading kullan
- [ ] Error handling ekle (try-catch)
- [ ] Marker'ı draggable yap
- [ ] Rate limiting ekle (Nominatim)
- [ ] Retry logic ekle (3x)
- [ ] Loading states ekle
- [ ] Toast feedback ekle
- [ ] Debug tool ekle (window.mapStatus)

### **Code Review Checklist**
- [ ] `async initMap()` var mı?
- [ ] `waitForLeaflet()` Promise kullanılmış mı?
- [ ] `showMapError()` fonksiyonu var mı?
- [ ] Marker `draggable: true` mı?
- [ ] Nominatim `lastGeocodeCall` check var mı?
- [ ] Retry loop 3x mı?
- [ ] User-friendly error messages var mı?
- [ ] Loading button states var mı?

---

## 📚 REFERANSLAR

### **Dokümantasyon**
- `.context7/HARITA_SISTEMI_STANDARDS.md` - Harita sistemi standartları
- `yalihan-bekci/knowledge/harita-araclari-iyilestirme-2025-11-05.json` - Bu dosya
- `public/test-harita-tools.html` - Test sayfası

### **Code Locations**
- `resources/views/admin/ilanlar/create.blade.php` - Ana implementasyon
- `resources/views/admin/ilanlar/components/location-map.blade.php` - Harita component

### **External APIs**
- Nominatim: https://nominatim.org/release-docs/develop/api/Reverse/
- Leaflet: https://leafletjs.com/reference.html
- Leaflet.draw: https://leaflet.github.io/Leaflet.draw/docs/leaflet-draw-latest.html

---

## ⚡ HIZLI REFERANS

```javascript
// ✅ STANDART HARITA INIT
async initMap() {
    await this.waitForLeaflet();
    this.map = L.map('map').setView([lat, lng], zoom);
    this.addLayers();
    this.attachEvents();
    this.loadExistingCoordinates();
}

// ✅ STANDART MARKER PLACEMENT
setMarker(lat, lng, skipGeocode = false) {
    this.marker = L.marker([lat, lng], { 
        draggable: true,
        autoPan: true 
    }).addTo(this.map);
    
    this.marker.on('dragend', (e) => {
        this.updateCoordinates(e.target.getLatLng());
        this.reverseGeocode(...);
    });
    
    this.marker.bindPopup(template);
}

// ✅ STANDART REVERSE GEOCODING
async reverseGeocode(lat, lng) {
    // Rate limiting (1 req/sec)
    await this.waitForRateLimit();
    this.lastGeocodeCall = Date.now();
    
    // Retry logic (3x)
    for (let attempt = 1; attempt <= 3; attempt++) {
        try {
            const response = await fetch(url, { headers: {...} });
            if (response.ok) break;
            if (attempt < 3) await sleep(attempt * 1000);
        } catch (error) {
            if (attempt === 3) throw error;
        }
    }
    
    // Process response...
}
```

---

## 🎯 SONUÇ

Bu standartlar **ZORUNLU**dur ve tüm harita kullanan sayfalarda uygulanmalıdır:
- `admin/ilanlar/create`
- `admin/ilanlar/edit`
- `admin/kisiler/create`
- `admin/kisiler/edit`
- `admin/sites/create`

**Compliance Check:**
```bash
php artisan context7:check --module=maps
```

**Test:**
```
http://127.0.0.1:8000/test-harita-tools.html
```

---

**Son Güncelleme:** 5 Kasım 2025  
**Yalıhan Bekçi Öğrenme ID:** harita-araclari-iyilestirme-2025-11-05  
**Context7 Version:** 5.2.0  
**Enforcement:** STRICT

