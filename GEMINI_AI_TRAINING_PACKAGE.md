# 🧠 Yalihan Cortex → Gemini AI Eğitim Paketi

> **Context7 Hafızası**: Proje temel yapısını ve AI zekasını öğreten **5 KRITIK DOSYA**

---

## 📋 PAKET İÇERİĞİ

### **1. MERKEZI AI ORKESTRASYONU** 
**Dosya:** `app/Services/AI/YalihanCortex.php` (1988 satır)

**Nedir?** Sistemin "Beyni". Tüm AI görevlerini merkezi olarak koordine eder.

**Ana Metodlar:**
- `matchForSale(Talep)` - Müşteri talebi için emlak eşleştirmesi + Churn risk analizi
- `priceValuation(Ilan)` - İlan değerleme (TKGM tapu + finansal analiz)
- `checkIlanQuality(Ilan)` - İlan yayınlama öncesi kalite kontrolü (%80 tamamlanma)
- `getChurnRisk(Kisi)` - Müşteri kaybolma riski tahmini
- `generateAIContent(Ilan)` - İlan başlığı/açıklaması otomatik oluşturma
- `logCortexDecision()` - AI karar loglaması (audit trail)

**Alt Servisler (Dependency Injection):**
```
YalihanCortex
├── SmartPropertyMatcherAI (Eşleştirme algoritması)
├── KisiChurnService (Müşteri kaybı riski)
├── FinansService (Finansal analiz, komisyon hesaplaması)
├── TKGMService (Tapu Kadastro sorgulama)
└── AIService (LLM entegrasyonu: OpenAI/DeepSeek/Gemini/Claude)
```

