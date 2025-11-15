<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'trigger' => 'button',
    'position' => 'bottom-right', // bottom-right, bottom-left, top-right, top-left
    'size' => 'md', // sm, md, lg
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'trigger' => 'button',
    'position' => 'bottom-right', // bottom-right, bottom-left, top-right, top-left
    'size' => 'md', // sm, md, lg
]); ?>
<?php foreach (array_filter(([
    'trigger' => 'button',
    'position' => 'bottom-right', // bottom-right, bottom-left, top-right, top-left
    'size' => 'md', // sm, md, lg
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php
    $positionClasses = [
        'bottom-right' => 'top-full right-0 mt-1',
        'bottom-left' => 'top-full left-0 mt-1',
        'top-right' => 'bottom-full right-0 mb-1',
        'top-left' => 'bottom-full left-0 mb-1',
    ];

    $sizeClasses = [
        'sm' => 'min-w-32',
        'md' => 'min-w-48',
        'lg' => 'min-w-64',
    ];
?>

<div x-data="{ open: false }" class="relative inline-block text-left">
    <!-- Trigger -->
    <?php if($trigger === 'button'): ?>
        <button @click="open = !open" @click.away="open = false"
            class="inline-flex items-center justify-center w-8 h-8 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z">
                </path>
            </svg>
        </button>
    <?php else: ?>
        <?php echo e($trigger); ?>

    <?php endif; ?>

    <!-- Dropdown Menu -->
    <div x-show="open" x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute <?php echo e($positionClasses[$position]); ?> z-50 <?php echo e($sizeClasses[$size]); ?> bg-white rounded-lg shadow-lg border border-gray-200 py-1"
        style="display: none;">
        <?php echo e($slot); ?>

    </div>
</div>
<?php /**PATH /Users/macbookpro/Projects/yalihanai /resources/views/components/neo/dropdown.blade.php ENDPATH**/ ?>