## Amaç
- Projede kalan jQuery ve legacy kullanım noktalarını otomatik tespit edip raporlamak
- Tespit çıktısını CI/Pre-commit ile denetleyerek temizliği kademeli tamamlamak

## Yaklaşım
- Statik tarama (ripgrep) ile imza bazlı arama:
  - `$(...)`, `jQuery(...)`, `$.ajax`, `$.on`, `$.ready`, `$.fn` gibi imzalar
  - jQuery CDN/import: `jquery.min.js`, `cdnjs`, `unpkg` referansları
- Node.js analiz scripti ile çıktıların normalize edilmesi ve JSON rapora dönüştürülmesi
- İsteğe bağlı: ESLint özel kural (no-jquery) ile JS/TS dosyalarında jQuery çağrılarını işaretleme

## Uygulama Adımları
1. **Ripgrep taraması**
   - `rg -n --glob "**/*.{js,ts,blade.php}" "\$\(|jQuery\(|\$\.ajax|\$\.on|\$\.ready|jquery\.min\.js|cdnjs|unpkg"`
   - İgnorlar: `node_modules`, `vendor`, `storage`, `public/build`
2. **Node script (scanner.js)**
   - Ripgrep çıktılarını satır bazında parse edip dosya, satır, eşleşme türü (`call`, `import`, `plugin`) olarak JSON’a yaz
   - Raporu `./reports/jquery-usage.json` ve özetini `./reports/jquery-usage.md` üret
3. **ESLint kuralı (opsiyonel)**
   - `no-restricted-globals`/`no-restricted-syntax` kullanarak `jQuery`, `$` çağrılarını uyarı/hata olarak işaretle
4. **CI/Pre-commit entegrasyonu**
   - GitHub Actions/CI’da tarama scriptini koştur; eşleşme sayısı >0 ise uyarı/log üret (kademeli temizlik için fail etmek yerine raporlama)
   - Pre-commit hook: tarama çıktısı değiştiyse diff olarak geliştiriciye göster
5. **Raporlama ve Temizlik**
   - Modül bazlı listeler: admin sayfaları, bileşenler, inline Blade
   - Haftalık hedefler: en çok eşleşen dosyalardan başlayarak native refaktör

## Çıktılar
- `reports/jquery-usage.json` ve `reports/jquery-usage.md`
- CI/Pre-commit entegrasyonu ile otomatik raporlama
- Kademeli temizlik yol haritası (dosya/satır bazında)

## Riskler & Çözüm
- Blade içindeki `$` değişkenleri ile jQuery seçicilerinin karışması: dosya türüne ve bağlama göre filtreleme (Blade’de `@php` blokları dışında JS `<script>` içeriği)
- False-positive azaltımı: imza kümelerini genişletip bağlama kontrolleri eklemek

Onayınızla ripgrep + Node tabanlı tarama scriptini ekleyip CI/Pre-commit entegrasyonu ile jQuery kullanım raporunu otomatik üretmeye başlıyorum.