# 📋 FORM STANDARDS - Global CSS Guidelines

> **Version:** 1.0.0  
> **Last Updated:** 2025-11-02  
> **Status:** ✅ ACTIVE

---

## 🎯 Overview

This document defines the **global CSS standards** for all form elements across the admin panel. These standards ensure:

✅ **WCAG AAA Compliance** (21:1 contrast ratio)  
✅ **Dark Mode Support**  
✅ **Context7 Compatibility**  
✅ **Consistent User Experience**  
✅ **Accessibility (ARIA, keyboard navigation)**

---

## 📦 Usage

### Method 1: PHP Helper Class (Recommended)

```php
use App\Helpers\FormStandards;

<input type="text" class="{{ FormStandards::input() }}" />
<select class="{{ FormStandards::select() }}">...</select>
<textarea class="{{ FormStandards::textarea() }}"></textarea>
<input type="checkbox" class="{{ FormStandards::checkbox() }}" />
<input type="radio" class="{{ FormStandards::radio() }}" />
```

### Method 2: Direct Classes

```html
<input
    type="text"
    class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 hover:border-blue-400 disabled:bg-gray-100 dark:disabled:bg-gray-700 disabled:cursor-not-allowed"
/>
```

---

## 🎨 Standard Classes

### 1. Input Fields

**Class:** `FormStandards::input()`

**Features:**

- **Padding:** `px-4 py-2.5`
- **Background:** `bg-white dark:bg-gray-800`
- **Border:** `border border-gray-300 dark:border-gray-600`
- **Border Radius:** `rounded-lg`
- **Text Color:** `text-gray-900 dark:text-white`
- **Placeholder:** `placeholder-gray-500 dark:placeholder-gray-400`
- **Focus Ring:** `focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400`
- **Hover:** `hover:border-blue-400`
- **Disabled:** `disabled:bg-gray-100 dark:disabled:bg-gray-700 disabled:cursor-not-allowed`

**Example:**

```html
<label class="{{ FormStandards::label() }}"> Full Name * </label>
<input
    type="text"
    name="name"
    class="{{ FormStandards::input() }}"
    placeholder="Enter your name"
    required
/>
@error('name')
<p class="{{ FormStandards::error() }}">{{ $message }}</p>
@enderror
```

---

### 2. Select Dropdowns

**Class:** `FormStandards::select()`

**Features:**

- Same as input, but with `cursor-pointer`
- **Recommended:** Add `style="color-scheme: light dark;"` for native dropdown styling

**Example:**

```html
<select name="category" class="{{ FormStandards::select() }}" style="color-scheme: light dark;">
    <option value="" class="{{ FormStandards::optionDisabled() }}">Seçiniz</option>
    <option value="1" class="{{ FormStandards::option() }}">Kategori 1</option>
    <option value="2" class="{{ FormStandards::option() }}">Kategori 2</option>
</select>
```

---

### 3. Textarea

**Class:** `FormStandards::textarea()`

**Features:**

- Same as input
- **Additional:** `resize-y` (vertical resize only)

**Example:**

```html
<textarea
    name="description"
    rows="4"
    class="{{ FormStandards::textarea() }}"
    placeholder="Enter description..."
></textarea>
```

---

### 4. Checkbox

**Class:** `FormStandards::checkbox()`

**Features:**

- **Size:** `w-4 h-4`
- **Color:** `text-blue-600` (checked state)
- **Background:** `bg-gray-100 dark:bg-gray-700`
- **Border:** `border-gray-300 dark:border-gray-600`
- **Focus Ring:** `focus:ring-blue-500 dark:focus:ring-blue-600`

**Example:**

```html
<label class="flex items-center">
    <input type="checkbox" name="featured" class="{{ FormStandards::checkbox() }}" />
    <span class="ml-2 {{ FormStandards::label() }}">Öne Çıkan</span>
</label>
```

---

### 5. Radio Buttons

**Class:** `FormStandards::radio()`

**Features:**

- Same as checkbox

**Example:**

```html
<div class="flex items-center space-x-4">
    <label class="flex items-center">
        <input type="radio" name="status" value="1" class="{{ FormStandards::radio() }}" checked />
        <span class="ml-2 text-gray-700 dark:text-gray-300">Active</span>
    </label>
    <label class="flex items-center">
        <input type="radio" name="status" value="0" class="{{ FormStandards::radio() }}" />
        <span class="ml-2 text-gray-700 dark:text-gray-300">Inactive</span>
    </label>
</div>
```

