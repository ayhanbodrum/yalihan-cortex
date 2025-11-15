Tarih: 2025-11-15
Konu: MetaInfo + ListPaginate Auto-Init ve Testler

Komponent API:
- `title`, `meta{ total, current_page, last_page, per_page }`
- `showPerPage` (bool), `perPageOptions` (default `[20,50,100]`)
- `listId`, `listEndpoint` (data attributes: `data-list-id`, `data-list-endpoint`)

Auto-Init Mantığı:
- DOMReady sonrası `data-list-id` ve `data-list-endpoint` bulunan meta-info bileşenleri otomatik başlatılır
- Container/paginate seçimi: tablo → `table tbody`, grid → `[data-<listId>-grid="true"]`, paginate → `.mt-6`/`.shadow-sm.p-4`
- per_page öncelik: URL > localStorage(`yalihan_admin_per_page`) > 20
- AbortController ve 100–150ms debounce uygulanır

Testler (Vitest): `resources/js/__tests__/list-paginate.test.js`
- per_page önceliği, meta güncelleme ve abort davranışı doğrulandı

Not:
- SSR paginate ve `links()` JS kapalıyken aynen çalışır