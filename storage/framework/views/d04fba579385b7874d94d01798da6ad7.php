<?php $__env->startSection('title', 'İlan Özellikleri Yönetimi'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8"
     x-data="{ 
         activeTab: '<?php echo e($activeTab ?? 'ozellikler'); ?>',
         setTab(tab) {
             this.activeTab = tab;
             window.location.hash = tab;
         }
     }"
     x-init="
         // URL hash'den tab'ı al
         if (window.location.hash) {
             activeTab = window.location.hash.substring(1);
         }
     ">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                🏷️ İlan Özellikleri Yönetimi
            </h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                İlan formlarında kullanılacak özellikleri ve kategorilerini tek sayfada yönetin
            </p>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                <div class="text-sm opacity-90 mb-1">Toplam Özellik</div>
                <div class="text-3xl font-bold"><?php echo e($istatistikler['toplam']); ?></div>
            </div>
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="text-sm opacity-90 mb-1">Aktif</div>
                <div class="text-3xl font-bold"><?php echo e($istatistikler['aktif']); ?></div>
            </div>
            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
                <div class="text-sm opacity-90 mb-1">Pasif</div>
                <div class="text-3xl font-bold"><?php echo e($istatistikler['pasif']); ?></div>
            </div>
            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white">
                <div class="text-sm opacity-90 mb-1">Kategorisiz</div>
                <div class="text-3xl font-bold"><?php echo e($istatistikler['kategorisiz']); ?></div>
            </div>
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <div class="text-sm opacity-90 mb-1">Kategori</div>
                <div class="text-3xl font-bold"><?php echo e($istatistikler['kategori_sayisi']); ?></div>
            </div>
        </div>

        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            
            
            <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                <nav class="flex -mb-px">
                    <button @click="setTab('ozellikler')"
                            :class="activeTab === 'ozellikler' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                            class="group inline-flex items-center gap-2 px-6 py-4 border-b-2 font-semibold text-sm transition-colors">
                        <span class="text-lg">📋</span>
                        Tüm Özellikler
                        <span class="ml-2 px-2 py-0.5 rounded-full text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                            <?php echo e($istatistikler['toplam']); ?>

                        </span>
                    </button>

                    <button @click="setTab('kategoriler')"
                            :class="activeTab === 'kategoriler' ? 'border-purple-500 text-purple-600 dark:text-purple-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                            class="group inline-flex items-center gap-2 px-6 py-4 border-b-2 font-semibold text-sm transition-colors">
                        <span class="text-lg">🏷️</span>
                        Kategoriler
                        <span class="ml-2 px-2 py-0.5 rounded-full text-xs bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                            <?php echo e($istatistikler['kategori_sayisi']); ?>

                        </span>
                    </button>

                    <button @click="setTab('kategorisiz')"
                            :class="activeTab === 'kategorisiz' ? 'border-yellow-500 text-yellow-600 dark:text-yellow-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                            class="group inline-flex items-center gap-2 px-6 py-4 border-b-2 font-semibold text-sm transition-colors">
                        <span class="text-lg">⚠️</span>
                        Kategorisiz
                        <span class="ml-2 px-2 py-0.5 rounded-full text-xs bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                            <?php echo e($istatistikler['kategorisiz']); ?>

                        </span>
                    </button>
                </nav>
            </div>

            
            <div class="p-6">
                
                
                <div x-show="activeTab === 'ozellikler'"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100">
                    
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                            📋 Tüm Özellikler
                        </h2>
                        <a href="<?php echo e(route('admin.ozellikler.create')); ?>" 
                           class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg hover:scale-105 transition-all shadow-lg">
                            + Yeni Özellik
                        </a>
                    </div>

                    <?php if($ozellikler->isEmpty()): ?>
                        <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                            <div class="text-4xl mb-2">📭</div>
                            <p>Henüz özellik bulunmuyor</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 dark:text-white uppercase">Özellik</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 dark:text-white uppercase">Kategori</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 dark:text-white uppercase">Tip</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 dark:text-white uppercase">Durum</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-900 dark:text-white uppercase">İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <?php $__currentLoopData = $ozellikler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ozellik): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">
                                                <?php echo e($ozellik->name); ?>

                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                                <?php echo e($ozellik->category->name ?? 'Kategorisiz'); ?>

                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                                <?php echo e($ozellik->field_type); ?>

                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                    <?php echo e($ozellik->enabled ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'); ?>">
                                                    <?php echo e($ozellik->enabled ? 'Aktif' : 'Pasif'); ?>

                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right text-sm">
                                                <a href="<?php echo e(route('admin.ozellikler.edit', $ozellik)); ?>" 
                                                   class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                    Düzenle
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            <?php echo e($ozellikler->appends(['tab' => 'ozellikler'])->links()); ?>

                        </div>
                    <?php endif; ?>
                </div>

                
                <div x-show="activeTab === 'kategoriler'"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100">
                    
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                            🏷️ Özellik Kategorileri
                        </h2>
                        <a href="<?php echo e(route('admin.ozellikler.kategoriler.create')); ?>" 
                           class="px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-lg hover:scale-105 transition-all shadow-lg">
                            + Yeni Kategori
                        </a>
                    </div>

                    <?php if($kategoriListesi->isEmpty()): ?>
                        <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                            <div class="text-4xl mb-2">📭</div>
                            <p>Henüz kategori bulunmuyor</p>
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php $__currentLoopData = $kategoriListesi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-all hover:-translate-y-1">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                            <?php echo e($kategori->name); ?>

                                        </h3>
                                        <span class="text-2xl"><?php echo e($kategori->icon ?? '📦'); ?></span>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                        <?php echo e($kategori->description ?? 'Açıklama yok'); ?>

                                    </p>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">
                                            <?php echo e($kategori->features_count); ?> özellik
                                        </span>
                                        <a href="<?php echo e(route('admin.ozellikler.kategoriler.show', $kategori)); ?>" 
                                           class="text-blue-600 hover:text-blue-700 dark:text-blue-400 font-semibold">
                                            Detay →
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <div class="mt-6">
                            <?php echo e($kategoriListesi->appends(['tab' => 'kategoriler'])->links()); ?>

                        </div>
                    <?php endif; ?>
                </div>

                
                <div x-show="activeTab === 'kategorisiz'"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100">
                    
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                                ⚠️ Kategorisiz Özellikler
                            </h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Bu özellikler henüz bir kategoriye atanmamış
                            </p>
                        </div>
                    </div>

                    <?php if($kategorisizOzellikler->isEmpty()): ?>
                        <div class="text-center py-12">
                            <div class="text-6xl mb-4">✅</div>
                            <h3 class="text-xl font-bold text-green-600 dark:text-green-400 mb-2">
                                Harika! Tüm özellikler kategorize edilmiş
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                Kategorisiz özellik bulunmuyor
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4 mb-6">
                            <div class="flex items-center gap-2">
                                <span class="text-yellow-600 dark:text-yellow-400">⚠️</span>
                                <span class="text-sm text-yellow-700 dark:text-yellow-300 font-semibold">
                                    <?php echo e($kategorisizOzellikler->total()); ?> özellik kategoriye atanmayı bekliyor
                                </span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <?php $__currentLoopData = $kategorisizOzellikler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ozellik): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900 dark:text-white">
                                                <?php echo e($ozellik->name); ?>

                                            </h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                Tip: <?php echo e($ozellik->field_type); ?>

                                            </p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <a href="<?php echo e(route('admin.ozellikler.edit', $ozellik)); ?>" 
                                               class="px-3 py-1.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                                                Kategoriye Ata
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <div class="mt-4">
                            <?php echo e($kategorisizOzellikler->appends(['tab' => 'kategorisiz'])->links()); ?>

                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin.layouts.neo', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/admin/ozellikler/index.blade.php ENDPATH**/ ?>