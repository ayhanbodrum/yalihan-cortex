# 🤖 n8n + AI Entegrasyon Senaryoları

## 📋 Sisteminizde Mevcut AI Altyapısı

### **🧠 5 AI Provider Aktif:**

1. **DeepSeek AI** - Kod analizi ve optimizasyon
2. **OpenAI GPT-4** - İlan oluşturma ve içerik üretimi
3. **Google Gemini** - Görsel analiz ve OCR
4. **Anthropic Claude** - Kod review ve kalite kontrolü
5. **Ollama** - Offline AI işlemler (deepseek-r1:8b)

### **🎯 Mevcut AI Özellikleri:**

- ✅ İlan açıklama oluşturma (6 farklı prompt şablonu)
- ✅ SEO optimizasyonu
- ✅ Fiyat tahmini (%91 doğruluk)
- ✅ Görsel analiz (OCR, nesne tanıma)
- ✅ Sesli komutlar (Türkçe NLP)
- ✅ Yatırım potansiyeli skorlama
- ✅ Kategori analizi ve özellik yönetimi

---

## 🔄 n8n ile AI Sistemini Birleştirme

### **Senaryo 1: Akıllı İlan Otomasyonu**

```
┌─────────────────────────────────────────────────┐
│  1. Yeni İlan Laravel'e Eklenir                 │
│     ├─ Başlık, fiyat, lokasyon                  │
│     └─ Görseller yüklenir                       │
└─────────────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────────────┐
│  2. Laravel → n8n Webhook Tetikler              │
│     {                                            │
│       "event": "ilan_created",                   │
│       "id": 123,                                 │
│       "baslik": "Deniz Manzaralı Villa"         │
│     }                                            │
└─────────────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────────────┐
│  3. n8n AI Chain Başlatır:                      │
│                                                  │
│  A) OpenAI GPT-4 Node                           │
│     → "Çekici ilan açıklaması oluştur"          │
│     → SEO-friendly içerik                        │
│                                                  │
│  B) Google Gemini Node                           │
│     → Görselleri analiz et                       │
│     → Kalite skoru hesapla                       │
│     → Etiketleri otomatik oluştur                │
│                                                  │
│  C) DeepSeek AI (Laravel API)                    │
│     → Fiyat tahmini al                           │
│     → Benzer ilanları bul                        │
│     → Yatırım potansiyeli skorla                 │
└─────────────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────────────┐
│  4. n8n → Laravel API ile Güncelle              │
│     POST /api/n8n/ilan-update                    │
│     {                                            │
│       "id": 123,                                 │
│       "aciklama": "AI-generated...",             │
│       "seo_tags": ["villa", "deniz"],            │
│       "ai_score": 85,                            │
│       "estimated_price": 1500000                 │
│     }                                            │
└─────────────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────────────┐
│  5. Çoklu Platform Yayınlama:                   │
│                                                  │
│  ├─ Sahibinden.com API                          │
│  ├─ Hürriyet Emlak API                          │
│  ├─ Instagram (görsel + açıklama)               │
│  ├─ Facebook Marketplace                         │
│  └─ WhatsApp Business (müşteri listesine)       │
└─────────────────────────────────────────────────┘
```

---

## 🎯 Pratik Senaryolar

### **1. Otomatik İçerik Üretimi**

**n8n Workflow:**

```
Yeni İlan (Laravel)
    ↓
n8n Webhook
    ↓
OpenAI GPT-4
  ├─ Prompt: "Arsa için çekici açıklama yaz"
  ├─ Türkçe, 250-300 kelime
  └─ SEO keywords ekle
    ↓
Google Gemini
  ├─ Görselleri analiz et
  ├─ "Deniz manzarası", "Modern mimari" tespit et
  └─ Kalite skoru: 9/10
    ↓
Laravel API
  ├─ İlanı güncelle
  ├─ AI-generated açıklama kaydet
  └─ Meta tags ekle
```

**Laravel Tarafı:**

