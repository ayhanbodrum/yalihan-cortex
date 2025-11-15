# Context7 Compliance Report

**Tarih:** 2025-11-14  
**Durum:** ⚠️ TARAMA TAMAMLANDI (Güncellendi)

## 📊 Özet
- **Toplam İhlal:** 795
- **Critical:** 559
- **High:** 236

## 🔍 İlk Bulgular (Örnekler)

- [CRITICAL] `database/migrations/2025_11_09_070721_rename_order_to_display_order_in_tables.php:33` — order → display_order kullanılmalı
- [CRITICAL] `database/migrations/2025_11_09_070721_rename_order_to_display_order_in_tables.php:38` — order → display_order kullanılmalı
- [CRITICAL] `database/migrations/2025_11_09_070721_rename_order_to_display_order_in_tables.php:41` — order → display_order kullanılmalı
- [CRITICAL] `database/migrations/2025_11_09_070721_rename_order_to_display_order_in_tables.php:69` — order → display_order kullanılmalı
- [CRITICAL] `database/migrations/2025_11_09_070721_rename_order_to_display_order_in_tables.php:72` — order → display_order kullanılmalı
- [CRITICAL] `database/migrations/2025_11_09_070721_rename_order_to_display_order_in_tables.php:97` — order → display_order kullanılmalı
- [CRITICAL] `database/migrations/2025_11_09_070721_rename_order_to_display_order_in_tables.php:121` — order → display_order kullanılmalı
- [CRITICAL] `database/migrations/2025_11_09_122119_rename_order_to_display_order_in_alt_kategori_yayin_tipi_table.php:27` — order → display_order kullanılmalı
- [CRITICAL] `database/migrations/2025_11_09_122119_rename_order_to_display_order_in_alt_kategori_yayin_tipi_table.php:32` — order → display_order kullanılmalı
- [CRITICAL] `database/migrations/2025_11_09_122119_rename_order_to_display_order_in_alt_kategori_yayin_tipi_table.php:35` — order → display_order kullanılmalı

## ✅ Önerilen Aksiyonlar
- Migration ve seeder metinlerinde `order` kullanımını gözden geçir; kod mantığı `display_order` ile uyumlu.
- Controller ve view dosyalarında hard-coded `order` referanslarını `display_order` ile değiştir.
- `crm.*` route’larını `admin.*` altına konsolide etme planıyla uyumlu tut.
- Tailwind dışı CSS class’larını temizle; Neo/Bootstrap kullanımını kaldır.

### Yapılan İyileştirmeler
- Controller validasyonlarında `order` kuralı kaldırıldı; `display_order` tek kaynak
- Admin Button, Live Search ve Form Builder Tailwind’e geçirildi; Bootstrap/neo sınıfları temizlendi
- Tarama sonuçlarında High ihlaller 257 → 244 azaldı

## 🛠️ Kaynak
- Komut: `php scripts/context7-compliance-scanner.php`