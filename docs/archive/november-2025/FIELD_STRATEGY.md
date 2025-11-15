# 🎯 Field Strategy Guide

**Tarih:** 1 Kasım 2025  
**Context7 Compliance:** %100  
**Yalıhan Bekçi:** ✅ Uyumlu

---

## 📋 KATEGORI BAZLI STRATEJİLER

### **1️⃣ ARSA - Direct Columns Strategy**

**Strateji:** `direct_columns`

**Alanlar (16):**

```sql
ada_no, parsel_no, ada_parsel,
imar_statusu, kaks, taks, gabari,
alan_m2, taban_alani,
yola_cephe, yola_cephesi,
altyapi_elektrik, altyapi_su, altyapi_dogalgaz,
elektrik_altyapisi, su_altyapisi, dogalgaz_altyapisi
```

**Neden Direct Column?**

- ✅ Standart alanlar (her arsada var)
- ✅ Sık aranır (index gerekli)
- ✅ TKGM standardı (değişmez)
- ✅ Basit veri tipleri

**Yeni Alan Eklerken:**

```bash
# 1. Migration oluştur
php artisan make:migration add_[field]_to_ilanlar_table

# 2. Migration'da:
$table->string('new_field')->nullable();

# 3. Field Dependencies ekle (Admin Panel)
Property Type Manager → Field Dependencies → Add Field

# 4. Validate et
php artisan fields:validate
```

---

### **2️⃣ KONUT (Daire/Villa) - Direct Columns Strategy**

**Strateji:** `direct_columns`

**Alanlar (Core):**

```sql
oda_sayisi, salon_sayisi, banyo_sayisi,
kat, toplam_kat,
brut_m2, net_m2,
bina_yasi, isitma, isinma_tipi,
esyali, aidat,
site_ozellikleri (JSON)
```

**Neden Direct Column?**

- ✅ Standart alanlar (her konutta var)
- ✅ Sık filtrelenir (fiyat/m², oda sayısı)
- ✅ Performans kritik (en çok ilan kategorisi)

---

### **3️⃣ YAZLIK - Separate Tables Strategy** ⭐ BEST PRACTICE

**Strateji:** `separate_tables`

**Tablo Yapısı:**

```sql
ilanlar (Core fields)
  ├─ id, baslik, fiyat, kategori_id

yazlik_details (Yazlık özel alanlar)
  ├─ ilan_id (FK)
  ├─ gunluk_fiyat, haftalik_fiyat, aylik_fiyat
  ├─ havuz, deniz_manzarasi, wifi_hizi
  └─ min_konaklama, max_konaklama

yazlik_fiyatlandirma (Sezonluk pricing - 1:N)
  ├─ ilan_id (FK)
  ├─ sezon_tipi (yaz, kis, ara_sezon)
  ├─ gunluk_fiyat, haftalik_fiyat
  └─ sezon_baslangic, sezon_bitis

yazlik_rezervasyonlar (Booking sistem - 1:N)
  ├─ ilan_id (FK)
  ├─ check_in, check_out
  ├─ musteri_bilgileri
  └─ odeme_bilgileri
```

**Neden Separate Tables?**

- ✅ Kompleks iş mantığı (sezonluk fiyatlandırma)
- ✅ 1:N ilişkiler (3 sezon = 3 fiyat)
- ✅ Ayrı business logic (rezervasyon sistemi)
- ✅ Time-based data (yaz/kış/ara sezon)
- ✅ External integrations (Airbnb, Booking.com)

**Avantajlar:**

- ✅ `ilanlar` tablosu temiz kalıyor
- ✅ Sezonluk fiyat değişikliği kolay
- ✅ Rezervasyon sistemi bağımsız
- ✅ Platform sync etkilenmiyor

**Yeni Alan Eklerken:**

```bash
# Statik alan için:
php artisan make:migration add_[field]_to_yazlik_details_table

# Dinamik/time-based alan için:
php artisan make:migration create_yazlik_[feature]_table
```

---

### **4️⃣ İŞYERİ - Direct Columns (Monitored) Strategy**

**Strateji:** `direct_columns_monitored`

**Mevcut Alanlar (6):**

```sql
isyeri_tipi, kira_bilgisi, ciro_bilgisi,
ruhsat_durumu, personel_kapasitesi, isyeri_cephesi
```

**Neden Direct (Şimdilik)?**

