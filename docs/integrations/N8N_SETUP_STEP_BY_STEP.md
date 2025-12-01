# 🔧 n8n Kurulum Rehberi - Adım Adım

**Tarih:** 2025-11-27  
**Hedef:** Yalıhan Emlak için n8n workflow'larını kurmak

---

## 📋 ÖN HAZIRLIK

### Gereksinimler

- ✅ n8n kurulumu (Cloud veya Self-hosted)
- ✅ Telegram Bot Token
- ✅ WhatsApp Business API (Opsiyonel)
- ✅ Email SMTP Ayarları
- ✅ Laravel `.env` dosyası düzenleme yetkisi

---

## 🚀 ADIM 1: n8n Webhook URL'lerini Oluşturma

### 1.1. n8n'e Giriş Yapın

1. n8n dashboard'a giriş yapın
2. Yeni workflow oluşturun

### 1.2. Webhook Node Ekleyin

Her workflow için bir webhook trigger ekleyin:

#### Workflow 1: Görev Oluşturuldu

```
1. "Webhook" node ekle
2. Settings:
   - HTTP Method: POST
   - Path: gorev-olustu
   - Authentication: Header Auth
   - Header Name: X-N8N-SECRET
   - Header Value: [Laravel'den gelecek secret]
```

#### Workflow 2: Görev Durumu Değişti

```
- Path: gorev-durum-degisti
```

#### Workflow 3: Deadline Yaklaşıyor

```
- Path: gorev-deadline-yaklasiyor
```

#### Workflow 4: Görev Gecikti

```
- Path: gorev-gecikti
```

#### Workflow 5: İlan Fiyat Değişti

```
- Path: ilan-fiyat-degisti
```

### 1.3. Webhook URL'lerini Kopyalayın

Her webhook için "Test URL" butonuna tıklayın ve URL'yi kopyalayın:
- `https://your-n8n-instance.com/webhook/gorev-olustu`
- `https://your-n8n-instance.com/webhook/gorev-durum-degisti`
- vb.

---

## 🔐 ADIM 2: Laravel .env Dosyasını Güncelleme

`.env` dosyanıza ekleyin:

```env
# n8n Webhook Secret (Laravel ve n8n'de aynı olmalı)
N8N_WEBHOOK_SECRET=your_super_secret_key_min_32_chars_long

# n8n Base URL
N8N_WEBHOOK_URL=https://your-n8n-instance.com

# n8n Timeout
N8N_TIMEOUT=30

# Görev Webhook'ları
N8N_GOREV_CREATED_WEBHOOK=https://your-n8n-instance.com/webhook/gorev-olustu
N8N_GOREV_STATUS_CHANGED_WEBHOOK=https://your-n8n-instance.com/webhook/gorev-durum-degisti
N8N_GOREV_DEADLINE_YAKLASIYOR_WEBHOOK=https://your-n8n-instance.com/webhook/gorev-deadline-yaklasiyor
N8N_GOREV_GECIKTI_WEBHOOK=https://your-n8n-instance.com/webhook/gorev-gecikti

# İlan Webhook'ları
N8N_ILAN_PRICE_CHANGED_WEBHOOK=https://your-n8n-instance.com/webhook/ilan-fiyat-degisti
```

---

## 📱 ADIM 3: Telegram Bot Kurulumu

### 3.1. Telegram Bot Oluşturma

1. Telegram'da `@BotFather` ile konuşun
2. `/newbot` komutunu gönderin
3. Bot adını ve username'ini belirleyin
4. Bot Token'ını kopyalayın

### 3.2. Chat ID'leri Toplama

Her danışman için Telegram Chat ID'sini toplayın:

1. Bot'a mesaj gönderin
2. `https://api.telegram.org/bot<TOKEN>/getUpdates` URL'sine gidin
3. Chat ID'yi bulun

### 3.3. n8n'de Telegram Node Yapılandırma

