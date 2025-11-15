<?php $__env->startSection('title', 'AI Destekli Talep Yönetimi'); ?>


<?php $__env->startSection('content'); ?>
    <div x-data="taleplerData()" class="space-y-6">

        
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293L18.707 8.707A1 1 0 0119 9.414V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">🤖 AI Destekli Talep Yönetimi</h1>
                    <p class="text-gray-600 dark:text-gray-400">Akıllı talep analizi ve portföy eşleştirme paneli</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                
                <button @click="showBatchAnalysisModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 text-white hover:from-indigo-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-200 animate-pulse">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>Toplu AI Analizi</span>
                </button>

                
                <a href="<?php echo e(route('admin.talepler.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 text-white hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Yeni Talep Ekle
                </a>
            </div>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Toplam Talepler</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($talepler->total()); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2 text-sm text-green-600 dark:text-green-400">
                    ↑ %12 bu ay
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">AI Eşleştirme Oranı</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">%87</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg animate-pulse">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2 text-sm text-green-600 dark:text-green-400">
                    ↑ %5 geçen hafta
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Ortalama Yanıt Süresi</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">2.4s</p>
                    </div>
                    <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2 text-sm text-green-600 dark:text-green-400">
                    ↓ %15 optimization
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Başarılı Eşleşme</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            <?php echo e($talepler->where('status', 'eslestirildi')->count()); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2 text-sm text-green-600 dark:text-green-400">
                    ↑ %23 bu hafta
                </div>
            </div>
        </div>

        
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900 dark:text-white">Akıllı Filtreler</h3>
                <button @click="showAdvancedFilters = !showAdvancedFilters"
                    class="text-sm text-blue-600 hover:text-blue-500">
                    <span x-text="showAdvancedFilters ? 'Gizle' : 'Gelişmiş Filtreler'">
                </button>
            </div>

            <form method="GET" action="<?php echo e(route('admin.talepler.index')); ?>" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                            placeholder="AI destekli akıllı arama..." class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 pl-10">
                    </div>

                    
                    <select style="color-scheme: light dark;" name="status" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                        <option value="">Tüm Durumlar</option>
                        <?php $__currentLoopData = $statuslar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($status); ?>" <?php echo e(request('status') == $status ? 'selected' : ''); ?>>
                                <?php echo e(ucfirst($status)); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>

                    
                    <select style="color-scheme: light dark;" name="alt_kategori_id" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                        <option value="">Tüm Kategoriler</option>
                        <?php $__currentLoopData = $kategoriler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($kategori->id); ?>"
                                <?php echo e(request('alt_kategori_id') == $kategori->id ? 'selected' : ''); ?>>
                                <?php echo e($kategori->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>

                    
                    <select style="color-scheme: light dark;" name="ai_priority" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                        <option value="">AI Önceliği</option>
                        <option value="high" <?php echo e(request('ai_priority') == 'high' ? 'selected' : ''); ?>>Yüksek</option>
                        <option value="medium" <?php echo e(request('ai_priority') == 'medium' ? 'selected' : ''); ?>>Orta</option>
                        <option value="low" <?php echo e(request('ai_priority') == 'low' ? 'selected' : ''); ?>>Düşük</option>
                    </select>
                </div>

                <div x-show="showAdvancedFilters" x-transition
                    class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    
                    <input type="number" name="min_fiyat" value="<?php echo e(request('min_fiyat')); ?>" placeholder="Min Fiyat"
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200">
                    <input type="number" name="max_fiyat" value="<?php echo e(request('max_fiyat')); ?>" placeholder="Max Fiyat"
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200">

                    
                    <input type="date" name="baslangic_tarihi" value="<?php echo e(request('baslangic_tarihi')); ?>"
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200">
                    <input type="date" name="bitis_tarihi" value="<?php echo e(request('bitis_tarihi')); ?>" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200">

                    
                    <select style="color-scheme: light dark;" name="ulke_id" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                        <option value="">Ülke Seçin</option>
                        <?php $__currentLoopData = $ulkeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ulke): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($ulke->id); ?>" <?php echo e(request('ulke_id') == $ulke->id ? 'selected' : ''); ?>>
                                <?php echo e($ulke->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>

                    
                    <select style="color-scheme: light dark;" name="talep_tipi" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                        <option value="">Talep Tipi</option>
                        <?php $__currentLoopData = $talepTipleri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($tip); ?>" <?php echo e(request('talep_tipi') == $tip ? 'selected' : ''); ?>>
                                <?php echo e($tip); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                            :disabled="filterLoading"
                            @click="filterLoading = true"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 text-white hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50">
                        <svg class="w-4 h-4" :class="filterLoading ? 'animate-spin' : ''" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Filtrele
                    </button>
                    <a href="<?php echo e(route('admin.talepler.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                        Temizle
                    </a>
                    <button @click="runAISearch()" type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 text-white hover:from-indigo-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        AI Önerileri
                    </button>
                </div>
            </form>
        </div>

        
        <?php if($talepler->count() > 0): ?>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                <?php $__currentLoopData = $talepler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $talep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm group hover:shadow-lg transition-all duration-300">
                        
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg text-white">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900 dark:text-white">
                                            <?php if($talep->kisi): ?>
                                                <?php echo e($talep->kisi->tam_ad); ?>

                                            <?php else: ?>
                                                Talep #<?php echo e($talep->id); ?>

                                            <?php endif; ?>
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            <?php echo e($talep->created_at->diffForHumans()); ?>

                                        </p>
                                    </div>
                                </div>

                                
                                <div class="flex items-center gap-2">
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full
                                        <?php if(strtolower($talep->status ?? 'active') === 'active'): ?> bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        <?php elseif(strtolower($talep->status ?? 'active') === 'pending'): ?> bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        <?php elseif(strtolower($talep->status ?? 'active') === 'matched'): ?> bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                        <?php else: ?> bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                        <?php endif; ?>">
                                        <?php echo e($talep->status ?? 'Aktif'); ?>

                                    </span>
                                    <?php if($talep->kisi && $talep->kisi->email && \App\Models\Kisi::where('email', $talep->kisi->email)->count() > 1): ?>
                                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z" />
                                            </svg>
                                            Mükerrer
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        
                        <div class="p-6">
                            
                            <p class="text-gray-900 dark:text-white mb-4 line-clamp-3">
                                <?php echo e(Str::limit($talep->aciklama, 150)); ?>

                            </p>

                            
                            <div class="space-y-2 mb-4">
                                <?php if($talep->kategori): ?>
                                    <div class="flex items-center gap-2 text-sm">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span class="text-gray-600 dark:text-gray-400"><?php echo e($talep->kategori->name); ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if($talep->min_fiyat || $talep->max_fiyat): ?>
                                    <div class="flex items-center gap-2 text-sm">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 14l9-5-9-5-9 5 9 5z" />
                                        </svg>
                                        <span class="text-gray-600 dark:text-gray-400">
                                            ₺<?php echo e(number_format($talep->min_fiyat ?? 0)); ?> -
                                            ₺<?php echo e(number_format($talep->max_fiyat ?? 0)); ?>

                                        </span>
                                    </div>
                                <?php endif; ?>

                                <?php if($talep->ulke || $talep->il || $talep->ilce): ?>
                                    <div class="flex items-center gap-2 text-sm">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span class="text-gray-600 dark:text-gray-400">
                                            <?php echo e($talep->ilce->ilce_adi ?? ($talep->il->il_adi ?? ($talep->ulke->ulke_adi ?? 'Lokasyon belirtilmemiş'))); ?>

                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            
                            <div
                                class="bg-gradient-to-r from-purple-50 to-blue-50 dark:from-purple-900/20 dark:to-blue-900/20 rounded-lg p-3 mb-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-4 h-4 ai-badge rounded-full flex items-center justify-center">
                                        <svg class="w-2 h-2 text-white" fill="currentColor">
                                            <circle cx="1" cy="1" r="1" />
                                        </svg>
                                    </div>
                                    <span class="text-xs font-medium text-purple-700 dark:text-purple-300">AI
                                        Insights</span>
                                </div>
                                <div class="text-xs text-purple-600 dark:text-purple-400 space-y-1">
                                    <div>• Uyumluluk Skoru: <span class="font-semibold">%87</span></div>
                                    <div>• Tahmini Satış Süresi: <span class="font-semibold">18 gün</span></div>
                                    <div>• Eşleşen İlan: <span class="font-semibold">12 adet</span></div>
                                </div>
                            </div>
                        </div>

                        
                        <div
                            class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    
                                    <button @click="analyzeWithAI(<?php echo e($talep->id); ?>)"
                                        class="text-xs px-3 py-1.5 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-full hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 animate-pulse">
                                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                        AI Analiz
                                    </button>

                                    
                                    <button @click="findMatches(<?php echo e($talep->id); ?>)"
                                        class="text-xs px-3 py-1.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-full hover:from-green-600 hover:to-emerald-700 transition-all duration-200">
                                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Eşleştir
                                    </button>
                                </div>

                                <div class="flex items-center gap-1">
                                    
                                    <a href="<?php echo e(route('admin.talepler.show', $talep)); ?>"
                                        class="inline-flex items-center justify-center p-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    
                                    <a href="<?php echo e(route('admin.talepler.edit', $talep)); ?>"
                                        class="inline-flex items-center justify-center p-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4">
                <?php echo e($talepler->appends(request()->query())->links('pagination::tailwind')); ?>

            </div>
        <?php else: ?>
            
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293L18.707 8.707A1 1 0 0119 9.414V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    Henüz talep bulunmuyor
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    İlk talebi ekleyerek AI destekli eşleştirme sistemini kullanmaya başlayın.
                </p>
                <a href="<?php echo e(route('admin.talepler.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 text-white hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    İlk Talebi Ekle
                </a>
            </div>
        <?php endif; ?>

        
        <div x-show="showAnalysisModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95" class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showAnalysisModal = false"></div>
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full relative border border-gray-200 dark:border-gray-700">
                    
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">🤖 AI Talep Analizi</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Akıllı analiz ve eşleştirme
                                        önerileri</p>
                                </div>
                            </div>
                            <button @click="showAnalysisModal = false"
                                class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    
                    <div class="px-6 py-6">
                        <div x-show="isAnalyzing" class="text-center py-12">
                            <div>
                                <div
                                    class="w-12 h-12 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin mx-auto mb-4">
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">AI Analiz
                                        Yapılıyor</h4>
                                    <p class="text-gray-600 dark:text-gray-400">Talep detayları analiz ediliyor ve
                                        eşleştirme önerileri hazırlanıyor...</p>
                                </div>
                            </div>
                        </div>

                        <div x-show="!isAnalyzing && analysisResult" class="space-y-6">
                            
                            <div x-html="analysisResult"></div>
                        </div>
                    </div>

                    
                    <div
                        class="px-6 py-4 bg-gradient-to-r from-gray-50 to-blue-50 dark:from-gray-700 dark:to-blue-900/20 rounded-b-2xl border-t border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    AI destekli analiz
                                </span>
                            </div>
                            <div class="flex items-center gap-3">
                                <button @click="showAnalysisModal = false" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Kapat
                                </button>
                                <button class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 text-white hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    Eşleştirmeleri Görüntüle
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div x-show="showBatchAnalysisModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95" class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showBatchAnalysisModal = false"></div>
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-4xl w-full relative border border-gray-200 dark:border-gray-700">
                    
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-gradient-to-r from-green-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">🚀 Toplu AI Analizi</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Tüm talepleri akıllı analiz ile
                                        işle</p>
                                </div>
                            </div>
                            <button @click="showBatchAnalysisModal = false"
                                class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="px-6 py-6">
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Tüm status talepleri AI ile analiz ederek eşleştirme oranını artırmak ister misiniz?
                        </p>
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="font-medium text-blue-900 dark:text-blue-200">İşlem Detayları</span>
                            </div>
                            <ul class="text-sm text-blue-800 dark:text-blue-300 space-y-1">
                                <li>• <?php echo e($talepler->total()); ?> talep analiz edilecek</li>
                                <li>• Tahmini süre: ~3-5 dakika</li>
                                <li>• Otomatik eşleştirme önerileri üretilecek</li>
                                <li>• E-posta ile sonuçlar bildirilecek</li>
                            </ul>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 rounded-b-xl">
                        <div class="flex items-center justify-end gap-3">
                            <button @click="showBatchAnalysisModal = false" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                                İptal
                            </button>
                            <button @click="runBatchAnalysis()" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 text-white hover:from-indigo-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Analizi Başlat
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            function taleplerData() {
                return {
                    showAdvancedFilters: false,
                    showAnalysisModal: false,
                    showBatchAnalysisModal: false,
                    isAnalyzing: false,
                    analysisResult: null,
                    selectedTalepId: null,
                    loading: false, // 🆕 USTA Auto-Fix: Loading state eklendi
                    filterLoading: false, // 🆕 Filter için loading

                    init() {
                        console.log('AI Destekli Talepler Sistemi başlatıldı');
                    },

                    async analyzeWithAI(talepId) {
                        this.selectedTalepId = talepId;
                        this.showAnalysisModal = true;
                        this.isAnalyzing = true;
                        this.analysisResult = null;

                        try {
                            const response = await fetch(`/api/ai/analyze-demand/${talepId}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                }
                            });

                            if (response.ok) {
                                const data = await response.json();
                                this.analysisResult = this.formatAnalysisResult(data);
                            } else {
                                this.analysisResult = '<div class="text-red-600">Analiz sırasında bir hata oluştu.</div>';
                            }
                        } catch (error) {
                            console.error('AI Analysis Error:', error);
                            this.analysisResult = '<div class="text-red-600">Bağlantı hatası oluştu.</div>';
                        } finally {
                            this.isAnalyzing = false;
                        }
                    },

                    async findMatches(talepId) {
                        try {
                            const response = await fetch(`/api/ai/find-matching-properties/${talepId}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                }
                            });

                            if (response.ok) {
                                const data = await response.json();
                                // Redirect to matches page or show results
                                window.location.href = `/admin/talepler/${talepId}/matches`;
                            } else {
                                alert('Eşleştirme sırasında bir hata oluştu.');
                            }
                        } catch (error) {
                            console.error('Matching Error:', error);
                            alert('Bağlantı hatası oluştu.');
                        }
                    },

                    async runAISearch() {
                        // AI destekli akıllı arama
                        const searchInput = document.querySelector('input[name="search"]');
                        if (searchInput && searchInput.value.trim()) {
                            // Add AI enhancement to search
                            const form = searchInput.closest('form');
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'ai_enhanced';
                            hiddenInput.value = '1';
                            form.appendChild(hiddenInput);
                            form.submit();
                        } else {
                            alert('Lütfen arama terimi girin.');
                        }
                    },

                    async runBatchAnalysis() {
                        this.showBatchAnalysisModal = false;

                        try {
                            const response = await fetch('/api/ai/batch-analysis', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                }
                            });

                            if (response.ok) {
                                alert('Toplu analiz başlatıldı! Sonuçlar e-posta ile bildirilecektir.');
                            } else {
                                alert('Toplu analiz başlatılamadı.');
                            }
                        } catch (error) {
                            console.error('Batch Analysis Error:', error);
                            alert('Bağlantı hatası oluştu.');
                        }
                    },

                    formatAnalysisResult(data) {
                        return `
                <div class="space-y-4">
                    <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                        <h4 class="font-semibold text-green-900 dark:text-green-200 mb-2">AI Analiz Sonuçları</h4>
                        <div class="text-sm text-green-800 dark:text-green-300 space-y-2">
                            <div><strong>Kategori:</strong> ${data.category || 'Belirlenmedi'}</div>
                            <div><strong>Öncelik Skoru:</strong> ${data.priority_score || '0'}/100</div>
                            <div><strong>Tahmini Bütçe:</strong> ₺${data.estimated_budget || '0'}</div>
                            <div><strong>Eşleşen İlan Sayısı:</strong> ${data.matching_properties || '0'}</div>
                        </div>
                    </div>
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                        <h4 class="font-semibold text-blue-900 dark:text-blue-200 mb-2">AI Önerileri</h4>
                        <ul class="text-sm text-blue-800 dark:text-blue-300 space-y-1">
                            ${data.recommendations ? data.recommendations.map(rec => `<li>• ${rec}</li>`).join('') : '<li>• Henüz öneri bulunmuyor</li>'}
                        </ul>
                    </div>
                </div>
            `;
                    }
                }
            }
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.neo', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/macbookpro/Projects/yalihanai /resources/views/admin/talepler/index.blade.php ENDPATH**/ ?>