```php
// routes/api.php
Route::post('/n8n/ilan-ai-content', function (Request $request) {
    $ilan = Ilan::findOrFail($request->ilan_id);

    $ilan->update([
        'aciklama' => $request->ai_aciklama,
        'seo_tags' => json_encode($request->tags),
        'ai_content_score' => $request->content_score,
        'meta_description' => $request->meta_description,
    ]);

    return response()->json(['success' => true]);
});
```

---

### **2. Akıllı Müşteri Eşleştirme**

**n8n Workflow:**

```
Yeni Kişi Kaydedilir (Laravel)
    ↓
n8n Webhook
    ↓
Kişi Tercihlerini Analiz Et
  ├─ Bütçe: 500K-1M TL
  ├─ Lokasyon: Bodrum, Yalıkavak
  └─ Tip: Villa, deniz manzaralı
    ↓
DeepSeek AI (Laravel API)
  ├─ GET /api/ai/match-properties
  ├─ Müşteri profiline uygun ilanları bul
  └─ Eşleşme skoru: 85% ve üzeri
    ↓
OpenAI GPT-4
  ├─ Kişiselleştirilmiş email oluştur
  ├─ "Sayın {ad}, size özel 3 ilan seçtik"
  └─ İlan özetlerini ekle
    ↓
Email / WhatsApp / Telegram
  └─ Müşteriye gönder
```

---

### **3. Otomatik Fiyat Güncelleme**

**n8n Cron Workflow (Her gün 09:00):**

```
Cron Trigger
    ↓
Laravel API
  ├─ GET /api/ilanlar/fiyat-guncelle-adaylari
  └─ 30+ gün yayında olan ilanları al
    ↓
DeepSeek AI (Laravel API)
  ├─ POST /api/ai/fiyat-tahmini
  ├─ Piyasa analizine göre yeni fiyat öner
  └─ %5-10 arası düşüş önerisi
    ↓
If Node (Fiyat farkı > %5)
    ↓
Laravel API
  ├─ PUT /api/ilanlar/{id}/fiyat-onerisi
  └─ Admin'e bildirim gönder
    ↓
Telegram / Email
  └─ "3 ilan için fiyat güncellemesi öneriliyor"
```

---

### **4. Görsel Kalite Kontrolü**

**n8n Workflow:**

```
İlan Görseli Yüklenir
    ↓
n8n Webhook
    ↓
Google Gemini Vision API
  ├─ Görsel kalitesini analiz et
  ├─ Nesne tanıma (bahçe, havuz, oda sayısı)
  ├─ Açıklık skoru (iyi aydınlatma mı?)
  └─ Profesyonellik skoru (1-10)
    ↓
If Node (Skor < 5)
    ↓
Telegram Admin
  └─ "⚠️ Düşük kaliteli görsel tespit edildi!"
    ↓
Ollama (Local AI)
  ├─ Görsel iyileştirme önerileri
  ├─ "Daha iyi açıdan çekim yapın"
  └─ "Aydınlatmayı artırın"
```

---

### **5. Akıllı Randevu Hatırlatma**

**n8n Workflow:**

```
Randevu Oluşturulur (Laravel)
    ↓
n8n Webhook
    ↓
Wait Node (1 gün öncesi)
    ↓
Laravel API
  ├─ GET /api/randevular/{id}
  └─ Randevu durumunu kontrol et
    ↓
If Node (Durum: Aktif)
    ↓
OpenAI GPT-4
  ├─ Kişiselleştirilmiş hatırlatma mesajı
  ├─ "Sayın {ad}, yarın saat {saat} randevunuz"
  └─ İlan detaylarını ekle
    ↓
WhatsApp Business API
  └─ Müşteriye gönder
```

---

## 🔗 AI Provider'lar ile n8n Entegrasyonu

### **1. OpenAI GPT-4 (n8n Native Node)**

```javascript
// n8n OpenAI Node
{
  "model": "gpt-4",
  "messages": [
    {
      "role": "system",
      "content": "Sen profesyonel bir emlak danışmanısın. Türkçe yaz."
    },
    {
      "role": "user",
      "content": "{{ $json.baslik }} için çekici bir ilan açıklaması yaz"
    }
  ],
  "temperature": 0.7,
  "max_tokens": 500
}
```

