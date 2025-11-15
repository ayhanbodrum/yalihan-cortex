# ✅ Yalıhan Bekçi Standartları - Kontrol Listesi

**Son Güncelleme:** 2025-11-04  
**Durum:** ACTIVE - Tüm yeni kodlarda uygulanmalı

---

## 🎯 CSS ARCHITECTURE

### ✅ Pure Tailwind CSS (NO Neo!)

```blade
<!-- ❌ YANLIŞ: Neo classes -->
<button class="neo-btn neo-btn-primary">Kaydet</button>

<!-- ✅ DOĞRU: Pure Tailwind -->
<button class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:scale-105 transition-all">
    Kaydet
</button>
```

**Zorunlu Tailwind Patterns:**

- ✅ Dark mode: `dark:bg-gray-800`, `dark:text-white`
- ✅ Focus states: `focus:ring-2 focus:ring-blue-500`
- ✅ Transitions: `transition-all duration-200`
- ✅ Responsive: `sm:px-4 md:px-6 lg:px-8`
- ✅ Hover effects: `hover:scale-105 hover:shadow-lg`

---

## 🎨 FORM STANDARDS

### Input Fields

```blade
<input type="text"
       class="w-full px-4 py-2
              bg-white dark:bg-gray-900
              border-2 border-gray-300 dark:border-gray-600
              rounded-lg
              text-black dark:text-white
              font-semibold
              placeholder-gray-600 dark:placeholder-gray-500
              focus:ring-2 focus:ring-blue-500 focus:border-transparent
              transition-colors">
```

### Labels

```blade
<label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
    İlan Başlığı *
</label>
```

### Buttons

```blade
<!-- Primary -->
<button class="px-6 py-3
               bg-gradient-to-r from-blue-600 to-purple-600
               text-white font-semibold
               rounded-lg
               hover:scale-105 active:scale-95
               focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
               transition-all shadow-lg">
    💾 Kaydet
</button>

<!-- Secondary -->
<button class="px-6 py-3
               bg-gray-200 dark:bg-gray-700
               text-gray-700 dark:text-gray-300
               font-semibold rounded-lg
               hover:bg-gray-300 dark:hover:bg-gray-600
               transition-all">
    İptal
</button>
```

---

## ⚡ JAVASCRIPT ARCHITECTURE

### ✅ Alpine.js (NO jQuery!)

```blade
<div x-data="{
    status: 'pending',
    showModal: false
}">
    <!-- Reactive state -->
    <button @click="showModal = true">Aç</button>

    <!-- Conditional rendering -->
    <div x-show="showModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100">
        Modal içeriği
    </div>

    <!-- Dynamic class -->
    <span :class="status === 'confirmed' ? 'text-green-600' : 'text-yellow-600'">
        Status
    </span>
</div>
```

**Alpine.js Directives:**

- ✅ `x-data`: Reactive state
- ✅ `x-show`: Conditional rendering
- ✅ `x-if`: Conditional DOM
- ✅ `@click`: Event handlers
- ✅ `:class`: Dynamic classes
- ✅ `x-transition`: Smooth animations
- ✅ `x-model`: Two-way binding

---

## 🗄️ DATABASE PATTERNS

### Table Naming

```php
// ✅ DOĞRU: Migration'daki ismi kullan
DB::table('yazlik_rezervasyonlar')

// ❌ YANLIŞ: Farklı isim kullanma
DB::table('yazlik_bookings')
```

### Context7 Compliance

```php
// ✅ DOĞRU: English status values
'status' => 'pending'  // ✅
'status' => 'confirmed' // ✅

// ❌ YANLIŞ: Turkish status values
'durum' => 'beklemede' // ❌
```

---

## 🔄 COMPONENT REUSABILITY

### DRY Principle

```blade
<!-- ✅ DOĞRU: Reusable component -->
@include('admin.ilanlar.components.photo-upload-manager', [
    'ilan' => $ilan
])

<!-- ❌ YANLIŞ: Code duplication -->
<!-- Same upload code copied to multiple files -->
```

**Component Usage Pattern:**

1. Component'i `admin.ilanlar.components.*` altında oluştur
2. `@include` ile farklı modüllerde kullan
3. Props ile data pass et: `['ilan' => $ilan]`

---

