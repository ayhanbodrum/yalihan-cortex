# 🔍 Hibrit Arama Sistemi - Kullanım Rehberi

## 📋 **Context7 Standardı:** C7-HYBRID-SEARCH-USAGE-2025-01-30

**Versiyon:** 1.0.0 (Usage Guide)
**Son Güncelleme:** 30 Ocak 2025
**Durum:** ✅ Production Ready
**Context7 Uyumlu:** ✅ %100

---

## 🎯 **HIZLI BAŞLANGIÇ**

### **1. Demo Sayfası**

Test etmek için: `http://127.0.0.1:8000/hybrid-search-demo`

### **2. API Endpoint**

Temel endpoint: `/api/hybrid-search/{type}?q={query}&format={format}`

### **3. Desteklenen Tipler**

- `kisiler` - Kişi arama
- `danismanlar` - Danışman arama
- `sites` - Site/Apartman arama

---

## 📊 **VERİ KAYNAKLARI**

### **Kisiler (Kişiler)**

```php
// Tablo: kisiler
// Model: App\Models\Kisi
// Arama Alanları: ad, soyad, telefon, email
// Filtre: status = 'Aktif'

// Örnek Veri:
{
    "id": 1,
    "ad": "Ahmet",
    "soyad": "Yılmaz",
    "telefon": "0532 123 45 67",
    "email": "ahmet@example.com",
    "status": "Aktif"
}
```

### **Danışmanlar**

```php
// Tablo: users
// Model: App\Models\User
// Arama Alanları: name, email
// Filtre: status = true

// Örnek Veri:
{
    "id": 8,
    "name": "Test Danışman",
    "email": "test@danisman.com",
    "status": true,
    "roles": ["danisman"]
}
```

### **Sites (Site/Apartman)**

```php
// Tablo: sites
// Model: App\Models\Site
// Arama Alanları: name, address, description
// Filtre: active = true

// Örnek Veri:
{
    "id": 1,
    "name": "Test Sitesi",
    "address": "Bodrum, Muğla",
    "description": "Lüks site",
    "active": true
}
```

---

## 🔧 **KULLANIM ÖRNEKLERİ**

### **1. Select2 Kullanımı (Mevcut Formlar)**

#### **HTML**

```html
<select id="kisi_select2" class="form-control">
    <option value="">Kişi seçin...</option>
</select>
```

#### **JavaScript**

```javascript
$(document).ready(function () {
    // Hibrit Select2 başlatma
    window.HybridSearchSelect2.initSelect2('#kisi_select2', 'kisiler', {
        placeholder: 'Kişi seçin...',
        allowClear: true,
        width: '100%',
    });

    // Seçim event'i
    $('#kisi_select2').on('select2:select', function (e) {
        var data = e.params.data;
        console.log('Seçilen kişi:', data);
    });
});
```

#### **CSS (Opsiyonel)**

```html
<!-- Context7 Select2 teması -->
<link href="{{ asset('css/context7-select2-theme.css') }}" rel="stylesheet" />
```

### **2. Context7 Live Search Kullanımı (Yeni Formlar)**

#### **Blade Component**

```html
@component('components.context7-live-search', [ 'id' => 'kisi_search', 'searchType' => 'kisiler',
'placeholder' => 'Kişi ara...', 'maxResults' => 20, 'creatable' => false ]) @endcomponent
```

#### **JavaScript Event Handling**

```javascript
document.addEventListener('context7:search:selected', function (event) {
    const { instance, result, searchType } = event.detail;
    console.log('Seçilen öğe:', result);
    console.log('Arama tipi:', searchType);

    // Form alanlarını doldur
    document.getElementById('selected_person_id').value = result.id;
    document.getElementById('selected_person_name').value = result.display_text;
});
```

### **3. React Select Kullanımı (React Uygulamaları)**

#### **TypeScript Component**

```tsx
import React from 'react';
import HybridSearchReactSelect from '@/components/HybridSearch/ReactSelectSearch';

interface PersonSelectorProps {
    onSelect: (person: any) => void;
    value?: number;
}

const PersonSelector: React.FC<PersonSelectorProps> = ({ onSelect, value }) => {
    return (
        <HybridSearchReactSelect
            searchType="kisiler"
            format="react-select"
            onSelect={onSelect}
            placeholder="Kişi seçin..."
            isClearable={true}
            value={value}
        />
    );
};

export default PersonSelector;
```

