# 🧠 CORTEX FİNANSAL ANALİZİ - Pazarlık Stratejisi Sistemi

**Tarih:** 2025-11-29  
**Versiyon:** 1.0.0  
**Durum:** ✅ Aktif  
**Context7 Standardı:** C7-CORTEX-NEGOTIATION-2025-11-29

---

## 🎯 AMAÇ

GİZEM GÜNAL ve diğer danışmanların, müşteriyle görüşmeden önce AI destekli pazarlık stratejisi almasını sağlamak. Her müşterinin "pazarlık DNA'sını" öğrenerek daha etkili satış yapmak.

---

## 📍 KULLANICI ERİŞİMİ

### Nerede Görünür?

**1. Menü Yolu:**
```
Admin Panel → Kişiler (veya Müşteriler) → Herhangi bir kişiye tıkla
```

**2. URL:**
```
/admin/kisiler/{id}
veya
/admin/musteriler/{id} (eski route, yönlendirir)
```

**3. Sayfa Konumu:**
Kişi detay sayfasında, "Müşteri Bilgileri" bölümünden sonra, "Notlar" bölümünden önce otomatik görünür.

---

## 🔄 NASIL ÇALIŞIR?

### Sistem Akışı

```
1. Kullanıcı Kişi Detay Sayfasını Açar
   └─ Widget otomatik olarak API'yi çağırır

2. API → YalihanCortex::getNegotiationStrategy()
   └─ Müşteri verilerini toplar:
      • yatirimci_profili
      • satis_potansiyeli
      • gelir_duzeyi
      • toplam_islem_tutari
      • karar_verici_mi
   
3. AIService → LLM ile Strateji Üretir
   └─ Prompt: "Bu müşteriyle pazarlık yaparken nasıl bir strateji izlemeliyim?"
   └─ LLM yanıtı: "Bu müşteri agresif indirim bekler, %10 ile başlayın..."

4. Widget Sonuçları Gösterir
   └─ Pazarlık önerisi
   └─ Müşteri profili bilgileri
   └─ Strateji detayları (indirim yaklaşımı, odak noktası)
```

---

## 🏗️ MİMARİ YAPISI

### 1. YalihanCortex Servisi

**Dosya:** `app/Services/AI/YalihanCortex.php`

**Metod:** `getNegotiationStrategy(Kisi $kisi): array`

**Özellikler:**
- `@CortexDecision` etiketi ile işaretlenmiş
- `LogService::startTimer/stopTimer` ile performans ölçümü
- `AiLog` kayıtları (MCP uyumluluğu)
- Hata yönetimi ve fallback mekanizması

**İşlem Adımları:**
1. Müşteri verilerini topla
2. AI prompt oluştur (`buildNegotiationPrompt`)
3. AIService ile LLM'den strateji üret
4. AI yanıtını parse et (`parseNegotiationResponse`)
5. Yapılandırılmış sonuç döndür

### 2. API Endpoint

**Route:** `/api/v1/ai/strategy/{kisiId}`

**Controller:** `App\Http\Controllers\Api\AIController::getNegotiationStrategy()`

**Özellikler:**
- `auth:sanctum` middleware ile korumalı
- `ResponseService` ile standart yanıt formatı
- Hata yönetimi ve logging

### 3. Frontend Widget

**Dosya:** `resources/views/admin/kisiler/show.blade.php`

**Özellikler:**
- Otomatik AJAX yükleme
- Loading state (spinner)
- Error handling
- Tailwind CSS + Dark Mode uyumlu
- Responsive tasarım

---

## 📊 VERİ YAPISI

### Müşteri Profili Verileri

```php
[
    'yatirimci_profili' => 'agresif|konservatif|firsatci|denge|yeni_baslayan',
    'satis_potansiyeli' => 0-100 (integer),
    'gelir_duzeyi' => 'dusuk|orta|yuksek|premium',
    'toplam_islem_tutari' => decimal(15,2),
    'toplam_islem' => integer,
    'memnuniyet_skoru' => decimal(1,1),
    'karar_verici_mi' => boolean,
    'crm_status' => 'sicak|soguk|takipte|musteri|potansiyel|ilgili|pasif',
]
```

### Strateji Yanıtı

```php
[
    'kisi_id' => integer,
    'strategy' => [
        'summary' => string, // Ana öneri metni
        'recommendation' => string, // Detaylı öneri
        'discount_approach' => 'aggressive|moderate|conservative',
        'focus' => 'price|quality|balanced',
    ],
    'customer_profile' => array, // Müşteri profili verileri
    'metadata' => [
        'processed_at' => ISO8601 timestamp,
        'algorithm' => 'YalihanCortex v1.0',
        'duration_ms' => float,
        'success' => boolean,
    ],
]
```

