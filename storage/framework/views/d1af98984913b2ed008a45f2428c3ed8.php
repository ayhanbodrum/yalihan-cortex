<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'variant' => 'default',
    'class' => '',
    'header' => null,
    'footer' => null
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'variant' => 'default',
    'class' => '',
    'header' => null,
    'footer' => null
]); ?>
<?php foreach (array_filter(([
    'variant' => 'default',
    'class' => '',
    'header' => null,
    'footer' => null
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php
    $baseClasses = 'bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden';

    $variantClasses = [
        'default' => 'shadow-sm',
        'elevated' => 'shadow-lg hover:shadow-xl transition-shadow duration-200',
        'outlined' => 'border-2 border-gray-300 dark:border-gray-600',
        'gradient' => 'bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-900 border-blue-200 dark:border-gray-700',
        'glass' => 'bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border-white/20 dark:border-gray-700/50'
    ];

    $classes = $baseClasses . ' ' . $variantClasses[$variant] . ' ' . $class;
?>

<div <?php echo e($attributes->merge(['class' => $classes])); ?>>
    <?php if($header): ?>
        <div class="card-header px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
            <?php echo e($header); ?>

        </div>
    <?php endif; ?>

    <div class="card-body p-6">
        <?php echo e($slot); ?>

    </div>

    <?php if($footer): ?>
        <div class="card-footer px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
            <?php echo e($footer); ?>

        </div>
    <?php endif; ?>
</div>
<?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/components/context7/card.blade.php ENDPATH**/ ?>