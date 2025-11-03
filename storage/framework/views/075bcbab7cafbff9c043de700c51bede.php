



<div x-data="photoUploadManager(<?php echo e(json_encode($ilan->id ?? null)); ?>)" 
     x-init="init()"
     class="bg-white dark:bg-gray-800 rounded-xl border-2 border-gray-200 dark:border-gray-700 p-6">
    
    
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                📸 Fotoğraf Yönetimi
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Sürükle-bırak veya tıklayarak fotoğraf yükleyin (Max: 10 MB)
            </p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm text-gray-600 dark:text-gray-400" x-show="photos.length > 0">
                <span x-text="photos.length"></span> fotoğraf
            </span>
        </div>
    </div>

    
    <div class="mb-6">
        <div 
            @dragover.prevent="dragOver = true"
            @dragleave.prevent="dragOver = false"
            @drop.prevent="handleDrop($event)"
            :class="dragOver ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600'"
            class="border-2 border-dashed rounded-xl p-8 text-center transition-all duration-200 cursor-pointer hover:border-blue-400 hover:bg-gray-50 dark:hover:bg-gray-700/50"
            @click="$refs.fileInput.click()">
            
            <input 
                type="file"
                x-ref="fileInput"
                @change="handleFileSelect($event)"
                accept="image/jpeg,image/jpg,image/png,image/webp"
                multiple
                class="hidden">
            
            <div class="flex flex-col items-center gap-3">
                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                        <span x-show="!dragOver">📁 Fotoğraf Seçin veya Sürükleyin</span>
                        <span x-show="dragOver" class="text-blue-600 dark:text-blue-400">🎯 Bırakın!</span>
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        JPG, PNG, WEBP • Max 10 MB • Çoklu seçim desteklenir
                    </p>
                </div>
            </div>
        </div>
    </div>

    
    <div x-show="uploading" class="mb-6">
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
            <div class="flex items-center gap-3">
                <div class="animate-spin w-5 h-5 border-2 border-blue-600 border-t-transparent rounded-full"></div>
                <span class="text-sm font-medium text-blue-900 dark:text-blue-200">
                    Yükleniyor... <span x-text="uploadProgress"></span>%
                </span>
            </div>
            <div class="mt-2 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" :style="`width: ${uploadProgress}%`"></div>
            </div>
        </div>
    </div>

    
    <div x-show="photos.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
        <template x-for="(photo, index) in photos" :key="photo.id || index">
            <div 
                class="relative group bg-gray-100 dark:bg-gray-900 rounded-lg overflow-hidden border-2 transition-all duration-200 hover:shadow-xl"
                :class="photo.is_featured ? 'border-yellow-500 dark:border-yellow-400' : 'border-gray-300 dark:border-gray-600 hover:border-blue-500'"
                draggable="true"
                @dragstart="dragStart(index)"
                @dragover.prevent
                @drop="dragDrop(index)">
                
                
                <div class="aspect-video bg-gray-200 dark:bg-gray-800">
                    <img 
                        :src="photo.preview || photo.url" 
                        :alt="photo.filename"
                        class="w-full h-full object-cover">
                </div>

                
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                    <div class="absolute bottom-0 left-0 right-0 p-3 space-y-2">
                        
                        <div x-show="photo.is_featured" class="flex items-center gap-1 text-xs font-bold text-yellow-400">
                            ⭐ Vitrin Fotoğrafı
                        </div>
                        
                        
                        <div class="flex items-center gap-2">
                            
                            <button 
                                type="button"
                                @click="setFeatured(index)"
                                x-show="!photo.is_featured"
                                class="flex-1 px-2 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-semibold rounded transition-colors">
                                ⭐ Vitrin Yap
                            </button>
                            
                            
                            <button 
                                type="button"
                                @click="deletePhoto(index)"
                                class="px-2 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded transition-colors">
                                🗑️
                            </button>
                        </div>
                    </div>
                </div>

                
                <div class="absolute top-2 left-2 px-2 py-1 bg-black/60 text-white text-xs font-bold rounded">
                    #<span x-text="index + 1"></span>
                </div>
            </div>
        </template>
    </div>

    
    <div x-show="photos.length === 0 && !uploading" class="text-center py-12 text-gray-500 dark:text-gray-400">
        <div class="text-4xl mb-3">📷</div>
        <p class="text-sm">Henüz fotoğraf yüklenmedi</p>
    </div>

    
    <input 
        type="hidden" 
        name="photos_data" 
        :value="JSON.stringify(photos)">
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function photoUploadManager(ilanId = null) {
    return {
        ilanId: ilanId,
        photos: [],
        uploading: false,
        uploadProgress: 0,
        dragOver: false,
        draggedIndex: null,

        async init() {
            // Mevcut fotoğrafları yükle (edit mode)
            if (this.ilanId) {
                await this.loadExistingPhotos();
            }
        },

        async loadExistingPhotos() {
            try {
                const response = await fetch(`/api/admin/ilanlar/${this.ilanId}/photos`);
                if (response.ok) {
                    const data = await response.json();
                    this.photos = data.photos || [];
                }
            } catch (error) {
                console.error('Fotoğraflar yüklenemedi:', error);
            }
        },

        handleFileSelect(event) {
            const files = Array.from(event.target.files);
            this.uploadFiles(files);
            event.target.value = ''; // Reset input
        },

        handleDrop(event) {
            this.dragOver = false;
            const files = Array.from(event.dataTransfer.files);
            const imageFiles = files.filter(f => f.type.startsWith('image/'));
            
            if (imageFiles.length > 0) {
                this.uploadFiles(imageFiles);
            } else {
                window.toast?.('Lütfen sadece resim dosyaları seçin', 'warning');
            }
        },

        async uploadFiles(files) {
            // Validation
            const maxSize = 10 * 1024 * 1024; // 10 MB
            const validFiles = files.filter(file => {
                if (file.size > maxSize) {
                    window.toast?.(`${file.name} çok büyük (Max: 10 MB)`, 'error');
                    return false;
                }
                return true;
            });

            if (validFiles.length === 0) return;

            this.uploading = true;
            this.uploadProgress = 0;

            for (let i = 0; i < validFiles.length; i++) {
                const file = validFiles[i];
                
                // Create preview
                const preview = await this.createPreview(file);
                
                // Add to photos array (optimistic UI)
                const tempPhoto = {
                    id: `temp-${Date.now()}-${i}`,
                    filename: file.name,
                    preview: preview,
                    is_featured: this.photos.length === 0, // First photo = featured
                    order: this.photos.length,
                    uploading: true
                };
                
                this.photos.push(tempPhoto);

                // Upload to server (if ilan exists)
                if (this.ilanId) {
                    await this.uploadToServer(file, tempPhoto);
                }

                this.uploadProgress = Math.round(((i + 1) / validFiles.length) * 100);
            }

            this.uploading = false;
            this.uploadProgress = 0;
            
            window.toast?.(`${validFiles.length} fotoğraf yüklendi`, 'success');
        },

        async uploadToServer(file, tempPhoto) {
            const formData = new FormData();
            formData.append('photo', file);
            formData.append('ilan_id', this.ilanId);
            formData.append('order', tempPhoto.order);
            formData.append('is_featured', tempPhoto.is_featured ? 1 : 0);

            try {
                const response = await fetch('/api/admin/photos/upload', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                if (response.ok) {
                    const data = await response.json();
                    // Update temp photo with server data
                    const photoIndex = this.photos.findIndex(p => p.id === tempPhoto.id);
                    if (photoIndex !== -1) {
                        this.photos[photoIndex] = { ...data.photo, uploading: false };
                    }
                }
            } catch (error) {
                console.error('Upload error:', error);
                window.toast?.('Fotoğraf yüklenemedi', 'error');
            }
        },

        createPreview(file) {
            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.onload = (e) => resolve(e.target.result);
                reader.readAsDataURL(file);
            });
        },

        setFeatured(index) {
            // Remove featured from all
            this.photos.forEach(p => p.is_featured = false);
            // Set new featured
            this.photos[index].is_featured = true;
            
            // Update server
            if (this.ilanId && this.photos[index].id) {
                this.updatePhotoOnServer(this.photos[index].id, { is_featured: 1 });
            }
        },

        async deletePhoto(index) {
            if (!confirm('Bu fotoğrafı silmek istediğinize emin misiniz?')) return;

            const photo = this.photos[index];
            
            // Delete from server
            if (this.ilanId && photo.id && !photo.id.toString().startsWith('temp-')) {
                try {
                    await fetch(`/api/admin/photos/${photo.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        }
                    });
                } catch (error) {
                    console.error('Delete error:', error);
                }
            }

            // Remove from array
            this.photos.splice(index, 1);
            
            // Reorder remaining photos
            this.photos.forEach((p, i) => p.order = i);
            
            // If deleted photo was featured, make first photo featured
            if (this.photos.length > 0 && !this.photos.some(p => p.is_featured)) {
                this.photos[0].is_featured = true;
            }

            window.toast?.('Fotoğraf silindi', 'success');
        },

        dragStart(index) {
            this.draggedIndex = index;
        },

        dragDrop(dropIndex) {
            if (this.draggedIndex === null || this.draggedIndex === dropIndex) return;

            // Reorder array
            const draggedPhoto = this.photos[this.draggedIndex];
            this.photos.splice(this.draggedIndex, 1);
            this.photos.splice(dropIndex, 0, draggedPhoto);

            // Update order values
            this.photos.forEach((p, i) => p.order = i);

            // Update server
            if (this.ilanId) {
                this.updateOrderOnServer();
            }

            this.draggedIndex = null;
        },

        async updatePhotoOnServer(photoId, data) {
            try {
                await fetch(`/api/admin/photos/${photoId}`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
            } catch (error) {
                console.error('Update error:', error);
            }
        },

        async updateOrderOnServer() {
            const orderData = this.photos.map((p, i) => ({
                id: p.id,
                order: i
            }));

            try {
                await fetch(`/api/admin/ilanlar/${this.ilanId}/photos/reorder`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ photos: orderData })
                });
            } catch (error) {
                console.error('Reorder error:', error);
            }
        }
    }
}
</script>
<?php $__env->stopPush(); ?>

<style>
/* Drag & drop animation */
[x-cloak] { display: none !important; }
</style>

<?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/admin/ilanlar/components/photo-upload-manager.blade.php ENDPATH**/ ?>