# 🔍 Live Search ve TKGM Entegrasyonu - Enterprise Özellikler

## 🎯 İstenen Özellikler

### 1. **Kişi Aramaları Live Search**

### 2. **Site/Apartman Aramaları Live Search**

### 3. **Seçilen Kategoriye Göre Alt Kategori**

### 4. **Rakamları Yazıya Çevirme (Backend)**

### 5. **TKGM Parsel Sorgu Entegrasyonu**

### 6. **Ada/Parsel Seçimi Sonrası JSON Bilgi Döndürme**

## 🚀 Enterprise Seviye Implementation

### 1. **Live Search System**

#### **Frontend Implementation**

```javascript
// Live Search Component
class LiveSearchSystem {
    constructor() {
        this.debounceTimer = null;
        this.searchCache = new Map();
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Kişi arama
        document.getElementById('kisi-search').addEventListener('input', (e) => {
            this.debounceSearch('kisi', e.target.value);
        });

        // Site/Apartman arama
        document.getElementById('site-search').addEventListener('input', (e) => {
            this.debounceSearch('site', e.target.value);
        });

        // Kategori seçimi
        document.getElementById('kategori-select').addEventListener('change', (e) => {
            this.loadAltKategoriler(e.target.value);
        });
    }

    debounceSearch(type, query) {
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(() => {
            this.performSearch(type, query);
        }, 300);
    }

    async performSearch(type, query) {
        if (query.length < 2) return;

        // Cache kontrolü
        const cacheKey = `${type}-${query}`;
        if (this.searchCache.has(cacheKey)) {
            this.displayResults(this.searchCache.get(cacheKey));
            return;
        }

        try {
            const response = await fetch(`/api/live-search/${type}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ query }),
            });

            const data = await response.json();
            this.searchCache.set(cacheKey, data);
            this.displayResults(data);
        } catch (error) {
            console.error('Search error:', error);
        }
    }

    displayResults(results) {
        const container = document.getElementById('search-results');
        container.innerHTML = '';

        results.forEach((result) => {
            const item = document.createElement('div');
            item.className = 'search-result-item';
            item.innerHTML = `
                <div class="result-content">
                    <h4>${result.title}</h4>
                    <p>${result.description}</p>
                    <span class="result-type">${result.type}</span>
                </div>
            `;
            item.addEventListener('click', () => this.selectResult(result));
            container.appendChild(item);
        });
    }
}
```

#### **Backend API Implementation**

```php
// Live Search Controller
class LiveSearchController extends Controller
{
    public function searchKisi(Request $request)
    {
        $query = $request->input('query');

        $results = DB::table('kisiler')
            ->where(function($q) use ($query) {
                $q->where('ad_soyad', 'LIKE', "%{$query}%")
                  ->orWhere('telefon', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->where('status', 'active')
            ->limit(10)
            ->get()
            ->map(function($kisi) {
                return [
                    'id' => $kisi->id,
                    'title' => $kisi->ad_soyad,
                    'description' => $kisi->telefon . ' - ' . $kisi->email,
                    'type' => 'Kişi',
                    'data' => $kisi
                ];
            });

        return response()->json($results);
    }

    public function searchSite(Request $request)
    {
        $query = $request->input('query');

        $results = DB::table('sites')
            ->where(function($q) use ($query) {
                $q->where('site_adi', 'LIKE', "%{$query}%")
                  ->orWhere('apartman_adi', 'LIKE', "%{$query}%")
                  ->orWhere('adres', 'LIKE', "%{$query}%");
            })
            ->where('status', 'active')
            ->limit(10)
            ->get()
            ->map(function($site) {
                return [
                    'id' => $site->id,
                    'title' => $site->site_adi ?? $site->apartman_adi,
                    'description' => $site->adres,
                    'type' => 'Site/Apartman',
                    'data' => $site
                ];
            });

        return response()->json($results);
    }
}
```

### 2. **Kategori-Alt Kategori Sistemi**

#### **Dynamic Category Loading**

```javascript
// Kategori Yönetimi
class CategoryManager {
    async loadAltKategoriler(anaKategoriId) {
        try {
            const response = await fetch(`/api/kategoriler/${anaKategoriId}/alt-kategoriler`);
            const altKategoriler = await response.json();

            const select = document.getElementById('alt-kategori-select');
            select.innerHTML = '<option value="">Alt Kategori Seçin</option>';

            altKategoriler.data.forEach((kategori) => {
                const option = document.createElement('option');
                option.value = kategori.id;
                option.textContent = kategori.name;
                select.appendChild(option);
            });

            // Alt kategori seçimi sonrası yayın tiplerini yükle
            select.addEventListener('change', (e) => {
                this.loadYayinTipleri(e.target.value);
            });
        } catch (error) {
            console.error('Alt kategori yükleme hatası:', error);
        }
    }

    async loadYayinTipleri(altKategoriId) {
        try {
            const response = await fetch(`/api/alt-kategoriler/${altKategoriId}/yayin-tipleri`);
            const yayinTipleri = await response.json();

            const select = document.getElementById('yayin-tipi-select');
            select.innerHTML = '<option value="">Yayın Tipi Seçin</option>';

            yayinTipleri.data.forEach((tip) => {
                const option = document.createElement('option');
                option.value = tip.id;
                option.textContent = tip.name;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Yayın tipi yükleme hatası:', error);
        }
    }
}
```

### 3. **Rakamları Yazıya Çevirme (Backend)**

#### **Number to Text Converter**

```php
// Number to Text Converter Service
class NumberToTextConverter
{
    private $ones = [
        0 => '', 1 => 'bir', 2 => 'iki', 3 => 'üç', 4 => 'dört',
        5 => 'beş', 6 => 'altı', 7 => 'yedi', 8 => 'sekiz', 9 => 'dokuz'
    ];

    private $tens = [
        10 => 'on', 20 => 'yirmi', 30 => 'otuz', 40 => 'kırk',
        50 => 'elli', 60 => 'altmış', 70 => 'yetmiş', 80 => 'seksen', 90 => 'doksan'
    ];

    private $hundreds = [
        100 => 'yüz', 200 => 'iki yüz', 300 => 'üç yüz', 400 => 'dört yüz',
        500 => 'beş yüz', 600 => 'altı yüz', 700 => 'yedi yüz', 800 => 'sekiz yüz', 900 => 'dokuz yüz'
    ];

    public function convertToText($number)
    {
        if ($number == 0) return 'sıfır';

        $text = '';
        $number = (int) $number;

        // Milyonlar
        if ($number >= 1000000) {
            $millions = intval($number / 1000000);
            $text .= $this->convertHundreds($millions) . ' milyon ';
            $number %= 1000000;
        }

        // Binler
        if ($number >= 1000) {
            $thousands = intval($number / 1000);
            if ($thousands == 1) {
                $text .= 'bin ';
            } else {
                $text .= $this->convertHundreds($thousands) . ' bin ';
            }
            $number %= 1000;
        }

        // Yüzler, onlar, birler
        if ($number > 0) {
            $text .= $this->convertHundreds($number);
        }

        return trim($text);
    }

    private function convertHundreds($number)
    {
        $text = '';

        // Yüzler
        if ($number >= 100) {
            $hundred = intval($number / 100) * 100;
            $text .= $this->hundreds[$hundred] . ' ';
            $number %= 100;
        }

        // Onlar
        if ($number >= 10) {
            $ten = intval($number / 10) * 10;
            $text .= $this->tens[$ten] . ' ';
            $number %= 10;
        }

        // Birler
        if ($number > 0) {
            $text .= $this->ones[$number];
        }

        return trim($text);
    }

    public function convertPriceToText($price, $currency = 'TRY')
    {
        $text = $this->convertToText($price);

        switch ($currency) {
            case 'TRY':
                return $text . ' Türk Lirası';
            case 'USD':
                return $text . ' Amerikan Doları';
            case 'EUR':
                return $text . ' Euro';
            default:
                return $text . ' ' . $currency;
        }
    }
}

// Usage in Controller
class PropertyController extends Controller
{
    public function createProperty(Request $request)
    {
        $converter = new NumberToTextConverter();

        $property = Property::create([
            'title' => $request->title,
            'price' => $request->price,
            'price_text' => $converter->convertPriceToText($request->price, $request->currency),
            // ... other fields
        ]);

        return response()->json([
            'success' => true,
            'property' => $property,
            'price_text' => $property->price_text
        ]);
    }
}
```

### 4. **TKGM Parsel Sorgu Entegrasyonu**

#### **TKGM API Integration**

```php
// TKGM Parsel Sorgu Service
class TKGMService
{
    private $baseUrl = 'https://parselsorgu.tkgm.gov.tr/';

    public function searchParcel($ada, $parsel)
    {
        try {
            // TKGM API'ye istek gönder
            $response = Http::timeout(30)->get($this->baseUrl . 'api/parsel-sorgu', [
                'ada' => $ada,
                'parsel' => $parsel
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $this->formatParcelData($data);
            }

            throw new Exception('TKGM API hatası: ' . $response->status());

        } catch (Exception $e) {
            Log::error('TKGM Parsel Sorgu Hatası: ' . $e->getMessage());
            return null;
        }
    }

    private function formatParcelData($data)
    {
        return [
            'ada' => $data['ada'] ?? null,
            'parsel' => $data['parsel'] ?? null,
            'il' => $data['il'] ?? null,
            'ilce' => $data['ilce'] ?? null,
            'mahalle' => $data['mahalle'] ?? null,
            'yol' => $data['yol'] ?? null,
            'bina_no' => $data['bina_no'] ?? null,
            'daire_no' => $data['daire_no'] ?? null,
            'alan' => $data['alan'] ?? null,
            'nitelik' => $data['nitelik'] ?? null,
            'tapu_durumu' => $data['tapu_durumu'] ?? null,
            'coordinates' => [
                'lat' => $data['lat'] ?? null,
                'lng' => $data['lng'] ?? null
            ],
            'raw_data' => $data
        ];
    }

    public function generateParcelJson($ada, $parsel)
    {
        $parcelData = $this->searchParcel($ada, $parsel);

        if (!$parcelData) {
            return null;
        }

        $filename = "tkgm-parsel-sorgu-sonuc-{$ada}-ada-{$parsel}-parsel.json";
        $filepath = storage_path('app/parcel-data/' . $filename);

        // Dizin oluştur
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        // JSON dosyası oluştur
        file_put_contents($filepath, json_encode($parcelData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return [
            'filename' => $filename,
            'filepath' => $filepath,
            'data' => $parcelData,
            'download_url' => url('storage/parcel-data/' . $filename)
        ];
    }
}

// Controller Implementation
class ParcelController extends Controller
{
    public function searchParcel(Request $request)
    {
        $request->validate([
            'ada' => 'required|integer|min:1',
            'parsel' => 'required|integer|min:1'
        ]);

        $tkgmService = new TKGMService();
        $result = $tkgmService->generateParcelJson($request->ada, $request->parsel);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Parsel bilgileri alınamadı'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Parsel bilgileri başarıyla alındı',
            'data' => $result['data'],
            'filename' => $result['filename'],
            'download_url' => $result['download_url']
        ]);
    }
}
```

### 5. **Frontend Parsel Sorgu Interface**

#### **Parcel Search Component**

```javascript
// Parsel Sorgu Component
class ParcelSearchComponent {
    constructor() {
        this.setupEventListeners();
    }

    setupEventListeners() {
        document.getElementById('parcel-search-form').addEventListener('submit', (e) => {
            e.preventDefault();
            this.searchParcel();
        });
    }

    async searchParcel() {
        const ada = document.getElementById('ada-input').value;
        const parsel = document.getElementById('parsel-input').value;

        if (!ada || !parsel) {
            this.showError('Ada ve parsel numarası gerekli');
            return;
        }

        this.showLoading();

        try {
            const response = await fetch('/api/parcel/search', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ ada, parsel }),
            });

            const result = await response.json();

            if (result.success) {
                this.displayParcelData(result.data);
                this.showDownloadLink(result.download_url, result.filename);
            } else {
                this.showError(result.message);
            }
        } catch (error) {
            this.showError('Parsel sorgu hatası: ' + error.message);
        } finally {
            this.hideLoading();
        }
    }

    displayParcelData(data) {
        const container = document.getElementById('parcel-results');
        container.innerHTML = `
            <div class="parcel-info">
                <h3>Parsel Bilgileri</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Ada:</label>
                        <span>${data.ada}</span>
                    </div>
                    <div class="info-item">
                        <label>Parsel:</label>
                        <span>${data.parsel}</span>
                    </div>
                    <div class="info-item">
                        <label>İl:</label>
                        <span>${data.il}</span>
                    </div>
                    <div class="info-item">
                        <label>İlçe:</label>
                        <span>${data.ilce}</span>
                    </div>
                    <div class="info-item">
                        <label>Mahalle:</label>
                        <span>${data.mahalle}</span>
                    </div>
                    <div class="info-item">
                        <label>Alan:</label>
                        <span>${data.alan}</span>
                    </div>
                    <div class="info-item">
                        <label>Nitelik:</label>
                        <span>${data.nitelik}</span>
                    </div>
                    <div class="info-item">
                        <label>Tapu Durumu:</label>
                        <span>${data.tapu_durumu}</span>
                    </div>
                </div>
            </div>
        `;
    }

    showDownloadLink(url, filename) {
        const container = document.getElementById('download-section');
        container.innerHTML = `
            <div class="download-info">
                <h4>JSON Dosyası Hazır</h4>
                <p>Parsel bilgileri JSON formatında hazırlandı.</p>
                <a href="${url}" download="${filename}" class="btn btn-primary">
                    <i class="fas fa-download"></i> ${filename} İndir
                </a>
            </div>
        `;
    }

    showLoading() {
        document.getElementById('loading').style.display = 'block';
    }

    hideLoading() {
        document.getElementById('loading').style.display = 'none';
    }

    showError(message) {
        const container = document.getElementById('error-message');
        container.innerHTML = `<div class="alert alert-danger">${message}</div>`;
    }
}
```

### 6. **API Routes**

#### **Route Definitions**

```php
// routes/api.php
Route::prefix('api')->group(function () {
    // Live Search Routes
    Route::post('/live-search/kisi', [LiveSearchController::class, 'searchKisi']);
    Route::post('/live-search/site', [LiveSearchController::class, 'searchSite']);

    // Category Routes
    Route::get('/kategoriler/{id}/alt-kategoriler', [CategoryController::class, 'getAltKategoriler']);
    Route::get('/alt-kategoriler/{id}/yayin-tipleri', [CategoryController::class, 'getYayinTipleri']);

    // Parcel Search Routes
    Route::post('/parcel/search', [ParcelController::class, 'searchParcel']);
    Route::get('/parcel/download/{filename}', [ParcelController::class, 'downloadParcelJson']);

    // Number to Text Routes
    Route::post('/convert/number-to-text', [NumberController::class, 'convertToText']);
    Route::post('/convert/price-to-text', [NumberController::class, 'convertPriceToText']);
});
```

## 🎯 Enterprise Features

### 1. **Real-time Search Suggestions**

- **Elasticsearch integration**
- **Fuzzy search capabilities**
- **Search analytics**
- **Personalized suggestions**

### 2. **Advanced Parsel Management**

- **Batch parcel processing**
- **Parcel history tracking**
- **Automated updates**
- **Integration with mapping services**

### 3. **Performance Optimization**

- **Redis caching**
- **Database indexing**
- **CDN integration**
- **Lazy loading**

### 4. **Security & Compliance**

- **API rate limiting**
- **Data encryption**
- **Audit logging**
- **GDPR compliance**

## 📊 Success Metrics

### Technical Metrics

- **Search Response Time**: <200ms
- **API Uptime**: 99.9%
- **Cache Hit Rate**: 95%+
- **Data Accuracy**: 99%+

### Business Metrics

- **User Engagement**: +250%
- **Search Success Rate**: 90%+
- **Feature Adoption**: 80%+
- **Customer Satisfaction**: 95%+

---

**Tarih**: 2025-01-30  
**Durum**: Planning Phase  
**Sonraki Adım**: Technical Implementation
