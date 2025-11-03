<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Villa Kiralama - <?php echo e(config('app.name')); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    
    
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-8">
                    <a href="/" class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        Yalıhan Emlak
                    </a>
                    <div class="hidden md:flex gap-6">
                        <a href="<?php echo e(route('villas.index')); ?>" class="text-blue-600 dark:text-blue-400 font-semibold">Villa Kiralama</a>
                        <a href="/" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Satılık</a>
                        <a href="/" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Kiralık</a>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <a href="/admin" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                        Admin Girişi
                    </a>
                </div>
            </div>
        </div>
    </nav>

    
    <div class="bg-gradient-to-br from-blue-600 to-purple-600 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                Hayalinizdeki Tatil Villası
            </h1>
            <p class="text-xl text-blue-100 mb-8">
                <?php echo e(number_format($stats['total'])); ?> villa arasından size en uygununu bulun
            </p>

            
            <form method="GET" action="<?php echo e(route('villas.index')); ?>" class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            📍 Lokasyon
                        </label>
                        <input 
                            type="text" 
                            name="location" 
                            value="<?php echo e(request('location')); ?>"
                            placeholder="Bodrum, Kaş, Fethiye..." 
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>

                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            📅 Giriş
                        </label>
                        <input 
                            type="date" 
                            name="check_in" 
                            value="<?php echo e(request('check_in')); ?>"
                            min="<?php echo e(date('Y-m-d')); ?>"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>

                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            📅 Çıkış
                        </label>
                        <input 
                            type="date" 
                            name="check_out" 
                            value="<?php echo e(request('check_out')); ?>"
                            min="<?php echo e(date('Y-m-d', strtotime('+1 day'))); ?>"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>

                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            👥 Kişi Sayısı
                        </label>
                        <select 
                            name="guests"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="">Seçiniz</option>
                            <?php for($i = 1; $i <= 10; $i++): ?>
                                <option value="<?php echo e($i); ?>" <?php echo e(request('guests') == $i ? 'selected' : ''); ?>>
                                    <?php echo e($i); ?> kişi
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                
                <div x-data="{ showFilters: false }" class="mt-4">
                    <button 
                        type="button"
                        @click="showFilters = !showFilters"
                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                        <span x-show="!showFilters">🔽 Gelişmiş Filtreler</span>
                        <span x-show="showFilters" x-cloak>🔼 Gizle</span>
                    </button>

                    <div x-show="showFilters" x-cloak x-transition class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    💰 Fiyat Aralığı (Günlük)
                                </label>
                                <div class="flex gap-4">
                                    <input 
                                        type="number" 
                                        name="min_price" 
                                        value="<?php echo e(request('min_price')); ?>"
                                        placeholder="Min" 
                                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white">
                                    <span class="text-gray-500 dark:text-gray-400 py-2">-</span>
                                    <input 
                                        type="number" 
                                        name="max_price" 
                                        value="<?php echo e(request('max_price')); ?>"
                                        placeholder="Max" 
                                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white">
                                </div>
                            </div>

                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    🔄 Sıralama
                                </label>
                                <select 
                                    name="sort"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white">
                                    <option value="popular" <?php echo e(request('sort') == 'popular' ? 'selected' : ''); ?>>Popüler</option>
                                    <option value="price_low" <?php echo e(request('sort') == 'price_low' ? 'selected' : ''); ?>>Fiyat (Düşük)</option>
                                    <option value="price_high" <?php echo e(request('sort') == 'price_high' ? 'selected' : ''); ?>>Fiyat (Yüksek)</option>
                                    <option value="newest" <?php echo e(request('sort') == 'newest' ? 'selected' : ''); ?>>En Yeni</option>
                                </select>
                            </div>
                        </div>

                        
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                ✨ Özellikler
                            </label>
                            <div class="flex flex-wrap gap-2">
                                <?php $__currentLoopData = $popularAmenities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amenity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-full cursor-pointer hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors">
                                        <input 
                                            type="checkbox" 
                                            name="amenities[]" 
                                            value="<?php echo e($amenity['slug']); ?>"
                                            <?php echo e(in_array($amenity['slug'], request('amenities', [])) ? 'checked' : ''); ?>

                                            class="rounded text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">
                                            <?php echo e($amenity['icon']); ?> <?php echo e($amenity['name']); ?>

                                        </span>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="mt-6 flex gap-3">
                    <button 
                        type="submit"
                        class="flex-1 px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                        🔍 Villa Ara
                    </button>
                    <?php if(request()->hasAny(['location', 'check_in', 'check_out', 'guests', 'min_price', 'max_price', 'amenities'])): ?>
                        <a 
                            href="<?php echo e(route('villas.index')); ?>"
                            class="px-6 py-4 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-semibold hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            ✖️ Temizle
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Villa Kiralama Seçenekleri
                </h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    <?php echo e(number_format($villas->total())); ?> villa bulundu
                    <?php if(request('check_in') && request('check_out')): ?>
                        • <?php echo e($stats['available_today']); ?> müsait
                    <?php endif; ?>
                </p>
            </div>

            
            <div class="flex gap-2 bg-gray-200 dark:bg-gray-700 rounded-lg p-1">
                <button class="px-4 py-2 rounded-lg bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm">
                    ⊞ Grid
                </button>
                <button class="px-4 py-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-white dark:hover:bg-gray-600 transition-colors">
                    ☰ Liste
                </button>
            </div>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php $__empty_1 = true; $__currentLoopData = $villas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $villa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                
                <a href="<?php echo e(route('villas.show', $villa->id)); ?>" 
                   class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden hover:-translate-y-1">
                    
                    
                    <div class="relative aspect-[4/3] overflow-hidden bg-gray-200 dark:bg-gray-700">
                        <?php if($villa->featuredPhoto): ?>
                            <img 
                                src="<?php echo e($villa->featuredPhoto->thumbnail_url); ?>" 
                                alt="<?php echo e($villa->baslik); ?>"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <?php elseif($villa->fotograflar->first()): ?>
                            <img 
                                src="/storage/<?php echo e($villa->fotograflar->first()->dosya_yolu); ?>" 
                                alt="<?php echo e($villa->baslik); ?>"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center text-6xl">
                                🏖️
                            </div>
                        <?php endif; ?>

                        
                        <?php if($villa->min_konaklama_suresi): ?>
                            <div class="absolute top-3 left-3 bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-semibold shadow-lg">
                                En Az <?php echo e($villa->min_konaklama_suresi); ?> Gece
                            </div>
                        <?php endif; ?>

                        
                        <button class="absolute top-3 right-3 w-10 h-10 bg-white/90 dark:bg-gray-800/90 rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform">
                            <span class="text-xl">🤍</span>
                        </button>
                    </div>

                    
                    <div class="p-5">
                        
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                            <?php echo e($villa->baslik); ?>

                        </h3>

                        
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 flex items-center gap-1">
                            <span>📍</span>
                            <?php echo e($villa->ilce->name ?? ''); ?>, <?php echo e($villa->il->name ?? ''); ?>

                        </p>

                        
                        <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400 mb-4">
                            <?php if($villa->oda_sayisi): ?>
                                <span>🛏️ <?php echo e($villa->oda_sayisi); ?> Oda</span>
                            <?php endif; ?>
                            <?php if($villa->banyo_sayisi): ?>
                                <span>🚿 <?php echo e($villa->banyo_sayisi); ?> Banyo</span>
                            <?php endif; ?>
                            <?php if($villa->maksimum_misafir): ?>
                                <span>👥 <?php echo e($villa->maksimum_misafir); ?> Kişi</span>
                            <?php endif; ?>
                        </div>

                        
                        <div class="flex items-center gap-2 mb-4">
                            <?php if($villa->havuz): ?>
                                <span title="Özel Havuz" class="text-xl">🏊</span>
                            <?php endif; ?>
                            <?php if($villa->jakuzi): ?>
                                <span title="Jakuzi" class="text-xl">🛁</span>
                            <?php endif; ?>
                            <?php if($villa->wifi): ?>
                                <span title="WiFi" class="text-xl">📶</span>
                            <?php endif; ?>
                            <?php if($villa->klima): ?>
                                <span title="Klima" class="text-xl">❄️</span>
                            <?php endif; ?>
                        </div>

                        
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-end justify-between">
                                <div>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                        <?php echo e(number_format($villa->gunluk_fiyat ?? $villa->fiyat)); ?> ₺
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        / gece
                                    </p>
                                </div>
                                <span class="text-blue-600 dark:text-blue-400 group-hover:translate-x-1 transition-transform">
                                    →
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                
                <div class="col-span-full text-center py-16">
                    <div class="text-6xl mb-4">🏖️</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                        Villa Bulunamadı
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Arama kriterlerinizi değiştirip tekrar deneyin
                    </p>
                    <a 
                        href="<?php echo e(route('villas.index')); ?>"
                        class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Tüm Villaları Görüntüle
                    </a>
                </div>
            <?php endif; ?>
        </div>

        
        <?php if($villas->hasPages()): ?>
            <div class="mt-12">
                <?php echo e($villas->links()); ?>

            </div>
        <?php endif; ?>
    </div>

    
    <footer class="bg-gray-900 text-white mt-20 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h4 class="text-xl font-bold mb-4">Yalıhan Emlak</h4>
                    <p class="text-gray-400">
                        Türkiye'nin en güvenilir tatil villası kiralama platformu
                    </p>
                </div>
                <div>
                    <h5 class="font-semibold mb-3">Popüler Bölgeler</h5>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="?location=Bodrum" class="hover:text-white">Bodrum</a></li>
                        <li><a href="?location=Kaş" class="hover:text-white">Kaş</a></li>
                        <li><a href="?location=Fethiye" class="hover:text-white">Fethiye</a></li>
                        <li><a href="?location=Kalkan" class="hover:text-white">Kalkan</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-semibold mb-3">Hızlı Linkler</h5>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="/" class="hover:text-white">Ana Sayfa</a></li>
                        <li><a href="<?php echo e(route('villas.index')); ?>" class="hover:text-white">Villa Kiralama</a></li>
                        <li><a href="/admin" class="hover:text-white">İlan Ver</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-semibold mb-3">İletişim</h5>
                    <ul class="space-y-2 text-gray-400">
                        <li>📧 info@yalihanemlak.com</li>
                        <li>📞 +90 (532) 123 4567</li>
                        <li>📍 Bodrum, Muğla</li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-gray-800 text-center text-gray-400 text-sm">
                © <?php echo e(date('Y')); ?> Yalıhan Emlak. Tüm hakları saklıdır.
            </div>
        </div>
    </footer>

    
    <script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>

<?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/villas/index.blade.php ENDPATH**/ ?>