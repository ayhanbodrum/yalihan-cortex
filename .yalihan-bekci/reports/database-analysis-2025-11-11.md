# Veritabanı Analiz Raporu - 2025-11-11

**Tarih:** 2025-11-11 18:05:00  
**Veritabanı:** yalihanemlak_ultra  
**Durum:** ✅ Aktif ve Çalışıyor

---

## 🗄️ Veritabanı Bilgileri

### Bağlantı Bilgileri

```yaml
Tip: MySQL
Host: 127.0.0.1
Port: 3306
Database: yalihanemlak_ultra
Kullanıcı: root
Toplam Boyut: 3.47 MiB
Aktif Bağlantılar: 2
```

---

## 📊 Tablo İstatistikleri

### Genel Bilgiler

- **Toplam Tablo:** 65 tablo
- **Toplam Boyut:** 3.47 MiB
- **Migration Durumu:** ✅ Tüm migration'lar uygulanmış

### Ana Tablolar ve Boyutları

| Tablo | Boyut (MiB) | Açıklama |
|-------|------------|----------|
| `ilanlar` | 0.39 | Ana ilan tablosu |
| `ilan_kategorileri` | 0.13 | İlan kategorileri |
| `ai_logs` | 0.11 | AI işlem logları |
| `eslesmeler` | 0.11 | Eşleşme kayıtları |
| `kisiler` | 0.11 | Kişi/Müşteri kayıtları |
| `sites` | 0.11 | Site/Apartman kayıtları |
| `talepler` | 0.11 | Talep kayıtları |
| `anahtar_yonetimi` | 0.09 | Anahtar yönetimi |
| `iller` | 0.09 | İl kayıtları |
| `ilceler` | 0.06 | İlçe kayıtları |
| `mahalleler` | 0.05 | Mahalle kayıtları |

### İlan Sistemi Tabloları

- `ilanlar` - Ana ilan tablosu (0.39 MiB)
- `ilan_kategorileri` - Kategori hiyerarşisi (0.13 MiB)
- `ilan_kategori_yayin_tipleri` - Kategori-yayın tipi ilişkileri (0.08 MiB)
- `ilan_etiketler` - İlan etiketleri (0.05 MiB)
- `ilan_ozellikleri` - İlan özellikleri (0.05 MiB)
- `ilan_feature` - İlan-feature ilişkileri (0.03 MiB)
- `ilan_fotograflari` - İlan fotoğrafları (0.03 MiB)
- `ilan_resimleri` - İlan resimleri (0.03 MiB)
- `ilan_takvim_sync` - Takvim senkronizasyonu (0.03 MiB)

### Yazlık Sistemi Tabloları

- `yazlik_details` - Yazlık detayları (0.08 MiB)
- `yazlik_fiyatlandirma` - Sezonluk fiyatlandırma (0.03 MiB)
- `yazlik_rezervasyonlar` - Rezervasyonlar (0.03 MiB)
- `yazlik_doluluk_durumlari` - Doluluk durumları (0.06 MiB)

### Özellik Sistemi Tabloları

- `features` - Özellikler (0.08 MiB)
- `feature_categories` - Özellik kategorileri (0.06 MiB)
- `ozellikler` - Özellikler (0.08 MiB)
- `ozellik_kategorileri` - Özellik kategorileri (0.06 MiB)
- `ozellik_alt_kategorileri` - Alt kategoriler (0.03 MiB)
- `kategori_ozellik_matrix` - Kategori-özellik matrisi (0.05 MiB)
- `kategori_yayin_tipi_field_dependencies` - Field dependencies (0.02 MiB)

### Konum Sistemi Tabloları

- `iller` - İller (0.09 MiB)
- `ilceler` - İlçeler (0.06 MiB)
- `mahalleler` - Mahalleler (0.05 MiB)
- `ulkeler` - Ülkeler (0.06 MiB)

### Kullanıcı ve Yetki Sistemi

- `users` - Kullanıcılar (0.03 MiB)
- `roles` - Roller (0.02 MiB)
- `permissions` - Yetkiler (0.03 MiB)
- `model_has_roles` - Kullanıcı-rol ilişkileri (0.03 MiB)
- `model_has_permissions` - Kullanıcı-yetki ilişkileri (0.03 MiB)
- `role_has_permissions` - Rol-yetki ilişkileri (0.03 MiB)

### AI Sistemi Tabloları

- `ai_logs` - AI işlem logları (0.11 MiB)
- `ai_storage` - AI veri depolama (0.02 MiB)
- `ai_category_analytics` - Kategori analitikleri (0.05 MiB)
- `ai_core_system` - AI core sistem (0.02 MiB)
- `ai_learning_data` - Öğrenme verileri (0.02 MiB)

