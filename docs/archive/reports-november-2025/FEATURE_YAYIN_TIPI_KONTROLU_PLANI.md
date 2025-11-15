# 🎯 Feature Categories + Yayın Tipi Kontrolü - Implementation Plan

**Tarih:** 1 Kasım 2025 - 23:45  
**Durum:** 📋 PLANLAMA  
**Öncelik:** 🔥 YÜ

KSEK (Kullanıcı önemli sorun tespit etti!)

---

## ❓ KULLANICI SORUSU

> "Arsa Özellikleri" ve "Genel Özellikler" hangi yayın tipinde (Satılık, Kiralık, vb.) gösterilsin?
> Bu ayar nereden yapılmalı?

---

## 🔍 MEVCUT DURUM ANALİZİ

### **Feature Categories Tablosu:**

```sql
feature_categories:
├─ applies_to: ["konut", "arsa", "yazlik"] ✅ KATEGORİ kontrolü var
└─ [YOK] yayin_tipi kontrolü ❌ EKSIK!
```

**Sonuç:** Şu anda "Arsa Özellikleri" **tüm yayın tiplerinde** gösteriliyor!

**Problem:**

- Konut + Satılık → "Arsa Özellikleri" gösterilmemeli!
- Konut + Kiralık → "Kira Bilgileri" gösterilmeli, "Satış Bilgileri" değil!
- Arsa + Satılık → "Arsa Özellikleri" ✅ gösterilmeli
- Arsa + Kiralık → "Arsa Özellikleri" (?) → Kullanıcı ihtiyacına göre

---

## 💡 ÇÖZÜM STRATEJİSİ

### **Strateji A: İlişki Tablosu (Database-Driven)** ⭐ ÖNERİLEN

**Kullanılacak Tablo:** `ilan_kategori_ozellik_baglanti`

**Yapı:**

```sql
CREATE TABLE ilan_kategori_ozellik_baglanti (
    id BIGINT PRIMARY KEY,
    category_id BIGINT,                -- IlanKategori ID (Konut, Arsa)
    ozellik_kategori_id BIGINT,        -- FeatureCategory ID
    yayin_tipi_id BIGINT,              -- IlanKategoriYayinTipi ID
    baglanti_tipi VARCHAR(20),         -- 'yayin'
    zorunlu BOOLEAN DEFAULT 0,
    siralama INT,
    FOREIGN KEY (category_id) REFERENCES ilan_kategorileri(id),
    FOREIGN KEY (ozellik_kategori_id) REFERENCES feature_categories(id),
    FOREIGN KEY (yayin_tipi_id) REFERENCES ilan_kategori_yayin_tipleri(id)
);
```

**Örnek Data:**

```sql
-- Konut + Satılık → Hangi özellik kategorileri?
(category_id: 1, ozellik_kategori_id: 1, yayin_tipi_id: 1, baglanti_tipi: 'yayin')
-- 1: Konut, 1: Genel Özellikler, 1: Satılık

(category_id: 1, ozellik_kategori_id: 3, yayin_tipi_id: 1, baglanti_tipi: 'yayin')
-- 1: Konut, 3: Fiyat Bilgileri, 1: Satılık

(category_id: 1, ozellik_kategori_id: 4, yayin_tipi_id: 2, baglanti_tipi: 'yayin')
-- 1: Konut, 4: Kira Bilgileri, 2: Kiralık (FARKLI!)
```

**Controller Query:**

```php
// İlan create formunda feature kategorilerini çek
public function getFeatureCategoriesForForm($kategoriId, $yayinTipiId)
{
    // Kategori + Yayın Tipi bazlı feature kategorileri
    $featureCategories = FeatureCategory::whereHas('baglantilar', function($q) use ($kategoriId, $yayinTipiId) {
        $q->where('category_id', $kategoriId)
          ->where('yayin_tipi_id', $yayinTipiId)
          ->where('baglanti_tipi', 'yayin');
    })
    ->with(['features' => function($q) {
        $q->where('status', true)->orderBy('order');
    }])
    ->orderBy('order')
    ->get();

    return $featureCategories;
}
```

---

### **Strateji B: Feature Categories Tablosuna Alan Ekle**

**Migration:**

```php
Schema::table('feature_categories', function (Blueprint $table) {
    $table->json('applies_to_yayin_tipleri')->nullable()->after('applies_to');
    // ["satilik", "kiralik"] gibi
});
```

**Örnek Data:**

