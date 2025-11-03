# 🗺️ TKGM Parsel Sorgu Entegrasyonu - Implementation Guide

## 📊 Parsel Sorgu Sonucu Analizi

### **TKGM Parsel Bilgileri (Ada: 126, Parsel: 7)**
```json
{
  "features": [
    {
      "type": "Feature",
      "geometry": {
        "type": "Polygon",
        "coordinates": [
          [
            [27.27891, 37.0954],
            [27.27882, 37.09543],
            [27.2787, 37.09544],
            [27.27868, 37.09545],
            [27.27854, 37.09524],
            [27.27856, 37.09523],
            [27.27878, 37.0952],
            [27.27892, 37.09516],
            [27.27897, 37.09518],
            [27.27903, 37.09537],
            [27.27891, 37.0954]
          ]
        ]
      },
      "properties": {
        "ParselNo": "7",
        "Alan": "900,01",
        "Mevkii": "Kavak",
        "Nitelik": "Arsa",
        "Ada": "126",
        "Il": "Muğla",
        "Ilce": "Bodrum",
        "Pafta": "",
        "Mahalle": "Geriş"
      }
    }
  ],
  "type": "FeatureCollection",
  "crs": {
    "type": "name",
    "properties": {
      "name": "EPSG:4326"
    }
  }
}
```

## 🚀 Enterprise Implementation

### 1. **TKGM Service Enhancement**

#### **Enhanced TKGM Service**
```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TKGMService
{
    private $baseUrl = 'https://parselsorgu.tkgm.gov.tr/';
    
    public function searchParcel($ada, $parsel)
    {
        try {
            // TKGM API'ye istek gönder
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => 'YalihanEmlak/1.0',
                    'Accept' => 'application/json'
                ])
                ->get($this->baseUrl . 'api/parsel-sorgu', [
                    'ada' => $ada,
                    'parsel' => $parsel
                ]);
            
            if ($response->successful()) {
                $data = $response->json();
                return $this->processGeoJSONData($data);
            }
            
            throw new Exception('TKGM API hatası: ' . $response->status());
            
        } catch (Exception $e) {
            Log::error('TKGM Parsel Sorgu Hatası: ' . $e->getMessage());
            return null;
        }
    }
    
    private function processGeoJSONData($geoJsonData)
    {
        $feature = $geoJsonData['features'][0] ?? null;
        
        if (!$feature) {
            return null;
        }
        
        $properties = $feature['properties'];
        $geometry = $feature['geometry'];
        
        return [
            'ada' => $properties['Ada'] ?? null,
            'parsel' => $properties['ParselNo'] ?? null,
            'alan' => $properties['Alan'] ?? null,
            'nitelik' => $properties['Nitelik'] ?? null,
            'mevkii' => $properties['Mevkii'] ?? null,
            'il' => $properties['Il'] ?? null,
            'ilce' => $properties['Ilce'] ?? null,
            'mahalle' => $properties['Mahalle'] ?? null,
            'pafta' => $properties['Pafta'] ?? null,
            'geometry' => [
                'type' => $geometry['type'],
                'coordinates' => $geometry['coordinates'],
                'center' => $this->calculatePolygonCenter($geometry['coordinates'][0])
            ],
            'crs' => $geoJsonData['crs'] ?? null,
            'raw_data' => $geoJsonData
        ];
    }
    
    private function calculatePolygonCenter($coordinates)
    {
        $latSum = 0;
        $lngSum = 0;
        $count = count($coordinates);
        
        foreach ($coordinates as $coord) {
            $lngSum += $coord[0];
            $latSum += $coord[1];
        }
        
        return [
            'lat' => $latSum / $count,
            'lng' => $lngSum / $count
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
            'download_url' => url('storage/parcel-data/' . $filename),
            'map_url' => $this->generateMapUrl($parcelData)
        ];
    }
    
    private function generateMapUrl($parcelData)
    {
        $center = $parcelData['geometry']['center'];
        return "https://www.google.com/maps?q={$center['lat']},{$center['lng']}";
    }
}
```

### 2. **Database Schema for Parcel Data**

