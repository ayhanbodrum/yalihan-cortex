# 🎤 Voice-to-CRM Sistemi - Sesli Komut ile Hızlı Kayıt

**Context7 Standardı:** C7-VOICE-TO-CRM-2025-11-27

## 📋 Genel Bakış

Sahadaki danışmanın telefon geldiğinde veya yeni bir talep aldığında, form doldurma yükünü azaltmak için sesli komut ile hızlı kayıt sistemi. Danışman sadece sesli mesaj gönderir, sistem otomatik olarak **Kisi** ve **Talep** draft kayıtlarını oluşturur.

## 🎯 Senaryo

**Sorun:** Danışman sahadayken telefon geliyor. "Yeni talep, Ahmet Yılmaz, 10 milyon TL, Bodrum Yalıkavak'ta villa arıyor." diyor. Ancak form doldurmak için zaman yok.

**Çözüm:** 5 saniyelik sesli komut → Sistem otomatik kayıt oluşturur.

## 🔄 İş Akışı

```
1. Danışman → Sesli mesajı Telegram/WhatsApp grubuna gönderir (5 saniye)
2. n8n Bot → Sesli mesajı yakalar (1 saniye)
3. Voice-to-Text → Ollama/Cloudflare Tüneli ile metne çevirir (10 saniye)
4. YalihanCortex → NLP ile JSON'a çevirir (5 saniye)
5. CRM → Kisi ve Talep draft kayıtları oluşturur (2 saniye)
6. Geri Bildirim → "✅ Kayıt alındı. Formu daha sonra doldurabilirsiniz." (2 saniye)

TOPLAM: ~25 saniye
```

## 🛠️ Teknik Detaylar

### 1. YalihanCortex Metodu

```php
// app/Services/AI/YalihanCortex.php

public function createDraftFromText(string $rawText, int $danismanId, array $options = []): array
```

**Özellikler:**
- ✅ NLP ile doğal dili JSON'a çevirme
- ✅ Fallback regex parsing (AI başarısız olursa)
- ✅ Kisi otomatik oluşturma/bulma (telefon/email ile)
- ✅ Talep draft kayıt oluşturma
- ✅ İl/İlçe/Mahalle otomatik eşleştirme
- ✅ Güven skoru (confidence_score) ile doğruluk ölçümü

### 2. API Endpoint

```
POST /api/v1/admin/ai/voice-to-crm
```

**Request:**
```json
{
  "text": "Yeni talep, Ahmet Yılmaz, 10 milyon TL, Bodrum Yalıkavak'ta villa arıyor.",
  "danisman_id": 1
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "kisi_id": 123,
    "talep_id": 456,
    "kisi": {
      "id": 123,
      "ad": "Ahmet",
      "soyad": "Yılmaz",
      "telefon": null,
      "email": null
    },
    "talep": {
      "id": 456,
      "baslik": "Bodrum Yalıkavak'ta Villa Arayışı",
      "status": "Taslak",
      "tip": "Satılık"
    },
    "message": "✅ Kayıt alındı. Formu daha sonra doldurabilirsiniz.",
    "metadata": {
      "processed_at": "2025-11-27T10:00:00Z",
      "algorithm": "YalihanCortex v1.0",
      "duration_ms": 5234,
      "confidence_score": 85
    }
  }
}
```

### 3. NLP Prompt Sistemi

**Prompt Örneği:**
```
Sen bir emlak CRM sistemi için doğal dil işleme (NLP) uzmanısın. 
Aşağıdaki Türkçe metni analiz edip JSON formatına çevir.

**Giriş Metni:**
Yeni talep, Ahmet Yılmaz, 10 milyon TL, Bodrum Yalıkavak'ta villa arıyor.

**Çıktı Formatı:**
{
  "kisi": {
    "ad": "Ahmet",
    "soyad": "Yılmaz",
    "telefon": null,
    "email": null
  },
  "talep": {
    "tip": "Satılık",
    "baslik": "Bodrum Yalıkavak'ta Villa Arayışı",
    "min_fiyat": 10000000,
    "il_adi": "Muğla",
    "ilce_adi": "Bodrum",
    "mahalle_adi": "Yalıkavak",
    "kategori": "Villa"
  },
  "confidence_score": 85
}
```

