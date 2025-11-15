# 🤖 Yalıhan Emlak AI Özellikleri Rehberi

**AnythingLLM Training Module 1**  
**Version:** 1.0.0  
**Güncelleme:** 11 Ekim 2025

---

## 🎯 AI ÖZELLİKLERİ GENEL BAKIŞ

### **5 Aktif AI Provider:**

#### **1. Ollama Local AI (Varsayılan)**

```yaml
Endpoint: http://51.75.64.121:11434
Model: gemma2:2b
Dil: Türkçe ✅
Hız: ~2 saniye
Maliyet: Ücretsiz (local)
Kullanım: İlan başlık/açıklama, fiyat önerileri, lokasyon analizi
```

#### **2. OpenAI GPT-4**

```yaml
API: OpenAI API
Model: gpt-4
Kullanım: Karmaşık içerik, çoklu dil
Maliyet: Token bazlı
```

#### **3. Google Gemini**

```yaml
API: Google AI API
Model: gemini-2.5-flash
Kullanım: Görsel analiz, OCR
Özellik: Vision API
```

#### **4. Anthropic Claude**

```yaml
API: Anthropic API
Model: claude-3
Kullanım: Kod review, kalite kontrolü
```

#### **5. DeepSeek AI**

```yaml
API: DeepSeek API
Kullanım: Kod analizi, optimizasyon
```

---

## 🎨 AI İÇERİK ÜRETİMİ

### **1. Başlık Üretimi (Title Generation)**

**Service:** `OllamaService::generateTitle()`  
**Endpoint:** `POST /stable-create/ai-suggest?action=title`

**Input Parameters:**

```json
{
    "kategori": "Villa",
    "lokasyon": "Bodrum Yalıkavak",
    "yayin_tipi": "Satılık",
    "fiyat": "3.5M ₺",
    "ai_tone": "luks",
    "ai_variant_count": 3
}
```

**Output:**

```json
{
    "success": true,
    "variants": [
        "Yalıkavak Deniz Manzaralı Satılık Lüks Villa - 3.5M ₺",
        "Bodrum Yalıkavak'ta Özel Havuzlu Satılık Villa",
        "Yalıkavak Premium Lokasyonda Denize Sıfır Villa"
    ],
    "count": 3,
    "model": "gemma2:2b"
}
```

**Kurallar:**

