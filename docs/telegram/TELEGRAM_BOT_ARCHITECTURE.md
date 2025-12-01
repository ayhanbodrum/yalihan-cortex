# 🤖 Telegram Bot Mimarisi ve Kullanım Stratejisi

**Tarih:** 01 Aralık 2025  
**Durum:** Aktif Bot Analizi ve Strateji Belirleme

---

## 📊 MEVCUT DURUM

### Şu Anda Aktif Bot
- **Bot Username:** `@Yalihan_gorev_bot`
- **Bot Token:** `8469337827:AAH6...`
- **Bot Adı:** `Yalıhan Team`
- **Kullanım:** Görev yönetimi, takım koordinasyonu

### Kullanıcının Belirttiği Botlar
1. **YalihanCortex_Bot** - AI/Cortex özellikleri için
2. **Yalıhan Channel** - Takım bildirimleri için
3. **@YalihanEmlak_Bot** - Müşteri hizmetleri için

---

## 🎯 ÖNERİLEN MİMARİ

### 1. **YalihanCortex_Bot** (Ana Bot - AI Özellikleri)
**Amaç:** Cortex AI sistemi, Voice-to-CRM, Akıllı eşleştirme

**Özellikler:**
- ✅ Voice-to-CRM (Sesli not → CRM aksiyonu)
- ✅ Konum bazlı ilan arama
- ✅ Kişi paylaşımı (CRM entegrasyonu)
- ✅ Günlük özet (`/ozet`)
- ✅ Görev yönetimi (`/gorevler`)
- ✅ Eşleştirme kodu sistemi
- ✅ Typing indicators

**Kullanıcılar:**
- Danışmanlar (eşleştirme kodu ile)
- Yöneticiler (kritik fırsat bildirimleri)

**Webhook:** `/api/telegram/webhook`

---

### 2. **Yalıhan Channel** (Takım Bildirimleri)
**Amaç:** Takım geneli bildirimler, raporlar, özetler

**Özellikler:**
- 📊 Günlük performans raporları
- 🚨 Kritik fırsat bildirimleri (Cortex)
- 📈 Haftalık/aylık özetler
- 🎯 Takım hedefleri ve durumu

**Kullanıcılar:**
- Tüm takım üyeleri
- Yöneticiler

**Kanal ID:** `-1003037949764` (mevcut)

---

### 3. **@YalihanEmlak_Bot** (Müşteri Hizmetleri - Gelecek)
**Amaç:** Müşteri iletişimi, ilan sorgulama, randevu yönetimi

**Özellikler:**
- 🔍 İlan arama
- 📅 Randevu oluşturma
- 💬 Müşteri desteği
- 📱 WhatsApp entegrasyonu (gelecek)

**Kullanıcılar:**
- Potansiyel müşteriler
- Mevcut müşteriler

**Durum:** ⏳ Planlama aşamasında

---

## 🔄 MİGRASYON STRATEJİSİ

### Adım 1: Mevcut Bot'u Cortex'e Dönüştür
**Şu An:** `@Yalihan_gorev_bot` → Görev yönetimi

**Hedef:** `@YalihanCortex_Bot` → AI özellikleri + Görev yönetimi

**Yapılacaklar:**
1. Bot token'ı değiştir (YalihanCortex_Bot token'ı)
2. Bot username'i güncelle
3. Mevcut özellikleri koru
4. Yeni AI özelliklerini ekle

### Adım 2: Kanal Yapılandırması
**Mevcut Kanal:** `-1003037949764`

**Kullanım:**
- Cortex kritik bildirimleri
- Takım özetleri
- Performans raporları

### Adım 3: Müşteri Botu (Gelecek)
**@YalihanEmlak_Bot** ayrı bir bot olarak planlanacak

---

## 📋 KULLANIM SENARYOLARI

### Senaryo 1: Danışman Günlük Kullanım
```
1. Telegram'da @YalihanCortex_Bot'u aç
2. /start → Eşleştirme kodu ile eşleş
3. /ozet → Günlük özeti gör
4. /gorevler → Bekleyen görevleri gör
5. Konum paylaş → Yakındaki ilanları bul
6. Kişi paylaş → CRM'e ekle
7. Sesli not → Voice-to-CRM
```

