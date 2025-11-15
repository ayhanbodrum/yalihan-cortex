---
applyTo: '**/*.php'
description: 'Laravel modellerinde AI kullanımı için temel kurallar ve rehberler.'
---

# 🤖 AI Model Kuralları ve Rehberler

**Context7 Standardı:** C7-AI-MODEL-RULES-2025-09-12
**Versiyon:** 1.0.0 (Context7 Entegrasyonu)
**Son Güncelleme:** 12 Eylül 2025
**Durum:** ✅ Aktif
**Context7 Hafıza:** ✅ Aktif

---

Bu dosya, EmlakPro sistemindeki tüm modeller için AI kullanım kurallarını içerir.

## 🧠 Laravel Model Kuralları: İlişki vs. Accessor

### 1. İlişki Tanımları (Relationships)

- Aşağıdaki Eloquent metotları sadece ilişki olarak kullanılmalıdır:
    - `hasOne()`
    - `hasMany()`
    - `belongsTo()`
    - `belongsToMany()`
    - `morphTo()`
    - `morphMany()`
- Bu metotlar `with('...')` ile kullanılabilir.
- İsimlendirme: `camelCase` biçiminde olmalı (örn: `ilanFotoğrafları`, `kategori`, `ozellikKategori`)

### 2. Accessor (Attribute Getter) Tanımları

- `Attribute::make(...)` ile tanımlanır.
- Bu metotlar **özellik gibi çağrılır**, `with('...')` ile çağrılmaz.
- İsimlendirme: `snake_case` önerilir (örn: `formatted_date`, `short_title`, `slug_title`)

### 3. Kesişme Uyarısı

- Accessor ve ilişki aynı isimde olmamalıdır.
    - ❌ `name()` hem accessor hem ilişki olamaz.
    - ✅ `getFormattedNameAttribute()` vs. `translations()` gibi ayrı olmalı.

### 4. `with()` Kullanımı

- Sadece `relationship` fonksiyonlarıyla çalışır.
- `Attribute` tanımı yapılan bir alan `with()` ile çağrılmamalıdır.
- `addEagerConstraints()` hatası bu yanlışlıktan doğar.

### 5. Geliştirici Notu

- Eğer bir accessor `Attribute::make()` ile tanımlanmışsa, bu sadece model nesnesi üzerinden çağrılır:
    ```php
    $ilan->formatted_title // ✅
    ```
- Ama şuna benzerse hata verir:
    ```php
    Ilan::with('formatted_title')->get(); // ❌
    ```

## 👤 Kullanıcı Modeli (User.php)

Bu dosya Laravel'in kimlik doğrulama sistemini temel alır. AI tarafından bu modelle ilgili işlem yapılırken aşağıdaki kurallara uyulmalıdır:

### 📌 Kullanım Kuralları

- `User` modeli `role_id` alanı ile bir role ilişkilidir (`belongsTo`).
- `with('role')` gibi ilişki çağrıları yalnızca ilişkili modeller için yapılmalıdır.
- Eğer bir alan `Attribute::make()` ile tanımlanmışsa, bu sadece accessor'dır ve eager load edilemez.
- `getFullNameAttribute` gibi accessor'lar sadece `$user->full_name` şeklinde çağrılabilir, `with('full_name')` kullanılamaz.

### 🔐 Diğer Bilgiler

- Şifre `bcrypt` ile hash'lenmelidir.
- `hidden` özelliği içinde `password`, `remember_token` gibi hassas alanlar tanımlanmalıdır.
- AI çıktılarında asla düz metin şifre gösterilmemeli.
- `fillable` alanlara dikkat edilmelidir.

## 🧠 AI için Örnek Prompt'lar

### Kullanıcı Rolü Yükleme

```
// bu kullanıcıya ait rol bilgilerini yükle
```

Beklenen: `User::with('role')->find($id);`

### Model İlişkileri Yükleme

```
// bu ilanın kategorisi ve özelliklerini yükle
```

Beklenen: `Ilan::with(['kategori', 'ozellikler'])->find($id);`

### Accessor Kullanımı

```
// bu ilanın formatlanmış başlığını al
```

Beklenen: `$ilan->formatted_title` (with() kullanılmaz)

---

Bu kurallar Copilot'un ve geliştiricinin Laravel projelerinde model tutarlılığını korumasını sağlar.
Her yeni modelde bu ayrım göz önünde bulundurulmalıdır.
