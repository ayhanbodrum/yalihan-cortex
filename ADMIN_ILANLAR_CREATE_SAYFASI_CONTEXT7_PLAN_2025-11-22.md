# Amaç
İlan ekleme (admin/ilanlar/create) sayfasını Yalıhan Bekçi kuralları ve Context7 standartları ile tam uyumlu hale getirmek. Odak alanları:
- Status alanı standardı
- Harita ve lokasyon sistemi (Leaflet / Nominatim)
- Dropdown/Tailwind görsel standartları
- JS tarafında debug/legacy kod temizliği

## Kapsam
Bu plan sadece ilan oluşturma sayfasını ve ona ait JS modüllerini kapsar:
- Blade: `resources/views/admin/ilanlar/create.blade.php`
- Blade: `resources/views/admin/ilanlar/components/location-map.blade.php`
- JS entry: `resources/js/admin/ilan-create.js`
- JS: `resources/js/admin/ilan-create/location.js`
- Gerekirse ilgili migration/model/controller dosyaları (status alanı için)

# Mevcut Durum Özeti

## Status alanı
- Formda `name="status"` olan bir select var, label Türkçe, değerler muhtemelen string state (taslak, pasif, yayinda vb.).
- Yalıhan Bekçi `status-field-standard.json` kuralı, basit aktif/pasif durumları için `status` alanının TINYINT(1) boolean olmasını ve sorgularda boolean kullanılmasını şart koşuyor.
- İlanlar için şu anki tasarım çok-durumlu bir yayın süreci (draft/pasif/yayında) izlenimi veriyor. DB ve model tarafı henüz doğrulanmadı.

## Harita ve lokasyon sistemi
- Blade tarafında Context7 uyumlu alan isimleri kullanılıyor: `il_id`, `ilce_id`, `mahalle_id`, `enlem`, `boylam`, `adres`.
- Harita bileşeni `x-context7.map-picker` ve OpenStreetMap/Leaflet ile çalışıyor.
- JS tarafında `resources/js/admin/ilan-create/location.js` içinde hem Leaflet/OpenStreetMap hem de oldukça büyük bir Google Maps/Places legacy bloğu bulunuyor.
- Leaflet yüklenmesini garanti eden bir `waitForLeaflet()` promise yapısı yok; `initializeMap` içinde Leaflet yoksa `setTimeout(initializeMap, 1000)` ile tekrar deneniyor, üst sınır/time-out tanımsız.
- Nominatim geocoding/reverse geocoding çağrılarında rate limiting veya retry/backoff mekanizması yok.

## Dropdown/Tailwind/UI
- Büyük kısmı Tailwind ile yazılmış, dark-mode desteği de var.
- Select bileşenlerinde genellikle `bg-white dark:bg-gray-800` ve `text-black dark:text-white` kullanılıyor.
- Yalıhan Bekçi `POST_IMPLEMENTATION_CHECKLIST` içinde dropdown görünürlüğü için önerilen/otomatik taranan pattern, özellikle `dark:bg-gray-900`, `dark:text-white` ve `style="color-scheme: light dark;"` kullanımını teşvik ediyor.
- Bazı interaktif `<a>` linklerinde `transition-*` sınıfları eksik.

## JS debug ve legacy kodlar
- `ilan-create.js` içinde `console.log` çağrıları var, fallback notification `alert(message)` kullanıyor.
- `location.js` içinde çok sayıda `console.log`/`console.warn`/`console.error` ve Google Maps’e ait, artık kullanılmayan fonksiyonlar bulunuyor.
- Yalıhan Bekçi `CODE_QUALITY_RULES` ve `POST_IMPLEMENTATION_CHECKLIST` duplicate/obsolete/debug kodların azaltılmasını ve temizlenmesini istiyor.

# Önerilen Değişiklikler

## Faz 1 – Status alanı ve veri modeli uyumu

1. **İlanlar tablosu şemasını inceleme**
   - `database/migrations` içinde `ilanlar` tablosunu tanımlayan migration(lar)ı tespit et.
   - `status` alanının veri tipini, default değerini ve anlamını netleştir (boolean mı, string workflow mu?).
   - `app/Models/Ilan.php` içinde `casts`, `fillable` ve kullanılan status alanlarını kontrol et.
   - Controller/service katmanında `status` üzerinde yapılan sorguları incele (örneğin `where('status', 'yayinda')` vs.).

