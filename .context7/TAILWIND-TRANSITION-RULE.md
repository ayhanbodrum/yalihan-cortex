# 🎨 Tailwind CSS + Transition Zorunlu Kuralı

**Tarih:** 1 Kasım 2025  
**Versiyon:** Context7 v5.0.0  
**Durum:** AKTIF - ZORUNLU  
**Etki:** TÜM Sayfalar ve Component'ler

---

## 🚨 BREAKING CHANGE

### **Neo Design System → Pure Tailwind CSS Geçişi**

```yaml
❌ YASAK (Artık Kullanılmayacak):
    - neo-btn
    - neo-card
    - neo-input
    - neo-select
    - neo-form-group
    - neo-label
    - neo-textarea
    - neo-badge
    - neo-* (tüm Neo class'ları)

✅ ZORUNLU:
    - Pure Tailwind utility classes
    - Transition/animation her işlemde
    - Dark mode variant'ları
    - Responsive breakpoint'ler
    - Hover/focus/active states
```

---

## ✅ YENİ STANDARTLAR

### **1. Button Standardı**

```html
<!-- ❌ ESKİ (YASAK) -->
<button class="neo-btn neo-btn-primary">Kaydet</button>

<!-- ✅ YENİ (ZORUNLU) -->
<button
    class="px-4 py-2.5 bg-blue-600 text-white rounded-lg 
               hover:bg-blue-700 hover:scale-105 
               active:scale-95 
               focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
               transition-all duration-200 ease-in-out
               shadow-md hover:shadow-lg
               disabled:opacity-50 disabled:cursor-not-allowed
               dark:bg-blue-500 dark:hover:bg-blue-600"
>
    Kaydet
</button>
```

### **2. Card Standardı**

```html
<!-- ❌ ESKİ (YASAK) -->
<div class="neo-card">
    <div class="neo-card-header">Başlık</div>
    <div class="neo-card-body">İçerik</div>
</div>

<!-- ✅ YENİ (ZORUNLU) -->
<div
    class="bg-white dark:bg-gray-800 
            rounded-xl shadow-lg 
            border border-gray-200 dark:border-gray-700 
            transition-all duration-300 ease-in-out
            hover:shadow-xl hover:-translate-y-1"
>
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Başlık</h2>
    </div>
    <div class="p-6">İçerik</div>
</div>
```

### **3. Input Standardı**

```html
<!-- ❌ ESKİ (YASAK) -->
<input class="neo-input" type="text" />

<!-- ✅ YENİ (ZORUNLU) -->
<input
    type="text"
    class="w-full px-4 py-2.5 
              border border-gray-300 dark:border-gray-600 
              rounded-lg 
              bg-white dark:bg-gray-800 
              text-gray-900 dark:text-white
              placeholder-gray-400 dark:placeholder-gray-500
              focus:ring-2 focus:ring-blue-500 focus:border-transparent
              transition-all duration-200 ease-in-out
              disabled:bg-gray-100 dark:disabled:bg-gray-700 
              disabled:cursor-not-allowed"
/>
```

### **4. Select Standardı**

```html
<!-- ❌ ESKİ (YASAK) -->
<select class="neo-select">
    <option>Seçiniz</option>
</select>

<!-- ✅ YENİ (ZORUNLU) -->
<select
    class="w-full px-4 py-2.5 
               border border-gray-300 dark:border-gray-600 
               rounded-lg 
               bg-white dark:bg-gray-800 
               text-gray-900 dark:text-white
               focus:ring-2 focus:ring-blue-500 focus:border-transparent
               transition-all duration-200 ease-in-out
               cursor-pointer
               disabled:bg-gray-100 dark:disabled:bg-gray-700 
               disabled:cursor-not-allowed"
>
    <option>Seçiniz</option>
</select>
```

---

## 🎬 ZORUNLU TRANSITION/ANIMATION KURALLARI

### **1. Her Interactive Element:**

```yaml
transition-all duration-200 ease-in-out  ← ZORUNLU!
```

### **2. Hover States:**

```yaml
hover:bg-{color}-700     ← Renk değişimi
hover:scale-105          ← Büyüme efekti
hover:shadow-lg          ← Gölge artışı
hover:-translate-y-1     ← Yukarı hareket (card'lar için)
```

### **3. Active States:**

```yaml
active:scale-95          ← Tıklanma efekti
active:shadow-inner      ← İç gölge
```

### **4. Focus States:**

```yaml
focus:ring-2 focus:ring-blue-500 focus:ring-offset-2  ← ZORUNLU!
focus:border-transparent  ← Border kaldır, ring kullan
```

