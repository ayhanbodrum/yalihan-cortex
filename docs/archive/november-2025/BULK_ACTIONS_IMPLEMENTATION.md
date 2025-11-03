# 💪 Bulk Actions Implementation Plan

**Tarih:** 1 Kasım 2025 - 22:35  
**Tahmini Süre:** 2 saat  
**Context7 Compliance:** %100  
**Yalıhan Bekçi:** ✅ Uyumlu

---

## 🎯 HEDEF

**Toplu İlan İşlemleri:**
- Çoklu ilan seçimi (checkbox)
- Toplu silme
- Toplu status değiştirme (Aktif, Pasif, Taslak)
- Confirm modal
- AJAX operation (no page reload)
- Progress indicator

---

## 📋 IMPLEMENTATION ADIMLARI

### **ADIM 1: Backend - Bulk Action Endpoint (30 dk)**

**Dosya:** `app/Http/Controllers/Admin/IlanController.php`

```php
/**
 * Bulk action handler
 * Context7: Toplu işlemler (delete, activate, deactivate, draft)
 * 
 * @param Request $request
 * @return \Illuminate\Http\JsonResponse
 */
public function bulkAction(Request $request)
{
    $validated = $request->validate([
        'ids' => 'required|array|min:1',
        'ids.*' => 'required|integer|exists:ilanlar,id',
        'action' => 'required|string|in:delete,activate,deactivate,draft',
    ]);

    try {
        DB::beginTransaction();
        
        $count = 0;
        $ids = $validated['ids'];
        
        switch ($validated['action']) {
            case 'delete':
                // Soft delete
                $count = Ilan::whereIn('id', $ids)->delete();
                $message = "{$count} ilan silindi";
                break;
                
            case 'activate':
                $count = Ilan::whereIn('id', $ids)->update([
                    'status' => 'Aktif',
                    'is_published' => true,
                    'updated_at' => now(),
                ]);
                $message = "{$count} ilan aktif yapıldı";
                break;
                
            case 'deactivate':
                $count = Ilan::whereIn('id', $ids)->update([
                    'status' => 'Pasif',
                    'is_published' => false,
                    'updated_at' => now(),
                ]);
                $message = "{$count} ilan pasif yapıldı";
                break;
                
            case 'draft':
                $count = Ilan::whereIn('id', $ids)->update([
                    'status' => 'Taslak',
                    'is_published' => false,
                    'updated_at' => now(),
                ]);
                $message = "{$count} ilan taslak yapıldı";
                break;
        }
        
        DB::commit();
        
        \Log::info('✅ Bulk action completed', [
            'action' => $validated['action'],
            'count' => $count,
            'ids' => $ids,
            'user_id' => Auth::id(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'count' => $count,
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        
        \Log::error('❌ Bulk action failed', [
            'error' => $e->getMessage(),
            'action' => $validated['action'],
            'ids' => $ids,
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Toplu işlem başarısız: ' . $e->getMessage(),
        ], 500);
    }
}
```

**Route Ekle:**
```php
// routes/admin.php
Route::post('/ilanlar/bulk-action', [\App\Http\Controllers\Admin\IlanController::class, 'bulkAction'])
    ->name('ilanlar.bulk-action');
```

---

### **ADIM 2: Frontend - Checkbox Selection (30 dk)**

**Dosya:** `resources/views/admin/ilanlar/index.blade.php`

**Thead'e checkbox ekle:**
```blade
<thead>
    <tr>
        {{-- Select All Checkbox --}}
        <th class="admin-table-th w-12">
            <input type="checkbox" 
                   id="select-all" 
                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                   x-model="selectAll"
                   @change="toggleSelectAll()">
        </th>
        <th class="admin-table-th">İlan</th>
        <!-- ... diğer kolonlar ... -->
    </tr>
</thead>
```

**Tbody'de checkbox ekle:**
```blade
<tbody>
    @foreach($ilanlar as $ilan)
    <tr>
        {{-- Row Checkbox --}}
        <td class="px-6 py-4">
            <input type="checkbox" 
                   class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                   value="{{ $ilan->id }}"
                   x-model="selectedIds"
                   @change="updateSelectAll()">
        </td>
        <!-- ... diğer kolonlar ... -->
    </tr>
    @endforeach
</tbody>
```

---

### **ADIM 3: Bulk Action UI (30 dk)**

