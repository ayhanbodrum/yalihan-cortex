# Migration Execution Plan - 2025-11-11

**Tarih:** 2025-11-11  
**Durum:** 🔄 HAZIR - ÇALIŞTIRILMAYI BEKLİYOR  
**Toplam Migration:** 5 dosya  
**Toplam Kolon:** 14 kolon

---

## 📋 Migration Listesi

### 1. `rename_order_to_display_order_in_multiple_tables.php`

**Kapsam:** 4 tablo, 4 kolon
- `blog_categories.order` → `display_order`
- `etiketler.order` → `display_order`
- `ozellikler.order` → `display_order`
- `site_ozellikleri.order` → `display_order`

**Öncelik:** HIGH  
**Risk:** LOW (sadece kolon adı değişiyor)

---

### 2. `rename_aktif_to_status_in_multiple_tables.php`

**Kapsam:** 3 tablo, 3 kolon
- `kategori_ozellik_matrix.aktif` → `status`
- `konut_ozellik_hibrit_siralama.aktif` → `status`
- `ozellik_alt_kategorileri.aktif` → `status`

**Öncelik:** HIGH  
**Risk:** LOW (sadece kolon adı değişiyor)

---

### 3. `rename_enabled_to_status_in_multiple_tables.php`

**Kapsam:** 2 tablo, 2 kolon
- `kategori_yayin_tipi_field_dependencies.enabled` → `status`
- `yayin_tipleri.enabled` → `status`

**Öncelik:** HIGH  
**Risk:** LOW (sadece kolon adı değişiyor)

---

### 4. `rename_musteri_to_kisi_in_yazlik_tables.php`

**Kapsam:** 2 tablo, 4 kolon
- `yazlik_details.musteri_notlari` → `kisi_notlari`
- `yazlik_rezervasyonlar.musteri_adi` → `kisi_adi`
- `yazlik_rezervasyonlar.musteri_email` → `kisi_email`
- `yazlik_rezervasyonlar.musteri_telefon` → `kisi_telefon`

**Öncelik:** HIGH  
**Risk:** MEDIUM (yazlık rezervasyon sistemi aktif kullanılıyor olabilir)

---

### 5. `rename_is_active_to_status_in_ai_core_system.php`

**Kapsam:** 1 tablo, 1 kolon
- `ai_core_system.is_active` → `status`

**Öncelik:** MEDIUM  
**Risk:** LOW (AI sistem yönetimi)

---

## 🚀 Execution Steps

### Step 1: Backup (ÖNEMLİ!)

```bash
# Veritabanı yedeği al
mysqldump -u root yalihanemlak_ultra > backup_$(date +%Y%m%d_%H%M%S).sql

# Veya sadece ilgili tabloları
mysqldump -u root yalihanemlak_ultra \
  blog_categories etiketler ozellikler site_ozellikleri \
  kategori_ozellik_matrix konut_ozellik_hibrit_siralama ozellik_alt_kategorileri \
  kategori_yayin_tipi_field_dependencies yayin_tipleri \
  yazlik_details yazlik_rezervasyonlar ai_core_system \
  > backup_tables_$(date +%Y%m%d_%H%M%S).sql
```

### Step 2: Migration Durumu Kontrolü

```bash
# Migration durumunu kontrol et
php artisan migrate:status | grep "2025_11_11"

# Beklenen çıktı: 5 migration "Pending" durumunda olmalı
```

### Step 3: Migration Çalıştırma

```bash
# Tüm migration'ları çalıştır
php artisan migrate

# Veya sadece bu migration'ları
php artisan migrate --path=database/migrations/2025_11_11_103353_rename_order_to_display_order_in_multiple_tables.php
php artisan migrate --path=database/migrations/2025_11_11_103353_rename_aktif_to_status_in_multiple_tables.php
php artisan migrate --path=database/migrations/2025_11_11_103354_rename_enabled_to_status_in_multiple_tables.php
php artisan migrate --path=database/migrations/2025_11_11_103355_rename_musteri_to_kisi_in_yazlik_tables.php
php artisan migrate --path=database/migrations/2025_11_11_103355_rename_is_active_to_status_in_ai_core_system.php
```

### Step 4: Schema Doğrulama

