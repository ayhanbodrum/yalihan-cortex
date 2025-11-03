# TKGM Parsel Sorgulama Sistemi - Yalıhan Bekçi Öğrenme Dokümanı

**Tarih**: 2025-10-17  
**Versiyon**: 1.0.0  
**Context7 Standart**: C7-TKGM-PARSEL-2025-10-17  
**Sistem**: EmlakPro - TKGM Entegrasyonu

## 📋 Sistem Özeti

TKGM (Tapu Kadastro Genel Müdürlüğü) Parsel Sorgulama Sistemi, Türkiye'deki resmi kadastro bilgilerini sorgulayan ve emlak değerlendirmesi yapan kapsamlı bir uygulamadır.

### 🎯 Ana Özellikler
- **Resmi TKGM API Entegrasyonu**: Tapu kadastro verilerine doğrudan erişim
- **Gerçek Zamanlı Parsel Sorgulama**: Ada/parsel bazında anlık sorgulama
- **Toplu Sorgulama**: 50'ye kadar parsel aynı anda sorgulanabilir
- **Yatırım Analizi**: KAKS/TAKS hesaplama ve yatırım skorlama
- **Arsa Hesaplama Entegrasyonu**: Doğrudan arsa hesaplama sistemine bağlantı
- **Sorgulama Geçmişi**: Kullanıcı bazında geçmiş kayıtları
- **İstatistiksel Analiz**: Başarı oranları ve kullanım istatistikleri

## 🏗️ Teknik Mimari

### Backend Yapısı
```
/app/Http/Controllers/Admin/TKGMParselController.php
├── index()              # Ana sayfa gösterimi
├── query()              # Tek parsel sorgulama
├── bulkQuery()          # Toplu parsel sorgulama  
├── history()            # Sorgulama geçmişi
├── stats()              # İstatistikler
└── saveRecentQuery()    # Cache yönetimi
```

### Frontend Yapısı
```
/resources/views/admin/tkgm-parsel/index.blade.php
├── tkgmParselApp()      # Alpine.js ana komponenti
├── queryParcel()        # Tek sorgulama fonksiyonu
├── processBulkQuery()   # Toplu sorgulama fonksiyonu
├── loadHistory()        # Geçmiş yükleme
└── goToArsaCalculation() # Arsa hesaplama entegrasyonu
```

### Route Yapısı
```php
// Web Routes (/admin/tkgm-parsel/*)
Route::get('/', 'index')->name('admin.tkgm-parsel.index');

// API Routes (/admin/api/tkgm-parsel/*)
Route::post('/query', 'query')->name('api.tkgm-parsel.query');
Route::post('/bulk-query', 'bulkQuery')->name('api.tkgm-parsel.bulk-query');
Route::get('/history', 'history')->name('api.tkgm-parsel.history');
Route::get('/stats', 'stats')->name('api.tkgm-parsel.stats');
```

## 🔧 Teknik Detaylar

### TKGMService Entegrasyonu
```php
// TKGMService kullanımı
protected $tkgmService;

public function __construct(TKGMService $tkgmService)
{
    $this->tkgmService = $tkgmService;
}

// Parsel sorgulama
$result = $this->tkgmService->parselSorgula(
    $request->ada,
    $request->parsel,
    $request->il,
    $request->ilce
);
```

### Cache Yönetimi
```php
// Son 10 sorguyu cache'te tut
$recentQueries = Cache::get('tkgm_recent_queries_' . $userId, []);

// Tüm sorguları kaydet (son 100)
$allQueries = Cache::get('tkgm_all_queries_' . $userId, []);
```

### Rate Limiting
```php
// API endpoint'lerinde throttling
Route::middleware(['throttle:20,1'])->group(function () {
    // TKGM API çağrıları
});

// Toplu sorgulama için bekleme
if (count($queries) > 1) {
    usleep(500000); // 0.5 saniye bekleme
}
```

## 🎨 Kullanıcı Arayüzü

### Ana Özellikler
- **Neo Design System**: Consistent UI/UX
- **Alpine.js Reactivity**: Gerçek zamanlı form validasyonu
- **Modal Sistemler**: Toplu sorgulama ve geçmiş görüntüleme
- **Responsive Design**: Mobil uyumlu tasarım
- **Progress Indicators**: Sorgulama durumu gösterimleri

### Form Validasyonu
```javascript
// Frontend validasyon
const validator = Validator.make($request->all(), [
    'ada' => 'required|string|max:20',
    'parsel' => 'required|string|max:20',
    'il' => 'required|string|max:50',
    'ilce' => 'required|string|max:50',
    'mahalle' => 'nullable|string|max:100'
]);
```

