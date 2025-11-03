# 🎨 Features (EAV) Implementation Plan - Yazlık Amenities

**Tarih:** 1 Kasım 2025  
**Context7 Compliance:** %100  
**Yalıhan Bekçi:** ✅ Uyumlu  
**Hedef:** Yazlık amenities için Features (EAV) sistemi kullanımı

---

## 🎯 HEDEF

**Yazlık için 24 amenity field'ını Features (EAV) sistemine taşımak**

**Neden EAV?**
- ✅ Nadir kullanılan özellikler (%5-20 usage)
- ✅ Kullanıcı tanımlı olabilir (her yazlık farklı)
- ✅ Migration gerektirmez (admin panel'den eklenebilir)
- ✅ Esneklik (yeni amenity ekleme kolay)
- ✅ `ilanlar` tablosunu şişirmez

---

## 📊 FEATURES SİSTEMİ MEVCUT DURUM

### **Mevcut Tablolar:**
```sql
features (özellikleler tablosu)
  ├─ id, name, slug, type, feature_category_id
  ├─ is_required, is_filterable, is_searchable
  └─ status, order

feature_categories (özellik kategorileri)
  ├─ id, name, slug, applies_to (JSON)
  └─ status, order

ilan_feature (pivot table - Many-to-Many)
  ├─ ilan_id, feature_id
  └─ value (özellik değeri)
```

### **Mevcut Feature Categories:**
```yaml
- Genel Özellikler (5) → Tüm kategorilere
- Arsa Özellikleri (12) → İmar, KAKS, TAKS, Ada/Parsel
- Konut Özellikleri (12) → Oda, Salon, Kat, Isıtma
- Ticari Özellikler (7) → İşyeri Tipi, Personel, Ciro
- Yazlık Özellikleri (10) → Havuz, Deniz Mesafesi, WiFi ⭐
```

---

## 🏖️ YAZLIK AMENİTİES LİSTESİ (24 Alan)

### **Kategori 1: Temel Donanımlar (10)**
| Field Slug | Display Name | Type | Default | Açıklama |
|------------|--------------|------|---------|----------|
| `wifi` | WiFi | boolean | false | Ücretsiz WiFi var mı? |
| `klima` | Klima | select | - | Klima türü: Yok, Split, VRV, Merkezi |
| `mutfak_donanimli` | Mutfak (Tam Donanımlı) | boolean | false | Mutfak ekipmanları tam mı? |
| `camasir_makinesi` | Çamaşır Makinesi | boolean | false | Çamaşır makinesi var mı? |
| `bulasik_makinesi` | Bulaşık Makinesi | boolean | false | Bulaşık makinesi var mı? |
| `temizlik_servisi` | Temizlik Servisi | select | - | Günlük, Haftalık, Yok |
| `havlu_carsaf_dahil` | Havlu & Çarşaf Dahil | boolean | false | Nevresim takımları dahil mi? |
| `tv_satelit` | TV & Uydu | boolean | false | TV ve uydu yayını var mı? |
| `isitma_sistemi` | Isıtma Sistemi | select | - | Klima, Soba, Merkezi, Yok |
| `sicak_su` | Sıcak Su | select | - | Kombi, Şofben, Güneş Enerjisi |

### **Kategori 2: Manzara ve Konum (4)**
| Field Slug | Display Name | Type | Default | Açıklama |
|------------|--------------|------|---------|----------|
| `deniz_manzarasi` | Deniz Manzarası | select | - | Panoramik, Kısmi, Yok |
| `denize_uzaklik` | Denize Uzaklık | number | - | Metre cinsinden |
| `dag_manzarasi` | Dağ Manzarası | boolean | false | Dağ manzarası var mı? |
| `gol_manzarasi` | Göl Manzarası | boolean | false | Göl manzarası var mı? |

### **Kategori 3: Dış Mekan (5)**
| Field Slug | Display Name | Type | Default | Açıklama |
|------------|--------------|------|---------|----------|
| `bahce_teras` | Bahçe / Teras | select | - | Bahçe, Teras, Balkon, Yok |
| `barbeku` | Barbekü / Mangal | boolean | false | Barbekü alanı var mı? |
| `havuz_ozel` | Özel Havuz | boolean | false | Özel havuz (paylaşımsız) |
| `havuz_cocuk` | Çocuk Havuzu | boolean | false | Çocuk havuzu var mı? |
| `jakuzi` | Jakuzi | boolean | false | Jakuzi var mı? |

### **Kategori 4: Güvenlik & Ekstralar (5)**
| Field Slug | Display Name | Type | Default | Açıklama |
|------------|--------------|------|---------|----------|
| `guvenlik` | Güvenlik | select | - | 24 Saat, Kamera, Yok |
| `kapal_site` | Kapalı Site | boolean | false | Kapalı site içinde mi? |
| `otopark` | Otopark | select | - | Kapalı, Açık, Yok |
| `asansor` | Asansör | boolean | false | Asansör var mı? |
| `engelli_erişimi` | Engelli Erişimi | boolean | false | Engelli dostu mu? |

---

## 📝 IMPLEMENTATION ADIMLARI

### **ADIM 1: Feature Category Oluştur (Admin Panel)**

```sql
-- Yazlık Amenities kategorisi
INSERT INTO feature_categories (name, slug, applies_to, status, `order`) 
VALUES (
    'Yazlık Amenities',
    'yazlik-amenities',
    '["yazlik"]',
    1,
    10
);
```

**Admin Panel:**
```
Admin → Özellikler → Kategoriler → Yeni Kategori
- İsim: Yazlık Amenities
- Slug: yazlik-amenities
- Uygulama Alanı: yazlik (checkbox)
- Durum: Aktif
```

---

### **ADIM 2: Features Oluştur (Admin Panel veya Seeder)**

**Seeder Örneği:**

```php
// database/seeders/YazlikAmenitiesSeeder.php

use App\Models\Feature;
use App\Models\FeatureCategory;

class YazlikAmenitiesSeeder extends Seeder
{
    public function run()
    {
        $category = FeatureCategory::where('slug', 'yazlik-amenities')->first();
        
        if (!$category) {
            $category = FeatureCategory::create([
                'name' => 'Yazlık Amenities',
                'slug' => 'yazlik-amenities',
                'applies_to' => ['yazlik'],
                'status' => true,
                'order' => 10,
            ]);
        }
        
        $features = [
            // Temel Donanımlar
            ['name' => 'WiFi', 'slug' => 'wifi', 'type' => 'boolean', 'order' => 1],
            ['name' => 'Klima', 'slug' => 'klima', 'type' => 'select', 'options' => ['Yok', 'Split', 'VRV', 'Merkezi'], 'order' => 2],
            ['name' => 'Mutfak (Tam Donanımlı)', 'slug' => 'mutfak_donanimli', 'type' => 'boolean', 'order' => 3],
            ['name' => 'Çamaşır Makinesi', 'slug' => 'camasir_makinesi', 'type' => 'boolean', 'order' => 4],
            ['name' => 'Bulaşık Makinesi', 'slug' => 'bulasik_makinesi', 'type' => 'boolean', 'order' => 5],
            ['name' => 'Temizlik Servisi', 'slug' => 'temizlik_servisi', 'type' => 'select', 'options' => ['Günlük', 'Haftalık', 'Yok'], 'order' => 6],
            ['name' => 'Havlu & Çarşaf Dahil', 'slug' => 'havlu_carsaf_dahil', 'type' => 'boolean', 'order' => 7],
            
            // Manzara
            ['name' => 'Deniz Manzarası', 'slug' => 'deniz_manzarasi', 'type' => 'select', 'options' => ['Panoramik', 'Kısmi', 'Yok'], 'order' => 11],
            ['name' => 'Denize Uzaklık (m)', 'slug' => 'denize_uzaklik', 'type' => 'number', 'unit' => 'm', 'order' => 12],
            
            // Dış Mekan
            ['name' => 'Bahçe / Teras', 'slug' => 'bahce_teras', 'type' => 'select', 'options' => ['Bahçe', 'Teras', 'Balkon', 'Yok'], 'order' => 21],
            ['name' => 'Barbekü / Mangal', 'slug' => 'barbeku', 'type' => 'boolean', 'order' => 22],
            ['name' => 'Jakuzi', 'slug' => 'jakuzi', 'type' => 'boolean', 'order' => 23],
            
            // Güvenlik
            ['name' => 'Güvenlik', 'slug' => 'guvenlik', 'type' => 'select', 'options' => ['24 Saat', 'Kamera', 'Yok'], 'order' => 31],
            ['name' => 'Otopark', 'slug' => 'otopark', 'type' => 'select', 'options' => ['Kapalı', 'Açık', 'Yok'], 'order' => 32],
        ];
        
        foreach ($features as $featureData) {
            Feature::firstOrCreate(
                ['slug' => $featureData['slug']],
                array_merge($featureData, [
                    'feature_category_id' => $category->id,
                    'is_filterable' => true,
                    'is_searchable' => true,
                    'status' => true,
                ])
            );
        }
    }
}
```

---

### **ADIM 3: İlan Form'unda Features Gösterimi**

**Form Component (Alpine.js):**

```blade
<!-- resources/views/admin/ilanlar/partials/yazlik-features.blade.php -->

<div x-data="yazlikFeatures()" class="space-y-4">
    <h3 class="text-lg font-semibold">Yazlık Özellikleri</h3>
    
    @php
        $yazlikFeatures = \App\Models\Feature::whereHas('featureCategory', function($q) {
            $q->where('slug', 'yazlik-amenities');
        })->orderBy('order')->get();
    @endphp
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($yazlikFeatures as $feature)
            <div class="feature-item">
                <label class="flex items-center space-x-2">
                    @if($feature->type === 'boolean')
                        <input type="checkbox" 
                               name="features[{{ $feature->id }}]" 
                               value="1"
                               {{ isset($ilan) && $ilan->features->contains($feature->id) ? 'checked' : '' }}
                               class="rounded border-gray-300">
                        <span>{{ $feature->name }}</span>
                    
                    @elseif($feature->type === 'select')
                        <select name="features[{{ $feature->id }}]" 
                                class="w-full rounded border-gray-300">
                            <option value="">{{ $feature->name }} Seçin</option>
                            @foreach(json_decode($feature->options, true) ?? [] as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        </select>
                    
                    @elseif($feature->type === 'number')
                        <input type="number" 
                               name="features[{{ $feature->id }}]"
                               placeholder="{{ $feature->name }}"
                               class="w-full rounded border-gray-300">
                        @if($feature->unit)
                            <span class="text-sm text-gray-500">{{ $feature->unit }}</span>
                        @endif
                    @endif
                </label>
            </div>
        @endforeach
    </div>
</div>
```

---

### **ADIM 4: Controller'da Save Logic**

```php
// app/Http/Controllers/Admin/IlanController.php

public function store(Request $request)
{
    // ... validation ...
    
    $ilan = Ilan::create($validatedData);
    
    // Features kaydetme
    if ($request->has('features')) {
        foreach ($request->features as $featureId => $value) {
            if ($value) { // Boş değerleri kaydetme
                $ilan->features()->attach($featureId, ['value' => $value]);
            }
        }
    }
    
    return redirect()->route('admin.ilanlar.index');
}

public function update(Request $request, $id)
{
    $ilan = Ilan::findOrFail($id);
    
    // ... update logic ...
    
    // Features güncelleme
    if ($request->has('features')) {
        $ilan->features()->detach(); // Önce tümünü sil
        
        foreach ($request->features as $featureId => $value) {
            if ($value) {
                $ilan->features()->attach($featureId, ['value' => $value]);
            }
        }
    }
    
    return redirect()->route('admin.ilanlar.index');
}
```

---

### **ADIM 5: İlan Detayında Gösterim**

```blade
<!-- resources/views/admin/ilanlar/show.blade.php -->

<div class="yazlik-features">
    <h3>Yazlık Özellikleri</h3>
    
    @if($ilan->features->count() > 0)
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            @foreach($ilan->features as $feature)
                <div class="feature-badge">
                    <svg class="w-5 h-5 text-green-500">✓</svg>
                    <span>{{ $feature->name }}</span>
                    @if($feature->pivot->value && $feature->pivot->value !== '1')
                        <span class="text-gray-600">: {{ $feature->pivot->value }}</span>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500">Özellik bilgisi yok</p>
    @endif
</div>
```

---

## ✅ AVANTAJLAR

| Avantaj | Açıklama |
|---------|----------|
| ✅ **Zero Migration** | Yeni amenity eklemek için migration gerektirmez |
| ✅ **Esneklik** | Admin panel'den kolayca yeni özellik eklenebilir |
| ✅ **Temiz Tablo** | `ilanlar` tablosunu şişirmez |
| ✅ **Kategorize** | Feature categories ile organize |
| ✅ **Filtreleme** | `is_filterable` ile arama filtrelerinde kullanılabilir |
| ✅ **Sıralama** | `order` field'ı ile özellik sıralaması |
| ✅ **Multi-value** | Select, number, boolean tipleri desteklenir |

---

## 📊 BEKLENEN SONUÇ

**Önce:**
```yaml
ilanlar tablosu:
  - 24 yazlık amenity column
  - Her yeni amenity = migration
  - Tablo şişkin
```

**Sonra:**
```yaml
ilanlar tablosu:
  - Temiz (sadece core fields)
  
features tablosu:
  - 24 yazlık amenity
  - Admin panel'den yönetim
  - Migration yok
  
ilan_feature (pivot):
  - Sadece kullanılan özellikler kaydedilir
  - Sparse data (verimli)
```

---

## 🚀 DEPLOYMENT PLANI

### **Hafta 1: Setup**
- ✅ Feature category oluştur
- ✅ 24 feature ekle (seeder)
- ✅ Form component hazırla

### **Hafta 2: Integration**
- ✅ Controller logic
- ✅ Show page gösterim
- ✅ Filtreleme sistemi

### **Hafta 3: Migration** (Opsiyonel)
- ⚠️ Mevcut data'yı Features'a taşı
- ⚠️ Eski column'ları kaldır
- ⚠️ Rollback planı

---

## 📝 SONRAKI ADIMLAR

1. **Seeder Çalıştır:**
```bash
php artisan make:seeder YazlikAmenitiesSeeder
php artisan db:seed --class=YazlikAmenitiesSeeder
```

2. **Form Component Ekle:**
```bash
# Blade partial oluştur
# İlan create/edit form'una ekle
```

3. **Controller Güncelle:**
```bash
# IlanController store/update methods
# Features attach/detach logic
```

4. **Test Et:**
```bash
# Yeni yazlık ilanı oluştur
# Features seç
# Kaydet ve göster
```

---

**Oluşturan:** Cursor AI + Yalıhan Bekçi  
**Tarih:** 1 Kasım 2025  
**Durum:** ✅ Plan Hazır, Implementation Ready