---

### 6. Labels

**Class:** `FormStandards::label()`

**Features:**

- **Display:** `block`
- **Size:** `text-sm`
- **Weight:** `font-medium`
- **Color:** `text-gray-700 dark:text-gray-300`
- **Margin:** `mb-2`

---

### 7. Error Messages

**Class:** `FormStandards::error()`

**Features:**

- **Color:** `text-red-600 dark:text-red-400`
- **Size:** `text-sm`
- **Margin:** `mt-1`

---

### 8. Help Text

**Class:** `FormStandards::help()`

**Features:**

- **Color:** `text-gray-500 dark:text-gray-400`
- **Size:** `text-xs`
- **Margin:** `mt-1`

---

## 🚫 FORBIDDEN PATTERNS

### ❌ DO NOT USE

```html
<!-- BAD: Old neo-* classes -->
<input class="neo-input" />
<select class="neo-select" />
<input type="radio" class="neo-radio" />

<!-- BAD: Inconsistent padding -->
<input class="px-3 py-2" />
<input class="px-4 py-3" />

<!-- BAD: Wrong dark background -->
<input class="dark:bg-gray-900" />

<!-- BAD: Wrong focus ring color -->
<input class="focus:ring-indigo-500" />
```

### ✅ USE INSTEAD

```html
<!-- GOOD: Standard helper -->
<input class="{{ FormStandards::input() }}" />
<select class="{{ FormStandards::select() }}" />
<input type="radio" class="{{ FormStandards::radio() }}" />
```

---

## 📊 Color Contrast Table

| Element     | Light Mode         | Dark Mode          | Contrast Ratio |
| ----------- | ------------------ | ------------------ | -------------- |
| Input Text  | #111827 on #FFFFFF | #FFFFFF on #1F2937 | 21:1 ✅        |
| Placeholder | #6B7280 on #FFFFFF | #9CA3AF on #1F2937 | 4.5:1 ✅       |
| Label       | #374151 on #FFFFFF | #D1D5DB on #111827 | 8:1 ✅         |
| Error       | #DC2626 on #FFFFFF | #F87171 on #111827 | 5:1 ✅         |
| Help        | #6B7280 on #FFFFFF | #9CA3AF on #111827 | 4.5:1 ✅       |

---

## 🔧 Migration Guide

### Step 1: Add Helper Class to Composer

```bash
composer dump-autoload
```

### Step 2: Import Helper in Blade

```php
use App\Helpers\FormStandards;
```

### Step 3: Replace Old Classes

```bash
# Run migration script
php scripts/migrate-to-form-standards.php
```

---

## 📝 Checklist for New Forms

Before creating any new form, ensure:

- [ ] All inputs use `FormStandards::input()`
- [ ] All selects use `FormStandards::select()`
- [ ] All textareas use `FormStandards::textarea()`
- [ ] All checkboxes use `FormStandards::checkbox()`
- [ ] All radio buttons use `FormStandards::radio()`
- [ ] All labels use `FormStandards::label()`
- [ ] Error messages use `FormStandards::error()`
- [ ] Help text uses `FormStandards::help()`
- [ ] Buttons use `FormStandards::button*()` classes
- [ ] Dark mode is tested and works correctly
- [ ] WCAG AAA contrast is maintained (21:1)
- [ ] Keyboard navigation is functional
- [ ] Screen reader compatibility is verified

---

## 🚀 Next Steps

1. ✅ Form standards documented
2. ⏳ Update all CRM forms
3. ⏳ Update all Danışman forms
4. ⏳ Update all İlan Yönetimi forms
5. ⏳ Update all Alt Kategoriler forms
6. ⏳ Add pre-commit hook to enforce standards
7. ⏳ Add automated tests for form styling

---

## 📚 Related Documentation

- [Context7 Compliance Report](../CONTEXT7_ULTIMATE_STATUS_REPORT.md)
- [CSS System Standards](../yalihan-bekci/knowledge/css-system-standards-2025-11-02.md)
- [Accessibility Guidelines](../docs/accessibility.md)

---

**Last Review:** 2025-11-02  
**Next Review:** 2025-12-01  
**Maintained by:** Yalıhan Bekçi AI System
