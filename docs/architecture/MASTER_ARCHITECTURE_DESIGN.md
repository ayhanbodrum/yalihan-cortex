# 🏗️ Yalıhan Emlak OS - Master Architecture Design

**Tarih:** 01 Aralık 2025  
**Versiyon:** 2.1.0  
**Mimari Felsefe:** "Mağaza" ve "Depo" Ayrımı

---

## 🎯 MİMARİ FELSEFE

### "Mağaza" ve "Depo" Ayrımı

**Panel (Cortex) = Depo + Yönetim Ofisi**

- Karmaşık, ağır, güvenlik önlemleri yüksek
- Tüm veriyi yönetir
- AI işlemleri yapar
- CRM operasyonları

**WWW (Vitrin) = Mağaza**

- Hızlı, hafif, SEO uyumlu
- Sadece "Satılık" ürünleri gösterir
- Müşteri odaklı
- Performans kritik

---

## 📊 SUNUCU MİMARİSİ

### Dosya Yapısı

```
/var/www/
├── yalihan-panel/       <-- DEPO (Cortex + CRM)
│   ├── Çalıştığı Adres: panel.yalihanemlak.com.tr
│   ├── Port: 8000 (Docker: panel-app:8000)
│   ├── Görevi:
│   │   - Veriyi yönetmek
│   │   - Cortex AI'i çalıştırmak
│   │   - API üretmek
│   │   - CRM operasyonları
│   └── Teknoloji: Laravel 10 (Full Stack)
│
└── yalihan-web/         <-- MAĞAZA (Vitrin)
    ├── Çalıştığı Adres: www.yalihanemlak.com.tr
    ├── Port: 3000 (Docker: web-app:3000)
    ├── Görevi:
    │   - Sadece müşteriye ilanları göstermek
    │   - SEO optimizasyonu
    │   - Hızlı yükleme
    │   - Müşteri deneyimi
    └── Teknoloji: Next.js veya Hafif Laravel
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
- ✅ Global edge network

---

## 🔗 API KÖPRÜSÜ MİMARİSİ

### İletişim Akışı

```
┌─────────────────────────────────────────────────┐
│  MÜŞTERİ (Browser)                              │
└─────────────────┬───────────────────────────────┘
                  │
                  ↓
┌─────────────────────────────────────────────────┐
│  www.yalihanemlak.com.tr (MAĞAZA)               │
│  - Next.js/Laravel Frontend                      │
│  - SEO Optimized                                 │
│  - Fast Loading                                 │
└─────────────────┬───────────────────────────────┘
                  │
                  ↓ (Docker Network - Internal)
┌─────────────────────────────────────────────────┐
│  panel-app:8000/api/frontend/properties (API)    │
│  - Internal API Key Authentication              │
│  - Rate Limited                                 │
│  - Cached Responses                             │
└─────────────────┬───────────────────────────────┘
                  │
                  ↓
┌─────────────────────────────────────────────────┐
│  Laravel (Cortex + Database)                    │
│  - PropertyFeedService                          │
│  - Database Queries                             │
│  - Cache Layer (Redis)                          │
└─────────────────┬───────────────────────────────┘
                  │
                  ↓
┌─────────────────────────────────────────────────┐
│  Response (JSON)                                 │
│  - Standardized Format                           │
│  - Pagination                                   │
│  - Metadata                                     │
└─────────────────┬───────────────────────────────┘
                  │
                  ↓
