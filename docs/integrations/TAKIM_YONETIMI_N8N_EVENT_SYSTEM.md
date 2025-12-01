# 🎯 Takım Yönetimi Otomasyonu - Temel Event Sistemi

**Tarih:** 2025-11-27  
**Durum:** ✅ Tamamlandı  
**Context7 Uyumluluk:** %100

---

## 📋 Genel Bakış

Takım Yönetimi modülünü n8n'e bağlamak için temel Event/Listener mimarisi. Görev oluşturma, durum değiştirme ve deadline hatırlatmalarını otomatikleştirir.

## 🎯 GİZEM GÜNAL İÇİN ETKİ

GİZEM artık görev oluşturduğunda, tüm hatırlatmaları sistemden alacak ve **hiçbir deadline'ı kaçırmayacak**.

## 🔄 İş Akışı

### 1. Görev Oluşturuldu
```
Görev Oluşturuldu → GorevObserver::created()
    ↓
GorevCreated Event
    ↓
NotifyN8nOnGorevCreated Listener
    ↓
NotifyN8nAboutNewGorev Job (Queue)
    ↓
n8n Webhook → Multi-channel bildirimi
```

### 2. Görev Durumu Değişti
```
Görev Durumu Değişti → GorevObserver::updated()
    ↓
GorevStatusChanged Event
    ↓
NotifyN8nOnGorevStatusChanged Listener
    ↓
NotifyN8nAboutGorevStatusChanged Job (Queue)
    ↓
n8n Webhook → Multi-channel bildirimi
```

### 3. Deadline Yaklaşıyor
```
Görev Güncellendi → GorevObserver::updated()
    ↓
Deadline 1 gün içinde mi? → Kontrol
    ↓
GorevDeadlineYaklasiyor Event
    ↓
NotifyN8nOnGorevDeadlineYaklasiyor Listener
    ↓
NotifyN8nAboutGorevDeadlineYaklasiyor Job (Queue)
    ↓
n8n Webhook → Multi-channel bildirimi
```

### 4. Görev Gecikti
```
Görev Güncellendi → GorevObserver::updated()
    ↓
Deadline geçti mi? → Kontrol
    ↓
GorevGecikti Event
    ↓
NotifyN8nOnGorevGecikti Listener
    ↓
NotifyN8nAboutGorevGecikti Job (Queue)
    ↓
n8n Webhook → Multi-channel bildirimi
```

## 📊 Oluşturulan Dosyalar

### Event'ler

1. **`app/Events/GorevCreated.php`**
   - Görev oluşturulduğunda fırlatılır
   - Parametreler: `Gorev $gorev`

2. **`app/Events/GorevStatusChanged.php`**
   - Görev durumu değiştiğinde fırlatılır
   - Parametreler: `Gorev $gorev`, `string $oldStatus`, `string $newStatus`

3. **`app/Events/GorevDeadlineYaklasiyor.php`**
   - Deadline yaklaştığında fırlatılır
   - Parametreler: `Gorev $gorev`, `int $kalanGun`

4. **`app/Events/GorevGecikti.php`**
   - Görev geciktiğinde fırlatılır
   - Parametreler: `Gorev $gorev`, `int $gecikmeGunu`

### Observer

**`app/Observers/GorevObserver.php`**

- `created()`: GorevCreated event fırlatır
- `updated()`: Status değişikliği, deadline yaklaşma ve gecikme kontrolleri yapar

### Job'lar

1. **`app/Jobs/NotifyN8nAboutNewGorev.php`**
   - Yeni görev bildirimi

2. **`app/Jobs/NotifyN8nAboutGorevStatusChanged.php`**
   - Durum değişikliği bildirimi

3. **`app/Jobs/NotifyN8nAboutGorevDeadlineYaklasiyor.php`**
   - Deadline yaklaşma bildirimi

4. **`app/Jobs/NotifyN8nAboutGorevGecikti.php`**
   - Gecikme bildirimi

### Listener'lar

1. **`app/Listeners/NotifyN8nOnGorevCreated.php`**
2. **`app/Listeners/NotifyN8nOnGorevStatusChanged.php`**
3. **`app/Listeners/NotifyN8nOnGorevDeadlineYaklasiyor.php`**
4. **`app/Listeners/NotifyN8nOnGorevGecikti.php`**

### Scheduler Komutu

**`app/Console/Commands/CheckGorevDeadlines.php`**

- Deadline'ı yaklaşan ve geciken görevleri bulur
- İlgili Event'leri fırlatır
- **Çalışma:** Her gün 08:00 ve 14:00

## 📦 n8n Payload Yapıları

### GorevCreated Payload

