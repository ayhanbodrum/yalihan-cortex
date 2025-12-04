# 🗺️ İlan Ekleme Sayfası - Yol Haritası

**Tarih:** Aralık 2025  
**Hedef:** Kolay, karmaşık olmayan, kullanıcı dostu ilan ekleme deneyimi  
**Durum:** 📋 Planlama Aşaması

---

## 📊 MEVCUT DURUM ANALİZİ

### Mevcut Sayfa (`create.blade.php`)
- **10 Bölüm:** Çok fazla, kullanıcıyı yoruyor
- **4000+ satır kod:** Çok karmaşık
- **Sticky navigation:** İyi ama çok fazla sekme
- **TKGM entegrasyonu:** ✅ Var (Ada/Parsel blur event)
- **AI yardımcı:** ✅ Var ama karmaşık

### Sorunlar
1. ❌ **Çok fazla bölüm** (10 adet)
2. ❌ **Uzun sayfa** (scroll gerekiyor)
3. ❌ **Karmaşık validasyon** (çok fazla kural)
4. ❌ **Kategoriye özel alanlar** karışık görünüyor
5. ⚠️ **TKGM entegrasyonu** var ama görünür değil

---

## 🎯 HEDEF: BASİT VE KULLANICI DOSTU

### Prensipler
1. **3-4 Adım Maksimum** (Wizard yaklaşımı)
2. **Sadece Gerekli Alanlar** (opsiyonel alanlar sonra)
3. **TKGM Otomatik Doldurma** (görünür ve anlaşılır)
4. **AI Yardımcı** (ama zorunlu değil)
5. **Mobil Uyumlu** (responsive)

---

## 🚀 ÖNERİLEN YOL HARİTASI

### **YAKLAŞIM: Wizard (Adım Adım) + Akıllı Gruplandırma**

```
┌─────────────────────────────────────────────────┐
│  ADIM 1: TEMEL BİLGİLER (Zorunlu)              │
│  ────────────────────────────────────────────   │
│  • Kategori (Ana + Alt)                         │
│  • Yayın Tipi                                   │
│  • Başlık                                       │
│  • Fiyat + Para Birimi                          │
│  • Lokasyon (İl, İlçe, Mahalle)                │
│  • Adres                                        │
│                                                  │
│  [İleri] butonu → Adım 2'ye geç                 │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│  ADIM 2: DETAYLAR (Kategoriye Özel)             │
│  ────────────────────────────────────────────   │
│  ARSA İSE:                                      │
│  • Ada/Parsel No (TKGM otomatik doldurma) ⭐    │
│  • İmar Durumu (TKGM'den gelir)                │
│  • Alan m² (TKGM'den gelir)                     │
│  • KAKS/TAKS (TKGM'den gelir)                  │
│  • Altyapı bilgileri                            │
│                                                  │
│  KONUT İSE:                                     │
│  • Oda Sayısı                                   │
│  • Brüt/Net m²                                  │
│  • Banyo Sayısı                                 │
│  • Kat/Bina Yaşı                                │
│  • Site Özellikleri                             │
│                                                  │
│  [Geri] [İleri] butonları                       │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│  ADIM 3: EK BİLGİLER (Opsiyonel)                │
│  ────────────────────────────────────────────   │
│  • Açıklama (AI ile üretilebilir) 🤖           │
│  • Fotoğraflar                                  │
│  • İlan Sahibi/Kişi                             │
│  • Site/Apartman                                │
│  • Anahtar Bilgileri                            │
│  • Yayın Durumu                                 │
│                                                  │
│  [Geri] [Taslak Olarak Kaydet] [Yayınla]       │
└─────────────────────────────────────────────────┘
```

---

## 📋 DETAYLI PLAN

### **ADIM 1: TEMEL BİLGİLER** (Zorunlu - 2-3 dakika)

**Amaç:** İlanın temel kimliğini oluştur

**Alanlar:**
```php
✅ Kategori (Ana + Alt) - Dropdown
✅ Yayın Tipi - Radio/Select
✅ Başlık - Text input (AI önerisi opsiyonel)
✅ Fiyat - Number input
✅ Para Birimi - Select (TRY, USD, EUR)
✅ Lokasyon - İl/İlçe/Mahalle (Cascade dropdown)
✅ Adres - Textarea (Harita ile seçilebilir)
```

**Özellikler:**
- ✅ **Harita entegrasyonu:** Adres seçimi için harita açılabilir
- ✅ **AI başlık önerisi:** Kategori + Lokasyon seçilince otomatik öneri
- ✅ **Hızlı validasyon:** Her alan için anlık kontrol

