<?php $__env->startSection('content'); ?>
    <div class="neo-container">
        <div class="neo-page-header">
            <h1 class="neo-page-title">⚙️ Sistem Ayarları</h1>
            <a href="<?php echo e(route('admin.ayarlar.create')); ?>" class="neo-btn neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                Yeni Ayar Ekle
            </a>
        </div>

        <?php if(session('success')): ?>
            <div class="neo-alert neo-alert-success">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php $__currentLoopData = $settings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $groupSettings): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="neo-card mb-6">
                <div class="neo-card-header">
                    <h2 class="neo-card-title"><?php echo e(ucfirst($group)); ?></h2>
                </div>
                <div class="neo-card-body">
                    <div class="neo-table-responsive">
                        <table class="neo-table">
                            <thead>
                                <tr>
                                    <th>Anahtar</th>
                                    <th>Değer</th>
                                    <th>Tip</th>
                                    <th>Açıklama</th>
                                    <th class="text-right">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $groupSettings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><code class="neo-code"><?php echo e($setting->key); ?></code></td>
                                        <td>
                                            <?php if(strlen($setting->value ?? '') > 50): ?>
                                                <?php echo e(substr($setting->value, 0, 50)); ?>...
                                            <?php else: ?>
                                                <?php echo e($setting->value ?? '-'); ?>

                                            <?php endif; ?>
                                        </td>
                                        <td><span class="neo-badge"><?php echo e($setting->type); ?></span></td>
                                        <td><?php echo e($setting->description ?? '-'); ?></td>
                                        <td class="text-right">
                                            <a href="<?php echo e(route('admin.ayarlar.edit', $setting->id)); ?>"
                                                class="neo-btn neo-btn-sm neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized">
                                                Düzenle
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-gray-500">
                                            Bu grupta ayar bulunamadı
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.neo', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/admin/ayarlar/index.blade.php ENDPATH**/ ?>