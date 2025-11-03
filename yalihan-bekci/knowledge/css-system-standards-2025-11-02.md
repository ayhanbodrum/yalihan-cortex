# 🎨 CSS SYSTEM STANDARDS - YALİHAN BEKÇİ

**Tarih:** 2 Kasım 2025  
**Versiyon:** 1.0.0  
**Status:** ACTIVE  
**Enforcement:** MANDATORY

---

## 📋 CSS SİSTEMİ DURUMU

### Mevcut Sistem:

```yaml
CSS Framework: Tailwind CSS 3.4.14
Transition Period: ACTIVE (Neo classes → Tailwind)
Build System: Vite 5.4.10
Dark Mode: class-based (html.dark)
Responsive: Mobile-first
```

### Stratejik Karar:

**"ADIM ADIM GEÇİŞ" - 3 Aşama:**

1. **PHASE 1: Cleanup** ✅ TAMAMLANDI
   - Duplicate CSS dosyaları silindi
   - app.css optimize edildi (1,158 → 217 satır, %81 küçültme)
   - Build başarılı: 161.49 kB (gzip: 21.47 kB)

2. **PHASE 2: Touch and Convert** 🔄 AKTİF
   - Yeni sayfalar → Saf Tailwind
   - Düzeltilen sayfalar → Neo → Tailwind dönüşümü
   - Çalışan sayfalar → Dokunma!

3. **PHASE 3: Component Library** 📅 6+ ay
   - Headless UI standartlarında Blade components
   - 20+ form component
   - WCAG 2.1 AA accessibility

---

## ✅ ZORUNLU CSS STANDARTLARI

### 1. Tailwind CSS Kullanımı (ZORUNLU!)

```html
<!-- ✅ DOĞRU: Pure Tailwind -->
<button class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium rounded-lg shadow-lg transition-all duration-200 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
    Kaydet
</button>

<!-- ❌ YASAK: Neo classes (transition period hariç) -->
<button class="neo-btn neo-btn-primary">
    Kaydet
</button>

<!-- ❌ YASAK: Inline styles -->
<button style="background: blue; padding: 10px;">
    Kaydet
</button>

<!-- ❌ YASAK: !important -->
<div class="mt-4" style="margin-top: 20px !important;">
    Content
</div>
```

---

### 2. Dark Mode Support (ZORUNLU!)

```html
<!-- ✅ Her element dark mode desteklemeli -->
<div class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
    <h1 class="text-gray-900 dark:text-gray-100">Başlık</h1>
    <p class="text-gray-600 dark:text-gray-400">İçerik</p>
</div>

<!-- Input elements -->
<input class="bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white focus:ring-blue-500 dark:focus:ring-blue-400">

<!-- Buttons -->
<button class="bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600">
    Action
</button>

<!-- Cards -->
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-lg">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Başlık</h3>
    </div>
</div>
```

**Kural:** Her color/background class'ının `dark:*` variant'ı olmalı!

---

### 3. Responsive Design (ZORUNLU!)

```html
<!-- ✅ Mobile-first approach -->

<!-- Width -->
<div class="w-full md:w-1/2 lg:w-1/3 xl:w-1/4">
    <!-- Mobile: 100%, Tablet: 50%, Desktop: 33%, XL: 25% -->
</div>

<!-- Padding/Margin -->
<div class="px-4 md:px-6 lg:px-8 xl:px-12">
    <!-- Mobile: 16px, Tablet: 24px, Desktop: 32px, XL: 48px -->
</div>

<!-- Text Size -->
<h1 class="text-2xl md:text-3xl lg:text-4xl xl:text-5xl">
    <!-- Responsive heading -->
</h1>

<!-- Grid Layout -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    <!-- Mobile: 1 column, Tablet: 2, Desktop: 3, XL: 4 -->
</div>

<!-- Flexbox -->
<div class="flex flex-col md:flex-row items-start md:items-center gap-4">
    <!-- Mobile: stack, Tablet+: horizontal -->
</div>

<!-- Display -->
<div class="hidden md:block">
    <!-- Hidden on mobile, visible on tablet+ -->
</div>

<div class="block md:hidden">
    <!-- Visible on mobile, hidden on tablet+ -->
</div>
```

**Breakpoints:**
- `sm:` 640px (mobiles)
- `md:` 768px (tablets)
- `lg:` 1024px (laptops)
- `xl:` 1280px (desktops)
- `2xl:` 1536px (large screens)

---

### 4. Accessibility (WCAG 2.1 AA - ZORUNLU!)

