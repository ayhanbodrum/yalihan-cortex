<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'open' => false,
    'title' => null,
    'size' => 'md',
    'bind' => null,
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'open' => false,
    'title' => null,
    'size' => 'md',
    'bind' => null,
]); ?>
<?php foreach (array_filter(([
    'open' => false,
    'title' => null,
    'size' => 'md',
    'bind' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php
    $isBound = !empty($bind);
?>

<div <?php if($isBound): ?> x-show="<?php echo e($bind); ?>"
    <?php else: ?>
        x-data="{ open: <?php echo e($open ? 'true' : 'false'); ?> }" x-show="open" <?php endif; ?>
    x-cloak x-transition.opacity class="fixed inset-0 z-50">
    <div class="fixed inset-0 bg-black/40"
        @click="<?php if($isBound): ?> <?php echo e($bind); ?> = false <?php else: ?> open = false <?php endif; ?>"></div>
    <div class="relative mx-auto mt-20 bg-white dark:bg-gray-900 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700"
        :class="{
            'w-full max-w-md': '<?php echo e($size); ?>' === 'sm',
            'w-full max-w-lg': '<?php echo e($size); ?>' === 'md',
            'w-full max-w-2xl': '<?php echo e($size); ?>' === 'lg',
        }"
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold"><?php echo e($title); ?></h3>
            <button class="btn btn-ghost px-2 py-1"
                @click="<?php if($isBound): ?> <?php echo e($bind); ?> = false <?php else: ?> open = false <?php endif; ?>">✕</button>
        </div>
        <div class="p-6">
            <?php echo e($slot); ?>

        </div>
        <?php if(isset($footer)): ?>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-b-xl">
                <?php echo e($footer); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/components/admin/modal.blade.php ENDPATH**/ ?>