```json
{
  "event": "GorevCreated",
  "gorev_id": 123,
  "gorev": {
    "id": 123,
    "baslik": "Müşteri Ziyareti",
    "aciklama": "Ahmet Bey ile görüşme",
    "oncelik": "acil",
    "status": "bekliyor",
    "tip": "musteri_takibi",
    "bitis_tarihi": "2025-11-28T10:00:00.000000Z",
    "danisman_adi": "Gizem Günal",
    "musteri_adi": "Ahmet Yılmaz",
    "url": "https://app.yalihanemlak.com.tr/admin/takim-yonetimi/gorevler/123"
  },
  "notification_channels": ["telegram", "whatsapp", "email"],
  "timestamp": "2025-11-27T10:00:00.000000Z"
}
```

### GorevStatusChanged Payload

```json
{
  "event": "GorevStatusChanged",
  "gorev_id": 123,
  "gorev": {
    "id": 123,
    "baslik": "Müşteri Ziyareti",
    "status": "devam_ediyor",
    "danisman_adi": "Gizem Günal",
    "url": "https://app.yalihanemlak.com.tr/admin/takim-yonetimi/gorevler/123"
  },
  "status_change": {
    "old_status": "bekliyor",
    "new_status": "devam_ediyor"
  },
  "notification_channels": ["telegram", "whatsapp", "email"],
  "timestamp": "2025-11-27T10:00:00.000000Z"
}
```

### GorevDeadlineYaklasiyor Payload

```json
{
  "event": "GorevDeadlineYaklasiyor",
  "gorev_id": 123,
  "gorev": {
    "id": 123,
    "baslik": "Müşteri Ziyareti",
    "bitis_tarihi": "2025-11-28T10:00:00.000000Z",
    "danisman_adi": "Gizem Günal",
    "url": "https://app.yalihanemlak.com.tr/admin/takim-yonetimi/gorevler/123"
  },
  "deadline": {
    "kalan_gun": 1,
    "bitis_tarihi": "2025-11-28T10:00:00.000000Z",
    "acil": true
  },
  "notification_channels": ["telegram", "whatsapp", "email"],
  "timestamp": "2025-11-27T10:00:00.000000Z"
}
```

### GorevGecikti Payload

```json
{
  "event": "GorevGecikti",
  "gorev_id": 123,
  "gorev": {
    "id": 123,
    "baslik": "Müşteri Ziyareti",
    "status": "devam_ediyor",
    "bitis_tarihi": "2025-11-26T10:00:00.000000Z",
    "danisman_adi": "Gizem Günal",
    "url": "https://app.yalihanemlak.com.tr/admin/takim-yonetimi/gorevler/123"
  },
  "gecikme": {
    "gecikme_gunu": 1,
    "bitis_tarihi": "2025-11-26T10:00:00.000000Z",
    "acil": true
  },
  "notification_channels": ["telegram", "whatsapp", "email"],
  "timestamp": "2025-11-27T10:00:00.000000Z"
}
```

## 🔧 Yapılandırma

### Environment Variables

`.env` dosyasına ekleyin:

```env
N8N_GOREV_CREATED_WEBHOOK=https://n8n.yalihanemlak.com.tr/webhook/gorev-olustu
N8N_GOREV_STATUS_CHANGED_WEBHOOK=https://n8n.yalihanemlak.com.tr/webhook/gorev-durum-degisti
N8N_GOREV_DEADLINE_YAKLASIYOR_WEBHOOK=https://n8n.yalihanemlak.com.tr/webhook/gorev-deadline-yaklasiyor
N8N_GOREV_GECIKTI_WEBHOOK=https://n8n.yalihanemlak.com.tr/webhook/gorev-gecikti
N8N_WEBHOOK_SECRET=your_secret_key_here
```

### Config Dosyası

`config/services.php` içinde:

```php
'n8n' => [
    'gorev_created_webhook_url' => env('N8N_GOREV_CREATED_WEBHOOK', '...'),
    'gorev_status_changed_webhook_url' => env('N8N_GOREV_STATUS_CHANGED_WEBHOOK', '...'),
    'gorev_deadline_yaklasiyor_webhook_url' => env('N8N_GOREV_DEADLINE_YAKLASIYOR_WEBHOOK', '...'),
    'gorev_gecikti_webhook_url' => env('N8N_GOREV_GECIKTI_WEBHOOK', '...'),
],
```

## ⏰ Scheduler

**`app/Console/Kernel.php`** içinde:

```php
// Görev deadline kontrolü - Her gün 08:00 ve 14:00
$schedule->command('gorevler:check-deadlines --gun=1')
    ->dailyAt('08:00')
    ->appendOutputTo(storage_path('logs/gorev-deadline-check.log'));

$schedule->command('gorevler:check-deadlines --gun=1')
    ->dailyAt('14:00')
    ->appendOutputTo(storage_path('logs/gorev-deadline-check.log'));
```