## 🎨 DARK MODE

### Required Implementation

```blade
<!-- ✅ Her element dark mode desteklemeli -->
<div class="bg-white dark:bg-gray-800
            text-gray-900 dark:text-white
            border-gray-200 dark:border-gray-700">
    <!-- Content -->
</div>
```

**Dark Mode Classes:**

- Background: `dark:bg-gray-800`, `dark:bg-gray-900`
- Text: `dark:text-white`, `dark:text-gray-300`
- Borders: `dark:border-gray-700`, `dark:border-gray-600`
- Shadows: `dark:shadow-2xl`

---

## 📱 RESPONSIVE DESIGN

### Mobile-First Approach

```blade
<div class="px-4           <!-- Mobile: 1rem -->
            sm:px-6        <!-- Tablet: 1.5rem -->
            lg:px-8        <!-- Desktop: 2rem -->
            grid
            grid-cols-1    <!-- Mobile: 1 column -->
            md:grid-cols-2 <!-- Tablet: 2 columns -->
            lg:grid-cols-3 <!-- Desktop: 3 columns -->
            gap-6">
    <!-- Content -->
</div>
```

**Breakpoints:**

- `sm:` 640px (Tablet)
- `md:` 768px (Small Desktop)
- `lg:` 1024px (Desktop)
- `xl:` 1280px (Large Desktop)

---

## ✅ CONTEXT7 COMPLIANCE

### Forbidden Patterns

```php
// ❌ YASAKLI
$query->where('durum', 'aktif')
$query->where('is_active', true)
$query->where('sehir', 'Istanbul')

// ✅ ZORUNLU
$query->where('status', 'active')
$query->where('enabled', true)
$query->where('city', 'Istanbul')
```

### Pre-Commit Checks

```bash
# Otomatik Context7 kontrolü
✅ PHP files: 0 violations
✅ Blade files: 0 violations
✅ Migration files: 0 violations
```

---

## 🧪 TESTING & VALIDATION

### Manual Tests

```
✅ Route erişilebilir mi? (200 OK)
✅ Dark mode çalışıyor mu?
✅ Responsive mi? (mobile, tablet, desktop)
✅ Form validation çalışıyor mu?
✅ Error handling var mı?
```

### Automated Tests

```bash
# Linter
npm run lint

# Context7 check
php artisan standard:check

# Build
npm run build
```

---

## 📋 PRE-COMMIT CHECKLIST

**Her commit öncesi kontrol et:**

- [ ] ✅ Pure Tailwind (Neo classes yok)
- [ ] ✅ Alpine.js (jQuery yok)
- [ ] ✅ Dark mode support
- [ ] ✅ Responsive design
- [ ] ✅ Form standards (labels, inputs, buttons)
- [ ] ✅ Component reusability
- [ ] ✅ Context7 compliance (0 violations)
- [ ] ✅ 0 lint errors
- [ ] ✅ Build successful

---

## 🚫 FORBIDDEN PATTERNS

```blade
<!-- ❌ Neo classes -->
<button class="neo-btn neo-btn-primary">

<!-- ❌ jQuery -->
$('#element').click(function() { ... })

<!-- ❌ Inline styles -->
<div style="color: red;">

<!-- ❌ Hard-coded colors -->
<div style="background: #000000;">

<!-- ❌ !important (sadece browser native için) -->
input { color: #000 !important; }
```

---

## ✅ REQUIRED PATTERNS

```blade
<!-- ✅ Pure Tailwind -->
<button class="px-4 py-2 bg-blue-600 text-white rounded-lg">

<!-- ✅ Alpine.js -->
<div x-data="{ open: false }" @click="open = !open">

<!-- ✅ Tailwind utilities -->
<div class="bg-white dark:bg-gray-800">

<!-- ✅ Component reuse -->
@include('admin.ilanlar.components.xxx')
```

---

## 📚 REFERENCES

- [Tailwind CSS Docs](https://tailwindcss.com/docs)
- [Alpine.js Docs](https://alpinejs.dev)
- [Context7 Authority](.context7/authority.json)
- [PHASE-1-COMPLETE-REPORT.md](PHASE-1-COMPLETE-REPORT.md)

---

**Bu standartlar ZORUNLU ve tüm yeni kodlarda uygulanmalıdır!**
