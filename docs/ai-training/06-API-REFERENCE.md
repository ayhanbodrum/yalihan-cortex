# 🔌 API Reference - AI Endpoints

**AnythingLLM Training Module 6**  
**Version:** 1.0.0

---

## 🚀 AI ENDPOINTS

### **Base URL:**

```
http://127.0.0.1:8000 (Development)
https://yalihanemlak.com (Production)
```

---

## 1️⃣ İlan AI Öneri Endpoint'i

### **POST /stable-create/ai-suggest**

**Açıklama:** Stable create sayfasında AI önerileri üretir

**Headers:**

```http
Content-Type: application/json
X-CSRF-TOKEN: {csrf_token}
X-Requested-With: XMLHttpRequest
```

**Request Body:**

```json
{
    "action": "title|description|location|price|all",
    "kategori": "Villa",
    "yayin_tipi": "Satılık",
    "lokasyon": "Bodrum Yalıkavak",
    "fiyat": 3500000,
    "para_birimi": "TRY",
    "metrekare": 250,
    "oda_sayisi": "5+2",
    "ozellikler": ["Havuz", "Deniz manzarası"],
    "ai_tone": "seo|kurumsal|hizli_satis|luks",
    "ai_variant_count": 3,
    "ai_ab_test": false,
    "ai_languages": ["EN", "DE"]
}
```

**Response (Success):**

```json
{
    "success": true,
    "action": "title",
    "variants": [
        "Yalıkavak Deniz Manzaralı Satılık Villa - 5+2 Havuzlu",
        "Bodrum Yalıkavak'ta Satılık Lüks Villa - 250 m²",
        "Yalıkavak Premium Lokasyonda Satılık Villa"
    ],
    "metadata": {
        "model": "gemma2:2b",
        "provider": "ollama",
        "response_time": 2150,
        "confidence_score": 0.91,
        "cached": false
    },
    "context7_compliant": true
}
```

**Response (Error):**

```json
{
    "success": false,
    "error": "Ollama servisi ulaşılamıyor",
    "fallback": "Yalıkavak Satılık Villa",
    "metadata": {
        "error_code": "OLLAMA_TIMEOUT",
        "fallback_used": true
    }
}
```

---

## 2️⃣ Currency Rate API

### **GET /api/currency/rates**

**Açıklama:** Güncel döviz kurları

**Response:**

```json
{
    "success": true,
    "rates": {
        "TRY": 1.0,
        "USD": 34.5,
        "EUR": 37.2,
        "GBP": 43.8
    },
    "last_updated": "2025-10-11T10:30:00Z",
    "source": "Exchange Rate API",
    "cache_ttl": 3600
}
```

### **POST /api/currency/convert**

**Request:**

```json
{
    "amount": 100000,
    "from": "USD",
    "to": "TRY"
}
```

**Response:**

```json
{
    "success": true,
    "result": {
        "amount": 100000,
        "from": "USD",
        "to": "TRY",
        "converted": 3450000,
        "rate": 34.5,
        "formatted": "3.450.000 ₺"
    }
}
```

---

## 3️⃣ Live Search API

### **GET /api/hybrid-search/kisiler**

**Açıklama:** Kişi canlı arama

**Parameters:**

```
q: string (min 2 karakter)
limit: int (default 20, max 50)
format: select2|context7|react (default: context7)
```

**Response:**

```json
{
    "success": true,
    "count": 3,
    "data": [
        {
            "id": 123,
            "display_text": "Ahmet Yılmaz - 0533 XXX XX 02 - Yalıkavak",
            "tam_ad": "Ahmet Yılmaz",
            "telefon": "0533 209 03 02",
            "musteri_tipi": "Alıcı",
            "il_adi": "Muğla"
        }
    ],
    "search_metadata": {
        "query": "Ahmet",
        "response_time": 145,
        "context7_compliant": true
    }
}
```

---

## 4️⃣ Location API

### **GET /api/location/iller**

**Response:**

```json
{
    "success": true,
    "data": [
        { "id": 48, "il_adi": "Muğla", "plaka_kodu": "48" },
        { "id": 34, "il_adi": "İstanbul", "plaka_kodu": "34" }
    ]
}
```

### **GET /api/location/ilceler/{il_id}**

**Response:**