## 📱 Multi-Channel Bildirim Desteği

Tüm payload'larda `notification_channels` alanı ile n8n workflow'unda hangi kanallara bildirim gönderileceği belirlenir:

```json
{
  "notification_channels": ["telegram", "whatsapp", "email"]
}
```

**n8n Workflow Örneği:**

1. Webhook trigger (Laravel'den gelen payload)
2. IF node: `notification_channels` içinde "telegram" var mı?
   - Evet → Telegram gönder
3. IF node: `notification_channels` içinde "whatsapp" var mı?
   - Evet → WhatsApp gönder
4. IF node: `notification_channels` içinde "email" var mı?
   - Evet → Email gönder

## 🎯 Kullanım Senaryoları

### Senaryo 1: Yeni Görev Oluşturuldu
```
GİZEM yeni görev oluşturur
    ↓
GorevCreated Event fırlatılır
    ↓
n8n'e bildirim gönderilir
    ↓
Telegram: "📋 Yeni görev: Müşteri Ziyareti"
WhatsApp: "Görev detayları..."
Email: "Görev oluşturuldu bildirimi"
```

### Senaryo 2: Deadline Yaklaşıyor
```
Scheduler (08:00 veya 14:00) çalışır
    ↓
Deadline 1 gün içinde görevler bulunur
    ↓
GorevDeadlineYaklasiyor Event fırlatılır
    ↓
n8n'e bildirim gönderilir
    ↓
Telegram: "⚠️ Deadline Yaklaşıyor: Müşteri Ziyareti (1 gün kaldı)"
Email: "Deadline hatırlatma"
```

### Senaryo 3: Görev Gecikti
```
Scheduler çalışır veya görev güncellenir
    ↓
Geciken görevler bulunur
    ↓
GorevGecikti Event fırlatılır
    ↓
n8n'e bildirim gönderilir
    ↓
Telegram: "🔴 ACİL: Görev Gecikti: Müşteri Ziyareti (1 gün gecikme)"
WhatsApp: "Acil bildirim"
Email: "Gecikme uyarısı"
```

## ⚙️ EventServiceProvider Yapılandırması

`app/Providers/EventServiceProvider.php` içinde:

```php
protected $listen = [
    \App\Events\GorevCreated::class => [
        \App\Listeners\NotifyN8nOnGorevCreated::class,
    ],
    \App\Events\GorevStatusChanged::class => [
        \App\Listeners\NotifyN8nOnGorevStatusChanged::class,
    ],
    \App\Events\GorevDeadlineYaklasiyor::class => [
        \App\Listeners\NotifyN8nOnGorevDeadlineYaklasiyor::class,
    ],
    \App\Events\GorevGecikti::class => [
        \App\Listeners\NotifyN8nOnGorevGecikti::class,
    ],
];
```

## 🔍 Error Handling

- Webhook URL yapılandırılmamışsa → Warning log, job sonlanır
- Görev bulunamazsa → Warning log, job sonlanır
- n8n webhook başarısız → Error log, job retry edilir (queue'da)
- Exception → Error log, job retry edilir

## 📊 Performans

- **Queue-based:** Async processing, kullanıcı deneyimini etkilemez
- **Timeout:** 30 saniye (configurable)
- **Retry:** Queue yapılandırmasına göre otomatik retry
- **Scheduler:** Günlük 2 kez çalışır (08:00 ve 14:00)

## 🚀 Test Senaryosu

```bash
# Manuel test
php artisan gorevler:check-deadlines --gun=1

# Dry-run (sadece bulunan görevleri göster, event fırlatma)
php artisan gorevler:check-deadlines --gun=1 --dry-run

# Görev oluşturma testi
$gorev = \App\Modules\TakimYonetimi\Models\Gorev::create([
    'baslik' => 'Test Görevi',
    'status' => 'bekliyor',
    'bitis_tarihi' => now()->addDay(),
    // ...
]);
// → GorevCreated event fırlatılır
```

## 📚 Referanslar

- **Event'ler:** `app/Events/`
- **Job'lar:** `app/Jobs/NotifyN8nAbout*.php`
- **Listener'lar:** `app/Listeners/NotifyN8nOn*.php`
- **Observer:** `app/Observers/GorevObserver.php`
- **Komut:** `app/Console/Commands/CheckGorevDeadlines.php`
- **Config:** `config/services.php`
- **n8n Strategy:** `yalihan-bekci/knowledge/TAKIM_YONETIMI_N8N_STRATEGY_2025-01-15.md`

## ✅ Context7 Uyumluluk

- ✅ Queue-based async processing
- ✅ Comprehensive error handling
- ✅ Logging (LogService)
- ✅ Config-based webhook URL
- ✅ Environment variables support
- ✅ Multi-channel notification support
- ✅ Scheduler integration