```html
<!-- ✅ Focus States -->
<button class="focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-blue-400 dark:focus:ring-offset-gray-900">
    Action
</button>

<!-- ✅ ARIA Labels -->
<button aria-label="Menüyü kapat" class="...">
    <svg class="w-5 h-5">...</svg>
</button>

<!-- ✅ Form Labels -->
<div class="space-y-2">
    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        Email Adresi
    </label>
    <input 
        id="email" 
        type="email" 
        name="email" 
        required 
        aria-required="true"
        class="..."
    >
</div>

<!-- ✅ Alt Text for Images -->
<img 
    src="/images/villa.jpg" 
    alt="Deniz manzaralı 3+1 villa, 150m², Antalya" 
    class="w-full h-auto rounded-lg"
>

<!-- ✅ Skip Links -->
<a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-blue-600 focus:text-white focus:rounded-lg">
    İçeriğe atla
</a>

<!-- ✅ Keyboard Navigation -->
<div 
    tabindex="0" 
    role="button" 
    @keydown.enter="handleClick"
    @keydown.space="handleClick"
    class="cursor-pointer focus:ring-2 focus:ring-blue-500"
>
    Click me
</div>

<!-- ✅ Color Contrast -->
<!-- En az 4.5:1 ratio (normal text), 3:1 (large text) -->
<p class="text-gray-900 dark:text-gray-100">
    <!-- Good contrast on both themes -->
</p>
```

---

### 5. Performance Best Practices

```html
<!-- ✅ Lazy Loading -->
<img src="/images/large.jpg" loading="lazy" alt="...">

<!-- ✅ Async Scripts -->
<script src="/js/analytics.js" async></script>

<!-- ✅ Preload Critical Assets -->
<link rel="preload" href="/fonts/inter.woff2" as="font" type="font/woff2" crossorigin>

<!-- ✅ Minimize Re-renders (Alpine.js) -->
<div x-data="{ count: 0 }" x-cloak>
    <span x-text="count"></span>
    <button @click="count++" class="...">Increment</button>
</div>

<!-- ✅ Transition Classes (GPU-accelerated) -->
<div class="transition-transform duration-200 hover:scale-105">
    <!-- transform ve opacity GPU'da çalışır -->
</div>

<!-- ❌ AVOID: Layout shifts -->
<div class="transition-all"> <!-- width, height, margin değişir → layout shift -->
</div>
```

---

## 🚫 YASAKLI CSS PATTERN'LER

### 1. Inline Styles (YASAK!)

```html
<!-- ❌ YASAK -->
<div style="margin-top: 10px; background: blue;">
    Content
</div>

<!-- ✅ DOĞRU -->
<div class="mt-2.5 bg-blue-500">
    Content
</div>
```

---

### 2. !important (YASAK!)

```css
/* ❌ YASAK */
.my-class {
    color: red !important;
}

/* ✅ DOĞRU: Specificity ile çöz */
.parent .my-class {
    color: red;
}
```

---

### 3. ID Selectors (YASAK!)

```css
/* ❌ YASAK */
#myElement {
    color: blue;
}

/* ✅ DOĞRU: Class selectors */
.my-element {
    color: blue;
}
```

---

### 4. Global CSS (AVOID)

```css
/* ❌ AVOID: Global styles */
div {
    margin: 0;
}

p {
    font-size: 16px;
}

/* ✅ DOĞRU: Component-scoped or Tailwind */
.article-content p {
    @apply text-base leading-relaxed;
}
```

---

### 5. Neo Classes (Transition Period Only)

```html
<!-- ❌ YENİ SAYFALARDA YASAK -->
<button class="neo-btn neo-btn-primary">
    Save
</button>

<!-- ✅ DOĞRU: Pure Tailwind -->
<button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
    Save
</button>

<!-- ⚠️  ESKİ SAYFALARDA İZİNLİ (geçici) -->
<!-- Düzeltildikçe Tailwind'e çevrilecek -->
```

---

## 🎨 TAILWIND UTILITY PATTERN'LER

### Button Patterns

```html
<!-- Primary Button -->
<button class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium rounded-lg shadow-lg transition-all duration-200 hover:scale-105 active:scale-95 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-blue-400 dark:focus:ring-offset-gray-900">
    <svg class="w-5 h-5 mr-2">...</svg>
    Primary Action
</button>

<!-- Secondary Button -->
<button class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
    Secondary
</button>

<!-- Danger Button -->
<button class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg shadow-md transition-all duration-200 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
    Delete
</button>
```

---

### Card Patterns

```html
<!-- Basic Card -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden transition-shadow duration-200 hover:shadow-xl">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
            Başlık
        </h3>
        <p class="text-gray-600 dark:text-gray-400">
            İçerik
        </p>
    </div>
</div>

<!-- Interactive Card -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 cursor-pointer transition-all duration-200 hover:shadow-xl hover:-translate-y-1">
    <div class="p-6">
        ...
    </div>
</div>
```

---

### Form Input Patterns

```html
<!-- Text Input -->
<input 
    type="text"
    class="w-full px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200"
    placeholder="Metin girin..."
>

<!-- Select -->
<select class="w-full px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    <option>Seçenek 1</option>
    <option>Seçenek 2</option>
</select>

<!-- Checkbox -->
<input 
    type="checkbox"
    class="w-4 h-4 text-blue-600 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500"
>
```