┌─────────────────────────────────────────────────┐
│  Vitrin (Render)                                │
│  - SSR/SSG (Next.js)                            │
│  - Client-side Hydration                        │
└─────────────────────────────────────────────────┘
```

---

## 🔒 GÜVENLİK KATMANLARI

### 1. Internal API Authentication

**Middleware:** `VerifyFrontendApi`

```php
// app/Http/Middleware/VerifyFrontendApi.php
class VerifyFrontendApi
{
    public function handle($request, Closure $next)
    {
        // 1. API Key kontrolü
        $apiKey = $request->header('X-Internal-API-Key');
        if ($apiKey !== config('services.frontend_api.internal_key')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // 2. IP Whitelist kontrolü (Docker network)
        $allowedIps = config('services.frontend_api.allowed_ips', []);
        if (!empty($allowedIps) && !$this->isIpAllowed($request->ip(), $allowedIps)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
```

### 2. Rate Limiting

```php
// routes/api.php
Route::prefix('frontend')->middleware([
    'throttle:60,1',  // 60 istek/dakika
    'frontend.api',   // Internal API key kontrolü
])->group(function () {
    Route::get('/properties', [PropertyFeedController::class, 'index']);
    Route::get('/properties/featured', [PropertyFeedController::class, 'featured']);
    Route::get('/properties/{id}', [PropertyFeedController::class, 'show']);
});
```

### 3. CORS Ayarları

```php
// config/cors.php
'allowed_origins' => [
    'https://www.yalihanemlak.com.tr',
    'https://yalihanemlak.com.tr',
],
'allowed_methods' => ['GET', 'OPTIONS'],
'allowed_headers' => ['Content-Type', 'X-Internal-API-Key'],
```

---

## ⚡ PERFORMANS OPTİMİZASYONU

### 1. Caching Strategy (Redis)

```php
// app/Services/Frontend/PropertyFeedService.php
public function getPublishedProperties(array $filters = []): Collection
{
    $cacheKey = 'frontend:properties:' . md5(json_encode($filters));

    return Cache::tags(['frontend-properties'])
        ->remember($cacheKey, 300, function () use ($filters) {
            return Ilan::where('status', 'published')
                ->where('yayin_tipi', $filters['type'] ?? 'satilik')
                ->with(['kategori', 'il', 'ilce', 'mahalle'])
                ->latest()
                ->limit($filters['limit'] ?? 10)
                ->get();
        });
}

// Cache invalidation
Event::listen(IlanUpdated::class, function () {
    Cache::tags(['frontend-properties'])->flush();
});
```

### 2. Database Indexing

```sql
-- İlan sorguları için kritik index'ler
CREATE INDEX idx_ilan_status_yayin ON ilanlar(status, yayin_tipi);
CREATE INDEX idx_ilan_kategori ON ilanlar(kategori_id);
CREATE INDEX idx_ilan_created ON ilanlar(created_at DESC);
CREATE INDEX idx_ilan_fiyat ON ilanlar(satis_fiyati);
CREATE INDEX idx_ilan_konum ON ilanlar(il_id, ilce_id, mahalle_id);
```

### 3. Eager Loading

```php
// N+1 problem'ini önle
Ilan::with([
    'kategori',
    'il',
    'ilce',
    'mahalle',
    'fiyat',
    'resimler' => function ($query) {
        $query->where('sira', 1); // Sadece ilk resim
    }
])->where('status', 'published')->get();
```

### 4. Response Compression

```nginx
# Nginx config
gzip on;
gzip_types application/json text/html text/css application/javascript;
gzip_min_length 1000;
```

---

## 📋 API ENDPOINT'LERİ

### 1. İlan Listesi

```
GET http://panel-app:8000/api/frontend/properties

Query Parameters:
  - category: villa, arsa, daire
  - district: ilce_id
  - neighborhood: mahalle_id
  - min_price: minimum fiyat
  - max_price: maximum fiyat
  - per_page: sayfa başına kayıt (default: 12)
  - page: sayfa numarası
  - currency: para birimi (TRY, USD, EUR)

Response:
{
  "success": true,
  "data": [...],
  "meta": {
    "current_page": 1,
    "per_page": 12,
    "last_page": 10,
    "total": 120,
    "has_more": true
  }
}
```

### 2. Öne Çıkan İlanlar

```
GET http://panel-app:8000/api/frontend/properties/featured

Query Parameters:
  - limit: kayıt sayısı (default: 6)
  - currency: para birimi

Response:
{
  "success": true,
  "data": [...],
  "meta": {
    "count": 6,
    "limit": 6
  }
}
```

### 3. İlan Detayı

```
GET http://panel-app:8000/api/frontend/properties/{id}

Query Parameters:
  - currency: para birimi

Response:
{
  "success": true,
  "data": {
    "id": 123,
    "baslik": "...",
    "aciklama": "...",
    "fiyat": 5000000,
    "para_birimi": "TRY",
    ...
  }
}
```

---

## 🐳 DOCKER YAPILANDIRMASI

### docker-compose.yml

```yaml
version: '3.8'

services:
    # DEPO: Panel (Cortex + CRM)
    panel-app:
        build: ./yalihan-panel
        container_name: yalihan-panel
        networks:
            - yalihan-network
        environment:
            - APP_URL=http://panel-app:8000
            - FRONTEND_API_KEY=${FRONTEND_API_KEY}
            - DB_HOST=mysql
            - REDIS_HOST=redis
        ports:
            - '8000:8000'
        volumes:
            - ./yalihan-panel:/var/www/html
            - panel-storage:/var/www/html/storage
        depends_on:
            - mysql
            - redis

    # MAĞAZA: Vitrin (Frontend)
    web-app:
        build: ./yalihan-web
        container_name: yalihan-web
        networks:
            - yalihan-network
        environment:
            - NEXT_PUBLIC_API_URL=http://panel-app:8000/api/frontend
            - INTERNAL_API_KEY=${FRONTEND_API_KEY}
            - NODE_ENV=production
        ports:
            - '3000:3000'
        volumes:
            - ./yalihan-web:/app
        depends_on:
            - panel-app

    # Database
    mysql:
        image: mysql:8.0
        container_name: yalihan-mysql
        networks:
            - yalihan-network
        environment:
            - MYSQL_DATABASE=yalihanemlak_ultra
            - MYSQL_ROOT_PASSWORD=${DB_PASSWORD}
        volumes:
            - mysql-data:/var/lib/mysql

    # Redis (Cache)
    redis:
        image: redis:7-alpine
        container_name: yalihan-redis
        networks:
            - yalihan-network
        volumes:
            - redis-data:/data

networks:
    yalihan-network:
        driver: bridge
        internal: false # External erişim için

volumes:
    panel-storage:
    mysql-data:
    redis-data:
```

---

## 🔄 DEPLOYMENT WORKFLOW

### 1. Panel Deployment

```bash
# Production sunucuda
cd /var/www/yalihan-panel
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
```

### 2. Vitrin Deployment

```bash
# Production sunucuda
cd /var/www/yalihan-web

# Next.js için
npm install --production
npm run build
pm2 restart yalihan-web

# veya Laravel için
composer install --no-dev
php artisan config:cache
php artisan route:cache
```

### 3. Docker Compose

```bash
# Her iki servisi de yeniden başlat
docker-compose up -d --build
```

---

## 📊 MONİTORİNG VE LOGGING

### 1. API Logging

```php
// app/Http/Middleware/LogApiRequests.php
Log::info('Frontend API Request', [
    'endpoint' => $request->path(),
    'ip' => $request->ip(),
    'user_agent' => $request->userAgent(),
    'response_time' => $responseTime,
]);
```

### 2. Performance Monitoring

```php
// Response time tracking
$startTime = microtime(true);
$response = $next($request);
$responseTime = (microtime(true) - $startTime) * 1000;

if ($responseTime > 1000) {
    Log::warning('Slow API Response', [
        'endpoint' => $request->path(),
        'response_time' => $responseTime,
    ]);
}
```

### 3. Error Tracking

```php
// Sentry veya benzeri error tracking
if (app()->bound('sentry')) {
    app('sentry')->captureException($exception);
}
```

---

## 🎯 SEO OPTİMİZASYONU (Vitrin)

### 1. Meta Tags

```php
// Next.js veya Laravel'de
<meta property="og:title" content="{{ $ilan->baslik }}">
<meta property="og:description" content="{{ $ilan->aciklama }}">
<meta property="og:image" content="{{ $ilan->resim_url }}">
```

### 2. Structured Data (JSON-LD)

```json
{
    "@context": "https://schema.org",
    "@type": "RealEstateAgent",
    "name": "Yalıhan Emlak",
    "url": "https://www.yalihanemlak.com.tr"
}
```

### 3. Sitemap Generation

```php
// Otomatik sitemap oluşturma
Route::get('/sitemap.xml', function () {
    $ilanlar = Ilan::where('status', 'published')->get();
    return response()->view('sitemap', ['ilanlar' => $ilanlar])
        ->header('Content-Type', 'text/xml');
});
```

---

## 🔐 GÜVENLİK CHECKLIST

### Panel (Depo)

- [ ] Admin authentication (Laravel Auth)
- [ ] CSRF protection
- [ ] Rate limiting
- [ ] Input validation
- [ ] SQL injection protection (Eloquent)
- [ ] XSS protection (Blade escaping)

### Vitrin (Mağaza)

- [ ] Internal API key authentication
- [ ] IP whitelisting (Docker network)
- [ ] Rate limiting
- [ ] CORS configuration
- [ ] Input sanitization
- [ ] Output escaping

### API Köprüsü

- [ ] Internal API key
- [ ] Request signing (optional)
- [ ] Response encryption (optional)
- [ ] Audit logging

---

## 📈 ÖLÇEKLENDİRME STRATEJİSİ

### Horizontal Scaling

```
Vitrin (Mağaza):
  - Load Balancer (Cloudflare)
  - Multiple web-app instances
  - Shared Redis cache
  - CDN for static assets

Panel (Depo):
  - Single instance (CRM complexity)
  - Queue workers (multiple)
  - Database replication (read replicas)
  - Redis cluster
```

### Vertical Scaling

```
Panel:
  - More CPU for AI processing
  - More RAM for database queries
  - SSD for database

Vitrin:
  - More CPU for rendering
  - More RAM for caching
  - CDN for static assets
```

---

## 🎯 SONUÇ

### Mimari: ✅ MÜKEMMEL

**Güçlü Yönler:**

- ✅ Ayrıştırılmış mimari (Mağaza + Depo)
- ✅ Docker network güvenliği
- ✅ Cloudflare Tunnel entegrasyonu
- ✅ Performans odaklı (Caching, Indexing)
- ✅ SEO uyumlu
- ✅ Ölçeklenebilir

**Production Ready:**

- ✅ Güvenlik katmanları
- ✅ Monitoring ve logging
- ✅ Error handling
- ✅ Deployment workflow

---

**Son Güncelleme:** 01 Aralık 2025
