# Kalan İhlal Dosyaları Analizi - 2025-11-09

## 📊 DURUM

**Toplam İhlal:** 7 dosya, 8 kullanım

---

## 🔍 DOSYA ANALİZİ

### 1. ✅ `app/Models/Etiket.php` (2 kullanım)
**Durum:** ÖZEL DURUM - `order` → `sira` mapping var
- Veritabanında: `sira` kolonu var
- Model'de: `order` → `sira` accessor/mutator var
- **Çözüm:** `orderBy('order')` → `orderBy('sira')` olmalı (accessor kullanılmaz, direkt kolon adı kullanılmalı)

### 2. ⚠️ `app/Models/FeatureAssignment.php` (1 kullanım)
**Durum:** Tablo yok
- Veritabanında: `feature_assignments` tablosu yok
- Model'de: `order` kolonu kullanılıyor
- **Çözüm:** Tablo oluşturulduğunda `display_order` kullanılmalı, şimdilik `display_order` kullan

### 3. ⚠️ `app/Models/DashboardWidget.php` (1 kullanım)
**Durum:** Tablo yok
- Veritabanında: `dashboard_widgets` tablosu yok
- Model'de: `order` kolonu kullanılıyor
- **Çözüm:** Tablo oluşturulduğunda `display_order` kullanılmalı, şimdilik `display_order` kullan

### 4. ⚠️ `app/Models/KategoriYayinTipiFieldDependency.php` (1 kullanım)
**Durum:** Tablo yok
- Veritabanında: `kategori_yayin_tipi_field_dependencies` tablosu yok
- Model'de: `order` kolonu kullanılıyor
- **Çözüm:** Tablo oluşturulduğunda `display_order` kullanılmalı, şimdilik `display_order` kullan

### 5. ✅ `app/Services/IlanDataProviderService.php` (2 kullanım)
**Durum:** `IlanKategori` modelinde `display_order` var
- **Çözüm:** `orderBy('order')` → `orderBy('display_order')` olmalı

### 6. ✅ `app/Services/SmartFieldGenerationService.php` (1 kullanım)
**Durum:** `OzellikKategori` modelinde `display_order` var
- **Çözüm:** `orderBy('order')` → `orderBy('display_order')` olmalı

---

## 🔧 DÜZELTME PLANI

### Öncelik 1: Service'ler (Kritik)
- ✅ `IlanDataProviderService.php` → `display_order` kullan
- ✅ `SmartFieldGenerationService.php` → `display_order` kullan

### Öncelik 2: Etiket Modeli (Özel Durum)
- ✅ `Etiket.php` → `orderBy('sira')` kullan (veritabanında `sira` var)

### Öncelik 3: Olmayan Tablolar (Düşük Öncelik)
- ⚠️ `FeatureAssignment.php` → `display_order` kullan (tablo yok ama gelecekte oluşturulacak)
- ⚠️ `DashboardWidget.php` → `display_order` kullan (tablo yok ama gelecekte oluşturulacak)
- ⚠️ `KategoriYayinTipiFieldDependency.php` → `display_order` kullan (tablo yok ama gelecekte oluşturulacak)

---

**Son Güncelleme:** 2025-11-09

