## Amaç
- Tüm liste UI bileşenlerini `admin/api/v1` uçlarına taşımak ve tek bir `api-adapter.js` üzerinden standart JSON (`success/message/data/meta/errors`) ile çalıştırmak.
- Sayfalama, boş durum ve yükleme durumlarını `meta` kullanarak tutarlı hale getirmek.
- A11y ve performans doğrulaması (axe + lighthouse + Puppeteer responsive snapshot).

## Çıktılar
- `resources/js/admin/services/api-adapter.js` (get/post/put/delete)
- Güncellenmiş liste bileşenleri (kisiler, danismanlar, talepler, ilanlar, calculator/history, favorites, types)
- Çalışan sayfalama (`meta.current_page`, `meta.per_page`, `meta.total`, `meta.last_page`)
- A11y ve snapshot raporu
- Changelog (dosya ve değişiklik özeti)

## Kurallar
- Context7 ve Yalıhan Bekçi standartlarına uyum
- jQuery yok; yalnız Select2 `trigger('change.select2')` uyumluluğu
- ResponseService formatı zorunlu (`success/message/data/meta/errors/timestamp`)
- Geriye uyumluluk: Eski UI adaptörleri geçici olarak korunacak

## Adım 1: UI API Adapter
- Dosya: `resources/js/admin/services/api-adapter.js`
- Sözleşme:
  - Base URL: `'/api/admin/api/v1'`
  - CSRF: `meta[name="csrf-token"]` → `X-CSRF-TOKEN`
  - Metodlar: `get(path, params)`, `post(path, body)`, `put(path, body)`, `delete(path, body)`
  - Otomatik JSON parse ve ResponseService şeması çıkarımı: `{ status, message, data, errors, meta, timestamp }`
  - Hata işleme: `success === false` → hata fırlat; `errors` varsa geçir
- Örnek kullanım:
  - `const res = await api.get('/kisiler', { per_page: 20, page: 1 })`
  - `if (!res.status) { showError(res.message) } else { render(res.data, res.meta) }`

## Adım 2: Liste Bileşen Migrasyonu
- Kapsam:
  - Kisiler: `GET /kisiler`
  - Danismanlar: `GET /danismanlar`
  - Talepler: `GET /talepler`
  - İlanlar: `GET /ilanlar`
  - Calculator (v1): `GET /calculator/history`, `GET /calculator/favorites`, `GET /calculator/types`
- İşlem:
  - Eski fetch/jQuery çağrılarını kaldır; `api-adapter` ile yeniden yaz
  - `per_page` ve `page` query paramlarını `api-adapter` üzerinden gönder
  - ResponseService formatından `data` ve `meta` ile UI durumu güncelle
- Geriye uyumluluk:
  - Eski uçlar çağrıları adapter içinde `v0`/`legacy` path kontrolü ile geçici yönlendirme (gerekirse)

## Adım 3: Sayfalama Mantığı
- `meta.current_page` → aktif sayfa
- `meta.per_page` → sayfa başına
- `meta.total`, `meta.last_page` → toplam ve son sayfa
- UI:
  - `«`, `‹`, `›`, `»` kontrol butonları; disabled state `current_page === 1 || current_page === last_page`
  - Boş durum: `data.length === 0` → boş mesaj + erişilebilir `role="status"`
  - Yükleme durumu: `aria-busy="true"` ve skeleton/loader

## Adım 4: Hata İşleme ve Standardizasyon
- Hata: `success=false` veya HTTP ≥400 → `ValidationManager` üzerinden tek örüntüde uyarı
- Form ve liste sayfalarında toast/inline hata bileşenleri aynı pattern
- Select2 uyumluluğu: yalnız `trigger('change.select2')` çağrıları korunur

## Adım 5: A11y ve Performans Testleri
- A11y:
  - axe-core ile sayfa analizi; rolleri kontrol: `listbox`, `option`, `menu`, `menuitem`
  - Dropdown ve pagination butonlarında `aria-label`, `aria-disabled`
- Performans:
  - Lighthouse: TTI, CLS, LCP ölçümü
  - Puppeteer responsive snapshot: `375x667`, `768x1024`, `1440x900`
  - Motion-reduce denetimi: `matchMedia('(prefers-reduced-motion: reduce)')`

## Changelog
- Etkilenen dosyalar listesi: adapter, liste bileşen JS/Blade, pagination UI, hata bileşenleri
- Her değişiklik için kısa özet: neden/sonuç ve uyumluluk notu

## Kabul Kriterleri
- Tüm liste sayfaları v1 ile çalışır ve `meta` tabanlı sayfalama doğru
- Hata yollarında ResponseService formatı UI’da tek örüntüde gösterilir
- axe/lighthouse uyarıları minimize; snapshotlar üretilir
- Geriye uyumluluk: Eski giriş noktaları bozulmadan çalışır

## Zamanlama
- Gün 1: Adapter ve kisiler/danismanlar migrasyonu
- Gün 2: talepler/ilanlar + calculator seti
- Gün 3: A11y & performans testleri, changelog ve son düzenlemeler

Onayın sonrası dosyaları güncelleyip liste komponentlerini `api-adapter.js` üzerinden v1 uçlarına taşıyacağım; sayfalama ve a11y testleriyle doğrulayıp rapor/Changelog çıkaracağım.