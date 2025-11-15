Tarih: 2025-11-15
Kapsam: Admin Panel UI ve API Standardizasyonu güncellemeleri

Tamamlanan UI Çalışmaları:
- jQuery temizliği: location-helper.js, location-map-helper.js, address-select.js, modules/ilan-form.js → vanilla JS + fetch
- Leaflet.draw: toolbar ARIA ve klavye erişimi, focus-visible; CSP uyumu korunur
- Validasyon: ValidationManager global erişim ve ilan create form entegrasyonu
- Dropdown a11y: admin ve neo bileşenlerinde role/ARIA, Escape ve ok tuşları gezinmesi

Test Altyapısı:
- Vitest konfigürasyonu ve birim testleri (debounce, motion-reduce)

API Standardizasyonu (Başlatıldı):
- routes/api-admin.php içinde v1 grup eklendi: `admin/api/v1`
- Konum servisleri: `GET /location/ilceler`, `GET /location/mahalleler`
- Arama: `GET /search/live`
- Özellikler: `GET /features`, `GET /features/category/{categoryId}`
- Geriye uyumluluk: mevcut uçlar korunur; v1 altında isimlendirme ve sürümleme sağlandı

Genişletilen v1 Grupları:
- AI: title/description/price/health, field-dependency (matrix/update/suggest/auto-fill/smart-calculate/get-fields)
- AI Image: analyze/generate-tags/analyze-quality/analyze-batch
- Property Features: suggestions/smart-suggestions/validate
- Çevre Analizi: analyze/smart-recommendations/calculate-distance/search-poi
- Photos: upload/show/update/destroy/bulk-delete
- Fields: by-category/render
- Smart Categories: default-types/compatible-types
- Calculator: calculate/history/favorites CRUD/tax-rates/commission-rates/types
- Sites: search/show/store

Response Standardizasyonu:
- Closure tabanlı uçlar `ResponseService` ile standardize edildi (success/error/unauthorized), eski `response()->json` kaldırıldı.

Notlar:
- CSRF taşıma `window.__csrfToken`, Select2 tetiklemeleri uyumlu
- Validasyon ve a11y örüntüleri modül geneline yayılacak; sonraki sprintte raporlanacak
