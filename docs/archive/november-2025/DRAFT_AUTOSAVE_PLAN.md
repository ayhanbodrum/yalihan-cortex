# 💾 Draft Auto-save Implementation Plan

**Tarih:** 1 Kasım 2025 - 22:50  
**Tahmini Süre:** 2 saat  
**Context7 Compliance:** %100  
**Yalıhan Bekçi:** ✅ Uyumlu

---

## 🎯 HEDEF

**Data Loss Prevention:**
- localStorage backup (every 30s)
- "Unsaved changes" warning on browser close
- "Restore Draft" button on page load
- Clear draft after successful submit
- Progress indicator (form completion %)

---

## 📋 IMPLEMENTATION

### **ADIM 1: Auto-save Manager (1 saat)**

**Dosya:** `resources/views/admin/ilanlar/create.blade.php`

```javascript
const DraftAutoSave = {
    formId: 'ilan-create-form',
    interval: null,
    saveIntervalSeconds: 30,
    hasChanges: false,
    
    init() {
        this.checkForDraft();
        this.startAutoSave();
        this.preventDataLoss();
        this.trackChanges();
    },
    
    checkForDraft() {
        const draft = this.loadDraft();
        
        if (draft && draft.timestamp) {
            const draftAge = Date.now() - draft.timestamp;
            const hours = Math.floor(draftAge / (1000 * 60 * 60));
            const minutes = Math.floor((draftAge % (1000 * 60 * 60)) / (1000 * 60));
            
            this.showRestoreButton(draft, hours, minutes);
        }
    },
    
    showRestoreButton(draft, hours, minutes) {
        const timeAgo = hours > 0 
            ? `${hours} saat ${minutes} dakika önce`
            : `${minutes} dakika önce`;
        
        const banner = document.createElement('div');
        banner.className = 'bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 mb-6 rounded-lg flex items-center justify-between';
        banner.innerHTML = `
            <div class="flex items-center">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium text-blue-800 dark:text-blue-300">
                        Kaydedilmemiş taslak bulundu
                    </p>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                        Son kayıt: ${timeAgo}
                    </p>
                </div>
            </div>
            <div class="flex gap-2">
                <button onclick="DraftAutoSave.restoreDraft()" 
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    Geri Yükle
                </button>
                <button onclick="DraftAutoSave.discardDraft()" 
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    Sil
                </button>
            </div>
        `;
        
        const form = document.getElementById(this.formId);
        form.parentElement.insertBefore(banner, form);
    },
    
    startAutoSave() {
        this.interval = setInterval(() => {
            if (this.hasChanges) {
                this.saveDraft();
            }
        }, this.saveIntervalSeconds * 1000);
    },
    
    saveDraft() {
        try {
            const form = document.getElementById(this.formId);
            if (!form) return;
            
            const formData = new FormData(form);
            const data = {};
            
            formData.forEach((value, key) => {
                if (value && value !== '') {
                    data[key] = value;
                }
            });
            
            const draft = {
                data: data,
                timestamp: Date.now(),
                version: '1.0',
            };
            
            localStorage.setItem('ilan_draft', JSON.stringify(draft));
            
            console.log('✅ Draft saved:', new Date().toLocaleTimeString());
            
            // Show save indicator
            this.showSaveIndicator();
            
        } catch (error) {
            console.error('Draft save error:', error);
        }
    },
    
    loadDraft() {
        try {
            const draftJson = localStorage.getItem('ilan_draft');
            return draftJson ? JSON.parse(draftJson) : null;
        } catch (error) {
            console.error('Draft load error:', error);
            return null;
        }
    },
    
    restoreDraft() {
        const draft = this.loadDraft();
        if (!draft || !draft.data) return;
        
        Object.entries(draft.data).forEach(([key, value]) => {
            const field = document.querySelector(`[name="${key}"]`);
            if (field) {
                if (field.type === 'checkbox') {
                    field.checked = value === 'on' || value === '1';
                } else {
                    field.value = value;
                }
                
                // Trigger change event (Alpine.js reactivity)
                field.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
        
        window.toast.success('Taslak geri yüklendi');
        document.querySelector('.bg-blue-50')?.remove(); // Remove restore banner
    },
    
    discardDraft() {
        localStorage.removeItem('ilan_draft');
        document.querySelector('.bg-blue-50')?.remove();
        window.toast.success('Taslak silindi');
    },
    
    clearDraft() {
        localStorage.removeItem('ilan_draft');
        this.hasChanges = false;
    },
    
    preventDataLoss() {
        window.addEventListener('beforeunload', (e) => {
            if (this.hasChanges) {
                e.preventDefault();
                e.returnValue = 'Kaydedilmemiş değişiklikler var! Sayfadan ayrılmak istediğinize emin misiniz?';
            }
        });
    },
    
    trackChanges() {
        const form = document.getElementById(this.formId);
        if (!form) return;
        
        form.addEventListener('input', () => {
            this.hasChanges = true;
        });
        
        form.addEventListener('submit', () => {
            this.hasChanges = false;
            this.clearDraft();
        });
    },
    
    showSaveIndicator() {
        const indicator = document.getElementById('save-indicator');
        if (!indicator) return;
        
        indicator.textContent = 'Kaydedildi ✓';
        indicator.classList.remove('text-gray-400');
        indicator.classList.add('text-green-600');
        
        setTimeout(() => {
            indicator.textContent = 'Otomatik kayıt aktif';
            indicator.classList.remove('text-green-600');
            indicator.classList.add('text-gray-400');
        }, 2000);
    },
    
    getProgress() {
        const form = document.getElementById(this.formId);
        if (!form) return 0;
        
        const requiredFields = form.querySelectorAll('[required]');
        const filledFields = Array.from(requiredFields).filter(field => {
            if (field.type === 'checkbox') return field.checked;
            return field.value && field.value.trim() !== '';
        });
        
        return Math.round((filledFields.length / requiredFields.length) * 100);
    },
    
    updateProgressBar() {
        const progress = this.getProgress();
        const progressBar = document.getElementById('form-progress-bar');
        const progressText = document.getElementById('form-progress-text');
        
        if (progressBar) {
            progressBar.style.width = `${progress}%`;
            progressBar.className = `h-full transition-all duration-500 ${this.getProgressColor(progress)}`;
        }
        
        if (progressText) {
            progressText.textContent = `%${progress} tamamlandı`;
        }
    },
    
    getProgressColor(progress) {
        if (progress < 33) return 'bg-red-500';
        if (progress < 66) return 'bg-yellow-500';
        return 'bg-green-500';
    }
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    DraftAutoSave.init();
    
    // Update progress on field changes
    document.getElementById('ilan-create-form')?.addEventListener('input', () => {
        DraftAutoSave.updateProgressBar();
    });
});
```

