

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'open' => false,
    'title' => null,
    'size' => 'md',
    'bind' => null,
    'position' => 'center',
    'scrollable' => false,
    'closeOnBackdrop' => true,
    'closeOnEsc' => true,
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'open' => false,
    'title' => null,
    'size' => 'md',
    'bind' => null,
    'position' => 'center',
    'scrollable' => false,
    'closeOnBackdrop' => true,
    'closeOnEsc' => true,
]); ?>
<?php foreach (array_filter(([
    'open' => false,
    'title' => null,
    'size' => 'md',
    'bind' => null,
    'position' => 'center',
    'scrollable' => false,
    'closeOnBackdrop' => true,
    'closeOnEsc' => true,
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
    $modalVar = $isBound ? $bind : 'open';
?>

<div 
    <?php if(!$isBound): ?> x-data="{ open: <?php echo e($open ? 'true' : 'false'); ?> }" <?php endif; ?>
    <?php if($isBound): ?> x-show="<?php echo e($bind); ?>" <?php else: ?> x-show="open" <?php endif; ?>
    <?php if($closeOnEsc): ?> @keydown.escape.window="<?php if($isBound): ?><?php echo e($bind); ?> = false <?php else: ?> open = false <?php endif; ?>" <?php endif; ?>
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
>
    
    <div 
        x-show="<?php if($isBound): ?><?php echo e($bind); ?><?php else: ?> open <?php endif; ?>"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm"
        <?php if($closeOnBackdrop): ?> 
            @click="<?php if($isBound): ?><?php echo e($bind); ?> = false <?php else: ?> open = false <?php endif; ?>"
        <?php endif; ?>
        aria-hidden="true"
    ></div>

    
    <div class="flex min-h-screen items-<?php echo e($position === 'top' ? 'start' : 'center'); ?> justify-center <?php echo e($position === 'top' ? 'pt-20' : 'p-4'); ?>">
        
        <div 
            x-show="<?php if($isBound): ?><?php echo e($bind); ?><?php else: ?> open <?php endif; ?>"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            @click.stop
            class="relative bg-white dark:bg-gray-900 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 <?php echo e($scrollable ? 'max-h-[90vh] flex flex-col' : ''); ?>"
            :class="{
                'w-full max-w-sm': '<?php echo e($size); ?>' === 'sm',
                'w-full max-w-lg': '<?php echo e($size); ?>' === 'md',
                'w-full max-w-2xl': '<?php echo e($size); ?>' === 'lg',
                'w-full max-w-4xl': '<?php echo e($size); ?>' === 'xl',
                'w-full max-w-full mx-4': '<?php echo e($size); ?>' === 'full',
            }"
        >
            
            <?php if($title): ?>
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between <?php echo e($scrollable ? 'flex-shrink-0' : ''); ?>">
                <h3 id="modal-title" class="text-lg font-semibold text-gray-900 dark:text-white">
                    <?php echo e($title); ?>

                </h3>
                <button 
                    type="button"
                    @click="<?php if($isBound): ?><?php echo e($bind); ?> = false <?php else: ?> open = false <?php endif; ?>"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 
                           transition-colors duration-200 p-1 rounded-lg 
                           hover:bg-gray-100 dark:hover:bg-gray-800
                           focus:outline-none focus:ring-2 focus:ring-blue-500"
                    aria-label="Close modal"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <?php endif; ?>

            
            <div class="p-6 <?php echo e($scrollable ? 'overflow-y-auto flex-1' : ''); ?>">
                <?php echo e($slot); ?>

            </div>

            
            <?php if(isset($footer)): ?>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-b-xl <?php echo e($scrollable ? 'flex-shrink-0' : ''); ?>">
                <?php echo e($footer); ?>

            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/components/admin/modal.blade.php ENDPATH**/ ?>