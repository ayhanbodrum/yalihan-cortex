<?php $__env->startSection('title', 'Müşteriler'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm mb-8 p-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                    👥 CRM Müşteri Yönetimi
                </h1>
                <p class="mt-3 text-lg text-gray-600 dark:text-gray-400">
                    Müşteri bilgilerini yönetin, takip edin ve analiz edin
                </p>
            </div>
            <div class="flex gap-3 items-center">
                <a href="<?php echo e(route('admin.kisiler.index', array_merge(request()->except('page'), ['status' => 'active']))); ?>"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors duration-200">Aktif</a>
                <a href="<?php echo e(route('admin.kisiler.index', array_merge(request()->except('page'), ['status' => 'inactive']))); ?>"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors duration-200">Pasif</a>
                <button onclick="exportCustomers()"
                    class="group relative inline-flex items-center px-6 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-md hover:bg-gray-50 hover:border-gray-400 transform hover:scale-105 transition-all duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    📊 Dışa Aktar
                </button>

                <a href="<?php echo e(route('admin.kisiler.create')); ?>"
                    class="group relative inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg shadow-lg hover:from-blue-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-300 hover:shadow-xl">
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg blur opacity-30 group-hover:opacity-50 transition-opacity duration-300">
                    </div>
                    <svg class="w-5 h-5 mr-2 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="relative z-10">✨ Yeni Müşteri Ekle</span>
                </a>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <!-- İstatistik Kartları -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Toplam Müşteri -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl border border-blue-200 dark:border-blue-700 p-6 transition-colors duration-200">
                <div class="flex items-center">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-blue-800 dark:text-blue-300"><?php echo e($stats['total'] ?? 0); ?></h3>
                        <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">Toplam Müşteri</p>
                    </div>
                </div>
            </div>

            <!-- Aktif Müşteri -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl border border-green-200 dark:border-green-700 p-6 transition-colors duration-200">
                <div class="flex items-center">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-green-800 dark:text-green-300"><?php echo e($stats['active'] ?? 0); ?></h3>
                        <p class="text-sm text-green-600 dark:text-green-400 font-medium">Aktif Müşteri</p>
                    </div>
                </div>
            </div>

            <!-- Potansiyel Müşteri -->
            <div class="bg-gradient-to-r from-yellow-50 to-amber-50 dark:from-yellow-900/20 dark:to-amber-900/20 rounded-xl border border-yellow-200 dark:border-yellow-700 p-6 transition-colors duration-200">
                <div class="flex items-center">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-amber-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-yellow-800 dark:text-yellow-300"><?php echo e($stats['potential'] ?? 0); ?></h3>
                        <p class="text-sm text-yellow-600 dark:text-yellow-400 font-medium">Potansiyel Müşteri</p>
                    </div>
                </div>
            </div>

            <!-- Bu Ay Eklenen -->
            <div class="bg-gradient-to-r from-purple-50 to-violet-50 dark:from-purple-900/20 dark:to-violet-900/20 rounded-xl border border-purple-200 dark:border-purple-700 p-6 transition-colors duration-200">
                <div class="flex items-center">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-purple-500 to-violet-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-purple-800 dark:text-purple-300"><?php echo e($stats['this_month'] ?? 0); ?></h3>
                        <p class="text-sm text-purple-600 dark:text-purple-400 font-medium">Bu Ay Eklenen</p>
                    </div>
                </div>
            </div>
        </div>


        <!-- Modern Arama ve Filtreleme -->
        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm mb-8 p-6">
            <h3 class="text-xl font-bold text-blue-800 dark:text-blue-300 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                🔍 Gelişmiş Arama ve Filtreleme
            </h3>

            
            <div class="space-y-6">
                <!-- Ana Arama (Geniş ve Basit) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div class="mb-6 relative md:col-span-2">
                        <label class="<?php echo e(App\Helpers\FormStandards::label()); ?> text-lg font-semibold mb-3">
                            🔍 Müşteri Ara (Canlı arama)
                        </label>
                        <div class="context7-live-search" data-search-type="kisiler"
                            data-placeholder="Ad, soyad, telefon..." data-max-results="20">
                            <input type="hidden" name="kisi_id" id="kisi_id">
                            <input type="text" id="kisi_search" class="<?php echo e(App\Helpers\FormStandards::input()); ?> text-lg py-4"
                                placeholder="Ad, soyad, telefon, e-posta..." autocomplete="off">
                            <div
                                class="context7-search-results absolute z-50 w-full mt-1 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto">
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            <i class="fas fa-bolt mr-1"></i>
                            Canlı arama - 300ms debounce
                        </p>
                    </div>

                    <div class="mb-6">
                        <label for="status" class="<?php echo e(App\Helpers\FormStandards::label()); ?>">Durum</label>
                        <select name="status" id="status" class="<?php echo e(App\Helpers\FormStandards::select()); ?>">
                            <option class="<?php echo e(App\Helpers\FormStandards::option()); ?>" value="">Tüm Durumlar</option>
                            <option class="<?php echo e(App\Helpers\FormStandards::option()); ?>" value="Aktif" <?php echo e(request('status') == 'Aktif' ? 'selected' : ''); ?>>Aktif</option>
                            <option class="<?php echo e(App\Helpers\FormStandards::option()); ?>" value="Pasif" <?php echo e(request('status') == 'Pasif' ? 'selected' : ''); ?>>Pasif</option>
                            <option class="<?php echo e(App\Helpers\FormStandards::option()); ?>" value="Potansiyel" <?php echo e(request('status') == 'Potansiyel' ? 'selected' : ''); ?>>
                                Potansiyel
                            </option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label for="musteri_tipi" class="<?php echo e(App\Helpers\FormStandards::label()); ?>">Müşteri Tipi</label>
                        <select name="musteri_tipi" id="musteri_tipi" class="<?php echo e(App\Helpers\FormStandards::select()); ?>">
                            <option class="<?php echo e(App\Helpers\FormStandards::option()); ?>" value="">Tüm Tipler</option>
                            <option class="<?php echo e(App\Helpers\FormStandards::option()); ?>" value="Müşteri" <?php echo e(request('musteri_tipi') == 'Müşteri' ? 'selected' : ''); ?>>
                                Müşteri</option>
                            <option class="<?php echo e(App\Helpers\FormStandards::option()); ?>" value="Potansiyel" <?php echo e(request('musteri_tipi') == 'Potansiyel' ? 'selected' : ''); ?>>
                                Potansiyel</option>
                            <option class="<?php echo e(App\Helpers\FormStandards::option()); ?>" value="Danışman" <?php echo e(request('musteri_tipi') == 'Danışman' ? 'selected' : ''); ?>>
                                Danışman</option>
                            <option class="<?php echo e(App\Helpers\FormStandards::option()); ?>" value="Ev Sahibi" <?php echo e(request('musteri_tipi') == 'Ev Sahibi' ? 'selected' : ''); ?>>
                                Ev Sahibi</option>
                            <option class="<?php echo e(App\Helpers\FormStandards::option()); ?>" value="Alıcı" <?php echo e(request('musteri_tipi') == 'Alıcı' ? 'selected' : ''); ?>>
                                Alıcı</option>
                        </select>
                    </div>
                </div>

                <!-- Ek Filtreler (Basit) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div class="mb-6">
                        <label for="danisman_id" class="<?php echo e(App\Helpers\FormStandards::label()); ?>">Danışman</label>
                        <select name="danisman_id" id="danisman_id" class="<?php echo e(App\Helpers\FormStandards::select()); ?>">
                            <option class="<?php echo e(App\Helpers\FormStandards::option()); ?>" value="">Tüm Danışmanlar</option>
                            <?php $__currentLoopData = $danismanlar ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $danisman): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option class="<?php echo e(App\Helpers\FormStandards::option()); ?>" value="<?php echo e($danisman->id); ?>"
                                    <?php echo e(request('danisman_id') == $danisman->id ? 'selected' : ''); ?>>
                                    <?php echo e($danisman->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <!-- Butonlar -->
                <div class="flex gap-4">
                    <button type="submit"
                        class="group relative inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-green-500 to-blue-500 rounded-lg shadow-lg hover:from-green-600 hover:to-blue-600 transform hover:scale-105 transition-all duration-300 hover:shadow-xl">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-green-500 to-blue-500 rounded-lg blur opacity-30 group-hover:opacity-50 transition-opacity duration-300">
                        </div>
                        <svg class="w-4 h-4 mr-2 relative z-10" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span class="relative z-10">🔍 Ara</span>
                    </button>

                    <a href="<?php echo e(route('admin.kisiler.index')); ?>"
                        class="group relative inline-flex items-center px-6 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-md hover:bg-gray-50 hover:border-gray-400 transform hover:scale-105 transition-all duration-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.001 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                        🧹 Temizle
                    </a>

                    <button type="button" onclick="toggleAdvancedSearch()"
                        class="group relative inline-flex items-center px-6 py-3 text-sm font-medium text-purple-700 bg-purple-50 border border-purple-200 rounded-lg shadow-md hover:bg-purple-100 hover:border-purple-300 transform hover:scale-105 transition-all duration-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z">
                            </path>
                        </svg>
                        ⚙️ Gelişmiş
                    </button>
                </div>
                </form>
            </div>

            <!-- Context7 AI Önerileri Banner -->
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-xl border border-blue-200 dark:border-blue-700 shadow-sm mb-6 p-6 transition-colors duration-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-blue-800 dark:text-blue-300">🤖 AI Önerileri</h3>
                            <p class="text-sm text-blue-600 dark:text-blue-400">Context7 Intelligence ile akıllı müşteri analizi</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 text-xs font-medium rounded-full">✓ Context7
                            Uyumlu</span>
                        <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-xs font-medium rounded-full">AI Aktif</span>
                    </div>
                </div>
            </div>

            <!-- Müşteri Listesi -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm" x-data="{ selectedItems: [], showFilters: false, filterStatus: '', filterTag: '', filterStart: '', filterEnd: '' }">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200 flex items-center">
                            <div
                                class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            👥 Müşteri Listesi
                        </h3>

                        <div class="flex items-center gap-3">
                            <button onclick="showQuickFilters()"
                                class="group relative inline-flex items-center px-4 py-2 text-sm font-medium text-purple-700 bg-purple-50 border border-purple-200 rounded-lg shadow-md hover:bg-purple-100 hover:border-purple-300 transform hover:scale-105 transition-all duration-300">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z">
                                    </path>
                                </svg>
                                ⚡ Hızlı Filtre
                            </button>

                            <button onclick="bulkActions()"
                                class="group relative inline-flex items-center px-4 py-2 text-sm font-medium text-orange-700 bg-orange-50 border border-orange-200 rounded-lg shadow-md hover:bg-orange-100 hover:border-orange-300 transform hover:scale-105 transition-all duration-300">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                    </path>
                                </svg>
                                📋 Toplu İşlem
                            </button>

                            <a href="<?php echo e(route('admin.kisiler.create')); ?>"
                                class="group relative inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-green-500 to-blue-500 rounded-lg shadow-lg hover:from-green-600 hover:to-blue-600 transform hover:scale-105 transition-all duration-300 hover:shadow-xl">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-green-500 to-blue-500 rounded-lg blur opacity-30 group-hover:opacity-50 transition-opacity duration-300">
                                </div>
                                <svg class="w-4 h-4 mr-2 relative z-10" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span class="relative z-10">✨ Yeni Müşteri</span>
                            </a>
                        </div>
                    </div>

                    <div x-show="selectedItems.length > 0" class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm font-medium text-blue-800">
                                    <span x-text="selectedItems.length"></span> öğe seçildi
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <?php if (isset($component)) { $__componentOriginal30a40d87b88e32aca663bade78693e32 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal30a40d87b88e32aca663bade78693e32 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.neo.button','data' => ['variant' => 'warning','size' => 'sm','@click' => '(() => { if(selectedItems.length===0){return;} const f=document.createElement(\'form\'); f.method=\'POST\'; f.action=\''.e(url('/admin/kisiler/bulk-action')).'\'; const t=document.createElement(\'input\'); t.type=\'hidden\'; t.name=\'_token\'; t.value=document.querySelector(\'meta[name=csrf-token]\')?.content||\'\'; f.appendChild(t); const a=document.createElement(\'input\'); a.type=\'hidden\'; a.name=\'action\'; a.value=\'status_potansiyel\'; f.appendChild(a); selectedItems.forEach(id=>{ const i=document.createElement(\'input\'); i.type=\'hidden\'; i.name=\'ids[]\'; i.value=id; f.appendChild(i); }); document.body.appendChild(f); f.submit(); })()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('neo.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'warning','size' => 'sm','@click' => '(() => { if(selectedItems.length===0){return;} const f=document.createElement(\'form\'); f.method=\'POST\'; f.action=\''.e(url('/admin/kisiler/bulk-action')).'\'; const t=document.createElement(\'input\'); t.type=\'hidden\'; t.name=\'_token\'; t.value=document.querySelector(\'meta[name=csrf-token]\')?.content||\'\'; f.appendChild(t); const a=document.createElement(\'input\'); a.type=\'hidden\'; a.name=\'action\'; a.value=\'status_potansiyel\'; f.appendChild(a); selectedItems.forEach(id=>{ const i=document.createElement(\'input\'); i.type=\'hidden\'; i.name=\'ids[]\'; i.value=id; f.appendChild(i); }); document.body.appendChild(f); f.submit(); })()']); ?>
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Potansiyel Yap
                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal30a40d87b88e32aca663bade78693e32)): ?>
<?php $attributes = $__attributesOriginal30a40d87b88e32aca663bade78693e32; ?>
<?php unset($__attributesOriginal30a40d87b88e32aca663bade78693e32); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal30a40d87b88e32aca663bade78693e32)): ?>
<?php $component = $__componentOriginal30a40d87b88e32aca663bade78693e32; ?>
<?php unset($__componentOriginal30a40d87b88e32aca663bade78693e32); ?>
<?php endif; ?>
                                <?php if (isset($component)) { $__componentOriginal30a40d87b88e32aca663bade78693e32 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal30a40d87b88e32aca663bade78693e32 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.neo.button','data' => ['variant' => 'danger','size' => 'sm','@click' => '(() => { if(selectedItems.length===0){return;} if(!confirm(selectedItems.length + \' kayıt silinecek. Onaylıyor musunuz?\')){return;} const f=document.createElement(\'form\'); f.method=\'POST\'; f.action=\''.e(url('/admin/kisiler/bulk-action')).'\'; const t=document.createElement(\'input\'); t.type=\'hidden\'; t.name=\'_token\'; t.value=document.querySelector(\'meta[name=csrf-token]\')?.content||\'\'; f.appendChild(t); const a=document.createElement(\'input\'); a.type=\'hidden\'; a.name=\'action\'; a.value=\'delete\'; f.appendChild(a); selectedItems.forEach(id=>{ const i=document.createElement(\'input\'); i.type=\'hidden\'; i.name=\'ids[]\'; i.value=id; f.appendChild(i); }); document.body.appendChild(f); f.submit(); })()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('neo.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'danger','size' => 'sm','@click' => '(() => { if(selectedItems.length===0){return;} if(!confirm(selectedItems.length + \' kayıt silinecek. Onaylıyor musunuz?\')){return;} const f=document.createElement(\'form\'); f.method=\'POST\'; f.action=\''.e(url('/admin/kisiler/bulk-action')).'\'; const t=document.createElement(\'input\'); t.type=\'hidden\'; t.name=\'_token\'; t.value=document.querySelector(\'meta[name=csrf-token]\')?.content||\'\'; f.appendChild(t); const a=document.createElement(\'input\'); a.type=\'hidden\'; a.name=\'action\'; a.value=\'delete\'; f.appendChild(a); selectedItems.forEach(id=>{ const i=document.createElement(\'input\'); i.type=\'hidden\'; i.name=\'ids[]\'; i.value=id; f.appendChild(i); }); document.body.appendChild(f); f.submit(); })()']); ?>
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                    Seçilenleri Sil
                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal30a40d87b88e32aca663bade78693e32)): ?>
<?php $attributes = $__attributesOriginal30a40d87b88e32aca663bade78693e32; ?>
<?php unset($__attributesOriginal30a40d87b88e32aca663bade78693e32); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal30a40d87b88e32aca663bade78693e32)): ?>
<?php $component = $__componentOriginal30a40d87b88e32aca663bade78693e32; ?>
<?php unset($__componentOriginal30a40d87b88e32aca663bade78693e32); ?>
<?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php if (isset($component)) { $__componentOriginal883972b03e56cea0994a1aaccc5761f0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal883972b03e56cea0994a1aaccc5761f0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.modal','data' => ['bind' => 'showFilters','title' => 'Hızlı Filtre','size' => 'md']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin.modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['bind' => 'showFilters','title' => 'Hızlı Filtre','size' => 'md']); ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="<?php echo e(App\Helpers\FormStandards::label()); ?>">Durum</label>
                                <select class="<?php echo e(App\Helpers\FormStandards::select()); ?>" x-model="filterStatus">
                                    <option class="<?php echo e(App\Helpers\FormStandards::option()); ?>" value="">Tümü</option>
                                    <option class="<?php echo e(App\Helpers\FormStandards::option()); ?>" value="Aktif">Aktif</option>
                                    <option class="<?php echo e(App\Helpers\FormStandards::option()); ?>" value="Pasif">Pasif</option>
                                    <option class="<?php echo e(App\Helpers\FormStandards::option()); ?>" value="Potansiyel">Potansiyel</option>
                                    <option class="<?php echo e(App\Helpers\FormStandards::option()); ?>" value="Yeni">Yeni</option>
                                </select>
                            </div>
                            <div>
                                <label class="<?php echo e(App\Helpers\FormStandards::label()); ?>">Etiket</label>
                                <input type="text" class="<?php echo e(App\Helpers\FormStandards::input()); ?>" x-model="filterTag" placeholder="Etiket adı">
                            </div>
                            <div>
                                <label class="<?php echo e(App\Helpers\FormStandards::label()); ?>">Başlangıç Tarihi</label>
                                <input type="date" class="<?php echo e(App\Helpers\FormStandards::input()); ?>" x-model="filterStart">
                            </div>
                            <div>
                                <label class="<?php echo e(App\Helpers\FormStandards::label()); ?>">Bitiş Tarihi</label>
                                <input type="date" class="<?php echo e(App\Helpers\FormStandards::input()); ?>" x-model="filterEnd">
                            </div>
                        </div>
                         <?php $__env->slot('footer', null, []); ?> 
                            <div class="flex justify-end gap-3">
                                <?php if (isset($component)) { $__componentOriginal30a40d87b88e32aca663bade78693e32 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal30a40d87b88e32aca663bade78693e32 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.neo.button','data' => ['variant' => 'secondary','@click' => 'showFilters = false']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('neo.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'secondary','@click' => 'showFilters = false']); ?>
                                    Kapat
                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal30a40d87b88e32aca663bade78693e32)): ?>
