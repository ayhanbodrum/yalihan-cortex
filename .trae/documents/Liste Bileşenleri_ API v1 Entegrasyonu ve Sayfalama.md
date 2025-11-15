## Amaç
- Kisiler/Danismanlar/Talepler/Ilanlar ve Calculator listeleri, tek `api-adapter.js` ile `admin/api/v1` uçlarına bağlanır.
- Sayfalama UI `meta.current_page/per_page/total/last_page` değerlerini kullanır.
- Hata, boş ve yükleme durumları standartlaştırılır; A11y ve performans testleri yapılır.

## Uygulama Adımları
### 1) Adapter Kullanımı
- Var olan fetch/jQuery çağrılarını `ApiAdapter.get/post/put/delete` ile değiştir.
- CSRF ve JSON parse adapter tarafından otomatik karşılanır.

### 2) Liste Migrasyonları
- Kisiler (Blade + JS):
  - JS: `loadKisiler(page, perPage)` → `ApiAdapter.get('/kisiler', { page, per_page: perPage })`
  - UI: tablo/grid bileşeni `data-meta` tüketir; sayfalama butonları `current_page/last_page` ile disabled.
- Danismanlar:
  - Aynı örüntü: `ApiAdapter.get('/danismanlar', { page, per_page })`
- Talepler:
  - `ApiAdapter.get('/talepler', { page, per_page })`
- İlanlar:
  - `ApiAdapter.get('/ilanlar', { page, per_page })`
- Calculator listeleri:
  - History: `ApiAdapter.get('/calculator/history', { page, per_page })` ve meta tüketimi
  - Favorites: `ApiAdapter.get('/calculator/favorites')`
  - Types: `ApiAdapter.get('/calculator/types')`

### 3) Sayfalama UI ve Durumlar
- Sayfa state: `state.page`, `state.perPage` (varsayılan 20), `state.total`, `state.lastPage`.
- Kontroller: İlk/Önceki/Sonraki/Son butonları; `aria-label` ve `aria-disabled` ekle.
- Boş durum: `role="status"` mesajı, `data.length===0` olduğunda göster.
- Yükleme durumu: `aria-busy="true"` ve skeleton/loader; azaltılmış hareket tercihi `matchMedia` ile desteklenir.

### 4) Hata İşleme ve Validasyon
- Adapter hata fırlatır; `try/catch` içinde `ValidationManager` veya toast ile tek örüntüde göster.
- Seçenek dışı durumlarda UI fallback: `message` ve `errors` alanları kullanıcıya dost bir metne dönüştürülür.

### 5) A11y ve Performans Testleri
- A11y (axe-core): list sayfalarında `listbox`, `option`, `menu`, `menuitem` rolleri doğrulanır.
- Lighthouse: TTI, LCP, CLS metrikleri gözlemlenir; regresyon yok.
- Puppeteer responsive snapshot: `375x667`, `768x1024`, `1440x900` viewport’larında tablo/grid görünümü ve sayfalama test edilir.

## Dosya Değişiklikleri (Planlanan)
- `resources/js/admin/services/api-adapter.js`: hazır (kullanılacak)
- Liste sayfalarının JS modülleri veya inline script’leri (belirlenecek): fetch/jQuery → `ApiAdapter` migrasyonu
- Blade sayfalama parçaları: `aria-*` ve durum göstergeleri
- Smart Calculator listelerinde meta tüketimi: tamamlandı (ek gözden geçirme)

## Geriye Uyumluluk
- Eski uçlar geçici olarak korunur; yeni adapter çağrıları v1’e yönlenir.
- Select2 ile uyumluluk için yalnız `trigger('change.select2')` çağrıları bırakılır.

## Changelog
- Her dosya için “ne değişti” ve “neden” özeti; Context7/Yalıhan Bekçi standartlarına uyum notu.

## Kabul Kriterleri
- Tüm listeler v1 ve adapter ile veri çeker; hatalar tek örüntüde.
- Meta tabanlı sayfalama doğru çalışır; boş/yükleme durumları görünür ve erişilebilir.
- A11y ve snapshot testleri raporlanır; kritik uyarı yok.

Onay sonrası ilgili JS ve Blade dosyalarını güncelleyip testleri çalıştırarak rapor ve changelog’u paylaşacağım.