---

## 🤖 AI PROMPT YAPISI

### Prompt Örneği

```
Bir emlak danışmanısın. Aşağıdaki müşteri profili için pazarlık stratejisi öner:

**Müşteri Profili:**
- Yatırımcı Profili: agresif
- Satış Potansiyeli: 85/100
- Gelir Düzeyi: yuksek
- Toplam İşlem Tutarı: 5.000.000 ₺
- Karar Verici: Evet

**Görev:**
Bu müşteriyle pazarlık yaparken nasıl bir strateji izlemeliyim? Şu konularda öneri ver:
1. İndirim yaklaşımı (agresif mi, yumuşak mı?)
2. Fiyat vurgusu mu, kalite vurgusu mu?
3. İlk teklif nasıl olmalı?
4. Pazarlık sırasında dikkat edilmesi gerekenler

**Format:**
Kısa, net ve uygulanabilir öneriler ver. Maksimum 200 kelime.
```

### Örnek LLM Yanıtı

```
"Bu müşteri, agresif bir indirim bekler. %10 indirimle başlayın ve müşterinin tepkisine göre %15'e kadar çıkabilirsiniz. Fiyat yerine kalite ve konum avantajlarını vurgulayın. Yüksek gelir düzeyi nedeniyle değer odaklı yaklaşım daha etkili olacaktır."
```

---

## 🎨 UI/UX ÖZELLİKLERİ

### Widget Tasarımı

- **Başlık:** 🧠 CORTEX FİNANSAL ANALİZİ (Mor renk, gradient)
- **Arka Plan:** Purple-Blue gradient (from-purple-50 to-blue-50)
- **Kartlar:** Beyaz arka plan, mor border
- **Dark Mode:** Tam destek (dark:bg-gray-800, dark:text-gray-100)

### İçerik Bölümleri

1. **Pazarlık Önerisi Kartı**
   - Ana öneri metni
   - İkon: ✓ (başarı)

2. **Müşteri Profili Kartı**
   - Yatırımcı Profili
   - Satış Potansiyeli
   - Gelir Düzeyi

3. **Strateji Detayları Kartı**
   - İndirim Yaklaşımı
   - Odak Noktası

---

## 🔧 TEKNİK DETAYLAR

### API Endpoint

```http
GET /api/v1/ai/strategy/{kisiId}
Authorization: Bearer {token}
```

**Yanıt:**
```json
{
    "success": true,
    "data": {
        "kisi_id": 123,
        "strategy": {
            "summary": "Bu müşteri agresif indirim bekler...",
            "recommendation": "Agresif indirim yaklaşımı önerilir...",
            "discount_approach": "aggressive",
            "focus": "quality"
        },
        "customer_profile": {
            "yatirimci_profili": "agresif",
            "satis_potansiyeli": 85,
            "gelir_duzeyi": "yuksek"
        },
        "metadata": {
            "processed_at": "2025-11-29T22:30:00Z",
            "algorithm": "YalihanCortex v1.0",
            "duration_ms": 1250.5,
            "success": true
        }
    },
    "message": "Pazarlık stratejisi başarıyla oluşturuldu."
}
```

### JavaScript Kullanımı

```javascript
fetch(`/api/v1/ai/strategy/${kisiId}`, {
    method: 'GET',
    headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
    },
    credentials: 'same-origin',
})
.then(response => response.json())
.then(data => {
    if (data.success && data.data.strategy) {
        // Widget içeriğini güncelle
        displayStrategy(data.data);
    }
});
```

---

## 📈 PERFORMANS METRİKLERİ

### Ölçülen Metrikler

