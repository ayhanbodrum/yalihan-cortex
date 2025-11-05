

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'ilanId' => null,
    'selectedFeatures' => [],
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'ilanId' => null,
    'selectedFeatures' => [],
]); ?>
<?php foreach (array_filter(([
    'ilanId' => null,
    'selectedFeatures' => [],
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div x-data="featureCategoriesModal(<?php echo e(json_encode([
    'ilanId' => $ilanId,
    'selectedFeatures' => $selectedFeatures,
])); ?>)"
     x-init="loadCategories()"
     class="feature-categories-modal">
    
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white">İç Özellikleri</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400">ADSL, Asansör, Balkon...</p>
                    </div>
                </div>
                <?php if (isset($component)) { $__componentOriginalaa28c1bda1c848d9520aaabfb861cb78 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalaa28c1bda1c848d9520aaabfb861cb78 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.feature-modal-selector','data' => ['categorySlug' => 'ic-ozellikleri','selectedFeatures' => getSelectedByCategory('ic-ozellikleri'),'ilanId' => $ilanId,'@featuresSelected' => 'handleFeaturesSelected($event.detail)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('feature-modal-selector'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['category-slug' => 'ic-ozellikleri','selected-features' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(getSelectedByCategory('ic-ozellikleri')),'ilan-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ilanId),'@features-selected' => 'handleFeaturesSelected($event.detail)']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalaa28c1bda1c848d9520aaabfb861cb78)): ?>
<?php $attributes = $__attributesOriginalaa28c1bda1c848d9520aaabfb861cb78; ?>
<?php unset($__attributesOriginalaa28c1bda1c848d9520aaabfb861cb78); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalaa28c1bda1c848d9520aaabfb861cb78)): ?>
<?php $component = $__componentOriginalaa28c1bda1c848d9520aaabfb861cb78; ?>
<?php unset($__componentOriginalaa28c1bda1c848d9520aaabfb861cb78); ?>
<?php endif; ?>
            </div>
            <div class="flex flex-wrap gap-2">
                <template x-for="feature in getSelectedByCategory('ic-ozellikleri')" :key="feature.id">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200">
                        <span x-text="feature.name"></span>
                    </span>
                </template>
            </div>
        </div>

        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white">Dış Özellikleri</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Bahçe, Otopark, Güvenlik...</p>
                    </div>
                </div>
                <?php if (isset($component)) { $__componentOriginalaa28c1bda1c848d9520aaabfb861cb78 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalaa28c1bda1c848d9520aaabfb861cb78 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.feature-modal-selector','data' => ['categorySlug' => 'dis-ozellikleri','selectedFeatures' => getSelectedByCategory('dis-ozellikleri'),'ilanId' => $ilanId,'@featuresSelected' => 'handleFeaturesSelected($event.detail)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('feature-modal-selector'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['category-slug' => 'dis-ozellikleri','selected-features' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(getSelectedByCategory('dis-ozellikleri')),'ilan-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ilanId),'@features-selected' => 'handleFeaturesSelected($event.detail)']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalaa28c1bda1c848d9520aaabfb861cb78)): ?>
<?php $attributes = $__attributesOriginalaa28c1bda1c848d9520aaabfb861cb78; ?>
<?php unset($__attributesOriginalaa28c1bda1c848d9520aaabfb861cb78); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalaa28c1bda1c848d9520aaabfb861cb78)): ?>
<?php $component = $__componentOriginalaa28c1bda1c848d9520aaabfb861cb78; ?>
<?php unset($__componentOriginalaa28c1bda1c848d9520aaabfb861cb78); ?>
<?php endif; ?>
            </div>
            <div class="flex flex-wrap gap-2">
                <template x-for="feature in getSelectedByCategory('dis-ozellikleri')" :key="feature.id">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                        <span x-text="feature.name"></span>
                    </span>
                </template>
            </div>
        </div>

        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white">Muhit</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Çevre, Sosyal alanlar...</p>
                    </div>
                </div>
                <?php if (isset($component)) { $__componentOriginalaa28c1bda1c848d9520aaabfb861cb78 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalaa28c1bda1c848d9520aaabfb861cb78 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.feature-modal-selector','data' => ['categorySlug' => 'muhit','selectedFeatures' => getSelectedByCategory('muhit'),'ilanId' => $ilanId,'@featuresSelected' => 'handleFeaturesSelected($event.detail)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('feature-modal-selector'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['category-slug' => 'muhit','selected-features' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(getSelectedByCategory('muhit')),'ilan-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ilanId),'@features-selected' => 'handleFeaturesSelected($event.detail)']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalaa28c1bda1c848d9520aaabfb861cb78)): ?>
<?php $attributes = $__attributesOriginalaa28c1bda1c848d9520aaabfb861cb78; ?>
<?php unset($__attributesOriginalaa28c1bda1c848d9520aaabfb861cb78); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalaa28c1bda1c848d9520aaabfb861cb78)): ?>
<?php $component = $__componentOriginalaa28c1bda1c848d9520aaabfb861cb78; ?>
<?php unset($__componentOriginalaa28c1bda1c848d9520aaabfb861cb78); ?>
<?php endif; ?>
            </div>
            <div class="flex flex-wrap gap-2">
                <template x-for="feature in getSelectedByCategory('muhit')" :key="feature.id">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-200">
                        <span x-text="feature.name"></span>
                    </span>
                </template>
            </div>
        </div>

        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white">Ulaşım</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Metro, Otobüs, İstasyon...</p>
                    </div>
                </div>
                <?php if (isset($component)) { $__componentOriginalaa28c1bda1c848d9520aaabfb861cb78 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalaa28c1bda1c848d9520aaabfb861cb78 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.feature-modal-selector','data' => ['categorySlug' => 'ulasim','selectedFeatures' => getSelectedByCategory('ulasim'),'ilanId' => $ilanId,'@featuresSelected' => 'handleFeaturesSelected($event.detail)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('feature-modal-selector'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['category-slug' => 'ulasim','selected-features' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(getSelectedByCategory('ulasim')),'ilan-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ilanId),'@features-selected' => 'handleFeaturesSelected($event.detail)']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalaa28c1bda1c848d9520aaabfb861cb78)): ?>
<?php $attributes = $__attributesOriginalaa28c1bda1c848d9520aaabfb861cb78; ?>
<?php unset($__attributesOriginalaa28c1bda1c848d9520aaabfb861cb78); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalaa28c1bda1c848d9520aaabfb861cb78)): ?>
<?php $component = $__componentOriginalaa28c1bda1c848d9520aaabfb861cb78; ?>
<?php unset($__componentOriginalaa28c1bda1c848d9520aaabfb861cb78); ?>
<?php endif; ?>
            </div>
            <div class="flex flex-wrap gap-2">
                <template x-for="feature in getSelectedByCategory('ulasim')" :key="feature.id">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200">
                        <span x-text="feature.name"></span>
                    </span>
                </template>
            </div>
        </div>

        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white">Cephe</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Kuzey, Güney, Doğu, Batı...</p>
                    </div>
                </div>
                <?php if (isset($component)) { $__componentOriginalaa28c1bda1c848d9520aaabfb861cb78 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalaa28c1bda1c848d9520aaabfb861cb78 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.feature-modal-selector','data' => ['categorySlug' => 'cephe','selectedFeatures' => getSelectedByCategory('cephe'),'ilanId' => $ilanId,'@featuresSelected' => 'handleFeaturesSelected($event.detail)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('feature-modal-selector'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['category-slug' => 'cephe','selected-features' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(getSelectedByCategory('cephe')),'ilan-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ilanId),'@features-selected' => 'handleFeaturesSelected($event.detail)']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalaa28c1bda1c848d9520aaabfb861cb78)): ?>
<?php $attributes = $__attributesOriginalaa28c1bda1c848d9520aaabfb861cb78; ?>
<?php unset($__attributesOriginalaa28c1bda1c848d9520aaabfb861cb78); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalaa28c1bda1c848d9520aaabfb861cb78)): ?>
<?php $component = $__componentOriginalaa28c1bda1c848d9520aaabfb861cb78; ?>
<?php unset($__componentOriginalaa28c1bda1c848d9520aaabfb861cb78); ?>
<?php endif; ?>
            </div>
            <div class="flex flex-wrap gap-2">
                <template x-for="feature in getSelectedByCategory('cephe')" :key="feature.id">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-pink-100 dark:bg-pink-900/30 text-pink-800 dark:text-pink-200">
                        <span x-text="feature.name"></span>
                    </span>
                </template>
            </div>
        </div>

        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-cyan-100 dark:bg-cyan-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white">Manzara</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Deniz, Dağ, Boğaz, Park...</p>
                    </div>
                </div>
                <?php if (isset($component)) { $__componentOriginalaa28c1bda1c848d9520aaabfb861cb78 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalaa28c1bda1c848d9520aaabfb861cb78 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.feature-modal-selector','data' => ['categorySlug' => 'manzara','selectedFeatures' => getSelectedByCategory('manzara'),'ilanId' => $ilanId,'@featuresSelected' => 'handleFeaturesSelected($event.detail)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('feature-modal-selector'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['category-slug' => 'manzara','selected-features' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(getSelectedByCategory('manzara')),'ilan-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ilanId),'@features-selected' => 'handleFeaturesSelected($event.detail)']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalaa28c1bda1c848d9520aaabfb861cb78)): ?>
<?php $attributes = $__attributesOriginalaa28c1bda1c848d9520aaabfb861cb78; ?>
<?php unset($__attributesOriginalaa28c1bda1c848d9520aaabfb861cb78); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalaa28c1bda1c848d9520aaabfb861cb78)): ?>
<?php $component = $__componentOriginalaa28c1bda1c848d9520aaabfb861cb78; ?>
<?php unset($__componentOriginalaa28c1bda1c848d9520aaabfb861cb78); ?>
<?php endif; ?>
            </div>
            <div class="flex flex-wrap gap-2">
                <template x-for="feature in getSelectedByCategory('manzara')" :key="feature.id">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-cyan-100 dark:bg-cyan-900/30 text-cyan-800 dark:text-cyan-200">
                        <span x-text="feature.name"></span>
                    </span>
                </template>
            </div>
        </div>
    </div>

    
    <div class="mt-6">
        <?php if (isset($component)) { $__componentOriginala661a8eb3eb1177bccb3ec05e970bf44 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala661a8eb3eb1177bccb3ec05e970bf44 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.fixtures-manager','data' => ['fixtures' => [],'ilanId' => $ilanId]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('fixtures-manager'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['fixtures' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([]),'ilan-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ilanId)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala661a8eb3eb1177bccb3ec05e970bf44)): ?>
<?php $attributes = $__attributesOriginala661a8eb3eb1177bccb3ec05e970bf44; ?>
<?php unset($__attributesOriginala661a8eb3eb1177bccb3ec05e970bf44); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala661a8eb3eb1177bccb3ec05e970bf44)): ?>
<?php $component = $__componentOriginala661a8eb3eb1177bccb3ec05e970bf44; ?>
<?php unset($__componentOriginala661a8eb3eb1177bccb3ec05e970bf44); ?>
<?php endif; ?>
    </div>

    
    <template x-for="(feature, index) in allSelectedFeatures" :key="index">
        <input type="hidden" 
               :name="`features[${index}][id]`" 
               :value="feature.id">
        <input type="hidden" 
               :name="`features[${index}][category_slug]`" 
               :value="feature.categorySlug">
    </template>
</div>

<script>
function featureCategoriesModal(config) {
    return {
        ilanId: config.ilanId || null,
        selectedFeatures: config.selectedFeatures || [],
        allSelectedFeatures: [],
        categories: [],
        
        init() {
            // Initialize selected features by category
            this.allSelectedFeatures = this.selectedFeatures || [];
        },
        
        async loadCategories() {
            try {
                const response = await fetch('/api/admin/features/categories');
                const data = await response.json();
                if (data.success) {
                    this.categories = data.categories || [];
                }
            } catch (error) {
                console.error('Categories load error:', error);
            }
        },
        
        getSelectedByCategory(categorySlug) {
            return this.allSelectedFeatures.filter(f => f.categorySlug === categorySlug);
        },
        
        handleFeaturesSelected(detail) {
            // Remove old features for this category
            this.allSelectedFeatures = this.allSelectedFeatures.filter(
                f => f.categorySlug !== detail.categorySlug
            );
            
            // Add new features
            const newFeatures = detail.features.map(f => ({
                ...f,
                categorySlug: detail.categorySlug
            }));
            this.allSelectedFeatures = [...this.allSelectedFeatures, ...newFeatures];
            
            // Dispatch event
            this.$dispatch('all-features-updated', {
                features: this.allSelectedFeatures
            });
        }
    }
}
</script>

<?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/components/feature-categories-modal.blade.php ENDPATH**/ ?>