# 🎯 COMPONENT KULLANIM ÖRNEKLERİ - GERÇEK SAYFALARDA

**Tarih:** 5 Kasım 2025  
**Component Library V1.0.0**

---

## 📌 **GERÇEKBu sayfalarda kullan component'leri:**

1. ✅ İlan Create/Edit - Checkbox'lar
2. ✅ Kişi Create/Edit - Radio buttons
3. ✅ Ayarlar Edit - Toggle switch

---

## 1️⃣ **İLAN CREATE/EDIT** - Checkbox Component

### **ÖNCEDEN (Eski):**
```blade
<!-- resources/views/admin/ayarlar/edit.blade.php - Satır 94-96 -->
<div class="flex items-center space-x-3">
    <input type="checkbox" name="is_public" value="1" id="is_public" 
           class="w-5 h-5 text-blue-600 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all duration-200 cursor-pointer"
           {{ old('is_public', $ayar->is_public ?? false) ? 'checked' : '' }}>
    <label for="is_public" class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
        Herkese açık ayar
    </label>
</div>
```

### **ŞİMDİ (Yeni Component):**
```blade
<x-checkbox
    name="is_public"
    label="Herkese Açık Ayar"
    :checked="old('is_public', $ayar->is_public ?? false)"
    help="Bu ayar frontend'de görünür olacak"
/>
```

**Kazanç:** 5 satır → 1 satır! 🎉

---

## 2️⃣ **AYARLAR EDİT** - Toggle Component

### **Ayarlar Sayfasında Boolean Değerler İçin**

**ÖNCEDEN (Manual Toggle):**
```blade
<!-- Kendi toggle kodumuz -->
<div class="flex items-center space-x-3">
    <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" name="maintenance_mode" class="sr-only peer" 
               {{ old('maintenance_mode') ? 'checked' : '' }}>
        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 
                    dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 
                    peer-checked:after:translate-x-full peer-checked:after:border-white 
                    after:content-[''] after:absolute after:top-[2px] after:left-[2px] 
                    after:bg-white after:border-gray-300 after:border after:rounded-full 
                    after:h-5 after:w-5 after:transition-all dark:border-gray-600 
                    peer-checked:bg-blue-600"></div>
        <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">
            Bakım Modu
        </span>
    </label>
</div>
```

**ŞİMDİ (Component):**
```blade
<x-admin.toggle
    name="maintenance_mode"
    label="Bakım Modu"
    :checked="old('maintenance_mode', setting('maintenance_mode', false))"
    help="Aktif olduğunda site bakım sayfası gösterir"
/>
```

**Kazanç:** 15 satır → 1 satır! 🚀

---

## 3️⃣ **KİŞİ CREATE** - Radio Button Group

### **Kişi Tipi Seçimi**

**ÖNCEDEN (Manual):**
```blade
<div class="space-y-3">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        Kişi Tipi
    </label>
    
    <div class="flex items-center">
        <input type="radio" name="kisi_tipi" value="bireysel" id="type_bireysel"
               class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
               {{ old('kisi_tipi') == 'bireysel' ? 'checked' : '' }}>
        <label for="type_bireysel" class="ml-2 text-sm text-gray-900 dark:text-white">
            Bireysel
        </label>
    </div>
    
    <div class="flex items-center">
        <input type="radio" name="kisi_tipi" value="kurumsal" id="type_kurumsal"
               class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
               {{ old('kisi_tipi') == 'kurumsal' ? 'checked' : '' }}>
        <label for="type_kurumsal" class="ml-2 text-sm text-gray-900 dark:text-white">
            Kurumsal
        </label>
    </div>
    
    <div class="flex items-center">
        <input type="radio" name="kisi_tipi" value="yabanci" id="type_yabanci"
               class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
               {{ old('kisi_tipi') == 'yabanci' ? 'checked' : '' }}>
        <label for="type_yabanci" class="ml-2 text-sm text-gray-900 dark:text-white">
            Yabancı Uyruklu
        </label>
    </div>
</div>
```

**ŞİMDİ (Component):**
```blade
<div class="space-y-3">
    <p class="text-sm font-medium text-gray-900 dark:text-white mb-3">Kişi Tipi</p>
    
    <x-radio
        name="kisi_tipi"
        label="Bireysel"
        value="bireysel"
        :checked="old('kisi_tipi', $kisi->kisi_tipi ?? '') === 'bireysel'"
    />

    <x-radio
        name="kisi_tipi"
        label="Kurumsal"
        value="kurumsal"
        :checked="old('kisi_tipi', $kisi->kisi_tipi ?? '') === 'kurumsal'"
    />

    <x-radio
        name="kisi_tipi"
        label="Yabancı Uyruklu"
        value="yabanci"
        :checked="old('kisi_tipi', $kisi->kisi_tipi ?? '') === 'yabanci'"
    />
</div>
```

**Kazanç:** 30 satır → 12 satır! ✨

---

## 4️⃣ **MODAL KULLANIMI** - Silme Onayı

### **İlan Silme Modal**

**ÖNCEDEN (Manuel Modal):**
```blade
<!-- 80+ satır modal HTML kodu -->
<div x-show="showDeleteModal" class="fixed inset-0 z-50 bg-black/50" ...>
    <div class="bg-white rounded-lg p-6">
        <h3>İlanı Sil</h3>
        <p>Emin misiniz?</p>
        <!-- ... daha fazla kod -->
    </div>
</div>
```

