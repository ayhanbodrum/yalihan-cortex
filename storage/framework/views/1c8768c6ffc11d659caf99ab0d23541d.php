<?php $__env->startSection('title', 'Yeni Ayar Oluştur - Yalıhan Emlak Pro'); ?>

<?php $__env->startSection('content'); ?>
    <div class="content-header mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold flex items-center text-gray-800">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-plus text-white text-xl"></i>
                    </div>
                    Yeni Ayar Oluştur
                </h1>
                <p class="text-lg text-gray-600 mt-2">Sistem ayarı ekleyin</p>
            </div>
            <a href="<?php echo e(route('admin.ayarlar.index')); ?>" class="neo-btn neo-btn neo-btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>
                Geri Dön
            </a>
        </div>
    </div>

    <div class="px-6">
        <div class="max-w-2xl mx-auto">
            <form action="<?php echo e(route('admin.ayarlar.store')); ?>" method="POST" class="neo-card p-6">
                <?php echo csrf_field(); ?>

                <div class="space-y-6">
                    <!-- Ayar Anahtarı - Neo Component -->
                    <?php if (isset($component)) { $__componentOriginal89b0cf0484e6764a80a0f2d5bda44237 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal89b0cf0484e6764a80a0f2d5bda44237 = $attributes; } ?>
<?php $component = App\View\Components\NeoInput::resolve(['name' => 'key','label' => 'Ayar Anahtarı','required' => true,'placeholder' => 'ayar_anahtari','helpText' => 'Benzersiz anahtar adı (örn: site_title, max_upload_size)','icon' => 'fas fa-key'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('neo-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\NeoInput::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal89b0cf0484e6764a80a0f2d5bda44237)): ?>
<?php $attributes = $__attributesOriginal89b0cf0484e6764a80a0f2d5bda44237; ?>
<?php unset($__attributesOriginal89b0cf0484e6764a80a0f2d5bda44237); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal89b0cf0484e6764a80a0f2d5bda44237)): ?>
<?php $component = $__componentOriginal89b0cf0484e6764a80a0f2d5bda44237; ?>
<?php unset($__componentOriginal89b0cf0484e6764a80a0f2d5bda44237); ?>
<?php endif; ?>

                    <!-- Ayar Değeri - Textarea geçici standart -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ayar Değeri</label>
                        <textarea name="value" rows="3"
                                  class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200" placeholder="Ayar değerini girin..."></textarea>
                    </div>

                    <!-- Veri Tipi - Neo Component -->
                    <?php if (isset($component)) { $__componentOriginal65acfeb5d05ebfb4f0d16cc7bfe38bb3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal65acfeb5d05ebfb4f0d16cc7bfe38bb3 = $attributes; } ?>
<?php $component = App\View\Components\NeoSelect::resolve(['name' => 'type','label' => 'Veri Tipi','required' => true,'placeholder' => 'Veri tipi seçin...','icon' => 'fas fa-database'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('neo-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\NeoSelect::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                        <option value="">Veri tipi seçin...</option>
                        <option value="string">String (Metin)</option>
                        <option value="integer">Integer (Sayı)</option>
                        <option value="boolean">Boolean (Doğru/Yanlış)</option>
                        <option value="json">JSON (Yapılandırılmış Veri)</option>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal65acfeb5d05ebfb4f0d16cc7bfe38bb3)): ?>
<?php $attributes = $__attributesOriginal65acfeb5d05ebfb4f0d16cc7bfe38bb3; ?>
<?php unset($__attributesOriginal65acfeb5d05ebfb4f0d16cc7bfe38bb3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal65acfeb5d05ebfb4f0d16cc7bfe38bb3)): ?>
<?php $component = $__componentOriginal65acfeb5d05ebfb4f0d16cc7bfe38bb3; ?>
<?php unset($__componentOriginal65acfeb5d05ebfb4f0d16cc7bfe38bb3); ?>
<?php endif; ?>

                    <!-- Grup - Neo Component -->
                    <?php if (isset($component)) { $__componentOriginal89b0cf0484e6764a80a0f2d5bda44237 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal89b0cf0484e6764a80a0f2d5bda44237 = $attributes; } ?>
<?php $component = App\View\Components\NeoInput::resolve(['name' => 'group','label' => 'Grup','required' => true,'placeholder' => 'genel, email, sistem','helpText' => 'Ayarları gruplamak için (örn: genel, email, sistem)','icon' => 'fas fa-folder'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('neo-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\NeoInput::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal89b0cf0484e6764a80a0f2d5bda44237)): ?>
<?php $attributes = $__attributesOriginal89b0cf0484e6764a80a0f2d5bda44237; ?>
<?php unset($__attributesOriginal89b0cf0484e6764a80a0f2d5bda44237); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal89b0cf0484e6764a80a0f2d5bda44237)): ?>
<?php $component = $__componentOriginal89b0cf0484e6764a80a0f2d5bda44237; ?>
<?php unset($__componentOriginal89b0cf0484e6764a80a0f2d5bda44237); ?>
<?php endif; ?>

                    <!-- Açıklama - Textarea geçici standart -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                        <textarea name="description" rows="2"
                                  class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200" placeholder="Bu ayarın ne işe yaradığını açıklayın..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end space-x-4 mt-8">
                    <a href="<?php echo e(route('admin.ayarlar.index')); ?>" class="neo-btn neo-btn neo-btn-secondary">
                        <i class="fas fa-times mr-2"></i>
                        İptal
                    </a>
                    <button type="submit" class="neo-btn neo-btn neo-btn-primary">
                        <i class="fas fa-save mr-2"></i>
                        Ayar Oluştur
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.neo', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/admin/ayarlar/create.blade.php ENDPATH**/ ?>