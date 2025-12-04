# İlan ↔ CRM Kişi Entegrasyonu Standartları (2025-11-22)

Bu doküman, **ilan ekleme / düzenleme** akışında kullanılan kişi alanlarının (İlan Sahibi, İlgili Kişi, Danışman) **CRM sistemi ve Context7 standartlarıyla tam uyumlu** olacak şekilde nasıl tasarlanması gerektiğini özetler.

Odağımız:
- `ilanlar` tablosundaki kişi alanları (`ilan_sahibi_id`, `ilgili_kisi_id`, `danisman_id`)
- `kisiler` tablosu ve CRM kişi modeli
- İlan oluşturma formu (`admin/ilanlar/create`)
- Context7 Live Search kişi seçimi + "Yoksa Ekle" modal entegrasyonu

---

## 1. Veri Modeli ve İlişkiler

### 1.1. İlanlar tablosu (`ilanlar`)

Kaynak: `docs/technical/database/schemas/ilanlar-schema.md`

Zorunlu kişi alanları:
- `ilan_sahibi_id` → `kisiler.id`
  - FK: `ON DELETE SET NULL`
  - Index: `ilanlar_ilan_sahibi_id_index`
- `danisman_id` → `users.id`
  - FK: `ON DELETE CASCADE`
  - Index: `ilanlar_user_id_index`

Önerilen ek alan:
- `ilgili_kisi_id` → `kisiler.id`
  - İlgili kişi (aracı, avukat vb.)
  - **Şema tarafında zaten varsa** FK `ON DELETE SET NULL` ile tanımlanmalı.

### 1.2. CRM kişiler tablosu (`kisiler`)

Kaynak: `docs/technical/database/schemas/kisiler-schema.md` + Context7 ek alanlar

Temel kolonlar:
- `id` (PK)
- `ad`, `soyad`
- `telefon`, `email`
- `adres`, `notlar`
- `status` (enum: `Aktif`, `Pasif`, `Potansiyel`, `Müşteri`)
- `danisman_id` → `users.id`

Context7 ek kolonlar (uygulamada mevcut):
- `kisi_tipi` (örn: `Müşteri`, `Ev Sahibi`, `Alıcı`, `Kiracı`, `Satıcı`, `Yatırımcı`, `Danışman`)
- Lokasyon alanları: `il_id`, `ilce_id`, `mahalle_id`

### 1.3. Eloquent modeller

#### `App\Models\Ilan`

- İlişki alanları:
  - `ilan_sahibi_id`, `ilgili_kisi_id`, `danisman_id` → `$fillable` içinde.
- Örnek ilişki tasarımı (özet):
  - `ilanSahibi(): belongsTo(Kisi::class, 'ilan_sahibi_id')`
  - `ilgiliKisi(): belongsTo(Kisi::class, 'ilgili_kisi_id')`
  - `danisman(): belongsTo(User::class, 'danisman_id')`

#### `App\Models\Kisi` (ana CRM modeli)

- `fillable` içinde en az:
  - `ad`, `soyad`, `telefon`, `email`, `notlar`
  - `status`, `kisi_tipi`, `danisman_id`
  - `ulke_id`, `il_id`, `ilce_id`, `mahalle_id`, `adres`
- Yardımcılar:
  - `getTamAdAttribute()` → "Ad Soyad"
  - `isActive()` → `status` alanını boolean olarak normalize eder.

#### `App\Modules\Crm\Models\Kisi`

- Modüler CRM tarafında da `kisiler` tablosuna bağlı, daha sınırlı alan setiyle.
- Bu model **iç CRM ekranları ve API’ler** için kullanılır; ilan/portal tarafında ana referans `App\Models\Kisi` olmalıdır.

---

## 2. İlan Formunda Kişi Alanları (Create/Edit)

Kaynak: `resources/views/admin/ilanlar/create-wizard.blade.php` + `partials/stable/_kisi-secimi.blade.php`

### 2.1. Standart 3 alan

1. **İlan Sahibi**  
   - Hidden: `name="ilan_sahibi_id"`  
   - Live search: `data-search-type="kisiler"` (CRM kişi araması)  
   - Zorunlu (`required`).

2. **İlgili Kişi** (opsiyonel)  
   - Hidden: `name="ilgili_kisi_id"`  
   - Live search: `data-search-type="kisiler"`  
   - Opsiyonel; boş kalabilir.

