# 📊 İlan Kategori Yapısı - Tam Liste

**Tarih:** 27 Ekim 2025  
**Sistem:** Yalıhan Emlak Platform

---

## 🏗️ 3 Seviyeli Kategori Yapısı

### Seviye 0: Ana Kategori (5)

### Seviye 1: Alt Kategori (17)

### Seviye 2: Yayın Tipi (Multiple)

---

## 📁 1. KONUT (Ana Kategori)

### 1.1. Daire (Alt Kategori)

- **Yayın Tipleri:**
    - ✅ Satılık
    - ✅ Kiralık

### 1.2. Villa (Alt Kategori)

- **Yayın Tipleri:**
    - ✅ Satılık
    - ✅ Kiralık

### 1.3. Müstakil Ev (Alt Kategori)

- **Yayın Tipleri:**
    - ✅ Satılık
    - ✅ Kiralık

### 1.4. Dubleks (Alt Kategori)

- **Yayın Tipleri:**
    - ✅ Satılık
    - ✅ Kiralık

---

## 🏢 2. İŞYERİ (Ana Kategori)

### 2.1. Ofis (Alt Kategori)

- **Yayın Tipleri:**
    - ✅ Satılık
    - ✅ Kiralık

### 2.2. Dükkan (Alt Kategori)

- **Yayın Tipleri:**
    - ✅ Satılık
    - ✅ Kiralık

### 2.3. Fabrika (Alt Kategori)

- **Yayın Tipleri:**
    - ✅ Satılık
    - ✅ Kiralık

### 2.4. Depo (Alt Kategori)

- **Yayın Tipleri:**
    - ✅ Satılık
    - ✅ Kiralık

---

## 🌳 3. ARSA (Ana Kategori)

### 3.1. İmar Arsaları (Alt Kategori)

- **Yayın Tipleri:**
    - ✅ Satılık
    - ✅ Kiralık

### 3.2. Tarım Arazileri (Alt Kategori)

- **Yayın Tipleri:**
    - ✅ Satılık
    - ✅ Kiralık

### 3.3. Orman Arazileri (Alt Kategori)

- **Yayın Tipleri:**
    - ✅ Satılık
    - ✅ Kiralık

---

## 🏖️ 4. YAZLIK KİRALAMA (Ana Kategori)

### 4.1. Günlük Kiralama (Alt Kategori)

- **Özel Özellikler:**
    - ✅ Minimum konaklama günü
    - ✅ Maksimum misafir sayısı
    - ✅ Havuz bilgileri
    - ✅ Günlük/haftalık/aylık fiyat
    - ✅ Sezon bilgileri

### 4.2. Haftalık Kiralama (Alt Kategori)

- **Özel Özellikler:**
    - ✅ Minimum konaklama haftası
    - ✅ Maksimum misafir sayısı
    - ✅ Havuz bilgileri
    - ✅ Günlük/haftalık/aylık fiyat

### 4.3. Aylık Kiralama (Alt Kategori)

- **Özel Özellikler:**
    - ✅ Minimum konaklama ayı
    - ✅ Maksimum misafir sayısı
    - ✅ Havuz bilgileri
    - ✅ Aylık/sezonluk fiyat

---

## 🏨 5. TURİSTİK TESİSLER (Ana Kategori)

### 5.1. Otel (Alt Kategori)

- **Yayın Tipleri:**
    - ✅ Satılık
    - ✅ Kiralık

### 5.2. Pansiyon (Alt Kategori)

- **Yayın Tipleri:**
    - ✅ Satılık
    - ✅ Kiralık

### 5.3. Tatil Köyü (Alt Kategori)

- **Yayın Tipleri:**
    - ✅ Satılık
    - ✅ Kiralık

---

## 📊 Toplam İstatistikler

| Seviye           | Adet | Açıklama |
| ---------------- | ---- | -------- |
| **Ana Kategori** | 5    | Seviye 0 |
| **Alt Kategori** | 17   | Seviye 1 |
| **Yayın Tipi**   | 28+  | Seviye 2 |

---

## 🎯 Kategori Kullanım Alanları

### Property Type Manager

- **URL:** `/admin/property-type-manager`
- **İşlev:** Kategori oluşturma, düzenleme, silme
- **Özellikler:** 3 seviyeli hiyerarşik yapı

### Site Özellikleri

- **URL:** `/admin/site-ozellikleri`
- **İşlev:** Kategoriye bağlı özellik yönetimi
- **Özellikler:** Dinamik özellik seçimi

### İlan Yönetimi

- **URL:** `/admin/ilanlar/create`, `/admin/ilanlar/edit`
- **İşlev:** İlan oluşturma/düzenleme
- **Özellikler:** Kategori bazlı form alanları

---

## 🔗 İlişkiler

### İlan ↔ Kategori (1:1)

```php
$ilan->kategori_id // Alt kategori (seviye 1)
$ilan->parent_kategori_id // Ana kategori (seviye 0)
$ilan->yayin_tipi_id // Yayın tipi (seviye 2)
```

### İlan ↔ Özellikler (N:M)

```php
$ilan->ozellikler // Kategoriye bağlı özellikler
```

---

## 💡 Özel Notlar

### Yazlık Kiralama

- **Özel Tablo:** `yazlik_details` (30+ alan)
- **Takvim Entegrasyonu:** Airbnb, Booking.com, Google Calendar
- **Doluluk Yönetimi:** Günlük durum takibi

### Property Type Manager

- **Alan Bağımlılığı:** Kategori → Yayın Tipi
- **Dinamik Form:** Seçilen kategoriye göre alanlar gösterilir

### Site Özellikleri

- **Kategori Filtreleme:** Alt kategori bazlı özellik listesi
- **Yayın Tipi Filtreleme:** Yayın tipine göre özellik gösterimi

---

## 🚀 Sonraki Adımlar

- [ ] Kategori bazlı fiyatlandırma
- [ ] Kategori bazlı görsel yönetimi
- [ ] Kategori bazlı raporlama
- [ ] Kategori bazlı filtreleme

---

**Hazırlayan:** Yalıhan Bekçi AI System  
**Tarih:** 27 Ekim 2025