**UI:**
```
┌─────────────────────────────────────┐
│  📝 Temel Bilgiler                  │
│  ─────────────────────────────────  │
│                                      │
│  Kategori *                          │
│  [Ana Kategori ▼] [Alt Kategori ▼] │
│                                      │
│  Yayın Tipi *                        │
│  ( ) Satılık  ( ) Kiralık           │
│                                      │
│  Başlık *                            │
│  [___________________________]      │
│  [🤖 AI ile Üret] (opsiyonel)       │
│                                      │
│  Fiyat *                             │
│  [___________] [Para Birimi ▼]      │
│                                      │
│  Lokasyon *                          │
│  [İl ▼] [İlçe ▼] [Mahalle ▼]       │
│                                      │
│  Adres *                             │
│  [___________________________]      │
│  [🗺️ Haritadan Seç]                 │
│                                      │
│              [İleri →]              │
└─────────────────────────────────────┘
```

---

### **ADIM 2: DETAYLAR** (Kategoriye Özel - 3-5 dakika)

**Amaç:** Kategoriye göre özel alanları doldur

#### **ARSA İSE:**

**TKGM Otomatik Doldurma (ÖNEMLİ!):**
```
┌─────────────────────────────────────┐
│  🏞️ Arsa Bilgileri                  │
│  ─────────────────────────────────  │
│                                      │
│  Ada No *                            │
│  [____] Parsel No *                  │
│  [____]                              │
│                                      │
│  [🔍 TKGM'den Otomatik Doldur] ⭐   │
│  (Ada/Parsel girince otomatik)      │
│                                      │
│  ─────────────────────────────────  │
│  TKGM'den Gelen Bilgiler:           │
│  ✅ Alan: 2.845 m²                   │
│  ✅ İmar Durumu: İmarlı              │
│  ✅ KAKS: 0.60                       │
│  ✅ TAKS: 0.25                       │
│  ✅ Gabari: 7.50 m                   │
│  ✅ Koordinatlar: 37.0344, 27.4305  │
│                                      │
│  [✏️ Düzenle] (manuel override)     │
└─────────────────────────────────────┘
```

**TKGM Mantığı:**
1. Kullanıcı Ada/Parsel girer
2. İl/İlçe seçiliyse otomatik sorgu başlar
3. Loading indicator gösterilir
4. TKGM verileri formu doldurur
5. Kullanıcı isterse manuel düzenleyebilir

**Diğer Arsa Alanları:**
- Altyapı (Elektrik, Su, Doğalgaz) - Checkbox'lar
- Yola Cephe - Checkbox
- Tapu Durumu - Select

#### **KONUT İSE:**

```
┌─────────────────────────────────────┐
│  🏠 Konut Bilgileri                 │
│  ─────────────────────────────────  │
│                                      │
│  Oda Sayısı *                        │
│  [1+1 ▼] [2+1 ▼] [3+1 ▼] ...        │
│                                      │
│  Alan Bilgileri *                    │
│  Brüt m²: [____] Net m²: [____]     │
│                                      │
│  Banyo Sayısı *                      │
│  [1 ▼] [2 ▼] [3 ▼]                  │
│                                      │
│  Kat Bilgileri                        │
│  Bulunduğu Kat: [____]               │
│  Toplam Kat: [____]                  │
│                                      │
│  Bina Yaşı                           │
│  [____] yıl                          │
│                                      │
│  Site Özellikleri                    │
│  ☑ Havuz  ☑ Otopark  ☑ Asansör     │
│  ☑ Güvenlik  ☑ Spor Salonu          │
└─────────────────────────────────────┘
```

---

### **ADIM 3: EK BİLGİLER** (Opsiyonel - 2-3 dakika)

**Amaç:** İlanı zenginleştir, yayınla

**Alanlar:**
```
┌─────────────────────────────────────┐
│  📝 Açıklama                        │
│  ─────────────────────────────────  │
│  [Textarea - 50-5000 karakter]      │
│  [🤖 AI ile Üret] (TKGM + POI kullan)│
│                                      │
│  📸 Fotoğraflar                      │
│  [Drag & Drop veya Seç]             │
│  (Minimum 1 fotoğraf önerilir)      │
│                                      │
│  👤 İlan Sahibi *                    │
│  [Kişi Seç ▼] veya [Yeni Kişi +]    │
│                                      │
│  🏢 Site/Apartman (Opsiyonel)        │
│  [Site Seç ▼] veya [Yeni Site +]    │
│                                      │
│  🔑 Anahtar Bilgileri                │
│  Anahtar Kimde: [▼]                  │
│  Notlar: [________________]          │
│                                      │
│  📢 Yayın Durumu                     │
│  ( ) Taslak  ( ) Aktif  ( ) Pasif   │
│                                      │
│  [← Geri]  [💾 Taslak Kaydet]       │
│              [✅ Yayınla]            │
└─────────────────────────────────────┘
```

