# 🎯 Otonom Fiyat Değişim Takibi ve n8n Entegrasyonu

**Tarih:** 2025-11-27  
**Durum:** ✅ Tamamlandı  
**Context7 Uyumluluk:** %100

---

## 📋 Genel Bakış

İlan fiyatı değiştiğinde otomatik olarak n8n'e bildirim gönderen sistem. n8n workflow'u multi-channel (Telegram, WhatsApp, Email) bildirimleri tetikleyebilir.

## 🔄 İş Akışı

```
1. İlan fiyatı değiştiğinde → IlanObserver::updating()
2. Fiyat geçmişi kaydedilir → IlanPriceHistory::create()
3. Event fırlatılır → IlanPriceChanged event
4. Listener tetiklenir → NotifyN8nOnIlanPriceChanged
5. Job dispatch edilir → NotifyN8nAboutIlanPriceChange (Queue)
6. n8n webhook'a POST → Multi-channel bildirimi için hazır
```

## 📊 Oluşturulan Dosyalar

### 1. Event
**Dosya:** `app/Events/IlanPriceChanged.php`

**Özellikler:**
- İlan modeli
- Eski fiyat (oldPrice)
- Yeni fiyat (newPrice)
- Para birimi (currency)

### 2. Job
**Dosya:** `app/Jobs/NotifyN8nAboutIlanPriceChange.php`

**Özellikler:**
- Queue-based async processing
- n8n webhook'a HTTP POST isteği
- Multi-channel bildirim desteği
- Comprehensive error handling

### 3. Listener
**Dosya:** `app/Listeners/NotifyN8nOnIlanPriceChanged.php`

**Özellikler:**
- Event'i dinler
- Job'ı dispatch eder
- ShouldQueue interface (async processing)

### 4. Observer
**Dosya:** `app/Observers/IlanObserver.php`

**Güncellenen Metod:** `updating()`

- Fiyat değişikliğini tespit eder
- Fiyat geçmişi kaydeder
- `IlanPriceChanged` event'ini fırlatır

## 📦 n8n Payload Yapısı

```json
{
  "event": "IlanPriceChanged",
  "ilan_id": 123,
  "ilan": {
    "id": 123,
    "baslik": "Bodrum Yalıkavak'ta Denize Sıfır Villa",
    "fiyat": 10000000,
    "para_birimi": "TRY",
    "il_adi": "Muğla",
    "ilce_adi": "Bodrum",
    "mahalle_adi": "Yalıkavak",
    "status": "Aktif",
    "url": "https://app.yalihanemlak.com.tr/admin/ilanlar/123"
  },
  "price_change": {
    "old_price": 12000000,
    "new_price": 10000000,
    "currency": "TRY",
    "change_percent": -16.67,
    "is_increase": false,
    "is_decrease": true
  },
  "notification_channels": ["telegram", "whatsapp", "email"],
  "timestamp": "2025-11-27T10:00:00.000000Z",
  "metadata": {
    "source": "laravel",
    "version": "1.0.0"
  }
}
```

## 🔧 Yapılandırma

### Environment Variables

`.env` dosyasına ekleyin:

```env
N8N_ILAN_PRICE_CHANGED_WEBHOOK=https://n8n.yalihanemlak.com.tr/webhook/ilan-fiyat-degisti
N8N_WEBHOOK_SECRET=your_secret_key_here
N8N_TIMEOUT=30
```

### Config Dosyası

`config/services.php` içinde:

```php
'n8n' => [
    'ilan_price_changed_webhook_url' => env('N8N_ILAN_PRICE_CHANGED_WEBHOOK', 'https://n8n.yalihanemlak.com.tr/webhook/ilan-fiyat-degisti'),
    'webhook_secret' => env('N8N_WEBHOOK_SECRET', ''),
    'timeout' => env('N8N_TIMEOUT', 30),
],
```

## 📱 Multi-Channel Bildirim Desteği

Payload içindeki `notification_channels` alanı ile n8n workflow'unda hangi kanallara bildirim gönderileceği belirlenir:

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

### Senaryo 1: Fiyat İndirimi Bildirimi
```
Eski Fiyat: 12.000.000 TL
Yeni Fiyat: 10.000.000 TL
Değişim: %16.67 azalma

→ n8n workflow:
   - Telegram: "💰 Fırsat! İlan fiyatı %16.67 düştü..."
   - WhatsApp: "Yeni fiyat: 10.000.000 TL"
   - Email: Detaylı fiyat değişim raporu
```

### Senaryo 2: Fiyat Artışı Bildirimi
```
Eski Fiyat: 10.000.000 TL
Yeni Fiyat: 12.000.000 TL
Değişim: %20 artış

→ n8n workflow:
   - Email: "İlan fiyatı güncellendi: 12.000.000 TL"
```

## ⚙️ EventServiceProvider Yapılandırması

`app/Providers/EventServiceProvider.php` içinde:

```php
protected $listen = [
    \App\Events\IlanPriceChanged::class => [
        \App\Listeners\NotifyN8nOnIlanPriceChanged::class,
    ],
];
```

## 🔍 Error Handling

- Webhook URL yapılandırılmamışsa → Warning log, job sonlanır
- İlan bulunamazsa → Warning log, job sonlanır
- n8n webhook başarısız → Error log, job retry edilir (queue'da)
- Exception → Error log, job retry edilir

## 📊 Performans

- **Queue-based:** Async processing, kullanıcı deneyimini etkilemez
- **Timeout:** 30 saniye (configurable)
- **Retry:** Queue yapılandırmasına göre otomatik retry

## 🚀 Test Senaryosu

```php
// İlan fiyatını güncelle
$ilan = Ilan::find(1);
$ilan->fiyat = 10000000; // Yeni fiyat
$ilan->save(); // Observer otomatik tetiklenir

// Sonuç:
// 1. IlanPriceHistory kaydı oluşturulur
// 2. IlanPriceChanged event fırlatılır
// 3. NotifyN8nOnIlanPriceChanged listener çalışır
// 4. NotifyN8nAboutIlanPriceChange job queue'ya eklenir
// 5. Job çalıştığında n8n webhook'a POST isteği gönderilir
```

## 📚 Referanslar

- **Event:** `app/Events/IlanPriceChanged.php`
- **Job:** `app/Jobs/NotifyN8nAboutIlanPriceChange.php`
- **Listener:** `app/Listeners/NotifyN8nOnIlanPriceChanged.php`
- **Observer:** `app/Observers/IlanObserver.php`
- **Config:** `config/services.php`
- **n8n Strategy:** `yalihan-bekci/knowledge/N8N_DEEP_INTEGRATION_STRATEGY_2025-01-15.md`

## ✅ Context7 Uyumluluk

- ✅ Queue-based async processing
- ✅ Comprehensive error handling
- ✅ Logging (LogService)
- ✅ Config-based webhook URL
- ✅ Environment variables support
- ✅ Multi-channel notification support






