## Amaç
- per_page seçici mantığını tamamen `meta-info` komponentine almak ve A11y’yi tek merkezden yönetmek.
- `ListPaginate`’i data-attributes ile DOMReady sonrası otomatik başlatmak (init çağrısız).
- Vitest birimleriyle per_page önceliği, meta güncelleme ve AbortController davranışını doğrulamak.

## Değişiklikler
### 1) Meta Komponent API Genişletme
- Dosya: `resources/views/components/admin/meta-info.blade.php`
- Yeni props:
  - `title`: başlık çipi
  - `meta`: `{ total, current_page, last_page, per_page }`
  - `showPerPage` (bool): per_page seçici gösterimi
  - `perPageOptions` (array): varsayılan `[20, 50, 100]`
  - `listId` (string): ör. `kisiler`, `danismanlar`, `talepler`, `ilanlar`
  - `listEndpoint` (string): ör. `/api/admin/api/v1/kisiler`
- Çıktı/HTML:
  - Kök element: `data-list-id`, `data-list-endpoint`, opsiyonel `data-container`, `data-paginate` (gelecekte kapsam için)
  - Sayaç alanları: `#meta-status` (role=status, aria-live=polite, aria-busy), `#meta-total`, `#meta-page`
  - per_page seçici: `select[data-per-page-select="1"]` `showPerPage=true` iken render edilir

### 2) ListPaginate Auto-Init
- Dosya: `resources/js/admin/services/list-paginate.js`
- Yeni davranış:
  - DOMContentLoaded sonrası `[data-list-id][data-list-endpoint]` öğelerini bulur ve her biri için otomatik başlatır
  - Seçiciler:
    - `containerSelector`: `data-container` varsa kullan; yoksa default: tablo → `table tbody`, grid → `[data-*-grid="true"]`
    - `paginateSelector`: `data-paginate` varsa; yoksa default: `.mt-6` veya `.shadow-sm.p-4`
  - perPageKey: sabit `yalihan_admin_per_page`
  - Öncelik: URL `per_page` > localStorage > 20
  - Ajax sonrası:
    - `#meta-total`, `#meta-page`, `#meta-status` güncellenir
    - `select[data-per-page-select]` meta.per_page ile senkronize ve localStorage’a yazılır
  - AbortController: önceki istek iptal edilir
  - Debounce: tıklamalarda 100–150ms

### 3) Sayfa Scriptleri Sadeleştirme
- Dosyalar:
  - `resources/views/admin/kisiler/index.blade.php`
  - `resources/views/admin/danisman/index.blade.php`
  - `resources/views/admin/talepler/index.blade.php`
  - `resources/views/admin/ilanlar/index.blade.php`
- İşlem:
  - Meta komponenti şu biçimde kullan:
    - `<x-admin.meta-info title="Kişiler" :meta="$meta ?? []" :show-per-page="true" :per-page-options="[20,50,100]" listId="kisiler" listEndpoint="/api/admin/api/v1/kisiler" />`
  - Manuel `ListPaginate.init(...)` çağrılarını kaldır; auto-init’e bırak

### 4) Vitest Birim Testleri
- Dosya: `resources/js/__tests__/list-paginate.test.js`
- Senaryolar:
  - per_page önceliği:
    - URL `?per_page=50` → seçici ve çağrı 50; localStorage yok/20 default
    - localStorage=100, URL yok → 100
    - ikisi yok → 20
  - meta güncelleme:
    - Mock response `meta: { total: 123, current_page: 2, last_page: 7, per_page: 50 }` ile `#meta-total` ve `#meta-page` güncellenir
  - AbortController:
    - Ardışık iki `loadPage` → yalnız sonuncunun `render()` çağrısı yapılır (ilk abort edilir)
- Konfig: mevcut `vitest.config.ts` ile entegre

### 5) Rapor ve Changelog
- Dosya: `yalihan-bekci/reports/2025-11/LIST_PAGINATE_AUTO_INIT_2025-11-15.md`
  - Yeni komponent props’ları ve auto-init mantığı
  - Test senaryoları ve sonuçları
- `A11Y_PERF_LISTS_2025-11-15.md` içine not: auto-init + yeni komponent API

## Kabul Kriterleri
- Dört liste sayfası JS kapalıyken SSR paginate ve `links()` ile tam çalışır
- JS açıkken auto-init ListPaginate ile gövde ve meta güncellenir; per_page seçici listeler arası ortak
- Vitest testleri yeşil: öncelik, meta güncellemeleri ve abort davranışı doğrulanır
- A11y: role/status/aria-live/aria-disabled uygulanır; Enter/Space ile pagination çalışır

## Zamanlama
- Gün 1: Komponent ve modül auto-init, bir sayfa entegrasyon örneği
- Gün 2: Dört sayfa entegrasyonu, testler ve rapor
- Gün 3: İnce ayar ve a11y/lighthouse kısa doğrulama