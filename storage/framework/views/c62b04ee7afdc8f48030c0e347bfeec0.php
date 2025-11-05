<?php $__env->startSection('title', 'Takvim - Rezervasyon Yönetimi'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8" 
     x-data="{
         currentMonth: <?php echo e($currentMonth ?? date('n')); ?>,
         currentYear: <?php echo e($currentYear ?? date('Y')); ?>,
         viewMode: 'month', // month, week, day
         selectedEvent: null,
         showEventModal: false
     }">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    📅 Rezervasyon Takvimi
                </h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Yazlık kiralama rezervasyonlarını takvim görünümünde yönetin
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="<?php echo e(route('admin.yazlik-kiralama.bookings')); ?>" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    📋 Liste Görünümü
                </a>
                <a href="<?php echo e(route('admin.yazlik-kiralama.index')); ?>" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-all">
                    ← Geri Dön
                </a>
            </div>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90 mb-1">Toplam Etkinlik</p>
                        <p class="text-3xl font-bold"><?php echo e($stats['total_events'] ?? 0); ?></p>
                    </div>
                    <div class="text-4xl opacity-80">📊</div>
                </div>
            </div>

            
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90 mb-1">Bu Hafta</p>
                        <p class="text-3xl font-bold"><?php echo e($stats['this_week'] ?? 0); ?></p>
                    </div>
                    <div class="text-4xl opacity-80">📆</div>
                </div>
            </div>

            
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90 mb-1">Gelecek</p>
                        <p class="text-3xl font-bold"><?php echo e($stats['upcoming'] ?? 0); ?></p>
                    </div>
                    <div class="text-4xl opacity-80">🔮</div>
                </div>
            </div>
        </div>

        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                
                <div class="flex items-center gap-3">
                    <button @click="currentMonth--; if(currentMonth < 1) { currentMonth = 12; currentYear--; }"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                        ← Önceki
                    </button>
                    
                    <div class="text-center px-4">
                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                            <span x-text="['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'][currentMonth - 1]"></span>
                            <span x-text="currentYear"></span>
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Ay/Yıl</p>
                    </div>
                    
                    <button @click="currentMonth++; if(currentMonth > 12) { currentMonth = 1; currentYear++; }"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                        Sonraki →
                    </button>

                    <button @click="currentMonth = <?php echo e(date('n')); ?>; currentYear = <?php echo e(date('Y')); ?>;"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Bugün
                    </button>
                </div>

                
                <div class="flex items-center gap-2 bg-gray-100 dark:bg-gray-900 rounded-lg p-1">
                    <button @click="viewMode = 'month'"
                            :class="viewMode === 'month' ? 'bg-white dark:bg-gray-700 shadow' : ''"
                            class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-300 transition-all">
                        Ay
                    </button>
                    <button @click="viewMode = 'week'"
                            :class="viewMode === 'week' ? 'bg-white dark:bg-gray-700 shadow' : ''"
                            class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-300 transition-all">
                        Hafta
                    </button>
                    <button @click="viewMode = 'day'"
                            :class="viewMode === 'day' ? 'bg-white dark:bg-gray-700 shadow' : ''"
                            class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-300 transition-all">
                        Gün
                    </button>
                </div>
            </div>
        </div>

        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            
            
            <div x-show="viewMode === 'month'" class="p-6">
                
                <div class="grid grid-cols-7 gap-2">
                    
                    <?php $__currentLoopData = ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="text-center py-3 text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">
                            <?php echo e($day); ?>

                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    
                    <?php for($i = 1; $i <= 35; $i++): ?>
                        <div class="aspect-square border border-gray-200 dark:border-gray-700 rounded-lg p-2 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer"
                             @click="alert('Gün <?php echo e($i); ?> detayları')">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white mb-1">
                                <?php echo e($i <= 30 ? $i : ($i - 30)); ?>

                            </div>
                            
                            
                            <?php if($i % 5 === 0): ?>
                                <div class="space-y-1">
                                    <div class="text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded truncate">
                                        Rezervasyon
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if($i % 7 === 0): ?>
                                <div class="space-y-1 mt-1">
                                    <div class="text-xs bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-2 py-1 rounded truncate">
                                        Check-in
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            
            <div x-show="viewMode === 'week'" class="p-6">
                <div class="text-center text-gray-500 dark:text-gray-400 py-12">
                    <div class="text-4xl mb-4">📆</div>
                    <p class="text-lg font-semibold">Hafta Görünümü</p>
                    <p class="text-sm">Yakında eklenecek...</p>
                </div>
            </div>

            
            <div x-show="viewMode === 'day'" class="p-6">
                <div class="text-center text-gray-500 dark:text-gray-400 py-12">
                    <div class="text-4xl mb-4">📅</div>
                    <p class="text-lg font-semibold">Gün Görünümü</p>
                    <p class="text-sm">Yakında eklenecek...</p>
                </div>
            </div>
        </div>

        
        <div class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                📋 Yaklaşan Etkinlikler
            </h3>

            <?php if(isset($events) && count($events) > 0): ?>
                <div class="space-y-3">
                    <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="text-3xl">
                                    <?php if($event['type'] === 'meeting'): ?> 🤝
                                    <?php elseif($event['type'] === 'viewing'): ?> 🏠
                                    <?php elseif($event['type'] === 'call'): ?> 📞
                                    <?php elseif($event['type'] === 'followup'): ?> 🔄
                                    <?php else: ?> 📌
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        <?php echo e($event['title']); ?>

                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <?php echo e(\Carbon\Carbon::parse($event['date'])->format('d.m.Y')); ?> - <?php echo e($event['time']); ?>

                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500">
                                        📍 <?php echo e($event['location']); ?>

                                    </p>
                                </div>
                            </div>
                            
                            <button @click="alert('Etkinlik detayı: <?php echo e($event['title']); ?>')"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Detay
                            </button>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <div class="text-4xl mb-2">📭</div>
                    <p>Yaklaşan etkinlik bulunmuyor</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin.layouts.neo', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/admin/yazlik-kiralama/takvim.blade.php ENDPATH**/ ?>