```sql
-- Genel Özellikler
applies_to: ["konut", "arsa", "isyeri"]
applies_to_yayin_tipleri: ["satilik", "kiralik", "gunluk-kiralik"]
-- Tüm yayın tiplerinde göster

-- Fiyat Bilgileri (Satış)
applies_to: ["konut", "arsa"]
applies_to_yayin_tipleri: ["satilik", "devren-satilik"]
-- Sadece satılık ilanlarında

-- Kira Bilgileri
applies_to: ["konut"]
applies_to_yayin_tipleri: ["kiralik", "gunluk-kiralik"]
-- Sadece kiralık ilanlarında
```

**Controller Query:**

```php
$featureCategories = FeatureCategory::where('status', true)
    ->where(function($q) use ($kategoriSlug, $yayinTipi) {
        // Kategori kontrolü
        $q->whereRaw('FIND_IN_SET(?, applies_to)', [$kategoriSlug])
          ->orWhereNull('applies_to');
    })
    ->where(function($q) use ($yayinTipi) {
        // Yayın tipi kontrolü
        $q->whereRaw('FIND_IN_SET(?, applies_to_yayin_tipleri)', [strtolower($yayinTipi)])
          ->orWhereNull('applies_to_yayin_tipleri');
    })
    ->orderBy('order')
    ->get();
```

---

## 🎨 ADMIN PANEL UI - İMPLEMENTATION

### **Yeni Sayfa: Feature-Yayın Tipi Bağlantıları**

**URL:** `/admin/property-type-manager/{kategori}/yayin-tipleri/{yayin_tipi}/features`

**Örnek:**

```
/admin/property-type-manager/1/yayin-tipleri/1/features
→ Konut + Satılık → Hangi özellik kategorileri?
```

**UI Design:**

```
┌─────────────────────────────────────────────────────────┐
│ 🏘️ Konut > Satılık > Özellik Kategorileri             │
├─────────────────────────────────────────────────────────┤
│                                                           │
│ ✅ Seçili Özellik Kategorileri:                         │
│                                                           │
│ ☑️ Genel Özellikler (Sıra: 1)                           │
│ ☑️ Fiyat Bilgileri - Satış (Sıra: 2)                    │
│ ☑️ Dokuman (Sıra: 3)                                     │
│ ☑️ Konut Özellikleri (Sıra: 4)                          │
│                                                           │
│ ❌ Seçilmemiş:                                           │
│                                                           │
│ ☐ Arsa Özellikleri                                       │
│ ☐ Kira Bilgileri                                         │
│ ☐ Yazlık Amenities                                       │
│                                                           │
│ [Kaydet]                                                  │
└─────────────────────────────────────────────────────────┘
```

**Controller:**

```php
// app/Http/Controllers/Admin/PropertyTypeFeatureController.php

public function index($kategoriId, $yayinTipiId)
{
    $kategori = IlanKategori::findOrFail($kategoriId);
    $yayinTipi = IlanKategoriYayinTipi::findOrFail($yayinTipiId);

    // Tüm feature kategorileri
    $allFeatureCategories = FeatureCategory::where('status', true)
        ->orderBy('order')
        ->get();

    // Bu kategori + yayın tipi için seçili olanlar
    $selectedIds = IlanKategoriOzellikBaglanti::where('category_id', $kategoriId)
        ->where('yayin_tipi_id', $yayinTipiId)
        ->where('baglanti_tipi', 'yayin')
        ->pluck('ozellik_kategori_id')
        ->toArray();

    return view('admin.property-type-manager.yayin-tipi-features', compact(
        'kategori', 'yayinTipi', 'allFeatureCategories', 'selectedIds'
    ));
}

public function update(Request $request, $kategoriId, $yayinTipiId)
{
    $selectedIds = $request->input('feature_categories', []);

    // Mevcut bağlantıları sil
    IlanKategoriOzellikBaglanti::where('category_id', $kategoriId)
        ->where('yayin_tipi_id', $yayinTipiId)
        ->where('baglanti_tipi', 'yayin')
        ->delete();

    // Yeni bağlantıları ekle
    foreach ($selectedIds as $index => $featureCategoryId) {
        IlanKategoriOzellikBaglanti::create([
            'category_id' => $kategoriId,
            'ozellik_kategori_id' => $featureCategoryId,
            'yayin_tipi_id' => $yayinTipiId,
            'baglanti_tipi' => 'yayin',
            'zorunlu' => false,
            'siralama' => $index + 1,
        ]);
    }

    return redirect()->back()->with('success', 'Özellik kategorileri güncellendi!');
}
```

