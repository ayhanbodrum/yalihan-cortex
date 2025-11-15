<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'variant' => 'default', // default, danger, success, warning
    'icon' => null,
    'href' => null,
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'variant' => 'default', // default, danger, success, warning
    'icon' => null,
    'href' => null,
]); ?>
<?php foreach (array_filter(([
    'variant' => 'default', // default, danger, success, warning
    'icon' => null,
    'href' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php
    $baseClasses = 'flex items-center w-full px-4 py-2 text-sm transition-colors duration-150';

    $variantClasses = [
        'default' => 'text-gray-700 hover:bg-gray-100 hover:text-gray-900',
        'danger' => 'text-red-600 hover:bg-red-50 hover:text-red-700',
        'success' => 'text-green-600 hover:bg-green-50 hover:text-green-700',
        'warning' => 'text-yellow-600 hover:bg-yellow-50 hover:text-yellow-700',
    ];

    $classes = $baseClasses . ' ' . $variantClasses[$variant];
?>

<?php if($href): ?>
    <a href="<?php echo e($href); ?>" <?php echo e($attributes->merge(['class' => $classes])); ?>>
        <?php if($icon): ?>
            <span class="mr-3"><?php echo $icon; ?></span>
        <?php endif; ?>
        <?php echo e($slot); ?>

    </a>
<?php else: ?>
    <button <?php echo e($attributes->merge(['class' => $classes])); ?>>
        <?php if($icon): ?>
            <span class="mr-3"><?php echo $icon; ?></span>
        <?php endif; ?>
        <?php echo e($slot); ?>

    </button>
<?php endif; ?>
<?php /**PATH /Users/macbookpro/Projects/yalihanai /resources/views/components/neo/dropdown-item.blade.php ENDPATH**/ ?>