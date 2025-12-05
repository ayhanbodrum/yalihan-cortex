# 🚀 Telegram + n8n Production Test Planı

**Tarih:** 5 Aralık 2025  
**Hedef:** Production ortamında Telegram Bot + n8n entegrasyonunu test etmek  
**Durum:** 📋 Test Planı Hazır  
**Context7:** %100 Uyumlu

---

## 📋 TEST KAPSAMI

### 1️⃣ Telegram Bot Testleri
- ✅ Webhook health check
- ✅ Komut işleme (/start, /help, /ilan, /gorev)
- ✅ Sesli mesaj işleme (Voice-to-CRM)
- ✅ Konum paylaşımı
- ✅ Bildirim gönderimi
- ✅ Hata yönetimi

### 2️⃣ n8n Workflow Testleri
- ✅ Webhook alıcı testi
- ✅ Multi-channel bildirim (Telegram, WhatsApp, Email)
- ✅ Voice-to-CRM workflow
- ✅ İlan fiyat değişikliği bildirimi
- ✅ Görev yönetimi bildirimleri
- ✅ Hata handling ve retry mekanizması

### 3️⃣ Entegrasyon Testleri
- ✅ Laravel → n8n webhook gönderimi
- ✅ n8n → Laravel API çağrıları
- ✅ Veri senkronizasyonu
- ✅ Rate limiting kontrolü
- ✅ Timeout handling

---

## 🔧 ÖN HAZIRLIK

### Gereksinimler Kontrolü

```bash
# 1. Environment Variables Kontrolü
grep -E "TELEGRAM|N8N" .env

# Gerekli değişkenler:
# TELEGRAM_BOT_TOKEN=...
# TELEGRAM_WEBHOOK_URL=...
# N8N_WEBHOOK_URL=...
# N8N_WEBHOOK_SECRET=...
# N8N_GOREV_CREATED_WEBHOOK=...
# N8N_ILAN_PRICE_CHANGED_WEBHOOK=...
```

### Database Kontrolü

```sql
-- Telegram chat ID'leri kontrolü
SELECT id, name, email, telegram_chat_id 
FROM users 
WHERE telegram_chat_id IS NOT NULL;

-- n8n webhook log kontrolü
SELECT * FROM webhook_logs 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)
ORDER BY created_at DESC;
```

### n8n Workflow Kontrolü

1. **n8n Dashboard'a giriş yap**
2. **Aktif workflow'ları kontrol et:**
   - ✅ Görev Oluşturuldu
   - ✅ Görev Durumu Değişti
   - ✅ İlan Fiyat Değişti
   - ✅ Voice-to-CRM
   - ✅ Deadline Yaklaşıyor

3. **Webhook URL'lerini doğrula:**
   - Her workflow'un webhook URL'si aktif mi?
   - Authentication header doğru mu?

---

## 🧪 TEST SENARYOLARI

### TEST 1: Telegram Bot Webhook Health Check

**Amaç:** Telegram webhook'unun çalıştığını doğrula

**Adımlar:**

1. **Webhook durumunu kontrol et:**
```bash
curl -X GET "https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/getWebhookInfo"
```

**Beklenen Sonuç:**
```json
{
  "ok": true,
  "result": {
    "url": "https://your-domain.com/api/telegram/webhook",
    "has_custom_certificate": false,
    "pending_update_count": 0
  }
}
```

2. **Manuel webhook test:**
```bash
curl -X POST "https://your-domain.com/api/telegram/webhook" \
  -H "Content-Type: application/json" \
  -d '{
    "update_id": 123456789,
    "message": {
      "message_id": 1,
      "from": {
        "id": 123456789,
        "is_bot": false,
        "first_name": "Test",
        "username": "testuser"
      },
      "chat": {
        "id": 123456789,
        "type": "private"
      },
      "date": 1701234567,
      "text": "/start"
    }
  }'
```

**Beklenen Sonuç:**
- HTTP 200 OK
- Laravel log'da "Telegram webhook received" mesajı
- Bot yanıt gönderir

