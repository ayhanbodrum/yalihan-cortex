# 🏖️ YAZLIK KİRALAMA SİSTEMİ - MASTER DOKÜMAN

## 📋 GENEL BAKIŞ

Yazlık kiralama sistemi, emlak platformunda turizm amaçlı kısa süreli kiralama hizmetlerini yönetmek için geliştirilmiş kapsamlı bir modüldür.

## 🗄️ VERİTABANI YAPISI

### Tablo: `yazlik_fiyatlandirma`

**Amaç:** Sezonluk fiyatlandırma yönetimi

```sql
- id (PK)
- ilan_id (FK -> ilanlar)
- sezon_tipi (enum: yaz, ara_sezon, kis)
- baslangic_tarihi (date)
- bitis_tarihi (date)
- gunluk_fiyat (decimal)
- haftalik_fiyat (decimal)
- aylik_fiyat (decimal)
- minimum_konaklama (integer)
- maksimum_konaklama (integer)
- ozel_gunler (JSON)
- status (boolean)
- timestamps
```

### Tablo: `yazlik_rezervasyonlar`

**Amaç:** Rezervasyon yönetimi

```sql
- id (PK)
- ilan_id (FK -> ilanlar)
- musteri_adi (string)
- musteri_telefon (string)
- musteri_email (string)
- check_in (date)
- check_out (date)
- misafir_sayisi (integer)
- cocuk_sayisi (integer)
- pet_sayisi (integer)
- ozel_istekler (text)
- toplam_fiyat (decimal)
- kapora_tutari (decimal)
- status (enum: beklemede, onaylandi, iptal, tamamlandi)
- iptal_nedeni (text)
- onay_tarihi (timestamp)
- timestamps
```

## 📂 MODELLER

### YazlikFiyatlandirma

**Path:** `app/Models/YazlikFiyatlandirma.php`

**Önemli Metodlar:**

- `calculatePrice($days)` - Gün sayısına göre fiyat hesaplama
- `scopeActive()` - Aktif fiyatlandırmalar
- `scopeTarihAraliginda($baslangic, $bitis)` - Tarih aralığı filtreleme
- `getSezonTipleri()` - Sezon tipleri array

**Sezon Tipleri:**

- `yaz` - Yaz Sezonu
- `ara_sezon` - Ara Sezon
- `kis` - Kış Sezonu

### YazlikRezervasyon

**Path:** `app/Models/YazlikRezervasyon.php`

**Önemli Metodlar:**

- `scopeCakisan($checkIn, $checkOut, $excludeId)` - Çakışan rezervasyonlar
- `updateDurum($status, $not)` - Durum güncelleme
- `iptalEdilebilinirMi()` - İptal edilebilirlik kontrolü
- `getKonaklumaSuresiAttribute()` - Konaklama süresi (gün)

**Status Enum:**

- `beklemede` - Beklemede
- `onaylandi` - Onaylandı
- `iptal` - İptal Edildi
- `tamamlandi` - Tamamlandı

## 🎮 CONTROLLER'LAR

### YazlikKiralamaController

**Path:** `app/Http/Controllers/Admin/YazlikKiralamaController.php`

**Ana Metodlar:**

- Yazlık ilan yönetimi (CRUD)
- Rezervasyon yönetimi
- Sezon yönetimi
- Fiyatlandırma yönetimi
- Raporlama

### TakvimController

**Path:** `app/Http/Controllers/Admin/TakvimController.php`

**Ana Metodlar:**

- Takvim görünümü
- Rezervasyon takvimi API
- Tarih bloklama
- Rezervasyon oluşturma/silme

## 🛣️ ROUTE YAPISI

### Admin Routes

```php
/admin/yazlik-kiralama (index)
/admin/yazlik-ilanlar (CRUD)
/admin/rezervasyonlar (CRUD + status güncelleme + iptal)
/admin/sezonlar (CRUD)
/admin/fiyatlandirma (CRUD)
/admin/takvim/{ilan} (takvim görünümü)
/admin/raporlar (çeşitli raporlar)
```