---

## 🛠️ IMPLEMENTATION ADIMLAR

### **Phase 1: Database Check (5 dk)**

```bash
# Tabloyu kontrol et
php artisan tinker
>>> DB::table('ilan_kategori_ozellik_baglanti')->count();
>>> DB::table('ilan_kategori_ozellik_baglanti')->where('baglanti_tipi', 'yayin')->get();
```

**Eğer kayıt yoksa:**

- Seeder oluştur
- Default bağlantıları ekle

---

### **Phase 2: Model İlişkileri (15 dk)**

**FeatureCategory.php:**

```php
public function baglantilar()
{
    return $this->hasMany(IlanKategoriOzellikBaglanti::class, 'ozellik_kategori_id');
}

public function yayinTipleri()
{
    return $this->belongsToMany(
        IlanKategoriYayinTipi::class,
        'ilan_kategori_ozellik_baglanti',
        'ozellik_kategori_id',
        'yayin_tipi_id'
    )->wherePivot('baglanti_tipi', 'yayin');
}
```

**IlanKategoriYayinTipi.php:**

```php
public function featureCategories()
{
    return $this->belongsToMany(
        FeatureCategory::class,
        'ilan_kategori_ozellik_baglanti',
        'yayin_tipi_id',
        'ozellik_kategori_id'
    )->wherePivot('baglanti_tipi', 'yayin');
}
```

---

### **Phase 3: Controller Logic (30 dk)**

