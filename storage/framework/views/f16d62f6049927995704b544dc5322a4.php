

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'name' => '',
    'label' => '',
    'checked' => false,
    'disabled' => false,
    'error' => null,
    'help' => null,
    'id' => null,
    'size' => 'md',
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'name' => '',
    'label' => '',
    'checked' => false,
    'disabled' => false,
    'error' => null,
    'help' => null,
    'id' => null,
    'size' => 'md',
]); ?>
<?php foreach (array_filter(([
    'name' => '',
    'label' => '',
    'checked' => false,
    'disabled' => false,
    'error' => null,
    'help' => null,
    'id' => null,
    'size' => 'md',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php
$toggleId = $id ?? 'toggle-' . $name;
$hasError = !empty($error);

// Size variants
$sizes = [
    'sm' => [
        'switch' => 'w-9 h-5',
        'toggle' => 'w-4 h-4',
        'translate' => 'translate-x-4',
    ],
    'md' => [
        'switch' => 'w-11 h-6',
        'toggle' => 'w-5 h-5',
        'translate' => 'translate-x-5',
    ],
    'lg' => [
        'switch' => 'w-14 h-7',
        'toggle' => 'w-6 h-6',
        'translate' => 'translate-x-7',
    ],
];

$sizeClasses = $sizes[$size] ?? $sizes['md'];
?>

<div 
    x-data="{ enabled: <?php echo e($checked ? 'true' : 'false'); ?> }"
    class="flex items-start"
>
    
    <button
        type="button"
        @click="if (!<?php echo e($disabled ? 'true' : 'false'); ?>) enabled = !enabled"
        @keydown.space.prevent="if (!<?php echo e($disabled ? 'true' : 'false'); ?>) enabled = !enabled"
        @keydown.enter.prevent="if (!<?php echo e($disabled ? 'true' : 'false'); ?>) enabled = !enabled"
        :aria-checked="enabled"
        :aria-labelledby="'<?php echo e($toggleId); ?>-label'"
        :aria-describedby="'<?php echo e($help ? $toggleId . '-help' : ''); ?> <?php echo e($hasError ? $toggleId . '-error' : ''); ?>'"
        role="switch"
        class="relative inline-flex <?php echo e($sizeClasses['switch']); ?> flex-shrink-0 
               rounded-full transition-colors duration-200 ease-in-out
               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-900
               <?php echo e($disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'); ?>

               <?php echo e($hasError ? 'ring-2 ring-red-500' : ''); ?>"
        :class="{
            'bg-blue-600': enabled && !<?php echo e($hasError ? 'true' : 'false'); ?>,
            'bg-gray-200 dark:bg-gray-700': !enabled && !<?php echo e($hasError ? 'true' : 'false'); ?>,
            'bg-red-600': enabled && <?php echo e($hasError ? 'true' : 'false'); ?>,
            'bg-red-200': !enabled && <?php echo e($hasError ? 'true' : 'false'); ?>

        }"
    >
        
        <span
            aria-hidden="true"
            class="<?php echo e($sizeClasses['toggle']); ?> inline-block rounded-full bg-white shadow-lg
                   transform ring-0 transition-transform duration-200 ease-in-out
                   translate-x-0.5"
            :class="{ '<?php echo e($sizeClasses['translate']); ?>': enabled }"
        ></span>
    </button>

    
    <input
        type="hidden"
        id="<?php echo e($toggleId); ?>"
        name="<?php echo e($name); ?>"
        :value="enabled ? '1' : '0'"
        <?php echo e($disabled ? 'disabled' : ''); ?>

    />

    
    <div class="ml-3">
        <?php if($label): ?>
        <span 
            id="<?php echo e($toggleId); ?>-label"
            class="text-sm font-medium text-gray-900 dark:text-white block
                   <?php echo e($disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'); ?>"
            @click="if (!<?php echo e($disabled ? 'true' : 'false'); ?>) enabled = !enabled"
        >
            <?php echo e($label); ?>

        </span>
        <?php endif; ?>

        <?php if($help): ?>
        <p 
            id="<?php echo e($toggleId); ?>-help" 
            class="mt-1 text-xs text-gray-600 dark:text-gray-400"
        >
            <?php echo e($help); ?>

        </p>
        <?php endif; ?>

        <?php if($hasError): ?>
        <p 
            id="<?php echo e($toggleId); ?>-error" 
            class="mt-1 text-xs text-red-600 dark:text-red-400"
            role="alert"
        >
            <?php echo e($error); ?>

        </p>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/components/admin/toggle.blade.php ENDPATH**/ ?>