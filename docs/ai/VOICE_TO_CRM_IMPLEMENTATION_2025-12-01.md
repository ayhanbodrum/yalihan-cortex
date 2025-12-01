# 🎤 Voice-to-CRM Implementation

**Tarih:** 1 Aralık 2025  
**Context7 Standard:** C7-VOICE-TO-CRM-2025-12-01  
**Durum:** ✅ TAMAMLANDI

---

## 📋 GENEL BAKIŞ

Telegram'dan gelen sesli notları alıp, Whisper ile yazıya çevirip, Ollama ile analiz edip CRM aksiyonuna dönüştüren sistem.

**Akış:**
1. Telegram → Voice mesaj gelir
2. Whisper API → Ses dosyası yazıya çevrilir
3. Ollama → Metin analiz edilir (intent, müşteri, not, tarih)
4. CRM → Görüşme Notu veya Görev oluşturulur
5. Telegram → Danışmana onay mesajı gönderilir

---

## 🏗️ MİMARİ

### **1. AudioTranscriptionService**

**Dosya:** `app/Services/AudioTranscriptionService.php`

**Özellikler:**
- Whisper API entegrasyonu
- Telegram voice dosyası indirme
- Geçici dosya temizleme
- Retry mekanizması (3 deneme)
- Timeout yönetimi (60 saniye)

**Metodlar:**
- `transcribe(string $localFilePath): string` - Ses dosyasını yazıya çevir
- `downloadTelegramVoice(string $fileId, string $botToken): string` - Telegram'dan dosya indir
- `cleanup(string $localFilePath): void` - Geçici dosyayı sil

**Konfigürasyon:**
```env
WHISPER_URL=http://whisper:9000
WHISPER_TIMEOUT=60
```

---

### **2. VoiceCommandProcessor**

**Dosya:** `app/Services/VoiceCommandProcessor.php`

**Özellikler:**
- Ollama ile intent analizi
- Müşteri adı çıkarma
- Tarih/deadline tespiti
- CRM aksiyonu uygulama
- Fallback parsing (Ollama başarısız olursa)

**Metodlar:**
- `process(string $text, int $consultantId): array` - Komutu analiz et
- `executeAction(array $commandData, int $consultantId): array` - CRM aksiyonunu uygula
- `createGorusmeNotu(...)` - Görüşme notu oluştur
- `createGorev(...)` - Görev oluştur

**Ollama System Prompt:**
```
Sen Yalıhan Emlak'ın CRM asistanısın. Danışmanların sesli notlarını analiz edip CRM aksiyonuna dönüştürüyorsun.

Görevlerin:
1. Intent tespit et: "not_ekle", "gorev_olustur", "randevu_ayarla"
2. Müşteri adını çıkar (varsa)
3. Not içeriğini özetle
4. Tarih/deadline varsa çıkar

Çıktı formatı (JSON):
{
  "intent": "not_ekle" | "gorev_olustur" | "randevu_ayarla",
  "client_name": "Müşteri adı (varsa)",
  "note_body": "Not içeriği",
  "due_date": "YYYY-MM-DD (varsa, yoksa null)",
  "action_type": "gorusme_notu" | "gorev" | "randevu"
}
```

**Konfigürasyon:**
```env
OLLAMA_URL=http://ollama:11434
OLLAMA_MODEL=llama3.2
OLLAMA_TIMEOUT=30
```

---

### **3. TelegramBotService Güncellemesi**

**Dosya:** `app/Modules/TakimYonetimi/Services/TelegramBotService.php`

**Değişiklikler:**
- `processVoiceMessage()` metodu eklendi
- `handleWebhook()` metodunda voice mesaj kontrolü eklendi

**Akış:**
1. Voice mesaj geldiğinde `processVoiceMessage()` çağrılır
2. Kullanıcı doğrulanır
3. Ses dosyası indirilir
4. Transkript edilir
5. Komut analiz edilir
6. CRM aksiyonu uygulanır
7. Danışmana onay mesajı gönderilir

---

## 🔧 KULLANIM

### **Telegram'dan Sesli Not Gönderme:**