```json
{
    "success": true,
    "data": [
        { "id": 1, "ilce_adi": "Bodrum", "il_id": 48 },
        { "id": 2, "ilce_adi": "Milas", "il_id": 48 }
    ]
}
```

### **GET /api/location/mahalleler/{ilce_id}**

**Response:**

```json
{
    "success": true,
    "data": [
        { "id": 1, "mahalle_adi": "Yalıkavak", "ilce_id": 1 },
        { "id": 2, "mahalle_adi": "Gümüşlük", "ilce_id": 1 }
    ]
}
```

---

## 5️⃣ Kategori Dinamik API

### **GET /api/smart-ilan/kategoriler/{anaKategoriId}/alt-kategoriler**

**Response:**

```json
{
    "success": true,
    "data": [
        { "id": 2, "name": "Villa", "parent_id": 1 },
        { "id": 3, "name": "Daire", "parent_id": 1 }
    ]
}
```

### **GET /api/smart-ilan/kategoriler/{altKategoriId}/yayin-tipleri**

**Response:**

```json
{
    "success": true,
    "data": [
        { "id": 4, "name": "Satılık", "parent_id": 2 },
        { "id": 5, "name": "Kiralık", "parent_id": 2 }
    ]
}
```

### **GET /api/features/by-selection**

**Parameters:**

```
alt_kategori_id: int
yayin_tipi_id: int
alt_kategori_name: string
```

**Response:**

```json
{
    "success": true,
    "features": {
        "Konum Özellikleri": [
            { "id": 1, "name": "Deniz manzarası" },
            { "id": 2, "name": "Şehir manzarası" }
        ],
        "İç Özellikler": [
            { "id": 10, "name": "Asansör" },
            { "id": 11, "name": "Otopark" }
        ]
    }
}
```

---

## 6️⃣ CRM AI Endpoints

### **GET /api/kisiler/{id}/ilan-gecmisi**

**Açıklama:** Kişinin geçmiş ilan analizi

**Response:**

```json
{
    "success": true,
    "data": {
        "total_listings": 5,
        "avg_price": 2800000,
        "preferred_category": "Villa",
        "preferred_location": "Yalıkavak",
        "crm_score": 85,
        "insights": "Yüksek bütçeli, villa odaklı, Yalıkavak bölgesinde aktif"
    }
}
```

---

## 🎯 AI HEALTH CHECK

### **GET /stable-create/ai-health**

**Response:**

```json
{
    "success": true,
    "providers": {
        "ollama": {
            "status": "online",
            "endpoint": "http://51.75.64.121:11434",
            "model": "gemma2:2b",
            "response_time": 150
        },
        "openai": {
            "status": "online",
            "model": "gpt-4"
        },
        "gemini": {
            "status": "online",
            "model": "gemini-2.5-flash"
        }
    },
    "default_provider": "ollama"
}
```

---

## 🔐 AUTHENTICATION

### **CSRF Token:**

```javascript
// Frontend'de
fetch("/stable-create/ai-suggest", {
    method: "POST",
    headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
            .content,
        "X-Requested-With": "XMLHttpRequest",
    },
    body: JSON.stringify(data),
});
```

### **Rate Limiting:**

```
AI Endpoints: 10 requests/minute/user
429 Too Many Requests durumunda:
  → 60 saniye bekle
  → Veya cache'den yanıt al
```

---

## ⚡ RESPONSE TIME STANDARDS

```yaml
Başlık Üretimi: <2 saniye
Açıklama Üretimi: <3 saniye
Lokasyon Analizi: <2 saniye
Fiyat Önerisi: <1 saniye
Görsel Analiz: <5 saniye (Gemini)
```

---

## 🎯 ERROR CODES

```yaml
200: Success
400: Bad Request (geçersiz parametre)
401: Unauthorized (auth gerekli)
422: Validation Error (alan eksik/hatalı)
429: Too Many Requests (rate limit)
500: Server Error
502: Bad Gateway (AI servisi çalışmıyor)
503: Service Unavailable (geçici)
```

---

## 📊 CACHE HEADERS

```http
X-Cache-Status: HIT|MISS
X-Cache-Age: 3600 (saniye)
X-Response-Time: 2150 (ms)
X-Provider: ollama|openai|gemini
X-Model: gemma2:2b
```

---

## 7️⃣ AI İlan Geçmişi Analizi API (YENİ)