<?php $attributes = $__attributesOriginal30a40d87b88e32aca663bade78693e32; ?>
<?php unset($__attributesOriginal30a40d87b88e32aca663bade78693e32); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal30a40d87b88e32aca663bade78693e32)): ?>
<?php $component = $__componentOriginal30a40d87b88e32aca663bade78693e32; ?>
<?php unset($__componentOriginal30a40d87b88e32aca663bade78693e32); ?>
<?php endif; ?>
                                <?php if (isset($component)) { $__componentOriginal30a40d87b88e32aca663bade78693e32 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal30a40d87b88e32aca663bade78693e32 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.neo.button','data' => ['variant' => 'primary','@click' => '(() => { const url = new URL(window.location); if(filterStatus){ url.searchParams.set(\'status\', filterStatus); } else { url.searchParams.delete(\'status\'); } if(filterTag){ url.searchParams.set(\'etiket\', filterTag); } else { url.searchParams.delete(\'etiket\'); } if(filterStart){ url.searchParams.set(\'baslangic\', filterStart); } else { url.searchParams.delete(\'baslangic\'); } if(filterEnd){ url.searchParams.set(\'bitis\', filterEnd); } else { url.searchParams.delete(\'bitis\'); } window.location.href = url.toString(); })()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('neo.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'primary','@click' => '(() => { const url = new URL(window.location); if(filterStatus){ url.searchParams.set(\'status\', filterStatus); } else { url.searchParams.delete(\'status\'); } if(filterTag){ url.searchParams.set(\'etiket\', filterTag); } else { url.searchParams.delete(\'etiket\'); } if(filterStart){ url.searchParams.set(\'baslangic\', filterStart); } else { url.searchParams.delete(\'baslangic\'); } if(filterEnd){ url.searchParams.set(\'bitis\', filterEnd); } else { url.searchParams.delete(\'bitis\'); } window.location.href = url.toString(); })()']); ?>
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z">
                                        </path>
                                    </svg>
                                    Filtrele
                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal30a40d87b88e32aca663bade78693e32)): ?>
<?php $attributes = $__attributesOriginal30a40d87b88e32aca663bade78693e32; ?>
<?php unset($__attributesOriginal30a40d87b88e32aca663bade78693e32); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal30a40d87b88e32aca663bade78693e32)): ?>
<?php $component = $__componentOriginal30a40d87b88e32aca663bade78693e32; ?>
<?php unset($__componentOriginal30a40d87b88e32aca663bade78693e32); ?>
<?php endif; ?>
                            </div>
                         <?php $__env->endSlot(); ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal883972b03e56cea0994a1aaccc5761f0)): ?>