### Senaryo 2: Yönetici Kritik Bildirim
```
1. Cortex yüksek skorlu eşleşme bulur (>90)
2. YalihanCortex_Bot → Yöneticiye özel mesaj gönderir
3. Yalıhan Channel → Takıma genel bildirim gönderir
```

### Senaryo 3: Takım Özeti
```
1. Her gün saat 18:00'de otomatik rapor
2. Yalıhan Channel'a gönderilir
3. Günlük performans, hedefler, özetler
```

---

## ⚙️ YAPILANDIRMA

### .env Dosyası
```env
# Ana Bot (Cortex)
TELEGRAM_BOT_TOKEN=YalihanCortex_Bot_token_buraya
TELEGRAM_BOT_USERNAME=YalihanCortex_Bot

# Kanal
TELEGRAM_TEAM_CHANNEL_ID=-1003037949764

# Admin Chat ID (Yönetici)
TELEGRAM_ADMIN_CHAT_ID=515406829
```

### config/services.php
```php
'telegram' => [
    'bot_token' => env('TELEGRAM_BOT_TOKEN', ''),
    'bot_username' => env('TELEGRAM_BOT_USERNAME', 'YalihanCortex_Bot'),
    'webhook_url' => env('TELEGRAM_WEBHOOK_URL', url('/api/telegram/webhook')),
    'team_channel_id' => env('TELEGRAM_TEAM_CHANNEL_ID', ''),
    'admin_chat_id' => env('TELEGRAM_ADMIN_CHAT_ID', ''),
],
```

---

## 🎯 KARAR GEREKTİREN NOKTALAR

### 1. Bot Token Değişikliği
**Soru:** Mevcut `@Yalihan_gorev_bot` token'ını mı kullanacağız, yoksa `YalihanCortex_Bot` token'ını mı?

**Öneri:** 
- Eğer `YalihanCortex_Bot` token'ı varsa → Onu kullan
- Eğer yoksa → Mevcut bot'u `YalihanCortex_Bot` olarak yeniden adlandır

### 2. Kanal Kullanımı
**Soru:** `Yalıhan Channel` zaten var mı, yoksa oluşturulacak mı?

**Öneri:**
- Mevcut kanal ID: `-1003037949764`
- Bu kanalı kullanabiliriz
- Veya yeni bir kanal oluşturulabilir

### 3. Müşteri Botu
**Soru:** `@YalihanEmlak_Bot` şimdi mi oluşturulacak, yoksa gelecekte mi?

**Öneri:**
- Şimdilik `YalihanCortex_Bot` ile devam et
- Müşteri botu için ayrı planlama yap

---

## 📝 SONRAKİ ADIMLAR

1. **Bot Token Belirleme**
   - `YalihanCortex_Bot` token'ı var mı kontrol et
   - Varsa `.env` dosyasına ekle
   - Yoksa mevcut bot'u kullan

2. **Kanal Yapılandırması**
   - Mevcut kanal ID'yi doğrula
   - Kanal erişimini kontrol et
   - Bot'u kanala admin olarak ekle

3. **Test ve Doğrulama**
   - Bot token'ı test et
   - Kanal mesaj gönderme testi
   - Webhook çalışma testi

4. **Dokümantasyon Güncelleme**
   - Bot kullanım kılavuzu
   - Kanal kullanım kılavuzu
   - Entegrasyon dokümantasyonu

---

## 🔗 İLGİLİ DOSYALAR

- `app/Services/Telegram/TelegramBrain.php` - Ana mesaj yönlendirici
- `app/Services/TelegramService.php` - Kritik bildirim servisi
- `app/Modules/TakimYonetimi/Services/TelegramBotService.php` - Bot servisi
- `docs/telegram/TELEGRAM_BOT_TEST_GUIDE.md` - Test rehberi

---

**Son Güncelleme:** 01 Aralık 2025

