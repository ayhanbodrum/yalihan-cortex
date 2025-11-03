<?php $__env->startSection('title', 'İlanlar - Yalıhan Emlak'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900">Emlak İlanları</h1>
                <p class="mt-2 text-gray-600">Hayalinizdeki evi bulun</p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Filters Sidebar -->
            <div class="lg:w-1/4">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filtreler</h3>
                    
                    <form method="GET" class="space-y-4">
                        <!-- Search -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Arama</label>
                            <input type="text" name="search" value="<?php echo e(request('search')); ?>" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                placeholder="İlan başlığı veya açıklama...">
                        </div>

                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                            <select name="kategori" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Tüm Kategoriler</option>
                                <?php $__currentLoopData = $kategoriler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($kategori->id); ?>" <?php echo e(request('kategori') == $kategori->id ? 'selected' : ''); ?>>
                                        <?php echo e($kategori->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <!-- City -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">İl</label>
                            <select name="il" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Tüm İller</option>
                                <?php $__currentLoopData = $iller; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $il): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($il->id); ?>" <?php echo e(request('il') == $il->id ? 'selected' : ''); ?>>
                                        <?php echo e($il->il_adi); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fiyat Aralığı</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" name="min_fiyat" value="<?php echo e(request('min_fiyat')); ?>" 
                                    class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Min">
                                <input type="number" name="max_fiyat" value="<?php echo e(request('max_fiyat')); ?>" 
                                    class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Max">
                            </div>
                        </div>

                        <!-- Filter Buttons -->
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                Filtrele
                            </button>
                            <a href="<?php echo e(route('ilanlar.index')); ?>" class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors text-center">
                                Temizle
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results -->
            <div class="lg:w-3/4">
                <!-- Results Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">
                            <?php echo e($ilanlar->total()); ?> ilan bulundu
                        </h2>
                        <?php if(request()->hasAny(['search', 'kategori', 'il', 'min_fiyat', 'max_fiyat'])): ?>
                            <p class="text-sm text-gray-600 mt-1">Filtrelenmiş sonuçlar</p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Sort -->
                    <div>
                        <select class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option>En Yeni</option>
                            <option>En Eski</option>
                            <option>Fiyat (Düşük → Yüksek)</option>
                            <option>Fiyat (Yüksek → Düşük)</option>
                        </select>
                    </div>
                </div>

                <!-- Properties Grid -->
                <?php if($ilanlar->count() > 0): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <?php $__currentLoopData = $ilanlar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ilan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                                <!-- Image -->
                                <div class="h-48 bg-gray-200 relative">
                                    <?php if($ilan->ilanFotograflari && $ilan->ilanFotograflari->count() > 0): ?>
                                        <img src="<?php echo e(Storage::url($ilan->ilanFotograflari->first()->dosya_yolu)); ?>" 
                                            alt="<?php echo e($ilan->baslik); ?>" 
                                            class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <i class="fas fa-home text-4xl"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Category Badge -->
                                    <div class="absolute top-3 left-3">
                                        <span class="bg-blue-600 text-white px-2 py-1 rounded-full text-xs font-medium">
                                            <?php echo e($ilan->kategori->name ?? 'Genel'); ?>

                                        </span>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="p-4">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                                        <?php echo e($ilan->baslik); ?>

                                    </h3>
                                    
                                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                                        <?php echo e(Str::limit($ilan->aciklama, 100)); ?>

                                    </p>

                                    <!-- Location -->
                                    <div class="flex items-center text-gray-500 text-sm mb-3">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        <span>
                                            <?php if($ilan->il): ?>
                                                <?php echo e($ilan->il->il_adi); ?>

                                                <?php if($ilan->ilce): ?>
                                                    / <?php echo e($ilan->ilce->ilce_adi); ?>

                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </span>
                                    </div>

                                    <!-- Price -->
                                    <div class="flex justify-between items-center">
                                        <div class="text-lg font-bold text-blue-600">
                                            <?php echo e(number_format($ilan->fiyat, 0, ',', '.')); ?> 
                                            <span class="text-sm font-normal"><?php echo e($ilan->para_birimi); ?></span>
                                        </div>
                                        
                                        <a href="<?php echo e(route('ilanlar.show', $ilan->id)); ?>" 
                                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                            Detay
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        <?php echo e($ilanlar->appends(request()->query())->links()); ?>

                    </div>
                <?php else: ?>
                    <!-- No Results -->
                    <div class="text-center py-12">
                        <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">İlan bulunamadı</h3>
                        <p class="text-gray-600">Arama kriterlerinizi değiştirerek tekrar deneyin.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.frontend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/frontend/ilanlar/index.blade.php ENDPATH**/ ?>