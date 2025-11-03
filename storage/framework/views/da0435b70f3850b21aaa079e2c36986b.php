<?php $__env->startSection('title', 'AI Analytics Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">AI Analytics Dashboard</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">AI provider kullanımı ve performans metrikleri</p>
        </div>
        <a href="<?php echo e(route('admin.ai-settings.index')); ?>" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            AI Ayarları
        </a>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Toplam İstek</p>
                    <h3 class="text-3xl font-bold mt-2"><?php echo e(number_format($analytics['total_requests'] ?? 0)); ?></h3>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
        </div>

        
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Başarı Oranı</p>
                    <h3 class="text-3xl font-bold mt-2"><?php echo e(number_format($analytics['success_rate'] ?? 0, 1)); ?>%</h3>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Ort. Yanıt Süresi</p>
                    <h3 class="text-3xl font-bold mt-2"><?php echo e(number_format($analytics['avg_response_time'] ?? 0)); ?>ms</h3>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Toplam Maliyet</p>
                    <h3 class="text-3xl font-bold mt-2">$<?php echo e(number_format($analytics['total_cost'] ?? 0, 2)); ?></h3>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Provider Kullanım İstatistikleri</h2>
        
        <div class="space-y-4">
            <?php $__currentLoopData = ($analytics['provider_usage'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $provider => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div>
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-3">
                        <span class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e(ucfirst($provider)); ?></span>
                        <span class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($data['requests'] ?? 0); ?> istek</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white"><?php echo e(number_format($data['percentage'] ?? 0, 1)); ?>%</span>
                </div>
                
                
                <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-3 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-3 rounded-full transition-all duration-500"
                         style="width: <?php echo e($data['percentage'] ?? 0); ?>%"></div>
                </div>

                
                <div class="grid grid-cols-3 gap-4 mt-3 text-xs">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Başarı:</span>
                        <span class="font-semibold text-green-600 dark:text-green-400"><?php echo e(number_format($data['success_rate'] ?? 0, 1)); ?>%</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Yanıt Süresi:</span>
                        <span class="font-semibold text-blue-600 dark:text-blue-400"><?php echo e(number_format($data['avg_time'] ?? 0)); ?>ms</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Maliyet:</span>
                        <span class="font-semibold text-orange-600 dark:text-orange-400">$<?php echo e(number_format($data['cost'] ?? 0, 2)); ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Son AI İstekleri</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tarih</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Provider</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">İşlem</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Durum</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Süre</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php $__empty_1 = true; $__currentLoopData = ($analytics['recent_logs'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-4 py-2.5 text-sm text-gray-900 dark:text-white">
                            <?php echo e($log->created_at->format('d.m.Y H:i')); ?>

                        </td>
                        <td class="px-4 py-2.5 text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                <?php echo e(ucfirst($log->provider)); ?>

                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-sm text-gray-600 dark:text-gray-400">
                            <?php echo e($log->action); ?>

                        </td>
                        <td class="px-4 py-2.5 text-sm">
                            <?php if($log->success): ?>
                                <span class="text-green-600 dark:text-green-400">✓ Başarılı</span>
                            <?php else: ?>
                                <span class="text-red-600 dark:text-red-400">✗ Hata</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-2.5 text-sm text-gray-600 dark:text-gray-400">
                            <?php echo e(number_format($log->response_time)); ?>ms
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            Henüz AI isteği yapılmamış
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
// Auto-refresh analytics every 30 seconds
setInterval(() => {
    window.location.reload();
}, 30000);
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin.layouts.neo', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/admin/ai-settings/analytics.blade.php ENDPATH**/ ?>