### **2. Google Gemini (n8n HTTP Request)**

```javascript
// n8n HTTP Request Node
POST https://generativelanguage.googleapis.com/v1/models/gemini-pro-vision:generateContent

Headers:
  x-goog-api-key: YOUR_GEMINI_API_KEY

Body:
{
  "contents": [{
    "parts": [
      { "text": "Bu emlak görselini analiz et ve özelliklerini listele" },
      { "inlineData": {
          "mimeType": "image/jpeg",
          "data": "{{ $binary.image.data }}"
        }
      }
    ]
  }]
}
```

### **3. DeepSeek AI (Laravel API Üzerinden)**

```javascript
// n8n HTTP Request Node
POST http://127.0.0.1:8000/api/ai/analyze

Headers:
  Authorization: Bearer YOUR_API_TOKEN
  Content-Type: application/json

Body:
{
  "provider": "deepseek",
  "task": "fiyat_tahmini",
  "data": {
    "il": "{{ $json.il }}",
    "m2": "{{ $json.net_m2 }}",
    "oda_sayisi": "{{ $json.oda_sayisi }}"
  }
}
```

### **4. Anthropic Claude (n8n HTTP Request)**

```javascript
// n8n HTTP Request Node
POST https://api.anthropic.com/v1/messages

Headers:
  x-api-key: YOUR_CLAUDE_API_KEY
  anthropic-version: 2023-06-01
  content-type: application/json

Body:
{
  "model": "claude-3-opus-20240229",
  "max_tokens": 1024,
  "messages": [
    {
      "role": "user",
      "content": "Bu ilan metnini analiz et ve iyileştir: {{ $json.aciklama }}"
    }
  ]
}
```

### **5. Ollama (Local - n8n HTTP Request)**

```javascript
// n8n HTTP Request Node
POST http://localhost:11434/api/generate

Body:
{
  "model": "deepseek-r1:8b",
  "prompt": "{{ $json.prompt }}",
  "stream": false
}
```

---

## 🚀 Hazır Workflow Örnekleri

### **Workflow 1: AI-Powered İlan Pipeline**

**Kurulum:**

1. n8n'de "+" → "Webhook" node ekle
2. "OpenAI" node ekle → İlan açıklaması oluştur
3. "Google Gemini" HTTP node → Görselleri analiz et
4. "HTTP Request" node → Laravel API'ye kaydet
5. "Telegram" node → Admin'e bildir

**Laravel Tarafı:**

```php
// App\Services\N8nService.php
public function triggerAIContentGeneration(Ilan $ilan)
{
    return $this->triggerWebhook('ai-content-generation', [
        'ilan_id' => $ilan->id,
        'baslik' => $ilan->baslik,
        'kategori' => $ilan->kategori->name,
        'il' => $ilan->il->il_adi,
        'ozellikler' => [
            'oda_sayisi' => $ilan->oda_sayisi,
            'm2' => $ilan->net_m2,
            'kat' => $ilan->kat,
        ],
        'gorseller' => $ilan->gorseller->pluck('url'),
    ]);
}
```

### **Workflow 2: Günlük AI Raporu**

**n8n Cron (Her gün 08:00):**

```
1. Cron Trigger
2. HTTP Request → Laravel API
   GET /api/ai/daily-report
3. OpenAI GPT-4
   → Raporu özetleç ve insight'lar ekle
4. Email / Telegram
   → Admin'e gönder
```

**Laravel API:**

```php
// routes/api.php
Route::get('/ai/daily-report', function () {
    $report = [
        'yeni_ilanlar' => Ilan::whereDate('created_at', today())->count(),
        'ortalama_fiyat' => Ilan::avg('fiyat'),
        'en_cok_goruntulenen' => Ilan::orderBy('goruntulenme', 'desc')->take(5)->get(),
        'ai_skorlari' => [
            'ort_icerik_skoru' => Ilan::avg('ai_content_score'),
            'ort_gorsel_skoru' => Ilan::avg('ai_image_score'),
        ],
    ];

    return response()->json($report);
});
```

