<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'locales' => config('localization.supported_locales', []),
    'currentLocale' => app()->getLocale(),
    'currencies' => config('currency.supported', []),
    'currentCurrency' => session('currency', config('currency.default', 'TRY')),
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'locales' => config('localization.supported_locales', []),
    'currentLocale' => app()->getLocale(),
    'currencies' => config('currency.supported', []),
    'currentCurrency' => session('currency', config('currency.default', 'TRY')),
]); ?>
<?php foreach (array_filter(([
    'locales' => config('localization.supported_locales', []),
    'currentLocale' => app()->getLocale(),
    'currencies' => config('currency.supported', []),
    'currentCurrency' => session('currency', config('currency.default', 'TRY')),
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 dark:from-blue-700 dark:via-indigo-700 dark:to-purple-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex items-center justify-between text-white">
        <div class="flex items-center gap-2 text-sm">
            <i class="fas fa-globe-americas"></i>
            <span class="font-medium">Global Portfolio</span>
        </div>

        <div class="flex items-center gap-3 text-sm" data-preference-switcher>
            <div class="flex items-center gap-1">
                <span class="uppercase opacity-75">Dil</span>
                <div class="inline-flex rounded-full bg-white/10 p-1" role="group" aria-label="Dil tercihleri">
                    <?php $__currentLoopData = $locales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $locale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $isActive = $currentLocale === $code;
                        ?>
                        <button
                            type="button"
                            data-preference-locale="<?php echo e($code); ?>"
                            class="px-3 py-1 rounded-full text-xs font-semibold transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80 <?php echo e($isActive ? 'bg-white text-blue-700 dark:text-blue-900 shadow-lg' : 'hover:bg-white/20 text-white/90'); ?>"
                            aria-pressed="<?php echo e($isActive ? 'true' : 'false'); ?>"
                        >
                            <?php echo e(strtoupper($code)); ?>

                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <div class="hidden sm:flex items-center gap-1">
                <span class="uppercase opacity-75">Para Birimi</span>
                <div class="inline-flex rounded-full bg-white/10 p-1" role="group" aria-label="Para birimi tercihleri">
                    <?php $__currentLoopData = $currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $isActiveCurrency = strtoupper($currentCurrency) === strtoupper($code);
                        ?>
                        <button
                            type="button"
                            data-preference-currency="<?php echo e(strtoupper($code)); ?>"
                            class="px-3 py-1 rounded-full text-xs font-semibold transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80 <?php echo e($isActiveCurrency ? 'bg-white text-blue-700 dark:text-blue-900 shadow-lg' : 'hover:bg-white/20 text-white/90'); ?>"
                            aria-pressed="<?php echo e($isActiveCurrency ? 'true' : 'false'); ?>"
                        >
                            <?php echo e(strtoupper($code)); ?>

                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <div class="sm:hidden flex items-center gap-2">
                <label for="mobile-currency-switcher" class="uppercase opacity-75 text-xs">Para Birimi</label>
                <div class="relative">
                    <select id="mobile-currency-switcher" data-preference-currency-select class="appearance-none pl-3 pr-8 py-1.5 rounded-full text-xs font-semibold bg-white/90 text-blue-700 dark:text-blue-900 shadow focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80">
                        <?php $__currentLoopData = $currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e(strtoupper($code)); ?>" <?php if(strtoupper($currentCurrency) === strtoupper($code)): echo 'selected'; endif; ?>>
                                <?php echo e(strtoupper($code)); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <i class="fas fa-chevron-down absolute right-2 top-1/2 -translate-y-1/2 text-[10px] text-blue-600"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php /**PATH /Users/macbookpro/Projects/yalihanai /resources/views/components/frontend/global/topbar.blade.php ENDPATH**/ ?>