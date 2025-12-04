# 📋 İlan Oluştur Formu - Teknik Dokümantasyon

**Dosya:** `resources/views/admin/ilanlar/create.blade.php`  
**Satır:** 4082 satır  
**Amaç:** 10 adımlı, AI-assisted emlak ilanı oluşturma arayüzü

---

## 🎯 Form Mimarisi

### **5 Ana Bölüm**

1. **Sayfanız Başlığı & Kontroller**
   - İlan Oluştur başlığı
   - Geri Dön butonu
   
2. **İlerleme İndikatörleri**
   - Form tamamlanma yüzdesi (progress bar)
   - Otomatik kayıt durumu göstergesi

3. **Yapışkan Navigasyon (Sticky)**
   - 10 adımlı section sekmelerini gösterir
   - Aktif section'ı highlight eder

4. **Form İçeriği (10 Bölüm)**
   - Section 1-10: Kategori → Yayın Durumu
   
5. **Sabit Alt Panel**
   - İptal Et, Taslak Kaydet, Pasif Kaydet, Yayınla butonları

---

## 📌 10 Adımı Detaylı

### **ADIM 1: KATEGORİ SİSTEMİ**
```
#section-category
Bileşen: @include('admin.ilanlar.components.category-system')
- Ana Kategori (Arsa, Daire, Yazlık, vb.)
- Alt Kategori (dinamik)
- Yayın Tipi (Satış, Kiralama)
```

### **ADIM 2: LOKASYON VE HARİTA**
```
#section-location
Bileşen: @include('admin.ilanlar.components.location-map')
- İl / İlçe / Mahalle dropdown'ları
- OpenStreetMap + Leaflet.js (draggable marker)
- Reverse geocoding (Nominatim)
- GPS konumu alma (getCurrentLocation)
- Adres auto-complete
```

### **ADIM 3: FİYAT YÖNETİMİ**
```
#section-price
Bileşen: @include('admin.ilanlar.components.price-management')
- Fiyat input
- Para Birimi (TRY, USD, EUR, GBP)
- Otomatik döviz dönüştürme
- Piyasa karşılaştırması
```

### **ADIM 4: TEMEL BİLGİLER + AI ASISTANI**
```
#section-basic-info
Bileşen: @include('admin.ilanlar.components.basic-info')
+ AI ASISTANI PANELI:
  - Başlık Öner (AI LLM)
  - Açıklama Öner (AI LLM)
  - Fiyat Öner (AI ML)
  - Alan Önerileri (kategori-spesifik)
  - Tümünü Uygula
  - Geri Al (Ctrl+Z)
```

### **ADIM 5: FOTOĞRAFLAR**
```
#section-photos
Bileşen: @include('admin.ilanlar.components.photo-upload-manager')
- Lychee API ile resim yönetimi
- Sürükle-bırak (drag-drop)
- Başlık resim seçimi
- Sıra değiştirme
```

### **ADIM 6: İLAN ÖZELLİKLERİ**
```
#section-fields
Bileşenler:
- @include('admin.ilanlar.components.smart-field-organizer')
- @include('admin.ilanlar.components.field-dependencies-dynamic')

Dinamik: Kategoriye göre alanlar değişir
- Arsa: Ada No, Parsel No, KAKS, TAKS, İmar Durumu
- Yazlık: Oda, Banyo, Max Misafir, Sezon Tarihleri
- Daire: Kat, Asansör, Isıtma, Balkon
```

### **ADIM 7: KİŞİ BİLGİLERİ (CRM)**
```
#section-person
Bileşen: @include('admin.ilanlar.partials.stable._kisi-secimi')
- Kişi Live Search (Context7 Standard)
- Yeni Kişi Oluştur
- Telefon / Email Doğrulaması
```

### **ADIM 8: SİTE/APARTMAN (Sadece Konut)**
```
#section-site
Bileşen: @include('admin.ilanlar.components.site-apartman-context7')
data-show-for-categories="konut"
- Site/Apartman Auto-complete
- Ortak Alan Bilgileri
- Yönetim Şirketi
```

### **ADIM 9: ANAHTAR (Sadece Konut)**
```
#section-key
Bileşen: @include('admin.ilanlar.components.key-management')
data-show-for-categories="konut"
- Anahtar Numarası
- Anahtar Konumu
- Anahtar Erişimi
```

### **ADIM 10: YAYIN DURUMU**
```
#section-status
- Status (Aktif, Pasif, Taslak, Arşivlenmiş)
- Öncelik Seviyesi (Normal, Yüksek, Acil)
- Sadece CRM'de Yayınla checkbox
```

---

## 🤖 AI ASISTANI PANELI (ADIM 4'TE)

