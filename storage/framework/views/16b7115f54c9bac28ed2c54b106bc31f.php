<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'title' => null,
    'meta' => [],
    'showPerPage' => false,
    'perPageOptions' => [20, 50, 100],
    'listId' => null,
    'listEndpoint' => null,
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'title' => null,
    'meta' => [],
    'showPerPage' => false,
    'perPageOptions' => [20, 50, 100],
    'listId' => null,
    'listEndpoint' => null,
]); ?>
<?php foreach (array_filter(([
    'title' => null,
    'meta' => [],
    'showPerPage' => false,
    'perPageOptions' => [20, 50, 100],
    'listId' => null,
    'listEndpoint' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>
<?php
    $total = $meta['total'] ?? null;
    $currentPage = $meta['current_page'] ?? ($meta['currentPage'] ?? null);
    $lastPage = $meta['last_page'] ?? ($meta['lastPage'] ?? null);
    $perPage = $meta['per_page'] ?? ($meta['perPage'] ?? null);
?>
<div data-meta="true" class="px-6 py-2" <?php if(isset($listId)): ?> data-list-id="<?php echo e($listId); ?>" <?php endif; ?> <?php if(isset($listEndpoint)): ?> data-list-endpoint="<?php echo e($listEndpoint); ?>" <?php endif; ?>>
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <?php if($title): ?>
                <span class="text-sm font-semibold text-gray-900 dark:text-white"><?php echo e($title); ?></span>
            <?php endif; ?>
            <span id="meta-total" class="text-sm text-gray-700 dark:text-gray-300">Toplam: <?php echo e($total ?? '-'); ?></span>
            <span id="meta-page" class="text-sm text-gray-700 dark:text-gray-300">📄 Sayfa: <?php echo e($currentPage ?? 1); ?> / <?php echo e($lastPage ?? 1); ?></span>
        </div>
        <div class="flex items-center gap-2">
            <?php if($showPerPage): ?>
                <label for="per_page_select" class="sr-only">Sayfa başına</label>
                <select id="per_page_select" data-per-page class="border rounded px-2 py-1 text-sm">
                    <?php $__currentLoopData = $perPageOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($opt); ?>" <?php if($perPage == $opt): ?> selected <?php endif; ?>><?php echo e($opt); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            <?php endif; ?>
            <?php if(isset($actions)): ?>
                <?php echo e($actions); ?>

            <?php else: ?>
                <?php echo e($slot); ?>

            <?php endif; ?>
        </div>
    </div>
    <div id="meta-status" role="status" aria-busy="false" aria-live="polite" class="text-sm text-gray-600 dark:text-gray-300"></div>
</div><?php /**PATH /Users/macbookpro/Projects/yalihanai /resources/views/components/admin/meta-info.blade.php ENDPATH**/ ?>