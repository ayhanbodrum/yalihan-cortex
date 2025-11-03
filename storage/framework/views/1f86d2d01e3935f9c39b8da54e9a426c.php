

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['name', 'label', 'type' => 'text', 'required' => false, 'placeholder' => '', 'value' => '', 'helpText' => null, 'icon' => null]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['name', 'label', 'type' => 'text', 'required' => false, 'placeholder' => '', 'value' => '', 'helpText' => null, 'icon' => null]); ?>
<?php foreach (array_filter((['name', 'label', 'type' => 'text', 'required' => false, 'placeholder' => '', 'value' => '', 'helpText' => null, 'icon' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div <?php echo e($attributes->merge(['class' => ''])); ?>>
    
    <label for="<?php echo e($name); ?>" class="block text-sm font-medium text-gray-700 mb-2">
        <?php if($icon): ?>
            <i class="<?php echo e($icon); ?> mr-1"></i>
        <?php endif; ?>
        <?php echo e($label); ?>

        <?php if($required): ?>
            <span class="text-red-500">*</span>
        <?php endif; ?>
    </label>

    
    <input type="<?php echo e($type); ?>"
           id="<?php echo e($name); ?>"
           name="<?php echo e($name); ?>"
           class="neo-form-input <?php $__errorArgs = [$name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
           placeholder="<?php echo e($placeholder); ?>"
           value="<?php echo e(old($name, $value)); ?>"
           <?php echo e($required ? 'required' : ''); ?>

           <?php echo e($attributes->except(['class'])); ?>>

    
    <?php if($helpText): ?>
        <p class="text-xs text-gray-500 mt-1">
            <i class="fas fa-info-circle mr-1"></i><?php echo e($helpText); ?>

        </p>
    <?php endif; ?>

    
    <?php $__errorArgs = [$name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <p class="text-red-500 text-xs mt-1">
            <i class="fas fa-exclamation-circle mr-1"></i><?php echo e($message); ?>

        </p>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>
<?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/components/neo-input.blade.php ENDPATH**/ ?>