**✅ Başarı Kriteri:**
- Webhook aktif ve çalışıyor
- Bot komutlara yanıt veriyor
- Hata log'u yok

---

### TEST 2: Telegram Komut İşleme

**Amaç:** Bot komutlarının doğru çalıştığını doğrula

**Test Komutları:**

| Komut | Beklenen Yanıt | Test Yöntemi |
|-------|---------------|--------------|
| `/start` | Hoş geldin mesajı | Telegram'dan gönder |
| `/help` | Yardım menüsü | Telegram'dan gönder |
| `/ilan` | İlan listesi | Telegram'dan gönder |
| `/gorev` | Görev listesi | Telegram'dan gönder |
| `/talep` | Talep oluşturma formu | Telegram'dan gönder |

**Adımlar:**

1. **Telegram'da bot'a komut gönder**
2. **Yanıtı kontrol et:**
   - Mesaj geldi mi?
   - Format doğru mu?
   - İçerik doğru mu?

3. **Laravel log kontrolü:**
```bash
tail -f storage/logs/laravel.log | grep "Telegram"
```

**✅ Başarı Kriteri:**
- Tüm komutlar çalışıyor
- Yanıt süresi < 2 saniye
- Hata yok

---

### TEST 3: Voice-to-CRM Workflow

**Amaç:** Sesli mesajın CRM'e kaydedildiğini doğrula

**Adımlar:**

1. **Telegram'da bot'a sesli mesaj gönder:**
   ```
   "Merhaba, Yalıkavak'ta denize yakın 3+1 villa arıyorum. 
   Bütçem 5 milyon TL. Ahmet Duran, 0535-733-9742"
   ```

2. **n8n workflow kontrolü:**
   - n8n dashboard'da workflow çalıştı mı?
   - Voice-to-text başarılı mı?
   - Laravel API çağrısı yapıldı mı?

3. **Laravel log kontrolü:**
```bash
tail -f storage/logs/laravel.log | grep "voice-to-crm"
```

4. **Database kontrolü:**
```sql
-- Yeni kişi oluşturuldu mu?
SELECT * FROM kisiler 
WHERE telefon LIKE '%0535-733-9742%' 
ORDER BY created_at DESC LIMIT 1;

-- Yeni talep oluşturuldu mu?
SELECT * FROM talepler 
WHERE kisi_id = (SELECT id FROM kisiler WHERE telefon LIKE '%0535-733-9742%')
ORDER BY created_at DESC LIMIT 1;
```

**✅ Başarı Kriteri:**
- Sesli mesaj transkript edildi
- Kişi kaydı oluşturuldu
- Talep draft oluşturuldu
- Bot onay mesajı gönderdi

---

### TEST 4: n8n Webhook Alıcı Testi

**Amaç:** Laravel'den n8n'e webhook gönderimini test et

**Test Senaryosu: İlan Fiyat Değişikliği**

**Adımlar:**

1. **Test ilanı oluştur:**
```php
// Tinker'da
$ilan = App\Models\Ilan::find(40); // Mevcut ilan
$eskiFiyat = $ilan->fiyat;
$ilan->fiyat = $eskiFiyat + 100000;
$ilan->save();
```

2. **n8n workflow kontrolü:**
   - Webhook geldi mi?
   - Payload doğru mu?
   - Workflow çalıştı mı?

3. **n8n log kontrolü:**
   - n8n dashboard'da execution log'u kontrol et
   - Hata var mı?

4. **Bildirim kontrolü:**
   - Telegram mesajı geldi mi?
   - Email gönderildi mi?
   - WhatsApp mesajı gönderildi mi? (varsa)

**✅ Başarı Kriteri:**
- Webhook başarıyla gönderildi
- n8n workflow çalıştı
- Bildirimler gönderildi
- Hata yok

---

### TEST 5: Multi-Channel Bildirim Testi

**Amaç:** Görev oluşturulduğunda tüm kanallara bildirim gönderildiğini doğrula

