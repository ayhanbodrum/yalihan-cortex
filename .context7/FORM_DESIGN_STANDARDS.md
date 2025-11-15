# 📋 CONTEXT7 FORM DESIGN STANDARDS

## Tarih: 2025-11-01

## Versiyon: 1.1.0 (Dropdown Readability Fix - 2025-11-01)

---

## 🎨 **LOKASYON FORM STANDARDI**

### **Card Structure**

```html
<!-- 📍 LOKASYON BİLGİLERİ - Context7 Standart Form Pattern -->
<div
    class="bg-white dark:bg-gray-800 
            rounded-xl shadow-lg 
            border-2 border-gray-200 dark:border-gray-600 
            transition-all duration-300 ease-in-out
            hover:shadow-2xl hover:-translate-y-1"
></div>
```

### **Card Header (Mandatory)**

```html
<div
    class="px-6 py-4 border-b-2 border-gray-200 dark:border-gray-600 
            bg-gradient-to-r from-green-50 via-teal-50 to-emerald-50
            dark:from-gray-700 dark:via-gray-700 dark:to-gray-700
            rounded-t-xl"
>
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
            <div
                class="w-10 h-10 bg-green-500 dark:bg-green-600 rounded-lg flex items-center justify-center mr-3 
                        shadow-lg transform hover:scale-110 transition-all duration-200"
            >
                <svg
                    class="w-6 h-6 text-white"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                    />
                </svg>
            </div>
            <span class="text-gray-900 dark:text-gray-100">Lokasyon Bilgileri</span>
        </h2>
        <span
            class="px-3 py-1 text-xs font-semibold bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full"
        >
            İl / İlçe / Mahalle
        </span>
    </div>
</div>
```

### **Card Body**

```html
<div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-b-xl">
    <div class="space-y-6">
        <!-- Content here -->
    </div>
</div>
```

---

## 📝 **FORM INPUT STANDARDI**

### **Label (Bold + Icon + Contrast)**

```html
<label
    for="il_id"
    class="block text-sm font-bold text-gray-800 dark:text-gray-100 mb-2
                         flex items-center gap-2"
>
    <span class="flex items-center gap-1.5">
        <svg
            class="w-4 h-4 text-green-600 dark:text-green-400"
            fill="currentColor"
            viewBox="0 0 20 20"
        >
            <path
                fill-rule="evenodd"
                d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                clip-rule="evenodd"
            />
        </svg>
        İl
    </span>
    <span class="text-red-600 dark:text-red-400 font-extrabold text-base ml-1">*</span>
</label>
```

### **Select Field (High Contrast + Transitions + READABLE OPTIONS)**

> 🚨 **CRITICAL FIX (2025-11-01):** Dropdown option'ları browser native dropdown'da okunmuyor!
> **Çözüm:** `color-scheme: light dark;` + explicit option styling + `dark:bg-gray-900` (not gray-800)

```html
<select
    id="il_id"
    name="il_id"
    class="w-full px-4 py-3
               border-2 border-gray-300 dark:border-gray-500
               rounded-lg 
               bg-white dark:bg-gray-900
               text-gray-900 dark:text-white
               font-medium
               placeholder-gray-400 dark:placeholder-gray-500
               focus:ring-4 focus:ring-green-500/50 focus:border-green-500
               hover:border-green-400 dark:hover:border-green-500
               transition-all duration-200 ease-in-out
               cursor-pointer
               shadow-sm hover:shadow-md"
    style="color-scheme: light dark;"
    required
    x-model="form.il_id"
    @change="loadIlceler()"
>
    <option value="" class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 py-2">
        -- İl Seçiniz --
    </option>
    @foreach ($iller ?? [] as $il)
    <option
        value="{{ $il->id }}"
        class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-2 font-medium"
    >
        {{ $il->name }}
    </option>
    @endforeach
</select>
```

**KEY CHANGES:**