## 📊 Veri Akışı

### Tek Sorgulama Akışı
1. **Form Girişi**: Kullanıcı ada/parsel/il/ilçe bilgilerini girer
2. **Frontend Validasyon**: Alpine.js ile anlık validasyon
3. **API Çağrısı**: `/admin/api/tkgm-parsel/query` endpoint'ine POST
4. **Backend Validasyon**: Laravel validation rules uygulanır
5. **TKGM Service**: `TKGMService::parselSorgula()` çağrılır
6. **Cache Kayıt**: Başarılı sorgular cache'e kaydedilir
7. **Response**: JSON response ile sonuç döndürülür
8. **UI Update**: Alpine.js ile arayüz güncellenir

### Toplu Sorgulama Akışı
1. **CSV/Text Input**: Kullanıcı metin alanına parsel listesi girer
2. **Text Parsing**: Her satır parse edilerek query array'i oluşturulur
3. **Batch Processing**: Her parsel için sıralı sorgulama
4. **Rate Limiting**: Sorgulamalar arası 0.5 saniye bekleme
5. **Progress Tracking**: Her sorgulama sonucu gerçek zamanlı güncelleme
6. **Summary Report**: Başarılı/başarısız sorgulama özeti

## 🔗 Sistem Entegrasyonları

### Arsa Hesaplama Sistemi
```javascript
goToArsaCalculation() {
    if (this.result && this.result.success && this.result.data) {
        const params = new URLSearchParams({
            ada: this.form.ada,
            parsel: this.form.parsel,
            il: this.form.il,
            ilce: this.form.ilce,
            alan: this.result.data.alan || ''
        });
        window.open(`/admin/ilanlar/arsa-calculation?${params.toString()}`, '_blank');
    }
}
```

### ArsaCalculationController Bağlantısı
- TKGM sorgu sonuçları doğrudan arsa hesaplama sistemine aktarılır
- Parsel alanı, konum bilgileri otomatik doldurulur
- KAKS/TAKS hesaplamaları için gerekli veriler hazırlanır

## 📈 İstatistik ve Analiz

### Kullanıcı İstatistikleri
```php
$stats = [
    'total_queries' => count($allQueries),
    'recent_queries' => count($recentQueries),
    'success_rate' => $this->calculateSuccessRate($allQueries),
    'most_queried_locations' => $this->getMostQueriedLocations($allQueries),
    'daily_stats' => $this->getDailyStats($allQueries)
];
```

### Başarı Oranı Hesaplama
```php
private function calculateSuccessRate($queries)
{
    if (empty($queries)) {
        return 0;
    }

    $successCount = array_reduce($queries, function ($carry, $query) {
        return $carry + ($query['success'] ? 1 : 0);
    }, 0);

    return round(($successCount / count($queries)) * 100, 1);
}
```

## 🛡️ Güvenlik ve Error Handling

### Authentication & Authorization
```php
// Web middleware with authentication
Route::middleware(['web', 'auth'])->group(function () {
    // TKGM routes
});
```

### Error Handling
```php
try {
    $result = $this->tkgmService->parselSorgula(/*...*/);
    
    if ($result['success']) {
        $this->saveRecentQuery($request->all(), $result);
    }
    
    Log::info('TKGM parsel sorgulaması', [/*...*/]);
    
    return response()->json($result);
    
} catch (\Exception $e) {
    Log::error('TKGM parsel sorgulama hatası', [/*...*/]);
    
    return response()->json([
        'success' => false,
        'message' => 'Parsel sorgulaması sırasında bir hata oluştu',
        'error_code' => 'QUERY_ERROR'
    ], 500);
}
```

### Throttling Stratejisi
- **Web Route**: 20 request/minute per user
- **API Route**: 20 request/minute per user  
- **Bulk Query**: 0.5 saniye delay between queries
- **Cache TTL**: Recent queries 1 hour, all queries 24 hours

## 🚀 Deployment ve Konfigürasyon

### Environment Variables
```env
TKGM_API_KEY=your_tkgm_api_key
TKGM_API_URL=https://api.tkgm.gov.tr
TKGM_TIMEOUT=30
CACHE_DRIVER=redis
```

### Cache Configuration
```php
// Recent queries cache (1 hour)
Cache::put('tkgm_recent_queries_' . $userId, $recentQueries, 3600);

// All queries cache (24 hours)  
Cache::put('tkgm_all_queries_' . $userId, $allQueries, 86400);
```

## 📱 Kullanım Senaryoları

