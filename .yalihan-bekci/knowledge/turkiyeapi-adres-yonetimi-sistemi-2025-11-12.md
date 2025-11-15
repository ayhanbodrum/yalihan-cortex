# TurkiyeAPI Adres Yönetimi Sistemi - Çalışma Mantığı

**Tarih:** 2025-11-12  
**Sistem:** Adres Yönetimi (İl, İlçe, Mahalle)  
**API:** TurkiyeAPI (https://api.turkiyeapi.dev)  
**Context7 Compliance:** ✅ %100

---

## 🎯 SİSTEM MİMARİSİ

### İki Aşamalı Veri Yönetimi

```yaml
1. FETCH (Çekme):
   - TurkiyeAPI'den veri çekilir
   - Veritabanına kaydedilmez
   - Sadece UI'da gösterilir
   - "TurkiyeAPI" badge ile işaretlenir

2. SYNC (Senkronizasyon):
   - Çekilen veriler veritabanına kaydedilir
   - Kalıcı hale gelir
   - Diğer modüller tarafından kullanılabilir
```

---

## 📋 ÇALIŞMA MANTIĞI

### 1. İlk Yükleme (Auto-Sync)

**Dosya:** `app/Http/Controllers/Admin/AdresYonetimiController.php`  
**Fonksiyon:** `getIller()`

```php
// Eğer veritabanında il yoksa, TurkiyeAPI'den otomatik çek
if ($iller->isEmpty()) {
    $turkiyeIller = $this->turkiyeAPI->getProvinces();
    // Tüm illeri DB'ye kaydet
    foreach ($turkiyeIller as $il) {
        Il::updateOrCreate(['id' => $il['id']], [
            'il_adi' => $il['name'],
            'plaka_kodu' => str_pad($il['id'], 2, '0', STR_PAD_LEFT)
        ]);
    }
}
```

**Özellikler:**
- ✅ İlk sayfa yüklemesinde otomatik çalışır
- ✅ Kullanıcı müdahalesi gerektirmez
- ✅ Cache kullanır (7200 saniye)

---

### 2. Seçimli Veri Çekme (Fetch)

**Frontend:** `resources/views/admin/adres-yonetimi/index.blade.php`  
**Fonksiyon:** `fetchFromTurkiyeAPI()`

**Backend:** `AdresYonetimiController::fetchFromTurkiyeAPI()`

#### Kullanım Senaryoları:

**Senaryo 1: İl Seçildi**
```javascript
// İl dropdown'ından seçim yapılır
fetchSelectedIlId = 48; // Muğla

// TurkiyeAPI'den ilçeler çekilir
POST /admin/adres-yonetimi/fetch-from-turkiyeapi
{
    "province_id": 48,
    "type": "auto"
}

// Response:
{
    "success": true,
    "data": {
        "districts": [...], // 13 ilçe
        "neighborhoods": [] // Boş (ilçe seçilmedi)
    }
}
```

**Senaryo 2: İlçe Seçildi**
```javascript
// İlçe dropdown'ından seçim yapılır
fetchSelectedIlId = 48; // Muğla
fetchSelectedIlceId = 1; // Bodrum (TurkiyeAPI ID)

// TurkiyeAPI'den mahalleler çekilir
POST /admin/adres-yonetimi/fetch-from-turkiyeapi
{
    "province_id": 48,
    "district_id": 1,
    "type": "auto"
}

// Response:
{
    "success": true,
    "data": {
        "districts": [...], // 13 ilçe
        "neighborhoods": [...] // 574 mahalle
    }
}
```

#### Frontend İşleme:

```javascript
// Çekilen veriler UI'ya eklenir
neighborhoods.forEach((turkiyeMahalle) => {
    this.mahalleler.push({
        id: null, // Henüz DB'de yok
        ilce_id: fetchSelectedIlceId, // TurkiyeAPI ID
        mahalle_adi: turkiyeMahalle.name,
        _from_turkiyeapi: true // İşaretle
    });
});

// "TurkiyeAPI" badge ile gösterilir
<span class="bg-blue-100">TurkiyeAPI</span>
```

**Özellikler:**
- ✅ Veritabanına kaydedilmez
- ✅ Sadece UI'da gösterilir
- ✅ "Sync edilmemiş" etiketi ile işaretlenir
- ✅ Sayfa yenilendiğinde kaybolur

---

### 3. Veri Senkronizasyonu (Sync)

**Frontend:** `syncFromTurkiyeAPI()`  
**Backend:** `AdresYonetimiController::syncFromTurkiyeAPI()`

#### Kritik: İlçe ID Eşleştirmesi

**Sorun:** TurkiyeAPI ID'si ile DB ID'si farklı olabilir!

```php
// Örnek:
// TurkiyeAPI Bodrum ID: 1234
// DB Bodrum ID: 1

// Çözüm: İki aşamalı eşleştirme
```

**Backend Mantığı:**

```php
// 1. DB'de ilçe var mı kontrol et
$dbDistrict = Ilce::find($districtId);

if ($dbDistrict && $dbDistrict->il_id == $provinceId) {
    // DB'de var, TurkiyeAPI ID'sini bul
    $turkiyeAPIDistricts = $this->turkiyeAPI->getDistricts($provinceId);
    $turkiyeAPIDistrict = collect($turkiyeAPIDistricts)->first(function ($tIlce) use ($dbDistrict) {
        return mb_strtolower(trim($tIlce['name'])) === mb_strtolower(trim($dbDistrict->ilce_adi));
    });
    
    $turkiyeAPIDistrictId = $turkiyeAPIDistrict['id']; // TurkiyeAPI ID
    $dbDistrictId = $dbDistrict->id; // DB ID
} else {
    // DB'de yok, TurkiyeAPI'den eşleştir
    $turkiyeAPIDistrict = collect($turkiyeAPIDistricts)->firstWhere('id', $districtId);
    $dbDistrict = Ilce::where('il_id', $provinceId)
        ->where('ilce_adi', $turkiyeAPIDistrict['name'])
        ->first();
    
    $dbDistrictId = $dbDistrict->id; // DB ID
    $turkiyeAPIDistrictId = $districtId; // TurkiyeAPI ID
}

// 2. TurkiyeAPI ID'si ile mahalleleri çek
$mahalleler = $this->turkiyeAPI->getNeighborhoods($turkiyeAPIDistrictId);

// 3. DB ID'si ile mahalleleri kaydet
foreach ($mahalleler as $mahalle) {
    Mahalle::updateOrCreate(
        ['ilce_id' => $dbDistrictId, 'mahalle_adi' => $mahalle['name']],
        ['ilce_id' => $dbDistrictId, 'mahalle_adi' => $mahalle['name']]
    );
}
```

**Özellikler:**
- ✅ İlçe ID eşleştirmesi otomatik yapılır
- ✅ TurkiyeAPI ID'si ile çekilir
- ✅ DB ID'si ile kaydedilir
- ✅ Duplicate kontrolü yapılır
- ✅ Transaction kullanılır (rollback güvenliği)

---

## 🔄 VERİ AKIŞI

### Tam İşlem Akışı:

```
1. Kullanıcı İl Seçer (Muğla - ID: 48)
   ↓
2. "TurkiyeAPI'den Çek" Butonu
   ↓
3. Frontend: fetchFromTurkiyeAPI()
   - province_id: 48 gönderilir
   ↓
4. Backend: fetchFromTurkiyeAPI()
   - TurkiyeAPI'den ilçeler çekilir (13 ilçe)
   - Response döner
   ↓
5. Frontend: İlçeler UI'ya eklenir
   - _from_turkiyeapi: true işareti
   - "TurkiyeAPI" badge gösterilir
   ↓
6. Kullanıcı İlçe Seçer (Bodrum - TurkiyeAPI ID: 1)
   ↓
7. "TurkiyeAPI'den Çek" Butonu (tekrar)
   - province_id: 48, district_id: 1 gönderilir
   ↓
8. Backend: Mahalleler çekilir (574 mahalle)
   ↓
9. Frontend: Mahalleler UI'ya eklenir
   - "Sync edilmemiş" etiketi
   ↓
10. Kullanıcı "Seçime Göre Sync Et" Butonu
    ↓
11. Frontend: syncFromTurkiyeAPI()
    - province_id: 48, district_id: 1 gönderilir
    ↓
12. Backend: syncFromTurkiyeAPI()
    - İlçe ID eşleştirmesi yapılır
    - TurkiyeAPI ID: 1 → DB ID: 1 (Bodrum)
    - Mahalleler TurkiyeAPI'den çekilir
    - DB ID'si ile kaydedilir
    ↓
13. Frontend: refreshData()
    - Veriler yeniden yüklenir
    - Seçili ilçe tekrar seçilir
    ↓
14. UI: Mahalleler görüntülenir
    - "TurkiyeAPI" badge kaldırılır
    - Checkbox'lar aktif olur
```

---

## 🎨 UI/UX ÖZELLİKLERİ

### Fetch Verileri Göstergesi:

```html
<!-- Çekilen veriler bilgi kutusu -->
<div x-show="fetchedData && Object.keys(fetchedData).length > 0">
    <div class="bg-blue-50 dark:bg-blue-900/20">
        <div>İlçeler: <span x-text="fetchedData.districts.length"></span></div>
        <div>Mahalleler: <span x-text="fetchedData.neighborhoods.length"></span></div>
        <p class="text-xs text-blue-600">
            💡 Bu verileri veritabanına kaydetmek için "Sync Et" butonunu kullanabilirsiniz.
        </p>
    </div>
</div>
```

### Mahalle Listesi Gösterimi:

```html
<!-- Fetch'ten gelen mahalleler -->
<span class="text-gray-700">Mahalle Adı</span>
<span class="bg-blue-100 text-blue-800">TurkiyeAPI</span>
<span class="text-xs text-blue-600">Sync edilmemiş</span>

<!-- DB'den gelen mahalleler -->
<span class="text-gray-700">Mahalle Adı</span>
<input type="checkbox" /> <!-- Aktif -->
<button>Edit</button>
<button>Delete</button>
```

---

## 🔍 DEBUG VE LOGLAMA

### Frontend Debug Logları:

```javascript
console.log('🔍 syncFromTurkiyeAPI çağrıldı');
console.log('🔍 fetchSelectedIlId:', this.fetchSelectedIlId);
console.log('🔍 fetchSelectedIlceId:', this.fetchSelectedIlceId);
console.log('🔍 Belirlenen provinceId:', provinceId);
console.log('🔍 Belirlenen districtId:', districtId);
console.log('🔍 Sync Response:', data);
console.log('🔍 Sync Results:', data.results);
```

### Backend Logları:

```php
Log::info("TurkiyeAPI: İlçe eşleştirildi - DB ID: {$dbDistrictId}, TurkiyeAPI ID: {$turkiyeAPIDistrictId}");
Log::info("TurkiyeAPI: İlçe ID {$turkiyeAPIDistrictId} için " . count($mahalleler) . " mahalle çekildi");
Log::warning("TurkiyeAPI: İlçe bulunamadı - districtId: {$districtId}");
```

---

## ⚠️ ÖNEMLİ NOTLAR

### 1. ID Eşleştirmesi Kritik!

```yaml
TurkiyeAPI ID ≠ DB ID olabilir!

Örnek:
  Bodrum:
    TurkiyeAPI ID: 1234
    DB ID: 1

Çözüm:
  - İlçe adına göre eşleştirme yapılır
  - TurkiyeAPI ID'si ile çekilir
  - DB ID'si ile kaydedilir
```

### 2. Sync Butonu Kontrolü

```javascript
// Sync butonu sadece seçim yapıldığında aktif
:disabled="syncing || (!syncSelectedIlId && !syncSelectedIlceId && !fetchSelectedIlId && !fetchSelectedIlceId)"

// Tüm verileri sync etmek zaman aşımına neden olabilir!
// Bu yüzden seçim zorunlu
```

### 3. Performance Optimizasyonu

```yaml
Fetch İşlemi:
  - Hızlı (sadece API çağrısı)
  - Veritabanı yazma yok
  - UI'da gösterim

Sync İşlemi:
  - Yavaş olabilir (DB yazma)
  - Transaction kullanılır
  - Batch insert yapılabilir
```

---

## 📚 İLGİLİ DOSYALAR

### Backend:
- `app/Http/Controllers/Admin/AdresYonetimiController.php`
  - `getIller()` - Otomatik il yükleme
  - `fetchFromTurkiyeAPI()` - Seçimli veri çekme
  - `syncFromTurkiyeAPI()` - Veri senkronizasyonu

### Frontend:
- `resources/views/admin/adres-yonetimi/index.blade.php`
  - `fetchFromTurkiyeAPI()` - Frontend fetch
  - `syncFromTurkiyeAPI()` - Frontend sync
  - `selectIlce()` - İlçe seçimi

### Service:
- `app/Services/TurkiyeAPIService.php`
  - `getProvinces()` - İlleri çek
  - `getDistricts()` - İlçeleri çek
  - `getNeighborhoods()` - Mahalleleri çek

---

## ✅ CONTEXT7 COMPLIANCE

```yaml
Database Fields:
  ✅ il_adi (NOT sehir_adi)
  ✅ ilce_adi (NOT district_name)
  ✅ mahalle_adi (NOT neighborhood_name)
  ✅ il_id (NOT sehir_id)
  ✅ ilce_id (NOT district_id)

API Endpoints:
  ✅ /admin/adres-yonetimi/fetch-from-turkiyeapi
  ✅ /admin/adres-yonetimi/sync-from-turkiyeapi

UI Components:
  ✅ Tailwind CSS utility classes
  ✅ Alpine.js reactive components
  ✅ Vanilla JS (NO heavy libraries)
```

---

## 🚀 KULLANIM ÖRNEKLERİ

### Senaryo 1: Yeni İl Ekleme

```
1. İl dropdown'ından "Muğla" seç
2. "TurkiyeAPI'den Çek" → İlçeler gelir
3. "Seçime Göre Sync Et" → İlçeler DB'ye kaydedilir
```

### Senaryo 2: Mahalle Ekleme

```
1. İl: Muğla seç
2. İlçe: Bodrum seç
3. "TurkiyeAPI'den Çek" → 574 mahalle gelir
4. "Seçime Göre Sync Et" → Mahalleler DB'ye kaydedilir
```

### Senaryo 3: Sadece Belirli İlçe

```
1. İl: Muğla seç
2. İlçe: Bodrum seç
3. "TurkiyeAPI'den Çek" → Sadece Bodrum mahalleleri
4. "Seçime Göre Sync Et" → Sadece Bodrum mahalleleri kaydedilir
```

---

**Son Güncelleme:** 2025-11-12  
**Versiyon:** 1.0.0  
**Durum:** ✅ Production Ready

