<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'type' => session('toast_type', 'info'),
    'message' => session('success') ?? session('error') ?? session('warning') ?? session('info') ?? '',
    'dismissible' => true,
    'duration' => 5000,
    'position' => 'top-right'
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'type' => session('toast_type', 'info'),
    'message' => session('success') ?? session('error') ?? session('warning') ?? session('info') ?? '',
    'dismissible' => true,
    'duration' => 5000,
    'position' => 'top-right'
]); ?>
<?php foreach (array_filter(([
    'type' => session('toast_type', 'info'),
    'message' => session('success') ?? session('error') ?? session('warning') ?? session('info') ?? '',
    'dismissible' => true,
    'duration' => 5000,
    'position' => 'top-right'
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php
    $typeClasses = [
        'success' => 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800/30',
        'error' => 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/30',
        'warning' => 'bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800/30',
        'info' => 'bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/30'
    ];

    $iconClasses = [
        'success' => 'text-green-600 dark:text-green-400',
        'error' => 'text-red-600 dark:text-red-400',
        'warning' => 'text-yellow-600 dark:text-yellow-400',
        'info' => 'text-blue-600 dark:text-blue-400'
    ];

    $positionClasses = [
        'top-right' => 'top-4 right-4',
        'top-left' => 'top-4 left-4',
        'bottom-right' => 'bottom-4 right-4',
        'bottom-left' => 'bottom-4 left-4',
        'top-center' => 'top-4 left-1/2 -translate-x-1/2',
        'bottom-center' => 'bottom-4 left-1/2 -translate-x-1/2'
    ];
?>

<?php if($message): ?>
    <div x-data="{
        show: true,
        duration: <?php echo e($duration); ?>,
        init() {
            if (this.duration > 0) {
                setTimeout(() => { this.close(); }, this.duration);
            }
        },
        close() {
            this.show = false;
            this.$dispatch('toast-closed', { message: '<?php echo e(addslashes($message)); ?>', type: '<?php echo e($type); ?>' });
        }
    }"
         x-show="show"
         x-transition:enter="transform transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-x-full"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transform transition ease-in duration-300"
         x-transition:leave-start="opacity-100 translate-x-0"
         x-transition:leave-end="opacity-0 translate-x-full"
         class="fixed <?php echo e($positionClasses[$position] ?? $positionClasses['top-right']); ?> z-[9999] pointer-events-auto"
         role="alert" aria-live="assertive" aria-atomic="true">

        <div class="rounded-lg shadow-lg p-4 <?php echo e($typeClasses[$type] ?? $typeClasses['info']); ?> min-w-[320px] max-w-md">
            <div class="flex items-start gap-3 w-full">
                <div class="flex-shrink-0 mt-0.5">
                    <svg class="w-5 h-5 <?php echo e($iconClasses[$type] ?? $iconClasses['info']); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <?php if($type === 'success'): ?>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        <?php elseif($type === 'error'): ?>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        <?php elseif($type === 'warning'): ?>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        <?php else: ?>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        <?php endif; ?>
                    </svg>
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white break-words"><?php echo e($message); ?></p>
                    <?php if(isset($slot) && $slot != ''): ?>
                        <div class="mt-2 text-sm text-gray-600 dark:text-gray-300"><?php echo e($slot); ?></div>
                    <?php endif; ?>
                </div>

                <?php if($dismissible): ?>
                    <button @click="close()" type="button" class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors" aria-label="Bildirimi kapat">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if(session('success')): ?>
    <meta name="toast-success" content="<?php echo e(session('success')); ?>">
<?php endif; ?>
<?php if(session('error')): ?>
    <meta name="toast-error" content="<?php echo e(session('error')); ?>">
<?php endif; ?>
<?php if(session('warning')): ?>
    <meta name="toast-warning" content="<?php echo e(session('warning')); ?>">
<?php endif; ?>
<?php if(session('info')): ?>
    <meta name="toast-info" content="<?php echo e(session('info')); ?>">
<?php endif; ?>
<?php /**PATH /Users/macbookpro/Projects/yalihanai /resources/views/components/admin/toast.blade.php ENDPATH**/ ?>