**Adımlar:**

1. **Test görevi oluştur:**
```php
// Tinker'da
$gorev = App\Models\Gorev::create([
    'baslik' => 'Test Görevi - Production Test',
    'aciklama' => 'Bu bir test görevidir',
    'user_id' => 1, // Danışman ID
    'deadline' => now()->addDays(3),
    'status' => 'Beklemede'
]);
```

2. **n8n workflow kontrolü:**
   - Webhook geldi mi?
   - Multi-channel node çalıştı mı?

3. **Bildirim kontrolü:**
   - ✅ Telegram: Mesaj geldi mi?
   - ✅ Email: Email gönderildi mi?
   - ✅ WhatsApp: Mesaj gönderildi mi? (varsa)

**✅ Başarı Kriteri:**
- Tüm kanallara bildirim gönderildi
- Mesaj içeriği doğru
- Hata yok

---

### TEST 6: Rate Limiting Testi

**Amaç:** Rate limiting'in çalıştığını doğrula

**Adımlar:**

1. **Hızlı webhook gönderimi simüle et:**
```bash
# 10 saniyede 100 istek gönder
for i in {1..100}; do
  curl -X POST "https://your-domain.com/api/telegram/webhook" \
    -H "Content-Type: application/json" \
    -d '{"update_id": '$i', "message": {"text": "/start"}}' &
done
```

2. **Rate limit kontrolü:**
   - 429 Too Many Requests hatası geldi mi?
   - Rate limit header'ları doğru mu?

**✅ Başarı Kriteri:**
- Rate limiting çalışıyor
- 429 hatası doğru dönüyor
- Sistem stabil

---

### TEST 7: Error Handling Testi

**Amaç:** Hata durumlarının doğru yönetildiğini doğrula

**Test Senaryoları:**

1. **Geçersiz webhook payload:**
```bash
curl -X POST "https://your-domain.com/api/telegram/webhook" \
  -H "Content-Type: application/json" \
  -d '{"invalid": "payload"}'
```

2. **n8n webhook timeout simülasyonu:**
   - n8n workflow'u geçici olarak devre dışı bırak
   - Webhook gönder
   - Timeout kontrolü yap

3. **Telegram API hatası simülasyonu:**
   - Geçersiz bot token kullan
   - Hata handling kontrolü

**✅ Başarı Kriteri:**
- Hatalar yakalanıyor
- Log'a kaydediliyor
- Sistem çökmedi
- Kullanıcıya uygun mesaj gönderildi

---

## 📊 TEST RAPORU ŞABLONU

### Test Sonuçları

| Test No | Test Adı | Durum | Süre | Notlar |
|---------|----------|-------|------|--------|
| 1 | Webhook Health Check | ⏳ | - | - |
| 2 | Komut İşleme | ⏳ | - | - |
| 3 | Voice-to-CRM | ⏳ | - | - |
| 4 | n8n Webhook Alıcı | ⏳ | - | - |
| 5 | Multi-Channel Bildirim | ⏳ | - | - |
| 6 | Rate Limiting | ⏳ | - | - |
| 7 | Error Handling | ⏳ | - | - |

### Genel Değerlendirme

- **Toplam Test:** 7
- **Başarılı:** 0
- **Başarısız:** 0
- **Beklemede:** 7
- **Başarı Oranı:** -%

### Kritik Bulgular

- [ ] Webhook çalışıyor mu?
- [ ] n8n workflow'lar aktif mi?
- [ ] Bildirimler gönderiliyor mu?
- [ ] Hata handling doğru mu?
- [ ] Performance yeterli mi?

---

## 🔍 MONİTÖRİNG CHECKLIST

### Real-time Monitoring

```bash
# Telegram webhook log'ları
tail -f storage/logs/laravel.log | grep "Telegram"

# n8n webhook log'ları
tail -f storage/logs/laravel.log | grep "n8n"

# Queue işlemleri
php artisan queue:work --verbose

# API response time
tail -f storage/logs/laravel.log | grep "response_time"
```

