# 🤖 AI Yardımcısı - Veri Kaynakları Dokümantasyonu

**Tarih:** 22 Kasım 2025  
**Bölüm:** Temel Bilgiler - AI Yardımcısı  
**Amaç:** AI'nın hangi verileri kullandığını dokümante etmek

---

## 📊 VERİ KAYNAKLARI

### **1. FORM VERİLERİ (JavaScript - Frontend)**

**Fonksiyon:** `collectFormDataForAI()`  
**Dosya:** `resources/js/admin/ilan-create/ai.js`  
**Lokasyon:** `resources/views/admin/ilanlar/create.blade.php` (satır 1195-1240)

#### **Toplanan Veriler:**

```yaml
📋 TEMEL BİLGİLER:
  baslik: Form input (#baslik)
  aciklama: Textarea (#aciklama)

🏷️ KATEGORİ BİLGİLERİ:
  ana_kategori: Dropdown (#ana_kategori) → selectedOptions[0].text
  alt_kategori: Dropdown (#alt_kategori) → selectedOptions[0].text
  yayin_tipi: Dropdown (#yayin_tipi_id) → selectedOptions[0].text

💰 FİYAT BİLGİLERİ:
  fiyat: Form input (name="fiyat")
  para_birimi: Form input (name="para_birimi") → Default: 'TRY'
  metrekare: Form input (name="metrekare")
  oda_sayisi: Dropdown (#oda_sayisi) → selectedOptions[0].text

📍 LOKASYON BİLGİLERİ:
  il: Dropdown (#il_id) → selectedOptions[0].text
  ilce: Dropdown (#ilce_id) → selectedOptions[0].text
  semt: Dropdown (#semt_id) → selectedOptions[0].text (⚠️ Context7: mahalle_id olmalı)
  cadde_sokak: Form input (name="cadde_sokak")

⚙️ ÖZELLİKLER (FEATURES):
  features: FormData → features[category][feature] pattern
  - Dinamik olarak toplanır
  - Kategori bazlı gruplandırılır
```

---

### **2. BACKEND API ENDPOINT'LERİ**

#### **A. Başlık Üretimi**

**Endpoint:** `POST /admin/ilanlar/generate-ai-title`  
**Controller:** `app/Http/Controllers/Admin/AI/IlanAIController.php`  
**Method:** `generateTitle()`

**Gönderilen Veriler:**
```php
[
    'kategori' => $request->input('kategori', 'Gayrimenkul'),
    'lokasyon' => $this->getLocation($request), // İl, İlçe, Mahalle birleşimi
    'yayin_tipi' => $request->input('yayin_tipi', 'Satılık'),
    'fiyat' => $this->formatPrice($request->input('fiyat'), $request->input('para_birimi')),
    'tone' => $request->input('ai_tone', 'seo')
]
```

**AI Servis:** `OllamaService::generateTitle($data)`  
**Model:** `gemma2:2b` (Local AI)

---

#### **B. Açıklama Üretimi**

**Endpoint:** `POST /admin/ilanlar/generate-ai-description`  
**Controller:** `app/Http/Controllers/Admin/AI/IlanAIController.php`  
**Method:** `generateDescription()`

**Gönderilen Veriler:**
```php
[
    'kategori' => $request->input('kategori', 'Gayrimenkul'),
    'lokasyon' => $this->getLocation($request),
    'fiyat' => $this->formatPrice($request->input('fiyat'), $request->input('para_birimi')),
    'metrekare' => $request->input('metrekare', ''),
    'oda_sayisi' => $request->input('oda_sayisi', ''),
    'tone' => $request->input('ai_tone', 'seo')
]
```

**AI Servis:** `OllamaService::generateDescription($data)`  
**Model:** `gemma2:2b` (Local AI)

---

#### **C. Fiyat Önerisi**

**Endpoint:** `POST /api/admin/ai/suggest-price`  
**Controller:** `app/Http/Controllers/Api/IlanAIController.php`  
**Method:** `suggestOptimalPrice()`

**Gönderilen Veriler:**
```php
[
    'category_id' => $request->input('category_id'), // İlan kategorisi ID
    'location_id' => $request->input('location_id'), // İl ID
    'features' => $request->input('features', []), // Özellikler array
    'metrekare' => $request->input('metrekare'),
    'oda_sayisi' => $request->input('oda_sayisi')
]
```

**Ek Veriler:**
- Piyasa verileri (`getMarketData()`)
- Benzer ilanlar analizi
- Lokasyon bazlı fiyat ortalamaları

---

#### **D. Alan Önerileri**

**Endpoint:** `POST /api/admin/ai/suggest-fields`  
**Controller:** `app/Http/Controllers/Api/SmartFieldController.php`  
**Method:** `suggestFields()`

