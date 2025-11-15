Tarih: 2025-11-15
Konu: API v1 → UI adapter ve Smart Calculator migrasyonu

Değişiklikler:
- Yeni adapter: resources/js/admin/services/api-adapter.js (GET/POST/PUT/DELETE, CSRF, ResponseService parse)
- Vite girişleri güncellendi: adapter input eklendi (vite.config.js)
- Smart Calculator: v1 uçlarına ve adapter’a geçirildi; history/favorites/calculate artık v1 path ve meta tüketimi ile çalışıyor

Sayfalama ve Meta:
- Calculator history: per_page=50, meta.current_page/page/total/last_page kullanımı desteklenir

Uyumluluk:
- window.ApiAdapter global atandı; public JS dosyaları adapter yoksa v1 fetch fallback ile çalışır

Sonraki Adımlar:
- Kisiler/Danismanlar/Talepler/Ilanlar liste bileşenlerinin adapter’a taşınması ve meta tabanlı sayfalama UI