### Diğer Sistem Tabloları

- `settings` - Sistem ayarları (0.05 MiB)
- `site_settings` - Site ayarları (0.05 MiB)
- `projeler` - Projeler (0.08 MiB)
- `site_apartmanlar` - Site/Apartman kayıtları (0.08 MiB)
- `site_ozellikleri` - Site özellikleri (0.02 MiB)
- `talepler` - Talepler (0.11 MiB)
- `gorevler` - Görevler (0.06 MiB)
- `takim_uyeleri` - Takım üyeleri (0.06 MiB)
- `eslesmeler` - Eşleşmeler (0.11 MiB)
- `anahtar_yonetimi` - Anahtar yönetimi (0.09 MiB)

### Blog Sistemi Tabloları

- `blog_posts` - Blog yazıları (0.02 MiB)
- `blog_categories` - Blog kategorileri (0.05 MiB)
- `blog_tags` - Blog etiketleri (0.05 MiB)
- `blog_post_tags` - Yazı-etiket ilişkileri (0.05 MiB)
- `blog_comments` - Blog yorumları (0.02 MiB)

### Cache ve Monitoring

- `cache` - Cache tablosu (0.02 MiB)
- `cache_locks` - Cache kilitleri (0.02 MiB)
- `telescope_entries` - Telescope logları (0.02 MiB)
- `telescope_entries_tags` - Telescope etiketleri (0.03 MiB)
- `telescope_monitoring` - Telescope monitoring (0.02 MiB)

### Diğer Tablolar

- `yayin_tipleri` - Yayın tipleri (0.02 MiB)
- `etiketler` - Etiketler (0.06 MiB)
- `search_analytics` - Arama analitikleri (0.05 MiB)
- `design_token_usage` - Design token kullanımı (0.03 MiB)
- `error_memory` - Hata hafızası (0.02 MiB)
- `personal_access_tokens` - API token'ları (0.05 MiB)
- `migrations` - Migration kayıtları (0.02 MiB)
- `ref_sequences` - Referans numaraları (0.02 MiB)
- `booking_requests` - Rezervasyon talepleri (0.02 MiB)
- `dashboard_widgets` - Dashboard widget'ları (0.02 MiB)
- `seasons` - Sezonlar (0.02 MiB)
- `events` - Etkinlikler (0.02 MiB)
- `photos` - Fotoğraflar (0.02 MiB)
- `konut_ozellik_hibrit_siralama` - Konut özellik sıralaması (0.02 MiB)

---

## 📈 Veri İstatistikleri

### Kullanıcılar
- **Toplam:** 4 kullanıcı
- **Admin:** 2 kullanıcı
- **Danışman:** 2 kullanıcı

### İlanlar
- **Toplam:** 0 kayıt (yeni sistem)

### Kategoriler
- **Toplam:** 36 kategori
  - Seviye 0 (Ana): 5 kategori
  - Seviye 1 (Alt): 20 kategori
  - Seviye 2 (Yayın Tipi): 11 kategori

### Kişiler
- **Toplam:** 3 kayıt

### Özellikler
- **Toplam:** 46 özellik
- **Özellik Kategorileri:** 5 kategori

---

## 🎯 Ana Kategoriler

1. **Konut** (ID: 1)
2. **İşyeri** (ID: 2)
3. **Arsa** (ID: 3)
4. **Yazlık Kiralama** (ID: 4)
5. **Turistik Tesisler** (ID: 5)

---

## ✅ Context7 Uyumluluk

### Kolon İsimleri

✅ **Doğru Kullanımlar:**
- `name` (İngilizce)
- `status` (Boolean - TINYINT(1))
- `parent_id`, `seviye`, `display_order`

❌ **Eski Türkçe Kolonlar (Kullanılmıyor):**
- `kategori_adi` → `name` kullanılmalı
- `aktif` → `status` kullanılmalı
- `is_active` → `status` kullanılmalı
- `order` → `display_order` kullanılmalı

---

## 🔧 Migration Durumu

✅ **Tüm migration'lar uygulanmış:**
- Toplam migration: 134+ dosya
- Son batch: 31
- Durum: ✅ Tüm migration'lar çalıştırılmış

---

## 📋 Önemli Notlar

1. **Veritabanı:** `yalihanemlak_ultra` aktif veritabanı
2. **Test Veritabanı:** `yalihanemlak_test` kullanılmamalı (boş)
3. **Context7 Compliance:** %100 uyumlu
4. **Migration Durumu:** ✅ Tüm migration'lar uygulanmış
5. **Veri Durumu:** Yeni sistem, temiz başlangıç

---

**Durum:** ✅ Veritabanı sağlıklı ve çalışıyor  
**Son Güncelleme:** 2025-11-11

