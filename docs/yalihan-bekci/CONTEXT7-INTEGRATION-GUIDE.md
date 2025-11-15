# Yalıhan Bekçi ve Context7 Rehberi

## 1. Genel Bakış
- **Yalıhan Bekçi**: Projede kod kalite kontrolü, standart uygulama ve raporlama yapan özelleştirilmiş AI koruyucusu.
- **Context7 Sistemi**: Tasarım (Tailwind), alan adlandırma, form/harita standartları, JavaScript ve dokümantasyonu yöneten ana standart seti.
- **Amaç**: İki sistemi senkron tutarak kod tabanını tek bir doğrulama kaynağına bağlamak.

## 2. Yalıhan Bekçi'nin Bildikleri
- `.yalihan-bekci/knowledge/*.json` dosyalarındaki öğrenilmiş kurallar (ör. `location-mahalle-id-standard`, `tailwind-transition-rule`).
- `.context7/authority.json` ve alt standart dosyalarındaki (FORM_DESIGN, TAILWIND-TRANSITION, ROUTE_NAMING, HARITA_SISTEMI) maddeler.
- `yalihan-bekci/reports/` dizinindeki güncel ve arşiv raporları.
- 2025 sonrası eklenen servisler (PropertyFeedService, Context7 Live Search, Harita araçları) ile ilgili hafıza kayıtları.

## 3. Temel Yetkinlikler
- Context7 uyumluluk taraması (yasaklı alan adları, Tailwind/dark mode/responsive eksikleri).
- TODO/FIXME analizi ve teknik borç kategorize etme (`.context7/TODO_ANALYSIS.md`).
- Dokümantasyon ve raporlama (günlük/haftalık özetler, standart ihlal raporları).
- AI destekli iyileştirme önerileri: Form standartları, Tailwind dönüşümü, Vanilla JS rehberi.

## 4. Context7 ile İş Akışı
1. Context7 standartları güncellendiğinde Bekçi hafızasına (`.yalihan-bekci/knowledge/`) karşılık gelen kayıt eklenir.
2. `php context7_final_compliance_checker.php` veya `php artisan context7:check` çıktıları raporlanır.
3. Yeni kurallar için hem `.context7` hem `.yalihan-bekci` dizinlerinde referans güncellenir.
4. Bekçi raporları `yalihan-bekci/reports/summary/` altında kısa, `archive/` altında tarih bazlı tutulur.

## 5. Önerilen Geliştirmeler
- CI pipeline'a Bekçi raporlarını ekleyip merge öncesi otomatik kontrol.
- Test kapsama ve performans metriklerini raporlara dahil etme.
- Tailwind class analizi yaparak eksik dark mode/transition’ları işaretleme.
- Modül bazlı teknik borç skorları ve risk haritaları üretme.

## 6. Sorumluluk Matrisi
| Konu | Kaynak | Bekçi Görevi |
| --- | --- | --- |
| Form standartları | `.context7/FORM_DESIGN_STANDARDS.md` | Form Blade’lerini denetleyip eksikleri raporlamak |
| Tailwind animasyonları | `.context7/TAILWIND-TRANSITION-RULE.md` | Button/link hover & transition kontrolü |
| Lokasyon sistemi | `.context7/LOCATION_MAHALLE_ID_STANDARD.md` | `mahalle_id` kullanımını, API uçlarını doğrulamak |
| Harita araçları | `.context7/HARITA_SISTEMI_STANDARDS.md` | Map modüllerinin script/ID standartlarına uygunluğu |
| Kod temizliği | `.yalihan-bekci/knowledge/*` | TODO, legacy, kopya kod ve raporlama |

## 7. Kullanım Notları
- Bekçi’den rapor isterken hangi modül/klasörde çalışacağını belirt.
- Raporlar yayıldığında arşive taşı; güncel dosyalar yalnızca `summary` klasöründe kalsın.
- Yeni servis veya refactor sonrası ilgili bilgi JSON’una `learned` kaydı ekle ki Bekçi tekrar hatırlasın.
- Standart ihlali düzeltildikten sonra, aynı raporun tekrar üretilmemesi için JSON/MD kaydını güncelle.

## 8. Otomasyon Komutu
- `scripts/bekci/generate-summary.sh` komutu, Context7 denetimi ile yarım kalmış kod analizini çalıştırıp sonuçları `yalihan-bekci/reports/summary/` altında zaman damgalı bir rapor olarak kaydeder.
- İlk kullanımda `chmod +x scripts/bekci/generate-summary.sh` komutuyla çalıştırılabilir hale getir.
- Rapor içerisinde ilgili JSON yolu belirtilir; detaylı inceleme gerekiyorsa komutta adı geçen `.json` dosyasını aç.