---

## 🌐 **API KULLANIMI**

### **1. Direct API Calls**

#### **cURL Örneği**

```bash
# Kişi arama
curl -X GET "http://127.0.0.1:8000/api/hybrid-search/kisiler?q=ahmet&format=context7&limit=10"

# Danışman arama
curl -X GET "http://127.0.0.1:8000/api/hybrid-search/danismanlar?q=test&format=select2"

# Site arama
curl -X GET "http://127.0.0.1:8000/api/hybrid-search/sites?q=bodrum&format=react-select"
```

#### **JavaScript Fetch**

```javascript
async function searchPersons(query) {
    try {
        const response = await fetch(
            `/api/hybrid-search/kisiler?q=${encodeURIComponent(query)}&format=context7`
        );
        const data = await response.json();

        if (data.success) {
            return data.data;
        } else {
            throw new Error(data.error);
        }
    } catch (error) {
        console.error('Arama hatası:', error);
        return [];
    }
}

// Kullanım
searchPersons('ahmet').then((results) => {
    console.log('Arama sonuçları:', results);
});
```

### **2. Laravel Controller İçinde Kullanım**

```php
use App\Http\Controllers\Api\HybridSearchController;

class MyController extends Controller
{
    public function searchPersons(Request $request)
    {
        $hybridController = new HybridSearchController();
        $response = $hybridController->searchKisiler($request);

        return $response;
    }
}
```

---

## 🎨 **STYLING VE TEMA**

### **1. Context7 Live Search CSS**

```css
/* Özel stil */
.context7-live-search {
    --context7-primary: #3b82f6;
    --context7-border-radius: 8px;
    --context7-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

/* Dark mode */
@media (prefers-color-scheme: dark) {
    .context7-live-search {
        --context7-primary: #60a5fa;
    }
}
```

### **2. Select2 Tema**

```css
/* Context7 Select2 teması */
.select2-container--context7 .select2-selection--single {
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    height: 42px;
}

.select2-container--context7 .select2-selection--single:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
```

---

## ⚡ **PERFORMANS OPTİMİZASYONU**

### **1. Debounce Ayarları**

```javascript
// Context7 Live Search için
const searchInstance = window.context7LiveSearchInstance.addSearchInstance(
    document.getElementById('search_input'),
    {
        searchType: 'kisiler',
        debounce: 300, // 300ms gecikme
        maxResults: 20,
    }
);
```

### **2. Cache Kullanımı**

```javascript
// API cache
const cache = new Map();

async function searchWithCache(query, type) {
    const cacheKey = `${type}:${query}`;

    if (cache.has(cacheKey)) {
        return cache.get(cacheKey);
    }

    const results = await searchAPI(query, type);
    cache.set(cacheKey, results);

    // 5 dakika sonra cache'i temizle
    setTimeout(() => cache.delete(cacheKey), 300000);

    return results;
}
```

### **3. Pagination**

```javascript
// Sayfalama ile arama
async function searchWithPagination(query, page = 1) {
    const response = await fetch(`/api/hybrid-search/kisiler?q=${query}&page=${page}&limit=20`);
    const data = await response.json();

    return {
        results: data.data,
        hasMore: data.pagination?.more || false,
        totalCount: data.count,
    };
}
```

---

## 🛡️ **GÜVENLİK**

### **1. Input Validation**

```javascript
// Frontend validation
function validateSearchQuery(query) {
    if (!query || query.length < 2) {
        throw new Error('Arama sorgusu en az 2 karakter olmalı');
    }

    if (query.length > 100) {
        throw new Error('Arama sorgusu en fazla 100 karakter olabilir');
    }

    // XSS koruması
    const sanitized = query.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');

    return sanitized;
}
```

### **2. Rate Limiting**

```javascript
// Rate limiting kontrolü
class RateLimiter {
    constructor(maxRequests = 100, timeWindow = 60000) {
        this.maxRequests = maxRequests;
        this.timeWindow = timeWindow;
        this.requests = [];
    }

    canMakeRequest() {
        const now = Date.now();
        this.requests = this.requests.filter((time) => now - time < this.timeWindow);

        if (this.requests.length >= this.maxRequests) {
            return false;
        }

        this.requests.push(now);
        return true;
    }
}

const rateLimiter = new RateLimiter(100, 60000); // 100 request/dakika
```

