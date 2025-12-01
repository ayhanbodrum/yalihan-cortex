# 📦 Gemini JSON-Based Seeding Instructions

**Tarih:** 2025-11-27  
**Source:** `docs/ai/GEMINI_COMPLETE_SYSTEM_DATA.json v2.0.0`  
**Context7 Compliance:** ✅ %100

---

## 🎯 OLUŞTURULAN DOSYALAR

### 1. Config Dosyası
**Dosya:** `config/yali_options.php`

**İçerik:**
- ✅ İmar Durumu seçenekleri (6 tip)
- ✅ KAKS Aralıkları (5 aralık)
- ✅ TAKS Aralıkları (5 aralık)
- ✅ Gabari Aralıkları (5 aralık)
- ✅ Altyapı seçenekleri (6 tip)
- ✅ Oda Sayısı seçenekleri (19 seçenek)
- ✅ Banyo Sayısı seçenekleri (6 seçenek)
- ✅ Isıtma Tipi seçenekleri (7 seçenek)
- ✅ Para Birimleri (4 seçenek)
- ✅ Status Seçenekleri (7 seçenek)

**Kullanım:**
```php
// Config'den seçenekleri çek
$imarStatusu = config('yali_options.imar_statusu');
$odaSayisiOptions = config('yali_options.oda_sayisi_options');
```

---

### 2. Category Seeder
**Dosya:** `database/seeders/GeminiJsonBasedCategorySeeder.php`

**Özellikler:**
- ✅ JSON dosyasından otomatik okuma
- ✅ Ana kategoriler (Seviye 0)
- ✅ Alt kategoriler (Seviye 1)
- ✅ Yayın tipleri (ilan_kategori_yayin_tipleri tablosu)
- ✅ Context7 uyumlu: `status`, `display_order`
- ✅ UpdateOrCreate pattern (idempotent)

**Çalıştırma:**
```bash
php artisan db:seed --class=GeminiJsonBasedCategorySeeder
```

---

## 🔧 KULLANIM ADIMLARI

### Adım 1: JSON Dosyasını Kontrol Et

```bash
# JSON dosyasının var olduğundan emin ol
ls -la docs/ai/GEMINI_COMPLETE_SYSTEM_DATA.json
```

### Adım 2: Seeder'ı Çalıştır

```bash
# Sadece kategori seeder'ı çalıştır
php artisan db:seed --class=GeminiJsonBasedCategorySeeder

# Veya tüm seeder'ları çalıştır
php artisan db:seed
```

### Adım 3: Verileri Kontrol Et

```bash
# Ana kategorileri kontrol et
php artisan tinker
>>> \App\Models\IlanKategori::where('seviye', 0)->count()
# Beklenen: 5

# Alt kategorileri kontrol et
>>> \App\Models\IlanKategori::where('seviye', 1)->count()
# Beklenen: 17

# Yayın tiplerini kontrol et
>>> \App\Models\IlanKategoriYayinTipi::count()
# Beklenen: ~28
```

---

## ✅ CONTEXT7 COMPLIANCE KONTROLÜ

### Yapılan Kontroller:

1. **Status Field:**
   - ✅ `status` field kullanılıyor (NOT `enabled`, NOT `aktif`)
   - ✅ Boolean veya String olarak cast ediliyor (migration'a göre)

2. **Display Order:**
   - ✅ `display_order` field kullanılıyor (NOT `order`)

3. **Field Naming:**
   - ✅ Tüm field'lar İngilizce
   - ✅ Türkçe kolon adı yok

4. **Seeder Pattern:**
   - ✅ `updateOrCreate` kullanılıyor (idempotent)
   - ✅ Hata kontrolü mevcut
   - ✅ Detaylı log mesajları

---

## 📊 BEKLENEN SONUÇLAR

### Kategoriler:
- **Ana Kategoriler:** 5 adet
  - Konut
  - İşyeri
  - Arsa
  - Yazlık Kiralama
  - Turistik Tesisler

- **Alt Kategoriler:** 17 adet
  - Konut: 4 (Daire, Villa, Müstakil Ev, Dubleks)
  - İşyeri: 4 (Ofis, Dükkan, Fabrika, Depo)
  - Arsa: 3 (İmar Arsaları, Tarım Arazileri, Orman Arazileri)
  - Yazlık: 3 (Günlük, Haftalık, Aylık)
  - Turistik: 3 (Otel, Pansiyon, Tatil Köyü)

- **Yayın Tipleri:** ~28 adet
  - Her alt kategori için 1-2 yayın tipi

---

## 🔍 HATA DURUMUNDA

### JSON Dosyası Bulunamadı:
```
❌ JSON dosyası bulunamadı: /path/to/GEMINI_COMPLETE_SYSTEM_DATA.json
```
**Çözüm:** JSON dosyasının doğru konumda olduğundan emin olun.

### JSON Parse Hatası:
```
❌ JSON dosyası parse edilemedi veya categories.main_categories bulunamadı.
```
**Çözüm:** JSON dosyasının geçerli JSON formatında olduğundan emin olun.

### Model Bulunamadı:
```
⚠️ IlanKategoriYayinTipi model bulunamadı. Yayın tipleri atlanıyor.
```
**Çözüm:** Model dosyasının mevcut olduğundan emin olun.

---

## 📝 NOTLAR

1. **Idempotent Seeder:**
   - Seeder birden fazla çalıştırılabilir
   - Mevcut kayıtlar güncellenir, yeni kayıtlar eklenir
   - Duplicate kayıt oluşturmaz

2. **Context7 Uyumluluğu:**
   - Tüm field'lar Context7 standartlarına uygundur
   - Yasak pattern'ler kullanılmamıştır
   - Pre-commit hook'lar geçecektir

3. **Backward Compatibility:**
   - Mevcut kategori yapısı korunur
   - Sadece JSON'dan gelen verilerle güncellenir

---

**Son Güncelleme:** 2025-11-27  
**Versiyon:** 1.0.0  
**Context7 Compliance:** ✅ %100



