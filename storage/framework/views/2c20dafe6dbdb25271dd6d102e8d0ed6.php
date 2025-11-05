<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'label' => null,
    'name',
    'required' => false,
    'help' => null,
    'value' => null,
    'rows' => 3,
    'wrapperClass' => 'mb-4',
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'label' => null,
    'name',
    'required' => false,
    'help' => null,
    'value' => null,
    'rows' => 3,
    'wrapperClass' => 'mb-4',
]); ?>
<?php foreach (array_filter(([
    'label' => null,
    'name',
    'required' => false,
    'help' => null,
    'value' => null,
    'rows' => 3,
    'wrapperClass' => 'mb-4',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>
<div class="<?php echo e($wrapperClass); ?>">
    <?php if($label): ?>
        <label for="<?php echo e($name); ?>" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            <?php echo e($label); ?> <?php if($required): ?>
                <span class="text-red-500">*</span>
            <?php endif; ?>
        </label>
    <?php endif; ?>
    <textarea
        <?php echo e($attributes->merge(['class' => 'w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-800 text-black dark:text-white placeholder-gray-400 dark:placeholder-gray-500 resize-y'])); ?>

        id="<?php echo e($name); ?>" name="<?php echo e($name); ?>" rows="<?php echo e($rows); ?>"
        <?php if($required): ?> required <?php endif; ?>>
<?php if(old($name)): ?>
<?php echo e(old($name)); ?><?php else: ?><?php echo e($value); ?>

<?php endif; ?>
</textarea>
    <?php if($help): ?>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400"><?php echo $help; ?></p>
    <?php endif; ?>
    <?php $__errorArgs = [$name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>
<?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/components/admin/textarea.blade.php ENDPATH**/ ?>