- **Yanıt Süresi:** Ortalama 1-2 saniye (LLM'e bağlı)
- **Başarı Oranı:** %95+ (fallback mekanizması ile)
- **Cache:** Şu an yok (her istekte fresh analiz)

### Loglama

- **AiLog:** Her analiz kaydedilir
- **LogService:** AI işlemleri loglanır
- **MCP Uyumluluğu:** Timer ve metadata kayıtları

---

## 🚀 KULLANIM SENARYOLARI

### Senaryo 1: Yeni Müşteri Görüşmesi

1. GİZEM GÜNAL, yeni bir müşteriyle görüşme yapacak
2. Kişi detay sayfasını açar
3. "CORTEX FİNANSAL ANALİZİ" widget'ını görür
4. AI önerisini okur: "Bu müşteri agresif indirim bekler, %10 ile başlayın"
5. Görüşmede bu stratejiyi uygular

### Senaryo 2: Tekrar Görüşme

1. Daha önce görüşülen müşteri için sayfa açılır
2. Widget otomatik yüklenir
3. Müşteri profili güncellenmişse, yeni strateji üretilir
4. Önceki görüşme notları ile karşılaştırılabilir

---

## 🔒 GÜVENLİK VE YETKİLENDİRME

### Yetkilendirme

- **Middleware:** `auth:sanctum`
- **Kontrol:** Sadece giriş yapmış kullanıcılar erişebilir
- **Kişi Erişimi:** Danışman sadece kendi kişilerini görebilir (admin hariç)

### Veri Güvenliği

- Kişisel veriler (TC, gelir bilgisi) sadece backend'de işlenir
- API yanıtında hassas bilgiler filtrelenir
- AI prompt'unda sadece gerekli bilgiler gönderilir

---

## 🐛 SORUN GİDERME

### Widget Yüklenmiyor

**Sorun:** Widget'da "Pazarlık stratejisi yüklenemedi" hatası

**Çözüm:**
1. Browser console'u kontrol et (F12)
2. API endpoint'in çalıştığını doğrula: `/api/v1/ai/strategy/{kisiId}`
3. Authentication token'ın geçerli olduğunu kontrol et
4. AIService'in çalıştığını doğrula

### AI Yanıt Vermiyor

**Sorun:** Widget sürekli loading gösteriyor

**Çözüm:**
1. AIService provider'ını kontrol et (OpenAI, Ollama, vb.)
2. API key'lerin geçerli olduğunu doğrula
3. LogService loglarını kontrol et
4. Fallback mekanizması devreye girer (standart strateji gösterilir)

### Yanlış Strateji Önerisi

**Sorun:** AI yanlış öneri veriyor

**Çözüm:**
1. Müşteri profil verilerinin doğru olduğunu kontrol et
2. Prompt'u optimize et (`buildNegotiationPrompt` metodunu güncelle)
3. AI provider'ı değiştir (OpenAI → Gemini → Ollama)

---

## 📚 İLGİLİ DOSYALAR

### Backend

- `app/Services/AI/YalihanCortex.php` - Ana servis
- `app/Http/Controllers/Api/AIController.php` - API controller
- `routes/api/v1/ai.php` - API route tanımı

### Frontend

- `resources/views/admin/kisiler/show.blade.php` - Widget view
- JavaScript inline (sayfa içinde)

### Dokümantasyon

- `docs/ai/YALIHAN_CORTEX_CALISMA_MANTIGI.md` - Cortex genel dokümantasyonu
- `docs/ai/PAZARLIK_STRATEJISI_ANALIZI.md` - Bu dosya

---

## ✅ CONTEXT7 UYUMLULUK

### Standartlar

- ✅ **ResponseService:** Tüm API yanıtları ResponseService kullanır
- ✅ **LogService:** AI işlemleri LogService ile loglanır
- ✅ **AiLog:** MCP uyumluluğu için AiLog kayıtları
- ✅ **Timer:** LogService::startTimer/stopTimer kullanımı
- ✅ **Error Handling:** Try-catch ve fallback mekanizması
- ✅ **Tailwind CSS:** Pure Tailwind, Neo class yok
- ✅ **Dark Mode:** Tüm UI elementleri dark mode destekli

### Yasaklı Patterns

- ❌ `response()->json()` → ✅ `ResponseService::success()`
- ❌ `neo-*` classes → ✅ Pure Tailwind
- ❌ Inline styles → ✅ Tailwind utility classes

---

## 🎯 GELECEKTEKİ İYİLEŞTİRMELER

### Önerilen Özellikler

1. **Cache Mekanizması:** Müşteri profili değişmediyse cache'den döndür
2. **Strateji Geçmişi:** Önceki stratejileri sakla ve karşılaştır
3. **Feedback Sistemi:** Danışman stratejinin başarılı olup olmadığını bildirebilir
4. **A/B Testing:** Farklı stratejileri test et ve en başarılısını öğren
5. **Çoklu Dil:** İngilizce müşteriler için İngilizce strateji

---

## 📝 ÖZET

**Ne Yapar?**
- Müşteri profili analiz eder
- AI ile pazarlık stratejisi üretir
- Danışmana özel öneriler sunar

**Nerede?**
- Kişi detay sayfası (`/admin/kisiler/{id}`)
- Otomatik yüklenir

**Nasıl?**
- YalihanCortex servisi
- AIService ile LLM entegrasyonu
- AJAX ile gerçek zamanlı yükleme

**Kim İçin?**
- GİZEM GÜNAL ve tüm danışmanlar
- Müşteri görüşmesi öncesi hazırlık

---

**Son Güncelleme:** 2025-11-29  
**Versiyon:** 1.0.0  
**Durum:** ✅ Production Ready