**Gönderilen Veriler:**
```php
[
    'category_slug' => $request->input('category'), // 'konut', 'arsa', 'yazlik'
    'yayin_tipi' => $request->input('yayin_tipi'),
    'location' => [
        'il_id' => $request->input('il_id'),
        'ilce_id' => $request->input('ilce_id'),
        'mahalle_id' => $request->input('mahalle_id')
    ],
    'area' => $request->input('metrekare'),
    'price' => $request->input('fiyat')
]
```

**AI Servis:** `AIService::suggestFieldsForCategory()`  
**Cache:** 1 saat (3600 saniye)

---

## 🔄 VERİ AKIŞI

### **Başlık Üretimi Akışı:**

```mermaid
1. Kullanıcı "Başlık Öner" butonuna tıklar
   ↓
2. JavaScript: collectFormDataForAI() çalışır
   ↓
3. Form verileri toplanır (kategori, lokasyon, fiyat)
   ↓
4. POST /admin/ilanlar/generate-ai-title
   ↓
5. Backend: IlanAIController::generateTitle()
   ↓
6. Veriler normalize edilir ve formatlanır
   ↓
7. OllamaService::generateTitle($data) çağrılır
   ↓
8. Local AI (gemma2:2b) başlık üretir
   ↓
9. JSON response döner (variants array)
   ↓
10. Frontend: Başlık önerileri gösterilir
```

### **Açıklama Üretimi Akışı:**

```mermaid
1. Kullanıcı "Açıklama Öner" butonuna tıklar
   ↓
2. JavaScript: collectFormDataForAI() çalışır
   ↓
3. Form verileri toplanır (kategori, lokasyon, fiyat, metrekare, oda_sayisi)
   ↓
4. POST /admin/ilanlar/generate-ai-description
   ↓
5. Backend: IlanAIController::generateDescription()
   ↓
6. Veriler normalize edilir
   ↓
7. OllamaService::generateDescription($data) çağrılır
   ↓
8. Local AI (gemma2:2b) açıklama üretir
   ↓
9. JSON response döner (description string)
   ↓
10. Frontend: Açıklama textarea'ya yazılır
```

---

## 📝 VERİ TOPLAMA FONKSİYONU (Detaylı)

**Dosya:** `resources/js/admin/ilan-create/ai.js`  
**Fonksiyon:** `collectFormDataForAI()`

```javascript
function collectFormDataForAI() {
    const form = document.getElementById('ilan-create-form');
    if (!form) return {};

    const formData = new FormData(form);
    const data = {};

    // ✅ TEMEL BİLGİLER
    data.baslik = formData.get('baslik') || '';
    data.aciklama = formData.get('aciklama') || '';

    // ✅ KATEGORİ BİLGİLERİ (Dropdown text değerleri)
    data.ana_kategori = document.getElementById('ana_kategori')?.selectedOptions[0]?.text || '';
    data.alt_kategori = document.getElementById('alt_kategori')?.selectedOptions[0]?.text || '';
    data.yayin_tipi = document.getElementById('yayin_tipi_id')?.selectedOptions[0]?.text || '';

    // ✅ FİYAT BİLGİLERİ
    data.fiyat = formData.get('fiyat') || '';
    data.para_birimi = formData.get('para_birimi') || 'TRY';
    data.metrekare = formData.get('metrekare') || '';

    // ✅ ODA SAYISI (Dropdown veya input)
    const odaSayisiElement = document.getElementById('oda_sayisi');
    data.oda_sayisi = odaSayisiElement?.selectedOptions?.[0]?.text || formData.get('oda_sayisi') || '';

    // ✅ LOKASYON BİLGİLERİ (Dropdown text değerleri)
    data.il = document.getElementById('il_id')?.selectedOptions[0]?.text || '';
    data.ilce = document.getElementById('ilce_id')?.selectedOptions[0]?.text || '';
    data.semt = document.getElementById('semt_id')?.selectedOptions[0]?.text || ''; // ⚠️ mahalle_id olmalı
    data.cadde_sokak = formData.get('cadde_sokak') || '';

    // ✅ ÖZELLİKLER (Dinamik form fields)
    const features = {};
    formData.forEach((value, key) => {
        if (key.startsWith('features[')) {
            const match = key.match(/features\[(\w+)\]\[(\w+)\]/);
            if (match) {
                const category = match[1];
                const feature = match[2];
                if (!features[category]) features[category] = {};
                features[category][feature] = value;
            }
        }
    });
    data.features = features;

    return data;
}
```

---

## ⚠️ SORUNLAR VE İYİLEŞTİRMELER

### **1. Eksik Veri Kontrolü**

**Sorun:** AI'ye yetersiz veri gönderildiğinde kalitesiz sonuç üretiliyor.

