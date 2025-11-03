<?php
    use Illuminate\Support\Facades\Storage;
?>



<?php $__env->startSection('title', 'Görev Yönetimi'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container mx-auto px-4 py-8">
        <!-- Modern Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white flex items-center">
                        <div
                            class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mr-6 shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                </path>
                            </svg>
                        </div>
                        📋 Görev Yönetimi
                    </h1>
                    <p class="text-xl text-gray-600 dark:text-gray-400 mt-3">
                        Takım üyelerine görev atayın, takip edin ve performanslarını analiz edin
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <?php if (isset($component)) { $__componentOriginala617200b50274ed6f8aea665be4d256e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala617200b50274ed6f8aea665be4d256e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.context7.button','data' => ['variant' => 'secondary','href' => ''.e(route('admin.takim-yonetimi.gorevler.raporlar')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('context7.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'secondary','href' => ''.e(route('admin.takim-yonetimi.gorevler.raporlar')).'']); ?>
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        Raporlar
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala617200b50274ed6f8aea665be4d256e)): ?>
<?php $attributes = $__attributesOriginala617200b50274ed6f8aea665be4d256e; ?>
<?php unset($__attributesOriginala617200b50274ed6f8aea665be4d256e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala617200b50274ed6f8aea665be4d256e)): ?>
<?php $component = $__componentOriginala617200b50274ed6f8aea665be4d256e; ?>
<?php unset($__componentOriginala617200b50274ed6f8aea665be4d256e); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginala617200b50274ed6f8aea665be4d256e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala617200b50274ed6f8aea665be4d256e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.context7.button','data' => ['variant' => 'primary','href' => ''.e(route('admin.takim-yonetimi.gorevler.create')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('context7.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'primary','href' => ''.e(route('admin.takim-yonetimi.gorevler.create')).'']); ?>
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Yeni Görev
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala617200b50274ed6f8aea665be4d256e)): ?>
<?php $attributes = $__attributesOriginala617200b50274ed6f8aea665be4d256e; ?>
<?php unset($__attributesOriginala617200b50274ed6f8aea665be4d256e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala617200b50274ed6f8aea665be4d256e)): ?>
<?php $component = $__componentOriginala617200b50274ed6f8aea665be4d256e; ?>
<?php unset($__componentOriginala617200b50274ed6f8aea665be4d256e); ?>
<?php endif; ?>
                </div>
            </div>
        </div>

        <!-- 📊 Görev İstatistikleri -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200 shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-sm font-medium text-blue-800">Bekleyen Görevler</h4>
                        <p class="text-2xl font-bold text-blue-900"><?php echo e($istatistikler['bekleyen'] ?? 0); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-yellow-50 to-amber-50 rounded-xl border border-yellow-200 shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-amber-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-sm font-medium text-yellow-800">Devam Eden</h4>
                        <p class="text-2xl font-bold text-yellow-900"><?php echo e($istatistikler['devam_eden'] ?? 0); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200 shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-sm font-medium text-green-800">Tamamlanan</h4>
                        <p class="text-2xl font-bold text-green-900"><?php echo e($istatistikler['tamamlanan'] ?? 0); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-50 to-violet-50 rounded-xl border border-purple-200 shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-purple-500 to-violet-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-sm font-medium text-purple-800">Toplam Görev</h4>
                        <p class="text-2xl font-bold text-purple-900"><?php echo e($gorevler->total()); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🔍 Filtreler -->
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200 p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                🔍 Görev Filtreleri
            </h2>

            <form method="GET" action="<?php echo e(route('admin.takim-yonetimi.gorevler.index')); ?>"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                <div class="form-field">
                    <input type="text" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all duration-200" name="search" placeholder="Görev ara..."
                        value="<?php echo e(request('search')); ?>">
                </div>
                <div class="form-field">
                    <select style="color-scheme: light dark;" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200" name="status">
                        <option value="">Tüm Durumlar</option>
                        <?php $__currentLoopData = ['bekliyor', 'devam_ediyor', 'tamamlandi', 'iptal', 'beklemede']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statusOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($statusOption); ?>"
                                <?php echo e(request('status') == $statusOption ? 'selected' : ''); ?>>
                                <?php echo e(ucfirst(str_replace('_', ' ', $statusOption))); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="form-field">
                    <select style="color-scheme: light dark;" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200" name="oncelik">
                        <option value="">Tüm Öncelikler</option>
                        <?php $__currentLoopData = ['acil', 'yuksek', 'normal', 'dusuk']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $oncelik): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($oncelik); ?>" <?php echo e(request('oncelik') == $oncelik ? 'selected' : ''); ?>>
                                <?php echo e(ucfirst($oncelik)); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="form-field">
                    <select style="color-scheme: light dark;" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200" name="tip">
                        <option value="">Tüm Tipler</option>
                        <?php $__currentLoopData = ['musteri_takibi', 'ilan_hazirlama', 'musteri_ziyareti', 'dokuman_hazirlama', 'diger']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($tip); ?>" <?php echo e(request('tip') == $tip ? 'selected' : ''); ?>>
                                <?php echo e(ucfirst(str_replace('_', ' ', $tip))); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="form-field">
                    <select style="color-scheme: light dark;" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200" name="danisman_id">
                        <option value="">Tüm Danışmanlar</option>
                        <?php $__currentLoopData = $danismanlar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $danisman): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($danisman->id); ?>"
                                <?php echo e(request('danisman_id') == $danisman->id ? 'selected' : ''); ?>>
                                <?php echo e($danisman->name ?? ($danisman->ad ?? 'Danışman')); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="form-field">
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-orange-600 text-white font-semibold rounded-lg shadow-md hover:bg-orange-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:outline-none transition-all duration-200 w-full touch-target-optimized touch-target-optimized">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Ara
                    </button>
                </div>
            </form>
        </div>

        <!-- Görev Listesi -->
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-800">Görevler (<?php echo e($gorevler->total()); ?>)</h2>
                    <div class="flex items-center space-x-3">
                        <button type="button" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg shadow-md hover:bg-gray-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all duration-200 touch-target-optimized touch-target-optimized" onclick="topluGorevAta()">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Toplu Ata
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto" x-data="{ contentLoaded: true }" x-init="setTimeout(() => contentLoaded = true, 100)">
                <!-- Skeleton Loading State -->
                <div x-show="!contentLoaded" x-transition>
                    <?php if (isset($component)) { $__componentOriginalb42f26842044ee5361db359fa44eac85 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb42f26842044ee5361db359fa44eac85 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.neo-skeleton','data' => ['type' => 'table','rows' => '5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin.neo-skeleton'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'table','rows' => '5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb42f26842044ee5361db359fa44eac85)): ?>
