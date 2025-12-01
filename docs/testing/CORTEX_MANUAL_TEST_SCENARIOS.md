# 🧪 Cortex v2.1 - Manuel Test Senaryoları

**Tarih:** 2025-11-30  
**Versiyon:** 2.1.0  
**Durum:** Production Ready

---

## 📋 TEST ÖNCESİ HAZIRLIK

### Gereksinimler

- [ ] Laravel development sunucusu çalışıyor (`php artisan serve`)
- [ ] Queue worker çalışıyor (`php artisan queue:work --queue=cortex-notifications`)
- [ ] Ollama servisi erişilebilir
- [ ] AnythingLLM servisi erişilebilir (opsiyonel - Arsa modülü için)
- [ ] Telegram bot token yapılandırılmış (opsiyonel - Telegram bildirimleri için)
- [ ] Admin panel erişimi (`/admin`)

---

## 🏗️ TEST 1: ARSA MODÜLÜ - RAG İMAR ANALİZİ

### Senaryo

**Hedef:** Arsa kategorisinde "Cortex İmar & İnşaat Analizi" özelliğini test etmek.

### Adımlar

1. **İlan Oluşturma Sayfasına Git:**
   - URL: `/admin/ilanlar/create`
   - Kategori: **Arsa** seç

2. **Arsa Bilgilerini Gir:**
   - **Ada No:** `101`
   - **Parsel No:** `5`
   - **Alan (m²):** `500`
   - **İlçe:** Herhangi bir ilçe seç

3. **Cortex Analizi Başlat:**
   - "Cortex İmar & İnşaat Analizi" kartını bul
   - **"Analizi Başlat"** butonuna tıkla
   - Loading state'i kontrol et (buton disable olmalı, "Plan notları okunuyor..." yazmalı)

4. **Sonuç Kontrolü:**
   - ✅ Başarılı: AI analizi kartın altında görünmeli (KAKS, TAKS, Gabari bilgileri)
   - ❌ Hata: Toast mesajı görünmeli, hata detayları log'da olmalı

### Beklenen Sonuç

- AI analizi 60 saniye içinde tamamlanmalı
- Sonuç Markdown/HTML formatında görünmeli
- `ai_logs` tablosunda `request_type = 'analyze-construction'` kaydı olmalı

### Kontrol Komutları

```bash
# ai_logs kontrolü
php artisan tinker
>>> DB::table('ai_logs')->where('request_type', 'analyze-construction')->latest()->first()

# Laravel log kontrolü
tail -f storage/logs/laravel.log | grep "CortexKnowledgeService"
```

---

## 🏖️ TEST 2: YAZLIK MODÜLÜ - OTOMATİK FİYATLANDIRMA

### Senaryo

**Hedef:** Yazlık kategorisinde "Otomatik Fiyatlandırma" özelliğini test etmek.

### Adımlar

1. **İlan Oluşturma Sayfasına Git:**
   - URL: `/admin/ilanlar/create`
   - Kategori: **Yazlık Kiralama** seç

2. **Günlük Fiyat Gir:**
   - **Günlük Fiyat:** `10000` TL

3. **Otomatik Hesapla:**
   - **"⚡ Otomatik Hesapla"** butonuna tıkla
   - Flash effect'i kontrol et (inputlar yeşil yanıp sönmeli)

4. **Sonuç Kontrolü:**
   - ✅ **Haftalık Fiyat:** `66.500 TL` (10.000 * 7 * 0.95 = %5 indirim)
   - ✅ **Aylık Fiyat:** `255.000 TL` (10.000 * 30 * 0.85 = %15 indirim)
   - ✅ **Kış Sezonu Günlük:** `5.000 TL` (10.000 * 0.50 = %50 indirim)

### Beklenen Sonuç

- Form alanları otomatik doldurulmalı
- Flash effect görünmeli
- `ai_logs` tablosunda `request_type = 'calculate-seasonal-price'` kaydı olmalı

### Kontrol Komutları

```bash
# ai_logs kontrolü
php artisan tinker
>>> DB::table('ai_logs')->where('request_type', 'like', '%price%')->latest()->first()

# Config kontrolü
php artisan tinker
>>> config('yali_options.pricing_rules')
```

---

## 🏠 TEST 3: KONUT MODÜLÜ - AKILLI VALİDASYON

### Senaryo

**Hedef:** Konut kategorisinde "Görsel Zeka" ve "Akıllı Validasyon" özelliklerini test etmek.

### Adımlar

#### 3.1. Oda Sayısı Görselleştirme

1. **İlan Oluşturma Sayfasına Git:**
   - URL: `/admin/ilanlar/create`
   - Kategori: **Konut** (Daire veya Villa) seç

2. **Oda Sayısı Seç:**
   - **Oda Sayısı:** `3+1` seç
   - ✅ Input'un turuncu renge büründüğünü kontrol et

#### 3.2. Net/Brüt m² Validasyonu

1. **Brüt Metrekare Gir:**
   - **Brüt Metrekare:** `100` m²

