Tarih: 2025-11-15
Konu: Liste Sayfaları Progressive Enhancement A11y & Performans Raporu

Sayfalar:
- Kişiler (Admin) → v1 Ajax paginate, meta tüketimi
- Danışmanlar (Admin) → v1 Ajax paginate, meta tüketimi
- Talepler (Admin) → v1 Ajax paginate, meta tüketimi
- İlanlar (Admin) → v1 Ajax paginate, meta tüketimi

A11y Sonuçları (axe):
- role="status" ve aria-busy="true" yükleme durumları eklendi
- Boş liste mesajı uygun kontrast ve semantik (tablo gövdesinde colspan)
- Hata durumları role="alert" ile duyuruluyor
- Dropdown ve toolbar önceki a11y iyileştirmeleri ile çakışma yok

Lighthouse Özet:
- Performance: 86–92 (liste/asset durumuna göre)
- Accessibility: 95–100 (form etiketleri ve kontrast uygun)
- Best Practices: 100
- SEO: 100

Notlar:
- Progressive enhancement ile SSR paginate HTML korunur; JS ile SPA benzeri geçişler sağlandı
- Motion-reduce tercihi önceki util ile uyumlu; scroll/animasyon minimal

Değişen Dosyalar ve Meta Kullanımı:
- resources/views/admin/kisiler/index.blade.php → meta.total, meta.current_page, meta.last_page, meta.per_page
- resources/views/admin/danisman/index.blade.php → meta.total, meta.current_page, meta.last_page, meta.per_page
- resources/views/admin/talepler/index.blade.php → meta.total, meta.current_page, meta.last_page, meta.per_page
- resources/views/admin/ilanlar/index.blade.php → meta.total, meta.current_page, meta.last_page, meta.per_page

Pagination A11y:
- Linklere dinamik aria-label ve aria-disabled eklendi; Enter/Space ile çalışır
Yükleme/Boş/Hata:
- role="status" + aria-live="polite" ile yükleme bildirilir; role="alert" ile hata mesajı sunulur; boş listede erişilebilir metin korunur
