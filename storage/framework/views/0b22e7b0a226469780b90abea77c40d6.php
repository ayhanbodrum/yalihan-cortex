
<div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-8 hover:shadow-2xl transition-shadow duration-300">
    <!-- Section Header -->
    <div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 text-white shadow-lg shadow-green-500/50 font-bold text-lg">
            2
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                Kategori Sistemi
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">İlanınızın kategori ve yayın tipini seçin</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="group">
            <label for="ana_kategori" class="block text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-xs font-bold">
                    1
                </span>
                Ana Kategori
                <span class="text-red-500 font-bold">*</span>
            </label>
            <div class="relative">
                <select name="ana_kategori_id" 
                    id="ana_kategori" 
                    required
                    data-context7-field="ana_kategori_id" 
                    onchange="loadAltKategoriler(this.value)"
                    class="w-full px-4 py-2.5
                           border-2 border-gray-300 dark:border-gray-600 
                           rounded-xl 
                           bg-white dark:bg-gray-800 
                           text-black dark:text-white
                           focus:ring-4 focus:ring-blue-500 dark:focus:ring-blue-400/20 focus:border-green-500 dark:focus:border-green-400
                           transition-all duration-200
                           hover:border-gray-400 dark:hover:border-gray-500
                           cursor-pointer
                           shadow-sm hover:shadow-md focus:shadow-lg
                           appearance-none">
                    <option value="">Kategori Seçin...</option>
                    <?php $__currentLoopData = $anaKategoriler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($kategori->id); ?>" 
                                data-slug="<?php echo e($kategori->slug); ?>"
                                <?php echo e(old('ana_kategori_id') == $kategori->id ? 'selected' : ''); ?>>
                            <?php echo e($kategori->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400 group-focus-within:text-green-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
            <?php $__errorArgs = ['ana_kategori_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="mt-2 flex items-center gap-2 text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 px-4 py-2.5 rounded-lg">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <?php echo e($message); ?>

                </div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        
        <div class="group" x-data="{ loading: false }">
            <label for="alt_kategori" class="block text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-xs font-bold">
                    2
                </span>
                Alt Kategori
                <span class="text-red-500 font-bold">*</span>
            </label>
            <div class="relative">
                <select name="alt_kategori_id" 
                    id="alt_kategori" 
                    required 
                    data-context7-field="alt_kategori_id" 
                    onchange="loadYayinTipleri(this.value)"
                    class="w-full px-4 py-2.5
                           border-2 border-gray-300 dark:border-gray-600 
                           rounded-xl 
                           bg-white dark:bg-gray-800 
                           text-black dark:text-white
                           focus:ring-4 focus:ring-blue-500 dark:focus:ring-blue-400/20 focus:border-green-500 dark:focus:border-green-400
                           transition-all duration-200
                           hover:border-gray-400 dark:hover:border-gray-500
                           cursor-pointer
                           shadow-sm hover:shadow-md focus:shadow-lg
                           disabled:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-60
                           appearance-none">
                    <option value="">Önce ana kategori seçin...</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400 group-focus-within:text-green-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
            <?php $__errorArgs = ['alt_kategori_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="mt-2 flex items-center gap-2 text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 px-4 py-2.5 rounded-lg">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <?php echo e($message); ?>

                </div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        
        <div class="group">
            <label for="yayin_tipi_id" class="block text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-xs font-bold">
                    3
                </span>
                Yayın Tipi
                <span class="text-red-500 font-bold">*</span>
            </label>
            <div class="relative">
                <select name="yayin_tipi_id" 
                    id="yayin_tipi_id" 
                    required 
                    data-context7-field="yayin_tipi_id" 
                    onchange="dispatchCategoryChanged(); IlanCreateCategories.loadTypeBasedFields();"
                    class="w-full px-4 py-2.5
                           border-2 border-gray-300 dark:border-gray-600 
                           rounded-xl 
                           bg-white dark:bg-gray-800 
                           text-black dark:text-white
                           focus:ring-4 focus:ring-blue-500 dark:focus:ring-blue-400/20 focus:border-green-500 dark:focus:border-green-400
                           transition-all duration-200
                           hover:border-gray-400 dark:hover:border-gray-500
                           cursor-pointer
                           shadow-sm hover:shadow-md focus:shadow-lg
                           disabled:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-60
                           appearance-none">
                    <option value="">Önce alt kategori seçin...</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400 group-focus-within:text-green-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
            <?php $__errorArgs = ['yayin_tipi_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="mt-2 flex items-center gap-2 text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 px-4 py-2.5 rounded-lg">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <?php echo e($message); ?>

                </div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>

    
    <div class="mt-6 flex items-center justify-center gap-2 text-sm text-gray-500 dark:text-gray-400">
        <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 dark:bg-gray-800 rounded-full">
            <span class="w-2 h-2 rounded-full bg-green-500" id="ana-kategori-indicator"></span>
            <span>Ana Kategori</span>
        </div>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 dark:bg-gray-800 rounded-full">
            <span class="w-2 h-2 rounded-full bg-gray-300" id="alt-kategori-indicator"></span>
            <span>Alt Kategori</span>
        </div>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 dark:bg-gray-800 rounded-full">
            <span class="w-2 h-2 rounded-full bg-gray-300" id="yayin-tipi-indicator"></span>
            <span>Yayın Tipi</span>
        </div>
    </div>

    
    <?php echo $__env->make('admin.ilanlar.partials.stable._kategori-dinamik-alanlar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>



<script>
console.log('✅ Kategori sistemi - categories.js modülü kullanılıyor');

</script>
<?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/admin/ilanlar/components/category-system.blade.php ENDPATH**/ ?>