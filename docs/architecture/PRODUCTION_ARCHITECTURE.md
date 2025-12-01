# 🏗️ Production Mimari - Yalıhan Emlak OS

**Tarih:** 01 Aralık 2025  
**Durum:** Mimari Planlama  
**Versiyon:** 2.1.0

---

## 📊 MEVCUT MİMARİ PLANI

### Sunucu Yapısı

```
/var/www/
├── yalihan-panel/       <-- CRM + AI (Laravel)
│   ├── Çalıştığı Adres: panel.yalihanemlak.com.tr
│   ├── Görevi: Veriyi yönetmek, Cortex'i çalıştırmak, API üretmek
│   └── Port: 8000 (Docker: panel-app:8000)
│
└── yalihan-web/         <-- Vitrin (Next.js veya Laravel)
    ├── Çalıştığı Adres: www.yalihanemlak.com.tr
    ├── Görevi: Sadece müşteriye ilanları göstermek
    └── Port: 3000 (Docker: web-app:3000)
```

---

## 🌐 CLOUDFLARE TUNNEL YAPILANDIRMASI

### Tunnel Ayarları

```
Public Hostname (Domain)          Service (Docker içi)
─────────────────────────────────────────────────────
panel.yalihanemlak.com.tr    →    http://panel-app:8000
www.yalihanemlak.com.tr      →    http://web-app:3000
```

### Avantajlar
- ✅ Tek tunnel, iki domain
- ✅ Docker network üzerinden iletişim
- ✅ SSL otomatik (Cloudflare)
- ✅ DDoS koruması
- ✅ CDN desteği

---

## 🔗 API KÖPRÜSÜ MİMARİSİ

### İletişim Akışı

```
Müşteri (Browser)
    ↓
www.yalihanemlak.com.tr (Vitrin)
    ↓
Docker Network (Internal)
    ↓
panel.yalihanemlak.com.tr/api/frontend/properties (API)
    ↓
Laravel (Cortex + Database)
    ↓
Response (JSON)
    ↓
Vitrin (Render)
```

### API Endpoint'leri

#### 1. İlan Listesi
```
GET http://panel-app:8000/api/frontend/properties
Query Params:
  - category: villa, arsa, daire
  - status: published
  - limit: 10
  - offset: 0
```

#### 2. İlan Detayı
```
GET http://panel-app:8000/api/frontend/properties/{id}
```

#### 3. Öne Çıkan İlanlar
```
GET http://panel-app:8000/api/frontend/properties/featured
```

---

## 🎯 ÖNERİLER VE İYİLEŞTİRMELER

### 1. API Authentication (Önerilen)

**Sorun:** Docker network içinde olsa bile güvenlik önemli

**Çözüm:** API Key veya Internal Token

```php
// config/services.php
'frontend_api' => [
    'internal_key' => env('FRONTEND_API_KEY', ''),
    'allowed_ips' => ['172.17.0.0/16'], // Docker network
],
```

**Middleware:**
```php
// app/Http/Middleware/VerifyFrontendApi.php
class VerifyFrontendApi
{
    public function handle($request, Closure $next)
    {
        $apiKey = $request->header('X-Internal-API-Key');
        if ($apiKey !== config('services.frontend_api.internal_key')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $next($request);
    }
}
```

---

### 2. Rate Limiting (Önerilen)

**Neden:** API abuse'u önlemek

```php
// routes/api.php
Route::prefix('frontend')->middleware(['throttle:60,1'])->group(function () {
    Route::get('/properties', [PropertyFeedController::class, 'index']);
});
```

---

### 3. Caching Strategy (Kritik!)

**Sorun:** Her istekte database sorgusu yavaş

**Çözüm:** Redis Cache

```php
// PropertyFeedService.php
public function getPublishedProperties(array $filters = []): Collection
{
    $cacheKey = 'frontend:properties:' . md5(json_encode($filters));
    
    return Cache::remember($cacheKey, 300, function () use ($filters) {
        return Ilan::where('status', 'published')
            ->where('yayin_tipi', $filters['type'] ?? 'satilik')
            ->with(['kategori', 'il', 'ilce'])
            ->latest()
            ->limit($filters['limit'] ?? 10)
            ->get();
    });
}
```

**Cache Invalidation:**
```php
// İlan güncellendiğinde cache'i temizle
Event::listen(IlanUpdated::class, function () {
    Cache::tags(['frontend-properties'])->flush();
});
```

---

### 4. Response Format Standardization

**Önerilen Format:**
```json
{
  "success": true,
  "data": {
    "properties": [...],
    "pagination": {
      "total": 150,
      "per_page": 10,
      "current_page": 1,
      "last_page": 15
    }
  },
  "meta": {
    "cached": true,
    "cache_ttl": 300
  }
}
```

---

### 5. Error Handling