### **Bağlam Durumu (%)**
- Kategori, Lokasyon, Fiyat yüklendiyse → "Hazır"
- Eşik: %70 tamamlanma

### **4 Ana Buton**

| Buton | İşlev | Endpoint | Response |
|-------|-------|----------|----------|
| **Başlık Öner** | LLM çağrısı | `/admin/ilanlar/generate-ai-title` | `{title: "..."}` |
| **Açıklama Öner** | LLM çağrısı | `/admin/ilanlar/generate-ai-description` | `{description: "..."}` |
| **Fiyat Öner** | ML modeli | `/admin/ilanlar/ai-price-optimization` | `{optimized: 12500000}` |
| **Alan Önerileri** | Kategoriye özel | `/admin/ilanlar/ai-property-suggestions` | `{suggestions: [...]}` |

### **Önerilerin Uygulanması**
1. Uygula → Form alanına yazılır
2. Geri Al (Alt+Z) → Önceki değer restore edilir
3. Kopyala → Clipboard'a kopyalanır
4. Görmezden Gel → Öneris silindi

---

## 🔧 JavaScript Sistemleri

### **1. VanillaLocationManager (Harita)**
```javascript
window.VanillaLocationManager = {
  init()                          // Harita başlatma
  setMarker(lat, lng)            // Marker yerleştirme (draggable)
  reverseGeocode(lat, lng)       // Koordinat → Adres
  autoSelectLocationDropdowns()  // İl/İlçe/Mahalle auto-select
  setMapType('satellite')        // Uydu modu
  getCurrentLocation()           // GPS
  startDrawingBoundary()         // Arsa sınırı çiz
  calculateDistance()            // Yakın mesafeler
}
```

### **2. ValidationManager (İstemci Doğrulama)**
```javascript
ValidationManager = {
  rules: {...}                   // Doğrulama kuralları
  validate(fieldName, value)     // Tek alan doğrulaması
  validateAll()                  // Tüm alanları doğrula (submit öncesi)
  showError(fieldName, msg)      // Hata göster
  clearError(fieldName)          // Hata sil
}
```

### **3. DraftAutoSave (Otomatik Taslak Kayıt)**
```javascript
DraftAutoSave = {
  saveDraft()                    // localStorage'a kaydet (30 sn'de bir)
  loadDraft()                    // localStorage'dan yükle
  restoreDraft()                 // Form'u doldur
  discardDraft()                 // localStorage'dan sil
  getProgress()                  // Form tamamlanma % (required fields)
  updateProgressBar()            // Progress bar güncelle
}
```

### **4. AI Suggestion Handler**
```javascript
// Context7 önerilerin uygulanması:
applyToForm(type, value)       // title|description|price|feature
revertChange(change)            // Değişikliği geri al
logChange(action, field, old, new)  // Changelog'a kayıt

// Endpoint'ler POST olarak:
/admin/ilanlar/generate-ai-title
/admin/ilanlar/generate-ai-description
/admin/ilanlar/ai-price-optimization
/admin/ilanlar/ai-property-suggestions
```

### **5. Keyboard Shortcuts**
```
Ctrl+S veya Cmd+S      → Taslak Kaydet
Ctrl+Enter veya Cmd+Enter → İlanı Yayınla
Escape                 → İptal Et (onay ile)
```

---

## 🎨 Styling & Tailwind Classes

### **Progress Bar Renkleri**
```
< 33%  → bg-red-500
< 66%  → bg-yellow-500
≥ 66%  → bg-green-500
```

### **Validation Error Styling**
```
border-red-500           (ön plan kızıl çerçeve)
ring-2 ring-red-500      (shake animasyonu sırasında)
text-red-600             (hata mesajı kızıl)
```

### **AI Panel Gradient**
```
Button 1: from-blue-600 to-indigo-600    (Başlık)
Button 2: from-green-600 to-emerald-600  (Açıklama)
Button 3: from-yellow-500 to-orange-600  (Fiyat)
Button 4: from-cyan-600 to-blue-600      (Alanlar)
```

### **Dark Mode**
```
Tüm bg-white → bg-white dark:bg-gray-900
Tüm text-gray-900 → text-gray-900 dark:text-white
```

---

## 🔄 Form İş Akışı

