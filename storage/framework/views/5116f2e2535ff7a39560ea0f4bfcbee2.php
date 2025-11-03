

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'type' => 'text',
    'rows' => 3,
    'items' => 3,
    'height' => 'auto',
    'width' => 'full',
    'rounded' => 'md',
    'animate' => true
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'type' => 'text',
    'rows' => 3,
    'items' => 3,
    'height' => 'auto',
    'width' => 'full',
    'rounded' => 'md',
    'animate' => true
]); ?>
<?php foreach (array_filter(([
    'type' => 'text',
    'rows' => 3,
    'items' => 3,
    'height' => 'auto',
    'width' => 'full',
    'rounded' => 'md',
    'animate' => true
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php
    $animateClass = $animate ? 'neo-skeleton-animate' : '';
    $widthClass = $width === 'full' ? 'w-full' : $width;
    $roundedClass = 'rounded-' . $rounded;
?>


<?php if($type === 'text'): ?>
    <div class="neo-skeleton <?php echo e($animateClass); ?> <?php echo e($widthClass); ?> <?php echo e($roundedClass); ?>"
         style="height: <?php echo e($height === 'auto' ? '1rem' : $height); ?>"
         role="status"
         aria-label="Yükleniyor...">
        <span class="sr-only">Yükleniyor...</span>
    </div>
<?php endif; ?>


<?php if($type === 'heading'): ?>
    <div class="space-y-3">
        <div class="neo-skeleton <?php echo e($animateClass); ?> w-3/4 h-8 <?php echo e($roundedClass); ?>" role="status" aria-label="Başlık yükleniyor...">
            <span class="sr-only">Başlık yükleniyor...</span>
        </div>
        <div class="neo-skeleton <?php echo e($animateClass); ?> w-1/2 h-4 <?php echo e($roundedClass); ?>" role="status" aria-label="Alt başlık yükleniyor...">
            <span class="sr-only">Alt başlık yükleniyor...</span>
        </div>
    </div>
<?php endif; ?>


<?php if($type === 'paragraph'): ?>
    <div class="space-y-2">
        <?php for($i = 0; $i < $rows; $i++): ?>
            <div class="neo-skeleton <?php echo e($animateClass); ?> <?php echo e($i === $rows - 1 ? 'w-3/4' : 'w-full'); ?> h-4 <?php echo e($roundedClass); ?>"
                 role="status"
                 aria-label="Paragraf yükleniyor...">
                <span class="sr-only">Paragraf yükleniyor...</span>
            </div>
        <?php endfor; ?>
    </div>
<?php endif; ?>


<?php if($type === 'card'): ?>
    <div class="neo-card p-6 space-y-4" role="status" aria-label="Kart yükleniyor...">
        <!-- Header -->
        <div class="flex items-center gap-3">
            <div class="neo-skeleton <?php echo e($animateClass); ?> w-12 h-12 rounded-full"></div>
            <div class="flex-1 space-y-2">
                <div class="neo-skeleton <?php echo e($animateClass); ?> w-1/2 h-5 <?php echo e($roundedClass); ?>"></div>
                <div class="neo-skeleton <?php echo e($animateClass); ?> w-1/3 h-4 <?php echo e($roundedClass); ?>"></div>
            </div>
        </div>

        <!-- Body -->
        <div class="space-y-2">
            <?php for($i = 0; $i < 3; $i++): ?>
                <div class="neo-skeleton <?php echo e($animateClass); ?> w-full h-4 <?php echo e($roundedClass); ?>"></div>
            <?php endfor; ?>
            <div class="neo-skeleton <?php echo e($animateClass); ?> w-3/4 h-4 <?php echo e($roundedClass); ?>"></div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="neo-skeleton <?php echo e($animateClass); ?> w-24 h-8 <?php echo e($roundedClass); ?>"></div>
            <div class="neo-skeleton <?php echo e($animateClass); ?> w-24 h-8 <?php echo e($roundedClass); ?>"></div>
        </div>

        <span class="sr-only">Kart yükleniyor...</span>
    </div>
<?php endif; ?>


<?php if($type === 'table'): ?>
    <div class="neo-card overflow-hidden" role="status" aria-label="Tablo yükleniyor...">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                    <?php for($i = 0; $i < 5; $i++): ?>
                        <th class="px-4 py-2.5">
                            <div class="neo-skeleton <?php echo e($animateClass); ?> w-full h-3 <?php echo e($roundedClass); ?>"></div>
                        </th>
                    <?php endfor; ?>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <?php for($r = 0; $r < $rows; $r++): ?>
                    <tr>
                        <?php for($c = 0; $c < 5; $c++): ?>
                            <td class="px-4 py-2.5">
                                <div class="neo-skeleton <?php echo e($animateClass); ?> w-full h-3 <?php echo e($roundedClass); ?>"></div>
                            </td>
                        <?php endfor; ?>
                    </tr>
                <?php endfor; ?>
            </tbody>
        </table>
        <span class="sr-only">Tablo yükleniyor...</span>
    </div>
<?php endif; ?>


<?php if($type === 'list'): ?>
    <div class="space-y-3" role="status" aria-label="Liste yükleniyor...">
        <?php for($i = 0; $i < $items; $i++): ?>
            <div class="flex items-center gap-3 p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="neo-skeleton <?php echo e($animateClass); ?> w-10 h-10 rounded-full"></div>
                <div class="flex-1 space-y-2">
                    <div class="neo-skeleton <?php echo e($animateClass); ?> w-3/4 h-4 <?php echo e($roundedClass); ?>"></div>
                    <div class="neo-skeleton <?php echo e($animateClass); ?> w-1/2 h-3 <?php echo e($roundedClass); ?>"></div>
                </div>
                <div class="neo-skeleton <?php echo e($animateClass); ?> w-20 h-8 <?php echo e($roundedClass); ?>"></div>
            </div>
        <?php endfor; ?>
        <span class="sr-only">Liste yükleniyor...</span>
    </div>
<?php endif; ?>


<?php if($type === 'avatar'): ?>
    <div class="flex items-center gap-3" role="status" aria-label="Profil yükleniyor...">
        <div class="neo-skeleton <?php echo e($animateClass); ?> w-12 h-12 rounded-full"></div>
        <div class="flex-1 space-y-2">
            <div class="neo-skeleton <?php echo e($animateClass); ?> w-32 h-4 <?php echo e($roundedClass); ?>"></div>
            <div class="neo-skeleton <?php echo e($animateClass); ?> w-24 h-3 <?php echo e($roundedClass); ?>"></div>
        </div>
        <span class="sr-only">Profil yükleniyor...</span>
    </div>
<?php endif; ?>


<?php if($type === 'image'): ?>
    <div class="neo-skeleton <?php echo e($animateClass); ?> <?php echo e($widthClass); ?> <?php echo e($roundedClass); ?>"
         style="height: <?php echo e($height === 'auto' ? '200px' : $height); ?>"
         role="status"
         aria-label="Görsel yükleniyor...">
        <div class="flex items-center justify-center h-full">
            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
            </svg>
        </div>
        <span class="sr-only">Görsel yükleniyor...</span>
    </div>
<?php endif; ?>


<?php if($type === 'stats'): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <?php for($i = 0; $i < 4; $i++): ?>
            <div class="neo-card p-6" role="status" aria-label="İstatistik yükleniyor...">
                <div class="flex items-center justify-between">
                    <div class="flex-1 space-y-3">
                        <div class="neo-skeleton <?php echo e($animateClass); ?> w-24 h-4 <?php echo e($roundedClass); ?>"></div>
                        <div class="neo-skeleton <?php echo e($animateClass); ?> w-16 h-8 <?php echo e($roundedClass); ?>"></div>
                    </div>
                    <div class="neo-skeleton <?php echo e($animateClass); ?> w-12 h-12 rounded-full"></div>
                </div>
                <span class="sr-only">İstatistik kartı yükleniyor...</span>
            </div>
        <?php endfor; ?>
    </div>
<?php endif; ?>


<?php if($type === 'form'): ?>
    <div class="neo-card p-6 space-y-6" role="status" aria-label="Form yükleniyor...">
        <?php for($i = 0; $i < $rows; $i++): ?>
            <div class="space-y-2">
                <div class="neo-skeleton <?php echo e($animateClass); ?> w-32 h-4 <?php echo e($roundedClass); ?>"></div>
                <div class="neo-skeleton <?php echo e($animateClass); ?> w-full h-10 <?php echo e($roundedClass); ?>"></div>
            </div>
        <?php endfor; ?>

        <div class="flex items-center gap-3 pt-4">
            <div class="neo-skeleton <?php echo e($animateClass); ?> w-32 h-10 <?php echo e($roundedClass); ?>"></div>
            <div class="neo-skeleton <?php echo e($animateClass); ?> w-32 h-10 <?php echo e($roundedClass); ?>"></div>
        </div>

        <span class="sr-only">Form yükleniyor...</span>
    </div>
<?php endif; ?>


<?php if($type === 'custom'): ?>
    <div class="neo-skeleton <?php echo e($animateClass); ?> <?php echo e($widthClass); ?> <?php echo e($roundedClass); ?>"
         style="height: <?php echo e($height); ?>"
         role="status"
         aria-label="İçerik yükleniyor...">
        <?php echo e($slot); ?>

        <span class="sr-only">İçerik yükleniyor...</span>
    </div>
<?php endif; ?>

<?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/components/admin/neo-skeleton.blade.php ENDPATH**/ ?>