**Standardize Edilmiş Hata Yanıtları:**
```php
// app/Http/Controllers/Api/Frontend/PropertyFeedController.php
try {
    $properties = $this->propertyFeedService->getPublishedProperties($filters);
    return ResponseService::success($properties);
} catch (\Exception $e) {
    Log::error('Frontend API Error', [
        'error' => $e->getMessage(),
        'filters' => $filters,
    ]);
    
    return ResponseService::error('İlanlar yüklenirken hata oluştu', 500);
}
```

---

## 🐳 DOCKER NETWORK YAPILANDIRMASI

### docker-compose.yml (Önerilen)

```yaml
version: '3.8'

services:
  panel-app:
    build: ./yalihan-panel
    container_name: yalihan-panel
    networks:
      - yalihan-network
    environment:
      - APP_URL=http://panel-app:8000
    ports:
      - "8000:8000"
  
  web-app:
    build: ./yalihan-web
    container_name: yalihan-web
    networks:
      - yalihan-network
    environment:
      - NEXT_PUBLIC_API_URL=http://panel-app:8000/api/frontend
      - INTERNAL_API_KEY=${FRONTEND_API_KEY}
    ports:
      - "3000:3000"
    depends_on:
      - panel-app

networks:
  yalihan-network:
    driver: bridge
```

---

## 🔒 GÜVENLİK ÖNERİLERİ

### 1. Internal API Key
- Docker network içinde olsa bile API key kullanın
- Environment variable'dan okuyun
- Her istekte kontrol edin

### 2. IP Whitelisting
```php
// Sadece Docker network IP'lerinden erişim
$allowedIps = ['172.17.0.0/16', '10.0.0.0/8'];
if (!in_array($request->ip(), $allowedIps)) {
    return response()->json(['error' => 'Forbidden'], 403);
}
```

### 3. CORS Ayarları
```php
// config/cors.php
'allowed_origins' => [
    'https://www.yalihanemlak.com.tr',
    'https://yalihanemlak.com.tr',
],
```

---

## 📊 PERFORMANS ÖNERİLERİ

### 1. Database Indexing
```sql
-- İlan sorguları için index'ler
CREATE INDEX idx_ilan_status_yayin ON ilanlar(status, yayin_tipi);
CREATE INDEX idx_ilan_kategori ON ilanlar(kategori_id);
CREATE INDEX idx_ilan_created ON ilanlar(created_at DESC);
```

### 2. Eager Loading
```php
// N+1 problem'ini önle
Ilan::with(['kategori', 'il', 'ilce', 'mahalle', 'fiyat'])
    ->where('status', 'published')
    ->get();
```

### 3. Response Compression
```php
// Nginx veya Cloudflare'de gzip aktif
// Response boyutunu %70 azaltır
```

---

## 🎯 MİMARİ DEĞERLENDİRME

### ✅ Güçlü Yönler

1. **Ayrıştırılmış Mimari**
   - Panel (CRM + AI) ve Vitrin (Frontend) ayrı
   - Bakım ve ölçekleme kolay

2. **Docker Network**
   - Internal iletişim güvenli
   - Public API'ye gerek yok

3. **Cloudflare Tunnel**
   - SSL otomatik
   - DDoS koruması
   - CDN desteği

### ⚠️ Dikkat Edilmesi Gerekenler

1. **API Authentication**
   - Internal olsa bile API key kullanın
   - Rate limiting ekleyin

2. **Caching**
   - Redis cache kullanın
   - Cache invalidation stratejisi

3. **Error Handling**
   - Standardize edilmiş hata yanıtları
   - Logging ve monitoring

4. **Database Performance**
   - Index'ler ekleyin
   - Eager loading kullanın
   - Query optimization

---

## 📋 DEPLOYMENT CHECKLIST

### Panel (yalihan-panel)
- [ ] Laravel projesi production'a alındı
- [ ] Environment değişkenleri ayarlandı
- [ ] Database migration çalıştırıldı
- [ ] Queue worker başlatıldı
- [ ] Cloudflare Tunnel yapılandırıldı
- [ ] API endpoint'leri test edildi

### Vitrin (yalihan-web)
- [ ] Next.js/Laravel projesi kuruldu
- [ ] Internal API URL yapılandırıldı
- [ ] API key yapılandırıldı
- [ ] Cloudflare Tunnel yapılandırıldı
- [ ] Frontend test edildi

### Ortak
- [ ] Docker network oluşturuldu
- [ ] Internal iletişim test edildi
- [ ] Caching yapılandırıldı
- [ ] Monitoring kuruldu

---

## 🚀 SONUÇ

### Mimari: ✅ İYİ

**Güçlü Yönler:**
- Ayrıştırılmış yapı
- Docker network güvenliği
- Cloudflare Tunnel entegrasyonu

**Öneriler:**
- API authentication ekleyin
- Caching stratejisi uygulayın
- Rate limiting ekleyin
- Database index'leri optimize edin

**Sonuç:** Mimari sağlam, önerilen iyileştirmelerle production-ready olacak.

---

**Son Güncelleme:** 01 Aralık 2025