---

## 🎨 UI/UX TASARIM PRENSİPLERİ

### 1. **Wizard Navigation**
```
[1] Temel Bilgiler  →  [2] Detaylar  →  [3] Ek Bilgiler
     ✅ Tamamlandı        ⏳ Devam Ediyor     ⏸️ Bekliyor
```

### 2. **Progress Bar**
```
[████████░░░░░░░░░░░░] %40 Tamamlandı
```

### 3. **Smart Validation**
- ✅ Her adımda sadece o adımın validasyonu
- ✅ Hata mesajları alanın altında
- ✅ Başarılı alanlar yeşil checkmark ile

### 4. **TKGM Entegrasyonu**
- ✅ **Görünür buton:** "🔍 TKGM'den Otomatik Doldur"
- ✅ **Loading state:** "TKGM sorgulanıyor..."
- ✅ **Başarı mesajı:** "✅ Parsel bilgileri alındı!"
- ✅ **Hata durumu:** "⚠️ TKGM verisi bulunamadı, manuel girebilirsiniz"

### 5. **AI Yardımcı**
- ✅ **Opsiyonel:** Zorunlu değil
- ✅ **Görünür:** "🤖 AI ile Üret" butonları
- ✅ **Hızlı:** 2-3 saniyede sonuç

---

## 🔧 TEKNİK DETAYLAR

### **Component Yapısı**

```
resources/views/admin/ilanlar/
├── create-wizard.blade.php          # ⭐ YENİ - Ana wizard sayfası
└── components/
    ├── wizard/
    │   ├── step-1-basic-info.blade.php      # Adım 1
    │   ├── step-2-details.blade.php         # Adım 2 (kategoriye özel)
    │   └── step-3-additional.blade.php      # Adım 3
    │
    ├── tkgm-auto-fill.blade.php     # ⭐ YENİ - TKGM widget
    │   - Ada/Parsel input'ları
    │   - "TKGM'den Doldur" butonu
    │   - Loading/Success/Error state'leri
    │   - Otomatik form doldurma
    │
    └── ai-assistant-inline.blade.php # ⭐ YENİ - Inline AI yardımcı
        - Başlık için AI önerisi
        - Açıklama için AI üretimi
        - TKGM + POI kullanarak
```

### **JavaScript Yapısı**

```javascript
// Wizard Manager (Vanilla JS)
class IlanWizardManager {
    constructor() {
        this.currentStep = 1;
        this.totalSteps = 3;
        this.formData = {};
    }
    
    // Adım geçişleri
    nextStep() { }
    prevStep() { }
    
    // Validasyon
    validateStep(step) { }
    
    // TKGM entegrasyonu
    setupTKGMAutoFill() { }
    
    // AI yardımcı
    setupAIAssistant() { }
    
    // Form kaydetme
    saveDraft() { }
    submitForm() { }
}
```

### **Backend Yapısı**

```php
// Controller
IlanController@createWizard()  // Wizard sayfası
IlanController@storeWizard()    // Wizard submit

// API Endpoints
POST /api/admin/ilanlar/wizard/validate-step/{step}
POST /api/admin/ilanlar/wizard/save-draft
POST /api/admin/ilanlar/wizard/submit
```

---

## 🎯 ÖNCELİK SIRASI

### **PHASE 1: Temel Wizard (1-2 gün)**
1. ✅ Wizard yapısı (3 adım)
2. ✅ Adım 1: Temel bilgiler
3. ✅ Adım 2: Detaylar (kategoriye özel)
4. ✅ Adım 3: Ek bilgiler
5. ✅ Navigation (İleri/Geri)
6. ✅ Progress bar

### **PHASE 2: TKGM Entegrasyonu (1 gün)**
1. ✅ TKGM widget component
2. ✅ Ada/Parsel input'ları
3. ✅ Otomatik sorgulama
4. ✅ Form doldurma
5. ✅ Loading/Success/Error state'leri
6. ✅ Manuel override desteği

### **PHASE 3: AI Yardımcı (1 gün)**
1. ✅ Başlık AI önerisi
2. ✅ Açıklama AI üretimi
3. ✅ TKGM + POI kullanımı
4. ✅ Inline AI widget

### **PHASE 4: İyileştirmeler (1 gün)**
1. ✅ Mobil uyumluluk
2. ✅ Keyboard shortcuts
3. ✅ Auto-save (draft)
4. ✅ Validation iyileştirmeleri

