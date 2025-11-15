## Amaç
- Yapılan meta-info + ListPaginate auto-init mimarisini Yalıhan Bekçi’ye öğretip dokümante etmek.
- Test, CI ve kalite kontrollerini yaygınlaştırmak.
- Render şablonlarını merkezi hale getirerek geliştirmeye devam etmek.

## Öğrenme ve Dokümantasyon
- Rapor konsolidasyonu: Tek ana rapor altında (LIST_PAGINATE_AUTO_INIT) komponent API, data-attributes, auto-init akışı, per_page önceliği.
- Kısa kullanım kılavuzu: Blade kullanım örnekleri (title/meta/listId/listEndpoint) ve SSR uyumluluk notları.
- A11y rehberi: role/status/aria-live/aria-busy, pagination aria-label/disabled ve Enter/Space desteği.

## Geliştirme – Altyapı Yaygınlaştırma
- Render tipleri: `table` ve `cards` için merkezi şablonlar; talepler grid kartlarını standartlaştır.
- Data-attributes genişletme: `data-container`, `data-paginate` override desteği (özel layoutlar için).
- Abort + debounce iyileştirmeleri: 150ms sabit, tıklama yarışlarını engelle.
- Error UI standardı: role="alert" mesajını tek stil ile göster; toast/inline varyantlar.

## Test ve CI
- Vitest kapsamı: per_page önceliği, meta güncellemeleri ve abort davranışı senaryolarını genişlet.
- Axe/Lighthouse komutları: npm script olarak ekle; liste sayfaları için otomatik rapor üret.
- Pipeline: PHP Feature + JS Unit + A11y + Lighthouse aşamalı job.

## Yaygınlaştırma ve Uygulama
- Kişiler/Danışmanlar/Talepler/İlanlar dışında kalan liste sayfalarına (raporlar, portföy, CRM alt listeleri) aynı komponent ve auto-init modeli.
- Select2 tetik uyumluluğu korunur; jQuery kullanılmaz.

## Kabul Kriterleri
- Tüm liste sayfaları SSR kapalı-JS açık senaryoda auto-init ile çalışır.
- per_page ortak tercih tek anahtarla senkron; URL her zaman öncelikli.
- Vitest/Axe/Lighthouse komutları çalışır ve raporlar oluşur.

Onayla, dokümantasyon ve CI entegrasyonlarını ekleyip render şablonlarını merkezi hale getirerek geliştirmeye devam edeyim.