```
BAŞLA
  ↓
KATEGORI SEÇ (ZORUNLU)
  ↓
LOKASYON SEÇ + HARİTA (ZORUNLU)
  ↓
FİYAT GİR (ZORUNLU)
  ↓
BAŞLIK & AÇIKLAMA (ZOR... AI ÖNER + MANUEL)
  ↓
FOTOĞRAF YÜKLE (Opsiyonel)
  ↓
ÖZELLİKLER DOLDUR (Kategoriye göre)
  ↓
KİŞİ SEÇ (ZORUNLU)
  ↓
SİTE/APARTMAN (Sadece Konut)
  ↓
ANAHTAR (Sadece Konut)
  ↓
YAYIN DURUMU SEÇ (ZORUNLU)
  ↓
DOĞRULA (ValidationManager.validateAll())
  ↓
YAYINLA (POST /admin/ilanlar/store)
  ↓
SONUÇ: 
  - ✅ Başarılı → show.blade.php'ye yönlendir
  - ❌ Hata → Form doldurulmuş şekilde geri dön
```

---

## 💾 Form Verisi Yapısı

```javascript
{
  // ADIM 1: Kategori
  ana_kategori_id: 5,
  alt_kategori_id: 12,
  yayin_tipi_id: 2,

  // ADIM 2: Lokasyon
  il_id: 34,
  ilce_id: 456,
  mahalle_id: 7890,
  adres: "Bodrum, Yalıkavak, Muğla",
  enlem: 37.0344,
  boylam: 27.4305,
  
  // ADIM 3: Fiyat
  fiyat: 12500000,
  para_birimi: "TRY",
  
  // ADIM 4: Temel Bilgiler
  ilan_basligi: "Bodrum'da Denize Sıfır Villa",
  aciklama: "Uzun detaylı açıklama...",
  
  // ADIM 5: Fotoğraflar
  photo_ids: [1, 2, 3],
  main_photo_id: 1,
  
  // ADIM 6: Özellikler (EAV)
  features: {
    "oda_sayisi": "4",
    "banyo_sayisi": "2",
    "asansor": true,
    "is ema": false,
  },
  
  // ADIM 7: Kişi
  ilan_sahibi_id: 456,
  
  // ADIM 8: Site
  site_id: 123,
  
  // ADIM 9: Anahtar
  anahtar_no: "K-001",
  
  // ADIM 10: Yayın Durumu
  status: "yayinda",
  oncelik: "yuksek",
  crm_only: false,
}
```

---

## 🚀 Deployment Notları

### **Gerekli Bağımlılıklar**
- **Leaflet.js** (Harita)
- **Leaflet.draw** (Sınır çizimi - dinamik yükleme)
- **Nominatim API** (Reverse geocoding)
- **Lychee API** (Fotoğraf yönetimi)
- **Alpine.js** (Reaktivite)

### **Performance İyileştirmeleri**
- ✅ Gerekli alanların validate edilmesi (form bloker olmaz)
- ✅ Otomatik taslak kayıtı (30 saniye aralığında)
- ✅ Lazy loading (harita, draw tools)
- ✅ Rate limiting (Nominatim: 1 req/s)

### **Context7 Uyumluluğu**
- ✅ `il_id` standartlaştırması (sehir_id ❌)
- ✅ `adres` + `enlem`/`boylam` koordinat yönetimi
- ✅ Kategoriye göre dinamik alanlar
- ✅ AI feedback logging (AiLog kaydı)

---

## 🧪 Test Senaryoları

1. **Temel Form Tamamlama**
   - Tüm gerekli alanları doltur
   - Yayınla butonunu tıkla
   - ✓ Success toast ve yönlendirme

2. **AI Önerileri**
   - Kategori + Lokasyon + Fiyat seç
   - "Başlık Öner" tıkla
   - ✓ AI önerisi form'a yazılsın

3. **Validation Hataları**
   - Boş başlıkla yayınla
   - ✓ Hata mesajı gösterin ve form geri dönün

4. **Taslak Kayıt**
   - Formu yarısı doltur ve sayfadan ayrıl
   - Sayfaya geri gel
   - ✓ "Restore Draft" butonu görülsün

5. **Harita İnteraktion**
   - Haritaya tıkla
   - ✓ Marker yerleşsin, koordinatlar otomatik dolsun
   - ✓ Adres reverse geocoding'den dolsun

---

## 📚 Kaynaklar

| Sistem | Dosya | Açıklama |
|--------|-------|----------|
| **Kategori** | `category-system.blade.php` | Ana kategori dropdown'ı |
| **Harita** | `location-map.blade.php` | Leaflet + Nominatim |
| **Fiyat** | `price-management.blade.php` | Fiyat + döviz hesaplama |
| **Temel** | `basic-info.blade.php` | Başlık + açıklama |
| **Fotoğraf** | `photo-upload-manager.blade.php` | Lychee API entegrasyonu |
| **Özellikler** | `smart-field-organizer.blade.php` | Dinamik alan sistemi |

---

**Güncelleme:** 2 Aralık 2025  
**Durum:** ✅ Production Ready  
**Kontrol:** Context7 Uyumlu, AI-Assisted, Mobile-Responsive