### Metrics to Track

- ✅ Webhook başarı oranı (%)
- ✅ Ortalama response time (ms)
- ✅ Hata sayısı (günlük)
- ✅ Queue işlem sayısı
- ✅ n8n workflow execution time
- ✅ Bildirim gönderim başarı oranı

---

## 🚨 TROUBLESHOOTING GUIDE

### Problem 1: Telegram Webhook Çalışmıyor

**Belirtiler:**
- Bot komutlara yanıt vermiyor
- Webhook log'ları yok

**Çözüm:**
```bash
# 1. Webhook URL'ini kontrol et
curl -X GET "https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/getWebhookInfo"

# 2. Webhook'u yeniden ayarla
curl -X POST "https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/setWebhook" \
  -d "url=https://your-domain.com/api/telegram/webhook"

# 3. SSL sertifikası kontrolü
openssl s_client -connect your-domain.com:443
```

### Problem 2: n8n Webhook Gelmiyor

**Belirtiler:**
- Laravel'den webhook gönderiliyor ama n8n'de görünmüyor
- n8n workflow çalışmıyor

**Çözüm:**
```bash
# 1. Webhook URL'ini kontrol et
echo $N8N_GOREV_CREATED_WEBHOOK

# 2. Manuel test
curl -X POST "${N8N_GOREV_CREATED_WEBHOOK}" \
  -H "X-N8N-SECRET: ${N8N_WEBHOOK_SECRET}" \
  -H "Content-Type: application/json" \
  -d '{"test": true}'

# 3. n8n workflow'u kontrol et
# - Webhook node aktif mi?
# - Authentication header doğru mu?
# - Workflow enabled mi?
```

### Problem 3: Voice-to-CRM Çalışmıyor

**Belirtiler:**
- Sesli mesaj gönderiliyor ama CRM'e kaydedilmiyor
- n8n workflow hata veriyor

**Çözüm:**
```bash
# 1. Voice-to-text servisi kontrolü
# Ollama/OpenAI API çalışıyor mu?

# 2. Laravel API endpoint kontrolü
curl -X POST "https://your-domain.com/api/v1/admin/ai/voice-to-crm" \
  -H "Content-Type: application/json" \
  -d '{"text": "Test mesajı", "danisman_id": 1}'

# 3. n8n workflow log kontrolü
# - Voice-to-text node çalıştı mı?
# - Laravel API çağrısı başarılı mı?
```

---

## ✅ PRODUCTION READY CHECKLIST

### Telegram Bot
- [ ] Webhook aktif ve çalışıyor
- [ ] Tüm komutlar test edildi
- [ ] Voice-to-CRM çalışıyor
- [ ] Bildirimler gönderiliyor
- [ ] Hata handling doğru
- [ ] Rate limiting aktif

### n8n Workflows
- [ ] Tüm workflow'lar aktif
- [ ] Webhook URL'leri doğru
- [ ] Authentication çalışıyor
- [ ] Multi-channel bildirimler çalışıyor
- [ ] Error handling doğru
- [ ] Retry mekanizması aktif

### Entegrasyon
- [ ] Laravel → n8n webhook'ları çalışıyor
- [ ] n8n → Laravel API çağrıları çalışıyor
- [ ] Veri senkronizasyonu doğru
- [ ] Timeout handling doğru
- [ ] Log'lar kaydediliyor

### Monitoring
- [ ] Log monitoring aktif
- [ ] Error tracking aktif
- [ ] Performance metrics toplanıyor
- [ ] Alert sistemi kurulu

---

## 📝 SONRAKI ADIMLAR

1. **Test planını çalıştır** (Bu doküman)
2. **Test sonuçlarını raporla**
3. **Kritik bulguları düzelt**
4. **Production'a deploy et**
5. **Monitoring'i aktifleştir**

---

**Hazırlayan:** Yalıhan Technical Team  
**Tarih:** 5 Aralık 2025  
**Versiyon:** 1.0.0  
**Durum:** ✅ Production Test Planı Hazır