2. **Net Metrekare Gir (HATALI):**
   - **Net Metrekare:** `110` m² (Brüt'ten büyük)
   - ✅ Kırmızı border görünmeli
   - ✅ "Net metrekare, Brüt metrekareden büyük olamaz!" uyarısı görünmeli

3. **Net Metrekare Düzelt:**
   - **Net Metrekare:** `90` m² (Brüt'ten küçük)
   - ✅ Kırmızı border kaybolmalı
   - ✅ Uyarı mesajı kaybolmalı

#### 3.3. Birim Fiyat Badge

1. **Satış Fiyatı Gir:**
   - **Satış Fiyatı:** `2.500.000` TL
   - **Brüt Metrekare:** `100` m²

2. **Birim Fiyat Kontrolü:**
   - ✅ Input'un sağ altında "Birim: 25.000 TL/m²" badge'i görünmeli

### Beklenen Sonuç

- Oda sayısı seçimleri renkli görünmeli
- Net > Brüt validasyonu çalışmalı
- Birim fiyat badge'i dinamik hesaplanmalı
- Form submit edildiğinde server-side validation da çalışmalı

### Kontrol Komutları

```bash
# Config kontrolü
php artisan tinker
>>> config('yali_options.oda_sayisi_options')

# Validation test
php artisan tinker
>>> $validator = app(\App\Services\CategoryFieldValidator::class);
>>> $validator->validateKonut(['brut_m2' => 100, 'net_m2' => 110]);
```

---

## 📱 TEST 4: TELEGRAM BİLDİRİMLERİ

### Senaryo

**Hedef:** Yüksek skorlu (>90) fırsatların Telegram'a bildirim gönderilmesini test etmek.

### Ön Koşullar

- [ ] `TELEGRAM_BOT_TOKEN` `.env` dosyasında tanımlı
- [ ] `TELEGRAM_ADMIN_CHAT_ID` `.env` dosyasında tanımlı
- [ ] Queue worker çalışıyor

### Adımlar

1. **Yüksek Skorlu Eşleşme Oluştur:**
   - `SmartPropertyMatcherAI` servisinin çalıştığından emin ol
   - Skor > 90 olan bir eşleşme oluştur (test verisi ile)

2. **Queue Kontrolü:**
   ```bash
   # jobs tablosunda bekleyen işleri kontrol et
   php artisan tinker
   >>> DB::table('jobs')->where('queue', 'cortex-notifications')->count()
   ```

3. **Queue Worker İşleme:**
   - Queue worker'ın işi işlediğini kontrol et
   - Log'larda "TelegramService: Kritik fırsat bildirimi gönderildi" mesajını ara

4. **Telegram Kontrolü:**
   - Yöneticinin Telegram'ına bildirim gelip gelmediğini kontrol et
   - Mesaj formatını kontrol et (Markdown, linkler, emojiler)

### Beklenen Sonuç

- Job `cortex-notifications` kuyruğuna atılmalı
- Queue worker işi işlemeli
- Telegram bildirimi gönderilmeli
- `ai_logs` tablosunda `request_type = 'notification_sent'` kaydı olmalı

### Kontrol Komutları

```bash
# Queue durumu
php artisan queue:monitor cortex-notifications

# ai_logs kontrolü
php artisan tinker
>>> DB::table('ai_logs')->where('request_type', 'notification_sent')->latest()->first()

# Laravel log kontrolü
tail -f storage/logs/laravel.log | grep "TelegramService"
```

---

## 📊 TEST 5: AI COMMAND CENTER DASHBOARD

### Senaryo

**Hedef:** AI Command Center dashboard'unun tüm bileşenlerini test etmek.

### Adımlar

1. **Dashboard'a Git:**
   - URL: `/admin/ai/dashboard`

2. **Sistem Sağlık Kontrolü:**
   - ✅ **Cortex Brain:** Yeşil pulse (Online)
   - ✅ **LLM Engine (Ollama):** Yeşil/Kırmızı pulse (Duruma göre)
   - ✅ **Knowledge Base (AnythingLLM):** Yeşil/Sarı/Kırmızı pulse (Duruma göre)

3. **Fırsat Akışı Kontrolü:**
   - Son 24 saatte yüksek skorlu (>80) eşleşmeler görünmeli
   - Skor 90+ olanlar "⚠️ ACİL" badge'i ile işaretlenmeli
   - "Detay Gör" ve "Danışmana Ata" butonları çalışmalı

4. **AI Aktivitesi Kontrolü:**
   - ✅ **İmar Analizi:** Bugünkü sayı görünmeli
   - ✅ **İlan Açıklaması:** Bugünkü sayı görünmeli
   - ✅ **Fiyat Hesaplama:** Bugünkü sayı görünmeli
   - ✅ **Token Kullanımı:** Bugünkü token sayısı görünmeli (örn: "3.5M")
   - ✅ **Başarı Oranı:** Yüzde görünmeli

5. **Yenile Butonu:**
   - "Yenile" butonuna tıkla
   - Sayfa yenilenmeli, veriler güncellenmeli

### Beklenen Sonuç

- Tüm sistem sağlık durumları doğru görünmeli
- Fırsat akışı gerçek zamanlı verileri göstermeli
- AI aktivite istatistikleri doğru hesaplanmalı

### Kontrol Komutları

```bash
# System health API test
curl -X GET http://localhost:8000/admin/ai/system-health \
  -H "Accept: application/json" \
  -H "Cookie: laravel_session=..."

# Usage stats API test
curl -X GET http://localhost:8000/admin/ai/usage-statistics \
  -H "Accept: application/json" \
  -H "Cookie: laravel_session=..."
```

---

## 🔄 TEST 6: QUEUE WORKER DURUMU

### Senaryo

**Hedef:** Queue worker'ın çalışıp çalışmadığını ve işleri işlediğini test etmek.

### Adımlar

1. **Queue Worker Durumu Kontrolü:**
   ```bash
   # Process kontrolü
   ps aux | grep "queue:work"
   
   # Supervisor kontrolü (eğer kullanılıyorsa)
   sudo supervisorctl status cortex-queue-worker:*
   ```

2. **Bekleyen Job Kontrolü:**
   ```bash
   php artisan tinker
   >>> DB::table('jobs')->where('queue', 'cortex-notifications')->count()
   ```

3. **Job İşleme Testi:**
   - Yeni bir kritik fırsat oluştur
   - Job'un kuyruğa atıldığını kontrol et
   - Queue worker'ın işi işlediğini kontrol et

### Beklenen Sonuç

- Queue worker sürekli çalışmalı
- Bekleyen job'lar işlenmeli
- Hata durumunda retry mekanizması çalışmalı

---

## ✅ TEST SONUÇLARI ŞABLONU

### Test Raporu

```
Test Tarihi: _______________
Test Eden: _______________

[ ] TEST 1: Arsa Modülü - RAG İmar Analizi
    Durum: ✅ Başarılı / ❌ Başarısız
    Notlar: ________________________________

[ ] TEST 2: Yazlık Modülü - Otomatik Fiyatlandırma
    Durum: ✅ Başarılı / ❌ Başarısız
    Notlar: ________________________________

[ ] TEST 3: Konut Modülü - Akıllı Validasyon
    Durum: ✅ Başarılı / ❌ Başarısız
    Notlar: ________________________________

[ ] TEST 4: Telegram Bildirimleri
    Durum: ✅ Başarılı / ❌ Başarısız
    Notlar: ________________________________

[ ] TEST 5: AI Command Center Dashboard
    Durum: ✅ Başarılı / ❌ Başarısız
    Notlar: ________________________________

[ ] TEST 6: Queue Worker Durumu
    Durum: ✅ Başarılı / ❌ Başarısız
    Notlar: ________________________________
```

---

## 🚨 SORUN GİDERME

### Test 1 Başarısız (Arsa RAG)

- **Sorun:** AI analizi gelmiyor
- **Çözüm:**
  1. AnythingLLM servisi çalışıyor mu? (`curl http://127.0.0.1:3001/api/system/health`)
  2. `ANYTHINGLLM_KEY` doğru mu?
  3. `ANYTHINGLLM_WORKSPACE` doğru mu?
  4. Log'larda hata var mı? (`tail -f storage/logs/laravel.log`)

### Test 2 Başarısız (Yazlık Pricing)

- **Sorun:** Fiyatlar hesaplanmıyor
- **Çözüm:**
  1. `config/yali_options.php` dosyasında `pricing_rules` var mı?
  2. Cache temizlendi mi? (`php artisan config:clear`)
  3. API endpoint çalışıyor mu? (Network tab'da kontrol et)

### Test 3 Başarısız (Konut Validation)

- **Sorun:** Validasyon çalışmıyor
- **Çözüm:**
  1. `CategoryFieldValidator` servisi yükleniyor mu?
  2. Alpine.js çalışıyor mu? (Console'da hata var mı?)
  3. Config'de `oda_sayisi_options` var mı?

### Test 4 Başarısız (Telegram)

- **Sorun:** Telegram bildirimi gitmiyor
- **Çözüm:**
  1. Bot token doğru mu? (`curl "https://api.telegram.org/bot<TOKEN>/getMe"`)
  2. Admin chat ID doğru mu?
  3. Queue worker çalışıyor mu?
  4. `jobs` tablosunda bekleyen iş var mı?

### Test 5 Başarısız (Dashboard)

- **Sorun:** Dashboard verileri görünmüyor
- **Çözüm:**
  1. `ai_logs` tablosunda veri var mı?
  2. Controller metodları çalışıyor mu?
  3. Blade template doğru mu?

### Test 6 Başarısız (Queue Worker)

- **Sorun:** Queue worker çalışmıyor
- **Çözüm:**
  1. Supervisor yapılandırması doğru mu?
  2. `jobs` tablosu var mı? (`php artisan migrate`)
  3. Queue connection doğru mu? (`QUEUE_CONNECTION=database`)

---

**Son Güncelleme:** 2025-11-30  
**Hazırlayan:** Yalıhan Cortex Testing Team  
**Durum:** ✅ Production Ready