- ✅ Az kullanılan (ilanların ~%10'u)
- ✅ Basit alanlar
- ⚠️ Monitored (growth izlenecek)

**İzleme Kriterleri:**

```yaml
EĞER aşağıdakilerden biri olursa → Separate Table'a geç:

1. İşyeri ilan sayısı > %20
2. Time-based data gereksinimi (örn: aylık ciro grafiği)
3. 1:N ilişkiler (örn: kiracı geçmişi, ruhsat değişiklikleri)
4. Complex business logic (örn: rezervasyon, shift yönetimi)
```

**Separate Table'a Geçiş Planı (İhtiyaç halinde):**

```sql
-- Gelecekte:
ilanlar (Core)
isyeri_details (Static fields)
isyeri_ciro_history (Time-series)
isyeri_kiracilari (Rental history)
isyeri_ruhsatlar (License history)
```

---

### **5️⃣ CUSTOM/RARE FIELDS - EAV (Features) Strategy**

**Strateji:** `features` (Entity-Attribute-Value)

**Kullanım Senaryosu:**

```yaml
✅ UYGUN:
  - Nadir kullanılan özellikler (%5 < usage)
  - Kullanıcı tanımlı alanlar
  - Kategori özel (sadece bazı ilanlarda)
  - Özel ihtiyaçlar

✅ ÖRNEKLER:
  - "Jeneratör" (sadece fabrika)
  - "Yangın Söndürme Sistemi" (sadece işyeri)
  - "WiFi Hızı" (sadece yazlık/ofis)
  - "Manzara Tipi" (opsiyonel)

❌ YANLIŞ KULLANIM:
  - "Oda Sayısı" → Direct column (her konutta var)
  - "Ada No" → Direct column (her arsada var)
  - "Fiyat" → Direct column (tüm ilanlarda var)
```

**Tablo Yapısı:**

```sql
features
  ├─ id, name, type, feature_category_id

feature_categories
  ├─ id, name, applies_to (JSON)

ilan_feature (Pivot)
  ├─ ilan_id, feature_id, value
```

**Yeni Feature Eklerken:**

```bash
# Admin Panel → Özellikler → Yeni Özellik
# applies_to: ["arsa", "konut"] seç
# type: text, select, number, boolean
```

---

## 🎯 KARAR AĞACI: Yeni Alan Eklerken

```
┌─ Yeni alan eklemek istiyorum
│
├─ ❓ Bu alan tüm kategorilerde mi var?
│  └─ EVET → ilanlar tablosu (core field)
│
├─ ❓ Bu alan sadece 1 kategoriye özel mi?
│  ├─ EVET → Kategori ne?
│  │  ├─ Arsa/Konut → Direct column (ilanlar)
│  │  ├─ Yazlık → yazlik_details tablosu
│  │  └─ İşyeri → Direct column (şimdilik)
│  │
│  └─ HAYIR → Birden fazla kategoride
│     └─ Features sistemi (EAV)
│
├─ ❓ Bu alan time-based/1:N ilişki mi?
│  └─ EVET → Separate table (yazlik_fiyatlandirma gibi)
│
├─ ❓ Nadir kullanılıyor mu? (<%5)
│  └─ EVET → Features sistemi (EAV)
│
└─ ❓ Kompleks iş mantığı var mı?
   └─ EVET → Separate table + Service class
```

---

## ✅ VALIDATION: Field Sync Kontrolü

### **1. Command Kullanımı:**

```bash
# Tüm alanları kontrol et
php artisan fields:validate

# Sadece arsa kategorisi
php artisan fields:validate --category=arsa

# Düzeltme önerileri
php artisan fields:validate --fix

# Detaylı rapor oluştur
php artisan fields:validate --report
```

### **2. Pre-commit Hook (Önerilen):**

```bash
# .git/hooks/pre-commit
#!/bin/bash
php artisan fields:validate --category=all
if [ $? -ne 0 ]; then
    echo "❌ Field sync hatası! Commit iptal edildi."
    exit 1
fi
```

### **3. CI/CD Pipeline:**

```yaml
# .github/workflows/validation.yml
- name: Field Sync Validation
  run: php artisan fields:validate --report
```

---

## 📊 MEVCUT DURUM ANALİZİ

| Kategori     | Strateji           | Alan Sayısı | Durum            |
| ------------ | ------------------ | ----------- | ---------------- |
| **Arsa**     | Direct Columns     | 16          | ✅ Optimal       |
| **Konut**    | Direct Columns     | ~12         | ✅ Optimal       |
| **Yazlık**   | Separate Tables    | 3 tablo     | ✅ BEST PRACTICE |
| **İşyeri**   | Direct (Monitored) | 6           | ⚠️ İzleniyor     |
| **Features** | EAV                | 100+        | ✅ Optimal       |

---

## 🚀 BEST PRACTICES

### **DO:**

- ✅ Standart alanlar için direct columns kullan
- ✅ Time-based data için separate tables kullan
- ✅ Nadir alanlar için features kullan
- ✅ Her yeni alan için `fields:validate` çalıştır
- ✅ Separate table geçişinde migration plan hazırla

### **DON'T:**

- ❌ Her yeni alan için migration yapma (nadir alanlar Features'ta olmalı)
- ❌ Time-based data'yı direct column'da tutma
- ❌ Kompleks JSON'ları ilanlar tablosunda şişirme
- ❌ Field Dependencies ve DB'yi manuel senkronize etme

---

## 📝 MIGRATION PATTERN'LERİ

### **Pattern 1: Direct Column Ekleme**

```php
// Migration: 2025_11_01_add_new_field_to_ilanlar.php
public function up()
{
    Schema::table('ilanlar', function (Blueprint $table) {
        $table->string('new_field')->nullable()->after('existing_field');
        $table->index('new_field'); // Eğer sık aranacaksa
    });
}
```

### **Pattern 2: Separate Table Oluşturma**

```php
// Migration: 2025_11_01_create_category_details_table.php
public function up()
{
    Schema::create('category_details', function (Blueprint $table) {
        $table->id();
        $table->foreignId('ilan_id')->constrained('ilanlar')->onDelete('cascade');
        $table->string('special_field')->nullable();
        $table->timestamps();

        $table->index('ilan_id');
    });
}
```

### **Pattern 3: Feature Ekleme (No Migration)**

```php
// Seeder veya Admin Panel
Feature::create([
    'name' => 'Special Feature',
    'type' => 'select',
    'options' => ['Option 1', 'Option 2'],
    'feature_category_id' => 1,
]);
```

---

## 🔗 İLGİLİ DÖKÜMANLAR

- [Yazlık Backend Tamamlama](BACKEND_TAMAMLAMA_RAPORU.md)
- [Property Type Manager](PROPERTY_TYPE_MANAGER_YENİ_SİSTEM_2025_10_27.md)
- [Context7 Rules](.context7/authority.json)

---

**Son Güncelleme:** 1 Kasım 2025  
**Durum:** ✅ Aktif, Production Ready