---

## 📐 TASARIM ÖRNEĞİ

### **Adım 1: Temel Bilgiler**

```html
<div class="wizard-step" data-step="1">
    <div class="max-w-2xl mx-auto">
        <h2 class="text-2xl font-bold mb-6">Temel Bilgiler</h2>
        
        <!-- Kategori -->
        <div class="mb-6">
            <label class="block text-sm font-medium mb-2">
                Kategori <span class="text-red-500">*</span>
            </label>
            <div class="grid grid-cols-2 gap-4">
                <select name="ana_kategori_id" required>
                    <option value="">Ana Kategori Seçin</option>
                    <!-- options -->
                </select>
                <select name="alt_kategori_id" required>
                    <option value="">Alt Kategori Seçin</option>
                    <!-- options -->
                </select>
            </div>
        </div>
        
        <!-- Başlık -->
        <div class="mb-6">
            <label class="block text-sm font-medium mb-2">
                Başlık <span class="text-red-500">*</span>
            </label>
            <div class="flex gap-2">
                <input type="text" name="baslik" required 
                       class="flex-1 px-4 py-2 border rounded-lg">
                <button type="button" onclick="generateTitle()"
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg">
                    🤖 AI ile Üret
                </button>
            </div>
        </div>
        
        <!-- Fiyat -->
        <div class="mb-6">
            <label class="block text-sm font-medium mb-2">
                Fiyat <span class="text-red-500">*</span>
            </label>
            <div class="flex gap-2">
                <input type="number" name="fiyat" required 
                       class="flex-1 px-4 py-2 border rounded-lg">
                <select name="para_birimi" required
                        class="px-4 py-2 border rounded-lg">
                    <option value="TRY">TRY</option>
                    <option value="USD">USD</option>
                    <option value="EUR">EUR</option>
                </select>
            </div>
        </div>
        
        <!-- Lokasyon -->
        <div class="mb-6">
            <label class="block text-sm font-medium mb-2">
                Lokasyon <span class="text-red-500">*</span>
            </label>
            <div class="grid grid-cols-3 gap-4">
                <select name="il_id" required id="il_id">
                    <option value="">İl Seçin</option>
                    <!-- options -->
                </select>
                <select name="ilce_id" required id="ilce_id">
                    <option value="">İlçe Seçin</option>
                    <!-- options -->
                </select>
                <select name="mahalle_id" id="mahalle_id">
                    <option value="">Mahalle Seçin</option>
                    <!-- options -->
                </select>
            </div>
        </div>
        
        <!-- Adres -->
        <div class="mb-6">
            <label class="block text-sm font-medium mb-2">
                Adres <span class="text-red-500">*</span>
            </label>
            <textarea name="adres" required rows="3"
                      class="w-full px-4 py-2 border rounded-lg"></textarea>
            <button type="button" onclick="openMapPicker()"
                    class="mt-2 px-4 py-2 bg-blue-600 text-white rounded-lg">
                🗺️ Haritadan Seç
            </button>
        </div>
        
        <!-- Navigation -->
        <div class="flex justify-end gap-4 mt-8">
            <button type="button" onclick="wizard.prevStep()"
                    class="px-6 py-3 bg-gray-200 rounded-lg" disabled>
                ← Geri
            </button>
            <button type="button" onclick="wizard.nextStep()"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg">
                İleri →
            </button>
        </div>
    </div>
</div>
```

### **Adım 2: Detaylar (Arsa için)**

```html
<div class="wizard-step" data-step="2" x-show="category === 'arsa'">
    <div class="max-w-2xl mx-auto">
        <h2 class="text-2xl font-bold mb-6">Arsa Detayları</h2>
        
        <!-- TKGM Widget -->
        @include('admin.ilanlar.components.tkgm-auto-fill')
        
        <!-- Diğer Arsa Alanları -->
        <div class="mt-6 space-y-4">
            <!-- Altyapı -->
            <div>
                <label class="block text-sm font-medium mb-2">Altyapı</label>
                <div class="flex gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="altyapi_elektrik">
                        <span class="ml-2">Elektrik</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="altyapi_su">
                        <span class="ml-2">Su</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="altyapi_dogalgaz">
                        <span class="ml-2">Doğalgaz</span>
                    </label>
                </div>
            </div>
        </div>
        
        <!-- Navigation -->
        <div class="flex justify-between gap-4 mt-8">
            <button type="button" onclick="wizard.prevStep()"
                    class="px-6 py-3 bg-gray-200 rounded-lg">
                ← Geri
            </button>
            <button type="button" onclick="wizard.nextStep()"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg">
                İleri →
            </button>
        </div>
    </div>
</div>
```