### **5. Disabled States:**

```yaml
disabled:opacity-50               ← Yarı saydam
disabled:cursor-not-allowed       ← Cursor değişimi
disabled:bg-gray-100              ← Gri arkaplan
```

### **6. Loading States:**

```yaml
<!-- Spinner -->
<svg class="animate-spin h-5 w-5 text-white">...</svg>

<!-- Pulse -->
<div class="animate-pulse bg-gray-200 rounded">...</div>

<!-- Bounce -->
<div class="animate-bounce">↓</div>
```

---

## 🌓 DARK MODE ZORUNLU

Her element için dark mode variant ZORUNLU:

```html
bg-white dark:bg-gray-800 text-gray-900 dark:text-white border-gray-200 dark:border-gray-700
```

---

## 📱 RESPONSIVE ZORUNLU

Mobile-first approach:

```html
<!-- Base: mobile -->
<div
    class="grid grid-cols-1 gap-4
            sm:grid-cols-2    <!-- 640px+ -->
            md:grid-cols-3    <!-- 768px+ -->
            lg:grid-cols-4    <!-- 1024px+ -->
            xl:grid-cols-6"
>
    <!-- 1280px+ -->
</div>
```

---

## 🎯 MİGRATION STRATEJİSİ

```yaml
Yeni Kod: → TAİLWIND ONLY (hemen)
    → Neo class yasak

Mevcut Kod: → Kademeli geçiş
    → Düzenlendiğinde Tailwind'e çevir
    → Neo CSS dosyalarını koru (geçici)

Öncelik Sırası: 1. Forms (en yüksek)
    2. Dashboards
    3. List pages
    4. Detail pages
    5. Components
    6. Modals
```

---

## 📋 COMPONENT ÖRNEKLERİ

### **Form Group**

```html
<!-- ❌ ESKİ -->
<div class="neo-form-group">
    <label class="neo-label">Alan</label>
    <input class="neo-input" />
</div>

<!-- ✅ YENİ -->
<div class="mb-6">
    <label
        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2
                  transition-colors duration-200"
    >
        Alan
    </label>
    <input
        class="w-full px-4 py-2.5 
                  border border-gray-300 dark:border-gray-600 
                  rounded-lg 
                  focus:ring-2 focus:ring-blue-500 
                  transition-all duration-200
                  bg-white dark:bg-gray-800
                  text-gray-900 dark:text-white"
    />
</div>
```

### **Action Buttons**

```html
<!-- Primary Button -->
<button
    class="px-6 py-3 
               bg-orange-600 text-white font-semibold
               rounded-lg shadow-md
               hover:bg-orange-700 hover:scale-105 hover:shadow-lg
               active:scale-95
               focus:ring-2 focus:ring-orange-500 focus:ring-offset-2
               transition-all duration-200 ease-in-out
               disabled:opacity-50 disabled:cursor-not-allowed
               dark:bg-orange-500 dark:hover:bg-orange-600"
>
    Kaydet
</button>

<!-- Secondary Button -->
<button
    class="px-6 py-3 
               bg-gray-200 text-gray-700 font-semibold
               rounded-lg shadow-sm
               hover:bg-gray-300 hover:scale-105
               active:scale-95
               transition-all duration-200 ease-in-out
               dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
>
    İptal
</button>
```

---

## 🚀 PERFORMANCE

```yaml
Bundle Size Hedefi:
    ✅ CSS: < 30KB gzipped (Tailwind JIT)
    ❌ Neo CSS: 45KB (KALDIRILACAK)

Transition Performance:
    ✅ GPU acceleration: transform, opacity
    ❌ CPU heavy: width, height, padding (mümkünse kaçın)
```

---

## ⚡ ENFORCEMENT

```yaml
Pre-commit Hook:
  → neo-* class'ları engelle
  → transition-* yoksa uyar
  → dark: variant yoksa uyar

Yalıhan Bekçi:
  → Otomatik tarama
  → İhlal bildirimi
  → Öneriler sun

IDE Integration:
  → Cursor, VSCode, Windsurf
  → Auto-suggest Tailwind
  → Neo class uyarısı
```

---

## 📚 REFERANSLAR

- **Tailwind Docs:** https://tailwindcss.com/docs
- **Transition Guide:** https://tailwindcss.com/docs/transition-property
- **Dark Mode:** https://tailwindcss.com/docs/dark-mode
- **Transforms:** https://tailwindcss.com/docs/transform

---

**ÖZET:** Neo Design KALDIRILDI. Tailwind ZORUNLU. Transition HER YERDE!
