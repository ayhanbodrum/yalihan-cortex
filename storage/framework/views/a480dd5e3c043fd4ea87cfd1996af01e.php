

<div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-8 hover:shadow-2xl transition-shadow duration-300">
    <!-- Section Header -->
    <div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-fuchsia-600 text-white shadow-lg shadow-purple-500/50 font-bold text-lg">
            6
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Kişi Bilgileri
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">İlan sahibi, ilgili kişi ve danışman seçimi (Context7 Live Search)</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="group">
            <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 text-xs font-bold">
                    1
                </span>
                İlan Sahibi
                <span class="text-red-500 font-bold">*</span>
            </label>
            <div class="context7-live-search relative" data-search-type="kisiler" data-placeholder="İsim veya telefon ara..."
                data-max-results="15" data-creatable="true" data-add-modal-id="add_person_modal"
                data-add-modal-url="/api/kisiler" data-add-modal-title="Yeni İlan Sahibi Ekle">
                <input type="hidden" name="ilan_sahibi_id" id="ilan_sahibi_id" value="<?php echo e(old('ilan_sahibi_id')); ?>">
                <input type="text" id="ilan_sahibi_search"
                    class="w-full px-4 py-2.5
                           border-2 border-gray-300 dark:border-gray-600 
                           rounded-xl 
                           bg-white dark:bg-gray-800 
                           text-black dark:text-white
                           placeholder-gray-400 dark:placeholder-gray-500
                           focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 dark:focus:border-purple-400
                           transition-all duration-200
                           hover:border-gray-400 dark:hover:border-gray-500
                           shadow-sm hover:shadow-md focus:shadow-lg"
                    placeholder="İsim, telefon veya email ile ara..." autocomplete="off">
                <div class="context7-search-results absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border-2 border-purple-300 dark:border-purple-600 rounded-xl shadow-2xl hidden max-h-60 overflow-y-auto">
                </div>
                <button type="button" onclick="openAddPersonModal('owner')"
                    class="mt-3 flex items-center gap-2 text-sm text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 font-medium transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Listede yoksa yeni kişi ekle
                </button>
            </div>
        </div>

        
        <div class="group">
            <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 text-xs font-bold">
                    2
                </span>
                İlgili Kişi
                <span class="ml-auto text-xs text-gray-500 dark:text-gray-400 font-normal">(Opsiyonel)</span>
            </label>
            <div class="context7-live-search relative" data-search-type="kisiler" data-placeholder="İsim veya telefon ara..."
                data-max-results="15" data-creatable="true" data-add-modal-id="add_person_modal">
                <input type="hidden" name="ilgili_kisi_id" id="ilgili_kisi_id" value="<?php echo e(old('ilgili_kisi_id')); ?>">
                <input type="text" id="ilgili_kisi_search"
                    class="w-full px-4 py-2.5
                           border-2 border-gray-300 dark:border-gray-600 
                           rounded-xl 
                           bg-white dark:bg-gray-800 
                           text-black dark:text-white
                           placeholder-gray-400 dark:placeholder-gray-500
                           focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 dark:focus:border-purple-400
                           transition-all duration-200
                           hover:border-gray-400 dark:hover:border-gray-500
                           shadow-sm hover:shadow-md focus:shadow-lg"
                    placeholder="Aracı, avukat vb. ara..." autocomplete="off">
                <div class="context7-search-results absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border-2 border-purple-300 dark:border-purple-600 rounded-xl shadow-2xl hidden max-h-60 overflow-y-auto">
                </div>
                <button type="button" onclick="openAddPersonModal('related')"
                    class="mt-3 flex items-center gap-2 text-sm text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 font-medium transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Listede yoksa yeni kişi ekle
                </button>
            </div>
        </div>

        
        <div class="group">
            <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 text-xs font-bold">
                    3
                </span>
                Danışman
                <span class="text-red-500 font-bold">*</span>
                <span class="ml-auto text-xs text-blue-600 dark:text-blue-400 font-normal">
                    <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    Sistem Kullanıcısı
                </span>
            </label>
            <div class="context7-live-search relative" data-search-type="users" data-placeholder="Danışman ara..."
                data-max-results="15" data-creatable="false">
                <input type="hidden" name="danisman_id" id="danisman_id" value="<?php echo e(old('danisman_id')); ?>" required>
                <input type="text" id="danisman_search"
                    class="w-full px-4 py-2.5
                           border-2 border-blue-300 dark:border-blue-600 
                           rounded-xl 
                           bg-blue-50 dark:bg-blue-900/20 
                           text-black dark:text-white
                           placeholder-blue-400 dark:placeholder-blue-500
                           focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:focus:border-blue-400
                           transition-all duration-200
                           hover:border-blue-400 dark:hover:border-blue-500
                           shadow-sm hover:shadow-md focus:shadow-lg"
                    placeholder="Sistem danışmanı ara (isim, email)..." autocomplete="off">
                <div class="context7-search-results absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border-2 border-blue-300 dark:border-blue-600 rounded-xl shadow-2xl hidden max-h-60 overflow-y-auto">
                </div>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    Danışman, admin panel kullanıcılarından seçilir (Kullanıcılar menüsünden ekleyebilirsiniz)
                </p>
            </div>
            <?php $__errorArgs = ['danisman_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <?php echo e($message); ?>

                </p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>
</div>
<?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/admin/ilanlar/partials/stable/_kisi-secimi.blade.php ENDPATH**/ ?>