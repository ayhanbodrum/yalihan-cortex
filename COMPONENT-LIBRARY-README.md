# 🎨 Component Library - Usage Guide

**Tarih:** 2025-11-04 (Gece)  
**Durum:** ✅ İlk 3 Component Hazır  
**Standart:** Pure Tailwind + Alpine.js + Dark Mode

---

## 📦 MEVCUT COMPONENTLER

### 1. Modal Component ✅

**Dosya:** `resources/views/components/modal.blade.php`

**Kullanım:**
```blade
<x-modal id="deleteModal" title="Confirm Delete" size="md">
    <p>Are you sure you want to delete this item?</p>
    
    <x-slot name="footer">
        <button @click="show = false" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
            Cancel
        </button>
        <button class="px-4 py-2 bg-red-600 text-white hover:bg-red-700 rounded-lg">
            Delete
        </button>
    </x-slot>
</x-modal>

{{-- Open/Close modal via JavaScript --}}
<button onclick="openModal('deleteModal')">Open Modal</button>
```

**Props:**
- `id` (required) - Unique modal identifier
- `title` (required) - Modal title
- `size` (optional) - sm, md, lg, xl, 2xl, full (default: md)
- `closeable` (optional) - Show close button (default: true)
- `show` (optional) - Initial visibility (default: false)

**Features:**
- ✅ Alpine.js transitions
- ✅ Backdrop blur
- ✅ ESC key to close
- ✅ Click outside to close
- ✅ Dark mode support
- ✅ WCAG AAA accessible
- ✅ Focus management

---

### 2. Checkbox Component ✅

**Dosya:** `resources/views/components/checkbox.blade.php`

**Kullanım:**
```blade
<x-checkbox
    name="featured"
    label="Featured Listing"
    :checked="old('featured', $ilan->featured ?? false)"
    help="Featured listings appear on the homepage"
/>

{{-- With error --}}
<x-checkbox
    name="terms"
    label="I agree to Terms & Conditions"
    :error="$errors->first('terms')"
    required
/>
```

**Props:**
- `name` (required) - Input name
- `label` (required) - Checkbox label
- `value` (optional) - Checkbox value (default: 1)
- `checked` (optional) - Checked state (default: false)
- `disabled` (optional) - Disabled state (default: false)
- `error` (optional) - Error message
- `help` (optional) - Help text

**Features:**
- ✅ Large touch-friendly size (20px)
- ✅ Focus ring (2px blue)
- ✅ Error state (red border)
- ✅ Help text
- ✅ Dark mode support
- ✅ WCAG AAA accessible

---

### 3. Radio Component ✅

**Dosya:** `resources/views/components/radio.blade.php`

**Kullanım:**
```blade
<div class="space-y-2">
    <x-radio
        name="status"
        label="Active"
        value="active"
        :checked="old('status', $ilan->status) === 'active'"
    />
    <x-radio
        name="status"
        label="Pending"
        value="pending"
        :checked="old('status', $ilan->status) === 'pending'"
    />
    <x-radio
        name="status"
        label="Inactive"
        value="inactive"
        :checked="old('status', $ilan->status) === 'inactive'"
    />
</div>
```

**Props:**
- `name` (required) - Input name (same for all radios in group)
- `label` (required) - Radio label
- `value` (required) - Radio value
- `checked` (optional) - Checked state (default: false)
- `disabled` (optional) - Disabled state (default: false)
- `error` (optional) - Error message
- `help` (optional) - Help text

**Features:**
- ✅ Standard size (16px)
- ✅ Focus ring (2px blue)
- ✅ Error state (red border)
- ✅ Help text
- ✅ Dark mode support
- ✅ WCAG AAA accessible

---

## 🎯 SONRAKI COMPONENTLER

### Hafta 1 (Kalan):
- [ ] Toggle.blade.php (switch button)
- [ ] Dropdown.blade.php (dropdown menu)
- [ ] File-upload.blade.php (drag & drop)

### Hafta 2:
- [ ] Tabs.blade.php (tab navigation)
- [ ] Accordion.blade.php (collapsible)
- [ ] Badge.blade.php (status badges)
- [ ] Alert.blade.php (notifications)

---

## ✅ STANDARTLAR

### CSS:
- ✅ Pure Tailwind CSS (no Neo classes)
- ✅ Dark mode support (dark:* variants)
- ✅ Focus states (focus:ring-2)
- ✅ Transitions (transition-* duration-200)
- ✅ Responsive (mobile-first)

### JavaScript:
- ✅ Alpine.js for interactivity
- ✅ No jQuery
- ✅ ES6+ syntax
- ✅ Global helper functions (@once)

### Accessibility:
- ✅ ARIA labels
- ✅ Keyboard navigation
- ✅ Focus management
- ✅ Screen reader support
- ✅ Error announcements (role="alert")

### Colors:
- ✅ Primary: Blue (blue-600)
- ✅ Success: Green (green-600)
- ✅ Danger: Red (red-600)
- ✅ Warning: Yellow (yellow-600)
- ✅ Gray scale: gray-50 to gray-900

---

## 🧪 TESTING

### Manual Testing Checklist:
- [ ] Light mode görünüm
- [ ] Dark mode görünüm
- [ ] Focus states (Tab navigation)
- [ ] Keyboard actions (Enter, Space, ESC)
- [ ] Error states
- [ ] Disabled states
- [ ] Mobile responsive (< 640px)
- [ ] Tablet responsive (640px - 1024px)
- [ ] Desktop responsive (> 1024px)

---

## 📚 DOCUMENTATION

### Component Documentation Format:
```blade
{{--
    Component Name
    
    @component x-component-name
    @description One-line description
    
    @props
        - prop1: type (required/optional) - description
        - prop2: type (required/optional) - description
    
    @example
        <x-component-name prop1="value" prop2="value" />
    
    @accessibility
        - Feature 1
        - Feature 2
--}}
```

---

## 🚀 MIGRATION STRATEGY

### Touch and Convert:
```yaml
Yeni sayfa yazarken:
  ✅ Component kullan
  ❌ Manuel HTML yazma

Var olan sayfayı düzeltirken:
  ✅ Manuel HTML → Component'e dönüştür
  ✅ Test et
  ✅ Commit et

Çalışan sayfaya dokunmuyorsan:
  ⏸️ Olduğu gibi bırak (sonra migrate ederiz)
```

---

## 💡 BEST PRACTICES

### DO (Yap):
- ✅ Component kullan (mümkün olduğunca)
- ✅ Props doğru kullan
- ✅ Error handling ekle
- ✅ Help text ekle (kullanıcı için)
- ✅ Dark mode test et
- ✅ Keyboard navigation test et

### DON'T (Yapma):
- ❌ Manuel HTML (component varsa)
- ❌ Inline styles
- ❌ !important
- ❌ jQuery
- ❌ Neo classes (deprecated)

---

## 🎊 SONUÇ

**Mevcut:** 3 component (Modal, Checkbox, Radio)  
**Kalan:** 7 component  
**Hedef:** 10 component (1 hafta)

**İlerleme:** %30 ✅

---

**Hazırlayan:** AI Assistant  
**Tarih:** 2025-11-04 (Gece)  
**Durum:** ✅ İLK 3 COMPONENT HAZIR