**Çözüm:**
```javascript
// Veri hazırlık kontrolü
function checkAIReadiness() {
    const data = collectFormDataForAI();
    let readiness = 0;
    let missing = [];

    if (data.ana_kategori) readiness += 20; else missing.push('Ana Kategori');
    if (data.alt_kategori) readiness += 20; else missing.push('Alt Kategori');
    if (data.il) readiness += 15; else missing.push('İl');
    if (data.ilce) readiness += 15; else missing.push('İlçe');
    if (data.fiyat) readiness += 15; else missing.push('Fiyat');
    if (data.metrekare) readiness += 10; else missing.push('Metrekare');
    if (data.oda_sayisi) readiness += 5; else missing.push('Oda Sayısı');

    return { readiness, missing };
}
```

### **2. Context7 Uyumsuzluğu**

**Sorun:** `semt_id` kullanılıyor, `mahalle_id` olmalı.

**Çözüm:**
```javascript
// ❌ YANLIŞ
data.semt = document.getElementById('semt_id')?.selectedOptions[0]?.text || '';

// ✅ DOĞRU
data.mahalle = document.getElementById('mahalle_id')?.selectedOptions[0]?.text || '';
```

### **3. Veri Formatı Standardizasyonu**

**Sorun:** Farklı endpoint'ler farklı veri formatları bekliyor.

**Çözüm:** Standart context builder fonksiyonu:
```javascript
function buildAIContext() {
    const formData = collectFormDataForAI();
    
    return {
        category: {
            main: formData.ana_kategori,
            sub: formData.alt_kategori,
            publication_type: formData.yayin_tipi
        },
        location: {
            province: formData.il,
            district: formData.ilce,
            neighborhood: formData.mahalle, // ✅ Context7
            street: formData.cadde_sokak
        },
        property: {
            price: formData.fiyat,
            currency: formData.para_birimi,
            area: formData.metrekare,
            room_count: formData.oda_sayisi
        },
        features: formData.features
    };
}
```

---

## 🎯 ÖNERİLER

### **1. Veri Öncelik Sırası**

```yaml
YÜKSEK ÖNCELİK (Zorunlu):
  - Ana Kategori
  - Alt Kategori
  - Yayın Tipi
  - İl
  - İlçe

ORTA ÖNCELİK (Önemli):
  - Fiyat
  - Metrekare
  - Mahalle

DÜŞÜK ÖNCELİK (Opsiyonel):
  - Oda Sayısı
  - Özellikler
  - Cadde/Sokak
```

### **2. AI Hazırlık Göstergesi**

**Mevcut:** `ai-readiness-bar` var ama eksik veri gösterimi yok.

**Önerilen:**
```javascript
function updateAIReadiness() {
    const { readiness, missing } = checkAIReadiness();
    
    // Progress bar güncelle
    document.getElementById('ai-readiness-bar-fill').style.width = readiness + '%';
    
    // Badge güncelle
    const badge = document.getElementById('ai-readiness-badge');
    if (readiness >= 80) {
        badge.textContent = 'Hazır ✅';
        badge.className = 'text-xs px-2 py-1 rounded bg-green-100 text-green-700';
    } else if (readiness >= 50) {
        badge.textContent = 'Kısmen Hazır ⚠️';
        badge.className = 'text-xs px-2 py-1 rounded bg-yellow-100 text-yellow-700';
    } else {
        badge.textContent = 'Hazır Değil ❌';
        badge.className = 'text-xs px-2 py-1 rounded bg-red-100 text-red-700';
    }
    
    // Eksik alanları göster
    const hints = document.getElementById('ai-missing-hints');
    if (missing.length > 0) {
        hints.textContent = `Eksik: ${missing.join(', ')}`;
    } else {
        hints.textContent = '';
    }
}
```

### **3. Real-time Veri Toplama**

**Önerilen:** Form değişikliklerinde otomatik güncelleme:
```javascript
// Form input'larına event listener ekle
['ana_kategori', 'alt_kategori', 'yayin_tipi_id', 'il_id', 'ilce_id', 'mahalle_id', 'fiyat', 'metrekare', 'oda_sayisi'].forEach(id => {
    const element = document.getElementById(id);
    if (element) {
        element.addEventListener('change', updateAIReadiness);
    }
});
```

---

## 📚 İLGİLİ DOSYALAR

```yaml
Frontend:
  - resources/views/admin/ilanlar/create.blade.php (AI panel HTML)
  - resources/js/admin/ilan-create/ai.js (Veri toplama)
  - resources/js/admin/ilan-create/features-ai.js (Feature AI)

Backend:
  - app/Http/Controllers/Admin/AI/IlanAIController.php (Title/Description)
  - app/Http/Controllers/Api/IlanAIController.php (Price suggestion)
  - app/Http/Controllers/Api/SmartFieldController.php (Field suggestions)
  - app/Services/AIService.php (Genel AI servisi)
  - app/Services/OllamaService.php (Local AI entegrasyonu)
```

---

**Son Güncelleme:** 22 Kasım 2025  
**Durum:** Dokümantasyon tamamlandı