3. **Danışman**  
   - Hidden: `name="danisman_id"`  
   - Live search: `data-search-type="users"`, endpoint: `/api/admin/users/search`  
   - Zorunlu; danışman seçilmezse backend `Auth::id()` ile doldurulabilir.

### 2.2. Form tarafında dikkat edilmesi gerekenler

- Üç alanın da HTML yapısı **aynı şablonu** izlemeli:
  - Dış kapsayıcı: `.context7-live-search`
  - Hidden input: `{field}_id`
  - Text input: `{field}_search`
  - Sonuç container: `.context7-search-results`
  - "Listede yoksa yeni kişi ekle" butonu (kisiler için)
- Dark mode / Tailwind:
  - Tüm input ve kapsayıcılarda `dark:bg-gray-800`, `dark:text-white`, `transition-all duration-200` kullanılmalı.
- Label metinleri Context7 kurallarıyla uyumlu:
  - "İlan Sahibi", "İlgili Kişi", "Danışman"; `musteri` terimi **kullanılmamalı**.

---

## 3. Backend Validasyon ve Kaydetme Kuralları

Kaynak: `App\Http\Controllers\Admin\IlanController::store()`

### 3.1. Validasyon

İlan kaydında kişi alanları için önerilen kurallar:

```php
'ilan_sahibi_id' => 'required|exists:kisiler,id',
'ilgili_kisi_id' => 'nullable|exists:kisiler,id',
'danisman_id'    => 'nullable|exists:users,id', // Boşsa Auth::id() atanabilir
```

- `ilan_sahibi_id` **zorunlu**, doğrudan CRM `kisiler` tablosuna bağlı.
- `ilgili_kisi_id` opsiyonel ama girilmişse mutlaka `kisiler.id` olmalı.
- `danisman_id` boş bırakılabilir; boşsa controller içinde `Auth::id()` fallback olarak atanmalıdır.

### 3.2. Kaydetme (Ilan::create)

Kişi alanları mutlaka `create()` içinde set edilmelidir:

```php
$ilan = Ilan::create([
    // ... diğer alanlar ...
    'ilan_sahibi_id' => $request->ilan_sahibi_id,
    'ilgili_kisi_id' => $request->ilgili_kisi_id,   // ÖNEMLİ: mutlaka eklenmeli
    'danisman_id'    => $request->danisman_id ?? Auth::id(),
]);
```

> Not: Mevcut kodda `ilan_sahibi_id` ve `danisman_id` set ediliyor ancak `ilgili_kisi_id` bazı yerlerde gözden kaçmış olabilir. Bu alanın hem validasyon hem de `create()` aşamasında kullanıldığından emin olmak gerekiyor.

---

## 4. Context7 Live Search Standardı

Kaynak: `public/js/context7-live-search-simple.js` ve `yalihan-bekci/knowledge/kisi-bilgileri-standardization-2025-10-23.json`

### 4.1. Kişi arama (kisiler)

- Endpoint: `GET /api/kisiler/search?q={query}&limit={n}`
- Uygulama: `App\Http\Controllers\Api\KisiController::search()` → `App\Modules\Crm\Services\KisiService::search()`
- Dönüş formatı (Context7 standardı):
  - `success: bool`
  - `data: array<{
      id, ad, soyad, tam_ad, telefon, email, kisi_tipi, text
    }>`
  - `count`, `query`, `source: 'kisiler'`

### 4.2. Danışman arama (users)

- Endpoint: `GET /api/admin/users/search?q={query}&limit={n}&role=danisman`
- Uygulama: `App\Http\Controllers\Api\UserController::search()`
- Filtreler:
  - `whereHas('roles', name='danisman')`
  - `where('status', 1)` → sadece aktif kullanıcılar
- Dönüş formatı:
  - `data: [{ id, name, email, text, kisi_tipi: 'Sistem Danışmanı' }]`

### 4.3. Frontend davranışı

- Debounce: **300ms**
- Minimum karakter: **2**
- Maksimum sonuç: default 20, data attribute ile override edilebilir.
- Seçim sonrası:
  - Hidden input → `id`
  - Text input → gösterim metni (`text` veya `tam_ad - telefon`)
  - Change event’leri tetiklenerek Alpine/validation ile entegre olur.