**Öğrenmen Gereken:**
- Tüm AI isteklerinin arkasında neler yatıyor
- Risk skorları nasıl hesaplanıyor
- Fallback provider sistemi (bir provider başarısız olursa diğerine geç)
- MCP uyumluluğu (LogService timer'lar, AiLog kayıtları)

---

### **2. MÜŞTERI DNA MODELİ**
**Dosya:** `app/Models/Kisi.php` (815 satır)

**Nedir?** Müşterinin elektronik kimliği. Tüm AI skorları bu model üzerine inşa edilir.

**Kritik Alanlar:**
```php
// CRM Temel Bilgileri
$kisi->ad, $kisi->soyad, $kisi->telefon, $kisi->email

// İlişkisel Adres Sistemi (Context7 Standard)
$kisi->il_id → Il model → Il.ad (e.g., "İstanbul")
$kisi->ilce_id → Ilce model
$kisi->mahalle_id → Mahalle model
// ⚠️ Dikkat: "sehir_id" DEĞIL, "il_id" kullanılır

// AI Scoring Fields (YalihanCortex tarafından set edilir)
$kisi->satis_potansiyeli    // 0-100 (satış yapma ihtimali)
$kisi->yatirimci_profili    // Enum: CONSERVATIVE, MODERATE, AGGRESSIVE
$kisi->aciliyet_derecesi    // 1-10 (ne kadar acele)
$kisi->karar_verici_mi      // true/false (kendi kararı verecek mi?)

// CRM Pipeline (Satış aşamaları)
$kisi->segment              // 'potansiyel' | 'aktif' | 'eski' | 'vip'
$kisi->skor                 // Lead scoring 0-100
$kisi->pipeline_stage       // 1-5 satış aşaması (0 = kaybedilen)
$kisi->son_etkilesim        // Last contact timestamp

// Finansal Profil
$kisi->gelir_duzeyi         // 'dusuk' | 'orta' | 'yuksek'
$kisi->medeni_status        // 'evli' | 'bekâr' | 'diger'
$kisi->memnuniyet_skoru     // 0-10 (referans olası mı?)

// İlişkiler (Relationships)
$kisi->talepler             // hasMany Talep
$kisi->ilanlarAsSahibi      // hasMany Ilan (Property Owner)
$kisi->ilanlarAsIlgili      // hasMany Ilan (Co-owner/Agent)
$kisi->danisman             // belongsTo User (danışman_id)
```

**Öğrenmen Gereken:**
- Kişi nasıl segmente bölünüyor (potansiyel vs VIP vs eski)
- AI skorları ne zaman ve kim tarafından güncelleniyor
- Enum casting sistemi (String → PHP Enum)
- İlişkiler nasıl optimize ediliyor (eager loading)

---

### **3. FINANS & KOMİSYON ÖR'CÜ**
**Dosya:** `app/Modules/CRMSatis/Models/Satis.php` (273 satır)

**Nedir?** Satış işleminin finansal merkezi. Komisyon hesaplaması ve split payment sistemi.

**Kritik Alanlar:**
```php
// Satış Temel Bilgileri
$satis->ilan_id              // İlan
$satis->musteri_id           // Müşteri (Kişi)
$satis->satis_tipi           // 'satis' | 'kiralama' | 'danismanlik'
$satis->satis_tarihi         // Satış tarihi

// Split Commission System (Yeni: 2025-11-25)
// ⚠️ Artık hem "Satıcı danışman" hem "Alıcı danışman" olabilir!
$satis->satici_danisman_id   // Property Owner's Agent
$satis->alici_danisman_id    // Buyer's Agent

// Split Commission Amounts
$satis->satici_komisyon_orani    // Satıcı danışmanının komisyon % (e.g., 2%)
$satis->alici_komisyon_orani     // Alıcı danışmanının komisyon % (e.g., 1.5%)
$satis->satici_komisyon_tutari   // Satıcı danışman kazancı (TRY)
$satis->alici_komisyon_tutari    // Alıcı danışman kazancı (TRY)

// Ödeme Durumu
$satis->status                   // 'baslangic' | 'sozlesme' | 'odeme' | 'teslim' | 'tamamlandi' | 'iptal'
$satis->odeme_durumu            // 'bekliyor' | 'kismi' | 'tamamlandi'
$satis->odenen_tutar            // Şu ana kadar ödenen
$satis->kalan_tutar             // Kalan ödeme

// Referans Sistemi
$satis->referans_no             // Client's reference
$satis->sozlesme_no             // Contract number
$satis->fatura_no               // Invoice number
```

**Relationships:**
```php
$satis->ilan()              // İlan detayları
$satis->musteri()           // Müşteri (Kişi) bilgileri
$satis->saticiDanisman()    // Property owner's agent (User)
$satis->aliciDanisman()     // Buyer's agent (User)
$satis->raporlar()          // Satış raporları (audit trail)
```

**Öğrenmen Gereken:**
- Split commission nasıl çalışıyor (iki danışman = iki komisyon)
- Ödeme aşamaları (bekliyor → kısmi → tamamlandı)
- Finansal raporlama (YalihanCortex tarafından entegre)
- CRM satış pipeline ile bağlantı

---

### **4. FORM VE VERİ GIRIŞ ÇEKIRDEĞI**
**Dosya:** `resources/views/admin/ilanlar/create-wizard.blade.php` (wizard ana sayfa)

**Nedir?** Danışmanın emlak ilanı oluşturduğu arayüz. 10 adımlı, AI-assisted form.

**Yapısı:**
```
STEP 1: Kategori Seçimi
├── Ana kategori (Arsa, Yazlık, Daire, vb.)
└── Yayın tipi (Satış, Kiralama, Takas)

STEP 2: Temel Bilgiler
├── Başlık (otomatik AI önerileri)
├── Açıklama (AI asistanı penceresi)
├── Fiyat & Para Birimi
└── İletişim Bilgileri

STEP 3: Adres Bilgileri
├── İl → İlçe → Mahalle (cascading selects)
├── Mahalle yazılı arama (Nominatim/Elastic)
├── Harita seçimi (click to place pin)
└── Koordinat (latitude, longitude)

STEP 4-7: Kategori Spesifik Özellikler
├── Arsa: Ada-Parsel No, Imar Statusu, KAKS/TAKS, Altyapı
├── Yazlık: Oda-Salon, Yapı Yaşı, Yüzme Havuzu
├── Daire: Asansör, Isıtma, Balkon
└── (Dinamik: anaKategori.slug ile kontrol edilir)

STEP 8: Fotograf & Medya
├── Resim yükleme (Lychee API ile entegre)
├── Başlık resmi seçimi
└── Sıra değiştirme (drag-drop)

STEP 9: Özellikler Seçimi
├── 100+ Özellik (Feature model'den dinamik)
├── Kategoriye göre filtreleme
└── Çoklu seçim

STEP 10: İnceleme & Yayın
├── Özet görüntüleme
├── AI Kalite Kontrolü İncelemesi
├── Uyarılar (eksik alanlar)
└── "Yayınla" Butonu
```

**Önemli Alpine.js Bileşenleri:**
```javascript
x-data="createListing()"
├── currentStep: 1-10 (adım kontrolü)
├── form: {} (tüm form verisi)
├── ilanKategorileri: [] (kategori listesi)
├── ilceler: {} (il → ilçe mapping, cached)
├── mahalleler: {} (ilçe → mahalle mapping)
├── ozellikler: [] (feature listesi)
├── resimler: [] (uploaded photos)
├── aiHazirla: () → AI suggestion widget
├── handleGeocode(): Adres → Koordinat
├── validateStep(): Adım validasyonu
├── submitForm(): POST /admin/ilanlar → IlanController@store
└── showQualityWarning(): Kalite kontrolü sonrası uyarı
```

**AI Widget Pozisyonu:**
- **STEP 2'de** Sağ panel: "🤖 AI Asistanı" (Başlık/açıklama önerileri)
- **STEP 10'da** Uyarı: "⚠️ Kalite Kontrol Sonucu" (missing fields list)

**Öğrenmen Gereken:**
- Form state management (10 adımlı flow)
- Cascading dropdown (İl → İlçe → Mahalle)
- Harita entegrasyonu (Leaflet)
- AI widget'ı nereye yerleştirildi
- Resim yükleme workflow (Lychee)
- Kategori dinamik özellikleri

---

### **5. DIŞA AÇILAN KAPILARI & GÜVENLİK**
**Dosya:** `routes/api/v1/common.php` (261 satır)

**Nedir?** Sistemin n8n otomasyonu ve dış entegrasyon noktaları.

**n8n Webhook Rotaları (Koruma: X-N8N-SECRET + Rate Limit):**
```php
Route::prefix('webhook/n8n')
    ->middleware(['throttle:60,1', 'n8n.secret'])
    ->group(function () {
        
    // Test Endpoint
    POST /api/v1/webhook/n8n/test
    → N8nWebhookController@test()
    
    // AI Content Generation
    POST /api/v1/webhook/n8n/ai/ilan-taslagi
    → N8nWebhookController@ilanTaslagi()
    (Input: kisi_id, kategori | Output: başlık, açıklama, keywords)
    
    POST /api/v1/webhook/n8n/ai/mesaj-taslagi
    → N8nWebhookController@mesajTaslagi()
    (Müşteri iletişim şablonları)
    
    POST /api/v1/webhook/n8n/ai/sozlesme-taslagi
    → N8nWebhookController@sozlesmeTaslagi()
    (Sözleşme şablonları)
    
    // Market Analysis & Listing Management
    POST /api/v1/webhook/n8n/analyze-market ⭐ EN ÖNEMLİ
    → N8nWebhookController@analyzeMarket()
    (Input: il_id, ilce_id, kategori | Output: piyasa istatistikleri)
    
    POST /api/v1/webhook/n8n/create-draft-listing
    → N8nWebhookController@createDraftListing()
    (Otomatik taslak ilan oluşturma)
    
    POST /api/v1/webhook/n8n/trigger-reverse-match
    → N8nWebhookController@triggerReverseMatch()
    (Ters eşleştirme: yeni ilanı tüm talepler'e karşı eşle)
});
```

**Middleware Koruma Sistemi:**
```
1. throttle:60,1
   └─ Dakikada 60 istek sınırı (n8n'i ratelimit'ten koru)

2. n8n.secret (Custom Middleware: VerifyCsrfToken.php)
   └─ Header'da "X-N8N-SECRET" kontrolü
   └─ .env'de tanımlı: N8N_WEBHOOK_SECRET
   └─ Format: "X-N8N-SECRET: {N8N_WEBHOOK_SECRET}"
```

**Context7 Status API:**
```php
GET /api/v1/context7/status
→ Context7Controller@status()
(Sistem sağlığı ve performans metrikleri)

GET /api/v1/context7/memory/performance
→ İlanPrivateAudit tablosu sorgusu
(24 saat ve aylık değişim sayıları)
```

**Diğer Ortak API'ler (Standart):**
```
GET /api/v1/categories/sub/{parentId}      # Kategori ağacı
GET /api/v1/features/category/{categoryId} # Özellikler listesi
POST /api/v1/webhook/telegram              # Telegram Bot webhooks
GET /api/v1/exchange-rates/                # Döviz kurları (TCMB)
GET /api/v1/kisiler/search                 # Kişi arama
GET /api/v1/ilanlar/search                 # İlan arama
```

**Öğrenmen Gereken:**
- n8n webhook'ları hangi trigger'larla çalışıyor
- Güvenlik: Secret header doğrulaması
- Rate limiting stratejisi
- Dış sistem ile JSON payload formatı
- Context7 health status nasıl raporlanıyor

---

## 🎯 GEMİNİ AI İÇİN KULLANMA ÖRNEĞİ

### **Senaryo:** "Yeni ilanı otomatik eşle ve müşterilere sun"

```markdown
N8N WORKFLOW:
1. "Webhook: yeni-ilan-yayinlandi" trigger
2. POST /api/v1/webhook/n8n/trigger-reverse-match
   {
     "ilan_id": 12345,
     "ilan_baslik": "Bakırköy'de Yeni Arsa"
   }
3. YalihanCortex::matchForSale() çağırılır
   ├─ Tüm aktif Talep'leri tara
   ├─ Churn risk + match skoru hesapla
   ├─ En uygun 5 müşteriye sun
   └─ AiLog'a kayıt yap
4. Danışman panelinde göster
5. İlgili müşteriye WhatsApp gönder (Telegram Bot)
```

### **Senaryo:** "Müşteri profili AI tarafından otomatik analiz"

```markdown
GEMINI'NIN İŞİ:
1. Kisi.php modelini oku
   - Müşterinin tüm satış geçmişini 
   - Satis.php ile bağla
   - Finansal profili (gelir_duzeyi, memnuniyet_skoru) oku
2. YalihanCortex::getChurnRisk() logic'ini anla
   - Neden bu müşteri satış yapacak?
   - Neden bu müşteri kaybediyoruz?
3. Kişiye best match ilanları öner (SmartPropertyMatcherAI)
```

---

## 📚 DOSYA DETAYLARI & İNDEKS

| Dosya | Satır | Ana Sınıf/Endpoint | Context7 Standart |
|-------|-------|------|---|
| YalihanCortex.php | 1988 | Service | C7-YALIHAN-CORTEX-2025-11-26 |
| Kisi.php | 815 | Model | C7-CRM-STANDARD-2025-11-25 |
| Satis.php | 273 | Model | C7-SPLIT-COMMISSION-2025-11-25 |
| create.blade.php | 4082 | View (10-step form) | C7-FORM-STANDARD-2025-11-25 |
| common.php (routes) | 261 | Routes | C7-N8N-WEBHOOK-2025-11-20 |

---

## 🔑 KILIT KAVRAMLAR

### **1. YalihanCortex Orkestrasyonu**
- Tüm AI işlemleri merkezi `YalihanCortex` servisi'nde
- Her AI metodu: **başlat timer** → **yapı yap** → **log'a kaydet** → **dön sonuç**
- Fallback providers (bir AI provider başarısız olursa diğeri çalış)

### **2. Context7 Standartı**
- **Alan adları:** "sehir_id" ❌ → "il_id" ✅
- **Adres sistemi:** Global (Ulke → Il → Ilce → Mahalle)
- **Status kuralı:** Tüm tablolarda "status" kolonu mevcut ve tutarlı
- **Accessor'lar:** `with()` ile yüklenmez, direkt erişim ile çalışır

### **3. Split Commission (Yeni)**
- Artık bir satışta 2 danışman olabilir
- `satici_danisman_id` + `alici_danisman_id`
- Her danışmanın kendi komisyon oranı ve tutarı
- Finansal raporlama otomatik (Satis model'i kontrol et)

### **4. n8n Entegrasyonu**
- Webhook endpoint'ler `n8n.secret` middleware ile korumalı
- Rate limit: 60 req/dakika
- Payload format: JSON (Content-Type: application/json)
- Yanıt: Standard ResponseService format

### **5. AI Kalite Kontrolü**
- İlan yayınlama öncesi: `YalihanCortex::checkIlanQuality(Ilan)`
- %80 tamamlanma hedefi
- Eksik alanlar: `missing_fields` array'inde döner
- Danışman uyarılır ama ilanı yayınlayabilir (soft-blocking)

---

## 🚀 SONRAKI ADIMLAR (GEMİNİ İÇİN)

1. **Tüm 5 dosyayı oku** (sırasıyla: Cortex → Kisi → Satis → create.blade → routes)
2. **İlişkileri harita et:**
   - Kisi.php'de bir talep başladığında → YalihanCortex.matchForSale() çağrılır
   - İlan yayınlandığında → checkIlanQuality() ve trigger-reverse-match webhook
   - Satış tamamlandığında → Satis model güncellenir, finansal rapor
3. **Test flow'unu takip et:**
   - POST /api/v1/webhook/n8n/test
   - POST /api/v1/webhook/n8n/analyze-market (il_id=34 İstanbul)
   - Yanıt logla (X-N8N-SECRET header'ı gerekli)
4. **AI eğitimini başla:**
   - Gemini'ye: "Yalihan Cortex'e göre, yeni müşteri müşterisi nasıl mı?"
   - Gemini'ye: "Split commission sistemini Python'da kod yaz"
   - Gemini'ye: "n8n workflow'u (3 adımda) oluştur"

---

**Daha sorular? Dosyaları kontrol et! Cevaplar orada. 🎯**