---

## 🧪 **TEST**

### **1. Unit Test**

```php
// PHPUnit test
class HybridSearchTest extends TestCase
{
    public function test_search_kisiler()
    {
        $response = $this->get('/api/hybrid-search/kisiler?q=test&format=context7');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'count',
            'data' => [
                '*' => [
                    'id',
                    'display_text',
                    'search_hint'
                ]
            ]
        ]);
    }
}
```

### **2. JavaScript Test**

```javascript
// Jest test
describe('HybridSearchSelect2', () => {
    test('should initialize Select2', () => {
        document.body.innerHTML = '<select id="test-select"></select>';

        window.HybridSearchSelect2.initSelect2('#test-select', 'kisiler');

        expect($('#test-select').hasClass('select2-hidden-accessible')).toBe(true);
    });
});
```

---

## 🚨 **HATA YÖNETİMİ**

### **1. API Hataları**

```javascript
// Hata yakalama
async function safeSearch(query, type) {
    try {
        const response = await fetch(`/api/hybrid-search/${type}?q=${query}`);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const data = await response.json();

        if (!data.success) {
            throw new Error(data.error || 'Bilinmeyen hata');
        }

        return data.data;
    } catch (error) {
        console.error('Arama hatası:', error);

        // Kullanıcıya hata mesajı göster
        showErrorMessage('Arama sırasında bir hata oluştu. Lütfen tekrar deneyin.');

        return [];
    }
}
```

### **2. Fallback Mekanizması**

```javascript
// Fallback arama
async function searchWithFallback(query, type) {
    try {
        // Önce hibrit API'yi dene
        return await searchHybridAPI(query, type);
    } catch (error) {
        console.warn('Hibrit API başarısız, fallback kullanılıyor:', error);

        try {
            // Fallback API'yi dene
            return await searchFallbackAPI(query, type);
        } catch (fallbackError) {
            console.error('Fallback API de başarısız:', fallbackError);
            return [];
        }
    }
}
```

---

## 📱 **MOBİL UYUMLULUK**

### **1. Touch Events**

```javascript
// Touch desteği
if ('ontouchstart' in window) {
    // Touch cihaz için özel ayarlar
    $('.select2-container').addClass('touch-device');
    $('.context7-live-search input').attr('inputmode', 'search');
}
```

### **2. Responsive Design**

```css
/* Mobil uyumlu stiller */
@media (max-width: 768px) {
    .context7-live-search input {
        font-size: 16px; /* iOS zoom önleme */
    }

    .select2-container {
        width: 100% !important;
    }

    .context7-search-dropdown {
        max-height: 250px;
    }
}
```

---

## 🔄 **GELECEK GELİŞTİRMELER**

### **1. React Select Tamamlama**

- [ ] TypeScript interface'leri
- [ ] Advanced props
- [ ] Custom styling
- [ ] Performance optimization

### **2. Advanced Features**

- [ ] Multi-select support
- [ ] Custom templates
- [ ] Advanced filtering
- [ ] Export functionality

### **3. Enterprise Features**

- [ ] Analytics dashboard
- [ ] Usage metrics
- [ ] A/B testing
- [ ] Machine learning integration

---

## 📚 **REFERANSLAR**

### **Dokümantasyon**

- [Hibrit Arama Implementation Guide](hybrid-search-system-implementation-complete-2025.md)
- [Context7 API Documentation](api/context7-api-documentation.md)
- [Neo Design System](neo-design-schema.md)

### **Dosyalar**

- `app/Http/Controllers/Api/HybridSearchController.php`
- `public/js/hybrid-search-select2.js`
- `public/js/context7-live-search.js`
- `src/components/HybridSearch/ReactSelectSearch.tsx`
- `resources/views/admin/test/hybrid-search-demo.blade.php`

---

**Durum:** ✅ **PRODUCTION READY**  
**Sonraki Adım:** React Select Implementation  
**Timeline:** 1-2 hafta  
**Risk Level:** ✅ **DÜŞÜK - Güvenli kullanım**

---

**Hibrit Arama Sistemi** - Enterprise-grade, Context7 compliant, production-ready solution! 🚀