<?php $attributes = $__attributesOriginal883972b03e56cea0994a1aaccc5761f0; ?>
<?php unset($__attributesOriginal883972b03e56cea0994a1aaccc5761f0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal883972b03e56cea0994a1aaccc5761f0)): ?>
<?php $component = $__componentOriginal883972b03e56cea0994a1aaccc5761f0; ?>
<?php unset($__componentOriginal883972b03e56cea0994a1aaccc5761f0); ?>
<?php endif; ?>

                    <div class="w-full overflow-x-auto">
                        <?php if (isset($component)) { $__componentOriginal53cf72b3da4b8700c9115c02c0eead10 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53cf72b3da4b8700c9115c02c0eead10 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.table','data' => ['class' => 'w-full text-left border-collapse']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin.table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full text-left border-collapse']); ?>
                             <?php $__env->slot('head', null, []); ?> 
                                <tr>
                                    <th class="px-6 py-3">
                                        <input type="checkbox"
                                            @change="($event.target.checked) ? selectedItems = Array.from($el.closest('table').querySelectorAll('[data-kisi-id]')).map(el => Number(el.dataset.kisiId)) : selectedItems = []">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                        <a href="<?php echo e(route('admin.kisiler.index', array_merge(request()->except('page'), ['sort' => request('sort') === 'name_asc' ? 'name_desc' : 'name_asc']))); ?>"
                                            class="inline-flex items-center gap-1 hover:underline">
                                            Müşteri
                                            <?php if(request('sort') === 'name_asc'): ?>
                                                <svg class="w-3 h-3 text-blue-600" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path d="M10 6l-5 6h10L10 6z" />
                                                </svg>
                                            <?php elseif(request('sort') === 'name_desc'): ?>
                                                <svg class="w-3 h-3 text-blue-600" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path d="M10 14l5-6H5l5 6z" />
                                                </svg>
                                            <?php endif; ?>
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                        İletişim</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                        Durum
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                        Kaynak
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                        Danışman</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                        <a href="<?php echo e(route('admin.kisiler.index', array_merge(request()->except('page'), ['sort' => request('sort') === 'created_desc' ? 'created_asc' : 'created_desc']))); ?>"
                                            class="inline-flex items-center gap-1 hover:underline">
                                            Kayıt Tarihi
                                            <?php if(request('sort') === 'created_asc'): ?>
                                                <svg class="w-3 h-3 text-blue-600" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path d="M10 6l-5 6h10L10 6z" />
                                                </svg>
                                            <?php elseif(request('sort') === 'created_desc'): ?>
                                                <svg class="w-3 h-3 text-blue-600" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path d="M10 14l5-6H5l5 6z" />
                                                </svg>
                                            <?php endif; ?>
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider">
                                        İşlemler</th>
                                </tr>
                             <?php $__env->endSlot(); ?>
                            <tbody class="c7-tbody divide-y divide-gray-200 dark:divide-gray-700">
                                <?php $__empty_1 = true; $__currentLoopData = $kisiler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kisi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                        data-kisi-id="<?php echo e($kisi->id); ?>">
                                        <td class="px-6 py-4">
                                            <input type="checkbox" :checked="selectedItems.includes(<?php echo e($kisi->id); ?>)"
                                                @change="($event.target.checked) ? selectedItems = Array.from(new Set([...selectedItems, <?php echo e($kisi->id); ?>])) : selectedItems = selectedItems.filter(id => id !== <?php echo e($kisi->id); ?>)">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-12 w-12 relative">
                                                    <div
                                                        class="h-12 w-12 bg-gradient-to-r from-green-400 to-green-600 rounded-full flex items-center justify-center">
                                                        <span class="text-white font-bold text-lg">
                                                            <?php echo e(strtoupper(substr($kisi->ad, 0, 1))); ?>

                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                        <?php echo e($kisi->ad); ?> <?php echo e($kisi->soyad); ?>

                                                    </div>
                                                    <?php if($kisi->tc_kimlik): ?>
                                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                                            TC: <?php echo e($kisi->tc_kimlik); ?>

                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-white"><?php echo e($kisi->telefon); ?>

                                            </div>
                                            <?php if($kisi->email): ?>
                                                <div class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($kisi->email); ?>

                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <?php if (isset($component)) { $__componentOriginal9e089bc5f8eac272c2c5ae05d84c4b58 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9e089bc5f8eac272c2c5ae05d84c4b58 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.neo.status-badge','data' => ['status' => $kisi->status ?? 'taslak']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('neo.status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($kisi->status ?? 'taslak')]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9e089bc5f8eac272c2c5ae05d84c4b58)): ?>
