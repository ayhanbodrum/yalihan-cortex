# 🔧 Context7 Implementation Standards

**Context7 Standardı:** C7-IMPLEMENTATION-2024-09-30
**Versiyon:** 1.0.0
**Son Güncelleme:** 30 Eylül 2024
**Durum:** ✅ ZORUNLU KURAL

## 🎯 **TEMEL KURAL: IMPLEMENTATION TUTARLILIĞI**

### ❌ **KULLANILMAYACAK:**

#### **1. Controller Alias Kullanımı:**

```php
// ❌ YASAK
$ulkeler = Ulke::get(['id', 'ulke_adi as name']);
$iller = Il::get(['id', 'il_adi as title']);

// ✅ DOĞRU
$ulkeler = Ulke::get(['id', 'ulke_adi']);
$iller = Il::get(['id', 'il_adi']);
```

#### **2. Blade Template Hata Riski:**

```php
// ❌ YASAK
{{ $ulke->ulke_adi }}
{{ $$$$$$$$$$$$il->il_adi }}

// ✅ DOĞRU
{{ $ulke->ulke_adi ?? 'Ülke Seçiniz' }}
{{ $$$$$$$$$$$$il->il_adi ?? 'İl Seçiniz' }}
```

#### **3. API Endpoint CSRF Eksikliği:**

```javascript
// ❌ YASAK
fetch("/api/endpoint");

// ✅ DOĞRU
fetch("/api/endpoint", {
    headers: {
        "X-CSRF-TOKEN": document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content"),
    },
});
```

#### **4. Eski Konum Sistemi Kullanımı:**

```php
// ❌ YASAK
@include('admin.ilanlar.partials.emlakloc-integration')

// ✅ DOĞRU
@include('admin.ilanlar.partials.google-maps-location')
```

### ✅ **KULLANILACAK:**

#### **1. Tutarlı Veri Çekme:**

```php
// Controller'da
$ulkeler = Ulke::orderBy('ulke_adi')->get(['id', 'ulke_adi']);

// Blade'de
@foreach($ulkeler as $ulke)
    <option value="{{ $ulke->id }}">{{ $ulke->ulke_adi }}</option>
@endforeach
```

#### **2. Güvenli Veri Gösterimi:**

```php
// Blade template'lerde
{{ $ulke->ulke_adi ?? 'Ülke Seçiniz' }}
{{ $$$$$$$$$$$$il->il_adi ?? 'İl Seçiniz' }}
{{ $ilce->ilce_adi ?? 'İlçe Seçiniz' }}
```

#### **3. CSRF Token ile API İstekleri:**

```javascript
// Tüm AJAX istekleri için
fetch(url, {
    method: "GET",
    headers: {
        "X-CSRF-TOKEN": document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content"),
        Accept: "application/json",
        "Content-Type": "application/json",
    },
});
```

## 📊 **VERİTABANI YAPISI**

### **Lokasyon Hiyerarşisi:**

```sql
🇹🇷 ulkeler (id, ulke_adi, ulke_kodu)
   └── 📍 iller (id, il_adi, plaka_kodu, ulke_id)
        └── 🏘️ ilceler (id, ilce_adi, il_id)
             └── 🏠 mahalleler (id, mahalle_adi, ilce_id)
```

### **Tablo Yapısı:**

```sql
-- ✅ DOĞRU TABLO ADLARI
ulkeler    (id, ulke_adi, ulke_kodu)
iller      (id, il_adi, plaka_kodu, ulke_id)
ilceler    (id, ilce_adi, il_id)
mahalleler (id, mahalle_adi, ilce_id)

-- ❌ YANLIŞ - KULLANILMAYACAK
sehirler   -- BU TABLO YOK VE OLMAYACAK
bolgeler   -- BU TABLO YOK VE OLMAYACAK
```

## 🔧 **MODEL İLİŞKİLERİ**

### **Doğru Relationship Kullanımları:**

```php
// ✅ DOĞRU
public function il() {
    return $this->belongsTo(Il::class, 'il_id');
}

public function ilce() {
    return $this->belongsTo(Ilce::class, 'ilce_id');
}

public function mahalle() {
    return $this->belongsTo(Mahalle::class, 'mahalle_id');
}

// ❌ YANLIŞ - ASLA KULLANILMAYACAK
public function il() {
    return $this->belongsTo(Sehir::class, 'il_id');
}

public function bolge() {
    return $this->belongsTo(Bolge::class, 'region_id');
}
```

## 🌍 **API ENDPOINTS**

### **✅ Doğru URL Yapısı:**

```
GET /admin/adres-yonetimi/iller/{ulkeId}      // İl listesi
GET /admin/adres-yonetimi/ilceler/{ilId}      // İlçe listesi
GET /admin/adres-yonetimi/mahalleler/{ilceId} // Mahalle listesi
```

### **✅ CSRF Token ile Güvenli İstekler:**

```javascript
// İl yükleme
fetch(`/admin/adres-yonetimi/iller/${ulkeId}`, {
    headers: {
        "X-CSRF-TOKEN": document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content"),
    },
});

// İlçe yükleme
fetch(`/admin/adres-yonetimi/ilceler/${ilId}`, {
    headers: {
        "X-CSRF-TOKEN": document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content"),
    },
});
```

## 🎨 **FRONTEND STANDARTLARI**

### **Neo Design System:**

```html
<!-- ✅ DOĞRU -->
<select class="neo-select">
    <option value="">İl Seçin</option>
    @foreach($iller as $$$$$$$$$$$$il)
    <option value="{{ $$$$$$$$$$$$il->id }}">{{ $$$$$$$$$$$$il->il_adi }}</option>
    @endforeach
</select>

<!-- ❌ YANLIŞ -->
<select class="form-control">
    <!-- Legacy CSS sınıfları kullanılamaz -->
</select>
```

### **JavaScript Güvenlik:**

```javascript
// ✅ DOĞRU - Null kontrolü
if (ulkeSelect && ulkeSelect.value) {
    // İşlem yap
}

// ❌ YANLIŞ - Null kontrolü yok
ulkeSelect.value; // Hata riski
```

## 🔍 **KONTROL SİSTEMİ**

### **Context7 Check Script:**

```bash
# Tüm kontrolleri çalıştır
./scripts/context7-check.sh --performance --security --quality

# Otomatik düzeltme
./scripts/context7-check.sh --auto-fix

# AI analizi
./scripts/context7-check.sh --ai-analysis
```

### **Otomatik Kontroller:**

-   ✅ Database alanları kontrolü
-   ✅ Model ilişkileri kontrolü
-   ✅ API endpoint CSRF kontrolü
-   ✅ Blade template fallback kontrolü
-   ✅ Neo Design System kontrolü

---

## 📋 **ÖZET**

### **Zorunlu Kurallar:**

1. **Alias kullanımı yasak** - Controller'larda `as name` kullanılamaz
2. **Fallback zorunlu** - Blade'de `?? 'default'` kullanılmalı
3. **CSRF token zorunlu** - Tüm API isteklerinde token bulunmalı
4. **Null kontrolü zorunlu** - JavaScript'te element kontrolü yapılmalı
5. **Neo Design System** - Legacy CSS sınıfları kullanılamaz

### **Faydalar:**

-   🚀 **Performans:** Tutarlı veri çekme
-   🔒 **Güvenlik:** CSRF token koruması
-   🎨 **Tasarım:** Neo Design System standardı
-   🛡️ **Hata Önleme:** Fallback değerler
-   🔧 **Bakım:** Otomatik kontrol sistemi

**Bu standartlar Context7 sisteminin temelidir ve tüm geliştirmelerde uygulanmalıdır.**
