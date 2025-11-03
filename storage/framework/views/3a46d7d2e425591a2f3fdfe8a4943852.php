

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['name', 'label', 'options' => [], 'required' => false, 'value' => '', 'placeholder' => 'Seçiniz', 'helpText' => null, 'icon' => null]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['name', 'label', 'options' => [], 'required' => false, 'value' => '', 'placeholder' => 'Seçiniz', 'helpText' => null, 'icon' => null]); ?>
<?php foreach (array_filter((['name', 'label', 'options' => [], 'required' => false, 'value' => '', 'placeholder' => 'Seçiniz', 'helpText' => null, 'icon' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
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

    
    <select name="<?php echo e($name); ?>"
            id="<?php echo e($name); ?>"
            class="neo-form-input <?php $__errorArgs = [$name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            <?php echo e($required ? 'required' : ''); ?>

            <?php echo e($attributes->except(['class'])); ?>>

        
        <option value=""><?php echo e($placeholder); ?></option>

        
        <?php if(is_array($options) || $options instanceof \Illuminate\Support\Collection): ?>
            <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $optionLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($key); ?>" <?php echo e(old($name, $value) == $key ? 'selected' : ''); ?>>
                    <?php echo e($optionLabel); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            
            <?php echo e($slot); ?>

        <?php endif; ?>
    </select>

    
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
<?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/components/neo-select.blade.php ENDPATH**/ ?>