> Öneri: `create.blade.php` içindeki eski/inline live search implementasyonu kaldırılıp sadece `context7-live-search-simple.js` kullanılmalı; böylece tek bir standarda bağlı kalınır.

---

## 5. "Yoksa Ekle" Modali ve Field İsimleri

Kaynak: `resources/views/admin/ilanlar/modals/_kisi-ekle.blade.php`, `Api\KisiController::store`, `yalihan-bekci/knowledge/kisiler-form-standardization-2025-10-23.json`

### 5.1. Doğru alan isimleri

API tarafında kişi oluşturma validasyonu:

```php
'ad'        => 'required|string|max:255',
'soyad'     => 'required|string|max:255',
'telefon'   => 'required|string|max:50',
'email'     => 'nullable|email|max:255',
'kisi_tipi' => 'required|in:Ev Sahibi,Satıcı,Alıcı,Kiracı,Yatırımcı,Müşteri',
'status'    => 'nullable|in:Aktif,Pasif',
'notlar'    => 'nullable|string',
```

**Modal formunun da birebir bu isimlerle çalışması gerekir:**
- `name="kisi_tipi"` (ESKİ `musteri_tipi` **kullanılmamalı**)  
- `name="status"` değerleri `Aktif` / `Pasif`

### 5.2. Terminoloji kuralları

Authority kuralları:
- `musteri_terminology`: "FORBIDDEN - PERMANENT"  
  → Form label ve alan isimlerinde `musteri` kökünü kullanmaktan kaçınılmalı.
- Temel alan/label önerileri:
  - `Kişi Tipi` (`kisi_tipi`)
  - Değerler: `Müşteri`, `Ev Sahibi`, `Alıcı`, `Kiracı`, `Satıcı`, `Yatırımcı`, `Danışman`, `Tedarikçi` vb.

### 5.3. Modal → Form entegrasyonu

"Listede yoksa yeni kişi ekle" butonuna basıldığında beklenen akış:

1. `_kisi-ekle` modalı açılır (`add_person_modal`).
2. Kullanıcı temel alanları doldurur (`ad`, `soyad`, `telefon`, `email`, `kisi_tipi`, `status`).
3. `Kaydet` butonu:
   - `POST /api/kisiler` çağrısını yapar (JSON body veya form-data)
   - Başarılı olursa dönen kişi verisinden `id` ve `tam_ad` alınır.
4. İlgili live search alanında:
   - `hidden` input (`ilan_sahibi_id` veya `ilgili_kisi_id`) yeni `id` ile güncellenir.
   - Text input, yeni kişinin adıyla (`tam_ad - telefon`) doldurulur.
   - Modal kapatılır.

Bu entegrasyonun JS tarafında **tek bir helper fonksiyon**la (örn. `openAddPersonModal(type)` + `applyNewPersonToField(type, kisi)`) yönetilmesi önerilir.

---

## 6. Danışman – CRM ve User Modeli İlişkisi

### 6.1. Danışman alanının kaynağı

- İlan üzerinde `danisman_id` **her zaman `users` tablosuna** işaret eder.
- CRM `Kisi` tarafında da `danisman_id` kolonuyla kişiye bağlı sorumlu danışman bilgisi tutulur.
- Danışman seçim ekranı:
  - Daima `User` modeli üzerinden (`/api/admin/users/search`) yapılmalı.
  - `kisiler` live search’ünde danışman araması **yapılmamalı**, kişi vs. kullanıcı kavramları net ayrılmalıdır.

### 6.2. Rol bazlı filtre

- `UserController::search` içinde:
  - `whereHas('roles', name='danisman')`
  - `where('status', 1)`
- Bu pattern **tüm danışman aramalarında tekrar kullanılmalıdır.**

---

## 7. Yetkilendirme ve Rol Bazlı Filtreleme

Kaynak: `App\Modules\Crm\Controllers\KisiApiController`, `KisiService`, `IlanController::index()`

### 7.1. CRM içinde kişi listeleme

- Danışman rolündeki kullanıcılar için default filtre:
  - `filters['danisman_id'] = Auth::id()`
- İlan tarafında da benzer şekilde "İlanlarım" vb. ekranlarda:
  - `where('danisman_id', Auth::id())` kullanılır.

### 7.2. API bazında erişim kontrolü

