# 🔄 n8n - Laravel Entegrasyonu

## 📋 İçindekiler

1. [n8n Nedir?](#n8n-nedir)
2. [Kurulum Yöntemleri](#kurulum-yöntemleri)
3. [Laravel Entegrasyonu](#laravel-entegrasyonu)
4. [Örnek Workflow'lar](#örnek-workflowlar)
5. [Güvenlik ve Best Practices](#güvenlik)

---

## 🤖 n8n Nedir?

n8n, açık kaynaklı bir workflow automation platformudur. Zapier ve Make (Integromat) alternatifidir.

### **Özellikler:**

- ✅ 400+ entegrasyon
- ✅ Self-hosted (kendi sunucunuzda)
- ✅ Görsel workflow editörü
- ✅ Webhook desteği
- ✅ Cron job scheduler
- ✅ Database bağlantıları

---

## 🚀 Kurulum Yöntemleri

### **Yöntem 1: Docker ile Kurulum (Önerilen)**

```bash
# 1. Docker container'ı çalıştır
docker run -d \
  --name n8n \
  -p 5678:5678 \
  -v ~/.n8n:/home/node/.n8n \
  -e N8N_BASIC_AUTH_ACTIVE=true \
  -e N8N_BASIC_AUTH_USER=admin \
  -e N8N_BASIC_AUTH_PASSWORD=admin123 \
  -e WEBHOOK_URL=http://localhost:5678/ \
  n8nio/n8n

# 2. n8n'e erişim
# Tarayıcıda: http://localhost:5678
```

### **Yöntem 2: npm ile Kurulum**

```bash
# 1. Global kurulum
npm install n8n -g

# 2. Çalıştırma
n8n start

# 3. Tarayıcıda aç
# http://localhost:5678
```

### **Yöntem 3: Docker Compose (Production)**

```yaml
# docker-compose.n8n.yml
version: '3.8'

services:
    n8n:
        image: n8nio/n8n
        container_name: yalihanemlak_n8n
        restart: unless-stopped
        ports:
            - '5678:5678'
        environment:
            - N8N_BASIC_AUTH_ACTIVE=true
            - N8N_BASIC_AUTH_USER=admin
            - N8N_BASIC_AUTH_PASSWORD=${N8N_PASSWORD}
            - WEBHOOK_URL=https://n8n.yalihanemlak.com/
            - GENERIC_TIMEZONE=Europe/Istanbul
            - N8N_SECURE_COOKIE=true
        volumes:
            - n8n_data:/home/node/.n8n
        networks:
            - yalihanemlak_network

volumes:
    n8n_data:

networks:
    yalihanemlak_network:
        external: true
```

```bash
# Çalıştırma
docker-compose -f docker-compose.n8n.yml up -d
```

---

## 🔗 Laravel Entegrasyonu

### **1. Laravel → n8n (Webhook Tetikleme)**

#### **Laravel Tarafı:**

```php
// app/Services/N8nService.php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class N8nService
{
    protected $baseUrl;
    protected $webhookToken;

    public function __construct()
    {
        $this->baseUrl = config('services.n8n.url');
        $this->webhookToken = config('services.n8n.webhook_token');
    }

    public function triggerWebhook(string $webhookPath, array $data)
    {
        try {
            $url = $this->baseUrl . '/webhook/' . $webhookPath;

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->webhookToken,
                    'Content-Type' => 'application/json',
                ])
                ->post($url, $data);

            if ($response->successful()) {
                Log::info('n8n webhook triggered', [
                    'webhook' => $webhookPath,
                    'status' => $response->status(),
                ]);

                return $response->json();
            }

            Log::error('n8n webhook failed', [
                'webhook' => $webhookPath,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('n8n webhook exception', [
                'webhook' => $webhookPath,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function sendNewIlan(array $ilanData)
    {
        return $this->triggerWebhook('yeni-ilan', [
            'event' => 'ilan_created',
            'data' => $ilanData,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function sendNewKisi(array $kisiData)
    {
        return $this->triggerWebhook('yeni-kisi', [
            'event' => 'kisi_created',
            'data' => $kisiData,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function sendNotification(string $type, array $data)
    {
        return $this->triggerWebhook('bildirim', [
            'type' => $type,
            'data' => $data,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
```

#### **Konfigürasyon:**

```php
// config/services.php
return [
    // ... diğer servisler

    'n8n' => [
        'url' => env('N8N_URL', 'http://localhost:5678'),
        'webhook_token' => env('N8N_WEBHOOK_TOKEN', 'your-secret-token'),
    ],
];
```

```bash
# .env
N8N_URL=http://localhost:5678
N8N_WEBHOOK_TOKEN=your-secret-token-here
```

#### **Kullanım Örnekleri:**

```php
// app/Http/Controllers/Admin/IlanController.php
use App\Services\N8nService;

public function store(Request $request, N8nService $n8n)
{
    $ilan = Ilan::create($request->validated());

    $n8n->sendNewIlan([
        'id' => $ilan->id,
        'baslik' => $ilan->baslik,
        'fiyat' => $ilan->fiyat,
        'il' => $ilan->il->il_adi ?? null,
        'kategori' => $ilan->kategori->name ?? null,
    ]);

    return redirect()->route('admin.ilanlar.index')
        ->with('success', 'İlan oluşturuldu!');
}
```

```php
// Event Listener ile otomatik tetikleme
// app/Listeners/NotifyN8nOnNewIlan.php
<?php

namespace App\Listeners;

use App\Events\IlanCreated;
use App\Services\N8nService;

class NotifyN8nOnNewIlan
{
    protected $n8n;

    public function __construct(N8nService $n8n)
    {
        $this->n8n = $n8n;
    }

    public function handle(IlanCreated $event)
    {
        $this->n8n->sendNewIlan([
            'id' => $event->ilan->id,
            'baslik' => $event->ilan->baslik,
            'user_id' => $event->ilan->user_id,
        ]);
    }
}
```

---

### **2. n8n → Laravel (API Çağrısı)**

#### **Laravel API Endpoint:**

```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/n8n/webhook/ilan-create', [N8nWebhookController::class, 'createIlan']);
    Route::post('/n8n/webhook/kisi-update', [N8nWebhookController::class, 'updateKisi']);
});

Route::middleware('n8n.webhook')->group(function () {
    Route::post('/webhooks/n8n/ilan-statusu-degisti', [N8nWebhookController::class, 'ilanStatusChanged']);
});
```

#### **Controller:**

```php
// app/Http/Controllers/Api/N8nWebhookController.php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ilan;
use App\Models\Kisi;
use Illuminate\Http\Request;

class N8nWebhookController extends Controller
{
    public function createIlan(Request $request)
    {
        $validated = $request->validate([
            'baslik' => 'required|string',
            'fiyat' => 'required|numeric',
            'kategori_id' => 'required|exists:ilan_kategorileri,id',
        ]);

        $ilan = Ilan::create($validated);

        return response()->json([
            'success' => true,
            'data' => $ilan,
        ], 201);
    }

    public function updateKisi(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:kisiler,id',
            'telefon' => 'nullable|string',
            'email' => 'nullable|email',
        ]);

        $kisi = Kisi::findOrFail($validated['id']);
        $kisi->update($validated);

        return response()->json([
            'success' => true,
            'data' => $kisi,
        ]);
    }

    public function ilanStatusChanged(Request $request)
    {
        $validated = $request->validate([
            'ilan_id' => 'required|exists:ilanlar,id',
            'status' => 'required|in:Aktif,Pasif,Satıldı',
        ]);

        $ilan = Ilan::findOrFail($validated['ilan_id']);
        $ilan->update(['status' => $validated['status']]);

        return response()->json(['success' => true]);
    }
}
```

#### **Middleware (Güvenlik):**

```php
// app/Http/Middleware/N8nWebhookMiddleware.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class N8nWebhookMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('X-N8N-Webhook-Token');

        if ($token !== config('services.n8n.webhook_token')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
```

---

## 🎯 Örnek Workflow'lar

### **1. Yeni İlan → Telegram Bildirimi**

n8n Workflow:

```
1. Webhook Trigger (Laravel'den)
   ↓
2. Filter (Fiyat > 500.000 TL)
   ↓
3. Telegram Node
   → "Yeni İlan: {baslik}"
   → "Fiyat: {fiyat} TL"
   → "Link: https://yalihanemlak.com/ilan/{id}"
```

### **2. Yeni Kişi → Google Sheets**

```
1. Webhook Trigger
   ↓
2. Google Sheets Node
   → Append row to "Müşteriler" sheet
   → [Ad, Soyad, Email, Telefon, Tarih]
```

### **3. Günlük Rapor (Cron)**

```
1. Cron Node (Her gün 09:00)
   ↓
2. HTTP Request (Laravel API)
   → GET /api/reports/daily
   ↓
3. Email Node
   → Konu: "Günlük Rapor - {tarih}"
   → İçerik: API response data
```

### **4. WhatsApp Otomatik Mesaj**

```
1. Webhook Trigger (Yeni Randevu)
   ↓
2. Wait Node (1 saat bekle)
   ↓
3. HTTP Request (Laravel API)
   → Randevu durumunu kontrol et
   ↓
4. IF Node (Durum: Beklemede)
   ↓
5. WhatsApp Business API
   → Hatırlatma mesajı gönder
```

---

## 🔒 Güvenlik ve Best Practices

### **1. Webhook Token Kullanımı**

```php
$request->header('X-N8N-Webhook-Token') === config('services.n8n.webhook_token')
```

### **2. IP Whitelist (Production)**

```php
// middleware
public function handle(Request $request, Closure $next)
{
    $allowedIps = explode(',', config('services.n8n.allowed_ips'));

    if (!in_array($request->ip(), $allowedIps)) {
        abort(403, 'Forbidden');
    }

    return $next($request);
}
```

### **3. Rate Limiting**

```php
Route::middleware('throttle:60,1')->group(function () {
    Route::post('/webhooks/n8n/*', ...);
});
```

### **4. HTTPS Kullanımı (Production)**

```bash
# nginx config
server {
    listen 443 ssl;
    server_name n8n.yalihanemlak.com;

    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;

    location / {
        proxy_pass http://localhost:5678;
    }
}
```

---

## 📊 n8n ile Yapılabilecekler

### **Emlak Otomasyonları:**

1. **Yeni İlan Yayınlama:**
    - Laravel → n8n → Sahibinden API
    - Laravel → n8n → Hürriyet Emlak API

2. **Müşteri Takibi:**
    - Yeni kişi → Google Sheets
    - Yeni kişi → CRM sistemi sync

3. **Bildirimler:**
    - Yeni ilan → Telegram/WhatsApp
    - Fiyat değişikliği → Email
    - Randevu hatırlatma → SMS

4. **Raporlama:**
    - Günlük satış raporu → Email
    - Haftalık performans → Slack

5. **Veri Entegrasyonu:**
    - İlanları otomatik sosyal medyada paylaş
    - Google My Business güncelle
    - Facebook & Instagram reklam oluştur

---

## 🚀 Hızlı Başlangıç

### **1. n8n Kurulumu:**

```bash
cd /Users/macbookpro/Projects/yalihanemlakwarp
docker run -d --name n8n -p 5678:5678 n8nio/n8n
```

### **2. Laravel Servis:**

```bash
php artisan make:service N8nService
```

### **3. İlk Webhook:**

n8n'de:

1. "+" → Webhook node ekle
2. Webhook URL'i kopyala
3. Laravel'de:

```php
Http::post('http://localhost:5678/webhook/test', ['data' => 'test']);
```

### **4. Test:**

```bash
php artisan tinker
app(App\Services\N8nService::class)->sendNotification('test', ['message' => 'Hello n8n!']);
```

---

## 📚 Kaynaklar

- [n8n Documentation](https://docs.n8n.io)
- [n8n Community](https://community.n8n.io)
- [n8n Templates](https://n8n.io/workflows)
- [Laravel HTTP Client](https://laravel.com/docs/http-client)

---

**Son Güncelleme:** 10 Ekim 2025
**Context7 Uyumlu:** ✅