1. n8n'de "Telegram" node ekleyin
2. **Credentials:**
   - **Access Token:** Bot Token
3. **Settings:**
   - **Resource:** Send Message
   - **Chat ID:** Dinamik (payload'dan gelecek) veya sabit

**Dinamik Chat ID Örneği:**
```javascript
// Payload'dan al
{{ $json.gorev.danisman_telegram_chat_id }}

// Veya User ID'den çöz
// (User tablosunda telegram_chat_id kolonu varsa)
```

---

## 💬 ADIM 4: WhatsApp Business API Kurulumu (Opsiyonel)

### 4.1. WhatsApp Business API Hesabı

1. Meta Business Suite'e giriş yapın
2. WhatsApp Business API'yi aktifleştirin
3. Phone Number ID ve Access Token'ı alın

### 4.2. n8n'de WhatsApp Node Yapılandırma

1. "WhatsApp" node ekleyin
2. **Credentials:**
   - **Phone Number ID:** WhatsApp Phone Number ID
   - **Access Token:** WhatsApp Access Token
3. **Settings:**
   - **To:** `{{ $json.gorev.danisman_telefon }}`
   - **Message:** Mesaj içeriği

---

## 📧 ADIM 5: Email SMTP Kurulumu

### 5.1. SMTP Ayarları

`.env` dosyanızda zaten email ayarları var, n8n'de de aynıları kullanın:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yalihanemlak.com.tr
```

### 5.2. n8n'de Email Node Yapılandırma

1. "Email Send" node ekleyin
2. **SMTP Settings:**
   - **Host:** `{{ $env.MAIL_HOST }}`
   - **Port:** `{{ $env.MAIL_PORT }}`
   - **User:** `{{ $env.MAIL_USERNAME }}`
   - **Password:** `{{ $env.MAIL_PASSWORD }}`
3. **Message:**
   - **From:** `{{ $env.MAIL_FROM_ADDRESS }}`
   - **To:** Dinamik (payload'dan)

---

## 🔄 ADIM 6: Workflow Oluşturma - Detaylı

### Workflow: Görev Oluşturuldu

#### Node 1: Webhook Trigger

```yaml
Type: Webhook
Settings:
  - HTTP Method: POST
  - Path: gorev-olustu
  - Authentication: Header Auth
    - Header Name: X-N8N-SECRET
    - Header Value: [env'den al]
```

#### Node 2: Validate Event

```yaml
Type: IF
Condition:
  - {{ $json.event }} === "GorevCreated"
```

#### Node 3: Set Variables

```yaml
Type: Set
Values:
  - gorevBaslik: {{ $json.gorev.baslik }}
  - danismanAdi: {{ $json.gorev.danisman_adi }}
  - deadline: {{ $json.gorev.bitis_tarihi }}
  - channels: {{ $json.notification_channels }}
```

#### Node 4: Telegram Check & Send

```yaml
Type: IF
Condition:
  - {{ $json.notification_channels }}.includes("telegram")

Then:
  Type: Telegram
  Settings:
    - Chat ID: {{ $json.gorev.danisman_telegram_chat_id }}
    - Text: |
        📋 Yeni Görev!
        {{ $json.gorev.baslik }}
        🔗 {{ $json.gorev.url }}
```

#### Node 5: WhatsApp Check & Send

```yaml
Type: IF
Condition:
  - {{ $json.notification_channels }}.includes("whatsapp")

Then:
  Type: WhatsApp
  Settings:
    - To: {{ $json.gorev.danisman_telefon }}
    - Message: Yeni görev: {{ $json.gorev.baslik }}
```

#### Node 6: Email Check & Send

```yaml
Type: IF
Condition:
  - {{ $json.notification_channels }}.includes("email")

Then:
  Type: Email Send
  Settings:
    - To: {{ $json.gorev.danisman_email }}
    - Subject: Yeni Görev: {{ $json.gorev.baslik }}
    - HTML: [Email template]
```

#### Node 7: Response

```yaml
Type: Respond to Webhook
Response:
  - Status Code: 200
  - Body: { "success": true }
```

---

## ✅ ADIM 7: Test Etme

### 7.1. n8n Workflow Test

1. Workflow'u "Test Workflow" modunda çalıştırın
2. Manuel test payload'ı gönderin:

```json
{
  "event": "GorevCreated",
  "gorev_id": 123,
  "gorev": {
    "id": 123,
    "baslik": "Test Görevi",
    "danisman_adi": "Gizem Günal",
    "danisman_telegram_chat_id": "123456789",
    "danisman_telefon": "+905551234567",
    "danisman_email": "gizem@example.com",
    "bitis_tarihi": "2025-11-28T10:00:00Z",
    "url": "https://app.yalihanemlak.com.tr/admin/takim-yonetimi/gorevler/123"
  },
  "notification_channels": ["telegram", "whatsapp", "email"],
  "timestamp": "2025-11-27T10:00:00Z"
}
```

### 7.2. Laravel'den Test

```bash
# Görev oluşturma testi
php artisan tinker
>>> $gorev = \App\Modules\TakimYonetimi\Models\Gorev::create([...]);
>>> // Event otomatik fırlatılır
```

### 7.3. curl ile Test

```bash
curl -X POST https://your-n8n-instance.com/webhook/gorev-olustu \
  -H "Content-Type: application/json" \
  -H "X-N8N-SECRET: your_secret_key" \
  -d @test-payload.json
```

---

## 🔍 ADIM 8: Hata Ayıklama

### Sorun: Webhook çalışmıyor

**Kontrol Listesi:**
- [ ] Webhook URL doğru mu?
- [ ] Secret key doğru mu?
- [ ] n8n workflow aktif mi?
- [ ] Laravel queue çalışıyor mu?

### Sorun: Telegram mesajı gitmiyor

**Kontrol Listesi:**
- [ ] Bot Token doğru mu?
- [ ] Chat ID doğru mu?
- [ ] Bot'a mesaj gönderildi mi? (Bot'un chat ID'sini bilmesi için)

### Sorun: Email gitmiyor

**Kontrol Listesi:**
- [ ] SMTP ayarları doğru mu?
- [ ] Email adresi geçerli mi?
- [ ] Spam klasörünü kontrol edin

---

## 📊 ADIM 9: Monitoring

### n8n Execution Logs

n8n dashboard'da her workflow'un execution loglarını izleyin:
- Başarılı: ✅
- Başarısız: ❌ (Hata detayları)

### Laravel Logs

```bash
# Queue loglarını izle
tail -f storage/logs/laravel.log | grep n8n

# Job loglarını izle
tail -f storage/logs/gorev-deadline-check.log
```

---

## 🎯 ADIM 10: Production Deployment

### Checklist

- [ ] Tüm webhook URL'leri production URL'lerine güncellendi
- [ ] Secret key production için yenilendi
- [ ] Telegram bot production'da test edildi
- [ ] WhatsApp API production'da test edildi
- [ ] Email SMTP production'da test edildi
- [ ] Workflow'lar aktif ve çalışıyor
- [ ] Error handling eklendi
- [ ] Monitoring kuruldu
- [ ] Backup alındı

---

## 📚 Referanslar

- **n8n Dokümantasyon:** https://docs.n8n.io
- **Telegram Bot API:** https://core.telegram.org/bots/api
- **WhatsApp Business API:** https://developers.facebook.com/docs/whatsapp
- **Laravel Queue:** https://laravel.com/docs/queues

---

## 🆘 Destek

Sorun yaşarsanız:
1. n8n execution loglarını kontrol edin
2. Laravel loglarını kontrol edin
3. Webhook test URL'ini deneyin
4. Payload formatını kontrol edin



