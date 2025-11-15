## Neo Design System – Component Schema

Bu şema; butonlar, formlar, kartlar, tablolar ve yardımcı sınıflar için Neo kurallarını ve varyantlarını özetler. Amaç: tasarım tutarlılığı ve hızlı uygulama.

### Genel İlkeler

- **Sınıf önekleri**: `neo-` (ör. `neo-btn`, `neo-input`, `neo-card`)
- **Responsive**: `sm:`, `md:`, `lg:`, `xl:`
- **Dark mode**: `dark:` ön ekleri ile zorunlu destek
- **Yasak**: `btn-*`, `card-*`, `form-*`, Bootstrap grid, jQuery

### Butonlar (`neo-btn-*`)

- Varyantlar: `neo-btn-primary`, `neo-btn-secondary`, `neo-btn-success`, `neo-btn-warning`, `neo-btn-danger`, `neo-btn-info`, `neo-btn-ghost`
- Boyutlar: `neo-btn-xs`, `neo-btn-sm`, `neo-btn-md`, `neo-btn-lg`
- Durumlar: `disabled`, `loading` (`aria-busy="true"` veya içte spinner)
- İkonlu kullanım: SVG ikon solda, `.mr-2` ile aralık

Örnek:

```blade
<button class="neo-btn-primary neo-btn-md">
    <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none"><path/></svg>
    Kaydet
</button>
```

### Form Alanları

- Etiket: `neo-label`
- Girdi: `neo-input` (text, email, password, tel, file, textarea, select)
- Grup: `neo-form-group`
- Yardım/uyarı: `text-xs text-gray-500`, hata: `text-red-600 dark:text-red-400`

Örnek:

```blade
<div class="neo-form-group">
  <label for="email" class="neo-label">E-posta</label>
  <input id="email" name="email" type="email" class="neo-input" required>
  @error('email')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
  <p class="text-xs text-gray-500">Kurumsal e-posta kullanın</p>
</div>
```

### Kartlar (`neo-card`)

- İç boşluk: tipik `p-4|p-6|p-8`
- Başlık: semantik `h2`/`h3` + ikon
- Bölüm başlığı çizgisi: `border-b border-gray-200 dark:border-gray-800`

Örnek:

```blade
<div class="neo-card p-6">
  <h2 class="text-xl font-bold flex items-center">
    <svg class="w-5 h-5 mr-2"/><span>Başlık</span>
  </h2>
  <div class="mt-4">İçerik</div>
</div>
```

### Tablolar

- Kapsayıcı: `neo-table-responsive`
- Tablo: `neo-table`
- Hücreler: `neo-table-th`, `neo-table-td`, satır: `neo-table-row`

Örnek:

```blade
<div class="neo-table-responsive">
  <table class="neo-table">
    <thead>
      <tr>
        <th class="neo-table-th">İsim</th>
        <th class="neo-table-th">Durum</th>
      </tr>
    </thead>
    <tbody>
      <tr class="neo-table-row">
        <td class="neo-table-td">John</td>
        <td class="neo-table-td"><x-neo.status-badge :value="'Aktif'" /></td>
      </tr>
    </tbody>
  </table>
</div>
```

### Rozetler (`x-neo.status-badge`)

- Kategori: `status`, `type`, `role` vb.
- Değer: metin

Örnek:

```blade
<x-neo.status-badge :value="$user->status ? 'Aktif' : 'Pasif'" />
```

### Grid ve Düzen

- Standart grid: `grid grid-cols-1 md:grid-cols-2 gap-6`
- Listeler: `space-y-4`, yatay gruplar: `flex space-x-2`
- Kart dizilimleri: `grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6`

### Responsive Kuralları

- Küçük → büyük yaklaşım (mobile-first)
- Örnek: `w-full sm:w-auto`, `hidden md:flex`, `text-sm md:text-base`

### Dark Mode Kuralları

- Metin: `dark:text-gray-100|300`
- Arka plan: `dark:bg-gray-900|800|700`
- Kenarlık: `dark:border-gray-700|800`

### Erişilebilirlik

- İkonlu butonlarda `aria-label`
- İnteraktif öğelerde `:focus` görsel geri bildirim (ring)
- `aria-busy` yükleme durumlarında

### Yasaklar ve Uyum

- Yasak sınıflar: `btn-*`, `card-*`, `form-*`, `row`, `col-*`
- jQuery ve Bootstrap kullanımı yasak
- Tüm görünümler `dark:` ve responsive ön eklerini makul seviyede içermeli

### Örnek Form Bölümü (Tam Blok)

```blade
<div class="neo-card p-6">
  <h2 class="text-xl font-bold mb-6">Kullanıcı Bilgileri</h2>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="neo-form-group">
      <label class="neo-label" for="name">Ad Soyad</label>
      <input id="name" name="name" class="neo-input" required>
    </div>
    <div class="neo-form-group">
      <label class="neo-label" for="email">E-posta</label>
      <input id="email" name="email" type="email" class="neo-input" required>
    </div>
  </div>
  <div class="mt-6 flex justify-end space-x-3">
    <a href="#" class="neo-btn-secondary">İptal</a>
    <button class="neo-btn-primary">Kaydet</button>
  </div>
</div>
```

### Dosya ve Varlıklar

- CSS: `public/css/admin/neo-components.css` (layout’a dahil edilmelidir)
- Blade component’ler: `<x-neo.*>` isimlendirmesiyle `resources/views/components/` altında

### Doğrulama

- Script: `./scripts/context7-design-consistency.sh` ile legacy sınıflar, responsive ve dark mode kullanımları kontrol edilir.