<?php $attributes = $__attributesOriginal9e089bc5f8eac272c2c5ae05d84c4b58; ?>
<?php unset($__attributesOriginal9e089bc5f8eac272c2c5ae05d84c4b58); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9e089bc5f8eac272c2c5ae05d84c4b58)): ?>
<?php $component = $__componentOriginal9e089bc5f8eac272c2c5ae05d84c4b58; ?>
<?php unset($__componentOriginal9e089bc5f8eac272c2c5ae05d84c4b58); ?>
<?php endif; ?>
                                                <?php if($kisi->email && \App\Models\Kisi::where('email', $kisi->email)->count() > 1): ?>
                                                    <span
                                                        class="px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z" />
                                                        </svg>
                                                        Mükerrer
                                                    </span>
                                                <?php endif; ?>
                                                
                                                <span
                                                    class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                                    CRM: <?php echo e($kisi->crm_score ?? 0); ?>/100
                                                </span>
                                                
                                                <?php if($kisi->isOwnerEligible()): ?>
                                                    <span
                                                        class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                                        ✓ İlan Sahibi
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <?php echo e($kisi->kaynak ?? 'Belirtilmemiş'); ?>

                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <?php if($kisi->userDanisman): ?>
                                                <span class="text-blue-600 hover:text-blue-800 font-medium">
                                                    <?php echo e($kisi->userDanisman->name); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="text-gray-400">Atanmamış</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <?php echo e($kisi->created_at ? $kisi->created_at->format('d.m.Y') : 'Belirtilmemiş'); ?>

                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end" x-data="{ openDelete: false }">
                                                <?php if (isset($component)) { $__componentOriginale84cb6e95f44972030d49ee684d135f4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale84cb6e95f44972030d49ee684d135f4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.neo.dropdown','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('neo.dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                                                    <?php if (isset($component)) { $__componentOriginalae9046510bb864c9e006cab99a2d231b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae9046510bb864c9e006cab99a2d231b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.neo.dropdown-item','data' => ['href' => ''.e(route('admin.kisiler.show', $kisi->id)).'','icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('neo.dropdown-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('admin.kisiler.show', $kisi->id)).'','icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>']); ?>
                                                        Detay
                                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalae9046510bb864c9e006cab99a2d231b)): ?>
