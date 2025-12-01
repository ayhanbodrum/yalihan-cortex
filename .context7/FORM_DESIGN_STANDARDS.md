# 📋 CONTEXT7 FORM DESIGN STANDARDS

## Tarih: 30 Kasım 2025
## Versiyon: 2.0.0 (Elegant & Compact Redesign)
## Durum: ✅ ACTIVE - ZORUNLU

---

## 🎨 **TASARIM FELSEFESİ: ELEGANT & COMPACT**

Context7 formları, kullanıcı odaklı, modern ve zarif bir deneyim sunmayı hedefler.
**Ana Prensipler:**
1.  **Kompakt:** Gereksiz boşluklardan kaçın (`py-2.5`, `space-y-4`).
2.  **Subtle (Hafif):** İnce kenarlıklar (`border`), hafif gölgeler (`shadow-sm`).
3.  **Hiyerarşik:** Font ağırlıklarıyla bilgi hiyerarşisi (`font-bold` yerine `font-medium` label).
4.  **Odaklı:** Kullanıcının içeriğe odaklanmasını sağlayan temiz zeminler.

---

## �️ **YAPI VE SPACING**

### **Card Structure**

```html
<!-- ✅ DOĞRU: Kompakt ve Zarif -->
<div class="bg-white dark:bg-gray-800 
            rounded-lg shadow-sm 
            border border-gray-200 dark:border-gray-700 
            transition-all duration-300 ease-in-out
            hover:shadow-md">
    <!-- Content -->
</div>
```

### **Card Header**

```html
<div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700 
            bg-gradient-to-r from-gray-50 to-white
            dark:from-gray-800 dark:to-gray-800
            rounded-t-lg">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <!-- Icon (Size: w-5 h-5) -->
            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-md flex items-center justify-center mr-3 text-blue-600 dark:text-blue-400">
                <svg class="w-5 h-5" ...></svg>
            </div>
            Başlık
        </h2>
    </div>
</div>
```

### **Spacing Scale**

- **Section Arası:** `space-y-4` (Eski: `space-y-6`)
- **Grid Gap:** `gap-4` (Eski: `gap-6`)
- **Padding:** `p-5` (Eski: `p-6` veya `p-8`)
- **Label Margin:** `mb-1.5` (Eski: `mb-2`)

---

## 📝 **FORM INPUT STANDARDI**

### **Label (Medium + Subtle)**

```html
<label for="field_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
    Alan Adı <span class="text-red-500">*</span>
</label>
```

### **Input & Select (Kompakt + İnce Border)**

```html
<input 
    type="text" 
    class="w-full px-4 py-2.5 text-sm
           border border-gray-300 dark:border-gray-600
           rounded-lg
           bg-white dark:bg-gray-900
           text-gray-900 dark:text-white
           placeholder-gray-400 dark:placeholder-gray-500
           focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
           hover:border-gray-400 dark:hover:border-gray-500
           transition-colors duration-200"
    placeholder="..."
>
```

### **Select (Dropdown Readability Fix)**

```html
<select 
    class="..." 
    style="color-scheme: light dark;"
>
    <option value="" class="bg-gray-50 dark:bg-gray-800 text-gray-500">Seçiniz</option>
    <option value="1" class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white">Seçenek 1</option>
</select>
```

---

## 🔍 **LIVE SEARCH STANDARDI**

```html
<div class="context7-live-search relative">
    <input type="text" class="..." placeholder="🔍 Ara...">
    
    <!-- Dropdown: Absolute + Z-Index + Shadow-lg (Sadece dropdown için gölge büyük olabilir) -->
    <div class="context7-search-results 
                absolute z-[9999] w-full mt-1 
                bg-white dark:bg-gray-800 
                border border-gray-200 dark:border-gray-700 
                rounded-lg shadow-xl 
                hidden max-h-60 overflow-y-auto">
    </div>
</div>
```

---

## 🎨 **RENK VE DURUM PALETİ**

### **Border Colors**
- **Default:** `border-gray-300` (Light) / `border-gray-600` (Dark)
- **Hover:** `hover:border-gray-400` (Light) / `hover:border-gray-500` (Dark)
- **Focus:** `focus:border-blue-500`

### **Backgrounds**
- **Page:** `bg-gray-50` (Light) / `bg-gray-900` (Dark)
- **Card:** `bg-white` (Light) / `bg-gray-800` (Dark)
- **Input:** `bg-white` (Light) / `bg-gray-900` (Dark) - **DİKKAT: Inputlar card ile aynı değil, daha koyu/açık olabilir**

### **Shadows**
- **Card:** `shadow-sm` -> `hover:shadow-md`
- **Input:** `shadow-sm` (Opsiyonel, genelde shadowsuz flat tercih edilir)
- **Dropdown/Modal:** `shadow-xl`

---

## 🚫 **YASAKLANANLAR (ESKİ TASARIM)**

- ❌ `border-2` (Çok kalın)
- ❌ `rounded-xl`, `rounded-2xl` (Çok yuvarlak, `rounded-lg` kullanın)
- ❌ `py-4`, `py-3` (Çok yüksek, `py-2.5` kullanın)
- ❌ `font-bold` Label (Çok baskın, `font-medium` kullanın)
- ❌ `shadow-2xl` (Gereksiz derinlik, sadece modallarda kullanın)

---

## 📚 **REFERANSLAR**

- **Tasarım Kaynağı:** `DESIGN_OPTIMIZATION_RECOMMENDATIONS.md` (Bu standarta entegre edildi)
- **Yetkili Belge:** `.context7/authority.json`

---

**Yalıhan Bekçi:** Bu standartlar 30 Kasım 2025 itibariyle tüm yeni formlarda geçerlidir. Eski formlar refactor edilmelidir.
