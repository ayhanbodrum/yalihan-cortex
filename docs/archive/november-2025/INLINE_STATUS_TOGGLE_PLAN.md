# 🔄 Inline Status Toggle - Implementation Plan

**Tarih:** 1 Kasım 2025 - 22:45  
**Tahmini Süre:** 2 saat  
**Context7 Compliance:** %100  
**Yalıhan Bekçi:** ✅ Uyumlu

---

## 🎯 HEDEF

**Hızlı Status Değiştirme:**
- Status badge'e tıkla → Dropdown açılsın
- Status seç (Aktif, Pasif, Taslak, Beklemede)
- AJAX ile güncelle (no page reload)
- Instant visual feedback (badge rengi değişsin)
- Toast notification

---

## 📋 IMPLEMENTATION

### **Backend:** ✅ ZATEN VAR!
`IlanController@updateStatus()` - Satır 1185-1217

Route: `PATCH /admin/ilanlar/{id}/status`

---

### **Frontend: Clickable Status Badge Component**

**Mevcut:**
```blade
<x-neo.status-badge :status="$ilan->status ?? 'draft'" />
```

**Yeni (Inline Toggle):**
```blade
<div x-data="statusToggle({{ $ilan->id }}, '{{ $ilan->status }}')" 
     @click.outside="open = false"
     class="relative inline-block">
    
    {{-- Clickable Badge --}}
    <button @click="open = !open"
            type="button"
            class="px-3 py-1 text-xs font-semibold rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 cursor-pointer
                   {{ getStatusClasses(currentStatus) }}">
        {{ getStatusLabel(currentStatus) }}
        <svg class="w-3 h-3 ml-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    
    {{-- Dropdown Menu --}}
    <div x-show="open"
         x-transition
         class="absolute z-50 mt-2 w-48 rounded-lg shadow-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 py-1">
        
        <button @click="changeStatus('Aktif')"
                type="button"
                class="w-full text-left px-4 py-2 text-sm hover:bg-green-50 dark:hover:bg-green-900/20 flex items-center"
                :class="{ 'bg-green-50 dark:bg-green-900/20': currentStatus === 'Aktif' }">
            <span class="w-2 h-2 rounded-full bg-green-500 mr-3"></span>
            <span class="text-green-700 dark:text-green-300 font-medium">Aktif</span>
        </button>
        
        <button @click="changeStatus('Beklemede')"
                type="button"
                class="w-full text-left px-4 py-2 text-sm hover:bg-yellow-50 dark:hover:bg-yellow-900/20 flex items-center"
                :class="{ 'bg-yellow-50 dark:bg-yellow-900/20': currentStatus === 'Beklemede' }">
            <span class="w-2 h-2 rounded-full bg-yellow-500 mr-3"></span>
            <span class="text-yellow-700 dark:text-yellow-300 font-medium">Beklemede</span>
        </button>
        
        <button @click="changeStatus('Taslak')"
                type="button"
                class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center"
                :class="{ 'bg-gray-50 dark:bg-gray-700': currentStatus === 'Taslak' }">
            <span class="w-2 h-2 rounded-full bg-gray-500 mr-3"></span>
            <span class="text-gray-700 dark:text-gray-300 font-medium">Taslak</span>
        </button>
        
        <button @click="changeStatus('Pasif')"
                type="button"
                class="w-full text-left px-4 py-2 text-sm hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center"
                :class="{ 'bg-red-50 dark:bg-red-900/20': currentStatus === 'Pasif' }">
            <span class="w-2 h-2 rounded-full bg-red-500 mr-3"></span>
            <span class="text-red-700 dark:text-red-300 font-medium">Pasif</span>
        </button>
    </div>
</div>
```

---

### **Alpine.js Component:**

```javascript
function statusToggle(ilanId, initialStatus) {
    return {
        open: false,
        currentStatus: initialStatus,
        updating: false,
        
        async changeStatus(newStatus) {
            if (newStatus === this.currentStatus) {
                this.open = false;
                return;
            }
            
            this.updating = true;
            
            try {
                const response = await fetch(`/admin/ilanlar/${ilanId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ status: newStatus }),
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.currentStatus = newStatus;
                    window.toast.success('Status güncellendi');
                } else {
                    throw new Error(data.message || 'Güncelleme başarısız');
                }
                
            } catch (error) {
                console.error('Status update error:', error);
                window.toast.error(error.message || 'Status güncellenemedi');
            } finally {
                this.updating = false;
                this.open = false;
            }
        },
        
        getStatusClasses(status) {
            const classes = {
                'Aktif': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 hover:bg-green-200',
                'Beklemede': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300 hover:bg-yellow-200',
                'Taslak': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200',
                'Pasif': 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 hover:bg-red-200',
            };
            return classes[status] || classes['Taslak'];
        },
        
        getStatusLabel(status) {
            return status || 'Taslak';
        }
    }
}
```

---

## 🧪 TEST SENARYOSU

### **Test 1: Dropdown Açma**
```
1. İlanlar sayfasına git
2. Herhangi bir status badge'e tıkla
3. ✅ Dropdown açılmalı
4. ✅ 4 status seçeneği görünmeli
5. ✅ Mevcut status highlighted olmalı
```

### **Test 2: Status Değiştirme**
```
1. Badge tıkla → Dropdown aç
2. "Aktif" seç
3. ✅ AJAX request gitmeli
4. ✅ Badge rengi değişmeli (instant)
5. ✅ Success toast gösterilmeli
6. ✅ Dropdown kapanmalı
7. ✅ Page reload OLMAMALI
```

### **Test 3: Click Outside**
```
1. Badge tıkla → Dropdown aç
2. Başka yere tıkla
3. ✅ Dropdown kapanmalı
```

---

## ✅ BEKLENEN SONUÇ

**UX İyileştirmesi:**
- Edit sayfasına gitmeden hızlı status değiştirme
- Instant feedback
- No page reload
- %300 daha hızlı workflow

**Durum:** READY TO IMPLEMENT!

