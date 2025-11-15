## Hedefler
- Projede kalan jQuery kullanımlarını kaldırıp native DOM/fetch ile standardize etmek
- `resources/js/admin/services/list-paginate.js` servisini modern hale getirerek erişilebilir, performanslı ve test edilebilir yapmak

## Aşama 1: jQuery Envanteri ve Temizlik
- Kod araması: jQuery imzaları (`$`, `jQuery`, `$.ajax`, `$.on`) için tüm kod tabanını tarama
- Kapsam: admin sayfaları, bileşenler, inline Blade scriptler
- Eylem: Bulunan kullanımları native karşılıklarıyla değiştir (fetch, addEventListener, classList, closest, querySelectorAll)
- Son kontrol: bundle ve konsol uyarılarının temizlenmesi

## Aşama 2: List Paginate Servisi Refaktörü
- Mevcut yapı analizi: `config.endpoint`, parametreler, state yönetimi (sayfa numarası, toplam, loader), DOM bağları
- Yeni tasarım:
  - API: `init(container, config)`, `load(page, query)`, `render(data)`, `bindEvents()`, `destroy()`
  - Veri çekme: `fetch` + `AbortController` (hızlı aramada iptal), JSON şeması `{data, meta: {total, per_page, current_page}}`
  - Hata/boş durum: `role="status" aria-live="polite"` ile “Sonuç yok”/hata bildirimi
  - A11y: pagination control `role="navigation" aria-label="Sayfalama"`, butonlar `aria-disabled`, aktif sayfada `aria-current="page"`
  - Performans: debounce (200–300ms) arama için, hafif DOM güncelleme (document fragment), minimal reflow
- E2E API entegrasyonu: `endpoint` üzerinde `?page=...&q=...&per_page=...` parametreleri ile test

## Aşama 3: UI Entegrasyonu
- Paginate UI bileşeni: Blade tarafında minimal markup (container, results, pagination nav)
- CSS hafif dokunuş: `focus-ring`, `hover`, `disabled` için tutarlı token’lar (Tailwind sınıfları ile)

## Aşama 4: Test ve Doğrulama
- Feature testleri: paging (ilk/son/orta sayfa), arama (debounce, iptal), hata/boş durum
- Snapshot: pagination nav ve sonuç listesi render doğrulamaları
- Performans: Navigation/Resource Timing ölçümü ve 37ms reflow uyarılarını azaltma

## Aşama 5: Uyum ve Kayıt
- Context7 ve Yalıhan Bekçi: erişilebilirlik, güvenlik (CSRF), modüler servis kurallarına uyum
- Changelog ve jQuery kaldırma raporu

Onayınızla bu planı uygulamaya başlayıp `list-paginate.js` modernizasyonu ve jQuery temizliği ile ilk aşamayı tamamlayacağım; ardından doğrulama raporunu paylaşacağım.