<?php $attributes = $__attributesOriginalae9046510bb864c9e006cab99a2d231b; ?>
<?php unset($__attributesOriginalae9046510bb864c9e006cab99a2d231b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalae9046510bb864c9e006cab99a2d231b)): ?>
<?php $component = $__componentOriginalae9046510bb864c9e006cab99a2d231b; ?>
<?php unset($__componentOriginalae9046510bb864c9e006cab99a2d231b); ?>
<?php endif; ?>
                                                    <?php if (isset($component)) { $__componentOriginalae9046510bb864c9e006cab99a2d231b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae9046510bb864c9e006cab99a2d231b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.neo.dropdown-item','data' => ['href' => ''.e(route('admin.kisiler.edit', $kisi->id)).'','icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('neo.dropdown-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('admin.kisiler.edit', $kisi->id)).'','icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>']); ?>
                                                        Düzenle
                                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalae9046510bb864c9e006cab99a2d231b)): ?>
<?php $attributes = $__attributesOriginalae9046510bb864c9e006cab99a2d231b; ?>
<?php unset($__attributesOriginalae9046510bb864c9e006cab99a2d231b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalae9046510bb864c9e006cab99a2d231b)): ?>
<?php $component = $__componentOriginalae9046510bb864c9e006cab99a2d231b; ?>
<?php unset($__componentOriginalae9046510bb864c9e006cab99a2d231b); ?>
<?php endif; ?>
                                                    <?php if (isset($component)) { $__componentOriginalae9046510bb864c9e006cab99a2d231b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae9046510bb864c9e006cab99a2d231b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.neo.dropdown-item','data' => ['variant' => 'danger','@click' => 'openDelete'.e($kisi->id).' = true','icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('neo.dropdown-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'danger','@click' => 'openDelete'.e($kisi->id).' = true','icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>']); ?>
                                                        Sil
                                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalae9046510bb864c9e006cab99a2d231b)): ?>