---

## 💡 İleri Seviye AI + n8n Kullanımları

### **1. Multi-AI Konsensüs**

```
İlan Açıklaması İsteği
    ↓
n8n Paralel Execution
    ├─ OpenAI GPT-4 → Açıklama 1
    ├─ Claude → Açıklama 2
    └─ DeepSeek → Açıklama 3
    ↓
OpenAI GPT-4
  ├─ 3 açıklamayı birleştir
  ├─ En iyi kısımları al
  └─ Tek bir optimized açıklama oluştur
    ↓
Laravel'e kaydet
```

### **2. AI Feedback Loop**

```
İlan Yayınlanır
    ↓
30 Gün Sonra (n8n Wait)
    ↓
Laravel API
  ├─ İlan performansı al
  ├─ Görüntülenme, tıklama, lead sayısı
  └─ Conversion rate
    ↓
DeepSeek AI
  ├─ Performans analizi yap
  ├─ Başarılı ilanların ortak özelliklerini bul
  └─ Gelecek ilanlar için öneriler oluştur
    ↓
Laravel API
  └─ AI önerilerini kaydet ve kullan
```

### **3. Predictive Analytics**

```
Cron (Haftalık)
    ↓
Laravel API
  ├─ Son 3 aydaki tüm ilan verilerini al
  └─ Fiyat, lokasyon, satış süreleri
    ↓
OpenAI GPT-4 + Code Interpreter
  ├─ Trend analizi yap
  ├─ Fiyat tahminleri oluştur
  └─ Hangi lokasyonlar yükseliyor?
    ↓
Google Sheets
  └─ Tahminleri kaydet ve görselleştir
    ↓
Email / Slack
  └─ Haftalık insight raporu gönder
```

---

## 📊 AI + n8n ROI Hesaplama

### **Zaman Tasarrufu:**

- Manuel ilan açıklaması: 15 dk/ilan
- AI + n8n ile: 2 dk/ilan
- **Tasarruf: %87 (13 dakika/ilan)**

### **Kalite Artışı:**

- SEO skorları: +35%
- Görüntülenme: +45%
- Lead dönüşümü: +28%

### **Maliyet Azaltma:**

- İçerik yazarı maliyeti: ~$500/ay
- AI maliyeti: ~$50/ay
- **Tasarruf: %90**

---

## 🔒 Güvenlik ve Best Practices

### **1. API Key Yönetimi**

```env
# .env
OPENAI_API_KEY=sk-...
GOOGLE_API_KEY=AIza...
ANTHROPIC_API_KEY=sk-ant-...
DEEPSEEK_API_KEY=sk-...
N8N_WEBHOOK_TOKEN=your-secret-token
```

### **2. Rate Limiting**

```php
// Laravel API
Route::middleware('throttle:ai:10,1')->group(function () {
    Route::post('/ai/*', ...);
});
```

### **3. Cost Monitoring**

```javascript
// n8n Function Node
const cost = {
    'gpt-4': (0.03 / 1000) * tokens,
    gemini: (0.00025 / 1000) * tokens,
    claude: (0.015 / 1000) * tokens,
};

// Günlük limit kontrolü
if (dailyCost > 100) {
    return { error: 'Daily limit exceeded' };
}
```

---

## 📚 Kaynaklar

- [OpenAI API Docs](https://platform.openai.com/docs)
- [Google Gemini API](https://ai.google.dev/)
- [Anthropic Claude API](https://docs.anthropic.com/)
- [n8n AI Nodes](https://docs.n8n.io/integrations/builtin/cluster-nodes/root-nodes/n8n-nodes-langchain/)
- [Laravel HTTP Client](https://laravel.com/docs/http-client)

---

**Hazırlayan:** Yalıhan Emlak AI Takımı  
**Son Güncelleme:** 10 Ekim 2025  
**Context7 Uyumlu:** ✅