---

## 🔑 KRİTİK ÖZELLİKLER

### 1. **TKGM Otomatik Doldurma (ÖNEMLİ!)**

**Widget Component:**
```blade
{{-- resources/views/admin/ilanlar/components/tkgm-auto-fill.blade.php --}}
<div class="tkgm-widget" x-data="tkgmAutoFill()">
    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mb-4">
        <div class="flex items-center gap-2 mb-3">
            <svg class="w-5 h-5 text-blue-600">🔍</svg>
            <h3 class="font-semibold">TKGM Otomatik Doldurma</h3>
        </div>
        
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label>Ada No *</label>
                <input type="text" id="ada_no" name="ada_no" 
                       @blur="checkTKGM()" x-model="adaNo">
            </div>
            <div>
                <label>Parsel No *</label>
                <input type="text" id="parsel_no" name="parsel_no" 
                       @blur="checkTKGM()" x-model="parselNo">
            </div>
        </div>
        
        <button type="button" @click="fetchTKGM()" 
                :disabled="loading || !canFetch"
                class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg
                       disabled:opacity-50">
            <span x-show="!loading">🔍 TKGM'den Otomatik Doldur</span>
            <span x-show="loading">⏳ TKGM sorgulanıyor...</span>
        </button>
        
        <!-- TKGM Sonuçları -->
        <div x-show="tkgmData" class="mt-4 p-4 bg-green-50 rounded-lg">
            <h4 class="font-semibold mb-2">✅ TKGM'den Gelen Bilgiler:</h4>
            <ul class="space-y-1 text-sm">
                <li>Alan: <strong x-text="tkgmData.alan_m2"></strong> m²</li>
                <li>İmar Durumu: <strong x-text="tkgmData.imar_statusu"></strong></li>
                <li>KAKS: <strong x-text="tkgmData.kaks"></strong></li>
                <li>TAKS: <strong x-text="tkgmData.taks"></strong></li>
            </ul>
            <button type="button" @click="fillForm()" 
                    class="mt-2 px-3 py-1 bg-green-600 text-white rounded text-sm">
                Formu Doldur
            </button>
        </div>
    </div>
</div>
```

### 2. **AI Yardımcı (Inline)**

**Başlık için:**
```blade
<div class="relative">
    <input type="text" name="baslik" id="baslik">
    <button type="button" onclick="generateTitleWithAI()"
            class="absolute right-2 top-2 px-3 py-1 bg-purple-600 text-white rounded text-sm">
        🤖 AI ile Üret
    </button>
</div>
```

**Açıklama için:**
```blade
<div>
    <textarea name="aciklama" id="aciklama" rows="5"></textarea>
    <button type="button" onclick="generateDescriptionWithAI()"
            class="mt-2 px-4 py-2 bg-purple-600 text-white rounded-lg">
        🤖 AI ile Açıklama Üret (TKGM + POI kullanarak)
    </button>
</div>
```

---

## 📱 RESPONSIVE TASARIM

### **Mobile (< 768px)**
- Wizard adımları tam ekran
- Navigation: Sadece "İleri/Geri" butonları
- Progress bar: Üstte sabit
- Form alanları: Tek sütun

### **Tablet (768px - 1024px)**
- Wizard adımları: 2 sütun (mümkünse)
- Navigation: Adım numaraları görünür

### **Desktop (> 1024px)**
- Wizard adımları: Merkezi, max-width 800px
- Sidebar: İlerleme göstergesi (opsiyonel)

---

## ✅ BAŞARI KRİTERLERİ

1. **Kullanıcı 5 dakikada ilan ekleyebilmeli**
2. **TKGM otomatik doldurma görünür ve çalışır**
3. **Mobilde sorunsuz çalışır**
4. **AI yardımcı opsiyonel ama kullanışlı**
5. **Validation hataları anlaşılır**

---

## 🚀 UYGULAMA PLANI

### **Hafta 1: Temel Wizard**
- [ ] Wizard yapısı oluştur
- [ ] Adım 1: Temel bilgiler
- [ ] Adım 2: Detaylar (kategoriye özel)
- [ ] Adım 3: Ek bilgiler
- [ ] Navigation ve progress bar

### **Hafta 2: TKGM + AI**
- [ ] TKGM widget component
- [ ] Otomatik doldurma mantığı
- [ ] AI yardımcı entegrasyonu
- [ ] Test ve iyileştirmeler

---

**Son Güncelleme:** Aralık 2025  
**Durum:** 📋 Planlama Tamamlandı - Uygulama Bekliyor