<?php $attributes = $__attributesOriginalb42f26842044ee5361db359fa44eac85; ?>
<?php unset($__attributesOriginalb42f26842044ee5361db359fa44eac85); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb42f26842044ee5361db359fa44eac85)): ?>
<?php $component = $__componentOriginalb42f26842044ee5361db359fa44eac85; ?>
<?php unset($__componentOriginalb42f26842044ee5361db359fa44eac85); ?>
<?php endif; ?>
                </div>

                <!-- Actual Content -->
                <div x-show="contentLoaded" x-transition class="opacity-100 transition-opacity duration-300">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    <input type="checkbox" class="w-4 h-4 text-orange-600 bg-gray-50 dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-colors duration-200" id="select-all">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Görev
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Danışman
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Durum
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Öncelik
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Tarih
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    İşlemler
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-50 dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <?php $__empty_1 = true; $__currentLoopData = $gorevler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gorev): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" class="w-4 h-4 text-orange-600 bg-gray-50 dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-colors duration-200 gorev-checkbox"
                                            value="<?php echo e($gorev->id); ?>">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div
                                                    class="h-10 w-10 rounded-lg bg-gradient-to-r from-purple-500 to-pink-600 flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900"><?php echo e($gorev->baslik); ?></div>
                                                <div class="text-sm text-gray-500"><?php echo e(Str::limit($gorev->aciklama, 50)); ?>

                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                <?php
                                                    $pf = $gorev->danisman->profil_fotografi ?? null;
                                                    $pfUrl = $pf && Storage::exists($pf) ? Storage::url($pf) : null;
                                                ?>
                                                <?php if($gorev->danisman && $pfUrl): ?>
                                                    <img class="h-8 w-8 rounded-full object-cover"
                                                        src="<?php echo e($pfUrl); ?>"
                                                        alt="<?php echo e($gorev->danisman->name ?? 'Danışman'); ?>"
                                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div
                                                        class="h-8 w-8 rounded-full bg-gray-200 hidden items-center justify-center text-gray-500">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                        </svg>
                                                    </div>
                                                <?php else: ?>
                                                    <div
                                                        class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-white" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                        </svg>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo e($gorev->danisman->name ?? 'Atanmamış'); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if (isset($component)) { $__componentOriginal9e089bc5f8eac272c2c5ae05d84c4b58 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9e089bc5f8eac272c2c5ae05d84c4b58 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.neo.status-badge','data' => ['value' => ucfirst(str_replace('_', ' ', $gorev->status))]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('neo.status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(ucfirst(str_replace('_', ' ', $gorev->status)))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9e089bc5f8eac272c2c5ae05d84c4b58)): ?>
