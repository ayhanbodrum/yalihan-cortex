<?php $__env->startSection('title', 'Yeni Özellik Kategorisi'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Yeni Özellik Kategorisi</h1>
        <a href="<?php echo e(route('admin.ozellikler.kategoriler.index')); ?>"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg
                   hover:bg-gray-200 dark:hover:bg-gray-600 hover:scale-105 active:scale-95
                   focus:ring-2 focus:ring-gray-500 focus:ring-offset-2
                   transition-all duration-200 shadow-sm hover:shadow-md">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700
                transition-all duration-300 ease-in-out hover:shadow-xl">
        <div class="p-6">
            <form action="<?php echo e(route('admin.ozellikler.kategoriler.store')); ?>" method="POST" x-data="{ showAdvanced: false }"
                onsubmit="const btn = document.getElementById('ozellik-kategori-submit-btn'); const icon = document.getElementById('ozellik-kategori-submit-icon'); const text = document.getElementById('ozellik-kategori-submit-text'); const spinner = document.getElementById('ozellik-kategori-submit-spinner'); if(btn && icon && text && spinner) { btn.disabled = true; icon.classList.add('hidden'); spinner.classList.remove('hidden'); text.textContent = 'Kaydediliyor...'; }">
                <?php echo csrf_field(); ?>
                <?php if($errors->any()): ?>
                    <div
                        class="bg-red-50 dark:bg-red-900/20 border-2 border-red-300 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg mb-6
                                transition-all duration-200 animate-pulse">
                        <p class="font-semibold mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                            Lütfen aşağıdaki hataları düzeltin:
                        </p>
                        <ul class="list-disc list-inside space-y-1 ml-2">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Kategori Adı <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="<?php echo e(old('name')); ?>" required
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                       transition-all duration-200
                                       hover:border-gray-400 dark:hover:border-gray-500">
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Açıklama
                            </label>
                            <textarea id="description" name="description" rows="4"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                       transition-all duration-200
                                       hover:border-gray-400 dark:hover:border-gray-500
                                       resize-y"><?php echo e(old('description')); ?></textarea>
                            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Uygulama Alanı
                            </label>
                            <select style="color-scheme: light dark;" name="applies_to"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                       transition-all duration-200
                                       hover:border-gray-400 dark:hover:border-gray-500
                                       cursor-pointer">
                                <option value="">Tüm Emlak Türleri</option>
                                <option value="konut" <?php echo e(old('applies_to') == 'konut' ? 'selected' : ''); ?>>Konut</option>
                                <option value="arsa" <?php echo e(old('applies_to') == 'arsa' ? 'selected' : ''); ?>>Arsa</option>
                                <option value="yazlik" <?php echo e(old('applies_to') == 'yazlik' ? 'selected' : ''); ?>>Yazlık</option>
                                <option value="isyeri" <?php echo e(old('applies_to') == 'isyeri' ? 'selected' : ''); ?>>İşyeri
                                </option>
                                <option value="konut,arsa" <?php echo e(old('applies_to') == 'konut,arsa' ? 'selected' : ''); ?>>Konut
                                    + Arsa</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Bu kategori hangi emlak türleri için
                                geçerli olsun?</p>
                        </div>

                        <div class="mb-4">
                            <label for="order" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Sıra
                            </label>
                            <input type="number" id="order" name="order" value="<?php echo e(old('order', 0)); ?>"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                       transition-all duration-200
                                       hover:border-gray-400 dark:hover:border-gray-500">
                            <?php $__errorArgs = ['order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="mb-4">
                            <label class="flex items-center cursor-pointer group">
                                <input type="checkbox" name="status" value="1"
                                    <?php echo e(old('status', true) ? 'checked' : ''); ?>

                                    class="w-4 h-4 rounded border-gray-300 text-blue-600 shadow-sm
                                           focus:border-blue-300 focus:ring-2 focus:ring-blue-200 focus:ring-opacity-50
                                           transition-all duration-200
                                           cursor-pointer
                                           group-hover:border-blue-400">
                                <span
                                    class="ml-2 text-sm font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200">Aktif</span>
                            </label>
                            <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4">
                            <button type="button" @click="showAdvanced=!showAdvanced"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400
                                       hover:text-indigo-700 dark:hover:text-indigo-300
                                       hover:bg-indigo-50 dark:hover:bg-indigo-900/20
                                       rounded-lg transition-all duration-200
                                       hover:scale-105 active:scale-95
                                       focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                <svg class="w-4 h-4 transition-transform duration-200"
                                    :class="showAdvanced ? 'rotate-45' : ''" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Gelişmiş Alanlar
                            </button>
                        </div>
                        <div x-show="showAdvanced" x-cloak x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform -translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 transform translate-y-0"
                            x-transition:leave-end="opacity-0 transform -translate-y-2">
                            <div class="mb-4">
                                <label for="icon" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    İkon
                                </label>
                                <input type="text" id="icon" name="icon" value="<?php echo e(old('icon')); ?>"
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg
                                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                           transition-all duration-200
                                           hover:border-gray-400 dark:hover:border-gray-500"
                                    placeholder="Örn: fas fa-home">
                                <?php $__errorArgs = ['icon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="mb-4">
                                <label for="renk" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    Renk
                                </label>
                                <input type="color" id="renk" name="renk" value="<?php echo e(old('renk', '#3B82F6')); ?>"
                                    class="w-full h-12 px-2 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg
                                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                           transition-all duration-200
                                           hover:border-gray-400 dark:hover:border-gray-500
                                           cursor-pointer">
                                <?php $__errorArgs = ['renk'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="<?php echo e(route('admin.ozellikler.kategoriler.index')); ?>"
                        class="inline-flex items-center px-6 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg
                               hover:bg-gray-300 dark:hover:bg-gray-600 hover:scale-105 active:scale-95
                               focus:ring-2 focus:ring-gray-500 focus:ring-offset-2
                               transition-all duration-200 shadow-sm hover:shadow-md">
                        İptal
                    </a>
                    <button type="submit" id="ozellik-kategori-submit-btn"
                        class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
                        <svg id="ozellik-kategori-submit-icon" class="fas fa-save mr-2"></svg>
                        <span id="ozellik-kategori-submit-text">Kaydet</span>
                        <svg id="ozellik-kategori-submit-spinner" class="hidden w-4 h-4 mr-2 animate-spin" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.neo', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/macbookpro/Projects/yalihanai /resources/views/admin/ozellikler/kategoriler/create.blade.php ENDPATH**/ ?>