---

## 📏 SPACING & SIZING STANDARTLARI

### Spacing Scale (Tailwind)

```yaml
0:   0px       # space-0
px:  1px       # space-px
0.5: 2px       # space-0.5
1:   4px       # space-1
1.5: 6px       # space-1.5
2:   8px       # space-2
2.5: 10px      # space-2.5
3:   12px      # space-3
4:   16px      # space-4
5:   20px      # space-5
6:   24px      # space-6
8:   32px      # space-8
10:  40px      # space-10
12:  48px      # space-12
16:  64px      # space-16
20:  80px      # space-20
24:  96px      # space-24
```

**Kullanım:**
- `p-4` → padding: 16px (all sides)
- `px-6` → padding-left: 24px, padding-right: 24px
- `mt-8` → margin-top: 32px
- `gap-4` → gap: 16px (flexbox/grid)

---

### Font Sizes

```yaml
xs:   12px  line-height: 16px  # text-xs
sm:   14px  line-height: 20px  # text-sm
base: 16px  line-height: 24px  # text-base
lg:   18px  line-height: 28px  # text-lg
xl:   20px  line-height: 28px  # text-xl
2xl:  24px  line-height: 32px  # text-2xl
3xl:  30px  line-height: 36px  # text-3xl
4xl:  36px  line-height: 40px  # text-4xl
```

---

## 🔍 YALİHAN BEKÇİ - CSS KONTROLLERI

### Auto-Detect Patterns

**Yalıhan Bekçi tespit eder:**

1. ✅ Inline styles (`style="..."`)
2. ✅ !important kullanımı
3. ✅ Neo classes (yeni sayfalarda)
4. ✅ ID selectors (#myElement)
5. ✅ Dark mode eksikliği
6. ✅ Responsive class eksikliği
7. ✅ Focus state eksikliği
8. ✅ ARIA label eksikliği

---

### MCP Server Komutları

```bash
# CSS validation
curl -X POST http://localhost:3000/context7_validate \
  -d '{"code": "<div class=\"neo-btn\">...</div>", "filePath": "resources/views/admin/test.blade.php"}'

# Response:
{
  "valid": false,
  "violations": [
    {
      "type": "neo_class_usage",
      "message": "Neo class kullanımı (yeni sayfalarda yasak)",
      "line": 1,
      "suggestion": "Tailwind CSS kullan"
    }
  ]
}
```

---

## 📊 CSS SİSTEMİ METRİKLERİ

### Önce (Phase 1 öncesi):

```yaml
app.css: 1,158 satır
Duplicate CSS: 2 dosya
Neo classes: Yaygın kullanım
Build size: ~200 kB
Dark mode: Kısmi destek
```

### Sonra (Phase 1 sonrası):

```yaml
app.css: 217 satır (%81 küçültme)
Duplicate CSS: 0
Neo classes: Transition period (plugin)
Build size: 161.49 kB (gzip: 21.47 kB)
Dark mode: Full support
```

---

## 🎯 MİGRATION WORKFLOW

### Yeni Sayfa Oluşturma:

```yaml
1. Template Seç:
   - resources/views/admin/templates/modern-page.blade.php
   
2. Pure Tailwind Kullan:
   - Neo classes YOK
   - Dark mode ✓
   - Responsive ✓
   - Accessibility ✓

3. Test:
   - Browser (Chrome, Firefox)
   - Mobile (responsive mode)
   - F12 Console (errors: 0)
   - Lighthouse (accessibility > 95)

4. Commit:
   - Pre-commit hooks geçmeli
```

---

### Eski Sayfa Düzeltme:

```yaml
1. Analiz:
   - Neo class sayısı (grep)
   - Layout kompleksitesi
   
2. Dönüşüm:
   - neo-btn → Tailwind button
   - neo-card → Tailwind card
   - neo-input → Tailwind input

3. Test:
   - Visual regression test
   - Functionality test
   - Accessibility test

4. Document:
   - Migration raporu yaz
   - Screenshot ekle
```

---

## 🛡️ ENFORCEMENT

### Pre-commit Hooks:

- ✅ Inline style check
- ✅ !important check
- ✅ Neo class check (yeni sayfalarda)
- ⚠️  Dark mode check (warning)
- ⚠️  Responsive check (warning)

### Cursor Rules:

- `.cursor/rules/mandatory-workflow-checks.mdc`
- CSS standards section

### Yalıhan Bekçi:

- `yalihan-bekci/knowledge/css-system-standards-2025-11-02.md` (bu dosya)
- MCP server validation tools

---

**🎨 HEDEF:** Modern, Accessible, Performant CSS Sistemi!

**📅 Son Güncelleme:** 2 Kasım 2025  
**✅ Status:** ACTIVE - Phase 2 devam ediyor!

