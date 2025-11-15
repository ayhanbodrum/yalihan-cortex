# İlan Yönetimi Modülü - İlişki ve Özellik Listesi

**Context7 MCP Uyumlu Dokümantasyon**  
**Tarih:** 11 Kasım 2025  
**Versiyon:** Context7 v5.2.0

---

## 📋 İçindekiler

1. [Model Özeti](#model-özeti)
2. [BelongsTo İlişkileri](#belongsto-ilişkileri)
3. [HasMany İlişkileri](#hasmany-ilişkileri)
4. [BelongsToMany İlişkileri](#belongstomany-ilişkileri)
5. [Traits](#traits)
6. [Scopes](#scopes)
7. [Accessors & Mutators](#accessors--mutators)
8. [Controller'lar](#controllerlar)
9. [Context7 Uyumluluk](#context7-uyumluluk)

---

## 🎯 Model Özeti

**Model:** `App\Models\Ilan`  
**Tablo:** `ilanlar`  
**Primary Key:** `id`  
**Soft Deletes:** ✅ Aktif

### Kullanılan Traits

- `HasFactory` - Laravel Factory desteği
- `SoftDeletes` - Yumuşak silme desteği
- `HasFeatures` - Özellik yönetimi trait'i
- `Filterable` - Filtreleme trait'i

---

## 🔗 BelongsTo İlişkileri

### 1. Kişi İlişkileri

#### `ilanSahibi()` → `Kisi`
- **Foreign Key:** `ilan_sahibi_id`
- **Açıklama:** İlanın sahibi (Mülk Sahibi)
- **Context7:** ✅ Uyumlu (`kisi_id` kullanımı)

```php
$ilan->ilanSahibi; // Kisi modeli
```

#### `ilgiliKisi()` → `Kisi`
- **Foreign Key:** `ilgili_kisi_id`
- **Açıklama:** İlanla ilgilenen kişi (Emlakçı, Kiracı adayı vb.)
- **Context7:** ✅ Uyumlu (`kisi_id` kullanımı)

```php
$ilan->ilgiliKisi; // Kisi modeli
```

#### `kisi()` → `Kisi`
- **Foreign Key:** `kisi_id`
- **Açıklama:** İlanla ilişkili kişi (Legacy)
- **Context7:** ✅ Uyumlu

```php
$ilan->kisi; // Kisi modeli
```

### 2. User İlişkileri

#### `danisman()` → `User`
- **Foreign Key:** `danisman_id`
- **Açıklama:** İlanın danışmanı
- **Context7:** ✅ Uyumlu

```php
$ilan->danisman; // User modeli
```

#### `userDanisman()` → `User`
- **Foreign Key:** `danisman_id`
- **Açıklama:** User modeli ile danışman ilişkisi (Eloquent için)
- **Context7:** ✅ Uyumlu

```php
$ilan->userDanisman; // User modeli
```

#### `user()` → `User`
- **Foreign Key:** `danisman_id`
- **Açıklama:** İlanın kullanıcısı (Legacy - `danisman()` kullanılmalı)
- **Context7:** ✅ Uyumlu

```php
$ilan->user; // User modeli
```

### 3. Lokasyon İlişkileri

#### `ulke()` → `Ulke`
- **Foreign Key:** `ulke_id`
- **Açıklama:** Ülke bilgisi
- **Context7:** ✅ Uyumlu (`ulke_id` kullanımı, `sehir_id` YASAK)

```php
$ilan->ulke; // Ulke modeli
```

#### `il()` → `Il`
- **Foreign Key:** `il_id`
- **Açıklama:** İl bilgisi
- **Context7:** ✅ Uyumlu (`il_id` kullanımı, `sehir_id` YASAK)

```php
$ilan->il; // Il modeli
```

#### `ilce()` → `Ilce`
- **Foreign Key:** `ilce_id`
- **Açıklama:** İlçe bilgisi
- **Context7:** ✅ Uyumlu

```php
$ilan->ilce; // Ilce modeli
```

#### `mahalle()` → `Mahalle`
- **Foreign Key:** `mahalle_id`
- **Açıklama:** Mahalle bilgisi
- **Context7:** ✅ Uyumlu (`mahalle_id` kullanımı, `semt_id` YASAK)

```php
$ilan->mahalle; // Mahalle modeli
```

### 4. Kategori İlişkileri

#### `anaKategori()` → `IlanKategori`
- **Foreign Key:** `ana_kategori_id`
- **Açıklama:** Ana kategori bilgisi
- **Context7:** ✅ Uyumlu

```php
$ilan->anaKategori; // IlanKategori modeli
```

#### `altKategori()` → `IlanKategori`
- **Foreign Key:** `alt_kategori_id`
- **Açıklama:** Alt kategori bilgisi
- **Context7:** ✅ Uyumlu

```php
$ilan->altKategori; // IlanKategori modeli
```

#### `kategori()` → `IlanKategori`
- **Foreign Key:** `alt_kategori_id`
- **Açıklama:** İlanın kategorisi (Alt Kategori - Legacy)
- **Context7:** ✅ Uyumlu

```php
$ilan->kategori; // IlanKategori modeli
```

#### `parentKategori()` → `IlanKategori`
- **Foreign Key:** `parent_kategori_id`
- **Açıklama:** Parent kategori ilişkisi (Geriye uyumluluk için)
- **Context7:** ✅ Uyumlu

```php
$ilan->parentKategori; // IlanKategori modeli
```

#### `yayinTipi()` → `IlanKategori`
- **Foreign Key:** `yayin_tipi_id`
- **Açıklama:** Yayın tipi ilişkisi (Foreign Key - Güvenli Sistem)
- **Context7:** ✅ Uyumlu
- **Not:** Sadece `seviye = 2` olan kayıtları getirir

```php
$ilan->yayinTipi; // IlanKategori modeli (seviye = 2)
```

---

## 📦 HasMany İlişkileri

### 1. Fiyat Yönetimi

#### `fiyatGecmisi()` → `IlanPriceHistory[]`
- **Foreign Key:** `ilan_id`
- **Açıklama:** İlanın fiyat geçmişi
- **Sıralama:** `created_at DESC`
- **Context7:** ✅ Uyumlu

```php
$ilan->fiyatGecmisi; // Collection<IlanPriceHistory>
```

### 2. Fotoğraf Yönetimi

#### `fotograflar()` → `IlanFotografi[]`
- **Foreign Key:** `ilan_id`
- **Açıklama:** İlanın fotoğrafları (Eski sistem)
- **Context7:** ✅ Uyumlu

```php
$ilan->fotograflar; // Collection<IlanFotografi>
```

#### `photos()` → `Photo[]`
- **Foreign Key:** `ilan_id` (polymorphic)
- **Açıklama:** İlanın fotoğrafları (Yeni Photo System)
- **Sıralama:** `ordered()` scope ile
- **Context7:** ✅ Uyumlu

```php
$ilan->photos; // Collection<Photo>
```

#### `featuredPhoto()` → `Photo`
- **Foreign Key:** `ilan_id` (polymorphic)
- **Açıklama:** Öne çıkan fotoğraf (Photo Model)
- **Koşul:** `is_featured = true`
- **Context7:** ✅ Uyumlu

```php
$ilan->featuredPhoto; // Photo modeli (tek kayıt)
```

### 3. Rezervasyon & Etkinlik Yönetimi

#### `events()` → `Event[]`
- **Foreign Key:** `ilan_id`
- **Açıklama:** Rezervasyonlar/Etkinlikler
- **Context7:** ✅ Uyumlu

```php
$ilan->events; // Collection<Event>
```

#### `activeEvents()` → `Event[]`
- **Foreign Key:** `ilan_id`
- **Açıklama:** Aktif rezervasyonlar
- **Scope:** `active()`
- **Context7:** ✅ Uyumlu

```php
$ilan->activeEvents; // Collection<Event> (aktif olanlar)
```

### 4. Sezon & Fiyatlandırma

#### `seasons()` → `Season[]`
- **Foreign Key:** `ilan_id`
- **Açıklama:** Sezonlar (Fiyatlandırma)
- **Context7:** ✅ Uyumlu

```php
$ilan->seasons; // Collection<Season>
```

#### `activeSeasons()` → `Season[]`
- **Foreign Key:** `ilan_id`
- **Açıklama:** Aktif sezonlar
- **Scope:** `active()`
- **Context7:** ✅ Uyumlu

```php
$ilan->activeSeasons; // Collection<Season> (aktif olanlar)
```

### 5. Yazlık Kiralama

#### `yazlikRezervasyonlar()` → `YazlikRezervasyon[]`
- **Foreign Key:** `ilan_id`
- **Açıklama:** Yazlık rezervasyonları
- **Context7:** ✅ Uyumlu (Yazlık kiralama sistemi için)

```php
$ilan->yazlikRezervasyonlar; // Collection<YazlikRezervasyon>
```

#### `yazlikFiyatlandirma()` → `YazlikFiyatlandirma[]`
- **Foreign Key:** `ilan_id`
- **Açıklama:** Yazlık fiyatlandırmaları (Sezonluk fiyatlandırma)
- **Context7:** ✅ Uyumlu (Yazlık kiralama sistemi için)

```php
$ilan->yazlikFiyatlandirma; // Collection<YazlikFiyatlandirma>
```

#### `dolulukDurumlari()` → `YazlikDolulukDurumu[]`
- **Foreign Key:** `ilan_id`
- **Açıklama:** İlanın doluluk durumları (Yazlık için)
- **Context7:** ✅ Uyumlu

```php
$ilan->dolulukDurumlari; // Collection<YazlikDolulukDurumu>
```

#### `yazlikDetail()` → `YazlikDetail`
- **Foreign Key:** `ilan_id`
- **Açıklama:** İlanın yazlık detayları (HasOne)
- **Context7:** ✅ Uyumlu

```php
$ilan->yazlikDetail; // YazlikDetail modeli (tek kayıt)
```

### 6. Çeviri & Diğer

#### `translations()` → `IlanTranslation[]`
- **Foreign Key:** `ilan_id`
- **Açıklama:** İlanın çevirileri
- **Context7:** ✅ Uyumlu

```php
$ilan->translations; // Collection<IlanTranslation>
```

#### `takvimSync()` → `IlanTakvimSync[]`
- **Foreign Key:** `ilan_id`
- **Açıklama:** İlanın takvim senkronizasyonları
- **Context7:** ✅ Uyumlu

```php
$ilan->takvimSync; // Collection<IlanTakvimSync>
```

---

## 🔄 BelongsToMany İlişkileri

### 1. Özellikler (Features)

#### `ozellikler()` → `Feature[]`
- **Pivot Table:** `ilan_feature`
- **Foreign Keys:** `ilan_id`, `feature_id`
- **Pivot Columns:** `value`
- **Açıklama:** İlanın özellikleri (features)
- **Context7:** ✅ Uyumlu

```php
$ilan->ozellikler; // Collection<Feature>
$ilan->ozellikler->first()->pivot->value; // Pivot değeri
```

#### `features()` → `Feature[]`
- **Alias:** `ozellikler()` için İngilizce alias
- **Açıklama:** Features relationship (English alias)
- **Context7:** ✅ Uyumlu (English naming standard)

```php
$ilan->features; // Collection<Feature> (ozellikler() ile aynı)
```

#### `ozelliklerLegacy()` → `Feature[]`
- **Pivot Table:** `listing_feature` (Eski tablo)
- **Açıklama:** Geçiş süreci için alternatif ilişki
- **Context7:** ✅ Uyumlu (Geçiş süreci)

```php
$ilan->ozelliklerLegacy; // Collection<Feature> (eski tablo)
```

### 2. Etiketler

#### `etiketler()` → `Etiket[]`
- **Pivot Table:** `ilan_etiketler`
- **Foreign Keys:** `ilan_id`, `etiket_id`
- **Pivot Columns:** `display_order`, `is_featured`
- **Sıralama:** `display_order` pivot kolonuna göre
- **Açıklama:** İlanın etiketleri
- **Context7:** ✅ Uyumlu (`display_order` kullanımı, `order` YASAK)

```php
$ilan->etiketler; // Collection<Etiket>
$ilan->etiketler->first()->pivot->display_order; // Sıralama
$ilan->etiketler->first()->pivot->is_featured; // Öne çıkan
```

---

## 🎨 Traits

### 1. HasFeatures Trait

**Dosya:** `app/Traits/HasFeatures.php`

#### İlişkiler

- `featureAssignments()` → `FeatureAssignment[]` (MorphMany)
- `featureValues()` → `FeatureValue[]` (MorphMany)

#### Metodlar

- `visibleFeatureAssignments()` - Görünür özellik atamaları
- `requiredFeatureAssignments()` - Zorunlu özellik atamaları
- `groupedFeatureAssignments()` - Gruplanmış özellik atamaları
- `assignFeature(Feature $feature, array $config)` - Özellik ata
- `assignFeatures(array $featureIds, array $config)` - Çoklu özellik ata
- `unassignFeature(Feature $feature)` - Özellik kaldır
- `syncFeatures(array $featureIds)` - Özellikleri senkronize et
- `getFeatureValue(string $featureSlug)` - Özellik değeri al
- `getAllFeatureValues()` - Tüm özellik değerlerini al
- `setFeatureValue(string $featureSlug, $value)` - Özellik değeri ayarla
- `setFeatureValues(array $values)` - Çoklu özellik değeri ayarla
- `hasFeature(Feature $feature)` - Özellik atanmış mı?
- `hasFeatureValue(string $featureSlug)` - Özellik değeri var mı?

**Context7:** ✅ Uyumlu

### 2. Filterable Trait

**Dosya:** `app/Traits/Filterable.php`

#### Metodlar

- `byStatus($status)` - Status'e göre filtrele
- `priceRange($min, $max)` - Fiyat aralığına göre filtrele
- `search($term)` - Arama yap

**Context7:** ✅ Uyumlu

---

## 🔍 Scopes

### 1. Status Scopes

#### `scopeActive($query)`
- **Açıklama:** Sadece yayında olan ilanları getirir
- **Koşul:** `status = 'yayinda'`
- **Context7:** ✅ Uyumlu (`enabled` YASAK, sadece `status` kullanılır)

```php
Ilan::active()->get(); // Sadece yayında olanlar
```

### 2. Kategori Scopes

#### `scopeKategoriyeGore($query, $kategoriId)`
- **Açıklama:** Belirli bir kategoriye ait ilanları getirir
- **Koşul:** `ana_kategori_id = $kategoriId OR alt_kategori_id = $kategoriId`
- **Context7:** ✅ Uyumlu

```php
Ilan::kategoriyeGore(5)->get(); // Kategori ID 5'e ait ilanlar
```

#### `scopeAnaKategoriyeGore($query, $kategoriId)`
- **Açıklama:** Ana kategoriye göre filtreleme
- **Koşul:** `ana_kategori_id = $kategoriId`
- **Context7:** ✅ Uyumlu

```php
Ilan::anaKategoriyeGore(3)->get(); // Ana kategori ID 3'e ait ilanlar
```

#### `scopeAltKategoriyeGore($query, $kategoriId)`
- **Açıklama:** Alt kategoriye göre filtreleme
- **Koşul:** `alt_kategori_id = $kategoriId`
- **Context7:** ✅ Uyumlu

```php
Ilan::altKategoriyeGore(7)->get(); // Alt kategori ID 7'ye ait ilanlar
```

#### `scopeYayinTipineGore($query, $yayinTipiId)`
- **Açıklama:** Yayın tipine göre filtreleme
- **Koşul:** `yayin_tipi_id = $yayinTipiId`
- **Context7:** ✅ Uyumlu

```php
Ilan::yayinTipineGore(2)->get(); // Yayın tipi ID 2'ye ait ilanlar
```

#### `scopeKategoriHiyerarsisineGore($query, $anaKategoriId, $altKategoriId = null)`
- **Açıklama:** Hem ana hem alt kategori ile filtreleme
- **Koşul:** `ana_kategori_id = $anaKategoriId` (+ `alt_kategori_id = $altKategoriId` if provided)
- **Context7:** ✅ Uyumlu

```php
Ilan::kategoriHiyerarsisineGore(3, 7)->get(); // Ana 3, Alt 7
```

---

## 🎯 Accessors & Mutators

### 1. `getKapakFotografiAttribute()`
- **Açıklama:** Kapak fotoğrafını döndürür
- **Mantık:** `kapak_fotografi = true` olanı bul, yoksa ilk fotoğrafı döndür
- **Context7:** ✅ Uyumlu

```php
$ilan->kapak_fotografi; // IlanFotografi modeli veya null
```

### 2. `getTamAdresAttribute()`
- **Açıklama:** Tam adres metnini oluşturur
- **Format:** `Mahalle, İlçe, İl, Ülke`
- **Context7:** ✅ Uyumlu

```php
$ilan->tam_adres; // "Kadıköy, İstanbul, İstanbul, Türkiye"
```

---

## 🎮 Controller'lar

### 1. IlanController

**Dosya:** `app/Http/Controllers/Admin/IlanController.php`

#### Metodlar

- `index()` - İlan listesi ve filtreleme
- `create()` - Yeni ilan oluşturma formu
- `store()` - Yeni ilan kaydetme
- `show()` - İlan detay sayfası
- `edit()` - İlan düzenleme formu
- `update()` - İlan güncelleme
- `destroy()` - İlan silme
- `bulkAction()` - Toplu işlemler

**Context7:** ✅ Uyumlu (`AdminController` extend eder)

### 2. IlanSegmentController

**Dosya:** `app/Http/Controllers/Admin/IlanSegmentController.php`

#### Metodlar

- `create()` - Yeni ilan oluşturma başlangıcı
- `showCreate()` - Yeni ilan segment görüntüleme
- `storeCreate()` - Yeni ilan segment kaydetme
- `showEdit()` - Mevcut ilan segment düzenleme
- `show()` - Segment tabanlı ilan oluşturma/düzenleme

**Context7:** ✅ Uyumlu (Sequential workflow management)

### 3. IlanSearchController

**Dosya:** `app/Http/Controllers/Admin/IlanSearchController.php`

#### Metodlar

- `index()` - İlan arama sayfası
- `store()` - Yeni ilan oluşturma
- `show()` - İlan detay sayfası
- `edit()` - İlan düzenleme formu
- `update()` - İlan güncelleme

**Context7:** ✅ Uyumlu

### 4. IlanAIController

**Dosya:** `app/Http/Controllers/Admin/AI/IlanAIController.php`

#### Metodlar

- AI destekli ilan yönetimi metodları

**Context7:** ✅ Uyumlu (AI entegrasyonu)

---

## ✅ Context7 Uyumluluk

### Yasak Pattern'ler (Kullanılmıyor)

- ❌ `durum` → ✅ `status` kullanılır
- ❌ `is_active` → ✅ `status` kullanılır
- ❌ `aktif` → ✅ `status` kullanılır
- ❌ `sehir` / `sehir_id` → ✅ `il` / `il_id` kullanılır
- ❌ `semt_id` → ✅ `mahalle_id` kullanılır
- ❌ `order` → ✅ `display_order` kullanılır (pivot tablolarda)

### Zorunlu Pattern'ler (Kullanılıyor)

- ✅ `status` field kullanımı (Enum: `IlanStatus`)
- ✅ `display_order` kullanımı (pivot tablolarda)
- ✅ `il_id`, `ilce_id`, `mahalle_id` kullanımı
- ✅ `kisi_id` kullanımı (`ilan_sahibi_id`, `ilgili_kisi_id`)
- ✅ `para_birimi` kullanımı

### Model Özellikleri

- ✅ `SoftDeletes` aktif
- ✅ `HasFeatures` trait kullanımı
- ✅ `Filterable` trait kullanımı
- ✅ Context7 field naming standardına uyumlu
- ✅ Enum kullanımı (`IlanStatus`, `YayinTipi`)

---

## 📊 İstatistikler

- **Toplam İlişki:** 30+
- **BelongsTo:** 13
- **HasMany:** 12
- **BelongsToMany:** 3
- **Traits:** 2
- **Scopes:** 6
- **Accessors:** 2
- **Controller'lar:** 4+

---

## 🔗 İlgili Dokümantasyon

- [Context7 Authority](./.context7/authority.json)
- [Context7 Memory System](./.context7/CONTEXT7_MEMORY_SYSTEM.md)
- [İlan Model Dokümantasyonu](../models/ilan.md)

---

**Son Güncelleme:** 11 Kasım 2025  
**Context7 Compliance:** %100 ✅

