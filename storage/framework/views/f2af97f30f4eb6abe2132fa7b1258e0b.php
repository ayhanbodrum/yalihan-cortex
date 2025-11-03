<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'status' => 'taslak',
    'size' => 'sm',
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'status' => 'taslak',
    'size' => 'sm',
]); ?>
<?php foreach (array_filter(([
    'status' => 'taslak',
    'size' => 'sm',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php
    $statusConfig = [
        'aktif' => [
            'bg' => 'bg-green-100 dark:bg-green-900',
            'text' => 'text-green-800 dark:text-green-200',
            'label' => 'Aktif',
        ],
        'inceleme' => [
            'bg' => 'bg-yellow-100 dark:bg-yellow-900',
            'text' => 'text-yellow-800 dark:text-yellow-200',
            'label' => 'İnceleme',
        ],
        'pasif' => [
            'bg' => 'bg-red-100 dark:bg-red-900',
            'text' => 'text-red-800 dark:text-red-200',
            'label' => 'Pasif',
        ],
        'taslak' => [
            'bg' => 'bg-gray-100 dark:bg-gray-900',
            'text' => 'text-gray-800 dark:text-gray-200',
            'label' => 'Taslak',
        ],
    ];

    $sizeClasses = [
        'xs' => 'px-2 py-0.5 text-xs',
        'sm' => 'px-2.5 py-0.5 text-xs',
        'md' => 'px-3 py-1 text-sm',
        'lg' => 'px-4 py-1.5 text-base',
    ];

    $config = $statusConfig[$status] ?? $statusConfig['taslak'];
    $selectedSize = $sizeClasses[$size] ?? $sizeClasses['sm'];
?>

<span
    class="inline-flex items-center rounded-full font-medium <?php echo e($config['bg']); ?> <?php echo e($config['text']); ?> <?php echo e($selectedSize); ?>"
    <?php echo e($attributes); ?>>
    <?php echo e($config['label']); ?>

</span>
<?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/components/neo/status-badge.blade.php ENDPATH**/ ?>