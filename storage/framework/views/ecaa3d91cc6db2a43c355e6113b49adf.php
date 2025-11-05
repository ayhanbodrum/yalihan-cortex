<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'striped' => true,
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'striped' => true,
]); ?>
<?php foreach (array_filter(([
    'striped' => true,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div class="overflow-hidden">
    <div class="overflow-x-auto">
        <table <?php echo e($attributes->merge(['class' => 'c7-table min-w-full'])); ?>>
            <thead class="c7-thead">
                <?php echo e($head ?? ''); ?>

            </thead>
            <tbody class="c7-tbody">
                <?php echo e($slot); ?>

            </tbody>
        </table>
    </div>
</div>
<?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/components/admin/table.blade.php ENDPATH**/ ?>