**Toolbar Ekle (Thead önce):**
```blade
{{-- Bulk Actions Toolbar --}}
<div x-show="selectedIds.length > 0" 
     x-transition
     class="bg-blue-50 dark:bg-blue-900/20 border-b-2 border-blue-200 dark:border-blue-800 px-6 py-4 flex items-center justify-between">
    
    <div class="flex items-center text-sm text-blue-800 dark:text-blue-300">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span x-text="`${selectedIds.length} ilan seçildi`"></span>
    </div>
    
    <div class="flex items-center gap-3">
        {{-- Activate Button --}}
        <button type="button"
                @click="bulkAction('activate')"
                :disabled="processing"
                class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 hover:scale-105 focus:ring-2 focus:ring-green-500 disabled:opacity-50 transition-all duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Aktif Yap
        </button>
        
        {{-- Deactivate Button --}}
        <button type="button"
                @click="bulkAction('deactivate')"
                :disabled="processing"
                class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-lg hover:bg-yellow-700 hover:scale-105 focus:ring-2 focus:ring-yellow-500 disabled:opacity-50 transition-all duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Pasif Yap
        </button>
        
        {{-- Draft Button --}}
        <button type="button"
                @click="bulkAction('draft')"
                :disabled="processing"
                class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 hover:scale-105 focus:ring-2 focus:ring-gray-500 disabled:opacity-50 transition-all duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Taslak Yap
        </button>
        
        {{-- Delete Button --}}
        <button type="button"
                @click="confirmBulkDelete()"
                :disabled="processing"
                class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 hover:scale-105 focus:ring-2 focus:ring-red-500 disabled:opacity-50 transition-all duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Sil
        </button>
        
        {{-- Clear Selection --}}
        <button type="button"
                @click="clearSelection()"
                class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white underline">
            Seçimi Temizle
        </button>
    </div>
</div>
```

---

### **ADIM 4: Alpine.js Component (30 dk)**

```javascript
function bulkActionsManager() {
    return {
        selectedIds: [],
        selectAll: false,
        processing: false,
        
        toggleSelectAll() {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            
            if (this.selectAll) {
                this.selectedIds = Array.from(checkboxes).map(cb => parseInt(cb.value));
            } else {
                this.selectedIds = [];
            }
            
            checkboxes.forEach(cb => cb.checked = this.selectAll);
        },
        
        updateSelectAll() {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
            
            this.selectAll = checkedCount === checkboxes.length && checkboxes.length > 0;
        },
        
        clearSelection() {
            this.selectedIds = [];
            this.selectAll = false;
            document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
        },
        
        confirmBulkDelete() {
            if (this.selectedIds.length === 0) return;
            
            if (confirm(`${this.selectedIds.length} ilanı silmek istediğinize emin misiniz?`)) {
                this.bulkAction('delete');
            }
        },
        
        async bulkAction(action) {
            if (this.selectedIds.length === 0) {
                window.toast.error('Lütfen en az bir ilan seçin');
                return;
            }
            
            this.processing = true;
            
            try {
                const response = await fetch('{{ route("admin.ilanlar.bulk-action") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        ids: this.selectedIds,
                        action: action,
                    }),
                });
                
                const data = await response.json();
                
                if (data.success) {
                    window.toast.success(data.message);
                    
                    // Reload page after 1 second
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    throw new Error(data.message || 'İşlem başarısız');
                }
                
            } catch (error) {
                console.error('Bulk action error:', error);
                window.toast.error(error.message || 'Toplu işlem başarısız');
            } finally {
                this.processing = false;
            }
        }
    }
}
```

---

## 🧪 TEST SENARYOSU

### **Test 1: Select All**
```
1. İlanlar sayfasına git
2. Thead'deki "Select All" checkbox'ı işaretle
3. ✅ Tüm satırlar seçilmeli
4. ✅ Bulk actions toolbar görünmeli
5. ✅ "X ilan seçildi" mesajı gösterilmeli
```

### **Test 2: Bulk Activate**
```
1. 3 ilan seç
2. "Aktif Yap" butonuna tıkla
3. ✅ Loading state gösterilmeli
4. ✅ AJAX request gitmeli
5. ✅ Success toast gösterilmeli
6. ✅ Sayfa reload olmalı
7. ✅ İlanlar aktif olmalı
```

### **Test 3: Bulk Delete**
```
1. 2 ilan seç
2. "Sil" butonuna tıkla
3. ✅ Confirm dialog gösterilmeli
4. ✅ Onaylarsa silmeli
5. ✅ Success toast gösterilmeli
6. ✅ İlanlar listeden kaybolmalı
```

---

## ✅ BEKLENEN SONUÇ

**Önce:**
```yaml
Bulk Operations: ❌ Yok
Multi-select: ❌ Yok
Efficiency: Düşük (tek tek işlem)
```

**Sonra:**
```yaml
Bulk Operations: ✅ 4 action (delete, activate, deactivate, draft)
Multi-select: ✅ Checkbox + Select All
Efficiency: Yüksek (%200+ artış)
UX: Excellent
```

---

**BAŞLA!** 🚀