<?php $attributes = $__attributesOriginalae9046510bb864c9e006cab99a2d231b; ?>
<?php unset($__attributesOriginalae9046510bb864c9e006cab99a2d231b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalae9046510bb864c9e006cab99a2d231b)): ?>
<?php $component = $__componentOriginalae9046510bb864c9e006cab99a2d231b; ?>
<?php unset($__componentOriginalae9046510bb864c9e006cab99a2d231b); ?>
<?php endif; ?>
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale84cb6e95f44972030d49ee684d135f4)): ?>
<?php $attributes = $__attributesOriginale84cb6e95f44972030d49ee684d135f4; ?>
<?php unset($__attributesOriginale84cb6e95f44972030d49ee684d135f4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale84cb6e95f44972030d49ee684d135f4)): ?>
<?php $component = $__componentOriginale84cb6e95f44972030d49ee684d135f4; ?>
<?php unset($__componentOriginale84cb6e95f44972030d49ee684d135f4); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal883972b03e56cea0994a1aaccc5761f0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal883972b03e56cea0994a1aaccc5761f0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.modal','data' => ['xShow' => 'openDelete'.e($kisi->id).'','@click.away' => 'openDelete'.e($kisi->id).' = false','title' => 'Silme Onayı','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin.modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-show' => 'openDelete'.e($kisi->id).'','@click.away' => 'openDelete'.e($kisi->id).' = false','title' => 'Silme Onayı','size' => 'sm']); ?>
                                                    <div class="text-center">
                                                        <svg class="mx-auto h-12 w-12 text-red-400" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z">
                                                            </path>
                                                        </svg>
                                                        <h3 class="mt-2 text-sm font-medium text-gray-900">Müşteriyi Sil
                                                        </h3>
                                                        <p class="mt-1 text-sm text-gray-500">Bu işlem geri alınamaz.
                                                            Müşteri
                                                            ve tüm verileri kalıcı olarak silinecek.</p>
                                                    </div>
                                                     <?php $__env->slot('footer', null, []); ?> 
                                                        <div class="flex justify-end gap-3">
                                                            <?php if (isset($component)) { $__componentOriginal30a40d87b88e32aca663bade78693e32 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal30a40d87b88e32aca663bade78693e32 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.neo.button','data' => ['variant' => 'secondary','@click' => 'openDelete'.e($kisi->id).' = false']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('neo.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'secondary','@click' => 'openDelete'.e($kisi->id).' = false']); ?>
                                                                Vazgeç
                                                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal30a40d87b88e32aca663bade78693e32)): ?>
<?php $attributes = $__attributesOriginal30a40d87b88e32aca663bade78693e32; ?>
<?php unset($__attributesOriginal30a40d87b88e32aca663bade78693e32); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal30a40d87b88e32aca663bade78693e32)): ?>
<?php $component = $__componentOriginal30a40d87b88e32aca663bade78693e32; ?>
<?php unset($__componentOriginal30a40d87b88e32aca663bade78693e32); ?>
<?php endif; ?>
                                                            <form action="<?php echo e(route('admin.kisiler.destroy', $kisi->id)); ?>"
                                                                method="POST" class="inline">
                                                                <?php echo csrf_field(); ?>
                                                                <?php echo method_field('DELETE'); ?>
                                                                <?php if (isset($component)) { $__componentOriginal30a40d87b88e32aca663bade78693e32 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal30a40d87b88e32aca663bade78693e32 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.neo.button','data' => ['variant' => 'danger','type' => 'submit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('neo.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'danger','type' => 'submit']); ?>
                                                                    <svg class="w-4 h-4 mr-2" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                        </path>
                                                                    </svg>
                                                                    Sil
                                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal30a40d87b88e32aca663bade78693e32)): ?>
<?php $attributes = $__attributesOriginal30a40d87b88e32aca663bade78693e32; ?>
<?php unset($__attributesOriginal30a40d87b88e32aca663bade78693e32); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal30a40d87b88e32aca663bade78693e32)): ?>
<?php $component = $__componentOriginal30a40d87b88e32aca663bade78693e32; ?>
<?php unset($__componentOriginal30a40d87b88e32aca663bade78693e32); ?>
<?php endif; ?>
                                                            </form>
                                                        </div>
                                                     <?php $__env->endSlot(); ?>
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal883972b03e56cea0994a1aaccc5761f0)): ?>
<?php $attributes = $__attributesOriginal883972b03e56cea0994a1aaccc5761f0; ?>
<?php unset($__attributesOriginal883972b03e56cea0994a1aaccc5761f0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal883972b03e56cea0994a1aaccc5761f0)): ?>
<?php $component = $__componentOriginal883972b03e56cea0994a1aaccc5761f0; ?>
<?php unset($__componentOriginal883972b03e56cea0994a1aaccc5761f0); ?>
<?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="8"
                                            class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Müşteri
                                                bulunamadı</h3>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Arama kriterlerinizi
                                                değiştirmeyi deneyin.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53cf72b3da4b8700c9115c02c0eead10)): ?>