---

### **ADIM 2: Progress Bar UI (30 dk)**

**Header'a ekle:**
```blade
{{-- Form Progress Indicator --}}
<div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-6">
    <div class="flex items-center justify-between mb-2">
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Form İlerlemesi</span>
        <span id="form-progress-text" class="text-sm text-gray-500 dark:text-gray-400">%0 tamamlandı</span>
    </div>
    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
        <div id="form-progress-bar" 
             class="h-full bg-green-500 rounded-full transition-all duration-500"
             style="width: 0%"></div>
    </div>
    <div class="flex items-center justify-between mt-2">
        <span id="save-indicator" class="text-xs text-gray-400">Otomatik kayıt aktif</span>
        <span class="text-xs text-gray-400">Her 30 saniyede</span>
    </div>
</div>
```

---

### **ADIM 3: Route (Backend Zaten Var)** ✅

`IlanController@autoSave()` - Satır 1632-1676 (zaten var!)

Route check:
```php
// routes/admin.php
Route::post('/ilanlar/draft', ...) // Zaten var
```

---

## 🧪 TEST

### **Test 1: Auto-save**
```
1. Create form aç
2. Başlık gir
3. 30 saniye bekle
4. ✅ Console: "Draft saved" görünmeli
5. ✅ localStorage'da draft olmalı
6. ✅ "Kaydedildi ✓" indicator gösterilmeli
```

### **Test 2: Restore**
```
1. Form doldur
2. Tab'ı kapat (kaydetmeden)
3. Tekrar aç
4. ✅ "Kaydedilmemiş taslak" banner görünmeli
5. ✅ "Geri Yükle" butonuna tıkla
6. ✅ Form dolu gelmeli
```

### **Test 3: Data Loss Prevention**
```
1. Form doldur
2. Tab'ı kapatmayı dene
3. ✅ "Unsaved changes" warning gösterilmeli
```

### **Test 4: Progress Bar**
```
1. Form aç (progress: 0%)
2. Başlık gir (progress: 10%)
3. Kategori seç (progress: 20%)
4. ✅ Progress bar yeşile dönmeli (%66+)
```

---

## ✅ BEKLENEN SONUÇ

**UX:**
- ✅ Data loss risk: %0
- ✅ Auto-save: Evet
- ✅ Draft restore: Evet
- ✅ Progress tracking: Evet

**Süre:** READY TO IMPLEMENT!