**IlanController.php** (create method'unu güncelle):

```php
public function create()
{
    // ... existing code ...

    // ✅ YENI: Kategori + Yayın Tipi bazlı feature kategorileri
    $featureCategories = collect();

    return view('admin.ilanlar.create', compact(
        // ... existing variables ...
        'featureCategories'  // Boş başlasın, JavaScript ile doldurulacak
    ));
}
```

**API Endpoint:**

```php
public function getFeatureCategoriesForForm(Request $request)
{
    $kategoriId = $request->get('kategori_id');
    $yayinTipiSlug = $request->get('yayin_tipi'); // "satilik", "kiralik"

    if (!$kategoriId || !$yayinTipiSlug) {
        return response()->json(['success' => false, 'message' => 'Gerekli parametreler eksik']);
    }

    // Yayın tipi ID'sini bul
    $yayinTipi = IlanKategoriYayinTipi::where('kategori_id', $kategoriId)
        ->where('yayin_tipi', ucfirst($yayinTipiSlug))
        ->first();

    if (!$yayinTipi) {
        return response()->json(['success' => false, 'message' => 'Yayın tipi bulunamadı']);
    }

    // Bu kategori + yayın tipi için feature kategorileri
    $featureCategories = FeatureCategory::whereHas('baglantilar', function($q) use ($kategoriId, $yayinTipi) {
        $q->where('category_id', $kategoriId)
          ->where('yayin_tipi_id', $yayinTipi->id)
          ->where('baglanti_tipi', 'yayin');
    })
    ->with(['features' => function($q) {
        $q->where('status', true)->orderBy('order');
    }])
    ->where('status', true)
    ->orderBy('order')
    ->get();

    return response()->json([
        'success' => true,
        'data' => $featureCategories
    ]);
}
```

---

### **Phase 4: Frontend (İlan Create Form) (30 dk)**

**resources/views/admin/ilanlar/create.blade.php**

JavaScript ekle:

```javascript
// Kategori veya Yayın Tipi değiştiğinde feature kategorilerini yenile
function updateFeatureCategories() {
    const kategoriId = document.getElementById('kategori_id').value;
    const yayinTipi = document.querySelector('input[name="yayin_tipi"]:checked')?.value;

    if (!kategoriId || !yayinTipi) {
        return;
    }

    // API'den feature kategorilerini çek
    fetch(`/api/admin/feature-categories?kategori_id=${kategoriId}&yayin_tipi=${yayinTipi}`)
        .then((res) => res.json())
        .then((data) => {
            if (data.success) {
                renderFeatureCategories(data.data);
            }
        })
        .catch((err) => console.error('Feature kategorileri yüklenemedi:', err));
}

function renderFeatureCategories(categories) {
    const container = document.getElementById('feature-categories-container');

    if (categories.length === 0) {
        container.innerHTML =
            '<p class="text-gray-500">Bu kategori + yayın tipi için özellik bulunamadı.</p>';
        return;
    }

    let html = '';
    categories.forEach((cat) => {
        html += `
            <div class="bg-white dark:bg-gray-800 rounded-lg border p-4 mb-4">
                <h4 class="text-lg font-semibold mb-3">${cat.name}</h4>
                <div class="grid grid-cols-2 gap-4">
                    ${cat.features
                        .map(
                            (feat) => `
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="features[${feat.id}]" value="1" class="mr-2">
                                ${feat.name}
                            </label>
                        </div>
                    `
                        )
                        .join('')}
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

// Event listeners
document.getElementById('kategori_id').addEventListener('change', updateFeatureCategories);
document.querySelectorAll('input[name="yayin_tipi"]').forEach((radio) => {
    radio.addEventListener('change', updateFeatureCategories);
});
```

---

### **Phase 5: Admin Panel UI (1 saat)**

**Route:**

```php
// routes/admin.php
Route::get('/property-type-manager/{kategori}/yayin-tipleri/{yayinTipi}/features',
    [PropertyTypeFeatureController::class, 'index'])
    ->name('admin.property-type-manager.yayin-tipi-features.index');

Route::post('/property-type-manager/{kategori}/yayin-tipleri/{yayinTipi}/features',
    [PropertyTypeFeatureController::class, 'update'])
    ->name('admin.property-type-manager.yayin-tipi-features.update');
```

**View:** (Basit checkbox listesi)

---

## ✅ BAŞARI KRİTERLERİ

```yaml
✅ Konut + Satılık → Sadece satış özellikleri
✅ Konut + Kiralık → Sadece kira özellikleri
✅ Arsa + Satılık → Arsa özellikleri
✅ Admin panel'den kontrol edilebilir
✅ İlan create formunda doğru kategoriler
✅ Database-driven (hard-code yok)
✅ Context7 compliant
```

---

## 📊 TIMELINE

| Phase | Görev                 | Süre   | Toplam      |
| ----- | --------------------- | ------ | ----------- |
| 1     | Database check        | 5 dk   | 5 dk        |
| 2     | Model ilişkileri      | 15 dk  | 20 dk       |
| 3     | Controller logic      | 30 dk  | 50 dk       |
| 4     | Frontend (ilan form)  | 30 dk  | 1h 20dk     |
| 5     | Admin panel UI        | 1 saat | 2h 20dk     |
| 6     | Seeder (default data) | 30 dk  | 2h 50dk     |
| 7     | Testing               | 30 dk  | **3h 20dk** |

**Total:** 3 saat 20 dakika

---

## 🚀 QUICK START

### **Hemen Test Et:**

```bash
# Database'de tablo var mı?
php artisan tinker
>>> Schema::hasTable('ilan_kategori_ozellik_baglanti');
# true

>>> DB::table('ilan_kategori_ozellik_baglanti')->where('baglanti_tipi', 'yayin')->count();
# 0 ise → Seeder gerekli
```

### **Eğer kayıt yoksa, manuel ekle:**

```sql
INSERT INTO ilan_kategori_ozellik_baglanti
(category_id, ozellik_kategori_id, yayin_tipi_id, baglanti_tipi, zorunlu, siralama, created_at, updated_at)
VALUES
-- Konut + Satılık
(1, 1, 1, 'yayin', 0, 1, NOW(), NOW()),  -- Genel Özellikler
(1, 3, 1, 'yayin', 0, 2, NOW(), NOW()),  -- Fiyat Bilgileri

-- Konut + Kiralık
(1, 1, 2, 'yayin', 0, 1, NOW(), NOW()),  -- Genel Özellikler
(1, 4, 2, 'yayin', 0, 2, NOW(), NOW());  -- Kira Bilgileri
```

---

## 📝 NOTLAR

**ÖNEMLİ:** Bu sistem 2-level filtering:

1. **Kategori** (Konut, Arsa) → `applies_to` field
2. **Yayın Tipi** (Satılık, Kiralık) → `ilan_kategori_ozellik_baglanti` tablo

**Mantık:**

```
FeatureCategory gösterilsin mi?
├─ 1. applies_to kontrolü (Konut için mi?)
└─ 2. baglanti kontrolü (Satılık için mi?)
```

**Gelecek İyileştirmeler:**

- Drag & drop sıralama
- Bulk edit (tüm yayın tipleri için aynı anda)
- Import/Export (Excel)
- Template system (yaygın kombinasyonları kaydet)

---

**Oluşturulma:** 1 Kasım 2025 - 23:45  
**Durum:** 📋 PLANLAMA  
**Öncelik:** 🔥 YÜKSEK