```bash
# Her tablo için kolon kontrolü
mysql -u root -e "USE yalihanemlak_ultra; DESCRIBE blog_categories;" | grep -E "Field|display_order"
mysql -u root -e "USE yalihanemlak_ultra; DESCRIBE etiketler;" | grep -E "Field|display_order"
mysql -u root -e "USE yalihanemlak_ultra; DESCRIBE ozellikler;" | grep -E "Field|display_order"
mysql -u root -e "USE yalihanemlak_ultra; DESCRIBE site_ozellikleri;" | grep -E "Field|display_order"

mysql -u root -e "USE yalihanemlak_ultra; DESCRIBE kategori_ozellik_matrix;" | grep -E "Field|status"
mysql -u root -e "USE yalihanemlak_ultra; DESCRIBE konut_ozellik_hibrit_siralama;" | grep -E "Field|status"
mysql -u root -e "USE yalihanemlak_ultra; DESCRIBE ozellik_alt_kategorileri;" | grep -E "Field|status"

mysql -u root -e "USE yalihanemlak_ultra; DESCRIBE kategori_yayin_tipi_field_dependencies;" | grep -E "Field|status"
mysql -u root -e "USE yalihanemlak_ultra; DESCRIBE yayin_tipleri;" | grep -E "Field|status"

mysql -u root -e "USE yalihanemlak_ultra; DESCRIBE yazlik_details;" | grep -E "Field|kisi_notlari"
mysql -u root -e "USE yalihanemlak_ultra; DESCRIBE yazlik_rezervasyonlar;" | grep -E "Field|kisi_"

mysql -u root -e "USE yalihanemlak_ultra; DESCRIBE ai_core_system;" | grep -E "Field|status"
```

### Step 5: Cache Temizleme

```bash
# Cache'leri temizle
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Step 6: Test

```bash
# Test endpoint'leri veya sayfaları aç
# - Blog kategorileri sayfası
# - Etiket yönetimi sayfası
# - Özellik yönetimi sayfası
# - Yazlık rezervasyon sayfası
# - AI sistem yönetimi sayfası
```

---

## ⚠️ Dikkat Edilmesi Gerekenler

### 1. Model Güncellemeleri

Migration'dan sonra ilgili model dosyalarını güncellemek gerekebilir:

- [ ] `app/Models/BlogCategory.php` - `$fillable`, `$casts` güncelle
- [ ] `app/Models/Etiket.php` - `$fillable`, `$casts` güncelle
- [ ] `app/Models/Ozellik.php` - `$fillable`, `$casts` güncelle
- [ ] `app/Models/SiteOzellik.php` - `$fillable`, `$casts` güncelle
- [ ] `app/Models/KategoriOzellikMatrix.php` - `$fillable`, `$casts` güncelle
- [ ] `app/Models/KonutOzellikHibritSiralama.php` - `$fillable`, `$casts` güncelle
- [ ] `app/Models/OzellikAltKategorisi.php` - `$fillable`, `$casts` güncelle
- [ ] `app/Models/KategoriYayinTipiFieldDependency.php` - `$fillable`, `$casts` güncelle
- [ ] `app/Models/YayinTipi.php` - `$fillable`, `$casts` güncelle
- [ ] `app/Models/YazlikDetail.php` - `$fillable`, `$casts` güncelle
- [ ] `app/Models/YazlikRezervasyon.php` - `$fillable`, `$casts` güncelle
- [ ] `app/Models/AiCoreSystem.php` - `$fillable`, `$casts` güncelle

### 2. Controller Güncellemeleri

Controller'larda kolon adı kullanımlarını kontrol et:

- [ ] Query'lerde kolon adı kullanımları
- [ ] Validation rules'da kolon adı kullanımları
- [ ] Form request'lerde kolon adı kullanımları

### 3. View Güncellemeleri

Blade template'lerde kolon adı kullanımlarını kontrol et:

- [ ] Form field'larında kolon adı kullanımları
- [ ] Display'de kolon adı kullanımları
- [ ] JavaScript'te kolon adı kullanımları

### 4. Seeder Güncellemeleri

Seeder'larda kolon adı kullanımlarını kontrol et:

- [ ] Data array'lerinde kolon adı kullanımları
- [ ] Query'lerde kolon adı kullanımları

---

## 🔄 Rollback Planı

Eğer bir sorun olursa:

```bash
# Son migration'ı geri al
php artisan migrate:rollback --step=1

# Veya belirli migration'ı
php artisan migrate:rollback --path=database/migrations/2025_11_11_103355_rename_is_active_to_status_in_ai_core_system.php
```

---

## ✅ Success Criteria

Migration başarılı sayılır eğer:

1. ✅ Tüm migration'lar çalıştırıldı (migrate:status'te "Ran" görünüyor)
2. ✅ Eski kolonlar yok (DESCRIBE'de görünmüyor)
3. ✅ Yeni kolonlar var (DESCRIBE'de görünüyor)
4. ✅ Veri kaybı yok (row count aynı)
5. ✅ Sayfalar çalışıyor (test edildi)
6. ✅ Cache temizlendi

---

**Son Güncelleme:** 2025-11-11  
**Hazırlayan:** Context7 Migration Standards