#### **Parcel Data Migration**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParcelDataTable extends Migration
{
    public function up()
    {
        Schema::create('parcel_data', function (Blueprint $table) {
            $table->id();
            $table->string('ada');
            $table->string('parsel');
            $table->string('alan')->nullable();
            $table->string('nitelik')->nullable();
            $table->string('mevkii')->nullable();
            $table->string('il');
            $table->string('ilce');
            $table->string('mahalle')->nullable();
            $table->string('pafta')->nullable();
            $table->json('geometry');
            $table->json('coordinates');
            $table->decimal('center_lat', 10, 8);
            $table->decimal('center_lng', 11, 8);
            $table->string('map_url')->nullable();
            $table->string('json_file')->nullable();
            $table->json('raw_data');
            $table->timestamps();
            
            $table->index(['ada', 'parsel']);
            $table->index(['il', 'ilce']);
            $table->index(['center_lat', 'center_lng']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('parcel_data');
    }
}
```

### 3. **Enhanced Controller**

#### **Parcel Controller with Database Storage**
```php
<?php

namespace App\Http\Controllers;

use App\Services\TKGMService;
use App\Models\ParcelData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ParcelController extends Controller
{
    private $tkgmService;
    
    public function __construct(TKGMService $tkgmService)
    {
        $this->tkgmService = $tkgmService;
    }
    
    public function searchParcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ada' => 'required|integer|min:1',
            'parsel' => 'required|integer|min:1'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $ada = $request->ada;
        $parsel = $request->parsel;
        
        // Önce veritabanında kontrol et
        $existingParcel = ParcelData::where('ada', $ada)
            ->where('parsel', $parsel)
            ->first();
        
        if ($existingParcel) {
            return response()->json([
                'success' => true,
                'message' => 'Parsel bilgileri veritabanından alındı',
                'data' => $existingParcel,
                'source' => 'database'
            ]);
        }
        
        // TKGM'den yeni veri çek
        $result = $this->tkgmService->generateParcelJson($ada, $parsel);
        
        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Parsel bilgileri alınamadı'
            ], 500);
        }
        
        // Veritabanına kaydet
        $parcelData = ParcelData::create([
            'ada' => $result['data']['ada'],
            'parsel' => $result['data']['parsel'],
            'alan' => $result['data']['alan'],
            'nitelik' => $result['data']['nitelik'],
            'mevkii' => $result['data']['mevkii'],
            'il' => $result['data']['il'],
            'ilce' => $result['data']['ilce'],
            'mahalle' => $result['data']['mahalle'],
            'pafta' => $result['data']['pafta'],
            'geometry' => $result['data']['geometry'],
            'coordinates' => $result['data']['geometry']['coordinates'],
            'center_lat' => $result['data']['geometry']['center']['lat'],
            'center_lng' => $result['data']['geometry']['center']['lng'],
            'map_url' => $result['map_url'],
            'json_file' => $result['filename'],
            'raw_data' => $result['data']['raw_data']
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Parsel bilgileri başarıyla alındı ve kaydedildi',
            'data' => $parcelData,
            'filename' => $result['filename'],
            'download_url' => $result['download_url'],
            'map_url' => $result['map_url'],
            'source' => 'tkgm'
        ]);
    }
    
    public function getParcelHistory($ada, $parsel)
    {
        $parcels = ParcelData::where('ada', $ada)
            ->where('parsel', $parsel)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $parcels
        ]);
    }
    
    public function downloadParcelJson($filename)
    {
        $filepath = storage_path('app/parcel-data/' . $filename);
        
        if (!file_exists($filepath)) {
            return response()->json([
                'success' => false,
                'message' => 'Dosya bulunamadı'
            ], 404);
        }
        
        return response()->download($filepath);
    }
}
```

### 4. **Frontend Integration**

#### **Enhanced Parcel Search Component**
```javascript
class EnhancedParcelSearchComponent {
    constructor() {
        this.setupEventListeners();
        this.initializeMap();
    }
    