<?php $attributes = $__attributesOriginal9e089bc5f8eac272c2c5ae05d84c4b58; ?>
<?php unset($__attributesOriginal9e089bc5f8eac272c2c5ae05d84c4b58); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9e089bc5f8eac272c2c5ae05d84c4b58)): ?>
<?php $component = $__componentOriginal9e089bc5f8eac272c2c5ae05d84c4b58; ?>
<?php unset($__componentOriginal9e089bc5f8eac272c2c5ae05d84c4b58); ?>
<?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if (isset($component)) { $__componentOriginal9e089bc5f8eac272c2c5ae05d84c4b58 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9e089bc5f8eac272c2c5ae05d84c4b58 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.neo.status-badge','data' => ['value' => ucfirst($gorev->oncelik),'category' => 'priority']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('neo.status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(ucfirst($gorev->oncelik)),'category' => 'priority']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9e089bc5f8eac272c2c5ae05d84c4b58)): ?>
<?php $attributes = $__attributesOriginal9e089bc5f8eac272c2c5ae05d84c4b58; ?>
<?php unset($__attributesOriginal9e089bc5f8eac272c2c5ae05d84c4b58); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9e089bc5f8eac272c2c5ae05d84c4b58)): ?>
<?php $component = $__componentOriginal9e089bc5f8eac272c2c5ae05d84c4b58; ?>
<?php unset($__componentOriginal9e089bc5f8eac272c2c5ae05d84c4b58); ?>
<?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo e($gorev->deadline ? $gorev->deadline->format('d.m.Y') : '-'); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="<?php echo e(route('admin.takim-yonetimi.gorevler.show', $gorev)); ?>"
                                                class="text-blue-600 hover:text-blue-900">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <a href="<?php echo e(route('admin.takim-yonetimi.gorevler.edit', $gorev)); ?>"
                                                class="text-indigo-600 hover:text-indigo-900">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <?php if (isset($component)) { $__componentOriginal34874c6961c0544aaa07cd3c1d2e7465 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal34874c6961c0544aaa07cd3c1d2e7465 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.neo.empty-state','data' => ['title' => 'Henüz görev bulunmuyor','description' => 'İlk görevi oluşturarak başlayın','actionHref' => route('admin.takim-yonetimi.gorevler.create'),'actionText' => 'İlk Görevi Oluştur']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('neo.empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Henüz görev bulunmuyor','description' => 'İlk görevi oluşturarak başlayın','actionHref' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.takim-yonetimi.gorevler.create')),'actionText' => 'İlk Görevi Oluştur']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal34874c6961c0544aaa07cd3c1d2e7465)): ?>
<?php $attributes = $__attributesOriginal34874c6961c0544aaa07cd3c1d2e7465; ?>
<?php unset($__attributesOriginal34874c6961c0544aaa07cd3c1d2e7465); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal34874c6961c0544aaa07cd3c1d2e7465)): ?>
<?php $component = $__componentOriginal34874c6961c0544aaa07cd3c1d2e7465; ?>
<?php unset($__componentOriginal34874c6961c0544aaa07cd3c1d2e7465); ?>
<?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if($gorevler->hasPages()): ?>
                <div class="px-6 py-4 border-t border-gray-200">
                    <?php echo e($gorevler->appends(request()->query())->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    <?php $__env->stopSection(); ?>

    <?php $__env->startPush('scripts'); ?>
        <script>
            // Toplu seçim
            document.getElementById('select-all').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.gorev-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });

            // Toplu görev atama
            function topluGorevAta() {
                const selectedGorevler = Array.from(document.querySelectorAll('.gorev-checkbox:checked')).map(cb => cb.value);

                if (selectedGorevler.length === 0) {
                    alert('Lütfen en az bir görev seçin');
                    return;
                }

                // Danışman seçim modalı açılabilir
                const danismanId = prompt('Danışman ID girin:');
                if (!danismanId) return;

                fetch('<?php echo e(route('admin.takim-yonetimi.gorevler.toplu-ata')); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            gorev_ids: selectedGorevler,
                            danisman_id: danismanId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Görevler başarıyla atandı!');
                            location.reload();
                        } else {
                            alert('Hata: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Bir hata oluştu');
                    });
            }
        </script>
    <?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.neo', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/admin/takim-yonetimi/gorevler/index.blade.php ENDPATH**/ ?>