### Public Routes

```php
/yazlik-kiralama (liste)
/yazlik-kiralama/arama (arama/filtreleme)
/yazlik-kiralama/{ilan:slug} (detay)
/yazlik-kiralama/{ilan}/rezervasyon (rezervasyon oluştur)
/yazlik-kiralama/rezervasyonlarim (kullanıcı rezervasyonları)
```

### API Routes

```php
/api/yazlik-kiralama/takvim/{ilan}
/api/yazlik-kiralama/fiyat-hesapla
/api/yazlik-kiralama/musaitlik-kontrol
/api/yazlik-kiralama/rezervasyon
/api/yazlik-kiralama/sezonlar
/api/yazlik-kiralama/fiyatlandirma/{ilan}
```

## 🎨 VIEWS

### Admin Views

```
resources/views/admin/yazlik-kiralama/
  ├── index.blade.php (ana liste)
  └── test.blade.php (test sayfası)
```

### Public Views

```
resources/views/yazlik-kiralama/
  ├── index.blade.php (liste)
  └── show.blade.php (detay)
```

## 🎯 FEATURE CATEGORIES

Yazlık kiralama sistemi özel feature kategorileri kullanır:

1. **Yazlık Özellikleri** (slug: `yazlik-ozellikleri`)
    - Havuz özellikleri
    - Denize uzaklık
    - Minimum konaklama
    - vb.

2. **Yazlık Ekstra Özellikler**
    - Ekstra tesisler
    - Aktivite olanakları

## 🚀 SEEDER'LAR

1. **YazlikOzellikleriSeeder** - Yazlık özellik kategorileri
2. **YazlikEkstraOzelliklerSeeder** - Ekstra özellikler
3. **YazlikKiralamaSeeder** - Test verileri

## 💡 İLİŞKİLER

### İlan Model İlişkileri

```php
public function yazlikFiyatlandirma()
{
    return $this->hasMany(YazlikFiyatlandirma::class);
}

public function yazlikRezervasyonlar()
{
    return $this->hasMany(YazlikRezervasyon::class);
}
```

### Dependencies

- İlan sistemi (İlan model)
- Özellik sistemi (Feature, FeatureCategory)
- Fiyat sistemi (para_birimi - ilanlar tablosu)
- CRM sistemi (müşteri bilgileri)

## 🔄 İŞ AKIŞI

### Rezervasyon Oluşturma

1. Müşteri tarih seçimi yapar
2. Müsaitlik kontrolü yapılır (`scopeCakisan`)
3. Fiyat hesaplanır (sezon bazlı)
4. Rezervasyon oluşturulur (status: beklemede)
5. Onay sonrası status: onaylandi
6. Check-in sonrası status: tamamlandi

### Fiyatlandırma Mantığı

1. Tarih aralığına göre sezon belirlenir
2. Minimum konaklama günü kontrol edilir
3. Sezon tipine göre fiyat seçilir
4. Özel günler varsa override yapılır
5. Uzun konaklama indirimleri hesaplanır

## 📊 RAPORLAMA

- Doluluk oranı
- Gelir analizi
- Rezervasyon istatistikleri
- Sezon bazlı karşılaştırma

## ⚠️ KURALLAR

### Context7 Compliance

- Database fields: İngilizce zorunlu
- para_birimi: ilanlar tablosunda
- status: boolean (fiyatlandırma), enum (rezervasyon)
- İlişkiler: belongsTo/hasMany pattern

### İş Kuralları

1. Çakışan rezervasyon yapılamaz
2. Minimum konaklama süresi uygulanır
3. Ödeme sonrası rezervasyon onaylanır
4. İptal politikası uygulanır

## 🔧 YENİ ÖZELLİK EKLEME

1. Migration oluştur
2. Model oluştur
3. Controller metodları ekle
4. Route tanımla
5. View oluştur
6. API endpoint ekle