1. `dark:bg-gray-800` → `dark:bg-gray-900` (daha koyu arka plan)
2. `dark:text-gray-100` → `dark:text-white` (tam beyaz text, daha belirgin)
3. `style="color-scheme: light dark;"` (browser native dropdown rendering fix)
4. **Option Styling:** Her option'a explicit class eklendi:
    - Placeholder: `bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 py-2`
    - Normal: `bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-2 font-medium`

### **Disabled State (Important!)**

```html
<select
    :disabled="!form.il_id"
    class="disabled:bg-gray-100 dark:disabled:bg-gray-700 
               disabled:border-gray-200 dark:disabled:border-gray-600
               disabled:text-gray-400 dark:disabled:text-gray-500
               disabled:cursor-not-allowed"
></select>
```

### **Error Message (High Visibility)**

```html
@error('il_id')
<div
    class="mt-2 px-3 py-2 text-sm font-medium
                text-red-700 dark:text-red-300
                bg-red-50 dark:bg-red-900/30
                border border-red-200 dark:border-red-800
                rounded-lg
                flex items-start gap-2"
>
    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path
            fill-rule="evenodd"
            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
            clip-rule="evenodd"
        />
    </svg>
    <span>{{ $message }}</span>
</div>
@enderror
```

### **Info/Help Badge (Context7 Standard)**

```html
<div
    class="mt-2 px-3 py-2 text-xs font-medium
            text-blue-700 dark:text-blue-300
            bg-blue-50 dark:bg-blue-900/30
            border border-blue-200 dark:border-blue-800
            rounded-lg flex items-start gap-2"
>
    <svg
        class="w-4 h-4 mt-0.5 text-blue-600 dark:text-blue-400 flex-shrink-0"
        fill="currentColor"
        viewBox="0 0 20 20"
    >
        <path
            fill-rule="evenodd"
            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
            clip-rule="evenodd"
        />
    </svg>
    <span
        ><strong>Context7 Standart:</strong> Mahalle verileri database'den dinamik yüklenir
        (mahalle_id)</span
    >
</div>
```

---

## 🔍 **CONTEXT7 LIVE SEARCH STANDARDI**

### **Critical: Relative Position**

```html
<!-- CRITICAL FIX: relative position for absolute dropdown -->
<div class="context7-live-search relative" data-search-type="kisiler" data-max-results="20">
    <input type="hidden" id="kisi_id" name="kisi_id" x-model="form.kisi_id" />
    <input
        type="text"
        class="w-full px-4 py-3
                  border-2 border-gray-300 dark:border-gray-500
                  rounded-lg 
                  bg-white dark:bg-gray-800
                  text-gray-900 dark:text-gray-100
                  font-medium
                  placeholder-gray-400 dark:placeholder-gray-500
                  focus:ring-4 focus:ring-purple-500/50 focus:border-purple-500
                  hover:border-purple-400 dark:hover:border-purple-500
                  transition-all duration-200 ease-in-out
                  shadow-sm hover:shadow-md"
        placeholder="🔍 Ad, soyad, telefon ile ara..."
        autocomplete="off"
    />

    <!-- Dropdown Results - Absolute positioned -->
    <div
        class="context7-search-results 
                absolute z-[9999] w-full mt-1 
                bg-white dark:bg-gray-800 
                border-2 border-purple-300 dark:border-purple-700 
                rounded-lg shadow-2xl 
                hidden max-h-96 overflow-y-auto
                animate-fadeIn"
    ></div>
</div>
```

### **Script Loading Order (CRITICAL)**

```html
{{-- Context7 Scripts - DEFER for proper loading order --}}
<script src="{{ asset('js/context7-live-search.js') }}" defer></script>
<script src="{{ asset('js/context7-location-system.js') }}" defer></script>
```

**Why DEFER?**

- Ensures DOM is fully loaded before script execution
- Prevents Alpine.js conflicts
- Maintains proper initialization order

---

## ⚠️ **COMMON MISTAKES TO AVOID**

### **❌ WRONG: Low Contrast Labels**

```html
<!-- BAD -->
<label class="text-gray-700 dark:text-gray-300">İl</label>
```

