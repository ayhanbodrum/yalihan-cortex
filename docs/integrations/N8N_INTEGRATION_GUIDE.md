# 🔄 n8n Entegrasyon Rehberi - Yalıhan Emlak

**Tarih:** 2025-11-27  
**Durum:** ✅ Aktif Sistemler  
**Context7 Uyumluluk:** %100

---

## 📋 Genel Bakış

Bu rehber, Laravel uygulamasından n8n'e gönderilen webhook'ları nasıl işleyeceğinizi ve multi-channel (Telegram, WhatsApp, Email) bildirimlerini nasıl kurulacağını açıklar.

## 🏗️ Sistem Mimarisi

```
Laravel Application
    ↓
Event/Observer → Event Fired
    ↓
Listener → Job Dispatch (Queue)
    ↓
HTTP POST → n8n Webhook URL
    ↓
n8n Workflow
    ├── IF Node (Channel Check)
    ├── Telegram Node
    ├── WhatsApp Node
    └── Email Node
```

## 🎯 Aktif n8n Entegrasyonları

### 1. İlan Fiyat Değişikliği
- **Webhook URL:** `N8N_ILAN_PRICE_CHANGED_WEBHOOK`
- **Event:** `IlanPriceChanged`
- **Payload:** İlan detayları + Fiyat değişimi

### 2. Takım Yönetimi - Görev Oluşturuldu
- **Webhook URL:** `N8N_GOREV_CREATED_WEBHOOK`
- **Event:** `GorevCreated`
- **Payload:** Görev detayları

### 3. Takım Yönetimi - Görev Durumu Değişti
- **Webhook URL:** `N8N_GOREV_STATUS_CHANGED_WEBHOOK`
- **Event:** `GorevStatusChanged`
- **Payload:** Görev detayları + Durum değişimi

### 4. Takım Yönetimi - Deadline Yaklaşıyor
- **Webhook URL:** `N8N_GOREV_DEADLINE_YAKLASIYOR_WEBHOOK`
- **Event:** `GorevDeadlineYaklasiyor`
- **Payload:** Görev detayları + Deadline bilgisi

### 5. Takım Yönetimi - Görev Gecikti
- **Webhook URL:** `N8N_GOREV_GECIKTI_WEBHOOK`
- **Event:** `GorevGecikti`
- **Payload:** Görev detayları + Gecikme bilgisi

## 📦 Payload Yapısı

### Ortak Payload Formatı

Tüm webhook'lar aynı temel yapıyı kullanır:

```json
{
  "event": "EventAdi",
  "xxx_id": 123,
  "xxx": {
    // Detaylı bilgiler
  },
  "notification_channels": ["telegram", "whatsapp", "email"],
  "timestamp": "2025-11-27T10:00:00.000000Z",
  "metadata": {
    "source": "laravel",
    "version": "1.0.0"
  }
}
```

## 🔧 n8n Workflow Kurulumu

### Workflow 1: İlan Fiyat Değişikliği → Multi-Channel Bildirim

#### Adım 1: Webhook Trigger Oluştur