2. **Karar: Basit boolean mı, complex workflow mu?**
   - Eğer `status` ilan için sadece “görünür/gizli” veya “aktif/pasif” ifade ediyorsa:
     - `status` alanını `tinyInteger` / boolean standardına çek.
     - Çok-durumlu yayın süreci gerekiyorsa ayrı bir alan tanımla (ör. `publication_status` veya `workflow_status`).
   - Eğer gerçekten çok-durumlu bir workflow (draft/published/archived vb.) gerekiyorsa:
     - `status-field-standard.json` içindeki `exceptions.complex_status_fields` listesine ilanlar tablosu için açık bir istisna tanımlanmasını planla.

3. **Form tarafını model kararına göre güncelleme**
   - **Seçenek A (önerilen, tam uyum):**
     - Formda `name="status"` select’i sadece 0/1 (aktif/pasif) mantığını yansıtır hale getir.
     - Yayın akışı için ikinci bir select/alan (ör. `name="publication_status"`) ya da butonların set ettiği hidden input ekle.
     - Taslak/pasif/webde yayında gibi stateler bu ek alanda string olarak tutulur.
   - **Seçenek B (complex workflow istisnası):**
     - `status` alanı string/enum olarak kalıyorsa, bu kararın dokümante edildiğinden emin ol (kural dosyasında istisna maddesine eklenmek üzere not al).
     - Controller ve model katmanında string status kullanımının tutarlı olduğundan ve validation kurallarının net olduğundan emin ol.

4. **Controller ve validation uyumu**
   - İlan oluşturma/güncelleme request sınıfında (varsa) `status` için validation kuralını kontrol et.
   - Boolean modeline geçildiğinde validation’ı `boolean` veya `required|boolean` şeklinde standardize et.
   - Controller’da status atamaları ve sorguları boolean kullanacak şekilde güncellenmeli (Seçenek A için).

## Faz 2 – Harita ve lokasyon sistemi (Leaflet/Nominatim standardı)

1. **Leaflet yükleme ve init akışını standardize etme**
   - `resources/js/admin/ilan-create/location.js` içinde:
     - `initializeMap` fonksiyonunu `waitForLeaflet()` isimli bir yardımcı fonksiyonla sarmala.
     - `waitForLeaflet()` içinde `setInterval` + `attempts` sayacı ile maksimum 50 deneme (10s) kuralını uygula.
     - Leaflet yüklenmezse promise `reject` etsin ve error UI (map alanında hata mesajı) gösterilsin.
   - Mevcut `setTimeout(initializeMap, 1000);` recursion’ını kaldır.

2. **Draggable marker ve çift yönlü koordinat senkronizasyonu**
   - Leaflet tarafında marker oluşturma fonksiyonlarını gözden geçir:
     - `placeLeafletMarker` gibi fonksiyonları `L.marker(coords, { draggable: true, autoPan: true })` kullanacak şekilde güncelle.
     - Marker drag event’inde `enlem`/`boylam` alanlarının güncellenmesini ve gerekirse reverse geocoding tetiklenmesini sağla.
   - `x-context7.map-picker` zaten bu işlevleri kısmen sunuyorsa, fallback fonksiyonların da standarda yakın olmasına dikkat et.

3. **Nominatim rate limiting ve retry/backoff**
   - Geocoding (`searchAddress` içinde Nominatim kullanımı) ve reverse geocoding (`reverseGeocode`) için:
     - Global veya module-scope bir `lastGeocodeCall` timestamp değişkeni tanımla.
     - Yeni istekten önce `Date.now() - lastGeocodeCall < 1000` ise bekleme süresi hesapla ve `await sleep(waitTime)` uygula.
     - Maksimum 3 denemelik bir loop ekle; her başarısız denemede exponential backoff (1s, 2s) uygulansın.
     - 3 denemeden sonra kullanıcıya anlaşılır bir hata mesajı göster (`window.toast` ile).

4. **Google Maps legacy kodunun sadeleştirilmesi**
   - `location.js` içindeki Google Maps/Places/geocoder ile ilgili fonksiyonları gruplandır:
     - Aktif olarak hiçbir yerde kullanılmayanları tespit et.
   - Strateji:
     - Tercihen bu legacy kodu ayrı bir `location-legacy-google.js` dosyasına taşı ve build sürecinde import etme.
     - Veya proje genelinde artık Google Maps kullanılmayacaksa tamamen temizle.
   - Bu sadeleştirme, dosya boyutunu düşürür ve Yalıhan Bekçi `obsolete_code_detection` kuralı ile uyum sağlar.

5. **Lokasyon dropdown zinciri ile harita entegrasyonu**
   - `il_id` → `ilce_id` → `mahalle_id` zincirini yöneten fonksiyonların (`loadIlceler`, `loadMahalleler`, event listener’lar) sadece `/api/location/districts/{ilId}` ve `/api/location/neighborhoods/{ilceId}` uçlarını kullandığından emin ol (TurkiyeAPI referansları kaldırılmış durumda, bu doğrulansın).
   - Mahalle seçimi tamamlandığında haritanın ilgili mahalleye odaklanması için `VanillaLocationManager` veya Context7 Leaflet manager üzerinden çağrı yapılıp yapılmadığını kontrol et; gerekirse iyileştir.