- `KisiApiController::show/update/destroy` içinde:
  - Eğer kullanıcı danışmansa **sadece kendi `danisman_id`’siyle eşleşen kişilere** erişebilir.

Bu desen, ilan–CRM–danışman üçgeninde veri izolasyonunu sağlar.

---

## 8. Önerilen İyileştirmeler (Özet)

Mevcut koda bakarak **özellikle ilan ekleme sayfasındaki kişi alanları ve CRM entegrasyonu** için önerilen ek adımlar:

1. **`ilgili_kisi_id` tam entegrasyon**  
   - Validation’a `nullable|exists:kisiler,id` eklenmeli.  
   - `Ilan::create()` çağrısına `'ilgili_kisi_id' => $request->ilgili_kisi_id` dahil edilmeli.

2. **Modal form alanlarının Context7 ile hizalanması**  
   - `_kisi-ekle.blade.php` içinde:
     - `name="musteri_tipi"` → `name="kisi_tipi"`
     - Label "Müşteri Tipi" → "Kişi Tipi" veya benzer nötr ifade.
   - `status` değerleri `Aktif` / `Pasif` olmalı; 0/1 veya farklı stringler kullanılmamalı.

3. **Tek live search standardına inmek**  
   - `create.blade.php` içindeki inline `.context7-live-search` JS bloğu kaldırılıp yalnızca
     `public/js/context7-live-search-simple.js` kullanılmalı.  
   - Böylece debounce, min karakter, JSON formatı tek bir yerden kontrol edilir.

4. **"Yoksa Ekle" akışının tamamlanması**  
   - `#save_add_person` butonu JS tarafında `/api/kisiler` endpoint’ine bağlanmalı.  
   - Dönen kişinin ID’si ve adı ilgili alanlara (owner/related) otomatik işlenmeli.

5. **Kişi tipi ve status değerlerinde tam tutarlılık**  
   - Tüm formlar ve API validasyonları, `kisiler-form-standardization-2025-10-23.json`’daki listeleri referans almalı:
     - `kisi_tipi` değerleri: `Müşteri`, `Potansiyel`, `Ev Sahibi`, `Alıcı`, `Kiracı`, `Satıcı`, `Yatırımcı`, `Danışman`, `Tedarikçi`.
     - `status` değerleri: `Aktif`, `Pasif`, `Beklemede`.

6. **Dokümantasyon ve otomatik kontroller**  
   - Bu doküman `.context7/authority.json` ve mevcut Context7 knowledge dosyalarıyla tutarlı tutulmalı.  
   - Gelecekteki scriptler, kişi alanlarında `musteri_tipi`, static `<select>` gibi yasak pattern’leri tarayıp uyarı verebilir.

---

## 9. Uygulama Stratejisi

1. **Küçük refactor paketi (1–2 PR)**
   - `IlanController::store()`
     - `ilgili_kisi_id` validasyon + create alanı.
   - `_kisi-ekle.blade.php`
     - Field isimleri (`kisi_tipi`), label metinleri, dark-mode/transition kontrolü.
   - Inline live search JS → kaldır, sadece `context7-live-search-simple.js` bırak.

2. **Entegrasyon testleri**
   - Yeni bir ilan oluştururken:
     - `ilan_sahibi_id`, `ilgili_kisi_id`, `danisman_id` doğru set ediliyor mu?  
     - Kayıt sonrası `ilanlar` tablosunda FK’ler doğru mu?
   - Modal üzerinden kişi ekleyip anında yeni ilan sahibi/ilgili kişi olarak seçilebiliyor mu?

3. **Gelecek adımlar (opsiyonel)**
   - Kişi ekranlarında (CRM) ilan geçmişi, uygunluk skoru (`getPotentialOwners`, `calculateOwnerScore`) gibi bilgilerin ilan formuna inline yansıtılması (ör. seçilen kişinin son 10 ilanı, toplam işlem tutarı vb.).

---

Bu doküman, ilan ekleme/düzenleme akışlarında kişi seçimi ve CRM entegrasyonunu **tek bir standart referans** altında toplamak için yazıldı. Yeni sayfa veya özellik eklerken, kişi ile ilgili her alanın:
- `kisiler` veya `users` tablosuna doğru bağlandığından,
- Context7 Live Search pattern’ini kullandığından,
- `kisi_tipi` ve `status` değerleriyle tutarlı olduğundan emin olmak için bu rehber referans alınmalıdır.
