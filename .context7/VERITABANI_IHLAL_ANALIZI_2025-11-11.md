# Veritabanı İhlal Analizi - 2025-11-11

**Tarih:** 2025-11-11  
**Durum:** 🔍 ANALİZ TAMAMLANDI  
**Toplam İhlal:** 14 kolon

---

## 📊 İhlal Kategorileri

### 1. `order` → `display_order` (4 kolon)

| Tablo | Kolon | Tip | Durum |
|-------|-------|-----|-------|
| `blog_categories` | `order` | int | ⚠️ Migration düzeltildi, DB'de hala var |
| `etiketler` | `order` | int | ⚠️ Migration gerekli |
| `ozellikler` | `order` | int | ⚠️ Migration gerekli |
| `site_ozellikleri` | `order` | int | ⚠️ Migration gerekli |

**Öncelik:** HIGH  
**Aksiyon:** Migration oluştur ve çalıştır

---

### 2. `durum` → `status` (1 kolon)

| Tablo | Kolon | Tip | Durum |
|-------|-------|-----|-------|
| `yazlik_doluluk_durumlari` | `durum` | enum | ⚠️ Özel durum - doluluk durumu |

**Not:** Bu kolon `durum` olarak kalabilir çünkü:
- Doluluk durumu için özel bir enum (`musait`, `rezerve`, `bloke`, `bakim`, `temizlik`, `kapali`)
- `status` field'ı ile karışmaz
- Domain-specific bir terim

**Öncelik:** LOW (İsteğe bağlı)  
**Aksiyon:** İsteğe bağlı - Context7 standardına göre `status` olarak değiştirilebilir ama domain-specific olduğu için `durum` kalabilir

---

### 3. `aktif` → `status` (3 kolon)

| Tablo | Kolon | Tip | Durum |
|-------|-------|-----|-------|
| `kategori_ozellik_matrix` | `aktif` | tinyint(1) | ⚠️ Migration gerekli |
| `konut_ozellik_hibrit_siralama` | `aktif` | tinyint(1) | ⚠️ Migration gerekli |
| `ozellik_alt_kategorileri` | `aktif` | tinyint(1) | ⚠️ Migration gerekli |

**Öncelik:** HIGH  
**Aksiyon:** Migration oluştur ve çalıştır

---

### 4. `enabled` → `status` (2 kolon)

| Tablo | Kolon | Tip | Durum |
|-------|-------|-----|-------|
| `kategori_yayin_tipi_field_dependencies` | `enabled` | tinyint | ⚠️ Migration gerekli |
| `yayin_tipleri` | `enabled` | tinyint(1) | ⚠️ Migration gerekli |

**Öncelik:** HIGH  
**Aksiyon:** Migration oluştur ve çalıştır

---

### 5. `is_active` → `status` (1 kolon)

| Tablo | Kolon | Tip | Durum |
|-------|-------|-----|-------|
| `ai_core_system` | `is_active` | tinyint(1) | ⚠️ Migration gerekli |

**Öncelik:** MEDIUM  
**Aksiyon:** Migration oluştur ve çalıştır

---

### 6. `musteri_*` → `kisi_*` (4 kolon)

| Tablo | Kolon | Tip | Durum |
|-------|-------|-----|-------|
| `yazlik_details` | `musteri_notlari` | text | ⚠️ Migration gerekli |
| `yazlik_rezervasyonlar` | `musteri_adi` | varchar(255) | ⚠️ Migration gerekli |
| `yazlik_rezervasyonlar` | `musteri_email` | varchar(255) | ⚠️ Migration gerekli |
| `yazlik_rezervasyonlar` | `musteri_telefon` | varchar(50) | ⚠️ Migration gerekli |

**Öncelik:** HIGH  
**Aksiyon:** Migration oluştur ve çalıştır

---

## 🎯 Öncelik Sırası

### Phase 1: CRITICAL (Hemen düzeltilmeli)
1. `order` → `display_order` (4 kolon)
2. `aktif` → `status` (3 kolon)
3. `musteri_*` → `kisi_*` (4 kolon)

### Phase 2: HIGH (Yakında düzeltilmeli)
4. `enabled` → `status` (2 kolon)

### Phase 3: MEDIUM (Planlanmalı)
5. `is_active` → `status` (1 kolon)

### Phase 4: LOW (İsteğe bağlı)
6. `durum` → `status` (1 kolon - domain-specific, isteğe bağlı)

---

## 📝 Migration Planı

### Migration 1: `order` → `display_order`
- `blog_categories.order` → `display_order`
- `etiketler.order` → `display_order`
- `ozellikler.order` → `display_order`
- `site_ozellikleri.order` → `display_order`

### Migration 2: `aktif` → `status`
- `kategori_ozellik_matrix.aktif` → `status`
- `konut_ozellik_hibrit_siralama.aktif` → `status`
- `ozellik_alt_kategorileri.aktif` → `status`

### Migration 3: `enabled` → `status`
- `kategori_yayin_tipi_field_dependencies.enabled` → `status`
- `yayin_tipleri.enabled` → `status`

### Migration 4: `is_active` → `status`
- `ai_core_system.is_active` → `status`

### Migration 5: `musteri_*` → `kisi_*`
- `yazlik_details.musteri_notlari` → `kisi_notlari`
- `yazlik_rezervasyonlar.musteri_adi` → `kisi_adi`
- `yazlik_rezervasyonlar.musteri_email` → `kisi_email`
- `yazlik_rezervasyonlar.musteri_telefon` → `kisi_telefon`

---

## ⚠️ Dikkat Edilmesi Gerekenler

1. **Veri Kaybı:** Migration'lar sırasında veri kaybı olmamalı
2. **Index'ler:** Kolon adı değişikliklerinde index'ler güncellenmeli
3. **Foreign Key'ler:** Foreign key'ler kontrol edilmeli
4. **Model Güncellemeleri:** İlgili model'lerde `$fillable`, `$casts` güncellenmeli
5. **Controller Güncellemeleri:** Controller'larda kolon adı kullanımları güncellenmeli
6. **View Güncellemeleri:** Blade template'lerde kolon adı kullanımları güncellenmeli
7. **Seeder Güncellemeleri:** Seeder'larda kolon adı kullanımları güncellenmeli

---

## 🔍 Kontrol Edilmesi Gerekenler

- [ ] Model dosyaları (`$fillable`, `$casts`, accessors/mutators)
- [ ] Controller dosyaları (query'ler, validation rules)
- [ ] View dosyaları (Blade template'ler)
- [ ] Seeder dosyaları (data insertion)
- [ ] Service dosyaları (business logic)
- [ ] Migration dosyaları (schema definitions)
- [ ] Test dosyaları (unit/integration tests)

---

**Son Güncelleme:** 2025-11-11