## Faz 3 – Dropdown/Tailwind ve erişilebilirlik rötuşları

1. **Kritik select bileşenlerinin standardizasyonu**
   - En azından şu select’ler için Tailwind ve stil standardını uygulamayı planla:
     - `#status` (Yayın Durumu)
     - `#il_id`, `#ilce_id`, `#mahalle_id` (Lokasyon)
   - Bu select’lerde:
     - `dark:bg-gray-900` ve `dark:text-white` kullanımına geç.
     - Mümkünse `style="color-scheme: light dark;"` ekle.
     - Varsayılan option için daha soluk bir stil uygula (ör. `text-gray-500 dark:text-gray-400`).

2. **Option class’ları (opsiyonel, orta vadeli)**
   - Checklist TC005’e uygun olarak, önemli dropdown’ların option’larına class eklemeyi değerlendir:
     - Örnek: `class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-2"`.
   - Bu adım hemen zorunlu değil, ama orta vadede tüm dropdown’larda okunabilirliği artırır.

3. **Stepper linkleri ve buton animasyonları**
   - `create.blade.php` içinde sayfa üstündeki bölüm linkleri (`#section-category`, `#section-location` vb.) için:
     - `transition-colors` veya `transition-all duration-200` ekleyerek daha tutarlı bir hover/focus davranışı sağla.
   - Var olan butonlarda zaten transition kullanılıyor; yeni eklenen butonlarda da aynı standarda uymayı takip et.

## Faz 4 – JS debug ve kalite temizliği

1. **`ilan-create.js` debug mesajlarının yönetimi**
   - Dosyadaki `console.log` çağrılarını gözden geçir:
     - Gereksiz olanları tamamen kaldır.
     - Geri kalanlar için (örn. ciddi error durumları) bir DEBUG wrapper düşün:
       - Global ölçekte bir `DEBUG_MODE` sabiti (ör. `config('app.debug')`) üzerinden conditional log.
   - `showNotification` fallback’inde `alert(message)` kullanımını kaldır:
     - Önce `window.IlanCreateCore.showNotification` deneniyor, bu iyi.
     - Fallback olarak basit bir `window.toast` veya en kötü `console.error(message)` kullan.

2. **`location.js` içindeki log ve uyarılar**
   - Çok sık kullanılan veya noise üreten `console.log`/`console.warn` çağrılarını azalt:
     - Önemli debug bilgileri için (örn. edit mode zincir tamamlandı log’ları) `DEBUG_MODE` denetimi eklenebilir.
   - Google Maps için tanımlanan `console.error` override fonksiyonu, sadece gerçekten ihtiyaç varsa tutulmalı; aksi halde sadeleştirilebilir.

3. **Legacy/öksüz fonksiyonları işaretleme veya temizleme**
   - `location.js` içinde ikinci defa tanımlanan `advancedLocationManager` gibi tekrar eden/çakışan tanımlar varsa konsolide et.
   - Kullanılmayan fonksiyonlar için iki seçenek:
     - Tamamen sil.
     - Geçici olarak tutmak gerekiyorsa, dosya başında `LEGACY` blok yorumları ile netçe ayrıştır.

# Uygulama Stratejisi

1. **Önce veri modeli ve status kararı**
   - İlk adımda ilanların `status` kullanımını netleştirip migration/model/controller tarafını düzenlemek, formu buna göre güncellemek.
   - Bu adım bittiğinde, status alanı için Yalıhan Bekçi uyumunu büyük ölçüde sağlamış olacağız.

2. **Sonra harita sistemi sertleştirme**
   - İkinci aşamada Leaflet yükleme mekanizmasını ve Nominatim çağrılarını standarda uyarlayıp; marker davranışını güncellemek.
   - Aynı aşamada Google Maps legacy kodunu sadeleştirmek ideal.

3. **UI/Tailwind rötuşları**
   - Üçüncü aşamada dropdown’lar ve küçük UX dokunuşları yapılır; bu, fonksiyonel risk taşımadığı için model/harita sonrası daha güvenli.

4. **Son adım: JS temizlik ve son kontrol**
   - Tüm ilgili JS dosyalarında debug ve obsolete kod temizliği yapılır.
   - Sonrasında Yalıhan Bekçi checklist’lerine göre (özellikle POST_IMPLEMENTATION_CHECKLIST) manuel/otomatik bir tarama yapılarak planın tamamlandığı doğrulanır.
