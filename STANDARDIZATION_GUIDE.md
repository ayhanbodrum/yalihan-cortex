# 🎯 YALIHAN EMLAK - STANDARDIZATION GUIDE

> **AMAÇ:** Bu rehber sistemin unutulmaması ve her zaman standart kalması için hazırlanmıştır.  
> **KİM İÇİN:** Tüm geliştiriciler (yeni, mevcut, gelecek)  
> **NE ZAMAN:** Her yeni feature, bug fix, refactoring öncesi

**Last Updated:** 2025-10-30  
**Version:** 2.0.0  
**Status:** MANDATORY - Her geliştirici okumak ZORUNDA!

---

## 📋 YENİ SAYFA/FEATURE EKLERKEN CHECKLIST

### ✅ **Başlamadan Önce:**
- [ ] Bu rehberi (STANDARDIZATION_GUIDE.md) oku
- [ ] MODERNIZATION_PLAN.md'ye göz at
- [ ] Benzer feature var mı kontrol et (duplicate'den kaçın)
- [ ] Component library'ye bak (`/admin/components` - local)
- [ ] Context7 kurallarını kontrol et (`.context7/authority.json`)

###✅ **Geliştirme Sırasında:**

#### **CSS/Styling:**
- [ ] **SADECE Tailwind CSS** kullan (Neo classes transition period)
- [ ] Mobile-first approach (sm:, md:, lg:, xl:, 2xl:)
- [ ] Dark mode support (`dark:` prefix)
- [ ] Responsive design (tüm ekran boyutları)
- [ ] **Yasak:** Inline styles, !important

```html
<!-- ✅ DOĞRU -->
<button class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg shadow-md transition-all">
    Kaydet
</button>

<!-- ❌ YANLIŞ -->
<button class="neo-btn neo-btn-primary" style="margin-top: 10px !important;">
    Kaydet
</button>
```

#### **Forms:**
- [ ] Form components kullan (`<x-form.input>`, `<x-form.select>`, etc.)
- [ ] Frontend validation ekle (HTML5 + Alpine.js)
- [ ] Backend validation ekle (Laravel validation)
- [ ] Error messages göster
- [ ] Success messages göster
- [ ] Loading states ekle
- [ ] ARIA labels ekle (accessibility)
- [ ] Keyboard navigation destekle

```blade
<!-- ✅ DOĞRU: Component kullanımı -->
<x-form.input
    name="title"
    label="İlan Başlığı"
    placeholder="Örnek: Deniz Manzaralı Villa"
    :value="old('title')"
    :error="$errors->first('title')"
    required
    autofocus
/>

<!-- ❌ YANLIŞ: Manuel HTML -->
<div>
    <label>İlan Başlığı</label>
    <input type="text" name="title" class="form-control">
</div>
```

#### **JavaScript:**
- [ ] Alpine.js kullan (interactivity için)
- [ ] Vanilla JS kullan (simple tasks için)
- [ ] **Yasak:** jQuery, heavy frameworks (React, Vue)
- [ ] ES6+ syntax kullan
- [ ] Console errors temizle
- [ ] Event listeners temizle (memory leaks)

```javascript
// ✅ DOĞRU: Alpine.js
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle</button>
    <div x-show="open">Content</div>
</div>

// ❌ YANLIŞ: jQuery
$('.button').click(function() {
    $('.content').toggle();
});
```

#### **PHP/Laravel:**
- [ ] Type hints kullan
- [ ] Return types belirt
- [ ] PSR-12 standard
- [ ] Eloquent ORM kullan (raw SQL'den kaçın)
- [ ] English field names (ZORUNLU!)
- [ ] Context7 kurallarına uy

```php
// ✅ DOĞRU
public function store(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'title' => 'required|string|max:200',
        'status' => 'required|in:active,pending',
    ]);

    $talep = Talep::create($validated);

    return redirect()->route('admin.talepler.show', $talep)
        ->with('success', 'Talep başarıyla oluşturuldu.');
}

// ❌ YANLIŞ
public function store($request)
{
    $talep = Talep::create([
        'baslik' => $request->baslik, // ❌ Türkçe field
        'durum' => $request->durum, // ❌ 'durum' yasak, 'status' kullan
    ]);

    return back(); // ❌ No success message
}
```

#### **Database:**
- [ ] English field names (ZORUNLU!)
- [ ] Indexes ekle (foreign keys, search fields)
- [ ] Soft deletes kullan (kalıcı silme yerine)
- [ ] Migrations yaz (rollback desteği)

```php
// ✅ DOĞRU: English field names
Schema::create('talepler', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->enum('status', ['active', 'pending', 'closed']);
    $table->boolean('enabled')->default(true);
    $table->timestamps();
    $table->softDeletes();
    
    $table->index('status');
    $table->index('enabled');
});

// ❌ YANLIŞ: Turkish field names
Schema::create('talepler', function (Blueprint $table) {
    $table->id();
    $table->string('baslik'); // ❌
    $table->string('durum'); // ❌
    $table->boolean('aktif'); // ❌
});
```

### ✅ **Commit Öncesi:**
- [ ] ESLint çalıştır (`npm run lint`)
- [ ] Prettier çalıştır (`npm run format`)
- [ ] PHP CS Fixer çalıştır (`./vendor/bin/php-cs-fixer fix`)
- [ ] Context7 validation geç (`php artisan context7:check`)
- [ ] Console errors temizle (F12 → Console)
- [ ] Browser'da test et (Chrome, Firefox, Safari)
- [ ] Mobile'da test et (Responsive mode)

### ✅ **Commit Mesajı:**

**Format:** `type(scope): description`

**Types:**
- `feat`: Yeni feature
- `fix`: Bug fix
- `docs`: Documentation
- `style`: Formatting, missing semicolons
- `refactor`: Code refactoring
- `test`: Adding tests
- `chore`: Maintenance tasks

```bash
# ✅ DOĞRU
feat(forms): add autocomplete component with search
fix(validation): fix email regex pattern bug
docs(components): update form component documentation
refactor(css): migrate Neo classes to Tailwind
chore(deps): update dependencies to latest versions

# ❌ YANLIŞ
Update files
Fix bug
WIP
asdasd
```

### ✅ **Pull Request/Merge:**
- [ ] Tests çalıştır (`php artisan test`)
- [ ] Build başarılı mı? (`npm run build`)
- [ ] Documentation güncellendi mi?
- [ ] CHANGELOG.md güncellendi mi?
- [ ] Review checklist dolduruldu mu?
- [ ] Screenshots eklendi mi? (UI changes için)

---

## 🚫 YASAKLI PATTERN'LER (ASLA KULLANMA!)

### **❌ CSS/Styling:**
```yaml
Yasak:
  - Neo classes (transition period dışında)
  - Inline styles (style="...")
  - !important (son çare olarak bile)
  - Global CSS (component-based kullan)
  - ID selectors (#myElement)

Kullan:
  ✅ Tailwind CSS classes
  ✅ Component classes (scoped)
  ✅ CSS variables (--neo-spacing-*)
```

### **❌ JavaScript:**
```yaml
Yasak:
  - jQuery ($(...))
  - document.write()
  - eval()
  - with statement
  - var (use const/let)
  - Global variables

Kullan:
  ✅ Alpine.js
  ✅ Vanilla JS (modern)
  ✅ ES6+ (const, let, arrow functions)
  ✅ Modules (import/export)
```

### **❌ PHP:**
```yaml
Yasak Field Names:
  - durum → use 'status'
  - aktif → use 'enabled'
  - is_active → use 'enabled'
  - sehir → use 'city'
  - sehir_id → use 'city_id'
  - musteriler → use 'kisiler'
  - ad_soyad → use 'full_name' or separate fields

Yasak Patterns:
  - Raw SQL (use Eloquent)
  - No type hints
  - No return types
  - Magic numbers
  - God classes
```

---

## ✅ ZORUNLU KULLANIM STANDARTLARI

### **📱 Responsive Design (ZORUNLU!):**
```html
<!-- ✅ Mobile-first approach -->
<div class="w-full md:w-1/2 lg:w-1/3">
    <!-- Mobilde full width, tablet'te yarım, desktop'ta 1/3 -->
</div>

<div class="px-4 md:px-6 lg:px-8">
    <!-- Mobilde 16px, tablet'te 24px, desktop'ta 32px padding -->
</div>

<div class="text-sm md:text-base lg:text-lg">
    <!-- Responsive text sizes -->
</div>
```

### **♿ Accessibility (WCAG 2.1 AA - ZORUNLU!):**
```html
<!-- ✅ ARIA labels -->
<button aria-label="Menüyü kapat" onclick="closeMenu()">
    <svg>...</svg>
</button>

<!-- ✅ Form labels -->
<label for="email">Email</label>
<input id="email" type="email" name="email" required>

<!-- ✅ Alt text for images -->
<img src="villa.jpg" alt="Deniz manzaralı 3+1 villa">

<!-- ✅ Skip links -->
<a href="#main" class="sr-only focus:not-sr-only">İçeriğe atla</a>

<!-- ✅ Keyboard navigation -->
<div tabindex="0" role="button" @keydown.enter="handleClick">
```

### **🌙 Dark Mode (ZORUNLU!):**
```html
<!-- ✅ Dark mode support -->
<div class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
    <h1 class="text-gray-900 dark:text-gray-100">Başlık</h1>
    <p class="text-gray-600 dark:text-gray-400">İçerik</p>
</div>

<input class="bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600">
```

### **⚡ Performance (ZORUNLU!):**
```html
<!-- ✅ Lazy loading -->
<img src="large.jpg" loading="lazy" alt="...">

<!-- ✅ Async scripts -->
<script src="analytics.js" async></script>

<!-- ✅ Preload critical assets -->
<link rel="preload" href="font.woff2" as="font" crossorigin>

<!-- ✅ Minimize re-renders (Alpine.js) -->
<div x-data="{ count: 0 }" x-cloak>
    <span x-text="count"></span>
    <button @click="count++">Increment</button>
</div>
```

---

## 📚 COMPONENT LIBRARY KULLANIMI

### **Form Components (resources/views/components/form/):**

#### **Input:**
```blade
<x-form.input
    name="title"
    type="text"
    label="Başlık"
    placeholder="İlan başlığını girin"
    :value="old('title', $ilan->title ?? '')"
    :error="$errors->first('title')"
    help="En az 10, en fazla 200 karakter"
    required
    autofocus
/>
```

#### **Select:**
```blade
<x-form.select
    name="category_id"
    label="Kategori"
    :options="$categories"
    :value="old('category_id')"
    :error="$errors->first('category_id')"
    placeholder="Kategori seçin"
    searchable
    clearable
    required
/>
```

#### **Textarea:**
```blade
<x-form.textarea
    name="description"
    label="Açıklama"
    :value="old('description')"
    :error="$errors->first('description')"
    rows="5"
    maxlength="1000"
    required
/>
```

#### **Checkbox:**
```blade
<x-form.checkbox
    name="featured"
    label="Öne Çıkan"
    :checked="old('featured', $ilan->featured ?? false)"
    help="Öne çıkan ilanlar anasayfada gösterilir"
/>
```

#### **File Upload:**
```blade
<x-form.file
    name="images[]"
    label="Fotoğraflar"
    accept="image/*"
    multiple
    max-size="5MB"
    preview
    :error="$errors->first('images')"
/>
```

### **Kullanım Kuralları:**
1. **Her zaman component kullan** (manuel HTML yerine)
2. **:value ve :error prop'larını geç** (old() ve $errors ile)
3. **Label ekle** (accessibility için)
4. **Help text ekle** (kullanıcıya yardımcı olmak için)
5. **Required işaretle** (gerekli alanlar için)

---

## 🔄 GÜNLÜK RITUAL (Unutma!)

### **🌅 Sabah (Çalışmaya Başlarken):**
```bash
# 1. Son değişiklikleri çek
git pull origin main

# 2. Dependencies güncelle (gerekirse)
composer install
npm install

# 3. Database migrate et (gerekirse)
php artisan migrate

# 4. Cache temizle
php artisan cache:clear
php artisan view:clear

# 5. Build yap (development)
npm run dev
```

### **🌆 Akşam (İş Bitiminde):**
```bash
# 1. Console errors temizle
# F12 → Console → Errors: 0

# 2. Lint errors düzelt
npm run lint --fix

# 3. Commit message kontrol et
git log --oneline -5

# 4. TODO listesi güncelle
# Yarın ne yapılacak not al

# 5. Branch temizle (gerekirse)
git branch --merged | grep -v "\*" | xargs -n 1 git branch -d
```

### **📅 Haftalık (Pazartesi):**
- [ ] STANDARDIZATION_GUIDE.md'yi oku (bu dosya!)
- [ ] MODERNIZATION_PLAN.md'ye göz at
- [ ] Component library'ye yeni component eklenmişmi kontrol et
- [ ] CHANGELOG.md güncelle
- [ ] Dependencies güncelle (`npm outdated`, `composer outdated`)

---

## 📖 REFERANSLAR (Hızlı Erişim)

### **Documentation:**
- **Tailwind CSS:** https://tailwindcss.com/docs
- **Alpine.js:** https://alpinejs.dev/
- **Laravel:** https://laravel.com/docs
- **Heroicons:** https://heroicons.com/
- **WCAG Guidelines:** https://www.w3.org/WAI/WCAG21/quickref/

### **Internal:**
- **Component Library:** `/admin/components` (local development)
- **Context7 Authority:** `.context7/authority.json`
- **Modernization Plan:** `MODERNIZATION_PLAN.md`
- **CSS Migration:** `.yalihan-bekci/knowledge/css-migration-strategy.md`
- **Phase 1 Report:** `.yalihan-bekci/knowledge/PHASE1-COMPLETED.md`

### **Tools:**
- **ESLint:** `.eslintrc.json`
- **Prettier:** `.prettierrc`
- **PHP CS Fixer:** `.php-cs-fixer.php`
- **Tailwind Config:** `tailwind.config.js`
- **Vite Config:** `vite.config.js`

---

## 🚨 CRITICAL REMINDERS (UNUTMA!)

### **🔥 Her Zaman:**
1. Yeni sayfa → **Form components kullan**
2. Yeni component → **Documentation yaz**
3. Her commit → **Pre-commit hooks geçmeli**
4. Her PR → **Review checklist doldur**
5. Her deploy → **CHANGELOG güncelle**
6. Her bug → **Yalıhan Bekçi'ye öğret**
7. Her feature → **STANDARDIZATION_GUIDE kontrol et**

### **🎯 Hedef Metrikler:**
```yaml
Code Quality:
  - Lint errors: 0
  - Console errors: 0
  - Test coverage: > 80%
  - Accessibility score: > 95/100

Performance:
  - Build time: < 30s
  - Bundle size: < 500KB (gzipped)
  - Page load: < 2s
  - Time to Interactive: < 3s

Developer Experience:
  - Component reuse: > 80%
  - Documentation coverage: > 90%
  - Onboarding time: < 1 day
  - Developer satisfaction: > 95%
```

---

## 📞 YARDIM & SORULAR

### **Sıralama (Bu sırayla kontrol et):**
1. **STANDARDIZATION_GUIDE.md** (bu dosya - ilk oku!)
2. **MODERNIZATION_PLAN.md** (detaylı plan)
3. **docs/** dizini (technical documentation)
4. **/admin/components** (component examples - local)
5. **Yalıhan Bekçi** (Context7 kuralları)
6. **Team Lead** (technical questions)
7. **GitHub Issues** (bug reports)

### **Sık Sorulan Sorular:**

**Q: Neo classes ne zaman kaldırılacak?**  
A: Kademeli geçiş yapıyoruz. Yeni sayfalar Tailwind, eski sayfalar düzeltildikçe Tailwind'e çevrilecek.

**Q: Component library nerede?**  
A: `/admin/components` (local development) - Yakında documentation eklenecek.

**Q: Pre-commit hooks çalışmıyor?**  
A: `npm install` ve `composer install` yap, sonra `git commit` tekrar dene.

**Q: Context7 validation failed?**  
A: Forbidden patterns kullanmış olabilirsin. `.context7/authority.json` kontrol et.

**Q: Dark mode test nasıl yapılır?**  
A: Browser'da tema toggle et veya `dark` class'ı `<html>` tag'ine manuel ekle.

---

## 🎓 ONBOARDING (Yeni Geliş tiriciler)

### **İlk 3 Gün:**
- [ ] Okuma (2 saat):
  - [ ] STANDARDIZATION_GUIDE.md (bu dosya)
  - [ ] MODERNIZATION_PLAN.md
  - [ ] README.md
  - [ ] ARCHITECTURE.md

- [ ] Setup (1 saat):
  - [ ] Git clone
  - [ ] Dependencies install
  - [ ] Database setup
  - [ ] Local development

- [ ] Exploration (3 saat):
  - [ ] Component library keşfet
  - [ ] Mevcut sayfaları incele
  - [ ] Code style'ı öğren

### **İlk Hafta:**
- [ ] Küçük bug fix yap (öğrenme amaçlı)
- [ ] Kod review'lara katıl
- [ ] Component document yaz (1 adet)
- [ ] Takım ile tanış

### **İlk Ay:**
- [ ] Orta boyutlu feature geliştir
- [ ] Test yaz
- [ ] Documentation yaz
- [ ] Takım standardlarını benimse

---

## 🏆 BEST PRACTICES ÖZETI

### **✅ DO (Yap):**
- Tailwind CSS kullan
- Component'ları reuse et
- Documentation yaz
- Test yaz
- Accessibility'yi düşün
- Performance'ı optimize et
- Code review yap
- Conventional commits kullan

### **❌ DON'T (Yapma):**
- Neo classes kullan (transition period hariç)
- Türkçe field names kullan
- jQuery kullan
- Inline styles kullan
- Console errors bırak
- Test'siz deploy et
- Documentation'sız feature ekle
- Pre-commit hooks'u atla

---

**🎯 Hedef:** Kusursuz, Standart, Ölçeklenebilir, Unutulmaz Sistem!

**📅 Son Güncelleme:** 2025-10-30  
**📌 Versiyon:** 2.0.0  
**✅ Status:** MANDATORY - Bu rehber zorunludur!

---

**💡 Unutma:** Bu rehber senin ve takımının zamanını kurtarmak, hataları önlemek ve sistemin sürdürülebilir olmasını sağlamak için var. Lütfen ciddiye al! 🚀