### **GET /api/kisiler/{id}/ai-gecmis-analiz**

**Açıklama:** Kişinin önceki ilanlarından öğrenerek yeni ilan için öneriler

**Response:**

```json
{
    "success": true,
    "has_history": true,
    "total_ilanlar": 15,
    "baslik_analizi": {
        "ortalama_uzunluk": 67,
        "kalite_skoru": 85,
        "en_basarili": {
            "baslik": "Yalıkavak Deniz Manzaralı Villa",
            "goruntulenme": 450
        }
    },
    "fiyat_trendi": {
        "ortalama_fiyat": 2800000,
        "trend": "Artış eğilimi (+12%)",
        "para_birimi_dagilimi": {
            "TRY": { "count": 12, "percentage": 80 },
            "USD": { "count": 3, "percentage": 20 }
        }
    },
    "oneriler": [
        "Başlık uzunluğunuz ideal (67 karakter)",
        "Önceki fiyat ortalamanız: 2.800.000 TRY",
        "En çok kullandığınız kategori: Villa (60%)"
    ]
}
```

---

## 8️⃣ TKGM Parsel Sorgulama API (YENİ)

### **POST /api/tkgm/parsel-sorgu**

**Açıklama:** TKGM'den parsel bilgilerini sorgula ve otomatik doldur

**Request:**

```json
{
    "ada": "126",
    "parsel": "7",
    "il": "Muğla",
    "ilce": "Bodrum"
}
```

**Response:**

```json
{
    "success": true,
    "parsel_bilgileri": {
        "ada": "126",
        "parsel": "7",
        "yuzolcumu": 1500,
        "nitelik": "Arsa",
        "imar_durumu": "İmarlı",
        "taks": 30,
        "kaks": 1.2,
        "gabari": 12.5,
        "maksimum_kat": 4
    },
    "hesaplamalar": {
        "taban_alani": 450,
        "insaat_alani": 1800,
        "maksimum_kat_sayisi": 4
    },
    "oneriler": [
        "Parsel alanı: 1500 m² (1.5 dönüm) olarak otomatik dolduruldu",
        "İnşaat alanı: 1.800 m² (KAKS: 1.2)",
        "Taban alanı: 450 m² (TAKS: 30%)",
        "Maksimum 4 kat yapı yapılabilir"
    ],
    "from_cache": false
}
```

### **POST /api/tkgm/yatirim-analizi**

**Açıklama:** Parsel için yatırım potansiyeli analizi

**Response:**

```json
{
    "success": true,
    "yatirim_analizi": {
        "yatirim_skoru": 85,
        "harf_notu": "A",
        "degerlendirme": "Mükemmel yatırım fırsatı",
        "risk_seviyesi": "Düşük",
        "tahmini_getiri": "Yıllık %15-20 değer artışı beklenir",
        "analizler": [
            "Yüksek KAKS (1.2) - Mükemmel inşaat potansiyeli",
            "Optimal TAKS (30%) - İdeal taban alanı",
            "İmarlı arsa - Yapılaşmaya hazır"
        ]
    }
}
```

---

## 9️⃣ Kategori Özel Alanlar API (YENİ)

### **GET /api/kategori/{id}/ozel-alanlar**

**Açıklama:** Kategoriye göre zorunlu/önerilen alanlar

**Response:**

```json
{
    "success": true,
    "kategori": "Arsa",
    "required": {
        "ada_no": {
            "label": "Ada No",
            "type": "text",
            "validation": "required|string|max:20",
            "help": "Tapuda yazan ada numarası"
        },
        "parsel_no": {
            "label": "Parsel No",
            "type": "text",
            "validation": "required|string|max:20"
        }
    },
    "recommended": {
        "taks": {
            "label": "TAKS (%)",
            "type": "number",
            "validation": "nullable|numeric|min:0|max:100"
        }
    },
    "ai_features": {
        "tkgm_integration": true,
        "investment_analysis": true
    }
}
```

---

**🔌 ÖZET:** Tüm API endpoint'leri Context7 uyumlu, hızlı ve güvenli. Rate limit'e dikkat et!

**🆕 Yeni Özellikler (v3.4.0):**

-   AI İlan Geçmişi Analizi
-   TKGM Parsel Sorgulama
-   Kategori Özel Alanlar
-   Yatırım Potansiyeli Analizi