<?php $attributes = $__attributesOriginal53cf72b3da4b8700c9115c02c0eead10; ?>
<?php unset($__attributesOriginal53cf72b3da4b8700c9115c02c0eead10); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53cf72b3da4b8700c9115c02c0eead10)): ?>
<?php $component = $__componentOriginal53cf72b3da4b8700c9115c02c0eead10; ?>
<?php unset($__componentOriginal53cf72b3da4b8700c9115c02c0eead10); ?>
<?php endif; ?>
                    </div>

                    <?php if($kisiler->hasPages()): ?>
                        <div class="mt-6">
                            <?php echo e($kisiler->links()); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php $__env->stopSection(); ?>

        <?php $__env->startPush('styles'); ?>
            <style>
                /* Modern Button Animations */
                .group:hover .group-hover\:inline {
                    display: inline;
                }

                /* Glow Effect for Primary Buttons */
                .glow-button {
                    box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
                }

                .glow-button:hover {
                    box-shadow: 0 0 30px rgba(59, 130, 246, 0.5);
                }

                /* Smooth Transitions */
                .transition-all {
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                }

                /* Custom Scrollbar */
                .custom-scrollbar::-webkit-scrollbar {
                    width: 6px;
                }

                .custom-scrollbar::-webkit-scrollbar-track {
                    background: #f1f5f9;
                    border-radius: 3px;
                }

                .custom-scrollbar::-webkit-scrollbar-thumb {
                    background: #cbd5e1;
                    border-radius: 3px;
                }

                .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                    background: #94a3b8;
                }

                /* Form Field Standartları */
                .form-field {
                    @apply space-y-2;
                }

                .admin-label {
                    @apply block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1;
                }

                .admin-input {
                    @apply w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                           bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                           placeholder-gray-500 dark:placeholder-gray-400
                           focus:border-blue-500 dark:focus:border-blue-400
                           focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
                           focus:outline-none transition-all duration-200;
                }

                /* Context7 Button Standartları - Unified */
                /* Button styles moved to common-styles.css */

                /* Hover Efektleri */
                .admin-input:hover {
                    @apply border-gray-400;
                }

                /* Focus Efektleri */
                .admin-input:focus {
                    @apply ring-2 ring-blue-200 dark:ring-blue-800;
                }
            </style>
        <?php $__env->stopPush(); ?>

        <?php $__env->startPush('scripts'); ?>
            
            <script src="<?php echo e(asset('js/context7-live-search-simple.js')); ?>"></script>

            
            <script>
                // Export Customers Function
                function exportCustomers() {
                    // Show loading state
                    const button = event.target.closest('button');
                    const originalText = button.innerHTML;
                    button.innerHTML =
                        '<svg class="w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>📊 Dışa Aktarılıyor...';
                    button.disabled = true;

                    // Simulate export process
                    setTimeout(() => {
                        // Create CSV content
                        const table = document.querySelector('table');
                        const rows = table.querySelectorAll('tbody tr');
                        let csvContent = 'Ad Soyad,Telefon,Email,TC Kimlik,Durum,Kaynak,Danışman,Kayıt Tarihi\n';

                        rows.forEach(row => {
                            const cells = row.querySelectorAll('td');
                            if (cells.length > 1) {
                                const name = cells[1].textContent.trim().split('\n')[0];
                                const contact = cells[2].textContent.trim().split('\n')[0];
                                const email = cells[2].textContent.trim().split('\n')[1] || '';
                                const tc = cells[1].textContent.trim().split('\n')[1] || '';
                                const status = cells[3].textContent.trim();
                                const source = cells[4].textContent.trim();
                                const consultant = cells[5].textContent.trim();
                                const date = cells[6].textContent.trim();

                                csvContent +=
                                    `"${name}","${contact}","${email}","${tc}","${status}","${source}","${consultant}","${date}"\n`;
                            }
                        });

                        // Download CSV
                        const blob = new Blob([csvContent], {
                            type: 'text/csv;charset=utf-8;'
                        });
                        const link = document.createElement('a');
                        const url = URL.createObjectURL(blob);
                        link.setAttribute('href', url);
                        link.setAttribute('download', 'musteriler_' + new Date().toISOString().split('T')[0] + '.csv');
                        link.style.visibility = 'hidden';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);

                        // Reset button
                        button.innerHTML = originalText;
                        button.disabled = false;

                        // Show success message
                        showNotification('✅ Müşteriler başarıyla dışa aktarıldı!', 'success');
                    }, 1500);
                }

                // Show Quick Filters
                function showQuickFilters() {
                    const modal = document.createElement('div');
                    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
                    modal.innerHTML = `
                    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-lg bg-white">
                        <div class="mt-3">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-900">⚡ Hızlı Filtreler</h3>
                                <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <button onclick="quickFilter('status', 'Aktif')" class="p-3 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                                    <div class="text-green-800 font-semibold">✅ Aktif Müşteriler</div>
                                    <div class="text-green-600 text-sm">Aktif statusdaki müşteriler</div>
                                </button>
                                <button onclick="quickFilter('status', 'Potansiyel')" class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg hover:bg-yellow-100 transition-colors">
                                    <div class="text-yellow-800 font-semibold">⏳ Potansiyel Müşteriler</div>
                                    <div class="text-yellow-600 text-sm">Potansiyel müşteriler</div>
                                </button>
                                <button onclick="quickFilter('musteri_tipi', 'Bireysel')" class="p-3 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                                    <div class="text-blue-800 font-semibold">👤 Bireysel Müşteriler</div>
                                    <div class="text-blue-600 text-sm">Bireysel müşteri tipi</div>
                                </button>
                                <button onclick="quickFilter('musteri_tipi', 'Kurumsal')" class="p-3 bg-purple-50 border border-purple-200 rounded-lg hover:bg-purple-100 transition-colors">
                                    <div class="text-purple-800 font-semibold">🏢 Kurumsal Müşteriler</div>
                                    <div class="text-purple-600 text-sm">Kurumsal müşteri tipi</div>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                    document.body.appendChild(modal);
                }

                // Quick Filter Function
                function quickFilter(type, value) {
                    const url = new URL(window.location);
                    url.searchParams.set(type, value);
                    window.location.href = url.toString();
                }

                // Bulk Actions
                function bulkActions() {
                    const selectedCount = document.querySelectorAll('input[type="checkbox"]:checked').length;
                    if (selectedCount === 0) {
                        showNotification('⚠️ Lütfen önce müşteri seçin!', 'warning');
                        return;
                    }

                    const modal = document.createElement('div');
                    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
                    modal.innerHTML = `
                    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-lg bg-white">
                        <div class="mt-3">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-900">📋 Toplu İşlemler (${selectedCount} seçili)</h3>
                                <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="grid grid-cols-1 gap-3">
                                <button onclick="bulkAction('status_potansiyel')" class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg hover:bg-yellow-100 transition-colors text-left">
                                    <div class="text-yellow-800 font-semibold">⏳ Potansiyel Yap</div>
                                    <div class="text-yellow-600 text-sm">Seçili müşterileri potansiyel yap</div>
                                </button>
                                <button onclick="bulkAction('status_status')" class="p-3 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors text-left">
                                    <div class="text-green-800 font-semibold">✅ Aktif Yap</div>
                                    <div class="text-green-600 text-sm">Seçili müşterileri status yap</div>
                                </button>
                                <button onclick="bulkAction('export')" class="p-3 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors text-left">
                                    <div class="text-blue-800 font-semibold">📊 Dışa Aktar</div>
                                    <div class="text-blue-600 text-sm">Seçili müşterileri CSV olarak dışa aktar</div>
                                </button>
                                <button onclick="bulkAction('delete')" class="p-3 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors text-left">
                                    <div class="text-red-800 font-semibold">🗑️ Sil</div>
                                    <div class="text-red-600 text-sm">Seçili müşterileri sil (Dikkatli olun!)</div>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                    document.body.appendChild(modal);
                }

                // Bulk Action Function
                function bulkAction(action) {
                    const selectedIds = Array.from(document.querySelectorAll('input[type="checkbox"]:checked'))
                        .map(checkbox => checkbox.closest('tr').dataset.kisiId);

                    if (action === 'delete' && !confirm(`${selectedIds.length} müşteri silinecek. Emin misiniz?`)) {
                        return;
                    }

                    // Create form and submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '<?php echo e(url('/admin/kisiler/bulk-action')); ?>';

                    const token = document.createElement('input');
                    token.type = 'hidden';
                    token.name = '_token';
                    token.value = document.querySelector('meta[name=csrf-token]')?.content || '';
                    form.appendChild(token);

                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = action;
                    form.appendChild(actionInput);

                    selectedIds.forEach(id => {
                        const idInput = document.createElement('input');
                        idInput.type = 'hidden';
                        idInput.name = 'ids[]';
                        idInput.value = id;
                        form.appendChild(idInput);
                    });

                    document.body.appendChild(form);
                    form.submit();
                }

                // Toggle Advanced Search
                function toggleAdvancedSearch() {
                    const advancedSection = document.getElementById('advancedSearchSection');
                    if (advancedSection) {
                        advancedSection.classList.toggle('hidden');
                    } else {
                        // Create advanced search section
                        const form = document.querySelector('form');
                        const advancedHTML = `
                        <div id="advancedSearchSection" class="mt-6 p-4 bg-gray-50 rounded-lg border">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">🔧 Gelişmiş Arama Seçenekleri</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">TC Kimlik</label>
                                    <input type="text" name="tc_kimlik" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="TC Kimlik No">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">İl</label>
                                    <input type="text" name="il" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="İl">
                                </div>
                            </div>
                        </div>
                    `;
                        form.insertAdjacentHTML('beforeend', advancedHTML);
                    }
                }

                // Show Notification
                function showNotification(message, type = 'info') {
                    const notification = document.createElement('div');
                    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transform transition-all duration-300 ${
                    type === 'success' ? 'bg-green-500 text-white' :
                    type === 'error' ? 'bg-red-500 text-white' :
                    type === 'warning' ? 'bg-yellow-500 text-white' :
                    'bg-blue-500 text-white'
                }`;
                    notification.innerHTML = message;

                    document.body.appendChild(notification);

                    // Animate in
                    setTimeout(() => {
                        notification.style.transform = 'translateX(0)';
                    }, 100);

                    // Remove after 3 seconds
                    setTimeout(() => {
                        notification.style.transform = 'translateX(100%)';
                        setTimeout(() => {
                            notification.remove();
                        }, 300);
                    }, 3000);
                }

                // Initialize page
                document.addEventListener('DOMContentLoaded', function() {
                    // Add glow effect to primary buttons
                    const primaryButtons = document.querySelectorAll('a[href*="create"], button[type="submit"]');
                    primaryButtons.forEach(button => {
                        button.classList.add('glow-button');
                    });

                    // Add smooth scrolling
                    document.documentElement.style.scrollBehavior = 'smooth';

                    // Auto-refresh stats every 30 seconds
                    setInterval(() => {
                        // You can add auto-refresh functionality here if needed
                    }, 30000);
                });
            </script>
        <?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.neo', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/macbookpro/Projects/yalihanai /resources/views/admin/kisiler/index.blade.php ENDPATH**/ ?>