**ŞİMDİ (Component):**
```blade
<div x-data="{ showDeleteModal: false }">
    <button @click="showDeleteModal = true" class="px-4 py-2 bg-red-600 text-white rounded-lg">
        İlanı Sil
    </button>
    
    <x-admin.modal title="İlanı Sil" size="sm" bind="showDeleteModal">
        <p class="text-gray-700 dark:text-gray-300 mb-4">
            Bu ilanı silmek istediğinize emin misiniz? Bu işlem geri alınamaz!
        </p>
        
        <x-slot:footer>
            <div class="flex items-center justify-end gap-3">
                <button 
                    @click="showDeleteModal = false"
                    class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg"
                >
                    İptal
                </button>
                <form method="POST" action="{{ route('admin.ilanlar.destroy', $ilan->id) }}">
                    @csrf @method('DELETE')
                    <button 
                        type="submit"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg"
                    >
                        Evet, Sil
                    </button>
                </form>
            </div>
        </x-slot:footer>
    </x-admin.modal>
</div>
```

**Kazanç:** 80 satır → 15 satır! 🎯

---

## 5️⃣ **FILE UPLOAD** - Logo Yükleme

### **Ayarlar Sayfasında Logo Upload**

**ÖNCEDEN (Basic Input):**
```blade
<input type="file" name="logo" accept="image/*" class="...">
```

**ŞİMDİ (Drag & Drop Component):**
```blade
<x-admin.file-upload
    name="logo"
    label="Site Logosu"
    accept="image/*"
    :maxSize="2"
    help="PNG veya JPG formatında, maksimum 2MB"
/>
```

**Kazanç:**
- ✅ Drag & drop otomatik
- ✅ Image preview otomatik
- ✅ Size validation otomatik
- ✅ Beautiful UI otomatik

---

## 6️⃣ **ALERT COMPONENT** - Flash Messages

### **Başarı/Hata Mesajları**

**ÖNCEDEN (Manuel Alert):**
```blade
@if (session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800">
        {{ session('error') }}
    </div>
@endif
```

**ŞİMDİ (Component):**
```blade
@if (session('success'))
    <x-admin.alert type="success">
        {{ session('success') }}
    </x-admin.alert>
@endif

@if (session('error'))
    <x-admin.alert type="error">
        {{ session('error') }}
    </x-admin.alert>
@endif
```

**Kazanç:** Daha temiz + otomatik icon + dark mode!

---

## 📊 **TOPLAM KAZANÇ**

```yaml
Kod Azalması:
  - Modal: 80 satır → 15 satır (%81 azalma)
  - Radio Group: 30 satır → 12 satır (%60 azalma)
  - Toggle: 15 satır → 1 satır (%93 azalma)
  - Checkbox: 5 satır → 1 satır (%80 azalma)

Özellikler Kazancı:
  ✅ Dark mode: Otomatik
  ✅ Accessibility: Otomatik (ARIA)
  ✅ Validation: Otomatik
  ✅ Error handling: Otomatik
  ✅ Help text: Otomatik

Bakım Kolaylığı:
  ✅ Bug fix: 1 yerde düzelt
  ✅ Design change: 1 yerde değiştir
  ✅ Feature add: Otomatik her yerde
```

---

## 🚀 **NASIL BAŞLAMALIYIM?**

### **Adım 1: Basit Bir Sayfa Seç**
Örnek: `resources/views/admin/ayarlar/edit.blade.php`

### **Adım 2: Tek Bir Component Ekle**
Satır 94'teki checkbox'u component ile değiştir:

```blade
<!-- Önceki -->
<input type="checkbox" name="is_public" ... >

<!-- Yeni -->
<x-checkbox name="is_public" label="Herkese Açık" :checked="..." />
```

### **Adım 3: Test Et**
```bash
php artisan serve
# http://127.0.0.1:8000/admin/ayarlar/1/edit
```

### **Adım 4: Çalışırsa, Diğer Sayfalara Geç!**

---

## 💡 **PRO TİPLER**

### **1. Validation Error Handling**
```blade
<x-checkbox
    name="featured"
    label="Öne Çıkan"
    :checked="old('featured', $ilan->featured ?? false)"
    :error="$errors->first('featured')"
/>
```

### **2. Help Text Kullan**
```blade
<x-radio
    name="status"
    label="Aktif"
    value="active"
    help="İlan görünür ve aranabilir olur"
/>
```

### **3. Modal ile Form**
```blade
<x-admin.modal title="Hızlı Düzenle" bind="showEditModal">
    <form method="POST" action="...">
        @csrf
        <x-checkbox name="verified" label="Onaylı" />
        <x-admin.toggle name="active" label="Aktif" />
        
        <x-slot:footer>
            <button type="submit">Kaydet</button>
        </x-slot:footer>
    </form>
</x-admin.modal>
```

---

## 📚 **REFERANSLAR**

- 🎨 **Demo:** http://127.0.0.1:8000/admin/components-demo
- 📖 **Full Guide:** COMPONENT-LIBRARY-GUIDE.md
- 🧩 **Components:** resources/views/components/

---

**Özet:** Component'ler **GERÇEKTEN işe yarıyor!** Kod kısalıyor, bakım kolaylaşıyor, dark mode otomatik geliyor! 🎉

**Şimdi:** Bir sayfada dene, farkı gör! 🚀

