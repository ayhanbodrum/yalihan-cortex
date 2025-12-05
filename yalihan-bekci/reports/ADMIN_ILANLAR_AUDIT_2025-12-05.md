# Admin İlanlar Sayfası İnceleme ve Güvenlik/PERF Raporu (2025-12-05)

## Özet
- Yönetim rotaları panel düzeyinde kimlik ve yetki kontrolü ile korunuyor (`routes/admin.php:17`).
- İlan modülünde hassas aksiyonlar için yetki denetimleri rota seviyesinde güçlendirildi.
- Listeleme ve filtre uçlarında dinamik `per_page` desteği eklendi (20/50/100).
- Public AI özellik route’ları `web` ve `ai.rate.limit` ile sınırlandı; draft ve status/bulk uçlarına throttling getirildi.

## Mimari ve Rotalar
- Ana admin grubu: `routes/admin.php:17` → `web + auth + verified + can:view-admin-panel`.
- İlan resource ve ek aksiyonlar:
  - Resource: `routes/admin.php:235`.
  - Owner private: `routes/admin.php:238` → `middleware('can:viewPrivateListingData,ilan')`.
  - Portal IDs: `routes/admin.php:239` → `middleware('can:edit-ilanlar')`.
  - Toggle status: `routes/admin.php:274` → `middleware('can:edit-ilanlar')`.
  - Update status: `routes/admin.php:311` → `middleware('can:edit-ilanlar')`.
  - Bulk update/delete: `routes/admin.php:312`–`313` → `middleware('can:manage-ilanlar')`.

## Controller İncelemesi
- Listeleme: `app/Http/Controllers/Admin/IlanController.php:48`.
- Paginasyon per-page desteği:
  - Index: `app/Http/Controllers/Admin/IlanController.php:287` → `per_page` paramı ile 20/50/100.
  - Filter (AJAX): `app/Http/Controllers/Admin/IlanController.php:1457` → aynı destek.
- Filtreler ve sıralama haritası: `app/Http/Controllers/Admin/IlanController.php:119`–`200`.
- Canlı arama: `app/Http/Controllers/Admin/IlanController.php:1483`.

## Blade/UX
- Liste ve kart görünümleri: `resources/views/admin/ilanlar/index.blade.php`.
- Per-page UI: meta bileşeni ve localStorage entegrasyonu (`resources/views/admin/ilanlar/index.blade.php:1026`–`1091`).
- Filtre formu: debounce ve AJAX (`resources/views/admin/ilanlar/index.blade.php:133`–`143`, `660`–`781`).

## Güvenlik
- Policy: `app/Policies/IlanPolicy.php:11` (ownerPrivate).
- Gate’ler: `app/Providers/AuthServiceProvider.php:66`–`80` (`manage-ilanlar`, `edit-ilanlar`).
- Rota seviyesinde `can:*` ile bağlama tamamlandı (yukarıdaki rota satırları).

## Performans
- Eager loading ve seçili kolonlar: `app/Http/Controllers/Admin/IlanController.php:203`–`245`.
- Paginasyon parametresi ile sayfa başına kayıt sayısı kontrolü (20/50/100).
- Öneri: `status`, `kategori_id`, `il_id`, `ilce_id`, `fiyat` alanlarına indeksler.

## Yapılan İyileştirmeler
1. Hassas ilan aksiyonlarına yetki middleware eklendi (owner-private, portal-ids, status, bulk).
2. `per_page` desteği index ve filter uçlarına eklendi; UI ile senkron.
3. Public AI route’ları rate limit altına alındı; legacy draft ve status/bulk uçlarında `throttle` eklendi.

## Sonraki Adımlar
- İndeks migration’ları: yoğun filtrelenen alanlara tekil/composite indeks.
- Kolon görünürlüğü ve sıralama seçenekleri (UI/UX geliştirme).
- Büyük toplu işlemler için queue ve ilerleme izleme.