    setupEventListeners() {
        document.getElementById('parcel-search-form').addEventListener('submit', (e) => {
            e.preventDefault();
            this.searchParcel();
        });
        
        // Auto-complete for ada/parsel
        document.getElementById('ada-input').addEventListener('input', (e) => {
            this.suggestAda(e.target.value);
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
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ ada, parsel })
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.displayParcelData(result.data);
                this.showMap(result.data);
                this.showDownloadOptions(result);
                this.showHistory(result.data.ada, result.data.parsel);
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
                        <label>Alan:</label>
                        <span>${data.alan} m²</span>
                    </div>
                    <div class="info-item">
                        <label>Nitelik:</label>
                        <span>${data.nitelik}</span>
                    </div>
                    <div class="info-item">
                        <label>Mevkii:</label>
                        <span>${data.mevkii}</span>
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
                        <label>Pafta:</label>
                        <span>${data.pafta || 'Belirtilmemiş'}</span>
                    </div>
                </div>
            </div>
        `;
    }
    
    showMap(data) {
        const mapContainer = document.getElementById('parcel-map');
        mapContainer.innerHTML = `
            <div class="map-container">
                <h4>Parsel Haritası</h4>
                <div id="map" style="height: 400px; width: 100%;"></div>
                <div class="map-actions">
                    <a href="${data.map_url}" target="_blank" class="btn btn-primary">
                        <i class="fas fa-external-link-alt"></i> Google Maps'te Aç
                    </a>
                </div>
            </div>
        `;
        
        // Initialize map with parcel coordinates
        this.initializeParcelMap(data);
    }
    
    initializeParcelMap(data) {
        const map = L.map('map').setView([data.center_lat, data.center_lng], 18);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        // Add parcel polygon
        const coordinates = data.coordinates[0].map(coord => [coord[1], coord[0]]);
        const polygon = L.polygon(coordinates, {
            color: 'red',
            fillColor: 'yellow',
            fillOpacity: 0.3
        }).addTo(map);
        
        // Add center marker
        L.marker([data.center_lat, data.center_lng])
            .addTo(map)
            .bindPopup(`
                <b>Ada: ${data.ada}, Parsel: ${data.parsel}</b><br>
                Alan: ${data.alan} m²<br>
                Nitelik: ${data.nitelik}
            `);
        
        map.fitBounds(polygon.getBounds());
    }
    
    showDownloadOptions(result) {
        const container = document.getElementById('download-section');
        container.innerHTML = `
            <div class="download-info">
                <h4>İndirme Seçenekleri</h4>
                <div class="download-buttons">
                    <a href="${result.download_url}" download="${result.filename}" class="btn btn-primary">
                        <i class="fas fa-download"></i> JSON Dosyası İndir
                    </a>
                    <button onclick="this.exportToExcel(${result.data.ada}, ${result.data.parsel})" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Excel'e Aktar
                    </button>
                    <button onclick="this.exportToPDF(${result.data.ada}, ${result.data.parsel})" class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> PDF Oluştur
                    </button>
                </div>
            </div>
        `;
    }
    
    async showHistory(ada, parsel) {
        try {
            const response = await fetch(`/api/parcel/history/${ada}/${parsel}`);
            const result = await response.json();
            
            if (result.success && result.data.length > 0) {
                const container = document.getElementById('parcel-history');
                container.innerHTML = `
                    <div class="history-info">
                        <h4>Sorgu Geçmişi</h4>
                        <div class="history-list">
                            ${result.data.map(item => `
                                <div class="history-item">
                                    <span class="date">${new Date(item.created_at).toLocaleString('tr-TR')}</span>
                                    <span class="source">${item.source || 'TKGM'}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            }
        } catch (error) {
            console.error('History yükleme hatası:', error);
        }
    }
}
```

### 5. **API Routes**

#### **Enhanced Route Definitions**
```php
// routes/api.php
Route::prefix('api')->group(function () {
    // Parcel Search Routes
    Route::post('/parcel/search', [ParcelController::class, 'searchParcel']);
    Route::get('/parcel/history/{ada}/{parsel}', [ParcelController::class, 'getParcelHistory']);
    Route::get('/parcel/download/{filename}', [ParcelController::class, 'downloadParcelJson']);
    
    // Parcel Analytics
    Route::get('/parcel/analytics', [ParcelController::class, 'getAnalytics']);
    Route::get('/parcel/statistics', [ParcelController::class, 'getStatistics']);
});
```

## 🎯 Enterprise Features

### 1. **Advanced Analytics**
- **Parcel search statistics**
- **Geographic distribution**
- **Usage patterns**
- **Performance metrics**

### 2. **Integration Capabilities**
- **Google Maps integration**
- **OpenStreetMap support**
- **GIS system integration**
- **CAD software export**

### 3. **Data Management**
- **Automated data updates**
- **Data validation**
- **Backup and recovery**
- **Data archiving**

### 4. **Security & Compliance**
- **API rate limiting**
- **Data encryption**
- **Audit logging**
- **GDPR compliance**

## 📊 Success Metrics

### Technical Metrics
- **API Response Time**: <500ms
- **Data Accuracy**: 99%+
- **Cache Hit Rate**: 90%+
- **Uptime**: 99.9%

### Business Metrics
- **User Adoption**: 80%+
- **Search Success Rate**: 95%+
- **Feature Usage**: 70%+
- **Customer Satisfaction**: 95%+

---

**Tarih**: 2025-01-30  
**Durum**: Implementation Ready  
**Sonraki Adım**: Development Phase
