<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'title' => '🏠 Yalıhan Emlak',
    'subtitle' => 'Bodrum\'un en güzel emlakları burada!',
    'showSearch' => true,
    'backgroundImage' => null,
    'overlay' => true,
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'title' => '🏠 Yalıhan Emlak',
    'subtitle' => 'Bodrum\'un en güzel emlakları burada!',
    'showSearch' => true,
    'backgroundImage' => null,
    'overlay' => true,
]); ?>
<?php foreach (array_filter(([
    'title' => '🏠 Yalıhan Emlak',
    'subtitle' => 'Bodrum\'un en güzel emlakları burada!',
    'showSearch' => true,
    'backgroundImage' => null,
    'overlay' => true,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<section
    class="hero-section relative <?php echo e($backgroundImage ? 'bg-cover bg-center' : 'bg-gradient-to-br from-blue-600 via-purple-600 to-blue-800 dark:from-blue-900 dark:via-purple-900 dark:to-blue-950'); ?> py-16 md:py-24 text-white overflow-hidden transition-all duration-300">
    <?php if($backgroundImage): ?>
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('<?php echo e($backgroundImage); ?>')"></div>
    <?php endif; ?>

    <?php if($overlay): ?>
        <div class="absolute inset-0 bg-black bg-opacity-40 dark:bg-opacity-60 transition-opacity duration-300"></div>
    <?php endif; ?>

    <div class="container mx-auto px-4 relative z-10">
        <div class="text-center max-w-4xl mx-auto">
            <!-- Main Title -->
            <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight transition-all duration-300">
                <?php echo e($title); ?>

            </h1>

            <!-- Subtitle -->
            <p class="text-xl md:text-2xl mb-8 opacity-90 dark:opacity-95 transition-opacity duration-300">
                <?php echo e($subtitle); ?>

            </p>

            <?php if($showSearch): ?>
                <div class="mt-10">
                    <?php if (isset($component)) { $__componentOriginal13b6b33bba92c189f8b10880f01a6683 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal13b6b33bba92c189f8b10880f01a6683 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.yaliihan.hero-search-tabs','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('yaliihan.hero-search-tabs'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal13b6b33bba92c189f8b10880f01a6683)): ?>
<?php $attributes = $__attributesOriginal13b6b33bba92c189f8b10880f01a6683; ?>
<?php unset($__attributesOriginal13b6b33bba92c189f8b10880f01a6683); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal13b6b33bba92c189f8b10880f01a6683)): ?>
<?php $component = $__componentOriginal13b6b33bba92c189f8b10880f01a6683; ?>
<?php unset($__componentOriginal13b6b33bba92c189f8b10880f01a6683); ?>
<?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Stats -->
            <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center transform hover:scale-105 transition-transform duration-300">
                    <div class="text-4xl font-bold mb-2">500+</div>
                    <div class="text-lg opacity-90 dark:opacity-95">Aktif İlan</div>
                </div>
                <div class="text-center transform hover:scale-105 transition-transform duration-300">
                    <div class="text-4xl font-bold mb-2">20+</div>
                    <div class="text-lg opacity-90 dark:opacity-95">Yıl Deneyim</div>
                </div>
                <div class="text-center transform hover:scale-105 transition-transform duration-300">
                    <div class="text-4xl font-bold mb-2">1000+</div>
                    <div class="text-lg opacity-90 dark:opacity-95">Mutlu Müşteri</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Elements -->
    <div class="absolute top-20 left-10 w-20 h-20 bg-white bg-opacity-10 dark:bg-opacity-20 rounded-full animate-pulse transition-opacity duration-300"></div>
    <div class="absolute bottom-20 right-10 w-16 h-16 bg-white bg-opacity-10 dark:bg-opacity-20 rounded-full animate-pulse delay-1000 transition-opacity duration-300"></div>
    <div class="absolute top-1/2 left-20 w-12 h-12 bg-white bg-opacity-10 dark:bg-opacity-20 rounded-full animate-pulse delay-500 transition-opacity duration-300"></div>
</section>

<?php $__env->startPush('styles'); ?>
<style>
    .hero-section {
        position: relative;
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-20px);
        }
    }

    .animate-float {
        animation: float 6s ease-in-out infinite;
    }

    .animate-float:nth-child(2) {
        animation-delay: 2s;
    }

    .animate-float:nth-child(3) {
        animation-delay: 4s;
    }
</style>
<?php $__env->stopPush(); ?>
<?php /**PATH /Users/macbookpro/Projects/yalihanai /resources/views/components/yaliihan/hero-section.blade.php ENDPATH**/ ?>