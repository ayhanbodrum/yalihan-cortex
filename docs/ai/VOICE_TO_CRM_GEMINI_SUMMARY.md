# 🎤 Voice-to-CRM: Gemini AI Özeti

**Tarih:** 1 Aralık 2025  
**Proje:** Yalıhan Emlak OS  
**Sistem:** Voice-to-CRM (Whisper + Ollama)

---

## 📋 GENEL BAKIŞ

Yalıhan Emlak OS'a **Voice-to-CRM** sistemi eklendi. Bu sistem, Telegram'dan gelen sesli notları alıp, Whisper ile yazıya çevirip, Ollama ile analiz edip CRM aksiyonuna dönüştürüyor.

---

## 🏗️ MİMARİ

### **3 Ana Bileşen:**

1. **AudioTranscriptionService** (`app/Services/AudioTranscriptionService.php`)
   - Whisper API entegrasyonu
   - Telegram voice dosyası indirme
   - Ses dosyasını yazıya çevirme

2. **VoiceCommandProcessor** (`app/Services/VoiceCommandProcessor.php`)
   - Ollama ile intent analizi
   - Müşteri adı çıkarma
   - Tarih/deadline tespiti
   - CRM aksiyonu uygulama

3. **TelegramBotService** (güncellendi)
   - Voice mesaj işleme
   - Kullanıcı doğrulama
   - Onay mesajı gönderme

---

## 🔄 İŞ AKIŞI

```
Telegram Voice → İndir → Whisper (Yazıya Çevir) → Ollama (Analiz Et) → CRM Aksiyonu → Onay Mesajı
```

**Detaylı:**
1. Telegram'dan sesli not gelir
2. Voice dosyası indirilir (Telegram API)
3. Whisper API ile yazıya çevrilir
4. Ollama ile intent analizi yapılır (JSON çıktı)
5. CRM aksiyonu uygulanır (KisiNot veya Gorev)
6. Danışmana onay mesajı gönderilir

---

## 💻 KOD ÖRNEKLERİ

### **Ses Transkripsiyonu:**
```php
$audioService = new AudioTranscriptionService();
$transcript = $audioService->transcribe($localFilePath);
```

### **Komut Analizi:**
```php
$commandProcessor = new VoiceCommandProcessor();
$commandData = $commandProcessor->process($transcript, $consultantId);
// Returns: ['intent', 'client_name', 'note_body', 'due_date', 'action_type']
```

### **CRM Aksiyonu:**
```php
$result = $commandProcessor->executeAction($commandData, $consultantId);
// Creates: KisiNot or Gorev
```

---

## 🎯 KULLANIM SENARYOLARI

### **Senaryo 1: Görüşme Notu**
**Sesli Not:**
> "Ahmet Yılmaz ile görüştüm. Bodrum'daki villayı beğendi. Yarın tekrar arayacağım."

**Sonuç:**
- ✅ KisiNot oluşturuldu
- Müşteri: Ahmet Yılmaz (bulundu)
- Tip: görüşme
- İçerik: Transkript edilmiş metin

### **Senaryo 2: Görev Oluşturma**
**Sesli Not:**
> "Ahmet Yılmaz için yarın saat 14:00'te randevu ayarla. Görev oluştur."

**Sonuç:**
- ✅ Gorev oluşturuldu
- Başlık: "Ahmet Yılmaz için yarın saat 14:00'te randevu ayarla"
- Deadline: 2025-12-02
- Status: beklemede

---

## 🔧 KONFİGÜRASYON

### **.env Ayarları:**
```env
WHISPER_URL=http://whisper:9000
WHISPER_TIMEOUT=60
OLLAMA_URL=http://ollama:11434
OLLAMA_MODEL=llama3.2
OLLAMA_TIMEOUT=30
```

### **Docker Servisleri:**
- Whisper: `http://whisper:9000` (ses transkripsiyonu)
- Ollama: `http://ollama:11434` (LLM analizi)

---

## 🛡️ HATA YÖNETİMİ

- **Retry:** 3 deneme, 1 saniye bekleme
- **Fallback:** Ollama başarısız olursa keyword matching
- **Cleanup:** Geçici dosyalar otomatik silinir
- **Logging:** Tüm adımlar loglanır

---

## 📊 VERİTABANI

### **KisiNot (Görüşme Notu):**
- `kisi_id` - Müşteri ID
- `user_id` - Danışman ID
- `aciklama` - Not içeriği
- `tip` - 'görüşme'
- `görüşme_tarihi` - Tarih

### **Gorev (Görev):**
- `baslik` - Görev başlığı
- `aciklama` - Görev açıklaması
- `danisman_id` - Danışman ID
- `kisi_id` - Müşteri ID
- `status` - 'beklemede'
- `deadline` - Bitiş tarihi

---

## ✅ CONTEXT7 UYUMLULUK

- ✅ `declare(strict_types=1);` kullanılıyor
- ✅ İngilizce kolon isimleri
- ✅ KisiNot modeli (GorusmeNotu değil)
- ✅ Kapsamlı hata yönetimi
- ✅ Tüm adımlar loglanıyor

---

## 📚 DETAYLI DOKÜMANTASYON

Tam dokümantasyon: `docs/ai/VOICE_TO_CRM_IMPLEMENTATION_2025-12-01.md`

---

**Son Güncelleme:** 1 Aralık 2025  
**Hazırlayan:** Yalıhan Cortex Development Team