1. **n8n'de yeni workflow oluştur**
2. **"Webhook" node'u ekle**
3. **Settings:**
   - **HTTP Method:** POST
   - **Path:** `/webhook/ilan-fiyat-degisti`
   - **Authentication:** Header Auth (veya Query Auth)
   - **Header Name:** `X-N8N-SECRET`
   - **Header Value:** `your_secret_key` (Laravel'den gelen)

#### Adım 2: Payload Validasyonu

1. **"IF" node ekle** (Webhook'dan sonra)
2. **Condition:**
   ```
   {{ $json.event }} === "IlanPriceChanged"
   ```

#### Adım 3: Telegram Bildirimi

1. **"IF" node ekle** (Validation'dan sonra)
2. **Condition:**
   ```javascript
   {{ $json.notification_channels }}.includes("telegram")
   ```
3. **"Telegram" node ekle** (IF içinde)
   - **Resource:** Send Message
   - **Chat ID:** Hedef Telegram chat ID
   - **Text:** 
   ```
   💰 Fiyat Değişikliği!
   
   📍 {{ $json.ilan.baslik }}
   {{ $json.ilan.il_adi }} / {{ $json.ilan.ilce_adi }}
   
   💵 Eski Fiyat: {{ $json.price_change.old_price }} {{ $json.price_change.currency }}
   💵 Yeni Fiyat: {{ $json.price_change.new_price }} {{ $json.price_change.currency }}
   {{#if $json.price_change.change_percent}}
   📊 Değişim: {{ $json.price_change.change_percent }}%
   {{/if}}
   
   🔗 {{ $json.ilan.url }}
   ```

#### Adım 4: WhatsApp Bildirimi

1. **"IF" node ekle** (Telegram'dan sonra)
2. **Condition:**
   ```javascript
   {{ $json.notification_channels }}.includes("whatsapp")
   ```
3. **"WhatsApp Business API" node ekle** (IF içinde)
   - Mesaj formatını Telegram ile benzer şekilde yapılandır

#### Adım 5: Email Bildirimi

1. **"IF" node ekle** (WhatsApp'tan sonra)
2. **Condition:**
   ```javascript
   {{ $json.notification_channels }}.includes("email")
   ```
3. **"Email Send" node ekle** (IF içinde)
   - **To:** Hedef email adresleri
   - **Subject:** `Fiyat Değişikliği: {{ $json.ilan.baslik }}`
   - **Text/HTML:** Detaylı email içeriği

---

### Workflow 2: Görev Oluşturuldu → Multi-Channel Bildirim

#### Adım 1: Webhook Trigger

- **Path:** `/webhook/gorev-olustu`
- **Method:** POST

#### Adım 2: Telegram Bildirimi

**Mesaj Formatı:**
```
📋 Yeni Görev Oluşturuldu!

🎯 Görev: {{ $json.gorev.baslik }}
📝 Açıklama: {{ $json.gorev.aciklama }}

👤 Danışman: {{ $json.gorev.danisman_adi }}
⏰ Deadline: {{ $json.gorev.bitis_tarihi }}
🎯 Öncelik: {{ $json.gorev.oncelik }}
📊 Durum: {{ $json.gorev.status }}

🔗 {{ $json.gorev.url }}
```

#### Adım 3: WhatsApp Bildirimi

- Similar format, shorter message

#### Adım 4: Email Bildirimi

- HTML email template with full details

---

### Workflow 3: Görev Deadline Yaklaşıyor → Hatırlatma

#### Telegram Mesajı:
```
⚠️ Deadline Yaklaşıyor!

🎯 Görev: {{ $json.gorev.baslik }}
⏰ Deadline: {{ $json.deadline.bitis_tarihi }}
⏳ Kalan Süre: {{ $json.deadline.kalan_gun }} gün

👤 Danışman: {{ $json.gorev.danisman_adi }}

{{#if $json.deadline.acil}}
🚨 ACİL: Sadece 1 gün kaldı!
{{/if}}

🔗 {{ $json.gorev.url }}
```

---

### Workflow 4: Görev Gecikti → Acil Bildirim

#### Telegram Mesajı:
```
🔴 ACİL: Görev Gecikti!

🎯 Görev: {{ $json.gorev.baslik }}
⏰ Deadline: {{ $json.gecikme.bitis_tarihi }}
📅 Gecikme: {{ $json.gecikme.gecikme_gunu }} gün

👤 Danışman: {{ $json.gorev.danisman_adi }}

🚨 Lütfen görevi tamamlayın veya deadline'ı güncelleyin!

🔗 {{ $json.gorev.url }}
```

---

## 🔐 Güvenlik Yapılandırması

### Header Authentication

Laravel'den gönderilen tüm isteklerde `X-N8N-SECRET` header'ı bulunur:

```php
->withHeaders([
    'Content-Type' => 'application/json',
    'X-N8N-SECRET' => config('services.n8n.webhook_secret', ''),
])
```

### n8n Webhook Yapılandırması

1. **Webhook node Settings:**
   - **Authentication:** Header Auth
   - **Name:** `X-N8N-SECRET`
   - **Value:** `.env` dosyasındaki `N8N_WEBHOOK_SECRET` değeri

### Secret Key Yönetimi

```env
# .env dosyası
N8N_WEBHOOK_SECRET=your_super_secret_key_here_min_32_chars
```

**Güvenlik İpuçları:**
- ✅ Minimum 32 karakter
- ✅ Rastgele oluşturulmuş
- ✅ Production'da mutlaka ayarlı
- ✅ Laravel ve n8n'de aynı olmalı

## 📱 Multi-Channel Seçimi

### Payload'dan Channel Seçimi

```javascript
// n8n IF node condition örneği
const channels = $json.notification_channels || [];

// Telegram kontrolü
channels.includes("telegram")

// WhatsApp kontrolü
channels.includes("whatsapp")

// Email kontrolü
channels.includes("email")
```

### Dinamik Channel Seçimi

n8n workflow'unda `Set` node kullanarak channel'ları dinamik olarak filtreleyebilirsiniz:

```javascript
// Set node: Filter Channels
const channels = $json.notification_channels || [];
return channels.map(channel => {
  return {
    channel: channel,
    enabled: true
  };
});
```

## 🎨 Mesaj Formatı Şablonları

### Telegram Formatı

```
💼 Yalıhan Emlak - {{ $json.event }}

{{#if $json.gorev}}
📋 Görev: {{ $json.gorev.baslik }}
{{/if}}

{{#if $json.ilan}}
🏠 İlan: {{ $json.ilan.baslik }}
{{/if}}

🔗 Detaylar: {{ $json.xxx.url }}
```

### WhatsApp Formatı

```
*{{ $json.event }}*

{{ $json.xxx.baslik }}

Detaylar: {{ $json.xxx.url }}
```

### Email HTML Template

```html
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { background: #0066cc; color: white; padding: 20px; }
        .content { padding: 20px; }
        .button { background: #0066cc; color: white; padding: 10px 20px; text-decoration: none; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Yalıhan Emlak</h1>
    </div>
    <div class="content">
        <h2>{{ $json.event }}</h2>
        <!-- İçerik -->
        <a href="{{ $json.xxx.url }}" class="button">Detayları Gör</a>
    </div>
</body>
</html>
```

## 🔄 Workflow Yapısı Önerileri

### Önerilen Workflow Yapısı

```
1. Webhook Trigger
   ↓
2. Validate Payload (IF)
   ↓
3. Set Variables (Set)
   ↓
4. IF: Telegram? → Telegram Send
   ↓
5. IF: WhatsApp? → WhatsApp Send
   ↓
6. IF: Email? → Email Send
   ↓
7. Response Node (200 OK)
```

### Error Handling

Her workflow'da error handling ekleyin:

```
1. Webhook Trigger
   ↓
2. Try-Catch (Error Trigger)
   ↓
3. On Error → Log Error → Email Admin
```

## 📊 Test Senaryoları

### Test 1: Manuel Webhook Testi

```bash
curl -X POST https://n8n.yalihanemlak.com.tr/webhook/gorev-olustu \
  -H "Content-Type: application/json" \
  -H "X-N8N-SECRET: your_secret_key" \
  -d '{
    "event": "GorevCreated",
    "gorev_id": 123,
    "gorev": {
      "id": 123,
      "baslik": "Test Görevi",
      "danisman_adi": "Gizem Günal"
    },
    "notification_channels": ["telegram", "whatsapp", "email"]
  }'
```

### Test 2: Laravel'den Test

```php
// Test job'ı manuel çalıştır
dispatch(new \App\Jobs\NotifyN8nAboutNewGorev(123));
```

## 🔍 Debugging

### n8n Workflow Debugging

1. **Workflow'u Test Mode'da çalıştır**
2. **Her node'un çıktısını incele**
3. **Console log ekle:**
   ```javascript
   console.log('Payload:', $json);
   ```

### Laravel Debugging

```php
// LogService ile loglama
LogService::info('n8n webhook payload', [
    'payload' => $payload,
    'webhook_url' => $webhookUrl,
]);
```

## 📚 n8n Node Referansları

### Telegram Node
- **Provider:** Telegram Bot API
- **Token:** Telegram Bot Token
- **Chat ID:** Hedef chat ID veya dinamik

### WhatsApp Node
- **Provider:** WhatsApp Business API
- **Phone Number ID:** WhatsApp Business Phone Number
- **Access Token:** WhatsApp API Token

### Email Node
- **Provider:** SMTP veya SendGrid/Postmark
- **From:** `noreply@yalihanemlak.com.tr`
- **To:** Dinamik veya sabit liste

## 🎯 En İyi Uygulamalar

1. **Error Handling:** Her workflow'da try-catch ekleyin
2. **Logging:** Her adımı loglayın
3. **Rate Limiting:** n8n'de rate limiting kullanın
4. **Retry Logic:** Başarısız istekler için retry ekleyin
5. **Monitoring:** Workflow çalışma durumunu izleyin
6. **Secret Management:** Secret'ları güvenli tutun
7. **Test Environment:** Production öncesi test edin

## 🚀 Deployment Checklist

- [ ] n8n webhook URL'leri yapılandırıldı
- [ ] Webhook secret key ayarlandı
- [ ] n8n workflow'ları oluşturuldu ve test edildi
- [ ] Telegram bot token ayarlandı
- [ ] WhatsApp Business API ayarlandı
- [ ] Email SMTP ayarlandı
- [ ] Test istekleri gönderildi
- [ ] Error handling eklendi
- [ ] Monitoring kuruldu

## 📖 Örnek Workflow JSON Export

Tam çalışır workflow örnekleri için `docs/n8n-workflows/` klasörüne bakın.

---

## 🔗 İlgili Dokümantasyon

- **İlan Fiyat Değişikliği:** `docs/integrations/ILAN_PRICE_CHANGE_N8N_INTEGRATION.md`
- **Takım Yönetimi:** `docs/integrations/TAKIM_YONETIMI_N8N_EVENT_SYSTEM.md`
- **n8n Strategy:** `yalihan-bekci/knowledge/N8N_DEEP_INTEGRATION_STRATEGY_2025-01-15.md`



