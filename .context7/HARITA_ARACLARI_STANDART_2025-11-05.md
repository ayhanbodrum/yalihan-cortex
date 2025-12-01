# 🗺️ Context7 - Harita Araçları Standart Kuralları

**Tarih:** 5 Kasım 2025  
**Versiyon:** Context7 v5.2.0  
**Durum:** AKTIF - ZORUNLU  
**Authority:** `.context7/authority.json`  
**Enforcement:** STRICT

---

## 🎯 KAPSAM

Bu standartlar **TÜM** harita kullanan sayfalarda ZORUNLUDUR:
- ✅ `admin/ilanlar/create` ve `edit`
- ✅ `admin/kisiler/create` ve `edit`
- ✅ `admin/sites/create` ve `edit`
- ✅ Harita içeren tüm custom sayfalar

---

## 📋 ZORUNLU STANDARTLAR

### **1. Promise-Based Loading (MANDATORY)**

```javascript
// ✅ DOĞRU
async initMap() {
    try {
        await this.waitForLeaflet();
        this.map = L.map('map').setView([lat, lng], zoom);
        // ...
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

**Kural:** Her harita init async/await kullanmalı, 10 saniye timeout ZORUNLU.

---

### **2. Draggable Markers (MANDATORY)**

```javascript
// ❌ YASAK
L.marker([lat, lng]).addTo(map);

// ✅ DOĞRU
L.marker([lat, lng], {
    draggable: true,
    autoPan: true,
    title: 'Sürükleyerek değiştirin'
}).addTo(map).on('dragend', (e) => {
    const pos = e.target.getLatLng();
    updateCoordinates(pos.lat, pos.lng);
    reverseGeocode(pos.lat, pos.lng);
});
```

**Kural:** Her marker sürüklenebilir olmalı, dragend event handler ZORUNLU.

---

### **3. Bidirectional Coordinate Sync (MANDATORY)**

```javascript
// Input → Map
input.addEventListener('blur', () => {
    const lat = parseFloat(enlemInput.value);
    const lng = parseFloat(boylamInput.value);
    if (!isNaN(lat) && !isNaN(lng)) {
        setMarker(lat, lng, true); // skipReverseGeocode
        map.setView([lat, lng], 15);
    }
});

// Map → Input (harita tıklama)
map.on('click', (e) => {
    document.getElementById('enlem').value = e.latlng.lat.toFixed(7);
    document.getElementById('boylam').value = e.latlng.lng.toFixed(7);
});

// Marker → Input (marker sürükleme)
marker.on('dragend', (e) => {
    const pos = e.target.getLatLng();
    document.getElementById('enlem').value = pos.lat.toFixed(7);
    document.getElementById('boylam').value = pos.lng.toFixed(7);
});
```

**Kural:** Koordinat değişimi her iki yönde de sync olmalı.

---

### **4. Nominatim Rate Limiting (MANDATORY)**

```javascript
// ✅ ZORUNLU
async reverseGeocode(lat, lng) {
    // Rate limiting (1 req/sec)
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

**Kural:** Nominatim API çağrılarında 1 saniye minimum interval ZORUNLU.

---

### **5. Retry Logic (MANDATORY)**

```javascript
// ✅ 3 ATTEMPT RETRY
let response;
let lastError;

for (let attempt = 1; attempt <= 3; attempt++) {
    try {
        response = await fetch(url, {
            headers: { 'User-Agent': 'YalihanEmlak/1.0' }
        });

        if (response.ok) break;
        
        lastError = `HTTP ${response.status}`;
        
        if (attempt < 3) {
            const backoff = attempt * 1000; // 1s, 2s
            await new Promise(r => setTimeout(r, backoff));
        }
    } catch (error) {
        lastError = error.message;
        if (attempt < 3) {
            await new Promise(r => setTimeout(r, attempt * 1000));
        }
    }
}

if (!response || !response.ok) {
    throw new Error(`Failed after 3 attempts: ${lastError}`);
}
```

**Kural:** External API'lerde 3x retry ZORUNLU, exponential backoff ÖNERİLİR.

---

### **6. Error Handling & Fallback UI (MANDATORY)**

```javascript
showMapError(message) {
    const mapEl = document.getElementById('map');
    mapEl.innerHTML = `
        <div class="flex items-center justify-center h-full min-h-[400px] 
                    bg-red-50 dark:bg-red-900/20 
                    border border-red-300 dark:border-red-700 rounded-lg">
            <div class="text-center p-6">
                <svg class="w-16 h-16 text-red-500 mx-auto mb-4">...</svg>
                <h3 class="text-lg font-medium text-red-800 dark:text-red-200 mb-2">
                    Harita Yüklenemedi
                </h3>
                <p class="text-sm text-red-600 dark:text-red-400 mb-4">
                    ${message}
                </p>
                <button onclick="location.reload()" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg 
                               hover:bg-red-700 transition-all duration-200">
                    Sayfayı Yenile
                </button>
            </div>
        </div>
    `;
}
```

**Kural:** Harita init fail → Fallback UI + action button ZORUNLU.

---

### **7. Loading States (RECOMMENDED)**

```javascript
// Button loading
button.disabled = true;
button.classList.add('opacity-50', 'cursor-wait', 'animate-pulse');

// Success/Error restore
button.disabled = false;
button.classList.remove('opacity-50', 'cursor-wait', 'animate-pulse');
```

**Kural:** Async işlemlerde button loading state ÖNERİLİR.

---

## 🚫 YASAK PATTERN'LER

```yaml
❌ setTimeout without Promise
❌ Static markers (draggable: false)
❌ Nominatim without rate limiting
❌ fetch() without try-catch
❌ API call without retry
❌ Error without user feedback
❌ Async without loading state
```

---

## 📊 COMPLIANCE CHECK

```bash
# Artisan command
php artisan context7:check --module=maps

# Manuel check
grep -r "L.marker" resources/views | grep -v "draggable: true"
grep -r "nominatim" resources/views | grep -v "lastGeocodeCall"
grep -r "initMap" resources/views | grep -v "async"
```

---

## 🧪 TEST

**Test Sayfası:** `public/test-harita-tools.html`  
**URL:** `http://127.0.0.1:8000/test-harita-tools.html`  

**Test Senaryoları:**
1. Harita yükleme (success/fail)
2. Koordinat sync (input/map/marker)
3. Marker sürükleme
4. GPS konum
5. Reverse geocoding
6. Mesafe ölçüm
7. Sınır çizme

---

## 📚 CONTEXT7 ENTEGRASYONU

Bu standartlar `.context7/authority.json` dosyasına eklendi:

```json
{
  "map_system_standards_2025_11_05": {
    "version": "2.0.0",
    "mandatory": [
      "promise_based_loading",
      "draggable_markers",
      "bidirectional_sync",
      "rate_limiting",
      "retry_logic",
      "error_handling"
    ],
    "recommended": [
      "loading_states",
      "toast_feedback",
      "debug_tools"
    ]
  }
}
```

---

**Son Güncelleme:** 5 Kasım 2025  
**Yalıhan Bekçi Status:** ✅ ÖĞRENME TAMAMLANDI  
**Enforcement:** STRICT  
**Next Review:** 2025-12-01