1. Telegram bot'una sesli not gönder
2. Bot otomatik olarak işler:
   - "🎤 Sesli not alınıyor..."
   - "✅ Görüşme Notu oluşturuldu!"

### **Örnek Sesli Notlar:**

**Görüşme Notu:**
> "Ahmet Yılmaz ile görüştüm. Bodrum'daki villayı beğendi. Yarın tekrar arayacağım."

**Görev:**
> "Ahmet Yılmaz için yarın saat 14:00'te randevu ayarla. Görev oluştur."

---

## 📊 VERİTABANI

### **Görüşme Notu (GorusmeNotu):**
- `kisi_id` - Müşteri ID (bulunursa)
- `user_id` - Danışman ID
- `not` - Not içeriği
- `tarih` - Tarih
- `kaynak` - 'telegram_voice'

### **Görev (Gorev):**
- `baslik` - Görev başlığı
- `aciklama` - Görev açıklaması
- `user_id` - Danışman ID
- `kisi_id` - Müşteri ID (bulunursa)
- `durum` - 'beklemede'
- `oncelik` - 'normal'
- `baslangic_tarihi` - Başlangıç tarihi
- `bitis_tarihi` - Bitiş tarihi (varsa)
- `kaynak` - 'telegram_voice'

---

## 🛡️ HATA YÖNETİMİ

### **Retry Mekanizması:**
- Whisper API: 3 deneme, 1 saniye bekleme
- Ollama API: 3 deneme, 1 saniye bekleme
- 5xx hatalarında retry, 4xx hatalarında retry yok

### **Fallback:**
- Ollama başarısız olursa basit keyword matching kullanılır
- Müşteri adı regex ile çıkarılır
- Tarih regex ile çıkarılır

### **Logging:**
- Tüm adımlar loglanır
- Hata durumları detaylı loglanır
- Geçici dosya temizleme loglanır

---

## 🔐 GÜVENLİK

- Erişim kontrolü: Sadece kayıtlı kullanıcılar
- Geçici dosyalar otomatik silinir
- Timeout koruması (60 saniye Whisper, 30 saniye Ollama)
- Exception handling (tüm hatalar yakalanır)

---

## 📝 ÖRNEK KULLANIM

### **1. Sesli Not Gönderme:**
```
Kullanıcı: [Sesli not gönder]
Bot: 🎤 Sesli not alınıyor...
Bot: ✅ Görüşme Notu oluşturuldu!

📝 Transkript: Ahmet Yılmaz ile görüştüm. Bodrum'daki villayı beğendi...
```

### **2. Görev Oluşturma:**
```
Kullanıcı: [Sesli not: "Ahmet Yılmaz için yarın saat 14:00'te randevu ayarla"]
Bot: 🎤 Sesli not alınıyor...
Bot: ✅ Görev oluşturuldu!

📝 Transkript: Ahmet Yılmaz için yarın saat 14:00'te randevu ayarla...
```

---

## 🚀 DEPLOYMENT

### **Gereksinimler:**
1. Whisper servisi çalışıyor olmalı (`http://whisper:9000`)
2. Ollama servisi çalışıyor olmalı (`http://ollama:11434`)
3. `.env` dosyasında gerekli ayarlar:
   ```env
   WHISPER_URL=http://whisper:9000
   WHISPER_TIMEOUT=60
   OLLAMA_URL=http://ollama:11434
   OLLAMA_MODEL=llama3.2
   OLLAMA_TIMEOUT=30
   ```

### **Test:**
1. Telegram bot'una sesli not gönder
2. Bot'un yanıtını kontrol et
3. CRM'de görüşme notu/görev oluştuğunu kontrol et

---

## 📚 REFERANSLAR

- **Whisper API:** Docker container üzerinde çalışan yerel servis
- **Ollama API:** Docker container üzerinde çalışan yerel LLM
- **Telegram Bot API:** Telegram Bot API dokümantasyonu

---

**Son Güncelleme:** 1 Aralık 2025  
**Hazırlayan:** Yalıhan Cortex Development Team  
**Durum:** ✅ Production Ready

