# CDN → Local Migration Rules

**Rule ID:** CDN-LOCAL-001  
**Created:** 13 Ekim 2025  
**Authority:** Context7 v3.5.0  
**Learned From:** Stable-Create Leaflet Migration

---

## 🎯 RULE: Always Prefer Local Over CDN

### **Why?**

1. ❌ CDN Dependency = Network dependency
2. ❌ External service downtime = Your site breaks
3. ❌ Privacy concerns (external requests)
4. ❌ Slower (2 requests: DNS + download)
5. ✅ Local = Faster, reliable, offline-ready

---

## 📊 WHEN TO MIGRATE CDN → LOCAL

### **Candidates:**

```
✅ CSS libraries (Leaflet, Bootstrap, etc.)
✅ JS libraries (Alpine.js, Chart.js, etc.)
✅ Fonts (Google Fonts → local)
✅ Icons (FontAwesome CDN → local)

❌ Keep CDN for:
  - Very large files (> 5MB)
  - Frequently updated (Google Maps API)
  - License restricted
```

---

## 🛠️ MIGRATION PROCESS

### **Step 1: Install Package**

```bash
npm install [package-name] --save
```

### **Step 2: Create Import File**

```css
/* resources/css/[library].css */
@import "[package-name]/dist/[library].css";
```

### **Step 3: Add to Vite**

```js
// vite.config.js
input: [
    "resources/css/[library].css",
    // ... other files
];
```

### **Step 4: Update Blade**

```blade
<!-- Before -->
<link rel="stylesheet" href="https://unpkg.com/package@1.0.0/dist/style.css" />

<!-- After -->
@vite(['resources/css/[library].css'])
```

### **Step 5: Test & Build**

```bash
npm run dev   # Test in dev
npm run build # Build for production
```

---

## ✅ LEAFLET EXAMPLE (SUCCESS CASE)

### **Before:**

```blade
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
```

-   External request to unpkg.com
-   Network dependency
-   CDN downtime risk

### **After:**

```bash
npm install leaflet
```

```css
/* resources/css/leaflet.css */
@import "leaflet/dist/leaflet.css";
```

```js
// vite.config.js
input: ["resources/css/leaflet.css"];
```

```blade
@vite(['resources/css/leaflet.css'])
```

**Result:**

-   ✅ No CDN dependency
-   ✅ Faster load
-   ✅ Offline support
-   ✅ Bundled & minified by Vite

---

## 🚨 YALIHAN BEKÇİ AUTO-DETECTION

### **Trigger:**

```regex
<link.*href=["']https?://.*unpkg\.com
<link.*href=["']https?://.*cdn\.jsdelivr\.net
<link.*href=["']https?://.*cdnjs\.cloudflare\.com
<script.*src=["']https?://.*unpkg\.com
<script.*src=["']https?://.*cdn\.jsdelivr\.net
```

### **Action:**

```
⚠️ CDN dependency detected!

File: resources/views/admin/example.blade.php
Line: 8
CDN: https://unpkg.com/leaflet@1.7.1/dist/leaflet.css

Öneri:
  1. npm install leaflet
  2. Create resources/css/leaflet.css
  3. @import 'leaflet/dist/leaflet.css'
  4. Add to vite.config.js
  5. Replace with @vite(['resources/css/leaflet.css'])

Benefit: Faster, more reliable, offline-ready
```

---

## 📋 EXCEPTION CASES

### **Keep CDN When:**

**1. Google Maps API**

```blade
<!-- OK: Frequently updated, requires API key -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('...') }}"></script>
```

**Why:** Google updates frequently, API key required, large SDK

**2. External Analytics**

```blade
<!-- OK: Privacy-conscious alternative only -->
<script async src="https://analytics.example.com/script.js"></script>
```

**Why:** Must be on their domain for analytics

**3. Very Large Assets (> 5MB)**

```blade
<!-- OK: Too large for bundle -->
<script src="https://cdn.example.com/huge-library-10mb.js"></script>
```

**Why:** Bundle size concern

---

## 🎯 BENEFITS SUMMARY

| Aspect          | CDN                 | Local              |
| --------------- | ------------------- | ------------------ |
| **Speed**       | Slower (2 requests) | Faster (bundled)   |
| **Reliability** | CDN downtime risk   | 100% reliable      |
| **Offline**     | ❌ Fails            | ✅ Works           |
| **Privacy**     | External requests   | No external        |
| **Control**     | No control          | Full control       |
| **Bundle**      | Separate request    | Bundled & minified |

**Winner:** Local ✅

---

## 📚 LEARNED FROM

**Case:** Stable-Create Leaflet Migration  
**Date:** 13 Ekim 2025  
**Result:** Success ✅  
**Time:** 30 minutes  
**Bundle Impact:** +15KB gzipped (acceptable)

---

## 🔄 FUTURE MIGRATIONS

**Candidates in Codebase:**

```bash
# Check all CDN usage
grep -r "unpkg.com\|cdn.jsdelivr.net\|cdnjs.cloudflare.com" resources/views/
```

**Priority List:**

1. CSS libraries (easiest, biggest win)
2. Small JS libraries (< 100KB)
3. Fonts
4. Icons

**Leave as CDN:**

-   Google Maps
-   Google Fonts (until font-display support)
-   External analytics
-   Very large libraries

---

**Rule Status:** ✅ Active  
**Enforcement:** Yalıhan Bekçi Auto-Detection  
**Context7 Compliance:** v3.5.0+