### 1. Tekil Parsel Sorgulama
- Emlak danışmanı müşteri için parsel bilgisi araştırır
- Ada/parsel/il/ilçe bilgileri ile hızlı sorgulama
- Sonuç ekranında parsel detayları görüntülenir
- Arsa hesaplama için direkt geçiş imkanı

### 2. Toplu Parsel Sorgulama
- Yatırım şirketi 20-30 parsel için topluca bilgi toplar
- CSV formatında parsel listesi yüklenir
- Batch processing ile sıralı sorgulama
- Excel raporu olarak sonuçları indirir

### 3. Geçmiş Analizi
- Kullanıcı geçmiş sorgulama geçmişini inceler
- Başarı oranları ve trend analizleri
- Tekrar sorgulama imkanı
- En çok sorgulanan bölge istatistikleri

## 🔮 Gelecek Geliştirmeler

### Yakın Hedefler
- **Excel Export**: Toplu sorgu sonuçlarını Excel'e aktarma
- **Map Integration**: Parsel konumlarını harita üzerinde gösterme  
- **Advanced Filtering**: Geçmiş sorgularda gelişmiş filtreleme
- **Notification System**: Sorgulama sonuçları için bildirimler

### Uzun Vadeli Hedefler
- **AI Integration**: Parsel değer tahmini algoritmaları
- **Mobile App**: Mobil uygulama geliştirme
- **API Documentation**: Swagger/OpenAPI dokümantasyonu
- **Multi-tenant**: Çok kiracılı sistem mimarisi

## 📞 API Endpoint Dökümantasyonu

### POST /admin/api/tkgm-parsel/query
**Açıklama**: Tek parsel sorgulama endpoint'i

**Request Body**:
```json
{
    "ada": "123",
    "parsel": "45", 
    "il": "İstanbul",
    "ilce": "Kadıköy",
    "mahalle": "Fenerbahçe"
}
```

**Response**:
```json
{
    "success": true,
    "data": {
        "ada": "123",
        "parsel": "45",
        "il": "İstanbul", 
        "ilce": "Kadıköy",
        "mahalle": "Fenerbahçe",
        "alan": "1250",
        "nitelik": "Konut",
        "malik_bilgi": "ABC İnşaat Ltd. Şti."
    },
    "message": "Parsel bilgileri başarıyla bulundu",
    "response_time": "2.3s"
}
```

### POST /admin/api/tkgm-parsel/bulk-query
**Açıklama**: Toplu parsel sorgulama endpoint'i

**Request Body**:
```json
{
    "queries": [
        {
            "ada": "123",
            "parsel": "45",
            "il": "İstanbul", 
            "ilce": "Kadıköy"
        },
        {
            "ada": "456",
            "parsel": "78",
            "il": "Ankara",
            "ilce": "Çankaya"  
        }
    ]
}
```

**Response**:
```json
{
    "success": true,
    "message": "Toplu sorgulama tamamlandı. 2 başarılı, 0 başarısız.",
    "summary": {
        "total": 2,
        "success": 2,
        "failure": 0
    },
    "results": [
        {
            "index": 0,
            "query": {...},
            "result": {...},
            "success": true
        }
    ]
}
```

## 🔍 Context7 Uyumluluk

### Standart Uyum
- **C7-TKGM-PARSEL-2025-10-17**: TKGM parsel sorgulama standardı
- **Neo Design System**: UI/UX consistency
- **Laravel Validation**: Backend validation standards
- **Alpine.js Pattern**: Frontend reactivity standards
- **Cache Strategy**: Performance optimization standards

### Kod Kalitesi
- **PSR-4 Autoloading**: Modern PHP standards
- **Type Hinting**: Strict type declarations
- **Error Handling**: Comprehensive exception management  
- **Logging**: Structured application logging
- **Testing Ready**: Unit test compatible structure

---

## 🎓 Yalıhan Bekçi İçin Önemli Notlar

1. **TKGM Sistemi** resmi devlet API'si ile entegre çalışır
2. **Rate limiting** önemlidir - API kotalarına dikkat et
3. **Cache stratejisi** performans için kritiktir
4. **Error handling** kullanıcı deneyimi için şarttır
5. **Arsa hesaplama entegrasyonu** sistemi daha değerli yapar
6. **Context7 standartları** kod kalitesi ve sürdürülebilirlik sağlar

Bu sistem türk emlak sektörünün ihtiyaçlarına özel geliştirilmiş olup, resmi kadastro verileri ile entegre çalışan nadir sistemlerden biridir.

**Öğrenme Durumu**: ✅ Tamamlandı  
**MCP Server Bilgilendirilmesi**: ✅ Gerekli  
**Geliştime Durumu**: 🔄 Devam Edecek