### 4. Fallback Regex Parsing

AI başarısız olursa basit regex ile parse eder:
- İsim/Soyisim: `([A-ZÇĞİÖŞÜ][a-zçğıöşü]+)\s+([A-ZÇĞİÖŞÜ][a-zçğıöşü]+)`
- Telefon: `(0[0-9]{10})`
- Fiyat: `(\d+)\s*(?:milyon|m)`
- Lokasyon: İl/İlçe listesi ile eşleştirme

## 📊 Örnek Kullanım Senaryoları

### Senaryo 1: Basit Talep
```
Metin: "Yeni talep, Ahmet Yılmaz, 10 milyon TL, Bodrum Yalıkavak'ta villa arıyor."
→ Kisi: Ahmet Yılmaz
→ Talep: Satılık Villa, 10M TL, Bodrum Yalıkavak
```

### Senaryo 2: Telefon ile
```
Metin: "Mehmet Bey aradı, 05321234567, 5 milyon TL'ye ev arıyor İstanbul'da."
→ Kisi: Mehmet (telefon: 05321234567)
→ Talep: Satılık Daire, 5M TL, İstanbul
```

### Senaryo 3: Detaylı Bilgi
```
Metin: "Ayşe Hanım, ayse@email.com, Bodrum'da kiralık villa arıyor, aylık 50 bin TL."
→ Kisi: Ayşe (email: ayse@email.com)
→ Talep: Kiralık Villa, 50.000 TL/ay, Bodrum
```

## 🔗 n8n Entegrasyonu

### Webhook Trigger
```
POST /api/v1/admin/ai/voice-to-crm
```

### n8n Workflow Örneği

```yaml
Workflow: Voice-to-CRM
Triggers:
  - Telegram Webhook (sesli mesaj geldiğinde)
  
Actions:
  1. Voice-to-Text (Ollama/Cloudflare Tüneli)
     - Input: Sesli mesaj dosyası
     - Output: Metin
  
  2. HTTP Request (Laravel API)
     - Method: POST
     - URL: http://localhost:8000/api/v1/admin/ai/voice-to-crm
     - Body:
       text: {{ $json.transcript }}
       danisman_id: {{ $json.user_id }}
  
  3. Telegram Notifier (Geri bildirim)
     - Message: ✅ Kayıt alındı. Formu daha sonra doldurabilirsiniz.
     - Chat ID: {{ $json.user_id }}
```

## 📝 Dokümantasyon

- **YalihanCortex Metodu:** `app/Services/AI/YalihanCortex.php`
- **API Controller:** `app/Http/Controllers/Api/AIController.php`
- **Route:** `routes/api/v1/ai.php`
- **Context7 Standardı:** C7-VOICE-TO-CRM-2025-11-27

## 🚀 Geliştirme Yol Haritası

### Tamamlanan ✅
- [x] YalihanCortex::createDraftFromText() metodu
- [x] NLP prompt sistemi
- [x] Fallback regex parsing
- [x] API endpoint
- [x] Kisi ve Talep draft kayıt oluşturma

### Planlanan 🔄
- [ ] n8n workflow entegrasyonu
- [ ] Telegram/WhatsApp bot entegrasyonu
- [ ] Voice-to-Text servis entegrasyonu
- [ ] Gerçek zamanlı bildirimler
- [ ] Dashboard widget (son sesli kayıtlar)

## ⚠️ Önemli Notlar

1. **Güvenlik:** API endpoint authentication gerektirir (`auth` middleware)
2. **Rate Limiting:** n8n bot için rate limiting uygulanabilir
3. **Error Handling:** Fallback parsing her zaman çalışır (confidence_score: 30)
4. **Draft Status:** Tüm kayıtlar `status: "Taslak"` olarak oluşturulur
5. **Validation:** Minimum 10 karakter, maksimum 2000 karakter metin kabul edilir

## 📚 Referanslar

- **YalihanCortex:** `app/Services/AI/YalihanCortex.php`
- **AIService:** `app/Services/AIService.php`
- **Talep Model:** `app/Models/Talep.php`
- **Kisi Model:** `app/Models/Kisi.php`
- **Context7 Standardları:** `.context7/authority.json`






