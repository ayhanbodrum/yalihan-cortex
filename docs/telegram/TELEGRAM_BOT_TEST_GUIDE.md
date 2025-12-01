# 🤖 Telegram Bot Test Rehberi

**Tarih:** 01 Aralık 2025  
**Bot:** @Yalihan_gorev_bot  
**Chat ID:** 515406829

---

## ✅ Test Durumu

- ✅ Bot Token: Aktif
- ✅ Bot Bağlantısı: Başarılı
- ✅ Webhook: Aktif (ngrok)
- ✅ Test Mesajı: Başarıyla gönderildi

---

## 📋 Test Senaryoları

### 1. Bot Başlatma Testi

**Komut:**
```
/start
```

**Beklenen Sonuç:**
- Bot hoş geldin mesajı göndermeli
- Chat ID kaydedilmeli
- Yardım menüsü gösterilmeli

---

### 2. Eşleştirme Kodu Testi

**Adımlar:**
1. Admin panelde: `http://127.0.0.1:8000/admin/telegram-bot`
2. "Eşleştirme Kodu Oluştur" butonuna tıklayın
3. 6 haneli kodu kopyalayın (örn: `123456`)
4. Bot'a gönderin

**Beklenen Sonuç:**
- Bot "✅ Eşleşme Başarılı!" mesajı göndermeli
- Kullanıcı `telegram_id` kaydedilmeli
- `telegram_paired_at` timestamp kaydedilmeli

---

### 3. Komut Testleri

#### 3.1. Günlük Özet
**Komut:**
```
/ozet
```

**Beklenen Sonuç:**
- Bugünün randevuları listelenmeli
- Acil görevler gösterilmeli
- Tarih ve saat bilgisi olmalı

#### 3.2. Bekleyen Görevler
**Komut:**
```
/gorevler
```

**Beklenen Sonuç:**
- Kullanıcıya atanan bekleyen görevler listelenmeli
- Görev başlığı, durum, deadline gösterilmeli
- Görev ID'leri gösterilmeli

#### 3.3. Yardım Menüsü
**Komut:**
```
/yardim
```

**Beklenen Sonuç:**
- Tüm komutlar listelenmeli
- Her komutun açıklaması olmalı
- Örnek kullanımlar gösterilmeli

---

### 4. Konum Bazlı Arama Testi

**Adımlar:**
1. Telegram'da konum paylaşın (📍)
2. Bot'a gönderin

**Beklenen Sonuç:**
- Bot "typing..." göstermeli
- 2km çapındaki ilanlar bulunmalı
- Her ilan için:
  - Başlık
  - Fiyat
  - CRM linki
- Eğer ilan yoksa: "Yakınınızda ilan bulunamadı" mesajı

---

### 5. Kişi Paylaşımı Testi

**Adımlar:**
1. Telegram'da bir kişi kartı paylaşın (📇)
2. Bot'a gönderin

**Beklenen Sonuç:**
- Bot "typing..." göstermeli
- Telefon numarası normalize edilmeli
- Veritabanında arama yapılmalı:
  - **Varsa:** "Bu kişi zaten [Ad Soyad] olarak kayıtlı" + CRM linki
  - **Yoksa:** Yeni `Kisi` oluşturulmalı:
    - `status` = 'active'
    - `kisi_tipi` = 'lead'
    - `danisman_id` = eşleşen kullanıcı
    - `kaynak` = 'telegram_contact'
    - `lead_source` = 'telegram'
- "✅ [Ad Soyad] başarıyla CRM'e 'Aday Müşteri' olarak eklendi" mesajı

---

### 6. Sesli Not Testi (Voice-to-CRM)

**Adımlar:**
1. Telegram'da sesli mesaj kaydedin (🎤)
2. Bot'a gönderin

**Beklenen Sonuç:**
- Bot "upload_voice" action göstermeli
- Ses dosyası indirilmeli
- Whisper API'ye gönderilmeli
- Transkript alınmalı
- Ollama ile analiz edilmeli:
  - Intent (new_note, new_task)
  - Client name
  - Note body
  - Due date
- CRM'de aksiyon oluşturulmalı:
  - `KisiNot` (görüşme notu) VEYA
  - `Gorev` (görev)
- "✅ [Not/Görev] oluşturuldu!" mesajı
- Transkript özeti gösterilmeli

---

### 7. Typing Indicator Testi

**Kontrol:**
- Her işlem başlamadan önce bot "typing..." göstermeli
- Ses işlerken: "upload_voice"
- Konum ararken: "find_location"
- Normal mesaj: "typing"

---

## 🔍 Hata Senaryoları

### Eşleştirme Kodu Hatalı
**Test:**
- Geçersiz kod gönderin (örn: `000000`)

**Beklenen:**
- "⛔ Lütfen panelden aldığınız 6 haneli kodu girin."

### Kullanıcı Bulunamadı
**Test:**
- Eşleşmemiş kullanıcı komut gönderir

**Beklenen:**
- `AuthProcessor` devreye girmeli
- Eşleştirme kodu istenmeli

### Ses Dosyası İşlenemez
**Test:**
- Bozuk ses dosyası gönderin

**Beklenen:**
- "❌ Ses dosyası işlenemedi" mesajı
- Hata log'a kaydedilmeli

---

## 📊 Test Checklist

- [ ] `/start` komutu çalışıyor
- [ ] Eşleştirme kodu oluşturulabiliyor
- [ ] Eşleştirme kodu ile eşleşme yapılabiliyor
- [ ] `/ozet` komutu çalışıyor
- [ ] `/gorevler` komutu çalışıyor
- [ ] `/yardim` komutu çalışıyor
- [ ] Konum paylaşımı çalışıyor
- [ ] Kişi paylaşımı çalışıyor
- [ ] Sesli not çalışıyor
- [ ] Typing indicator çalışıyor
- [ ] Hata mesajları doğru gösteriliyor

---

## 🐛 Sorun Giderme

### Bot Mesaj Göndermiyor
1. Webhook durumunu kontrol edin: `php scripts/test-telegram-bot.php`
2. Log dosyalarını kontrol edin: `storage/logs/laravel.log`
3. Bot token'ı kontrol edin: `.env` dosyasında `TELEGRAM_BOT_TOKEN`

### Eşleştirme Çalışmıyor
1. Veritabanında `telegram_pairing_code` kontrol edin
2. Kodun 6 haneli olduğundan emin olun
3. Kodun süresi dolmamış olmalı (24 saat)

### Sesli Not Çalışmıyor
1. Whisper servisinin çalıştığını kontrol edin: `http://whisper:9000`
2. Ollama servisinin çalıştığını kontrol edin
3. Log dosyalarını kontrol edin

---

## 📝 Notlar

- Webhook URL: `https://fb8fbc58b72c.ngrok-free.app/api/telegram/webhook`
- Local URL: `http://127.0.0.1:8002/api/telegram/webhook`
- Bot Username: `@Yalihan_gorev_bot`
- Admin Chat ID: `515406829`

---

**Son Güncelleme:** 01 Aralık 2025 17:09