### **✅ RIGHT: High Contrast Labels**

```html
<!-- GOOD -->
<label class="text-gray-800 dark:text-gray-100 font-bold">İl</label>
```

---

### **❌ WRONG: Unreadable Dropdown Options**

```html
<!-- BAD - Option'lar dark mode'da okunmuyor! -->
<select class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
    <option value="">-- İl Seçiniz --</option>
    <option value="1">Aydın</option>
</select>
```

### **✅ RIGHT: Readable Dropdown Options**

```html
<!-- GOOD - Dark mode'da option'lar net okunuyor! -->
<select
    class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
    style="color-scheme: light dark;"
>
    <option value="" class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 py-2">
        -- İl Seçiniz --
    </option>
    <option
        value="1"
        class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-2 font-medium"
    >
        Aydın
    </option>
</select>
```

**Why this fix works:**

- `color-scheme: light dark;` tells browser to render native dropdown in dark mode
- `dark:bg-gray-900` (not gray-800) provides better contrast
- `dark:text-white` (not text-gray-100) ensures maximum readability
- Explicit option classes override browser defaults

---

### **❌ WRONG: Missing Relative Position**

```html
<!-- BAD - Dropdown won't position correctly -->
<div class="context7-live-search">
    <div class="context7-search-results absolute ..."></div>
</div>
```

### **✅ RIGHT: Parent Relative, Child Absolute**

```html
<!-- GOOD -->
<div class="context7-live-search relative">
    <div class="context7-search-results absolute ..."></div>
</div>
```

---

### **❌ WRONG: Scripts Without Defer**

```html
<!-- BAD - May load before DOM ready -->
<script src="{{ asset('js/context7-live-search.js') }}"></script>
```

### **✅ RIGHT: Scripts With Defer**

```html
<!-- GOOD - Waits for DOM -->
<script src="{{ asset('js/context7-live-search.js') }}" defer></script>
```

---

## 🎯 **YALIHAN BEKÇİ CHECKLIST**

### **Form Design**

- [ ] Card header: 2px border, gradient background, icon badge
- [ ] Labels: **font-bold**, high contrast (gray-800/gray-100)
- [ ] Inputs: **border-2**, **py-3** (not py-2.5), **font-medium**
- [ ] Disabled states: Gray background, reduced opacity
- [ ] Error messages: Red background box with icon
- [ ] Info badges: Colored background boxes with icons
- [ ] Transitions: **ALL** interactive elements

### **Live Search**

- [ ] Parent div: **relative** position
- [ ] Dropdown: **absolute** position, **z-[9999]**
- [ ] Scripts: **defer** attribute
- [ ] Debug logs: Enabled in development
- [ ] API endpoint: /api/kisiler/search

### **Dark Mode**

- [ ] Text: gray-900 → gray-100
- [ ] Backgrounds: gray-50 → gray-900
- [ ] Borders: gray-300 → gray-500
- [ ] Disabled: gray-100 → gray-700
- [ ] All colors tested in dark mode

---

## 📚 **REFERENCE FILES**

- **Master Example**: `resources/views/admin/talepler/create.blade.php`
- **Authority**: `.context7/authority.json` (v5.0.0+)
- **Live Search**: `public/js/context7-live-search.js`

---

## 🔄 **VERSION HISTORY**

| Version | Date       | Changes                                |
| ------- | ---------- | -------------------------------------- |
| 1.1.0   | 2025-11-01 | **CRITICAL FIX:** Dropdown readability |
|         |            | `color-scheme: light dark;` added      |
|         |            | `dark:bg-gray-900` + `dark:text-white` |
|         |            | Explicit option class styling          |
|         |            | TÜM dropdown'lar düzeltildi            |
| 1.0.0   | 2025-11-01 | Initial lokasyon form standard         |
|         |            | Live search positioning fix            |
|         |            | Script loading order fix               |
|         |            | High contrast dark mode                |

---

**Yalıhan Bekçi:** Bu standartlar TÜM form sayfalarında uygulanmalıdır!
