
<div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-8 hover:shadow-2xl transition-shadow duration-300">
    <!-- Section Header -->
    <div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-lg shadow-blue-500/50 font-bold text-lg">
            1
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Temel Bilgiler
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">İlanınızın başlık ve açıklamasını girin</p>
        </div>
    </div>

    <div class="space-y-8">
        
        <div class="group">
            <label for="baslik" class="block text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-bold">
                    1
                </span>
                İlan Başlığı
                <span class="text-red-500 font-bold">*</span>
                <span class="ml-auto text-xs text-gray-500 dark:text-gray-400">(Maksimum 255 karakter)</span>
            </label>
            <div class="relative">
                <input 
                    type="text" 
                    name="baslik" 
                    id="baslik" 
                    value="<?php echo e(old('baslik')); ?>" 
                    required
                    data-context7-field="baslik"
                    data-validation="required|string|max:255"
                    placeholder="Örn: Bodrum Yalıkavak'ta Deniz Manzaralı Satılık Villa"
                    class="w-full px-5 py-4 text-lg
                           border-2 border-gray-300 dark:border-gray-600 
                           rounded-xl 
                           bg-white dark:bg-gray-800 
                           text-black dark:text-white
                           placeholder-gray-400 dark:placeholder-gray-500
                           focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:focus:border-blue-400
                           transition-all duration-200
                           hover:border-gray-400 dark:hover:border-gray-500
                           disabled:bg-gray-100 disabled:cursor-not-allowed
                           shadow-sm hover:shadow-md focus:shadow-lg">
                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
            </div>
            <?php $__errorArgs = ['baslik'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="mt-2 flex items-center gap-2 text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 px-4 py-2 rounded-lg">
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
            <label for="aciklama" class="block text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-bold">
                    2
                </span>
                İlan Açıklaması
                <span class="ml-auto text-xs text-gray-500 dark:text-gray-400">(Opsiyonel)</span>
            </label>
            <div class="relative">
                <textarea 
                    id="aciklama" 
                    name="aciklama" 
                    rows="8"
                    data-context7-field="aciklama"
                    data-validation="nullable|string"
                    placeholder="İlan açıklamasını buraya yazın... (AI ile otomatik oluşturabilirsiniz)"
                    class="w-full px-5 py-4
                           border-2 border-gray-300 dark:border-gray-600 
                           rounded-xl 
                           bg-white dark:bg-gray-800 
                           text-black dark:text-white
                           placeholder-gray-400 dark:placeholder-gray-500
                           focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:focus:border-blue-400
                           transition-all duration-200
                           hover:border-gray-400 dark:hover:border-gray-500
                           resize-y min-h-[120px] max-h-[400px]
                           shadow-sm hover:shadow-md focus:shadow-lg"><?php echo e(old('aciklama')); ?></textarea>
                <div class="absolute top-4 right-4 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                    </svg>
                </div>
            </div>
            <?php $__errorArgs = ['aciklama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="mt-2 flex items-center gap-2 text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 px-4 py-2 rounded-lg">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <?php echo e($message); ?>

                </div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            
            
            <div class="mt-3 flex items-start gap-3 p-4 bg-gradient-to-r from-purple-50 to-blue-50 dark:from-purple-900/10 dark:to-blue-900/10 border border-purple-200 dark:border-purple-800/30 rounded-xl">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500 to-blue-500 shadow-lg">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-purple-900 dark:text-purple-100 mb-1">
                        💡 AI İpucu
                    </p>
                    <p class="text-xs text-purple-700 dark:text-purple-300">
                        İlan açıklamanızı otomatik oluşturmak için aşağıdaki "AI İçerik" bölümünü kullanabilirsiniz. AI, ilanınızı analiz ederek profesyonel bir açıklama oluşturacaktır.
                    </p>
                </div>
            </div>
        </div>

        
    </div>
</div>
<?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/admin/ilanlar/components/basic-info.blade.php ENDPATH**/ ?>