- Her başlık 60-80 karakter
- Lokasyon ZORUNLU
- SEO anahtar kelimeleri
- Fiyat opsiyonel (lüks ton'da gösterilmez)
- Emoji YASAK

---

### **2. Açıklama Üretimi (Description Generation)**

**Service:** `OllamaService::generateDescription()`  
**Endpoint:** `POST /stable-create/ai-suggest?action=description`

**Input Parameters:**

```json
{
    "kategori": "Daire",
    "lokasyon": "Bodrum Merkez",
    "fiyat": "2.5M ₺",
    "metrekare": 120,
    "oda_sayisi": "3+1",
    "ozellikler": ["Deniz manzarası", "Havuzlu site", "Asansör"],
    "ai_tone": "seo"
}
```

**Output:**

```json
{
    "success": true,
    "description": "Bodrum Merkez'in en gözde lokasyonlarından birinde...",
    "length": 245,
    "model": "gemma2:2b"
}
```

**Kurallar:**

- 200-250 kelime
- 3 paragraf
- SEO uyumlu
- Profesyonel dil
- Türkçe gramer kurallarına uygun

**Paragraf Yapısı:**

```
Paragraf 1: Genel tanıtım + Lokasyon avantajları (60-80 kelime)
Paragraf 2: Özellikler ve teknik detaylar (80-100 kelime)
Paragraf 3: Çevre, ulaşım, yatırım değeri (60-80 kelime)
```

---

### **3. Lokasyon Analizi (Location Analysis)**

**Service:** `OllamaService::analyzeLocation()`  
**Endpoint:** `POST /stable-create/ai-suggest?action=location`

**Input:**

```json
{
    "il": "Muğla",
    "ilce": "Bodrum",
    "mahalle": "Yalıkavak",
    "latitude": 37.0902,
    "longitude": 27.4305,
    "nearby_poi": [
        { "type": "okul", "distance": 350 },
        { "type": "hastane", "distance": 820 },
        { "type": "market", "distance": 180 }
    ]
}
```

**Output:**

```json
{
    "success": true,
    "analysis": {
        "score": 92,
        "grade": "A",
        "potential": "Yüksek"
    },
    "reasoning": "Premium lokasyon, tüm sosyal tesislere yakın, yüksek yatırım potansiyeli",
    "model": "gemma2:2b"
}
```

**Skor Kriterleri:**

- Merkeze yakınlık: +25 puan
- Sosyal tesisler: +20 puan
- Ulaşım: +20 puan
- Altyapı: +20 puan
- Gelişim potansiyeli: +15 puan

---

### **4. Fiyat Önerisi (Price Suggestion)**

**Service:** `OllamaService::suggestPrice()`  
**Endpoint:** `POST /stable-create/ai-suggest?action=price`

**Input:**

```json
{
    "base_price": 3500000,
    "kategori": "Villa",
    "metrekare": 250,
    "lokasyon": "Yalıkavak"
}
```

**Output:**

```json
{
    "success": true,
    "suggestions": [
        {
            "label": "Pazarlık Payı (-10%)",
            "reason": "Hızlı satış için önerilen",
            "value": 3150000,
            "formatted": "3.150.000 ₺"
        },
        {
            "label": "Piyasa Ortalaması (+5%)",
            "reason": "Bölge ortalamasına göre",
            "value": 3675000,
            "formatted": "3.675.000 ₺"
        },
        {
            "label": "Premium Fiyat (+15%)",
            "reason": "Özel özellikler için",
            "value": 4025000,
            "formatted": "4.025.000 ₺"
        }
    ],
    "price_per_sqm": "14.000 ₺/m²",
    "model": "gemma2:2b"
}
```

---

## 🖼️ GÖRSEL ANALİZİ (Google Gemini)

### **Özellikler:**

- OCR (Tapu, belge okuma)
- Nesne tanıma (mobilya, mimari)
- Kalite skorlama (1-10)
- Otomatik etiketleme

### **Kullanım:**

```json
{
    "endpoint": "/api/ai/image-analysis",
    "input": "base64_image_data",
    "output": {
        "objects": ["havuz", "bahçe", "modern_mutfak"],
        "quality_score": 8.5,
        "suggested_tags": ["lüks", "modern", "deniz_manzarası"],
        "ocr_text": "Tapu Senedi: Ada 126, Parsel 7"
    }
}
```

---

## 🎯 CRM AI ÖZELLİKLERİ

### **Müşteri Segmentasyonu:**

**Input:**

```json
{
    "kisi_id": 123,
    "musteri_tipi": "Alıcı",
    "butce": "2M-3M ₺",
    "tercih_bolge": "Bodrum, Yalıkavak"
}
```

**AI Çıktısı:**

```json
{
    "crm_score": 85,
    "segment": "Premium Alıcı",
    "match_potential": "Yüksek",
    "recommended_listings": [
        { "id": 234, "match_score": 92 },
        { "id": 567, "match_score": 88 }
    ],
    "insights": "Yatırım odaklı, denize yakın villa arıyor"
}
```

---

## 🔄 ÇOKLU DİL DESTEĞİ

### **Desteklenen Diller:**

```yaml
TR: Türkçe (Ana dil)
EN: English
DE: Deutsch
RU: Русский
AR: العربية
FR: Français
```

### **Çeviri Kuralları:**

- Ana açıklama: Türkçe (zorunlu)
- Özet: Diğer diller (opsiyonel)
- SEO: Dil bazlı anahtar kelimeler
- Format: Her dil için ayrı alan

**Örnek:**

```json
{
    "tr": "Yalıkavak'ta denize sıfır lüks villa...",
    "en": "Luxury villa by the sea in Yalıkavak...",
    "de": "Luxusvilla am Meer in Yalıkavak...",
    "ru": "Роскошная вилла у моря в Ялыкаваке..."
}
```

---

## ⚡ PERFORMANS OPTİMİZASYONU

### **Cache Stratejisi:**

```yaml
İlan Başlığı Cache:
    Key: ollama_title_{md5(params)}
    TTL: 1 saat

İlan Açıklaması Cache:
    Key: ollama_desc_{md5(params)}
    TTL: 1 saat

Fiyat Analizi:
    Cache: YOK (real-time)

Lokasyon Analizi:
    Key: ollama_location_{md5(lokasyon)}
    TTL: 24 saat
```

### **Fallback Sistemi:**

```yaml
Primary: Ollama gemma2:2b
Fallback: Şablon bazlı öneriler

Örnek:
    - Ollama çalışmazsa → "Yalıkavak Satılık Villa" formatında basit başlık
    - Timeout (30s) → Fallback devreye girer
    - Error logging → Hata kaydedilir
```

---

## 🎯 KALİTE METRİKLERİ

### **Başlık Kalitesi:**

```
Uzunluk: 60-80 karakter (Optimal)
SEO Skor: >80/100
Anahtar Kelime: 3-5 adet
Lokasyon: Zorunlu
Fiyat: Opsiyonel (ton'a göre)
```

### **Açıklama Kalitesi:**

```
Kelime: 200-250
Paragraf: 3 adet
SEO Skor: >85/100
Okunabilirlik: >80/100
Gramer: %100 doğru
```

### **Yanıt Hızı:**

```
Başlık: <2 saniye
Açıklama: <3 saniye
Analiz: <2 saniye
Fiyat: <1 saniye
```

---

## 🚀 KULLANIM FLOWU

### **1. İlan Oluşturma:**

```
User: stable-create sayfasını açar
  ↓
User: Kategori, lokasyon, fiyat girer
  ↓
User: "Başlık Üret" butonuna tıklar
  ↓
Frontend: POST /stable-create/ai-suggest { action: "title" }
  ↓
Backend: OllamaService::generateTitle()
  ↓
Ollama: gemma2:2b modeli çalışır
  ↓
Backend: 3 başlık varyantı döner
  ↓
Frontend: Başlıklar kullanıcıya gösterilir
  ↓
User: Bir başlık seçer, input'a yazılır
```

### **2. Tümünü Üret:**

```
User: "Tümünü Üret" butonuna tıklar
  ↓
AI: Sırayla çalışır
  ├─ generateTitle() → 3 başlık
  ├─ generateDescription() → Açıklama
  ├─ analyzeLocation() → Lokasyon skoru
  └─ suggestPrice() → Fiyat önerileri (eğer doldurulmuşsa)
  ↓
Frontend: Tüm öneriler gösterilir
  ↓
User: İstediğini seçer ve uygular
```

---

## 💾 DATABASE ENTEGRASYONU

### **AI Log Tablosu:**

```sql
ai_chat_logs
├── id
├── user_id
├── prompt (kullanıcı sorusu)
├── response (AI yanıtı)
├── provider (ollama, openai, gemini)
├── model (gemma2:2b, gpt-4)
├── tokens_used
├── response_time
├── created_at
```

### **AI Knowledge Base:**

```sql
ai_knowledge_base
├── id
├── category (ilan, crm, arsa, yazlik)
├── content (öğrenilmiş bilgi)
├── tags (JSON)
├── usage_count
├── last_used_at
```

### **AI Embeddings:**

```sql
ai_embeddings
├── id
├── knowledge_base_id
├── embedding_vector (JSON)
├── model_name (text-embedding-ada-002)
├── dimensions (1536)
├── similarity_threshold (0.7)
```

---

## 🎯 AI ÖĞRENME ve FEEDBACK

### **Learning Loop:**

```
1. AI Öneri Üretir
   ↓
2. Kullanıcı Düzenler/Onaylar
   ↓
3. Düzenlemeler Kaydedilir (ai_chat_logs)
   ↓
4. AI Feedback Analizi (haftalık)
   ↓
5. Prompt İyileştirmesi
   ↓
6. Sonraki Önerilerde Daha İyi
```

### **Feedback Metrikleri:**

```yaml
Kabul Oranı: Öneri olduğu gibi kullanıldı mı?
Düzenleme Yüzdesi: Ne kadar değiştirildi?
Reddedilme: Öneri hiç kullanılmadı mı?

Target:
    Kabul Oranı: >70
    Düzenleme: <30%
    Red: <10%
```

---

## 🎨 TON PROFİLLERİ DETAYLI

### **SEO Tone:**

**Ne zaman kullan:** Genel ilanlar, geniş kitle
**Anahtar Kelimeler:** Yüksek yoğunluk (%2-3)
**CTA:** Orta seviye
**Örnek:**

```
"Bodrum Yalıkavak'ta Satılık Villa - Deniz Manzaralı 5+2 Havuzlu Lüks Konut
Yalıkavak bölgesinin en prestijli noktasında, denize sıfır konumda satılık villa.
5 yatak odası, 2 salon, özel havuz, 500m² arsa. Yatırım fırsatı!"
```

### **Kurumsal Tone:**

**Ne zaman kullan:** Yatırımcılar, kurumsal alıcılar
**Dil:** Profesyonel, resmi
**Vurgu:** Yatırım değeri, teknik detaylar
**Örnek:**

```
"Yalıkavak Bölgesinde Yüksek Yatırım Getirili Villa Projesi
Prime lokasyonda konumlanmış villa, bölgenin artan değerine paralel olarak
yıllık %15-20 değer artış potansiyeli sunmaktadır. Detaylı bilgi için..."
```

### **Hızlı Satış Tone:**

**Ne zaman kullan:** Acil satış, fırsat ilanları
**Dil:** Heyecan verici, aciliyet
**Vurgu:** İndirim, fırsat, kısıtlı süre
**Örnek:**

```
"FIRSATTAN KAÇIRMAYIN! Yalıkavak Denize Sıfır Villa - %15 İndirimli!
Sadece bu hafta! Piyasa değerinin altında, özel havuzlu, denize sıfır villa.
İlk gören alır! HEMEN ARAYIN!"
```

### **Lüks Tone:**

**Ne zaman kullan:** Luxury segment, premium ilanlar
**Dil:** Prestijli, özel
**Vurgu:** Kalite, ayrıcalık, eşsizlik
**Fiyat:** Gösterilmez (talep üzerine)
**Örnek:**

```
"Yalıkavak'ın En Prestijli Noktasında Eşsiz Villa - Exclusive Collection
Denizle iç içe, mükemmel mimari detaylarla tasarlanmış bu özel villa,
ayrıcalıklı yaşam arayanlar için benzersiz bir fırsat sunuyor.
Detaylar için lütfen bizimle iletişime geçin."
```

---

## 🧠 RAG (Retrieval-Augmented Generation)

### **Benzer İlan Örnekleri:**

AI, yeni öneri üretirken benzer ilanlardan öğrenir:

```
1. Embedding ile benzer ilanları bul (cosine similarity >0.8)
2. Başarılı örnekleri referans al
3. Stil ve yapıyı kopyala
4. Yeni içerik üret

Örnek:
  Yeni Villa İlanı için:
  → Son 5 başarılı villa ilanını bul
  → Ortak başlık yapısını tespit et
  → Aynı stilde yeni başlık üret
```

---

## 🎯 ÖZEL KULLANIM ALANLARI

### **1. Arsa İlanları AI:**

```
Vurgu:
  - KAKS/TAKS değerleri
  - İmar durumu
  - Yatırım potansiyeli
  - İnşaat alanı hesaplama

AI Önerileri:
  - TKGM sorgulama önerisi
  - m² başı fiyat karşılaştırma
  - Benzer arsalarda satış süresi
```

### **2. Yazlık İlanları AI:**

```
Vurgu:
  - Sezonluk özellikler
  - Aktivite imkanları
  - Denize mesafe
  - Haftalık fiyat

AI Önerileri:
  - Sezon bazlı fiyat optimizasyonu
  - Rezervasyon tahmini
  - Benzer yazlıkların doluluk oranı
```

### **3. Ticari İlanları AI:**

```
Vurgu:
  - İş potansiyeli
  - Ciro tahmini
  - Müşteri trafiği
  - Ruhsat durumu

AI Önerileri:
  - Sektör bazlı uygunluk
  - Kira getirisi hesaplama
  - Rekabet analizi
```

---

## 📊 AI PERFORMANS İZLEME

### **Monitoring Endpoints:**

```bash
GET /stable-create/ai-health
Response: {
  "success": true,
  "model": "gemma2:2b",
  "endpoint": "http://51.75.64.121:11434",
  "status": "online"
}
```

### **Metrikler:**

```yaml
Response Time: <3s (Target)
Success Rate: >95%
Error Rate: <5%
Cache Hit: >70%
User Satisfaction: >4.5/5
```

### **Log Analizi:**

```sql
-- En çok kullanılan özellikler
SELECT
  JSON_EXTRACT(prompt, '$.action') as feature,
  COUNT(*) as usage_count,
  AVG(response_time) as avg_time
FROM ai_chat_logs
WHERE provider = 'ollama'
GROUP BY feature
ORDER BY usage_count DESC;
```

---

## 🔐 GÜVENLİK ve LİMİTLER

### **Rate Limiting:**

```yaml
AI Endpoints:
    Kullanıcı başına: 10 request/minute
    IP başına: 20 request/minute

Fallback: 429 Too Many Requests durumunda
    → Cached yanıt döndür
    → Veya şablon bazlı öneri
```

### **Cost Control:**

```yaml
Günlük Limit:
    Ollama: Sınırsız (local)
    OpenAI: $50/gün
    Gemini: $20/gün

Alert:
    %80 kullanımda: Email uyarısı
    %100'de: Auto-switch to fallback
```

---

## 🎓 AI EĞİTİM NOTLARI

### **Sistem Davranışları:**

1. **Önce Cache Kontrol:** Her istekte önce cache'e bak
2. **Sonra Primary (Ollama):** Cache yoksa Ollama'ya sor
3. **Fallback:** Ollama çalışmazsa şablon kullan
4. **Log:** Tüm AI işlemlerini kaydet
5. **Learn:** Kullanıcı feedback'ini öğren

### **Özel Durumlar:**

```yaml
Boş Input: → Fallback şablonlar kullan
    → Kullanıcıya "Daha fazla bilgi ekleyin" öner

Çok Uzun İçerik: → Token limiti kontrol et
    → Gerekirse chunk'lara böl

Hatalı Kategori: → "Genel" kategorisi için default prompt
```

---

## 🆕 **YENİ AI ÖZELLİKLERİ (v3.4.0)**

### **1. AI İlan Geçmişi Analizi** 🎯

**Service:** `IlanGecmisAIService`  
**Endpoint:** `GET /api/kisiler/{id}/ai-gecmis-analiz`

**Özellikler:**

- Kişinin önceki 20 ilanını analiz et
- Başlık kalitesi (uzunluk, SEO, format)
- Açıklama analizi (kelime sayısı, detay seviyesi)
- Fiyat trendi (artış/azalış/stabil)
- Kategori tercihleri
- Lokasyon dağılımı
- Fotoğraf kullanımı
- Başarı metrikleri

**Kullanım:**

```javascript
// Frontend'de kişi seçildiğinde
async function loadKisiHistory(kisiId) {
    const response = await fetch(`/api/kisiler/${kisiId}/ai-gecmis-analiz`);
    const data = await response.json();

    if (data.has_history) {
        // Önerileri göster
        data.oneriler.forEach((oneri) => {
            window.toast.info(oneri, 5000);
        });
    }
}
```

### **2. Kategori Bazlı Dinamik Alanlar** 📋

**Service:** `KategoriOzellikService`

**Kategoriler:**

- **Arsa**: Ada/Parsel, İmar, TAKS/KAKS
- **Yazlık**: Havuz, Minimum konaklama, Sezon
- **Villa**: Bahçe, Otopark, Havuz
- **Daire**: Oda sayısı, Banyo, Net m²
- **İşyeri**: İşyeri tipi, Ciro, Personel
- **Turistik Tesis**: Oda sayısı, Yıldız, Yatak kapasitesi

**Kullanım:**

```php
$service = app(\App\Services\KategoriOzellikService::class);
$fields = $service->getOzelliklerByKategori($kategoriId);

// Required, recommended, optional fields döner
```

### **3. TKGM Parsel Sorgulama** 🏛️

**Service:** `TKGMService`  
**Endpoint:** `POST /api/tkgm/parsel-sorgu`

**Özellikler:**

- Ada/Parsel → TKGM API sorgu
- Otomatik alan doldurma
- TAKS/KAKS hesaplama
- İmar durumu tespit
- Yatırım potansiyeli analizi
- Cache sistemi (1 saat)

**Kullanım:**

```javascript
async function queryTKGM() {
    const response = await fetch('/api/tkgm/parsel-sorgu', {
        method: 'POST',
        body: JSON.stringify({
            ada: '126',
            parsel: '7',
            il: 'Muğla',
            ilce: 'Bodrum',
        }),
    });

    const result = await response.json();

    if (result.success) {
        // Alanları otomatik doldur
        document.getElementById('alan_m2').value = result.parsel_bilgileri.yuzolcumu;
        document.getElementById('taks').value = result.parsel_bilgileri.taks;
        document.getElementById('kaks').value = result.parsel_bilgileri.kaks;

        // Önerileri göster
        result.oneriler.forEach((oneri) => window.toast.info(oneri));
    }
}
```

---

**🤖 Bu doküman, AI'nin tüm içerik üretimi görevlerini nasıl yapacağını öğretir.**

**Güncelleme:** v3.4.0 - 3 yeni AI özelliği eklendi (11 Ekim 2025)
