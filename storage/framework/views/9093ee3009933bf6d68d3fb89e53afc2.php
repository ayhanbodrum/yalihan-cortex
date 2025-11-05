<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-3">
                    <span class="text-4xl">🎯</span>
                    Yayın Tipi Yöneticisi
                </h1>
                <p class="text-gray-600 dark:text-gray-400">
                    Tek Sayfada Kategori, Yayın Tipi ve İlişki Yönetimi
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="<?php echo e(route('admin.ilan-kategorileri.index')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-semibold rounded-lg shadow-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 dark:bg-gray-800 dark:hover:bg-gray-600">
                    <i class="fas fa-list mr-2"></i>
                    Tüm Kategoriler
                </a>
            </div>
        </div>
    </div>

    <!-- Kategori Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $__currentLoopData = $kategoriler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('admin.property-type-manager.show', $kategori->id)); ?>"
           class="group bg-gray-50 dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 border-l-4 border-blue-500 hover:border-blue-600 transform hover:-translate-y-1">

            <!-- Kategori İkonu ve İsim -->
            <div class="flex items-center mb-4">
                <div class="text-4xl mr-4 group-hover:scale-110 transition-transform duration-300">
                    <?php echo e($kategori->icon ?? '🏠'); ?>

                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                        <?php echo e($kategori->name); ?>

                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        <?php echo e($kategori->children->count()); ?> Alt Kategori
                    </p>
                </div>
            </div>

            <!-- Alt Kategoriler Preview -->
            <div class="flex flex-wrap gap-2 mb-4 min-h-[32px]">
                <?php $__currentLoopData = $kategori->children->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $altKategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="text-xs px-3 py-1 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/30 dark:to-purple-900/30 border border-blue-200 dark:border-blue-800 rounded-full text-blue-700 dark:text-blue-300 font-medium">
                        <?php echo e($altKategori->name); ?>

                    </span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($kategori->children->count() > 3): ?>
                    <span class="text-xs px-3 py-1 bg-gray-100 dark:bg-gray-800 rounded-full text-gray-600 dark:text-gray-400 font-medium">
                        +<?php echo e($kategori->children->count() - 3); ?> daha
                    </span>
                <?php endif; ?>
                <?php if($kategori->children->count() === 0): ?>
                    <span class="text-xs px-3 py-1 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 rounded-full text-yellow-700 dark:text-yellow-300">
                        Alt kategori yok
                    </span>
                <?php endif; ?>
            </div>

            <!-- Stats & Action -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                    <span class="flex items-center gap-1">
                        <i class="fas fa-layer-group text-blue-500"></i>
                        <?php echo e($kategori->children->count()); ?>

                    </span>
                </div>
                <span class="text-sm text-blue-600 dark:text-blue-400 font-semibold group-hover:text-blue-700 dark:group-hover:text-blue-300 flex items-center gap-2">
                    Yönet
                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </span>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- Footer Info -->
    <div class="mt-8 bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
        <div class="flex items-start">
            <div class="text-2xl mr-3">💡</div>
            <div>
                <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-1">
                    Nasıl Çalışır?
                </h4>
                <p class="text-sm text-blue-800 dark:text-blue-200">
                    Tek sayfada kategori, yayın tipleri ve ilişkileri yönetin.
                    Ayrı sayfalara gerek yok, her şey bir arada!
                </p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.neo', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/admin/property-type-manager/index.blade.php ENDPATH**/ ?>