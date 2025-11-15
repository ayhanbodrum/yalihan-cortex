# CSS Architecture Standards - Yalıhan Emlak

**Son Güncelleme:** 2025-11-04  
**Durum:** ACTIVE - Zorunlu Standartlar

---

## 🎯 TEMEL PRENSIP

> **Tailwind CSS yaklaşımını korumak için global `!important` kullanma!**

---

## ✅ DOĞRU YAKLAŞIM

### 1. @layer base ile Default Styles

```css
/* resources/css/app.css */
@layer base {
    /* ✅ DOĞRU: @apply ile Tailwind utility */
    input[type='text'],
    textarea {
        @apply text-black dark:text-white;
        @apply font-semibold;
    }

    input::placeholder {
        @apply text-gray-600 dark:text-gray-500;
        @apply font-semibold;
    }
}
```

**Avantajlar:**

- Tailwind cascade çalışıyor
- Form'larda explicit class'lar override edebiliyor
- Dark mode desteği kolay
- Maintainable

---

### 2. Tailwind Utility Classes (HTML'de)

```html
<!-- ✅ DOĞRU: Explicit utility class'lar -->
<input
    type="text"
    class="text-black dark:text-white font-semibold placeholder-gray-600 dark:placeholder-gray-500"
/>
```

**Avantajlar:**

- Tailwind yaklaşımına %100 uyumlu
- Override kolay
- Dark mode inline
- Okunabilir

---

### 3. Browser Native Controls

```css
/* resources/css/app.css */
/* ✅ DOĞRU: Browser native için !important ZORUNLU */
select {
    background-color: white !important;
    color: #111827 !important;
}

select option {
    background-color: white !important;
    color: #111827 !important;
}
```

**Neden `!important`?**

- Browser native rendering
- CSS ile override edilemiyor
- Bu durum normal ve gerekli

---

## ❌ YANLIŞ YAKLAŞIM

### 1. Global !important (YASAK!)

```css
/* ❌ YANLIŞ: Global !important */
input::placeholder {
    color: #4b5563 !important; /* Tailwind'i eziyor! */
}

input[type='text'] {
    color: #000000 !important; /* Tailwind'i eziyor! */
}
```

**Neden Yanlış?**

- Tailwind utility class'ları çalışmıyor
- Form'larda explicit class'lar ignore ediliyor
- Tailwind cascade kırılıyor
- Anti-pattern

---

### 2. Hard-coded Colors (YASAK!)

```css
/* ❌ YANLIŞ: Hard-coded renkler */
input {
    color: #000000; /* Tailwind utility yerine! */
}
```

**Neden Yanlış?**

- Dark mode zorlaşıyor
- Maintainability düşük
- Tailwind yaklaşımına ters

**Doğrusu:**

```css
/* ✅ DOĞRU: Tailwind utility */
input {
    @apply text-black dark:text-white;
}
```

---

## 📊 TAILWIND CASCADE SYSTEM

### Priority Order:

```
1. @layer base (default)         ← En düşük priority
2. Tailwind utility classes       ← Form'da explicit varsa kazanır
3. Inline styles                  ← En yüksek priority
```

### Örnek:

```css
/* @layer base - default */
@layer base {
    input {
        @apply text-black;
    } /* Default siyah */
}
```

```html
<!-- HTML - explicit class -->
<input class="text-blue-600" />
<!-- ✅ Mavi kazanır! -->
<input />
<!-- ✅ Siyah (default) -->
```

---

## 🔍 NASIL TEST EDİLİR?

### Test Case:

```html
<!-- Test: Tailwind utility çalışıyor mu? -->
<input type="text" class="text-blue-600 placeholder-red-500" placeholder="Test" />
```

**Beklenen Sonuç:**

- Input text: Mavi
- Placeholder: Kırmızı

**Eğer çalışmıyorsa:**

- app.css'de global `!important` var mı? → Kaldır!
- `@layer base` kullanılıyor mu? → Kullan!
- `@apply` ile Tailwind utility kullanılıyor mu? → Kullan!

---

## 🛠️ MIGRATION GUIDE

### Adım 1: Global !important'ları Bul

```bash
grep -r '!important' resources/css/ | grep -v select
```

### Adım 2: @layer base'e Taşı

```css
/* ÖNCESI */
input {
    color: #000000 !important;
}

/* SONRASI */
@layer base {
    input {
        @apply text-black;
    }
}
```

### Adım 3: Test Et

```html
<input class="text-blue-600" />
```

Mavi görünüyorsa ✅ başarılı!

### Adım 4: Build

```bash
npm run build
```

---

## 📋 CHECKLIST

**Yeni CSS Eklerken:**

- [ ] `!important` kullanmıyorum (select hariç)
- [ ] `@layer base` içinde `@apply` kullanıyorum
- [ ] Hard-coded renkler yerine Tailwind utility
- [ ] Dark mode için `dark:` variant ekledim
- [ ] Tailwind utility'leri test ettim

**Code Review:**

- [ ] Global `!important` yok (select hariç)
- [ ] `@layer base` içinde default'lar
- [ ] `@apply` ile Tailwind utility'leri
- [ ] Form'larda explicit class'lar çalışıyor

---

## 🚫 FORBIDDEN PATTERNS

```css
/* ❌ YASAKLI */
input {
    color: #000000 !important;
}
textarea::placeholder {
    color: #4b5563 !important;
}
.custom-input {
    background: white !important;
}
```

## ✅ REQUIRED PATTERNS

```css
/* ✅ ZORUNLU */
@layer base {
    input {
        @apply text-black dark:text-white;
    }
    textarea::placeholder {
        @apply text-gray-600 dark:text-gray-500;
    }
}

/* ✅ EXCEPTION: Browser native */
select {
    background-color: white !important;
}
```

---

## 📚 REFERANSLAR

1. [Tailwind CSS @layer Documentation](https://tailwindcss.com/docs/adding-custom-styles#using-css-and-layer)
2. [Tailwind CSS @apply Documentation](https://tailwindcss.com/docs/reusing-styles#extracting-classes-with-apply)
3. `TAILWIND_MIGRATION_SUCCESS_2025-11-04.md`

---

## 💡 KEY INSIGHTS

> **"!important is the enemy of Tailwind"**

> **"@layer base is the heart of Tailwind cascade"**

> **"@apply is the bridge between CSS and Tailwind"**

---

**Bu standartlar ZORUNLU ve tüm yeni CSS'lerde uygulanmalıdır!**
