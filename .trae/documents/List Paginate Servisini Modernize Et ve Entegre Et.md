## Hedef
Admin listelerinde (özellik/kategori vb.) hızlı, erişilebilir ve performanslı sayfalama/arama deneyimi sağlamak.

## Adımlar
1. Mevcut Servisi İncele
- `resources/js/admin/services/list-paginate.js` davranışını ve kullanılan sayfaları envanterle.
- Endpoint JSON şeması ve parametreler (page, q, per_page) doğrula.

2. API Tasarımı
- Metodlar: `init(container, config)`, `load(page, query)`, `render(data)`, `bindEvents()`, `destroy()`.
- Veri çekme: `fetch` + `AbortController` ile hızlı aramalarda iptal.
- Debounce: Arama inputu için 200–300ms gecikme.

3. Erişilebilirlik (WCAG)
- Boş/hata durumlarını `role="status" aria-live="polite"` ile bildir.
- Sayfalama nav: `role="navigation" aria-label="Sayfalama"`, aktif sayfada `aria-current="page"`.
- Klavye: `Tab/Enter` ile butonlar; odak yönetimi.

4. Performans
- Document fragment ile toplu DOM güncellemesi (minimal reflow).
- Hafif CSS: `focus-ring`, `hover`, `disabled` token’ları (Tailwind).

5. Entegrasyon
- Blade tarafında minimal markup (container, results, pagination nav).
- Admin listelerinde servisi `init` ederek devreye al.

6. Test
- Feature testleri: ilk/son/orta sayfa; arama (debounce, iptal); boş/hata durumları.
- Snapshot: pagination/nav ve sonuç render doğrulama.

7. Rapor
- Uygulama sonrası Navigation/Resource Timing ölçümleri ve kısa doğrulama raporu paylaş.

Onayınızla bu adımları uygulayıp her bölüm sonunda doğrulama çıktılarını ileteceğim.